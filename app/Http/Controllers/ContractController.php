<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Contract;
use App\Models\Tender;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ContractController extends Controller
{
    public function index(Request $request): View
    {
        $query = Contract::with(['client', 'tender', 'serviceJobs']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('contract_number', 'like', "%{$search}%")
                  ->orWhereHas('client', function ($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $contracts = $query->orderBy('created_at', 'desc')->paginate(10);
        $clients = Client::where('status', 'active')->get();
        $tenders = Tender::where('status', 'Menang')->orWhere('result', 'Menang')->get();

        return view('contracts.index', compact('contracts', 'clients', 'tenders'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'contract_number' => 'required|unique:contracts,contract_number',
            'client_id' => 'required|exists:clients,id',
            'tender_id' => 'nullable|exists:tenders,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'contract_value' => 'required|numeric|min:0',
            'fee_percentage' => 'required|numeric|min:0|max:100',
            'status' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $validated['fee_amount'] = ($validated['contract_value'] * $validated['fee_percentage']) / 100;
        $validated['created_by'] = Auth::id() ?? 1;

        Contract::create($validated);

        return redirect()->route('contracts.index')->with('success', 'Kontrak Jasa berhasil dibuat.');
    }

    public function update(Request $request, Contract $contract): RedirectResponse
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'contract_value' => 'required|numeric|min:0',
            'fee_percentage' => 'required|numeric|min:0|max:100',
            'status' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $validated['fee_amount'] = ($validated['contract_value'] * $validated['fee_percentage']) / 100;

        $contract->update($validated);

        return redirect()->route('contracts.index')->with('success', 'Kontrak Jasa berhasil diperbarui.');
    }

    public function destroy(Contract $contract): RedirectResponse
    {
        $contract->delete();
        return redirect()->route('contracts.index')->with('success', 'Kontrak Jasa berhasil dihapus.');
    }
}
