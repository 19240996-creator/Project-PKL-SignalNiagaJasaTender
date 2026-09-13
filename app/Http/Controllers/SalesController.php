<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\Tender;
use App\Services\SalesService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Exception;

class SalesController extends Controller
{
    public function index(Request $request): View
    {
        $sales = Sale::with(['tender', 'items.product', 'invoices'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $products = Product::where('is_active', true)->get();
        $tenders = Tender::all();

        return view('sales.index', compact('sales', 'products', 'tenders'));
    }

    public function store(Request $request, SalesService $service): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'customer_name' => 'required|string|max:200',
                'customer_phone' => 'nullable|string|max:30',
                'tender_id' => 'nullable|exists:tenders,id',
                'sale_date' => 'required|date',
                'notes' => 'nullable|string',
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.quantity' => 'required|numeric|min:0.01',
                'items.*.price' => 'required|numeric|min:0',
            ]);

            $sale = $service->createSale(
                $validated,
                $validated['items'],
                Auth::id() ?? 1
            );

            return redirect()->route('sales.index')->with('success', "Penjualan {$sale->sale_number} berhasil dicatat, stok berkurang, dan Invoice otomatis diterbitkan.");
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }
}
