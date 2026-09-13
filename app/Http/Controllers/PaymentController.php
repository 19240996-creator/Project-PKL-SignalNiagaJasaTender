<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Exception;

class PaymentController extends Controller
{
    public function index(Request $request): View
    {
        $payments = Payment::with(['invoice', 'creator'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $unpaidInvoices = Invoice::whereIn('status', ['Issued', 'Partial', 'Overdue'])->get();

        return view('payments.index', compact('payments', 'unpaidInvoices'));
    }

    public function store(Request $request, PaymentService $service): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'invoice_id' => 'required|exists:invoices,id',
                'payment_date' => 'required|date',
                'amount' => 'required|numeric|min:0.01',
                'payment_method' => 'required|string',
                'reference_number' => 'nullable|string|max:100',
                'notes' => 'nullable|string',
            ]);

            $payment = $service->recordPayment($validated, Auth::id() ?? 1);

            return redirect()->back()->with('success', "Pembayaran Rp " . number_format($payment->amount, 0, ',', '.') . " berhasil dicatat untuk Invoice {$payment->invoice->invoice_number}.");
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }
}
