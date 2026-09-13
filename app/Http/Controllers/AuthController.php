<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLoginForm(): View
    {
        $accounts = [
            ['role' => 'super_admin', 'label' => 'Super Admin', 'email' => 'superadmin@signalpanca.co.id', 'password' => 'password', 'desc' => 'Akses penuh ke seluruh sistem', 'badge' => 'bg-purple-100 text-purple-800'],
            ['role' => 'management', 'label' => 'Management', 'email' => 'management@signalpanca.co.id', 'password' => 'password', 'desc' => 'Dashboard KPI, monitoring & laporan', 'badge' => 'bg-blue-100 text-blue-800'],
            ['role' => 'tender_officer', 'label' => 'Tender Officer', 'email' => 'tender_officer@signalpanca.co.id', 'password' => 'password', 'desc' => 'Kelola pipeline & dokumen tender', 'badge' => 'bg-amber-100 text-amber-800'],
            ['role' => 'service_officer', 'label' => 'Service Officer', 'email' => 'service_officer@signalpanca.co.id', 'password' => 'password', 'desc' => 'Kelola klien, kontrak & pekerjaan jasa', 'badge' => 'bg-emerald-100 text-emerald-800'],
            ['role' => 'purchasing', 'label' => 'Purchasing', 'email' => 'purchasing@signalpanca.co.id', 'password' => 'password', 'desc' => 'Kelola supplier & pengadaan barang', 'badge' => 'bg-indigo-100 text-indigo-800'],
            ['role' => 'warehouse', 'label' => 'Warehouse', 'email' => 'warehouse@signalpanca.co.id', 'password' => 'password', 'desc' => 'Kelola persediaan & pergerakan stok', 'badge' => 'bg-teal-100 text-teal-800'],
            ['role' => 'sales', 'label' => 'Sales', 'email' => 'sales@signalpanca.co.id', 'password' => 'password', 'desc' => 'Kelola transaksi penjualan barang', 'badge' => 'bg-rose-100 text-rose-800'],
            ['role' => 'finance', 'label' => 'Finance', 'email' => 'finance@signalpanca.co.id', 'password' => 'password', 'desc' => 'Kelola invoice, tagihan & pembayaran', 'badge' => 'bg-cyan-100 text-cyan-800'],
        ];

        return view('auth.login', compact('accounts'));
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !$user->is_active) {
            return back()->withErrors(['email' => 'Akun tidak ditemukan atau sedang tidak aktif.'])->withInput();
        }

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended('/dashboard')->with('success', 'Selamat datang kembali, ' . $user->name . ' (' . ucwords(str_replace('_', ' ', $user->role->name ?? 'User')) . ')');
        }

        return back()->withErrors(['email' => 'Email atau password salah.'])->withInput();
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar dari sistem.');
    }
}
