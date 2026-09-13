<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Invoice;
use App\Models\ServiceJob;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            'progress' => 'required|integer|min:0|max:100',
            'notes' => 'nullable|string',
        ]);

        ServiceJob::create($validated);

        return redirect()->route('jasa.index')->with('success', 'Pekerjaan Jasa berhasil ditambahkan.');
    }

    public function update(Request $request, ServiceJob $serviceJob): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'status' => 'required|string',
            'progress' => 'required|integer|min:0|max:100',
            'notes' => 'nullable|string',
        ]);

        $serviceJob->update($validated);

        return redirect()->route('jasa.index')->with('success', 'Pekerjaan Jasa berhasil diperbarui.');
    }

    public function generateBill(Request $request, ServiceJob $serviceJob): RedirectResponse
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'due_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $subtotal = $validated['amount'];
        $taxAmount = $subtotal * 0.11;
        $totalAmount = $subtotal + $taxAmount;

        Invoice::create([
            'invoice_number' => 'INV-SVC-' . date('Ymd') . '-' . rand(100, 999),
            'service_job_id' => $serviceJob->id,
            'invoice_date' => now()->toDateString(),
            'due_date' => $validated['due_date'],
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount,
            'paid_amount' => 0,
            'status' => 'Issued',
            'notes' => $validated['notes'] ?? 'Tagihan untuk Pekerjaan Jasa ' . $serviceJob->job_number,
            'created_by' => Auth::id() ?? 1,
        ]);

        return redirect()->route('invoices.index')->with('success', 'Invoice/Tagihan Jasa berhasil diterbitkan.');
    }
}
