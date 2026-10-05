<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Client;
use App\Models\Tender;
use App\Models\TenderDocument;
use App\Models\TenderEvaluation;
use App\Services\TenderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Exception;

class TenderController extends Controller
{
    public function index(Request $request): View
    {
        $query = Tender::with(['client', 'documents', 'items.product']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('tender_number', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }

        $tenders = $query->orderBy('created_at', 'desc')->paginate(10);
        $clients = Client::where('status', 'active')->get();

        return view('tenders.index', compact('tenders', 'clients'));
    }

    public function store(Request $request): RedirectResponse
    {
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
            'notes' => 'nullable|string',
            'items' => 'nullable|array',
            'items.*.product_id' => 'nullable|exists:products,id',
            'items.*.item_name' => 'required_with:items.*.quantity|nullable|string|max:200',
            'items.*.quantity' => 'required_with:items.*.item_name|nullable|numeric|min:0.01',
            'items.*.unit' => 'nullable|string|max:30',
            'items.*.estimated_price' => 'nullable|numeric|min:0',
            'items.*.notes' => 'nullable|string',
        ]);

        $validated['created_by'] = Auth::id() ?? 1;
        $validated['bid_value'] = $validated['bid_value'] ?? $validated['estimated_value'];

        $items = $validated['items'] ?? [];
        unset($validated['items']);

        $tender = Tender::create($validated);
        $tender->items()->createMany(array_values(array_filter($items, fn (array $item) => filled($item['item_name'] ?? null))));

        return redirect()->route('tender.index')->with('success', 'Tender baru berhasil ditambahkan.');
    }

    public function update(Request $request, Tender $tender): RedirectResponse
    {
        $user = Auth::user();
        $isSuperAdmin = ($user && $user->role && $user->role->name === 'super_admin');
        if (!$isSuperAdmin && in_array($tender->status, ['Selesai', 'Kontrak', 'Batal'], true)) {
            return redirect()->route('tender.index')->with('error', 'Tender dengan status terminal tidak dapat diubah oleh pengguna biasa.');
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

        return redirect()->route('tender.index')->with('success', "Status pipeline tender \"{$tender->tender_number}\" berhasil diperbarui ke tahap \"{$tender->status}\".");
    }

    public function destroy(Tender $tender): RedirectResponse
    {
        $tender->delete();
        return redirect()->route('tender.index')->with('success', 'Tender berhasil dihapus.');
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
}
