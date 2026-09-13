<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Tender;
use App\Services\SalesService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BusinessApiController extends Controller
{
    public function dashboard(): JsonResponse
    {
        $won = Tender::where('result', 'Menang')->count();
        $lost = Tender::where('result', 'Kalah')->count();
        $finished = $won + $lost;

        return response()->json([
            'tenders_active' => Tender::whereNotIn('status', ['Menang', 'Kalah', 'Selesai', 'Batal'])->count(),
            'tenders_won' => $won,
            'tenders_lost' => $lost,
            'tender_value' => (float) Tender::sum('bid_value'),
            'win_rate' => $finished ? round(($won / $finished) * 100, 2) : 0,
            'active_contracts' => Contract::where('status', 'Aktif')->count(),
            'service_revenue' => (float) Invoice::whereNotNull('service_job_id')->sum('paid_amount'),
            'trade_revenue' => (float) Sale::sum('total_amount'),
            'outstanding_invoices' => (float) Invoice::whereIn('status', ['Issued', 'Partial', 'Overdue'])->selectRaw('SUM(total_amount - paid_amount) as value')->value('value'),
        ]);
    }

    public function tenders(Request $request): JsonResponse
    {
        $tenders = Tender::with(['client', 'items.product'])
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return response()->json($tenders);
    }

    public function storeTender(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'tender_number' => ['required', 'string', 'max:50', 'unique:tenders,tender_number'],
            'name' => ['required', 'string', 'max:200'],
            'client_id' => ['nullable', 'exists:clients,id'],
            'source' => ['nullable', 'string', 'max:100'],
            'found_date' => ['required', 'date'],
            'deadline' => ['nullable', 'date'],
            'estimated_value' => ['required', 'numeric', 'min:0'],
            'bid_value' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', 'string', 'max:30'],
            'result' => ['nullable', 'string', 'max:20'],
            'notes' => ['nullable', 'string'],
        ]);

        $validated['created_by'] = $request->user()->id;
        $validated['bid_value'] ??= $validated['estimated_value'];

        return response()->json(Tender::create($validated)->load('client'), 201);
    }

    public function showTender(Tender $tender): JsonResponse
    {
        return response()->json($tender->load(['client', 'documents', 'items.product', 'contract']));
    }

    public function updateTender(Request $request, Tender $tender): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:200'],
            'client_id' => ['sometimes', 'nullable', 'exists:clients,id'],
            'deadline' => ['sometimes', 'nullable', 'date'],
            'estimated_value' => ['sometimes', 'numeric', 'min:0'],
            'bid_value' => ['sometimes', 'numeric', 'min:0'],
            'status' => ['sometimes', 'string', 'max:30'],
            'result' => ['sometimes', 'nullable', 'string', 'max:20'],
            'notes' => ['sometimes', 'nullable', 'string'],
        ]);
        $tender->update($validated);

        return response()->json($tender->fresh()->load('client'));
    }

    public function contracts(Request $request): JsonResponse
    {
        return response()->json(Contract::with(['client', 'tender'])->latest()->paginate($request->integer('per_page', 15)));
    }

    public function storeContract(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'contract_number' => ['required', 'string', 'max:50', 'unique:contracts,contract_number'],
            'tender_id' => ['nullable', 'exists:tenders,id'],
            'client_id' => ['required', 'exists:clients,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'contract_value' => ['required', 'numeric', 'min:0'],
            'fee_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'status' => ['required', 'string', 'max:20'],
            'notes' => ['nullable', 'string'],
        ]);
        $validated['fee_percentage'] ??= 0;
        $validated['fee_amount'] = $validated['contract_value'] * $validated['fee_percentage'] / 100;
        $validated['created_by'] = $request->user()->id;

        return response()->json(Contract::create($validated)->load(['client', 'tender']), 201);
    }

    public function products(Request $request): JsonResponse
    {
        return response()->json(Product::where('is_active', true)->latest()->paginate($request->integer('per_page', 15)));
    }

    public function storeProduct(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'sku' => ['required', 'string', 'max:50', 'unique:products,sku'],
            'name' => ['required', 'string', 'max:200'],
            'category' => ['nullable', 'string', 'max:100'],
            'unit' => ['required', 'string', 'max:30'],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'minimum_stock' => ['required', 'numeric', 'min:0'],
        ]);

        return response()->json(Product::create($validated), 201);
    }

    public function sales(Request $request): JsonResponse
    {
        return response()->json(Sale::with(['items.product', 'invoices'])->latest()->paginate($request->integer('per_page', 15)));
    }

    public function storeSale(Request $request, SalesService $salesService): JsonResponse
    {
        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:200'],
            'customer_phone' => ['nullable', 'string', 'max:30'],
            'tender_id' => ['nullable', 'exists:tenders,id'],
            'sale_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'items.*.price' => ['required', 'numeric', 'min:0'],
        ]);

        $sale = $salesService->createSale($validated, $validated['items'], $request->user()->id);

        return response()->json($sale->load(['items.product', 'invoices']), 201);
    }
}
