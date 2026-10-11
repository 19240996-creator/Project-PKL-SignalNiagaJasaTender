<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Product;
use App\Models\ProductChangeRequest;
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
        $baseQuery = Product::query();

        $totalProductsCount = (clone $baseQuery)->count();
        $pendingDeletionCount = (clone $baseQuery)->where('deletion_status', 'pending_deletion')->count();
        $pendingAdjustmentCount = ProductChangeRequest::where('request_type', 'stock_adjustment')->where('status', 'pending')->count();
        $pendingEditCount = ProductChangeRequest::where('request_type', 'price_edit')->where('status', 'pending')->count();
        $totalPendingCount = $pendingDeletionCount + $pendingAdjustmentCount + $pendingEditCount;

        $query = Product::with([
            'stockMovements',
            'deletionRequester',
            'pendingAdjustment.requester',
            'pendingEdit.requester',
        ]);

        if ($request->filled('filter')) {
            $filter = $request->filter;
            if ($filter === 'pending_deletion') {
                $query->where('deletion_status', 'pending_deletion');
            } elseif ($filter === 'pending_adjustment') {
                $query->whereHas('pendingAdjustment');
            } elseif ($filter === 'pending_edit') {
                $query->whereHas('pendingEdit');
            } elseif ($filter === 'all_pending') {
                $query->where(function ($q) {
                    $q->where('deletion_status', 'pending_deletion')
                      ->orWhereHas('pendingAdjustment')
                      ->orWhereHas('pendingEdit');
                });
            }
        } elseif ($request->filled('deletion_status')) {
            $query->where('deletion_status', $request->deletion_status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('sku', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        $products = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();
        return view('products.index', compact(
            'products',
            'totalProductsCount',
            'pendingDeletionCount',
            'pendingAdjustmentCount',
            'pendingEditCount',
            'totalPendingCount'
        ));
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
        $user = Auth::user();

        // Only owner, manager, admin can edit product data
        if (!in_array($user?->role?->name, ['owner', 'manager', 'admin'], true)) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengedit data produk.');
        }

        // Jika user adalah Admin, alihkan ke alur permohonan persetujuan perubahan harga/data
        if ($user?->role?->name === 'admin') {
            return $this->requestEdit($request, $product);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'reason' => 'nullable|string|max:1000',
        ]);

        // Capture old values before update
        $oldValues = [
            'name' => $product->name,
            'purchase_price' => (float) $product->purchase_price,
            'selling_price' => (float) $product->selling_price,
        ];

        $product->update([
            'name' => $validated['name'],
            'purchase_price' => $validated['purchase_price'],
            'selling_price' => $validated['selling_price'],
        ]);

        // Mark as audit_logged to prevent duplicate in AuditActivity middleware
        $request->attributes->set('audit_logged', true);

        // Log changes to audit_logs
        $newValues = [
            'name' => $product->name,
            'purchase_price' => (float) $product->purchase_price,
            'selling_price' => (float) $product->selling_price,
            'notes' => $validated['reason'] ?? 'Diperbarui langsung oleh Manajemen',
        ];

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'update',
            'table_name' => 'products',
            'record_id' => $product->id,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);

        return redirect()->route('products.index')->with('success', "Data produk \"{$product->name}\" berhasil diperbarui oleh Manajemen.");
    }

    public function requestEdit(Request $request, Product $product): RedirectResponse
    {
        $user = Auth::user();

        if ($product->pendingEdit()->exists()) {
            return redirect()->route('products.index')->with('error', "Produk \"{$product->name}\" sudah memiliki permohonan perubahan harga atau data yang sedang menunggu persetujuan Manager.");
        }

        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'reason' => 'required|string|min:5|max:1000',
        ], [
            'reason.required' => 'Catatan atau alasan perubahan harga wajib diisi agar Manager dapat meninjau.',
            'reason.min' => 'Catatan alasan perubahan harga minimal 5 karakter.',
        ]);

        $changeRequest = ProductChangeRequest::create([
            'product_id' => $product->id,
            'request_type' => 'price_edit',
            'proposed_data' => [
                'name' => $validated['name'],
                'purchase_price' => (float) $validated['purchase_price'],
                'selling_price' => (float) $validated['selling_price'],
                'old_name' => $product->name,
                'old_purchase_price' => (float) $product->purchase_price,
                'old_selling_price' => (float) $product->selling_price,
            ],
            'reason' => $validated['reason'],
            'status' => 'pending',
            'requested_by' => $user->id ?? 1,
            'requested_at' => now(),
        ]);

        AuditLog::create([
            'user_id' => $user->id ?? 1,
            'action' => 'request_price_edit',
            'table_name' => 'product_change_requests',
            'record_id' => $changeRequest->id,
            'old_values' => [
                'name' => $product->name,
                'purchase_price' => (float) $product->purchase_price,
                'selling_price' => (float) $product->selling_price,
            ],
            'new_values' => [
                'proposed_name' => $validated['name'],
                'proposed_purchase_price' => (float) $validated['purchase_price'],
                'proposed_selling_price' => (float) $validated['selling_price'],
                'reason' => $validated['reason'],
            ],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);

        return redirect()->route('products.index')->with('success', "Permohonan perubahan harga dan data produk \"{$product->name}\" berhasil diajukan kepada Manager. Menunggu persetujuan.");
    }

    public function adjustStock(Request $request, Product $product): RedirectResponse
    {
        $user = Auth::user();

        // Jika user adalah Admin, alihkan ke alur permohonan penyesuaian stok
        if ($user?->role?->name === 'admin') {
            return $this->requestAdjustment($request, $product);
        }

        $validated = $request->validate([
            'movement_type' => 'required|in:IN,OUT,ADJUSTMENT',
            'quantity' => 'required|numeric|min:0.01',
            'notes' => 'required|string|min:5|max:1000',
        ], [
            'notes.required' => 'Alasan penyesuaian stok wajib diisi.',
            'notes.min' => 'Alasan penyesuaian stok minimal 5 karakter.',
        ]);

        if ($validated['movement_type'] === 'OUT' && $validated['quantity'] > $product->stock) {
            throw ValidationException::withMessages([
                'quantity' => "Stok {$product->name} tidak mencukupi untuk pengurangan. Tersedia saat ini: {$product->stock}.",
            ]);
        }

        StockMovement::create([
            'product_id' => $product->id,
            'movement_type' => $validated['movement_type'],
            'quantity' => $validated['quantity'],
            'reference_type' => 'Direct Adjustment',
            'reference_id' => $product->id,
            'movement_date' => now(),
            'notes' => $validated['notes'],
            'created_by' => $user->id ?? 1,
        ]);

        return redirect()->route('products.index')->with('success', "Penyesuaian stok produk \"{$product->name}\" berhasil dicatat oleh Manajemen.");
    }

    public function requestAdjustment(Request $request, Product $product): RedirectResponse
    {
        $user = Auth::user();

        if ($product->pendingAdjustment()->exists()) {
            return redirect()->route('products.index')->with('error', "Produk \"{$product->name}\" sudah memiliki permohonan penyesuaian stok yang sedang menunggu persetujuan Manager.");
        }

        $validated = $request->validate([
            'movement_type' => 'required|in:IN,OUT,ADJUSTMENT',
            'quantity' => 'required|numeric|min:0.01',
            'notes' => 'required|string|min:5|max:1000',
        ], [
            'notes.required' => 'Catatan atau alasan penyesuaian stok wajib diisi agar Manager dapat meninjau.',
            'notes.min' => 'Catatan alasan penyesuaian stok minimal 5 karakter.',
        ]);

        if ($validated['movement_type'] === 'OUT' && $validated['quantity'] > $product->stock) {
            throw ValidationException::withMessages([
                'quantity' => "Stok {$product->name} tidak mencukupi untuk pengurangan. Tersedia saat ini: {$product->stock}.",
            ]);
        }

        $changeRequest = ProductChangeRequest::create([
            'product_id' => $product->id,
            'request_type' => 'stock_adjustment',
            'proposed_data' => [
                'movement_type' => $validated['movement_type'],
                'quantity' => (float) $validated['quantity'],
                'current_stock' => (float) $product->stock,
            ],
            'reason' => $validated['notes'],
            'status' => 'pending',
            'requested_by' => $user->id ?? 1,
            'requested_at' => now(),
        ]);

        AuditLog::create([
            'user_id' => $user->id ?? 1,
            'action' => 'request_stock_adjustment',
            'table_name' => 'product_change_requests',
            'record_id' => $changeRequest->id,
            'old_values' => [
                'current_stock' => (float) $product->stock,
            ],
            'new_values' => [
                'movement_type' => $validated['movement_type'],
                'quantity' => (float) $validated['quantity'],
                'notes' => $validated['notes'],
            ],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);

        return redirect()->route('products.index')->with('success', "Permohonan penyesuaian stok produk \"{$product->name}\" berhasil diajukan kepada Manager. Menunggu persetujuan.");
    }

    public function approveChangeRequest(Request $request, ProductChangeRequest $changeRequest): RedirectResponse
    {
        $user = Auth::user();

        if (!$user || !$user->role || !in_array($user->role->name, ['manager', 'owner'], true)) {
            return redirect()->route('products.index')->with('error', 'Hanya Manajemen (Manager atau Owner) yang berhak menyetujui permohonan.');
        }

        if ($changeRequest->status !== 'pending') {
            return redirect()->route('products.index')->with('error', 'Permohonan ini sudah diproses sebelumnya.');
        }

        $product = $changeRequest->product;
        $proposedData = $changeRequest->proposed_data;

        if ($changeRequest->isStockAdjustment()) {
            $movementType = $proposedData['movement_type'];
            $quantity = $proposedData['quantity'];

            if ($movementType === 'OUT' && $quantity > $product->stock) {
                return redirect()->route('products.index')->with('error', "Stok {$product->name} saat ini ({$product->stock}) tidak mencukupi untuk pengurangan {$quantity}.");
            }

            StockMovement::create([
                'product_id' => $product->id,
                'movement_type' => $movementType,
                'quantity' => $quantity,
                'reference_type' => 'Approved Adjustment',
                'reference_id' => $changeRequest->id,
                'movement_date' => now(),
                'notes' => "Disetujui oleh {$user->name}. Alasan Admin: {$changeRequest->reason}",
                'created_by' => $changeRequest->requested_by,
            ]);

            $changeRequest->update([
                'status' => 'approved',
                'actioned_by' => $user->id,
                'actioned_at' => now(),
            ]);

            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'approve_stock_adjustment',
                'table_name' => 'product_change_requests',
                'record_id' => $changeRequest->id,
                'old_values' => ['current_stock' => $proposedData['current_stock'] ?? $product->stock],
                'new_values' => ['status' => 'approved', 'movement_type' => $movementType, 'quantity' => $quantity],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now(),
            ]);

            return redirect()->route('products.index')->with('success', "Permohonan penyesuaian stok produk \"{$product->name}\" berhasil disetujui dan stok telah diperbarui.");
        }

        if ($changeRequest->isPriceEdit()) {
            $oldValues = [
                'name' => $product->name,
                'purchase_price' => (float) $product->purchase_price,
                'selling_price' => (float) $product->selling_price,
            ];

            $product->update([
                'name' => $proposedData['name'],
                'purchase_price' => $proposedData['purchase_price'],
                'selling_price' => $proposedData['selling_price'],
            ]);

            $changeRequest->update([
                'status' => 'approved',
                'actioned_by' => $user->id,
                'actioned_at' => now(),
            ]);

            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'approve_price_edit',
                'table_name' => 'product_change_requests',
                'record_id' => $changeRequest->id,
                'old_values' => $oldValues,
                'new_values' => [
                    'name' => $proposedData['name'],
                    'purchase_price' => $proposedData['purchase_price'],
                    'selling_price' => $proposedData['selling_price'],
                    'status' => 'approved',
                ],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now(),
            ]);

            return redirect()->route('products.index')->with('success', "Permohonan perubahan harga dan data produk \"{$product->name}\" berhasil disetujui dan diperbarui.");
        }

        return redirect()->route('products.index');
    }

    public function rejectChangeRequest(Request $request, ProductChangeRequest $changeRequest): RedirectResponse
    {
        $user = Auth::user();

        if (!$user || !$user->role || !in_array($user->role->name, ['manager', 'owner'], true)) {
            return redirect()->route('products.index')->with('error', 'Hanya Manajemen (Manager atau Owner) yang berhak menolak permohonan.');
        }

        if ($changeRequest->status !== 'pending') {
            return redirect()->route('products.index')->with('error', 'Permohonan ini sudah diproses sebelumnya.');
        }

        $notes = $request->input('notes', "Permohonan ditolak oleh {$user->name}");

        $changeRequest->update([
            'status' => 'rejected',
            'actioned_by' => $user->id,
            'actioned_at' => now(),
            'rejection_notes' => $notes,
        ]);

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'reject_' . $changeRequest->request_type,
            'table_name' => 'product_change_requests',
            'record_id' => $changeRequest->id,
            'old_values' => ['status' => 'pending', 'reason' => $changeRequest->reason],
            'new_values' => ['status' => 'rejected', 'notes' => $notes],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);

        $typeLabel = $changeRequest->isStockAdjustment() ? 'penyesuaian stok' : 'perubahan harga';
        return redirect()->route('products.index')->with('success', "Permohonan {$typeLabel} produk \"{$changeRequest->product->name}\" telah ditolak.");
    }

    public function destroy(Request $request, Product $product): RedirectResponse
    {
        $user = Auth::user();

        // Jika Admin mencoba menghapus langsung, alihkan ke permohonan izin hapus
        if ($user && $user->role && $user->role->name === 'admin') {
            return $this->requestDeletion($request, $product);
        }

        // Hanya Manager atau Owner yang memiliki wewenang menghapus langsung
        if (!$user || !$user->role || !in_array($user->role->name, ['manager', 'owner'], true)) {
            abort(403, 'Hanya Manajemen (Manager atau Owner) yang berhak menghapus data produk.');
        }

        $productName = $product->name;
        $sku = $product->sku;

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'delete',
            'table_name' => 'products',
            'record_id' => $product->id,
            'old_values' => [
                'sku' => $sku,
                'name' => $productName,
                'category' => $product->category,
                'unit' => $product->unit,
                'stock' => $product->stock,
            ],
            'new_values' => ['status' => 'permanently_deleted'],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);

        $product->delete();

        return redirect()->route('products.index')->with('success', "Produk \"{$productName}\" ({$sku}) berhasil dihapus oleh Manajemen.");
    }

    public function requestDeletion(Request $request, Product $product): RedirectResponse
    {
        $user = Auth::user();

        if ($product->deletion_status === 'pending_deletion') {
            return redirect()->route('products.index')->with('error', "Permohonan penghapusan produk \"{$product->name}\" sudah diajukan sebelumnya dan sedang menunggu persetujuan Manager.");
        }

        $validated = $request->validate([
            'deletion_reason' => 'required|string|min:5|max:1000',
        ], [
            'deletion_reason.required' => 'Alasan penghapusan produk wajib diisi agar Manager dapat meninjau permohonan.',
            'deletion_reason.min' => 'Alasan penghapusan minimal 5 karakter.',
        ]);

        $product->update([
            'deletion_status' => 'pending_deletion',
            'deletion_reason' => $validated['deletion_reason'],
            'deletion_requested_by' => $user->id ?? 1,
            'deletion_requested_at' => now(),
        ]);

        AuditLog::create([
            'user_id' => $user->id ?? 1,
            'action' => 'request_deletion',
            'table_name' => 'products.request_deletion',
            'record_id' => $product->id,
            'old_values' => [
                'sku' => $product->sku,
                'name' => $product->name,
            ],
            'new_values' => [
                'deletion_status' => 'pending_deletion',
                'deletion_reason' => $validated['deletion_reason'],
            ],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);

        return redirect()->route('products.index')->with('success', "Permohonan penghapusan produk \"{$product->name}\" berhasil diajukan kepada Manager. Menunggu persetujuan.");
    }

    public function approveDeletion(Request $request, Product $product): RedirectResponse
    {
        $user = Auth::user();

        if (!$user || !$user->role || !in_array($user->role->name, ['manager', 'owner'], true)) {
            return redirect()->route('products.index')->with('error', 'Hanya Manajemen (Manager atau Owner) yang berhak menyetujui penghapusan data produk.');
        }

        if ($product->deletion_status !== 'pending_deletion') {
            return redirect()->route('products.index')->with('error', 'Produk ini tidak dalam status menunggu izin hapus.');
        }

        $productName = $product->name;
        $sku = $product->sku;
        $requesterName = $product->deletionRequester?->name ?? 'Admin';
        $reason = $product->deletion_reason;

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'approve_deletion',
            'table_name' => 'products.approve_deletion',
            'record_id' => $product->id,
            'old_values' => [
                'sku' => $sku,
                'name' => $productName,
                'category' => $product->category,
                'stock' => $product->stock,
                'requested_by' => $requesterName,
                'deletion_reason' => $reason,
            ],
            'new_values' => ['status' => 'deleted_by_manager_approval'],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);

        $product->delete();

        return redirect()->route('products.index')->with('success', "Izin penghapusan produk \"{$productName}\" ({$sku}) disetujui. Produk telah dihapus dari gudang.");
    }

    public function rejectDeletion(Request $request, Product $product): RedirectResponse
    {
        $user = Auth::user();

        if (!$user || !$user->role || !in_array($user->role->name, ['manager', 'owner'], true)) {
            return redirect()->route('products.index')->with('error', 'Hanya Manajemen (Manager atau Owner) yang berhak menolak permohonan hapus produk.');
        }

        if ($product->deletion_status !== 'pending_deletion') {
            return redirect()->route('products.index')->with('error', 'Produk ini tidak dalam status menunggu izin hapus.');
        }

        $productName = $product->name;
        $sku = $product->sku;
        $oldReason = $product->deletion_reason;
        $rejectNotes = $request->input('notes', "Permohonan hapus ditolak oleh {$user->name}");

        $product->update([
            'deletion_status' => 'none',
            'deletion_reason' => null,
            'deletion_requested_by' => null,
            'deletion_requested_at' => null,
        ]);

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'reject_deletion',
            'table_name' => 'products.reject_deletion',
            'record_id' => $product->id,
            'old_values' => [
                'sku' => $sku,
                'name' => $productName,
                'deletion_status' => 'pending_deletion',
                'reason' => $oldReason,
            ],
            'new_values' => [
                'deletion_status' => 'none',
                'notes' => $rejectNotes,
            ],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);

        return redirect()->route('products.index')->with('success', "Permohonan penghapusan produk \"{$productName}\" ({$sku}) telah ditolak. Data produk tetap dipertahankan di gudang.");
    }
}
