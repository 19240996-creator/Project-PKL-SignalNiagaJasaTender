@extends('layouts.app')

@section('title', 'Data Klien')
@section('header-title', 'Manajemen Klien & Mitra')

@section('content')
<div class="space-y-5" x-data="{ createModal: false }">

    <div class="bg-white rounded-lg p-4 border border-slate-200 shadow-xs flex flex-col md:flex-row justify-between items-center gap-3">
        <form method="GET" action="{{ route('clients.index') }}" class="flex items-center gap-2.5 w-full md:w-auto">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode, nama, atau perusahaan..." class="px-3 py-1.5 rounded-md border border-slate-200 text-xs focus:ring-2 focus:ring-blue-500 outline-none w-64">
            <button type="submit" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium text-xs rounded-md transition">Cari</button>
            @if(request('search'))
                <a href="{{ route('clients.index') }}" class="text-xs text-slate-500 hover:text-slate-800">Reset</a>
            @endif
        </form>

        <button @click="createModal = true" class="px-3.5 py-1.5 bg-slate-900 hover:bg-slate-800 text-white font-medium text-xs rounded-md shadow-xs transition flex items-center gap-1.5">
            <i class="fa-solid fa-plus text-[11px]"></i> Tambah Klien Baru
        </button>
    </div>

    <div class="bg-white rounded-lg border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-200 uppercase tracking-wider text-[11px]">
                        <th class="p-3.5">Kode Klien</th>
                        <th class="p-3.5">Nama Kontak</th>
                        <th class="p-3.5">Perusahaan / Instansi</th>
                        <th class="p-3.5">Kontak (Telepon & Email)</th>
                        <th class="p-3.5 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($clients as $c)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="p-3.5 font-mono font-medium text-slate-900">{{ $c->code }}</td>
                            <td class="p-3.5 font-semibold text-slate-800">{{ $c->name }}</td>
                            <td class="p-3.5 text-slate-600">{{ $c->company_name ?? '-' }}</td>
                            <td class="p-3.5 text-slate-600">
                                <div>{{ $c->phone ?? '-' }}</div>
                                <div class="text-[11px] text-slate-400">{{ $c->email ?? '-' }}</div>
                            </td>
                            <td class="p-3.5 text-center">
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-xs font-medium border {{ $c->status === 'active' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-slate-100 text-slate-600 border-slate-200' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $c->status === 'active' ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                    {{ ucfirst($c->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-slate-400 text-xs">Belum ada data klien terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3.5 border-t border-slate-100">
            {{ $clients->links() }}
        </div>
    </div>

    <!-- Modal Create Client -->
    <div x-show="createModal" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4" x-cloak>
        <div class="bg-white rounded-lg border border-slate-200 max-w-lg w-full p-5 shadow-xl space-y-4">
            <div class="flex justify-between items-center border-b border-slate-200 pb-3">
                <h3 class="font-bold text-sm text-slate-900">Tambah Data Klien Baru</h3>
                <button @click="createModal = false" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-sm"></i></button>
            </div>

            <form action="{{ route('clients.store') }}" method="POST" class="space-y-3.5">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Kode Klien</label>
                        <input type="text" name="code" required value="CLI-{{ rand(1000,9999) }}" class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500 font-mono bg-slate-50">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Nama Kontak PIC <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" required placeholder="Bpk. Hendra..." class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Nama Perusahaan / Instansi</label>
                    <input type="text" name="company_name" placeholder="PT Solusi Nusantara..." class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Nomor Telepon</label>
                        <input type="text" name="phone" placeholder="081234567890" class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Email</label>
                        <input type="email" name="email" placeholder="klien@company.com" class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Alamat Lengkap</label>
                    <textarea name="address" rows="2" class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500" placeholder="Jl. Sudirman No. 12..."></textarea>
                </div>

                <input type="hidden" name="status" value="active">

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-200">
                    <button type="button" @click="createModal = false" class="px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium text-xs rounded-md transition">Batal</button>
                    <button type="submit" class="px-4 py-1.5 bg-slate-900 hover:bg-slate-800 text-white font-medium text-xs rounded-md transition">Simpan Data Klien</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
