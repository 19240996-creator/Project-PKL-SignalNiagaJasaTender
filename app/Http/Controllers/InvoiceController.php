<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Invoice;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function index(Request $request): View
    {
        $query = Invoice::with(['serviceJob.contract.client', 'sale', 'payments']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('invoice_number', 'like', "%{$search}%");
        }

        $invoices = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('invoices.index', compact('invoices'));
    }

    public function show(Invoice $invoice): View
    {
        $invoice->load(['serviceJob.contract.client', 'sale.items.product', 'payments.creator']);
        return view('invoices.show', compact('invoice'));
    }

    public function destroy(Request $request, Invoice $invoice): RedirectResponse
    {
        $user = Auth::user();

        if (!$user || (!$user->isManager() && !$user->isOwner())) {
            return redirect()->route('invoices.index')->with('error', 'Hanya Manajemen (Manager atau Owner) yang berhak menghapus data invoice.');
        }

        $validated = $request->validate([
            'deletion_reason' => 'required|string|min:5|max:1000',
        ], [
            'deletion_reason.required' => 'Catatan alasan penghapusan invoice wajib diisi.',
            'deletion_reason.min' => 'Catatan alasan penghapusan minimal 5 karakter.',
            'deletion_reason.max' => 'Catatan alasan penghapusan maksimal 1000 karakter.',
        ]);

        $invoiceNumber = $invoice->invoice_number;
        $sourceDesc = $invoice->sale 
            ? ('Penjualan ' . $invoice->sale->sale_number) 
            : ($invoice->serviceJob ? ('Jasa ' . $invoice->serviceJob->job_number) : 'Tagihan');

        $request->attributes->set('audit_logged', true);

        DB::transaction(function () use ($invoice, $user, $validated, $sourceDesc, $request) {
            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'delete',
                'table_name' => 'invoices',
                'record_id' => $invoice->id,
                'old_values' => [
                    'name' => 'Invoice ' . $invoice->invoice_number,
                    'invoice_number' => $invoice->invoice_number,
                    'sumber' => $sourceDesc,
                    'total_amount' => (float) $invoice->total_amount,
                    'paid_amount' => (float) $invoice->paid_amount,
                    'status' => $invoice->status,
                    'alasan_penghapusan' => $validated['deletion_reason'],
                    'reason' => $validated['deletion_reason'],
                    'dihapus_oleh' => $user->name . ' (' . ucfirst($user->role?->name ?? 'manager') . ')',
                ],
                'new_values' => [
                    'status' => 'permanently_deleted',
                ],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now(),
            ]);

            $invoice->payments()->delete();
            $invoice->items()->delete();
            $invoice->delete();
        });

        return redirect()->route('invoices.index')->with('success', "Invoice \"{$invoiceNumber}\" berhasil dihapus dan tercatat pada log aktivitas Owner.");
    }
}
