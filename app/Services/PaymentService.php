<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Exception;

class PaymentService
{
    public function recordPayment(array $data, int $userId): Payment
    {
        return DB::transaction(function () use ($data, $userId) {
            $invoice = Invoice::findOrFail($data['invoice_id']);
            $amount = (float) $data['amount'];

            if ($amount <= 0) {
                throw new Exception('Jumlah pembayaran harus lebih besar dari 0.');
            }

            $remaining = $invoice->total_amount - $invoice->paid_amount;
            if ($amount > $remaining) {
                throw new Exception("Jumlah pembayaran (Rp " . number_format($amount, 0, ',', '.') . ") melebihi sisa tagihan (Rp " . number_format($remaining, 0, ',', '.') . ").");
            }

            $payment = Payment::create([
                'invoice_id' => $invoice->id,
                'payment_date' => $data['payment_date'] ?? now()->toDateString(),
                'amount' => $amount,
                'payment_method' => $data['payment_method'] ?? 'Transfer Bank',
                'reference_number' => $data['reference_number'] ?? null,
                'notes' => $data['notes'] ?? null,
                'created_by' => $userId,
            ]);

            $newPaidAmount = $invoice->paid_amount + $amount;
            $status = $newPaidAmount >= $invoice->total_amount ? 'Paid' : 'Partial';

            $invoice->update([
                'paid_amount' => $newPaidAmount,
                'status' => $status,
            ]);

            return $payment;
        });
    }
}
