<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Client;
use App\Models\Tender;
use App\Models\TenderApprovalHistory;
use App\Models\TenderDocument;
use App\Models\TenderEvaluation;
use App\Services\TenderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Exception;

class TenderController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        $query = Tender::with(['client', 'documents', 'items.product', 'creator', 'approver', 'approvalHistories.user', 'rabItems', 'assignments', 'deletionRequester']);

        // Admin hanya melihat data yang ia input sendiri sesuai PRD
        if ($user && $user->role && $user->role->name === 'admin') {
            $query->where('created_by', $user->id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('approval_status')) {
            $query->where('approval_status', $request->approval_status);
        }

        if ($request->filled('submission_status')) {
            $query->where('submission_status', $request->submission_status);
        }

        if ($request->filled('deletion_status')) {
            $query->where('deletion_status', $request->deletion_status);
        }

        if ($request->filled('metode_penanganan')) {
            $query->where('metode_penanganan', $request->metode_penanganan);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('tender_number', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('nama_vendor_relasi', 'like', "%{$search}%");
            });
        }

        $baseCountQuery = clone $query;
        $statusCounts = [
            'total' => (clone $baseCountQuery)->count(),
            'draft' => (clone $baseCountQuery)->where('submission_status', 'draft')->count(),
            'diajukan' => (clone $baseCountQuery)->where(function ($q) {
                $q->where('submission_status', 'diajukan')->orWhere('approval_status', 'pending');
            })->count(),
            'disetujui' => (clone $baseCountQuery)->where(function ($q) {
                $q->where('submission_status', 'disetujui')->orWhere('approval_status', 'approved');
            })->count(),
            'ditolak' => (clone $baseCountQuery)->where(function ($q) {
                $q->where('submission_status', 'ditolak')->orWhere('approval_status', 'rejected');
            })->count(),
            'perlu_revisi' => (clone $baseCountQuery)->where('submission_status', 'perlu_revisi')->count(),
            'pending_deletion' => (clone $baseCountQuery)->where('deletion_status', 'pending_deletion')->count(),
        ];

        $pendingCount = Tender::where('approval_status', 'pending')->orWhere('submission_status', 'diajukan')->count();
        $tenders = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();
        $clients = Client::where('status', 'active')->get();

        return view('tenders.index', compact('tenders', 'clients', 'pendingCount', 'statusCounts'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $resolvedClientId = $this->resolveClientId($request);
        if ($resolvedClientId) {
            $request->merge(['client_id' => $resolvedClientId]);
        }

        $validated = $request->validate([
            'tender_number' => 'required|unique:tenders,tender_number',
            'name' => 'required|string|max:200',
            'client_id' => 'required|exists:clients,id',
            'source' => 'nullable|string|max:100',
            'found_date' => 'required|date',
            'deadline' => 'nullable|date',
            'estimated_value' => 'required|numeric|min:0',
            'bid_value' => 'nullable|numeric|min:0',
            'status' => 'required|string',
            'metode_penanganan' => 'required|in:internal,vendor_relasi',
            'nama_vendor_relasi' => 'required_if:metode_penanganan,vendor_relasi|nullable|string|max:200',
            'alasan_metode' => 'nullable|string',
            'notes' => 'nullable|string',
            'action_type' => 'nullable|in:draft,submit',
            'items' => 'nullable|array',
            'items.*.product_id' => 'nullable|exists:products,id',
            'items.*.item_name' => 'required_with:items.*.quantity|nullable|string|max:200',
            'items.*.quantity' => 'required_with:items.*.item_name|nullable|numeric|min:0.01',
            'items.*.unit' => 'nullable|string|max:30',
            'items.*.estimated_price' => 'nullable|numeric|min:0',
            'items.*.notes' => 'nullable|string',
        ], [
            'client_id.required' => 'Pilih klien dari daftar atau ketik nama klien / instansi baru.',
            'client_id.exists' => 'Data klien yang dipilih tidak valid di sistem.',
        ]);

        $validated['created_by'] = $user->id ?? 1;
        $validated['found_date'] = $validated['found_date'] ?? now()->toDateString();
        $validated['estimated_value'] = $validated['estimated_value'] ?? ($validated['bid_value'] ?? 0);
        $validated['bid_value'] = $validated['bid_value'] ?? $validated['estimated_value'];

        $actionType = $request->input('action_type', 'submit');

        if ($user && $user->role && in_array($user->role->name, ['manager', 'owner'], true)) {
            $validated['approval_status'] = 'approved';
            $validated['submission_status'] = 'disetujui';
            $validated['approved_by'] = $user->id;
            $validated['approved_at'] = now();
        } else {
            if ($actionType === 'draft') {
                $validated['approval_status'] = 'draft';
                $validated['submission_status'] = 'draft';
            } else {
                $validated['approval_status'] = 'pending';
                $validated['submission_status'] = 'diajukan';
            }
        }

        $items = $validated['items'] ?? [];
        unset($validated['items'], $validated['action_type']);

        $tender = Tender::create($validated);
        $tender->items()->createMany(array_values(array_filter($items, fn (array $item) => filled($item['item_name'] ?? null))));

        // Rekam riwayat awal jika diajukan
        if ($tender->submission_status === 'diajukan') {
            TenderApprovalHistory::create([
                'tender_id' => $tender->id,
                'module_type' => 'administrasi',
                'action' => 'diajukan',
                'user_id' => $user->id ?? null,
                'notes' => 'Pengajuan tender baru ke Manajemen',
            ]);
        }

        $msg = match ($tender->submission_status) {
            'draft' => 'Tender baru berhasil disimpan sebagai DRAFT.',
            'diajukan' => 'Tender baru berhasil dicatat dan DIAJUKAN ke Manajemen untuk persetujuan.',
            default => 'Tender baru berhasil ditambahkan dan disetujui.',
        };

        return redirect()->route('tender.index')->with('success', $msg);
    }

    public function update(Request $request, Tender $tender): RedirectResponse
    {
        $user = Auth::user();
        $canForceUpdate = ($user && $user->role && in_array($user->role->name, ['owner', 'manager'], true));
        if (!$canForceUpdate && in_array($tender->status, ['Selesai', 'Kontrak', 'Batal'], true)) {
            return redirect()->route('tender.index')->with('error', 'Tender dengan status terminal tidak dapat diubah oleh pengguna biasa.');
        }

        if ($request->has('client_id') || $request->has('new_client_name')) {
            $resolvedClientId = $this->resolveClientId($request);
            if ($resolvedClientId) {
                $request->merge(['client_id' => $resolvedClientId]);
            }
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:200',
            'client_id' => 'sometimes|required|exists:clients,id',
            'source' => 'nullable|string|max:100',
            'found_date' => 'sometimes|required|date',
            'deadline' => 'nullable|date',
            'estimated_value' => 'sometimes|required|numeric|min:0',
            'bid_value' => 'nullable|numeric|min:0',
            'status' => 'required|string|in:Ditemukan,Evaluasi,Persiapan Dokumen,Penawaran,Menang,Kalah,Kontrak,Selesai,Batal',
            'metode_penanganan' => 'nullable|in:internal,vendor_relasi',
            'nama_vendor_relasi' => 'nullable|string|max:200',
            'alasan_metode' => 'nullable|string',
            'result' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        if (isset($validated['status'])) {
            if ($validated['status'] === 'Menang') {
                $validated['result'] = 'Menang';
            } elseif ($validated['status'] === 'Kalah') {
                $validated['result'] = 'Kalah';
            } elseif (in_array($validated['status'], ['Ditemukan', 'Evaluasi', 'Persiapan Dokumen', 'Penawaran'], true)) {
                $validated['result'] = null;
            }
        }

        $oldStatus = $tender->status;
        $oldBidValue = (float) $tender->bid_value;
        $oldName = $tender->name;

        $tender->update($validated);

        // Mark as audit_logged to prevent duplicate in AuditActivity middleware
        $request->attributes->set('audit_logged', true);

        // Record to AuditLog
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'update',
            'table_name' => 'tender.update',
            'record_id' => $tender->id,
            'old_values' => [
                'tender' => $tender->tender_number,
                'status' => $oldStatus,
                'bid_value' => $oldBidValue,
            ],
            'new_values' => [
                'tender' => $tender->tender_number,
                'status' => $tender->status,
                'bid_value' => (float) $tender->bid_value,
            ],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);

        return redirect()->route('tender.index')->with('success', "Status pipeline tender \"{$tender->tender_number}\" berhasil diperbarui.");
    }

    public function destroy(Request $request, Tender $tender): RedirectResponse
    {
        $user = Auth::user();

        // Jika user adalah Admin, penghapusan wajib melalui izin Manajemen
        if ($user && $user->role && $user->role->name === 'admin') {
            return $this->requestDeletion($request, $tender);
        }

        // Jika user adalah Manager atau Owner (Manajemen)
        if (!$user || !$user->role || !in_array($user->role->name, ['manager', 'owner'], true)) {
            return redirect()->route('tender.index')->with('error', 'Anda tidak memiliki hak akses untuk menghapus data tender.');
        }

        $tenderNumber = $tender->tender_number;

        // Catat ke AuditLog sebelum data dihapus
        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'delete',
            'table_name' => 'tender.delete',
            'record_id' => $tender->id,
            'old_values' => [
                'tender' => $tender->tender_number,
                'name' => $tender->name,
                'status' => $tender->status,
                'bid_value' => (float) $tender->bid_value,
            ],
            'new_values' => null,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);

        // Bersihkan berkas dokumen fisik jika ada
        foreach ($tender->documents as $doc) {
            if (Storage::disk('public')->exists($doc->file_path)) {
                Storage::disk('public')->delete($doc->file_path);
            }
        }

        $tender->delete();

        return redirect()->route('tender.index')->with('success', "Data tender \"{$tenderNumber}\" berhasil dihapus permanen oleh Manajemen.");
    }

    public function requestDeletion(Request $request, Tender $tender): RedirectResponse
    {
        $user = Auth::user();

        if ($tender->deletion_status === 'pending_deletion') {
            return redirect()->route('tender.index')->with('error', "Permohonan penghapusan tender {$tender->tender_number} sudah diajukan sebelumnya dan sedang menunggu persetujuan Manajemen.");
        }

        $validated = $request->validate([
            'deletion_reason' => 'required|string|min:5|max:1000',
        ], [
            'deletion_reason.required' => 'Alasan penghapusan wajib diisi agar Manajemen dapat meninjau permohonan.',
            'deletion_reason.min' => 'Alasan penghapusan minimal 5 karakter.',
        ]);

        $tender->update([
            'deletion_status' => 'pending_deletion',
            'deletion_reason' => $validated['deletion_reason'],
            'deletion_requested_by' => $user->id ?? 1,
            'deletion_requested_at' => now(),
        ]);

        TenderApprovalHistory::create([
            'tender_id' => $tender->id,
            'module_type' => 'administrasi',
            'action' => 'diajukan_hapus',
            'user_id' => $user->id ?? 1,
            'notes' => 'Permohonan izin hapus oleh Admin: ' . $validated['deletion_reason'],
        ]);

        AuditLog::create([
            'user_id' => $user->id ?? 1,
            'action' => 'request_deletion',
            'table_name' => 'tender.request_deletion',
            'record_id' => $tender->id,
            'old_values' => ['tender' => $tender->tender_number],
            'new_values' => [
                'deletion_status' => 'pending_deletion',
                'deletion_reason' => $validated['deletion_reason'],
            ],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);

        return redirect()->route('tender.index')->with('success', "Permohonan penghapusan tender {$tender->tender_number} berhasil diajukan kepada Manajemen. Menunggu izin/persetujuan.");
    }

    public function approveDeletion(Request $request, Tender $tender): RedirectResponse
    {
        $user = Auth::user();

        if (!$user || !$user->role || !in_array($user->role->name, ['manager', 'owner'], true)) {
            return redirect()->route('tender.index')->with('error', 'Hanya Manajemen (Manager atau Owner) yang berhak menyetujui penghapusan data tender.');
        }

        if ($tender->deletion_status !== 'pending_deletion') {
            return redirect()->route('tender.index')->with('error', 'Tender ini tidak dalam status menunggu izin hapus.');
        }

        $tenderNumber = $tender->tender_number;
        $requesterName = $tender->deletionRequester?->name ?? 'Admin';
        $reason = $tender->deletion_reason;

        // Catat riwayat sebelum dihapus
        TenderApprovalHistory::create([
            'tender_id' => $tender->id,
            'module_type' => 'administrasi',
            'action' => 'disetujui_hapus',
            'user_id' => $user->id,
            'notes' => "Penghapusan disetujui oleh {$user->name}. Permohonan awal dari {$requesterName}: {$reason}",
        ]);

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'approve_deletion',
            'table_name' => 'tender.approve_deletion',
            'record_id' => $tender->id,
            'old_values' => [
                'tender' => $tender->tender_number,
                'name' => $tender->name,
                'requested_by' => $requesterName,
                'deletion_reason' => $reason,
            ],
            'new_values' => ['status' => 'permanently_deleted'],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);

        // Bersihkan berkas dokumen fisik jika ada
        foreach ($tender->documents as $doc) {
            if (Storage::disk('public')->exists($doc->file_path)) {
                Storage::disk('public')->delete($doc->file_path);
            }
        }

        $tender->delete();

        return redirect()->route('tender.index')->with('success', "Izin penghapusan tender \"{$tenderNumber}\" disetujui. Data tender telah dihapus permanen oleh Manajemen.");
    }

    public function rejectDeletion(Request $request, Tender $tender): RedirectResponse
    {
        $user = Auth::user();

        if (!$user || !$user->role || !in_array($user->role->name, ['manager', 'owner'], true)) {
            return redirect()->route('tender.index')->with('error', 'Hanya Manajemen (Manager atau Owner) yang berhak menolak permohonan hapus tender.');
        }

        if ($tender->deletion_status !== 'pending_deletion') {
            return redirect()->route('tender.index')->with('error', 'Tender ini tidak dalam status menunggu izin hapus.');
        }

        $notes = $request->input('notes', "Permohonan izin hapus ditolak oleh {$user->name}");
        $oldReason = $tender->deletion_reason;

        $tender->update([
            'deletion_status' => 'none',
            'deletion_reason' => null,
            'deletion_requested_by' => null,
            'deletion_requested_at' => null,
        ]);

        TenderApprovalHistory::create([
            'tender_id' => $tender->id,
            'module_type' => 'administrasi',
            'action' => 'ditolak_hapus',
            'user_id' => $user->id,
            'notes' => "{$notes} (Alasan permohonan sebelumnya: {$oldReason})",
        ]);

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'reject_deletion',
            'table_name' => 'tender.reject_deletion',
            'record_id' => $tender->id,
            'old_values' => ['deletion_status' => 'pending_deletion', 'reason' => $oldReason],
            'new_values' => ['deletion_status' => 'none'],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);

        return redirect()->route('tender.index')->with('success', "Permohonan penghapusan tender \"{$tender->tender_number}\" telah ditolak. Data tender tetap dipertahankan.");
    }

    public function uploadDocument(Request $request, Tender $tender): RedirectResponse
    {
        $request->validate([
            'document_name' => 'required|string|max:200',
            'file' => 'required|file|max:10240', // Max 10MB
        ]);

        $file = $request->file('file');
        $path = $file->store('tender_documents', 'public');

        $previousDocuments = $tender->documents()->get();

        foreach ($previousDocuments as $previousDocument) {
            Storage::disk('public')->delete($previousDocument->file_path);
        }

        $tender->documents()->delete();

        TenderDocument::create([
            'tender_id' => $tender->id,
            'uploaded_by' => Auth::id() ?? 1,
            'document_name' => $request->document_name,
            'file_path' => $path,
            'file_type' => $file->getClientMimeType(),
            'file_size' => $file->getSize(),
        ]);

        return redirect()->route('tender.index')->with('success', 'Dokumen tender berhasil diunggah.');
    }

    public function viewDocument(Tender $tender, TenderDocument $document)
    {
        abort_unless($document->tender_id === $tender->id, 404);

        if (!Storage::disk('public')->exists($document->file_path)) {
            return redirect()->route('tender.index')->with('error', 'File dokumen tidak ditemukan di penyimpanan.');
        }

        return response()->file(
            Storage::disk('public')->path($document->file_path),
            [
                'Content-Type' => $document->file_type ?: 'application/octet-stream',
                'Content-Disposition' => 'inline; filename="' . basename($document->file_path) . '"',
            ]
        );
    }

    public function storeEvaluation(Request $request, Tender $tender): RedirectResponse
    {
        $validated = $request->validate([
            'score' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'decision' => ['required', 'in:Proceed,Hold,Reject'],
            'notes' => ['nullable', 'string'],
        ]);

        TenderEvaluation::create([
            'tender_id' => $tender->id,
            'evaluator_id' => Auth::id() ?? 1,
            'score' => $validated['score'] ?? null,
            'decision' => $validated['decision'],
            'notes' => $validated['notes'] ?? null,
            'evaluated_at' => now(),
        ]);

        $pipelineUpdate = match ($validated['decision']) {
            'Proceed' => ['status' => 'Penawaran', 'result' => null],
            'Reject' => ['status' => 'Kalah', 'result' => 'Kalah'],
            default => ['status' => 'Evaluasi', 'result' => null],
        };

        $tender->update($pipelineUpdate);

        return redirect()->route('tender.index')->with('success', 'Evaluasi tender berhasil dicatat.');
    }

    public function convertToContract(Request $request, Tender $tender, TenderService $service): RedirectResponse
    {
        try {
            $contractData = $request->validate([
                'contract_number' => 'required|unique:contracts,contract_number',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'contract_value' => 'required|numeric|min:0',
                'fee_percentage' => 'nullable|numeric|min:0|max:100',
            ]);

            $contract = $service->convertTenderToContract($tender, $contractData, Auth::id() ?? 1);

            return redirect()->route('contracts.index')->with('success', "Tender {$tender->tender_number} berhasil dikonversi ke Kontrak Jasa {$contract->contract_number}.");
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function submitForApproval(Request $request, Tender $tender): RedirectResponse
    {
        $user = Auth::user();
        if ($tender->submission_status === 'diajukan' && $tender->approval_status === 'pending') {
            return redirect()->route('tender.index')->with('error', 'Tender ini sudah diajukan ke Manajemen dan sedang menunggu persetujuan.');
        }

        $tender->update([
            'submission_status' => 'diajukan',
            'approval_status' => 'pending',
        ]);

        TenderApprovalHistory::create([
            'tender_id' => $tender->id,
            'module_type' => 'administrasi',
            'action' => 'diajukan',
            'user_id' => $user->id ?? null,
            'notes' => $request->input('notes', 'Pengajuan kembali tender ke Manajemen'),
        ]);

        return redirect()->route('tender.index')->with('success', "Tender {$tender->tender_number} berhasil DIAJUKAN kepada Manajemen untuk persetujuan.");
    }

    public function approve(Request $request, Tender $tender): RedirectResponse
    {
        $user = Auth::user();
        if (!$user || !$user->role || !in_array($user->role->name, ['manager', 'owner'], true)) {
            return redirect()->route('tender.index')->with('error', 'Hanya Manager atau Owner yang berhak menyetujui (Approve) tender.');
        }

        $notes = $request->input('notes', 'Disetujui oleh ' . $user->name);

        $tender->update([
            'approval_status' => 'approved',
            'submission_status' => 'disetujui',
            'approved_by' => $user->id,
            'approved_at' => now(),
            'approval_notes' => $notes,
        ]);

        TenderApprovalHistory::create([
            'tender_id' => $tender->id,
            'module_type' => 'administrasi',
            'action' => 'disetujui',
            'user_id' => $user->id,
            'notes' => $notes,
        ]);

        return redirect()->route('tender.index')->with('success', "Tender {$tender->tender_number} berhasil disetujui (Approved).");
    }

    public function reject(Request $request, Tender $tender): RedirectResponse
    {
        $user = Auth::user();
        if (!$user || !$user->role || !in_array($user->role->name, ['manager', 'owner'], true)) {
            return redirect()->route('tender.index')->with('error', 'Hanya Manager atau Owner yang berhak menolak (Reject) tender.');
        }

        $notes = $request->input('notes', 'Ditolak oleh ' . $user->name);

        $tender->update([
            'approval_status' => 'rejected',
            'submission_status' => 'ditolak',
            'approved_by' => $user->id,
            'approved_at' => now(),
            'approval_notes' => $notes,
        ]);

        TenderApprovalHistory::create([
            'tender_id' => $tender->id,
            'module_type' => 'administrasi',
            'action' => 'ditolak',
            'user_id' => $user->id,
            'notes' => $notes,
        ]);

        return redirect()->route('tender.index')->with('success', "Tender {$tender->tender_number} telah ditolak (Rejected).");
    }

    public function revise(Request $request, Tender $tender): RedirectResponse
    {
        $user = Auth::user();
        if (!$user || !$user->role || !in_array($user->role->name, ['manager', 'owner'], true)) {
            return redirect()->route('tender.index')->with('error', 'Hanya Manager atau Owner yang berhak meminta revisi tender.');
        }

        $request->validate([
            'revisi_notes' => 'required|string',
        ]);

        $notes = $request->input('revisi_notes');

        $tender->update([
            'submission_status' => 'perlu_revisi',
            'approval_status' => 'pending',
            'revisi_notes' => $notes,
            'approved_by' => $user->id,
            'approved_at' => now(),
        ]);

        TenderApprovalHistory::create([
            'tender_id' => $tender->id,
            'module_type' => 'administrasi',
            'action' => 'perlu_revisi',
            'user_id' => $user->id,
            'notes' => $notes,
        ]);

        return redirect()->route('tender.index')->with('success', "Permintaan revisi untuk tender {$tender->tender_number} berhasil dikirimkan ke Kepala Unit / Admin.");
    }

    private function resolveClientId(Request $request): ?int
    {
        $newClientName = trim((string) $request->input('new_client_name'));

        if ($newClientName !== '') {
            $existingClient = Client::where('name', $newClientName)
                ->orWhere('company_name', $newClientName)
                ->first();

            if ($existingClient) {
                return (int) $existingClient->id;
            }

            $newCode = 'CLI-' . strtoupper(Str::random(5));
            while (Client::where('code', $newCode)->exists()) {
                $newCode = 'CLI-' . strtoupper(Str::random(5));
            }

            $newClient = Client::create([
                'code' => $newCode,
                'name' => $newClientName,
                'company_name' => $newClientName,
                'status' => 'active',
            ]);

            return (int) $newClient->id;
        }

        $clientId = $request->input('client_id');
        if ($clientId === '__new__' || empty($clientId)) {
            return null;
        }

        return (int) $clientId;
    }
}
