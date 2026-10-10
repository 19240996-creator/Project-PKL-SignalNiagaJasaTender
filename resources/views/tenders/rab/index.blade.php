@extends('layouts.app')

@section('title', 'Estimasi / RAB Tender - PT Signal Panca Utama')
@section('header-title', 'Estimasi & Rencana Anggaran Biaya (RAB)')

@section('content')
<div class="space-y-6">

    <!-- Overview Banner -->
    <div class="border border-slate-200 bg-white rounded-lg p-4 flex flex-col md:flex-row items-start md:items-center justify-between gap-4 shadow-xs">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                <i class="fa-solid fa-calculator text-base"></i>
            </div>
            <div>
                <h2 class="text-sm font-bold text-slate-900 leading-tight">Perhitungan RAB & Analisis Kelayakan Proyek</h2>
                <p class="text-xs text-slate-500 mt-0.5">Sistem secara otomatis mengintegrasikan stok barang dari Modul Dagang dan kapasitas SDM dari Modul Jasa untuk menghitung total HPP modal, margin keuntungan, dan rekomendasi pelaksanaan.</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('tender.index') }}" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-md transition flex items-center gap-1.5">
                <i class="fa-solid fa-folder-tree text-[11px]"></i> Administrasi Tender
            </a>
            <a href="{{ route('tender.proyek.index') }}" class="px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 font-semibold text-xs rounded-md transition flex items-center gap-1.5">
                <i class="fa-solid fa-helmet-safety text-[11px]"></i> Lapangan / Proyek
            </a>
        </div>
    </div>

    <!-- Status Cards KPI RAB -->
    <div class="grid grid-cols-2 md:grid-cols-6 gap-3">
        <a href="{{ route('tender.rab.index') }}" class="bg-white p-3.5 rounded-lg border border-slate-200 shadow-xs hover:border-blue-400 transition {{ !request('rab_status') ? 'ring-2 ring-blue-500/20' : '' }}">
            <div class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Total RAB</div>
            <div class="text-xl font-bold text-slate-900 mt-1 font-numeric">{{ $rabCounts['total'] ?? 0 }}</div>
            <div class="text-[10px] text-slate-400 mt-0.5">Semua proyek tender</div>
        </a>
        <a href="{{ route('tender.rab.index', ['rab_status' => 'draft']) }}" class="bg-white p-3.5 rounded-lg border border-slate-200 shadow-xs hover:border-slate-400 transition {{ request('rab_status') === 'draft' ? 'ring-2 ring-slate-400' : '' }}">
            <div class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Draft</div>
            <div class="text-xl font-bold text-slate-700 mt-1 font-numeric">{{ $rabCounts['draft'] ?? 0 }}</div>
            <div class="text-[10px] text-slate-400 mt-0.5">Sedang disusun</div>
        </a>
        <a href="{{ route('tender.rab.index', ['rab_status' => 'diajukan']) }}" class="bg-white p-3.5 rounded-lg border border-slate-200 shadow-xs hover:border-amber-400 transition {{ request('rab_status') === 'diajukan' ? 'ring-2 ring-amber-400' : '' }}">
            <div class="text-[11px] font-semibold text-amber-600 uppercase tracking-wider">Diajukan</div>
            <div class="text-xl font-bold text-amber-700 mt-1 font-numeric">{{ $rabCounts['diajukan'] ?? 0 }}</div>
            <div class="text-[10px] text-amber-600/80 mt-0.5">Menunggu Manajemen</div>
        </a>
        <a href="{{ route('tender.rab.index', ['rab_status' => 'disetujui']) }}" class="bg-white p-3.5 rounded-lg border border-slate-200 shadow-xs hover:border-emerald-400 transition {{ request('rab_status') === 'disetujui' ? 'ring-2 ring-emerald-400' : '' }}">
            <div class="text-[11px] font-semibold text-emerald-600 uppercase tracking-wider">Disetujui</div>
            <div class="text-xl font-bold text-emerald-700 mt-1 font-numeric">{{ $rabCounts['disetujui'] ?? 0 }}</div>
            <div class="text-[10px] text-emerald-600/80 mt-0.5">RAB resmi disetujui</div>
        </a>
        <a href="{{ route('tender.rab.index', ['rab_status' => 'perlu_revisi']) }}" class="bg-white p-3.5 rounded-lg border border-slate-200 shadow-xs hover:border-amber-400 transition {{ request('rab_status') === 'perlu_revisi' ? 'ring-2 ring-amber-400' : '' }}">
            <div class="text-[11px] font-semibold text-amber-700 uppercase tracking-wider">Perlu Revisi</div>
            <div class="text-xl font-bold text-amber-800 mt-1 font-numeric">{{ $rabCounts['perlu_revisi'] ?? 0 }}</div>
            <div class="text-[10px] text-amber-700/80 mt-0.5">Perlu disesuaikan</div>
        </a>
        <a href="{{ route('tender.rab.index', ['rab_status' => 'ditolak']) }}" class="bg-white p-3.5 rounded-lg border border-slate-200 shadow-xs hover:border-rose-400 transition {{ request('rab_status') === 'ditolak' ? 'ring-2 ring-rose-400' : '' }}">
            <div class="text-[11px] font-semibold text-rose-600 uppercase tracking-wider">Ditolak</div>
            <div class="text-xl font-bold text-rose-700 mt-1 font-numeric">{{ $rabCounts['ditolak'] ?? 0 }}</div>
            <div class="text-[10px] text-rose-500 mt-0.5">RAB tidak disetujui</div>
        </a>
    </div>

    <!-- Filter & Action Bar -->
    <div class="bg-white rounded-lg p-3.5 border border-slate-200 shadow-xs flex flex-col md:flex-row justify-between items-center gap-3">
        <form method="GET" action="{{ route('tender.rab.index') }}" class="flex flex-wrap items-center gap-2 w-full md:w-auto">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nomor tender / nama proyek..." class="px-3 py-1.5 rounded-md border border-slate-200 text-xs w-60 outline-none focus:ring-2 focus:ring-blue-500">
            
            <select name="rab_status" onchange="this.form.submit()" class="px-2.5 py-1.5 rounded-md border border-slate-200 text-xs font-medium text-slate-700 bg-white hover:bg-slate-50 outline-none">
                <option value="">Semua Status RAB</option>
                <option value="draft" {{ request('rab_status') == 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="diajukan" {{ request('rab_status') == 'diajukan' ? 'selected' : '' }}>Diajukan</option>
                <option value="disetujui" {{ request('rab_status') == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                <option value="perlu_revisi" {{ request('rab_status') == 'perlu_revisi' ? 'selected' : '' }}>Perlu Revisi</option>
                <option value="ditolak" {{ request('rab_status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
            </select>

            <button type="submit" class="px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 font-semibold text-xs rounded-md transition">
                Filter
            </button>
            @if(request()->hasAny(['search', 'rab_status']))
                <a href="{{ route('tender.rab.index') }}" class="px-2.5 py-1.5 text-xs text-slate-500 hover:text-slate-800 transition">Reset</a>
            @endif
        </form>

        <span class="text-xs text-slate-500">
            Pilih tender untuk menyusun atau meninjau rincian komponen RAB.
        </span>
    </div>

    <!-- Data Table Card -->
    <div class="bg-white rounded-lg border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-600 font-semibold border-b border-slate-200 uppercase tracking-wider">
                        <th class="p-3">No. Tender & Klien</th>
                        <th class="p-3">Status RAB</th>
                        <th class="p-3">Total Biaya HPP (Modal)</th>
                        <th class="p-3">Nilai Penawaran (Bid)</th>
                        <th class="p-3">Estimasi Laba (Profit)</th>
                        <th class="p-3">Margin (%)</th>
                        <th class="p-3">Kesiapan Material Dagang</th>
                        <th class="p-3">Rekomendasi Sistem</th>
                        <th class="p-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($tenders as $tender)
                        @php
                            $rec = $tender->execution_recommendation;
                            $hasDeficit = $tender->deficit_items_count > 0;
                        @endphp
                        <tr class="hover:bg-slate-50/70 transition">
                            <!-- No Tender & Klien -->
                            <td class="p-3">
                                <span class="font-mono font-medium text-slate-900 block">{{ $tender->tender_number }}</span>
                                <div class="font-semibold text-slate-800 mt-0.5 leading-snug">{{ $tender->name }}</div>
                                <div class="text-[11px] text-slate-500">{{ $tender->client->name ?? 'Instansi N/A' }}</div>
                            </td>

                            <!-- Status RAB -->
                            <td class="p-3 whitespace-nowrap">
                                @if(($tender->rab_status ?? 'draft') === 'draft')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Draft
                                    </span>
                                @elseif($tender->rab_status === 'diajukan')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-medium bg-amber-50 text-amber-800 border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span> Diajukan
                                    </span>
                                @elseif($tender->rab_status === 'disetujui')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-medium bg-emerald-50 text-emerald-800 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Disetujui
                                    </span>
                                @elseif($tender->rab_status === 'perlu_revisi')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-medium bg-amber-100 text-amber-900 border border-amber-300">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span> Perlu Revisi
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-medium bg-rose-50 text-rose-800 border border-rose-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Ditolak
                                    </span>
                                @endif
                                <div class="text-[10px] text-slate-400 mt-1">{{ $tender->rabItems->count() }} item biaya</div>
                            </td>

                            <!-- Total HPP -->
                            <td class="p-3 whitespace-nowrap font-numeric">
                                <div class="font-bold text-slate-900">Rp {{ number_format($tender->total_rab_cost, 0, ',', '.') }}</div>
                                <div class="text-[10px] text-slate-400">Total HPP Modal</div>
                            </td>

                            <!-- Nilai Penawaran -->
                            <td class="p-3 whitespace-nowrap font-numeric">
                                <div class="font-bold text-blue-700">Rp {{ number_format($tender->bid_value > 0 ? $tender->bid_value : $tender->total_rab_price, 0, ',', '.') }}</div>
                                <div class="text-[10px] text-slate-400">Nilai Penawaran Klien</div>
                            </td>

                            <!-- Estimasi Profit -->
                            <td class="p-3 whitespace-nowrap font-numeric">
                                @php
                                    $profit = $tender->estimated_profit;
                                @endphp
                                <div class="font-bold {{ $profit >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                                    Rp {{ number_format($profit, 0, ',', '.') }}
                                </div>
                                <div class="text-[10px] text-slate-400">Laba Kotor</div>
                            </td>

                            <!-- Margin % -->
                            <td class="p-3 whitespace-nowrap font-numeric">
                                @php
                                    $margin = $tender->profit_margin_percentage;
                                @endphp
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold {{ $margin >= 20 ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : ($margin > 0 ? 'bg-amber-50 text-amber-800 border border-amber-200' : 'bg-rose-50 text-rose-800 border border-rose-200') }}">
                                    {{ $margin }}%
                                </span>
                            </td>

                            <!-- Kesiapan Material Dagang -->
                            <td class="p-3 whitespace-nowrap">
                                @if($hasDeficit)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-semibold bg-rose-50 text-rose-800 border border-rose-200" title="{{ $tender->deficit_items_count }} item material kurang dari stok gudang">
                                        <i class="fa-solid fa-triangle-exclamation text-[10px]"></i> Stok Kurang ({{ $tender->deficit_items_count }} Item)
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <i class="fa-solid fa-circle-check text-[10px]"></i> Stok Mencukupi
                                    </span>
                                @endif
                            </td>

                            <!-- Rekomendasi Sistem -->
                            <td class="p-3 min-w-[180px]">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-semibold border {{ $rec['badge_class'] }}">
                                    {{ $rec['title'] }}
                                </span>
                            </td>

                            <!-- Aksi -->
                            <td class="p-3 text-center whitespace-nowrap">
                                <a href="{{ route('tender.rab.show', $tender) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs rounded-md shadow-xs transition">
                                    <i class="fa-solid fa-calculator text-[10px]"></i> Rincian RAB
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="p-8 text-center text-slate-400 text-sm">
                                <div class="flex flex-col items-center justify-center space-y-2">
                                    <i class="fa-solid fa-calculator text-2xl text-slate-300"></i>
                                    <span>Belum ada data tender untuk estimasi RAB.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100">
            {{ $tenders->links() }}
        </div>
    </div>

</div>
@endsection
