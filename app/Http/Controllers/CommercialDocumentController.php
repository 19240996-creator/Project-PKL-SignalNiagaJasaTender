<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\CustomerOrder;
use App\Models\Product;
use App\Models\SalesQuotation;
use App\Models\ServiceQuotation;
use App\Services\SalesService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CommercialDocumentController extends Controller
{
    public function index(): View
    {
        return view('commercial.index', [
            'serviceQuotations' => ServiceQuotation::with('client')->latest()->paginate(10, ['*'], 'service_page'),
            'salesQuotations' => SalesQuotation::latest()->paginate(10, ['*'], 'sales_page'),
            'orders' => CustomerOrder::latest()->paginate(10, ['*'], 'order_page'),
            'clients' => Client::where('status', 'active')->get(),
            'products' => Product::where('is_active', true)->get(),
        ]);
    }

    public function storeServiceQuotation(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'client_id' => ['required', 'exists:clients,id'], 'quotation_number' => ['required', 'unique:service_quotations,quotation_number'],
            'quotation_date' => ['required', 'date'], 'valid_until' => ['nullable', 'date'], 'status' => ['required', 'in:Draft,Sent,Approved,Rejected'],
            'notes' => ['nullable', 'string'], 'items' => ['required', 'array', 'min:1'], 'items.*.description' => ['required', 'string', 'max:200'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'], 'items.*.price' => ['required', 'numeric', 'min:0'],
        ]);
        $items = array_map(fn ($item) => [...$item, 'subtotal' => $item['quantity'] * $item['price']], $validated['items']);
        $subtotal = collect($items)->sum('subtotal');
        ServiceQuotation::create([...$validated, 'items' => $items, 'subtotal' => $subtotal, 'tax_amount' => $subtotal * .11, 'total_amount' => $subtotal * 1.11, 'created_by' => $request->user()->id]);
        return back()->with('success', 'Quotation jasa berhasil dibuat.');
    }

    public function storeSalesQuotation(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:200'], 'customer_phone' => ['nullable', 'string', 'max:30'], 'quotation_number' => ['required', 'unique:sales_quotations,quotation_number'],
            'quotation_date' => ['required', 'date'], 'valid_until' => ['nullable', 'date'], 'status' => ['required', 'in:Draft,Sent,Approved,Rejected'], 'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'], 'items.*.product_id' => ['required', 'exists:products,id'], 'items.*.quantity' => ['required', 'numeric', 'min:0.01'], 'items.*.price' => ['required', 'numeric', 'min:0'],
        ]);
        $items = array_map(fn ($item) => [...$item, 'subtotal' => $item['quantity'] * $item['price']], $validated['items']);
        $subtotal = collect($items)->sum('subtotal');
        SalesQuotation::create([...$validated, 'items' => $items, 'subtotal' => $subtotal, 'tax_amount' => $subtotal * .11, 'total_amount' => $subtotal * 1.11, 'created_by' => $request->user()->id]);
        return back()->with('success', 'Quotation penjualan berhasil dibuat.');
    }

    public function storeOrder(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'sales_quotation_id' => ['nullable', 'exists:sales_quotations,id'], 'order_number' => ['required', 'unique:customer_orders,order_number'],
            'customer_name' => ['required', 'string', 'max:200'], 'customer_phone' => ['nullable', 'string', 'max:30'], 'order_date' => ['required', 'date'],
            'status' => ['required', 'in:Draft,Confirmed,Cancelled'], 'notes' => ['nullable', 'string'], 'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'], 'items.*.quantity' => ['required', 'numeric', 'min:0.01'], 'items.*.price' => ['required', 'numeric', 'min:0'],
        ]);
        $items = array_map(fn ($item) => [...$item, 'subtotal' => $item['quantity'] * $item['price']], $validated['items']);
        CustomerOrder::create([...$validated, 'items' => $items, 'total_amount' => collect($items)->sum('subtotal'), 'created_by' => $request->user()->id]);
        return back()->with('success', 'Customer order berhasil dibuat.');
    }

    public function convertOrder(Request $request, CustomerOrder $order, SalesService $salesService): RedirectResponse
    {
        abort_unless($order->status === 'Confirmed', 422, 'Customer order harus berstatus Confirmed.');

        $sale = $salesService->createSale([
            'customer_name' => $order->customer_name,
            'customer_phone' => $order->customer_phone,
            'sale_date' => now()->toDateString(),
            'notes' => 'Dibuat dari Customer Order ' . $order->order_number,
        ], $order->items, $request->user()->id);

        $order->update(['status' => 'Processed']);

        return back()->with('success', "Customer order {$order->order_number} dikonversi menjadi penjualan {$sale->sale_number}.");
    }
}
