@extends('layouts.app')

@section('title', 'Data Klien')
@section('header-title', 'Manajemen Klien & Perusahaan Partner')

@section('content')
<div class="space-y-6" x-data="{ createModal: false }">

    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex flex-col md:flex-row justify-between items-center gap-4">
        <form method="GET" action="{{ route('clients.index') }}" class="flex items-center gap-3 w-full md:w-auto">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode, nama, atau perusahaan..." class="px-4 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500 outline-none w-64">
            <button type="submit" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm rounded-xl">Cari</button>
        </form>

        <button @click="createModal = true" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-xl shadow transition flex items-center gap-2">
            <i class="fa-solid fa-plus"></i> Tambah Klien Baru
        </button>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-200">
                        <th class="p-4">Kode Klien</th>
                        <th class="p-4">Nama Kontak</th>
                        <th class="p-4">Perusahaan / Instansi</th>
                        <th class="p-4">Telepon & Email</th>
                        <th class="p-4">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($clients as $c)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="p-4 font-mono font-bold text-blue-600">{{ $c->code }}</td>
                            <td class="p-4 font-bold text-slate-800">{{ $c->name }}</td>
                            <td class="p-4 font-medium text-slate-700">{{ $c->company_name ?? '-' }}</td>
                            <td class="p-4 text-xs text-slate-600">
                                <div><i class="fa-solid fa-phone text-slate-400"></i> {{ $c->phone ?? '-' }}</div>
                                <div><i class="fa-solid fa-envelope text-slate-400"></i> {{ $c->email ?? '-' }}</div>
                            </td>
                            <td class="p-4">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $c->status === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                                    {{ ucfirst($c->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-slate-400">Belum ada data klien.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100">
            {{ $clients->links() }}
        </div>
    </div>

    <!-- Modal Create Client -->
    <div x-show="createModal" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4" x-cloak>
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4">
            <div class="flex justify-between items-center border-b pb-3">
                <h3 class="font-bold text-lg text-slate-800">Tambah Data Klien Baru</h3>
                <button @click="createModal = false" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>

            <form action="{{ route('clients.store') }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Kode Klien</label>
                        <input type="text" name="code" required value="CLI-{{ rand(1000,9999) }}" class="w-full px-3 py-2 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Nama Kontak PIC</label>
                        <input type="text" name="name" required placeholder="Bpk. Hendra..." class="w-full px-3 py-2 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Nama Perusahaan / Instansi</label>
                    <input type="text" name="company_name" placeholder="PT Digital Solusindo..." class="w-full px-3 py-2 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Nomor Telepon</label>
                        <input type="text" name="phone" placeholder="081234567890" class="w-full px-3 py-2 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Email</label>
                        <input type="email" name="email" placeholder="klien@company.com" class="w-full px-3 py-2 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Alamat Lengkap</label>
                    <textarea name="address" rows="2" class="w-full px-3 py-2 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500" placeholder="Jl. Sudirman No. 12..."></textarea>
                </div>

                <input type="hidden" name="status" value="active">

                <div class="flex justify-end gap-2 pt-3 border-t">
                    <button type="button" @click="createModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm rounded-xl">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-xl shadow">Simpan Data Klien</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
