<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Tender;
use App\Models\TenderDocument;
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
        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'client_id' => 'required|exists:clients,id',
            'source' => 'nullable|string|max:100',
            'found_date' => 'required|date',
            'deadline' => 'nullable|date',
            'estimated_value' => 'required|numeric|min:0',
            'bid_value' => 'nullable|numeric|min:0',
            'status' => 'required|string',
            'result' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $tender->update($validated);

        return redirect()->route('tender.index')->with('success', 'Data tender berhasil diperbarui.');
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
