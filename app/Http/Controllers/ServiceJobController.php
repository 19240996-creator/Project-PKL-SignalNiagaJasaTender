<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\ServiceBill;
use App\Models\ServiceJob;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ServiceJobController extends Controller
{
    public function index(Request $request): View
    {
        $query = ServiceJob::with(['contract.client', 'invoices']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('job_number', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
        }

        $serviceJobs = $query->orderBy('created_at', 'desc')->paginate(10);

        // Pastikan setiap record pekerjaan jasa sinkron otomatis persentase progress-nya
        foreach ($serviceJobs as $job) {
            $expectedProgress = $job->calculateProgress();
            if ($job->progress !== $expectedProgress) {
                $job->syncProgress();
            }
        }

        $contracts = Contract::with('client')->where('status', 'Aktif')->get();

        return view('services.index', compact('serviceJobs', 'contracts'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'contract_id' => 'required|exists:contracts,id',
            'job_number' => 'required|unique:service_jobs,job_number',
            'name' => 'required|string|max:200',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'status' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        // Progress awal otomatis 100% jika langsung berstatus Selesai, atau 0% untuk pekerjaan baru
        $validated['progress'] = ($validated['status'] === 'Selesai') ? 100 : 0;

        $job = ServiceJob::create($validated);
        $job->syncProgress();

        return redirect()->route('jasa.index')->with('success', 'Pekerjaan Jasa berhasil ditambahkan dengan progress otomatis.');
    }

    public function update(Request $request, ServiceJob $serviceJob): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'status' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        if ($validated['status'] === 'Selesai') {
            $validated['progress'] = 100;
        }

        $serviceJob->update($validated);
        $serviceJob->syncProgress();

        return redirect()->route('jasa.index')->with('success', 'Pekerjaan Jasa berhasil diperbarui.');
    }

    public function generateBill(Request $request, ServiceJob $serviceJob): RedirectResponse
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'due_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $subtotal = (float) $validated['amount'];
        $taxAmount = $subtotal * 0.11;
        $totalAmount = $subtotal + $taxAmount;

        DB::transaction(function () use ($serviceJob, $validated, $subtotal, $taxAmount, $totalAmount) {
            $bill = ServiceBill::create([
                'service_job_id' => $serviceJob->id,
                'bill_number' => 'BILL-SVC-' . date('Ymd') . '-' . rand(100, 999),
                'bill_date' => now()->toDateString(),
                'due_date' => $validated['due_date'],
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
                'status' => 'Issued',
                'notes' => $validated['notes'] ?? 'Tagihan untuk Pekerjaan Jasa ' . $serviceJob->job_number,
                'created_by' => Auth::id() ?? 1,
            ]);

            $invoice = Invoice::create([
                'invoice_number' => 'INV-SVC-' . date('Ymd') . '-' . rand(100, 999),
                'service_job_id' => $serviceJob->id,
                'service_bill_id' => $bill->id,
                'invoice_date' => now()->toDateString(),
                'due_date' => $validated['due_date'],
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
                'paid_amount' => 0,
                'status' => 'Issued',
                'notes' => $bill->notes,
                'created_by' => Auth::id() ?? 1,
            ]);

            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'description' => $serviceJob->name,
                'quantity' => 1,
                'price' => $subtotal,
                'subtotal' => $subtotal,
            ]);

            $serviceJob->refresh();
            $serviceJob->syncProgress();
        });

        $currentProgress = $serviceJob->fresh()->progress;

        return redirect()->route('jasa.index')->with('success', "Invoice/Tagihan Jasa berhasil diterbitkan. Progress pekerjaan otomatis diperbarui menjadi {$currentProgress}%.");
    }
}
