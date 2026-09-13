<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClientController extends Controller
{
    public function index(Request $request): View
    {
        $query = Client::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%");
            });
        }

        $clients = $query->orderBy('created_at', 'desc')->paginate(10);
        return view('clients.index', compact('clients'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => 'required|unique:clients,code',
            'name' => 'required|string|max:150',
            'company_name' => 'nullable|string|max:200',
            'phone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:150',
            'address' => 'nullable|string',
            'status' => 'required|string',
        ]);

        Client::create($validated);
        return redirect()->route('clients.index')->with('success', 'Data klien berhasil ditambahkan.');
    }

    public function update(Request $request, Client $client): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'company_name' => 'nullable|string|max:200',
            'phone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:150',
            'address' => 'nullable|string',
            'status' => 'required|string',
        ]);

        $client->update($validated);
        return redirect()->route('clients.index')->with('success', 'Data klien berhasil diperbarui.');
    }

    public function destroy(Client $client): RedirectResponse
    {
        $client->delete();
        return redirect()->route('clients.index')->with('success', 'Klien berhasil dihapus.');
    }
}
