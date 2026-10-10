<?php

namespace App\Http\Controllers;

use App\Models\Technician;
use App\Models\TechnicianAvailability;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TechnicianController extends Controller
{
    public function index(): View
    {
        $technicians = Technician::with(['user', 'availabilities', 'assignments.job'])
            ->withCount(['assignments as active_assignments' => fn ($query) => $query->whereIn('status', ['assigned', 'in_progress'])])
            ->orderBy('name')->get();

        return view('services.technicians.index', compact('technicians'));
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless(!Auth::user()?->isTechnician(), 403);
        $validated = $request->validate([
            'user_id' => 'nullable|exists:users,id|unique:technicians,user_id',
            'name' => 'required|string|max:150',
            'phone' => 'nullable|string|max:40',
            'email' => 'nullable|email|max:150',
            'competencies' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'notes' => 'nullable|string',
        ]);

        Technician::create($validated);
        return back()->with('success', 'Data teknisi berhasil ditambahkan.');
    }

    public function update(Request $request, Technician $technician): RedirectResponse
    {
        abort_unless(!Auth::user()?->isTechnician(), 403);
        $validated = $request->validate([
            'user_id' => 'nullable|exists:users,id|unique:technicians,user_id,' . $technician->id,
            'name' => 'required|string|max:150',
            'phone' => 'nullable|string|max:40',
            'email' => 'nullable|email|max:150',
            'competencies' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'notes' => 'nullable|string',
        ]);

        $technician->update($validated);
        return back()->with('success', 'Data teknisi berhasil diperbarui.');
    }

    public function storeAvailability(Request $request, Technician $technician): RedirectResponse
    {
        abort_unless(!Auth::user()?->isTechnician() || $technician->user_id === Auth::id(), 403);
        $validated = $request->validate([
            'available_from' => 'required|date',
            'available_until' => 'required|date|after_or_equal:available_from',
            'status' => 'required|in:available,leave,unavailable',
            'notes' => 'nullable|string',
        ]);

        $technician->availabilities()->create($validated);
        return back()->with('success', 'Ketersediaan teknisi berhasil dicatat.');
    }
}
