<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Product;
use App\Models\ServiceAssignment;
use App\Models\ServiceJob;
use App\Models\ServiceMaterialRequest;
use App\Models\StockMovement;
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
        if (!empty($validated['tender_id'])) {
            $tender = Tender::find($validated['tender_id']);
            if ($tender && ($tender->approval_status === 'approved' || in_array($tender->status, ['Menang', 'Kontrak', 'Pelaksanaan', 'Selesai'], true))) {
                $validated['approval_status'] = 'approved';
            } else {
                $validated['approval_status'] = Auth::user()?->isAdmin() ? 'pending' : 'approved';
            }
        } else {
            $validated['approval_status'] = Auth::user()?->isAdmin() ? 'pending' : 'approved';
        }
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

        // Jika barang dipilih dari Dagang (product_id), kurangi stok di Dagang via StockMovement OUT
        if (!empty($validated['product_id'])) {
            $product = Product::find($validated['product_id']);
            if ($product) {
                StockMovement::create([
                    'product_id' => $product->id,
                    'movement_type' => 'OUT',
                    'quantity' => (float) $validated['quantity'],
                    'reference_type' => 'Operasional Jasa',
                    'reference_id' => $serviceJob->id,
                    'movement_date' => now(),
                    'notes' => "Penggunaan material jasa: {$serviceJob->job_number} ({$serviceJob->name})",
                    'created_by' => Auth::id() ?? 1,
                ]);
            }
        }

        return back()->with('success', 'Permintaan material berhasil dicatat dan stok gudang Dagang disinkronkan.');
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

        $progress = (int) $validated['progress'];
        $status = $progress === 100 ? 'Selesai' : $validated['status'];

        $updateData = [
            'progress' => $progress,
            'status' => $status,
        ];

        if ($status === 'Selesai' || $progress === 100) {
            $updateData['approval_status'] = 'approved';
        }

        if ($request->filled('actual_cost')) {
            $updateData['actual_cost'] = (float) $validated['actual_cost'];
        } elseif ($request->has('actual_cost') && $request->input('actual_cost') === '0') {
            $updateData['actual_cost'] = 0;
        } else {
            $updateData['actual_cost'] = $serviceJob->actual_cost ?? 0;
        }

        if (array_key_exists('diagnosis', $validated)) {
            $updateData['diagnosis'] = $validated['diagnosis'];
        }
        if (array_key_exists('result_notes', $validated)) {
            $updateData['result_notes'] = $validated['result_notes'];
        }

        $serviceJob->update($updateData);

        // Jika pekerjaan Selesai, bebaskan penugasan teknisi (tugas aktif kembali 0)
        if ($status === 'Selesai' || $progress === 100) {
            $serviceJob->assignments()->update(['status' => 'completed']);
        } else {
            $serviceJob->assignments()->where('status', 'completed')->update(['status' => 'in_progress']);
        }

        // Sinkronkan ke Modul Tender jika serviceJob terhubung ke Tender
        if ($serviceJob->tender_id) {
            $tender = $serviceJob->tender;
            if ($tender) {
                $tenderUpdates = [
                    'project_progress' => $progress,
                ];

                if ($status === 'Selesai' || $progress === 100) {
                    $tenderUpdates['project_status'] = 'Selesai';
                    $tenderUpdates['status'] = 'Selesai';
                    $tender->assignments()->update(['status' => 'Selesai']);

                    // Sinkronkan juga service jobs lain yang terhubung ke tender ini jika ada
                    ServiceJob::where('tender_id', $tender->id)
                        ->where('id', '!=', $serviceJob->id)
                        ->get()
                        ->each(function ($siblingJob) {
                            $siblingJob->update(['progress' => 100, 'status' => 'Selesai']);
                            $siblingJob->assignments()->update(['status' => 'completed']);
                        });
                } elseif ($status === 'Dalam Pelaksanaan') {
                    $tenderUpdates['project_status'] = 'Dalam Pengerjaan';
                    if ($tender->status === 'Selesai') {
                        $tenderUpdates['status'] = 'Pelaksanaan';
                    }
                } elseif ($status === 'Tertunda') {
                    $tenderUpdates['project_status'] = 'Kendala';
                }

                $tender->update($tenderUpdates);
            }
        }

        return back()->with('success', 'Progres pekerjaan berhasil diperbarui dan disinkronkan ke Modul Tender.');
    }

    private function resolveType(string $type): string
    {
        abort_unless(in_array($type, ['installation', 'maintenance'], true), 404);
        return $type === 'installation' ? 'installation' : 'maintenance';
    }
}
