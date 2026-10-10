<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Product;
use App\Models\ServiceJob;
use App\Models\Tender;
use App\Models\TenderApprovalHistory;
use App\Models\TenderRabItem;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TenderRabController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        $query = Tender::with(['client', 'rabItems.product', 'creator', 'rabApprover']);

        if ($user && $user->role && $user->role->name === 'admin') {
            $query->where('created_by', $user->id);
        }

        if ($request->filled('rab_status')) {
            $query->where('rab_status', $request->rab_status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('tender_number', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }

        $rabCounts = [
            'total' => (clone $query)->count(),
            'draft' => (clone $query)->where('rab_status', 'draft')->count(),
            'diajukan' => (clone $query)->where('rab_status', 'diajukan')->count(),
            'disetujui' => (clone $query)->where('rab_status', 'disetujui')->count(),
            'ditolak' => (clone $query)->where('rab_status', 'ditolak')->count(),
            'perlu_revisi' => (clone $query)->where('rab_status', 'perlu_revisi')->count(),
        ];

        $tenders = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return view('tenders.rab.index', compact('tenders', 'rabCounts'));
    }

    public function show(Tender $tender): View
    {
        $tender->load([
            'client',
            'rabItems.product',
            'creator',
            'rabApprover',
            'assignments.user',
            'approvalHistories' => fn ($q) => $q->where('module_type', 'rab')->orderBy('created_at', 'desc'),
        ]);

        // Ambil data produk aktif dari Modul Dagang
        $products = Product::where('is_active', true)->orderBy('name')->get();

        // Ambil data pekerjaan jasa yang sedang berjalan untuk memantau ketersediaan jadwal SDM
        $activeServiceJobs = ServiceJob::whereIn('status', ['Dalam Pelaksanaan', 'Berjalan', 'planned'])
            ->orderBy('start_date')
            ->get();

        // Daftar pengguna / teknisi untuk penugasan dari modul Jasa
        $technicianCandidates = User::where('is_active', true)
            ->whereHas('role', fn ($q) => $q->whereIn('name', ['admin', 'service_officer', 'manager']))
            ->get();

        return view('tenders.rab.show', compact('tender', 'products', 'activeServiceJobs', 'technicianCandidates'));
    }

    public function storeItem(Request $request, Tender $tender): RedirectResponse
    {
        $validated = $request->validate([
            'category' => 'required|in:barang,tenaga_kerja,transportasi,operasional,vendor',
            'product_id' => 'nullable|exists:products,id',
            'item_name' => 'required|string|max:200',
            'quantity' => 'required|numeric|min:0.01',
            'unit' => 'required|string|max:50',
            'unit_cost' => 'required|numeric|min:0',
            'unit_price' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $validated['tender_id'] = $tender->id;
        $validated['subtotal_cost'] = $validated['quantity'] * $validated['unit_cost'];
        $validated['subtotal_price'] = $validated['quantity'] * $validated['unit_price'];

        // Jika terhubung ke modul Dagang dan kategori barang, ambil data produk jika nama belum disesuaikan
        if ($validated['category'] === 'barang' && !empty($validated['product_id'])) {
            $product = Product::find($validated['product_id']);
            if ($product && empty($validated['item_name'])) {
                $validated['item_name'] = $product->name;
            }
        }

        TenderRabItem::create($validated);

        // Jika status RAB sudah disetujui atau diajukan, kembalikan ke draft saat ada revisi item oleh admin
        if (in_array($tender->rab_status, ['disetujui', 'diajukan'], true) && Auth::user()->role?->name === 'admin') {
            $tender->update(['rab_status' => 'draft']);
        }

        return redirect()->route('tender.rab.show', $tender)->with('success', 'Komponen RAB berhasil ditambahkan.');
    }

    public function updateItem(Request $request, Tender $tender, TenderRabItem $item): RedirectResponse
    {
        abort_unless($item->tender_id === $tender->id, 404);

        $validated = $request->validate([
            'category' => 'required|in:barang,tenaga_kerja,transportasi,operasional,vendor',
            'product_id' => 'nullable|exists:products,id',
            'item_name' => 'required|string|max:200',
            'quantity' => 'required|numeric|min:0.01',
            'unit' => 'required|string|max:50',
            'unit_cost' => 'required|numeric|min:0',
            'unit_price' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $validated['subtotal_cost'] = $validated['quantity'] * $validated['unit_cost'];
        $validated['subtotal_price'] = $validated['quantity'] * $validated['unit_price'];

        $item->update($validated);

        return redirect()->route('tender.rab.show', $tender)->with('success', 'Komponen RAB berhasil diperbarui.');
    }

    public function destroyItem(Tender $tender, TenderRabItem $item): RedirectResponse
    {
        abort_unless($item->tender_id === $tender->id, 404);

        $item->delete();

        return redirect()->route('tender.rab.show', $tender)->with('success', 'Komponen RAB berhasil dihapus.');
    }

    public function submitRab(Request $request, Tender $tender): RedirectResponse
    {
        $user = Auth::user();

        if ($tender->rabItems()->count() === 0) {
            return redirect()->route('tender.rab.show', $tender)->with('error', 'RAB belum memiliki rincian biaya. Harap masukkan komponen biaya sebelum mengajukan.');
        }

        $tender->update([
            'rab_status' => 'diajukan',
            'rab_submitted_at' => now(),
        ]);

        TenderApprovalHistory::create([
            'tender_id' => $tender->id,
            'module_type' => 'rab',
            'action' => 'diajukan',
            'user_id' => $user->id,
            'notes' => $request->input('notes', 'Pengajuan estimasi RAB kepada Manajemen'),
        ]);

        return redirect()->route('tender.rab.show', $tender)->with('success', "Estimasi RAB untuk tender {$tender->tender_number} berhasil DIAJUKAN ke Manajemen.");
    }

    public function approveRab(Request $request, Tender $tender): RedirectResponse
    {
        $user = Auth::user();
        if (!$user || !$user->role || !in_array($user->role->name, ['manager', 'owner'], true)) {
            return redirect()->route('tender.rab.show', $tender)->with('error', 'Hanya Manager atau Owner yang berhak menyetujui RAB.');
        }

        $notes = $request->input('notes', 'Estimasi RAB disetujui oleh ' . $user->name);

        $tender->update([
            'rab_status' => 'disetujui',
            'rab_approved_by' => $user->id,
            'rab_approved_at' => now(),
            'rab_notes' => $notes,
        ]);

        TenderApprovalHistory::create([
            'tender_id' => $tender->id,
            'module_type' => 'rab',
            'action' => 'disetujui',
            'user_id' => $user->id,
            'notes' => $notes,
        ]);

        return redirect()->route('tender.rab.show', $tender)->with('success', "Estimasi RAB tender {$tender->tender_number} berhasil DISETUJUI.");
    }

    public function rejectRab(Request $request, Tender $tender): RedirectResponse
    {
        $user = Auth::user();
        if (!$user || !$user->role || !in_array($user->role->name, ['manager', 'owner'], true)) {
            return redirect()->route('tender.rab.show', $tender)->with('error', 'Hanya Manager atau Owner yang berhak menolak RAB.');
        }

        $notes = $request->input('notes', 'Estimasi RAB ditolak oleh ' . $user->name);

        $tender->update([
            'rab_status' => 'ditolak',
            'rab_approved_by' => $user->id,
            'rab_approved_at' => now(),
            'rab_notes' => $notes,
        ]);

        TenderApprovalHistory::create([
            'tender_id' => $tender->id,
            'module_type' => 'rab',
            'action' => 'ditolak',
            'user_id' => $user->id,
            'notes' => $notes,
        ]);

        return redirect()->route('tender.rab.show', $tender)->with('success', "Estimasi RAB tender {$tender->tender_number} telah DITOLAK.");
    }

    public function reviseRab(Request $request, Tender $tender): RedirectResponse
    {
        $user = Auth::user();
        if (!$user || !$user->role || !in_array($user->role->name, ['manager', 'owner'], true)) {
            return redirect()->route('tender.rab.show', $tender)->with('error', 'Hanya Manager atau Owner yang berhak meminta revisi RAB.');
        }

        $request->validate([
            'rab_notes' => 'required|string',
        ]);

        $notes = $request->input('rab_notes');

        $tender->update([
            'rab_status' => 'perlu_revisi',
            'rab_notes' => $notes,
            'rab_approved_by' => $user->id,
            'rab_approved_at' => now(),
        ]);

        TenderApprovalHistory::create([
            'tender_id' => $tender->id,
            'module_type' => 'rab',
            'action' => 'perlu_revisi',
            'user_id' => $user->id,
            'notes' => $notes,
        ]);

        return redirect()->route('tender.rab.show', $tender)->with('success', "Permintaan revisi RAB tender {$tender->tender_number} telah dikirimkan.");
    }
}
