<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Product;
use App\Models\ServiceAssignment;
use App\Models\ServiceJob;
use App\Models\ServiceMaterialRequest;
use App\Models\Technician;
use App\Models\Tender;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ServiceOperationsController extends Controller
{
    public function index(Request $request, string $type): View
    {
        abort_unless(in_array($type, ['installation', 'maintenance'], true), 404);
        $serviceType = $type === 'installation' ? 'installation' : 'maintenance';
        $query = ServiceJob::with(['client', 'tender', 'assignments.technician', 'materialRequests.product'])
            ->where('service_type', $serviceType);

        if (Auth::user()?->isAdmin()) {
            $query->where('created_by', Auth::id());
        } elseif (Auth::user()?->isTechnician()) {
            $query->whereHas('assignments.technician', fn ($q) => $q->where('user_id', Auth::id()));
        }
        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(fn ($q) => $q->where('job_number', 'like', "%{$search}%")
                ->orWhere('name', 'like', "%{$search}%")
                ->orWhere('klien', 'like', "%{$search}%"));
        }

        $jobs = $query->latest()->paginate(10)->withQueryString();
        $clients = Client::where('status', 'active')->orderBy('name')->get();
        $tenders = Tender::where(fn ($query) => $query->where('approval_status', 'approved')->orWhereNull('approval_status'))->latest()->get();
        $technicians = Technician::where('status', 'active')->orderBy('name')->get();
        $products = Product::where('is_active', true)->orderBy('name')->get();
        $title = $serviceType === 'installation' ? 'Instalasi' : 'Perbaikan / Maintenance';

        return view('services.operations.index', compact('jobs', 'clients', 'tenders', 'technicians', 'products', 'serviceType', 'title'));
    }

    public function store(Request $request, string $type): RedirectResponse
    {
        abort_unless(!Auth::user()?->isTechnician(), 403);
        $serviceType = $this->resolveType($type);
        $validated = $request->validate([
            'client_id' => 'nullable|exists:clients,id',
            'tender_id' => 'nullable|exists:tenders,id',
            'klien' => 'nullable|string|max:200',
            'name' => 'required|string|max:200',
            'location' => 'nullable|string|max:255',
            'work_type' => 'nullable|string|max:150',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'estimated_labor_cost' => 'nullable|numeric|min:0',
            'estimated_labor_hours' => 'nullable|numeric|min:0',
            'required_competency' => 'nullable|string|max:255',
            'diagnosis' => 'nullable|string',
            'parts_needed' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $validated['service_type'] = $serviceType;
        $validated['job_number'] = 'JOB-' . strtoupper($serviceType) . '-' . now()->format('YmdHis');
        $validated['status'] = 'Pending';
        $validated['approval_status'] = Auth::user()?->isAdmin() ? 'pending' : 'approved';
        $validated['created_by'] = Auth::id();
        $validated['biaya'] = $validated['estimated_labor_cost'] ?? 0;
        $validated['progress'] = 0;
        if (!empty($validated['client_id']) && empty($validated['klien'])) {
            $validated['klien'] = Client::find($validated['client_id'])?->name;
        }

        ServiceJob::create($validated);
        return redirect()->route($serviceType === 'installation' ? 'jasa.instalasi.index' : 'jasa.maintenance.index')
            ->with('success', "Permintaan {$serviceType} berhasil dicatat.");
    }

    public function assign(Request $request, ServiceJob $serviceJob): RedirectResponse
    {
        abort_unless(!Auth::user()?->isTechnician(), 403);
        $validated = $request->validate([
            'technician_id' => 'required|exists:technicians,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'notes' => 'nullable|string',
        ]);

        $conflict = ServiceAssignment::where('technician_id', $validated['technician_id'])
            ->whereIn('status', ['assigned', 'in_progress'])
            ->where('start_date', '<=', $validated['end_date'])
            ->where('end_date', '>=', $validated['start_date'])
            ->exists();
        if ($conflict) {
            throw ValidationException::withMessages(['technician_id' => 'Teknisi sudah memiliki penugasan pada rentang tanggal tersebut.']);
        }

        $serviceJob->assignments()->updateOrCreate(
            ['technician_id' => $validated['technician_id']],
            $validated + ['status' => 'assigned']
        );
        return back()->with('success', 'Teknisi berhasil ditugaskan tanpa konflik jadwal.');
    }

    public function requestMaterial(Request $request, ServiceJob $serviceJob): RedirectResponse
    {
        abort_unless(!Auth::user()?->isTechnician(), 403);
        $validated = $request->validate([
            'product_id' => 'nullable|exists:products,id',
            'item_name' => 'required|string|max:200',
            'quantity' => 'required|numeric|min:0.01',
            'unit' => 'nullable|string|max:30',
            'notes' => 'nullable|string',
        ]);
        $validated['requested_by'] = Auth::id();
        $serviceJob->materialRequests()->create($validated);
        $serviceJob->update(['material_request_status' => 'requested']);
        return back()->with('success', 'Permintaan material dikirim ke modul Dagang.');
    }

    public function updateProgress(Request $request, ServiceJob $serviceJob): RedirectResponse
    {
        if (Auth::user()?->isTechnician()) {
            abort_unless($serviceJob->assignments()->whereHas('technician', fn ($q) => $q->where('user_id', Auth::id()))->exists(), 403);
        }
        $validated = $request->validate([
            'progress' => 'required|integer|min:0|max:100',
            'status' => 'required|string|max:30',
            'diagnosis' => 'nullable|string',
            'result_notes' => 'nullable|string',
            'actual_cost' => 'nullable|numeric|min:0',
        ]);
        $serviceJob->update($validated + ['status' => $validated['progress'] === 100 ? 'Selesai' : $validated['status']]);
        return back()->with('success', 'Progres pekerjaan berhasil diperbarui.');
    }

    private function resolveType(string $type): string
    {
        abort_unless(in_array($type, ['installation', 'maintenance'], true), 404);
        return $type === 'installation' ? 'installation' : 'maintenance';
    }
}
