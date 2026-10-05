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
        $user = Auth::user();
        $query = ServiceJob::with(['contract.client', 'client', 'creator', 'approver', 'invoices']);

        // Admin hanya melihat data yang ia input sendiri sesuai PRD
        if ($user && $user->role && $user->role->name === 'admin') {
            $query->where('created_by', $user->id);
        }

        if ($request->filled('approval_status')) {
            $query->where('approval_status', $request->approval_status);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('job_number', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('klien', 'like', "%{$search}%");
            });
        }

        $pendingCount = ServiceJob::where('approval_status', 'pending')->count();
        $serviceJobs = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        // Pastikan setiap record pekerjaan jasa sinkron otomatis persentase progress-nya
        foreach ($serviceJobs as $job) {
            $expectedProgress = $job->calculateProgress();
            if ($job->progress !== $expectedProgress) {
                $job->syncProgress();
            }
        }

        $contracts = Contract::with('client')->where('status', 'Aktif')->get();
        $clients = \App\Models\Client::where('status', 'active')->orderBy('name')->get();

        return view('services.index', compact('serviceJobs', 'contracts', 'clients', 'pendingCount'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $validated = $request->validate([
            'contract_id' => 'nullable|exists:contracts,id',
            'client_id' => 'nullable|exists:clients,id',
            'klien' => 'nullable|string|max:200',
            'job_number' => 'nullable|string|max:50|unique:service_jobs,job_number',
            'name' => 'required|string|max:200',
            'biaya' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'status' => 'required|string',
            'notes' => 'nullable|string',
            'deskripsi_pekerjaan' => 'nullable|string',
        ]);

        if (empty($validated['job_number'])) {
            $validated['job_number'] = 'JOB-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));
        }

        // Ambil nama klien dari master klien jika client_id diisi
        if (!empty($validated['client_id']) && empty($validated['klien'])) {
            $cl = \App\Models\Client::find($validated['client_id']);
            $validated['klien'] = $cl ? $cl->name : null;
        }

        $validated['created_by'] = $user->id ?? 1;

        // Sesuai PRD: Data yang diinput Admin berstatus Pending secara default
        if ($user && $user->role && $user->role->name === 'admin') {
            $validated['approval_status'] = 'pending';
        } else {
            $validated['approval_status'] = 'approved';
            $validated['approved_by'] = $user->id ?? null;
            $validated['approved_at'] = now();
        }

        // Progress awal otomatis 100% jika langsung berstatus Selesai, atau 0% untuk pekerjaan baru
        $validated['progress'] = ($validated['status'] === 'Selesai') ? 100 : 0;

        $job = ServiceJob::create($validated);
        $job->syncProgress();

        $msg = ($validated['approval_status'] === 'pending')
            ? 'Pekerjaan Jasa berhasil dicatat dan berstatus PENDING menunggu persetujuan Manager.'
            : 'Pekerjaan Jasa berhasil ditambahkan dengan progress otomatis.';

        return redirect()->route('jasa.index')->with('success', $msg);
    }

    public function update(Request $request, ServiceJob $serviceJob): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'client_id' => 'nullable|exists:clients,id',
            'klien' => 'nullable|string|max:200',
            'biaya' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'status' => 'required|string',
            'notes' => 'nullable|string',
            'deskripsi_pekerjaan' => 'nullable|string',
        ]);

        if ($validated['status'] === 'Selesai') {
            $validated['progress'] = 100;
        }

        $serviceJob->update($validated);
        $serviceJob->syncProgress();

        return redirect()->route('jasa.index')->with('success', 'Pekerjaan Jasa berhasil diperbarui.');
    }

    public function approve(Request $request, ServiceJob $serviceJob): RedirectResponse
    {
        $user = Auth::user();
        if (!$user || !$user->role || !in_array($user->role->name, ['manager', 'owner'], true)) {
            return redirect()->route('jasa.index')->with('error', 'Hanya Manager atau Owner yang berhak menyetujui (Approve) pekerjaan jasa.');
        }

        $serviceJob->update([
            'approval_status' => 'approved',
            'approved_by' => $user->id,
            'approved_at' => now(),
            'approval_notes' => $request->input('notes'),
        ]);

        return redirect()->route('jasa.index')->with('success', "Pekerjaan Jasa {$serviceJob->job_number} berhasil disetujui (Approved).");
    }

    public function reject(Request $request, ServiceJob $serviceJob): RedirectResponse
    {
        $user = Auth::user();
        if (!$user || !$user->role || !in_array($user->role->name, ['manager', 'owner'], true)) {
            return redirect()->route('jasa.index')->with('error', 'Hanya Manager atau Owner yang berhak menolak (Reject) pekerjaan jasa.');
        }

        $serviceJob->update([
            'approval_status' => 'rejected',
            'approved_by' => $user->id,
            'approved_at' => now(),
            'approval_notes' => $request->input('notes', 'Ditolak oleh ' . $user->name),
        ]);

        return redirect()->route('jasa.index')->with('success', "Pekerjaan Jasa {$serviceJob->job_number} telah ditolak (Rejected).");
    }

    public function destroy(ServiceJob $serviceJob): RedirectResponse
    {
        $serviceJob->delete();
        return redirect()->route('jasa.index')->with('success', 'Pekerjaan Jasa berhasil dihapus.');
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
