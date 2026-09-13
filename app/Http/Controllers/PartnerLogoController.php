<?php

namespace App\Http\Controllers;

use App\Models\PartnerLogo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PartnerLogoController extends Controller
{
    public function index(): View
    {
        $partnerLogos = PartnerLogo::orderBy('sort_order', 'asc')->orderBy('created_at', 'desc')->paginate(15);
        return view('partner_logos.index', compact('partnerLogos'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'category' => 'nullable|string|max:100',
            'badge_text' => 'nullable|string|max:50',
            'badge_color' => 'nullable|string|max:50',
            'logo' => 'nullable|image|max:4096', // Max 4MB image upload
            'sort_order' => 'nullable|integer',
        ]);

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('partner_logos', 'public');
            $validated['logo_path'] = $path;
        }

        $validated['is_active'] = true;
        $validated['badge_color'] = $validated['badge_color'] ?? 'text-blue-600';
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        PartnerLogo::create($validated);

        return redirect()->route('partner-logos.index')->with('success', 'Logo Klien baru berhasil ditambahkan.');
    }

    public function update(Request $request, PartnerLogo $partnerLogo): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'category' => 'nullable|string|max:100',
            'badge_text' => 'nullable|string|max:50',
            'badge_color' => 'nullable|string|max:50',
            'logo' => 'nullable|image|max:4096',
            'is_active' => 'required|boolean',
            'sort_order' => 'nullable|integer',
        ]);

        if ($request->hasFile('logo')) {
            if ($partnerLogo->logo_path) {
                Storage::disk('public')->delete($partnerLogo->logo_path);
            }
            $path = $request->file('logo')->store('partner_logos', 'public');
            $validated['logo_path'] = $path;
        }

        $partnerLogo->update($validated);

        return redirect()->route('partner-logos.index')->with('success', 'Data logo klien berhasil diperbarui.');
    }

    public function destroy(PartnerLogo $partnerLogo): RedirectResponse
    {
        if ($partnerLogo->logo_path) {
            Storage::disk('public')->delete($partnerLogo->logo_path);
        }
        $partnerLogo->delete();

        return redirect()->route('partner-logos.index')->with('success', 'Logo klien berhasil dihapus.');
    }
}
