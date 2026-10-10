<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Tender;
use App\Models\TenderAssignment;
use App\Models\TenderProjectLog;
use App\Models\TenderRabItem;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class TenderProjectController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        $query = Tender::with(['client', 'rabItems', 'assignments', 'projectLogs', 'creator']);

        if ($user && $user->role && $user->role->name === 'admin') {
            $query->where('created_by', $user->id);
        }

        // Tampilkan tender yang disetujui, menang, kontrak, atau sedang pelaksanaan
        $query->where(function ($q) {
            $q->whereIn('status', ['Menang', 'Kontrak', 'Pelaksanaan', 'Selesai'])
              ->orWhere('approval_status', 'approved')
              ->orWhere('submission_status', 'disetujui');
        });

        if ($request->filled('project_status')) {
            $query->where('project_status', $request->project_status);
        }

        if ($request->filled('metode_penanganan')) {
            $query->where('metode_penanganan', $request->metode_penanganan);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('tender_number', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('project_location', 'like', "%{$search}%")
                  ->orWhere('project_pic', 'like', "%{$search}%");
            });
        }

        $projectCounts = [
            'total' => (clone $query)->count(),
            'persiapan' => (clone $query)->where('project_status', 'Persiapan')->count(),
            'dalam_pengerjaan' => (clone $query)->where('project_status', 'Dalam Pengerjaan')->count(),
            'kendala' => (clone $query)->where('project_status', 'Kendala')->count(),
            'selesai' => (clone $query)->where('project_status', 'Selesai')->count(),
        ];

        $tenders = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return view('tenders.proyek.index', compact('tenders', 'projectCounts'));
    }

    public function show(Tender $tender): View
    {
        $tender->load([
            'client',
            'rabItems.product',
            'assignments.user',
            'projectLogs.logger',
            'creator',
            'approver',
        ]);

        $users = User::where('is_active', true)->orderBy('name')->get();
        $products = Product::where('is_active', true)->orderBy('name')->get();

        return view('tenders.proyek.show', compact('tender', 'users', 'products'));
    }

    public function updateStatus(Request $request, Tender $tender): RedirectResponse
    {
        $validated = $request->validate([
            'project_status' => 'required|in:Persiapan,Dalam Pengerjaan,Kendala,Selesai,Dibatalkan',
            'project_progress' => 'required|integer|min:0|max:100',
            'project_location' => 'nullable|string|max:255',
            'project_pic' => 'nullable|string|max:150',
            'project_start_date' => 'nullable|date',
            'project_end_date' => 'nullable|date',
        ]);

        // Jika progres 100%, otomatis set status proyek ke Selesai
        if ((int) $validated['project_progress'] >= 100 && $validated['project_status'] !== 'Dibatalkan') {
            $validated['project_status'] = 'Selesai';
        }

        $tender->update($validated);

        return redirect()->route('tender.proyek.show', $tender)->with('success', 'Informasi dan status operasional proyek berhasil diperbarui.');
    }

    public function allocateMaterial(Request $request, Tender $tender): RedirectResponse
    {
        $validated = $request->validate([
            'rab_item_id' => 'required|exists:tender_rab_items,id',
            'quantity' => 'required|numeric|min:0.01',
        ]);

        $item = TenderRabItem::where('tender_id', $tender->id)->findOrFail($validated['rab_item_id']);

        if (!$item->product_id || !$item->product) {
            return redirect()->route('tender.proyek.show', $tender)->with('error', 'Item ini tidak terhubung ke master produk modul Dagang.');
        }

        $currentStock = (float) $item->product->stock;
        $allocateQty = (float) $validated['quantity'];

        if ($allocateQty > $currentStock) {
            return redirect()->route('tender.proyek.show', $tender)->with('error', "Stok gudang tidak mencukupi. Tersedia: {$currentStock} {$item->unit}, diminta alokasi: {$allocateQty} {$item->unit}.");
        }

        DB::transaction(function () use ($item, $allocateQty, $tender) {
            // Update jumlah alokasi pada item RAB proyek
            $item->increment('allocated_quantity', $allocateQty);

            // Kurangi stok di gudang melalui StockMovement (Modul Dagang)
            StockMovement::create([
                'product_id' => $item->product_id,
                'movement_type' => 'OUT',
                'quantity' => $allocateQty,
                'reference_type' => 'Proyek Lapangan Tender',
                'reference_id' => $tender->id,
                'movement_date' => now(),
                'notes' => "Alokasi barang ke proyek: {$tender->tender_number} ({$tender->name})",
                'created_by' => Auth::id() ?? 1,
            ]);
        });

        return redirect()->route('tender.proyek.show', $tender)->with('success', "Berhasil mengalokasikan {$allocateQty} {$item->unit} barang dari gudang ke proyek.");
    }

    public function recordMaterialUsage(Request $request, Tender $tender): RedirectResponse
    {
        $validated = $request->validate([
            'rab_item_id' => 'required|exists:tender_rab_items,id',
            'used_quantity' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string',
        ]);

        $item = TenderRabItem::where('tender_id', $tender->id)->findOrFail($validated['rab_item_id']);
        $newUsed = (float) $item->used_quantity + (float) $validated['used_quantity'];

        $item->update([
            'used_quantity' => $newUsed,
            'notes' => $validated['notes'] ? ($item->notes ? $item->notes . ' | ' . $validated['notes'] : $validated['notes']) : $item->notes,
        ]);

        return redirect()->route('tender.proyek.show', $tender)->with('success', 'Pencatatan penggunaan barang aktual di lapangan berhasil disimpan.');
    }

    public function storeAssignment(Request $request, Tender $tender): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'technician_name' => 'required|string|max:150',
            'role_or_competency' => 'required|string|max:100',
            'contact_phone' => 'nullable|string|max:50',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'status' => 'required|string|in:Ditugaskan,Sedang Bekerja,Selesai,Digantikan',
            'notes' => 'nullable|string',
        ]);

        $validated['tender_id'] = $tender->id;

        TenderAssignment::create($validated);

        return redirect()->route('tender.proyek.show', $tender)->with('success', 'Teknisi / SDM berhasil ditugaskan ke proyek.');
    }

    public function updateAssignment(Request $request, Tender $tender, TenderAssignment $assignment): RedirectResponse
    {
        abort_unless($assignment->tender_id === $tender->id, 404);

        $validated = $request->validate([
            'role_or_competency' => 'required|string|max:100',
            'contact_phone' => 'nullable|string|max:50',
            'status' => 'required|string|in:Ditugaskan,Sedang Bekerja,Selesai,Digantikan',
            'notes' => 'nullable|string',
        ]);

        $assignment->update($validated);

        return redirect()->route('tender.proyek.show', $tender)->with('success', 'Status penugasan teknisi berhasil diperbarui.');
    }

    public function destroyAssignment(Tender $tender, TenderAssignment $assignment): RedirectResponse
    {
        abort_unless($assignment->tender_id === $tender->id, 404);

        $assignment->delete();

        return redirect()->route('tender.proyek.show', $tender)->with('success', 'Penugasan teknisi berhasil dihapus.');
    }

    public function storeLog(Request $request, Tender $tender): RedirectResponse
    {
        $validated = $request->validate([
            'log_date' => 'required|date',
            'progress_percentage' => 'required|integer|min:0|max:100',
            'activity_description' => 'required|string',
            'obstacles' => 'nullable|string',
            'solutions' => 'nullable|string',
            'actual_cost_spent' => 'nullable|numeric|min:0',
            'documentation_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240',
        ]);

        $filePath = null;
        if ($request->hasFile('documentation_file')) {
            $file = $request->file('documentation_file');
            $filePath = $file->store('tender_documentation', 'public');
        }

        $costSpent = (float) ($validated['actual_cost_spent'] ?? 0);

        TenderProjectLog::create([
            'tender_id' => $tender->id,
            'log_date' => $validated['log_date'],
            'progress_percentage' => $validated['progress_percentage'],
            'activity_description' => $validated['activity_description'],
            'obstacles' => $validated['obstacles'] ?? null,
            'solutions' => $validated['solutions'] ?? null,
            'actual_cost_spent' => $costSpent,
            'documentation_file' => $filePath,
            'logged_by' => Auth::id() ?? 1,
        ]);

        // Perbarui progres proyek dan akumulasi biaya aktual tender
        $updates = [
            'project_progress' => max($tender->project_progress, (int) $validated['progress_percentage']),
            'actual_cost' => (float) $tender->actual_cost + $costSpent,
        ];

        // Jika terdapat kendala, otomatis set status proyek ke Kendala jika belum Selesai
        if (!empty($validated['obstacles']) && $tender->project_status !== 'Selesai') {
            $updates['project_status'] = 'Kendala';
        } elseif ((int) $validated['progress_percentage'] >= 100) {
            $updates['project_status'] = 'Selesai';
        } elseif ($tender->project_status === 'Persiapan') {
            $updates['project_status'] = 'Dalam Pengerjaan';
        }

        $tender->update($updates);

        return redirect()->route('tender.proyek.show', $tender)->with('success', 'Catatan log harian lapangan dan dokumentasi berhasil disimpan.');
    }

    public function destroyLog(Tender $tender, TenderProjectLog $log): RedirectResponse
    {
        abort_unless($log->tender_id === $tender->id, 404);

        if ($log->documentation_file) {
            Storage::disk('public')->delete($log->documentation_file);
        }

        $cost = (float) $log->actual_cost_spent;
        $log->delete();

        if ($cost > 0) {
            $tender->decrement('actual_cost', min((float) $tender->actual_cost, $cost));
        }

        return redirect()->route('tender.proyek.show', $tender)->with('success', 'Catatan log progres lapangan berhasil dihapus.');
    }

    public function updateVendorProgress(Request $request, Tender $tender): RedirectResponse
    {
        $validated = $request->validate([
            'vendor_progress' => 'required|integer|min:0|max:100',
            'vendor_notes' => 'nullable|string',
        ]);

        $tender->update($validated);

        return redirect()->route('tender.proyek.show', $tender)->with('success', 'Progres pengerjaan vendor relasi berhasil diperbarui.');
    }
}
