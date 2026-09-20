<?php

namespace App\Http\Controllers;

use App\Models\Procurement;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Tender;
use App\Services\ProcurementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Exception;

class ProcurementController extends Controller
{
    public function index(Request $request): View
    {
        $procurements = Procurement::with(['supplier', 'tender', 'items.product'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $suppliers = Supplier::where('status', 'active')->get();
        $products = Product::where('is_active', true)->get();
        $tenders = Tender::all();

        return view('procurements.index', compact('procurements', 'suppliers', 'products', 'tenders'));
    }

    public function store(Request $request, ProcurementService $service): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'supplier_id' => 'required|exists:suppliers,id',
                'tender_id' => 'nullable|exists:tenders,id',
                'procurement_date' => 'required|date',
                'status' => 'required|string',
                'notes' => 'nullable|string',
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.quantity' => 'required|numeric|min:0.01',
                'items.*.price' => 'required|numeric|min:0',
            ]);

            $procurement = $service->createProcurement(
                $validated,
                $validated['items'],
                Auth::id() ?? 1
            );

            return redirect()->route('procurements.index')->with('success', "Pengadaan {$procurement->procurement_number} berhasil dicatat & stok telah diperbarui.");
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function receive(Procurement $procurement, ProcurementService $service): RedirectResponse
    {
        try {
            $service->receiveProcurement($procurement, Auth::id() ?? 1);

            return redirect()->route('procurements.index')->with('success', "Pengadaan {$procurement->procurement_number} berhasil diterima dan stok diperbarui.");
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
