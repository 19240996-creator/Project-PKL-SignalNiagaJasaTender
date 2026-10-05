@extends('layouts.app')

@section('title', 'Dashboard')
@section('header-title', 'Dashboard')

@section('content')
@php
    $roleName = Auth::user()->role->name ?? '';
    $userDisplayName = Auth::user()->name ?? 'Pengguna';
    $isOwner = Auth::user()->isOwner();
    $isManager = Auth::user()->isManager();
    $isAdmin = Auth::user()->isAdmin();
@endphp

<div class="space-y-5">

    <!-- 1. Page Header & Periode Filter -->
    <div class="bg-white border border-slate-200/90 rounded-lg p-4 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-lg shrink-0">
                <i class="fa-solid fa-chart-line"></i>
            </div>
            <div>
                <h2 class="text-base font-bold text-slate-900 tracking-tight">Ringkasan Operasional Perusahaan</h2>
                <p class="text-xs text-slate-500">PT Signal Panca Utama • Pemantauan 3 Unit Bisnis Mandiri</p>
            </div>
        </div>

        <form method="GET" action="{{ route('dashboard') }}" class="flex items-center gap-2 self-start sm:self-auto">
            <span class="text-xs text-slate-500 font-medium">Periode:</span>
            <select name="period" onchange="this.form.submit()" class="px-3 py-1.5 rounded-md border border-slate-200 text-xs font-medium text-slate-700 bg-white hover:bg-slate-50 outline-none focus:ring-2 focus:ring-blue-500">
                <option value="all" {{ ($period ?? 'all') === 'all' ? 'selected' : '' }}>Semua Waktu</option>
                <option value="month" {{ ($period ?? '') === 'month' ? 'selected' : '' }}>Bulan Ini</option>
                <option value="year" {{ ($period ?? '') === 'year' ? 'selected' : '' }}>Tahun Ini</option>
            </select>
        </form>
    </div>

    <!-- 2. Manager Alert (Hanya muncul jika ada pengajuan pending) -->
    @if($isManager && ($totalPendingApprovals ?? 0) > 0)
        <div class="border-l-4 border-amber-500 bg-white border border-slate-200/90 rounded-lg p-3.5 flex flex-col md:flex-row md:items-center justify-between gap-3 shadow-xs">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-md bg-amber-50 text-amber-700 flex items-center justify-center text-sm shrink-0">
                    <i class="fa-solid fa-clock"></i>
                </div>
                <div>
                    <div class="text-xs font-bold text-slate-900">Persetujuan Transaksi Menunggu Verifikasi</div>
                    <div class="text-xs text-slate-500">Terdapat <span class="font-semibold text-slate-800">{{ $totalPendingApprovals }} pengajuan</span> dari Admin yang memerlukan persetujuan Manager.</div>
                </div>
            </div>
            <div class="flex items-center gap-2">
                @if(($pendingTendersCount ?? 0) > 0)
                    <a href="{{ route('tender.index', ['approval_status' => 'pending']) }}" class="px-2.5 py-1 bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 rounded-md text-xs font-semibold transition">
                        Tender ({{ $pendingTendersCount }})
                    </a>
                @endif
                @if(($pendingServicesCount ?? 0) > 0)
                    <a href="{{ route('jasa.index', ['approval_status' => 'pending']) }}" class="px-2.5 py-1 bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 rounded-md text-xs font-semibold transition">
                        Jasa ({{ $pendingServicesCount }})
                    </a>
                @endif
                @if(($pendingSalesCount ?? 0) > 0)
                    <a href="{{ route('sales.index', ['approval_status' => 'pending']) }}" class="px-2.5 py-1 bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 rounded-md text-xs font-semibold transition">
                        Dagang ({{ $pendingSalesCount }})
                    </a>
                @endif
            </div>
        </div>
    @endif

    <!-- 3. Owner Direct Report Navigation Bar -->
    @if($isOwner)
        <div class="border border-slate-200/90 bg-white rounded-lg p-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-xs">
            <div>
                <span class="text-xs font-bold text-slate-900 block leading-tight">Akses Cepat Laporan Eksekutif</span>
                <span class="text-xs text-slate-500">Buka rincian pembukuan per unit bisnis atau konsolidasi perusahaan:</span>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('laporan.index', ['domain' => 'tender']) }}" class="px-3 py-1.5 text-xs font-medium bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 rounded-md transition flex items-center gap-1.5">
                    <i class="fa-solid fa-file-contract"></i> Laporan Tender
                </a>
                <a href="{{ route('laporan.index', ['domain' => 'jasa']) }}" class="px-3 py-1.5 text-xs font-medium bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 rounded-md transition flex items-center gap-1.5">
                    <i class="fa-solid fa-wrench"></i> Laporan Jasa
                </a>
                <a href="{{ route('laporan.index', ['domain' => 'barang']) }}" class="px-3 py-1.5 text-xs font-medium bg-purple-50 hover:bg-purple-100 text-purple-700 border border-purple-200 rounded-md transition flex items-center gap-1.5">
                    <i class="fa-solid fa-boxes-stacked"></i> Laporan Dagang
                </a>
                <a href="{{ route('laporan.index', ['domain' => 'semua']) }}" class="px-3.5 py-1.5 text-xs font-semibold bg-blue-600 hover:bg-blue-700 text-white rounded-md transition flex items-center gap-1.5 shadow-xs">
                    <i class="fa-solid fa-chart-pie"></i> Ringkasan 3 Bisnis
                </a>
            </div>
        </div>
    @endif

    <!-- 4. 4 Metric Cards (Identitas Warna: Netral, Biru, Hijau, Ungu) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Metric 1: Total Transaksi -->
        <div class="bg-white border-l-4 border-slate-700 border border-slate-200/90 rounded-lg p-4 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Total Nilai Transaksi</span>
                <div class="w-8 h-8 rounded-md bg-slate-100 text-slate-700 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-wallet"></i>
                </div>
            </div>
            <div class="mt-2">
                <div class="text-xl md:text-2xl font-bold text-slate-900 tracking-tight font-mono truncate" title="Rp {{ number_format($companyTotalTransactionValue, 0, ',', '.') }}">
                    Rp {{ number_format($companyTotalTransactionValue, 0, ',', '.') }}
                </div>
                <div class="text-xs text-slate-500 mt-1 flex items-center gap-1">
                    <span class="font-semibold text-slate-700">{{ $companyTotalActivities }}</span> total transaksi perusahaan
                </div>
            </div>
        </div>

        <!-- Metric 2: Tender (Biru) -->
        <div class="bg-white border-l-4 border-blue-500 border border-slate-200/90 rounded-lg p-4 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Nilai Portofolio Tender</span>
                <div class="w-8 h-8 rounded-md bg-blue-50 text-blue-600 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-file-contract"></i>
                </div>
            </div>
            <div class="mt-2">
                <div class="text-xl md:text-2xl font-bold text-slate-900 tracking-tight font-mono truncate" title="Rp {{ number_format($totalTenderValue, 0, ',', '.') }}">
                    Rp {{ number_format($totalTenderValue, 0, ',', '.') }}
                </div>
                <div class="text-xs text-slate-500 mt-1 flex items-center gap-1">
                    <span class="font-semibold text-blue-600">{{ $totalTenders }}</span> proyek lelang terdaftar
                </div>
            </div>
        </div>

        <!-- Metric 3: Jasa (Hijau) -->
        <div class="bg-white border-l-4 border-emerald-500 border border-slate-200/90 rounded-lg p-4 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Total Biaya Jasa</span>
                <div class="w-8 h-8 rounded-md bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-wrench"></i>
                </div>
            </div>
            <div class="mt-2">
                <div class="text-xl md:text-2xl font-bold text-slate-900 tracking-tight font-mono truncate" title="Rp {{ number_format($totalServiceValue, 0, ',', '.') }}">
                    Rp {{ number_format($totalServiceValue, 0, ',', '.') }}
                </div>
                <div class="text-xs text-slate-500 mt-1 flex items-center gap-1">
                    <span class="font-semibold text-emerald-600">{{ $totalServices }}</span> pekerjaan jasa teknis
                </div>
            </div>
        </div>

        <!-- Metric 4: Dagang (Ungu) -->
        <div class="bg-white border-l-4 border-purple-500 border border-slate-200/90 rounded-lg p-4 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Total Omzet Dagang</span>
                <div class="w-8 h-8 rounded-md bg-purple-50 text-purple-600 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-boxes-stacked"></i>
                </div>
            </div>
            <div class="mt-2">
                <div class="text-xl md:text-2xl font-bold text-slate-900 tracking-tight font-mono truncate" title="Rp {{ number_format($tradeRevenue, 0, ',', '.') }}">
                    Rp {{ number_format($tradeRevenue, 0, ',', '.') }}
                </div>
                <div class="text-xs text-slate-500 mt-1 flex items-center gap-1">
                    <span class="font-semibold text-purple-600">{{ $totalSales }}</span> transaksi penjualan barang
                </div>
            </div>
        </div>
    </div>

    <!-- 5. Tiga Bidang Usaha Mandiri (3-Column Clean Blue-White Panel) -->
    <div>
        <div class="mb-3 flex items-center gap-2">
            <span class="w-1.5 h-3.5 bg-blue-600 rounded-xs"></span>
            <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider">Performa 3 Unit Bisnis Mandiri</h3>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Bisnis 1: Tender -->
            <div class="bg-white border border-slate-200/90 rounded-lg p-5 shadow-xs flex flex-col justify-between hover:border-blue-400 transition-colors">
                <div>
                    <div class="flex items-start justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center space-x-2.5">
                            <div class="w-9 h-9 rounded-md bg-blue-50 text-blue-600 flex items-center justify-center text-sm shrink-0">
                                <i class="fa-solid fa-file-contract"></i>
                            </div>
                            <div>
                                <span class="text-[10px] font-bold text-blue-600 uppercase tracking-wider">Bisnis 1</span>
                                <h4 class="text-sm font-bold text-slate-900 leading-tight">Tender & Lelang</h4>
                            </div>
                        </div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                            Win Rate: {{ $winRate }}%
                        </span>
                    </div>

                    <div class="py-3.5 space-y-2.5 text-xs">
                        <div class="flex justify-between py-1 border-b border-slate-50">
                            <span class="text-slate-500">Tender Berjalan</span>
                            <span class="font-semibold text-slate-800">{{ $tendersActive }} proyek</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-50">
                            <span class="text-slate-500">Hasil (Menang / Kalah)</span>
                            <span class="font-semibold text-slate-800">{{ $tendersWon }} menang / {{ $tendersLost }} kalah</span>
                        </div>
                        <div class="flex justify-between py-1">
                            <span class="text-slate-500">Nilai Bidding Portofolio</span>
                            <span class="font-mono font-bold text-blue-700">Rp {{ number_format($totalTenderValue, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-100">
                    @if($isOwner)
                        <a href="{{ route('laporan.index', ['domain' => 'tender']) }}" class="w-full px-3 py-2 bg-blue-50 hover:bg-blue-100 text-blue-700 font-semibold text-xs rounded-md transition flex items-center justify-between border border-blue-200">
                            <span>Buka Laporan Tender</span>
                            <i class="fa-solid fa-arrow-right text-[11px]"></i>
                        </a>
                    @else
                        <a href="{{ route('tender.index') }}" class="w-full px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs rounded-md transition flex items-center justify-between shadow-xs">
                            <span>Kelola Data Tender</span>
                            <i class="fa-solid fa-arrow-right text-[11px]"></i>
                        </a>
                    @endif
                </div>
            </div>

            <!-- Bisnis 2: Jasa (Hijau) -->
            <div class="bg-white border border-slate-200/90 rounded-lg p-5 shadow-xs flex flex-col justify-between hover:border-emerald-400 transition-colors">
                <div>
                    <div class="flex items-start justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center space-x-2.5">
                            <div class="w-9 h-9 rounded-md bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm shrink-0">
                                <i class="fa-solid fa-wrench"></i>
                            </div>
                            <div>
                                <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider">Bisnis 2</span>
                                <h4 class="text-sm font-bold text-slate-900 leading-tight">Layanan Jasa</h4>
                            </div>
                        </div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            {{ $servicesActive }} aktif
                        </span>
                    </div>

                    <div class="py-3.5 space-y-2.5 text-xs">
                        <div class="flex justify-between py-1 border-b border-slate-50">
                            <span class="text-slate-500">Pekerjaan Selesai</span>
                            <span class="font-semibold text-slate-800">{{ $servicesCompleted }} pekerjaan</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-50">
                            <span class="text-slate-500">Total Ditagih / Invoice</span>
                            <span class="font-mono font-semibold text-slate-800">Rp {{ number_format($totalServiceBilled, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between py-1">
                            <span class="text-slate-500">Total Biaya Layanan</span>
                            <span class="font-mono font-bold text-emerald-700">Rp {{ number_format($totalServiceValue, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-100">
                    @if($isOwner)
                        <a href="{{ route('laporan.index', ['domain' => 'jasa']) }}" class="w-full px-3 py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-semibold text-xs rounded-md transition flex items-center justify-between border border-emerald-200">
                            <span>Buka Laporan Jasa</span>
                            <i class="fa-solid fa-arrow-right text-[11px]"></i>
                        </a>
                    @else
                        <a href="{{ route('jasa.index') }}" class="w-full px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs rounded-md transition flex items-center justify-between shadow-xs">
                            <span>Kelola Data Jasa</span>
                            <i class="fa-solid fa-arrow-right text-[11px]"></i>
                        </a>
                    @endif
                </div>
            </div>

            <!-- Bisnis 3: Dagang (Ungu) -->
            <div class="bg-white border border-slate-200/90 rounded-lg p-5 shadow-xs flex flex-col justify-between hover:border-purple-400 transition-colors">
                <div>
                    <div class="flex items-start justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center space-x-2.5">
                            <div class="w-9 h-9 rounded-md bg-purple-50 text-purple-600 flex items-center justify-center text-sm shrink-0">
                                <i class="fa-solid fa-boxes-stacked"></i>
                            </div>
                            <div>
                                <span class="text-[10px] font-bold text-purple-600 uppercase tracking-wider">Bisnis 3</span>
                                <h4 class="text-sm font-bold text-slate-900 leading-tight">Dagang (Barang)</h4>
                            </div>
                        </div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-purple-50 text-purple-700 border border-purple-200">
                            {{ $totalProductItems }} jenis item
                        </span>
                    </div>

                    <div class="py-3.5 space-y-2.5 text-xs">
                        <div class="flex justify-between py-1 border-b border-slate-50">
                            <span class="text-slate-500">Total Transaksi Penjualan</span>
                            <span class="font-semibold text-slate-800">{{ $totalSales }} transaksi</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-50">
                            <span class="text-slate-500">Stok Barang Tersedia</span>
                            <span class="font-mono font-semibold text-slate-800">{{ number_format($totalStockUnits, 0, ',', '.') }} unit</span>
                        </div>
                        <div class="flex justify-between py-1">
                            <span class="text-slate-500">Total Omzet Penjualan</span>
                            <span class="font-mono font-bold text-purple-700">Rp {{ number_format($tradeRevenue, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-100">
                    @if($isOwner)
                        <a href="{{ route('laporan.index', ['domain' => 'barang']) }}" class="w-full px-3 py-2 bg-purple-50 hover:bg-purple-100 text-purple-700 font-semibold text-xs rounded-md transition flex items-center justify-between border border-purple-200">
                            <span>Buka Laporan Dagang</span>
                            <i class="fa-solid fa-arrow-right text-[11px]"></i>
                        </a>
                    @else
                        <a href="{{ route('sales.index') }}" class="w-full px-3 py-2 bg-purple-600 hover:bg-purple-700 text-white font-semibold text-xs rounded-md transition flex items-center justify-between shadow-xs">
                            <span>Kelola Data Dagang</span>
                            <i class="fa-solid fa-arrow-right text-[11px]"></i>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>
@endsection