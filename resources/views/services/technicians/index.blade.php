@extends('layouts.app')

@section('title', 'Teknisi')
@section('header-title', 'Teknisi & Ketersediaan SDM')

@section('content')
<div class="space-y-5">
    @if(session('success'))<div class="px-4 py-3 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="px-4 py-3 rounded-lg bg-rose-50 border border-rose-200 text-rose-800 text-xs">{{ $errors->first() }}</div>@endif

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-5">
        @if(!auth()->user()->isTechnician())
        <section class="xl:col-span-1 bg-white rounded-lg border border-slate-200 p-4 shadow-xs">
            <h2 class="text-sm font-bold text-slate-900 mb-3">Tambah Teknisi</h2>
            <form method="POST" action="{{ route('jasa.teknisi.store') }}" class="space-y-2.5">
                @csrf
                <input name="name" required placeholder="Nama teknisi" class="field">
                <input name="phone" placeholder="Kontak telepon" class="field">
                <input name="email" type="email" placeholder="Email" class="field">
                <input name="user_id" type="number" placeholder="ID akun pengguna (opsional)" class="field">
                <textarea name="competencies" rows="2" placeholder="Kompetensi, pisahkan dengan koma" class="field"></textarea>
                <select name="status" class="field"><option value="active">Aktif</option><option value="inactive">Tidak aktif</option></select>
                <button class="w-full px-3 py-2 rounded-md bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold">Simpan Teknisi</button>
            </form>
        </section>
        @endif

        <section class="xl:col-span-2 bg-white rounded-lg border border-slate-200 overflow-hidden shadow-xs">
            <div class="p-4 border-b border-slate-200 flex items-center justify-between"><div><h2 class="text-sm font-bold text-slate-900">Daftar SDM Teknis</h2><p class="text-xs text-slate-500 mt-1">Beban aktif dan riwayat penugasan per teknisi.</p></div><span class="text-xs text-slate-500">{{ $technicians->count() }} teknisi</span></div>
            <div class="divide-y divide-slate-100">
                @forelse($technicians as $technician)
                    <div class="p-4">
                        <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-3">
                            <div><div class="font-semibold text-sm text-slate-900">{{ $technician->name }} <span class="ml-1 px-1.5 py-0.5 rounded text-[10px] {{ $technician->status === 'active' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">{{ $technician->status }}</span></div><div class="text-xs text-slate-500 mt-1">{{ $technician->phone ?: 'Kontak belum diisi' }} · {{ $technician->email ?: 'Email belum diisi' }}</div><div class="text-xs text-slate-600 mt-2"><strong>Kompetensi:</strong> {{ $technician->competencies ?: 'Belum dicatat' }}</div></div>
                            <div class="text-left md:text-right"><div class="text-xl font-bold text-blue-700">{{ $technician->active_assignments }}</div><div class="text-[10px] uppercase tracking-wide text-slate-500">Tugas aktif</div></div>
                        </div>
                        <div class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div class="rounded-md bg-slate-50 p-3"><div class="text-[10px] font-bold uppercase text-slate-500 mb-2">Ketersediaan</div>@forelse($technician->availabilities->sortByDesc('available_from')->take(2) as $availability)<div class="text-xs text-slate-700">{{ $availability->available_from->format('d M Y') }} - {{ $availability->available_until->format('d M Y') }} <span class="text-slate-400">({{ $availability->status }})</span></div>@empty<div class="text-xs text-slate-400">Belum ada jadwal.</div>@endforelse</div>
                            <div class="rounded-md bg-slate-50 p-3"><div class="text-[10px] font-bold uppercase text-slate-500 mb-2">Riwayat pekerjaan</div>@forelse($technician->assignments->sortByDesc('start_date')->take(2) as $assignment)<div class="text-xs text-slate-700 truncate">{{ $assignment->job?->job_number }} · {{ $assignment->job?->name }}</div>@empty<div class="text-xs text-slate-400">Belum ada assignment.</div>@endforelse</div>
                        </div>
                        <form method="POST" action="{{ route('jasa.teknisi.availability', $technician) }}" class="mt-3 flex flex-wrap gap-2 items-end">@csrf<input type="date" name="available_from" required class="field"><input type="date" name="available_until" required class="field"><select name="status" class="field"><option value="available">Tersedia</option><option value="leave">Cuti</option><option value="unavailable">Tidak tersedia</option></select><button class="px-3 py-2 rounded-md border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-slate-50">Catat Jadwal</button></form>
                    </div>
                @empty<div class="p-8 text-center text-xs text-slate-500">Belum ada data teknisi.</div>@endforelse
            </div>
        </section>
    </div>
</div>
<style>.field{width:100%;padding:.5rem .65rem;border:1px solid #cbd5e1;border-radius:.375rem;font-size:.75rem;background:#fff;outline:none}.field:focus{border-color:#2563eb;box-shadow:0 0 0 2px #dbeafe}</style>
@endsection
