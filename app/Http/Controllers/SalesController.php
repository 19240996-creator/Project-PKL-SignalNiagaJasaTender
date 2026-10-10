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
        $user = Auth::user();
        $query = Sale::with(['tender', 'items.product', 'invoices', 'creator', 'approver']);

        // Admin hanya melihat data yang ia input sendiri sesuai PRD
        if ($user && $user->role && $user->role->name === 'admin') {
            $query->where('created_by', $user->id);
        }

        if ($request->filled('approval_status')) {
            $query->where('approval_status', $request->approval_status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('sale_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('nama_barang', 'like', "%{$search}%");
            });
        }

        $pendingCount = Sale::where('approval_status', 'pending')->count();
        $sales = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();
        $products = Product::where('is_active', true)->get();
        $tenders = Tender::with('client')->get();

        $distributionMode = $request->routeIs('distribusi.*');

        return view('sales.index', compact('sales', 'products', 'tenders', 'pendingCount', 'distributionMode'));
    }

    public function store(Request $request, SalesService $service): RedirectResponse
    {
        $user = Auth::user();

        try {
            $validated = $request->validate([
                'customer_name' => 'required|string|max:200',
                'customer_phone' => 'nullable|string|max:30',
                'nama_barang' => 'nullable|string|max:200',
                'kuantitas' => 'nullable|numeric|min:0.01',
                'harga_satuan' => 'nullable|numeric|min:0',
                'catatan_pengiriman' => 'nullable|string',
                'tender_id' => 'nullable|exists:tenders,id',
                'sale_date' => 'required|date',
                'notes' => 'nullable|string',
                'product_id' => 'nullable|exists:products,id',
                'items' => 'nullable|array',
                'items.*.product_id' => 'required_with:items|exists:products,id',
                'items.*.quantity' => 'required_with:items|numeric|min:0.01',
                'items.*.price' => 'required_with:items|numeric|min:0',
            ]);

            // Format items dari form modal
            $items = $validated['items'] ?? [];

            // Jika user memilih produk tunggal dari form cepat
            if (empty($items)) {
                $productId = $request->input('product_id');
                if ($productId) {
                    $prod = Product::findOrFail($productId);
                    $qty = (float) ($request->input('kuantitas') ?: 1);
                    $price = (float) ($request->input('harga_satuan') ?: $prod->selling_price);
                    $items[] = [
                        'product_id' => $prod->id,
                        'quantity' => $qty,
                        'price' => $price,
                    ];
                    $validated['nama_barang'] = $prod->name;
                    $validated['kuantitas'] = $qty;
                    $validated['harga_satuan'] = $price;
                } else {
                    // Fallback ke produk default pertama jika tersedia
                    $firstProd = Product::first();
                    if ($firstProd) {
                        $qty = (float) ($request->input('kuantitas') ?: 1);
                        $price = (float) ($request->input('harga_satuan') ?: $firstProd->selling_price);
                        $items[] = [
                            'product_id' => $firstProd->id,
                            'quantity' => $qty,
                            'price' => $price,
                        ];
                    }
                }
            }

            if (empty($items)) {
                throw new Exception('Pilih minimal satu produk / barang untuk transaksi.');
            }

            // Status approval: Admin -> pending, Manager/Owner -> approved
            if ($user && $user->role && $user->role->name === 'admin') {
                $validated['approval_status'] = 'pending';
            } else {
                $validated['approval_status'] = 'approved';
                $validated['approved_by'] = $user->id ?? null;
                $validated['approved_at'] = now();
            }

            $sale = $service->createSale(
                $validated,
                $items,
                $user->id ?? 1
            );

            $msg = ($validated['approval_status'] === 'pending')
                ? "Transaksi Penjualan Barang {$sale->sale_number} berhasil dicatat dan berstatus PENDING menunggu persetujuan Manager."
                : "Penjualan {$sale->sale_number} berhasil dicatat, stok berkurang, dan Invoice otomatis diterbitkan.";

            return redirect()->route('sales.index')->with('success', $msg);
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function approve(Request $request, Sale $sale): RedirectResponse
    {
        $user = Auth::user();
        if (!$user || !$user->role || !in_array($user->role->name, ['manager', 'owner'], true)) {
            return redirect()->route('sales.index')->with('error', 'Hanya Manager atau Owner yang berhak menyetujui (Approve) transaksi barang.');
        }

        $sale->update([
            'approval_status' => 'approved',
            'approved_by' => $user->id,
            'approved_at' => now(),
            'approval_notes' => $request->input('notes'),
        ]);

        return redirect()->route('sales.index')->with('success', "Penjualan Barang {$sale->sale_number} berhasil disetujui (Approved).");
    }

    public function reject(Request $request, Sale $sale): RedirectResponse
    {
        $user = Auth::user();
        if (!$user || !$user->role || !in_array($user->role->name, ['manager', 'owner'], true)) {
            return redirect()->route('sales.index')->with('error', 'Hanya Manager atau Owner yang berhak menolak (Reject) transaksi barang.');
        }

        $sale->update([
            'approval_status' => 'rejected',
            'approved_by' => $user->id,
            'approved_at' => now(),
            'approval_notes' => $request->input('notes', 'Ditolak oleh ' . $user->name),
        ]);

        return redirect()->route('sales.index')->with('success', "Penjualan Barang {$sale->sale_number} telah ditolak (Rejected).");
    }
}
