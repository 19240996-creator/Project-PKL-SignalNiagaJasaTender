<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $query = Product::with('stockMovements');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('sku', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        $products = $query->orderBy('created_at', 'desc')->paginate(10);
        return view('products.index', compact('products'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'sku' => 'required|unique:products,sku',
            'name' => 'required|string|max:200',
            'category' => 'nullable|string|max:100',
            'unit' => 'required|string|max:30',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'minimum_stock' => 'required|numeric|min:0',
            'initial_stock' => 'nullable|numeric|min:0',
        ]);

        $initialStock = $validated['initial_stock'] ?? 0;
        unset($validated['initial_stock']);

        $product = Product::create($validated);

        if ($initialStock > 0) {
            StockMovement::create([
                'product_id' => $product->id,
                'movement_type' => 'IN',
                'quantity' => $initialStock,
                'reference_type' => 'Initial Stock',
                'reference_id' => $product->id,
                'movement_date' => now(),
                'notes' => 'Stok awal produk baru',
                'created_by' => Auth::id() ?? 1,
            ]);
        }

        return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'category' => 'nullable|string|max:100',
            'unit' => 'required|string|max:30',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'minimum_stock' => 'required|numeric|min:0',
            'is_active' => 'required|boolean',
        ]);

        $product->update($validated);

        return redirect()->route('products.index')->with('success', 'Data produk berhasil diperbarui.');
    }

    public function adjustStock(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'movement_type' => 'required|in:IN,OUT,ADJUSTMENT',
            'quantity' => 'required|numeric|min:0.01',
            'notes' => 'required|string',
        ]);

        if ($validated['movement_type'] === 'OUT' && $validated['quantity'] > $product->stock) {
            throw ValidationException::withMessages([
                'quantity' => "Stok {$product->name} tidak mencukupi. Tersedia: {$product->stock}.",
            ]);
        }

        StockMovement::create([
            'product_id' => $product->id,
            'movement_type' => $validated['movement_type'],
            'quantity' => $validated['quantity'],
            'reference_type' => 'Manual Adjustment',
            'reference_id' => $product->id,
            'movement_date' => now(),
            'notes' => $validated['notes'],
            'created_by' => Auth::id() ?? 1,
        ]);

        return redirect()->route('products.index')->with('success', 'Penyesuaian stok produk berhasil dicatat.');
    }
}
