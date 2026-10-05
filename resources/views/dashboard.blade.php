@extends('layouts.app')

@section('title', 'Dashboard')
@section('header-title', 'Dashboard')

@section('content')
@php
    $user = Auth::user();
    $userDisplayName = $user->name ?? 'Pengguna';
    $isOwner = $user->isOwner();
    $isManager = $user->isManager();
    $firstName = explode(' ', trim($userDisplayName))[0];
    $tenderFinished = ($tendersWon ?? 0) + ($tendersLost ?? 0);
@endphp

<div class="space-y-5 max-w-7xl mx-auto pb-10">

    <!-- 1. HEADER (Simple, Clear & Welcoming) -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">
                Selamat Datang, <span class="text-blue-600">{{ $firstName }}</span>.
            </h1>
            <p class="mt-1 text-xs sm:text-sm text-slate-500 font-medium">
                Ringkasan performa pengadaan tender, pekerjaan jasa, dan perdagangan barang PT Signal Panca Utama.
            </p>
        </div>

        <!-- Filter Periode (Solid, Clear Border) -->
        <form method="GET" action="{{ route('dashboard') }}" class="flex items-center gap-1.5 self-start sm:self-auto bg-white border border-slate-300 px-3 py-1.5 rounded-xl shadow-xs">
            <span class="text-xs font-medium text-slate-500">Periode:</span>
            <select name="period" onchange="this.form.submit()" class="bg-transparent text-xs font-bold text-slate-800 px-1 outline-none cursor-pointer hover:text-blue-600">
                <option value="all" {{ ($period ?? 'all') === 'all' ? 'selected' : '' }}>Semua Waktu</option>
                <option value="month" {{ ($period ?? '') === 'month' ? 'selected' : '' }}>Bulan Ini</option>
                <option value="year" {{ ($period ?? '') === 'year' ? 'selected' : '' }}>Tahun Ini</option>
            </select>
        </form>
    </div>

    <!-- Alert Persetujuan (Hanya jika ada) -->
    @if($isManager && ($totalPendingApprovals ?? 0) > 0)
        <div class="flex items-center justify-between gap-3 px-4 py-3 rounded-xl bg-blue-50/80 border border-blue-200 text-xs shadow-xs">
            <div class="flex items-center gap-2.5 text-slate-800 font-medium">
                <span class="w-2.5 h-2.5 rounded-full bg-blue-600 animate-pulse"></span>
                <span><strong class="font-number">{{ $totalPendingApprovals }}</strong> pengajuan menunggu persetujuan Anda:</span>
            </div>
            <div class="flex items-center gap-2 font-semibold">
                @if(($pendingTendersCount ?? 0) > 0)
                    <a href="{{ route('tender.index', ['approval_status' => 'pending']) }}" class="text-blue-600 hover:underline">Tender (<span class="font-number">{{ $pendingTendersCount }}</span>)</a>
                @endif
                @if(($pendingServicesCount ?? 0) > 0)
                    <span class="text-slate-300">•</span>
                    <a href="{{ route('jasa.index', ['approval_status' => 'pending']) }}" class="text-blue-600 hover:underline">Jasa (<span class="font-number">{{ $pendingServicesCount }}</span>)</a>
                @endif
                @if(($pendingSalesCount ?? 0) > 0)
                    <span class="text-slate-300">•</span>
                    <a href="{{ route('sales.index', ['approval_status' => 'pending']) }}" class="text-blue-600 hover:underline">Dagang (<span class="font-number">{{ $pendingSalesCount }}</span>)</a>
                @endif
            </div>
        </div>
    @endif

    <!-- 2. WIDGET UTAMA: 4 METRIK KPI RINGKAS (Kotak Widget Tegas, Jelas & Solid) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
        <!-- 1. Total Nilai Transaksi -->
        <div class="bg-white rounded-2xl border-2 border-slate-200/90 shadow-sm p-5 hover:border-blue-400 hover:shadow transition duration-150">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-500 block">Total Nilai Transaksi</span>
            <span class="text-xl sm:text-2xl font-extrabold tracking-tight text-slate-900 block mt-2 truncate font-number" title="Rp {{ number_format($companyTotalTransactionValue, 0, ',', '.') }}">
                Rp {{ number_format($companyTotalTransactionValue, 0, ',', '.') }}
            </span>
            <span class="text-xs text-slate-600 font-medium mt-1.5 block">
                <span class="font-number font-bold text-slate-800">{{ $companyTotalActivities }}</span> aktivitas tercatat
            </span>
        </div>

        <!-- 2. Pendapatan Realisasi -->
        <div class="bg-white rounded-2xl border-2 border-slate-200/90 shadow-sm p-5 hover:border-blue-400 hover:shadow transition duration-150">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-500 block">Pendapatan Realisasi</span>
            <span class="text-xl sm:text-2xl font-extrabold tracking-tight text-slate-900 block mt-2 truncate font-number" title="Rp {{ number_format($companyTotalRevenue, 0, ',', '.') }}">
                Rp {{ number_format($companyTotalRevenue, 0, ',', '.') }}
            </span>
            <span class="text-xs text-slate-600 font-medium mt-1.5 block">
                <span class="font-number font-bold text-slate-800">{{ $unpaidInvoicesCount }}</span> invoice belum lunas
            </span>
        </div>

        <!-- 3. Aktivitas Berjalan -->
        <div class="bg-white rounded-2xl border-2 border-slate-200/90 shadow-sm p-5 hover:border-blue-400 hover:shadow transition duration-150">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-500 block">Aktivitas Berjalan</span>
            <span class="text-xl sm:text-2xl font-extrabold tracking-tight text-slate-900 block mt-2 font-number">
                {{ $companyActiveActivities }}
            </span>
            <span class="text-xs text-slate-600 font-medium mt-1.5 block">
                <span class="font-number font-bold text-slate-800">{{ $tendersActive }}</span> tender • <span class="font-number font-bold text-slate-800">{{ $servicesActive }}</span> jasa
            </span>
        </div>

        <!-- 4. Win Rate Tender -->
        <div class="bg-white rounded-2xl border-2 border-slate-200/90 shadow-sm p-5 hover:border-blue-400 hover:shadow transition duration-150">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-500 block">Win Rate Tender</span>
            <span class="text-xl sm:text-2xl font-extrabold tracking-tight text-slate-900 block mt-2 font-number">
                {{ $winRate }}%
            </span>
            <span class="text-xs text-slate-600 font-medium mt-1.5 block">
                <span class="font-number font-bold text-slate-800">{{ $tendersWon }}</span> menang dari <span class="font-number font-bold text-slate-800">{{ $tenderFinished }}</span> selesai
            </span>
        </div>
    </div>

    <!-- 3. TIGA PILAR BISNIS (Kotak Widget Tegas & Solid) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-5">
        <!-- Tender & Lelang -->
        <div class="bg-white rounded-2xl border-2 border-slate-200/90 shadow-sm p-5 flex flex-col justify-between hover:border-blue-400 hover:shadow transition duration-150">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2.5">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-200 flex items-center justify-center text-blue-600 text-sm shadow-xs">
                            <i class="fa-solid fa-file-contract"></i>
                        </div>
                        <h3 class="text-sm font-bold text-slate-900">Tender & Lelang</h3>
                    </div>
                    <span class="text-xs font-bold px-2.5 py-1 rounded-md bg-slate-100 border border-slate-300 text-slate-800 font-number">{{ $tendersActive }} Aktif</span>
                </div>
                <div class="text-xs space-y-2.5 text-slate-700">
                    <div class="flex justify-between py-1 border-b border-slate-100">
                        <span class="text-slate-600 font-medium">Nilai Penawaran</span>
                        <span class="font-bold text-slate-900 font-number">Rp {{ number_format($totalTenderValue, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between py-1">
                        <span class="text-slate-600 font-medium">Realisasi Menang</span>
                        <span class="font-semibold text-slate-800"><span class="font-number font-bold text-slate-900">{{ $tendersWon }}</span> dari <span class="font-number font-bold text-slate-900">{{ $totalTenders }}</span> tender</span>
                    </div>
                </div>
            </div>
            <div class="mt-4 pt-3.5 border-t border-slate-200">
                <a href="{{ $isOwner ? route('laporan.index', ['domain' => 'tender']) : route('tender.index') }}" class="text-xs font-bold text-blue-600 hover:text-blue-800 inline-flex items-center gap-1.5 transition">
                    <span>Buka Modul Tender</span>
                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </a>
            </div>
        </div>

        <!-- Layanan Jasa -->
        <div class="bg-white rounded-2xl border-2 border-slate-200/90 shadow-sm p-5 flex flex-col justify-between hover:border-blue-400 hover:shadow transition duration-150">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2.5">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-200 flex items-center justify-center text-blue-600 text-sm shadow-xs">
                            <i class="fa-solid fa-wrench"></i>
                        </div>
                        <h3 class="text-sm font-bold text-slate-900">Layanan Jasa</h3>
                    </div>
                    <span class="text-xs font-bold px-2.5 py-1 rounded-md bg-slate-100 border border-slate-300 text-slate-800 font-number">{{ $servicesActive }} Berjalan</span>
                </div>
                <div class="text-xs space-y-2.5 text-slate-700">
                    <div class="flex justify-between py-1 border-b border-slate-100">
                        <span class="text-slate-600 font-medium">Nilai Kontrak Aktif</span>
                        <span class="font-bold text-slate-900 font-number">Rp {{ number_format($totalServiceValue, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between py-1">
                        <span class="text-slate-600 font-medium">Kontrak & Selesai</span>
                        <span class="font-semibold text-slate-800"><span class="font-number font-bold text-slate-900">{{ $activeContracts }}</span> kontrak • <span class="font-number font-bold text-slate-900">{{ $servicesCompleted }}</span> selesai</span>
                    </div>
                </div>
            </div>
            <div class="mt-4 pt-3.5 border-t border-slate-200">
                <a href="{{ $isOwner ? route('laporan.index', ['domain' => 'jasa']) : route('jasa.index') }}" class="text-xs font-bold text-blue-600 hover:text-blue-800 inline-flex items-center gap-1.5 transition">
                    <span>Buka Modul Jasa</span>
                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </a>
            </div>
        </div>

        <!-- Perdagangan Barang -->
        <div class="bg-white rounded-2xl border-2 border-slate-200/90 shadow-sm p-5 flex flex-col justify-between hover:border-blue-400 hover:shadow transition duration-150">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2.5">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-200 flex items-center justify-center text-blue-600 text-sm shadow-xs">
                            <i class="fa-solid fa-boxes-stacked"></i>
                        </div>
                        <h3 class="text-sm font-bold text-slate-900">Perdagangan Barang</h3>
                    </div>
                    <span class="text-xs font-bold px-2.5 py-1 rounded-md bg-slate-100 border border-slate-300 text-slate-800 font-number">{{ $totalSales }} Penjualan</span>
                </div>
                <div class="text-xs space-y-2.5 text-slate-700">
                    <div class="flex justify-between py-1 border-b border-slate-100">
                        <span class="text-slate-600 font-medium">Nilai Penjualan</span>
                        <span class="font-bold text-slate-900 font-number">Rp {{ number_format($tradeRevenue, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between py-1">
                        <span class="text-slate-600 font-medium">Stok Gudang</span>
                        <span class="font-semibold text-slate-800"><span class="font-number font-bold text-slate-900">{{ $totalStockUnits }}</span> unit • <span class="font-number font-bold text-slate-900">{{ $totalProductItems }}</span> jenis item</span>
                    </div>
                </div>
            </div>
            <div class="mt-4 pt-3.5 border-t border-slate-200">
                <a href="{{ $isOwner ? route('laporan.index', ['domain' => 'barang']) : route('sales.index') }}" class="text-xs font-bold text-blue-600 hover:text-blue-800 inline-flex items-center gap-1.5 transition">
                    <span>Buka Modul Dagang</span>
                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- 4. DUA KOLOM: DEADLINE & KESEHATAN OPERASIONAL -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-5">
        <!-- Tenggat Waktu Tender -->
        <div class="bg-white rounded-2xl border-2 border-slate-200/90 shadow-sm p-5">
            <div class="flex items-center justify-between pb-3.5 border-b border-slate-200">
                <div class="flex items-center gap-2">
                    <i class="fa-regular fa-calendar text-blue-600 text-sm"></i>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800">Tenggat Waktu Tender</h3>
                </div>
                <a href="{{ route('tender.index') }}" class="text-xs font-bold text-blue-600 hover:underline">Semua Tender</a>
            </div>

            <div class="divide-y divide-slate-200">
                @forelse($upcomingDeadlines as $tender)
                    @php $daysLeft = now()->startOfDay()->diffInDays($tender->deadline, false); @endphp
                    <div class="py-3 flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <a href="{{ route('tender.index') }}" class="text-xs font-bold text-slate-900 hover:text-blue-600 truncate block">
                                {{ $tender->name ?: $tender->tender_number }}
                            </a>
                            <p class="text-xs text-slate-500 truncate mt-0.5 font-medium">
                                {{ $tender->client?->name ?? 'Klien belum diisi' }} • <span class="font-number font-semibold text-slate-700">{{ $tender->deadline->format('d M Y') }}</span>
                            </p>
                        </div>
                        <span class="shrink-0 px-2.5 py-1 rounded-md text-xs font-bold font-number {{ $daysLeft <= 3 ? 'bg-blue-50 text-blue-700 border border-blue-300' : 'bg-slate-100 text-slate-700 border border-slate-300' }}">
                            {{ $daysLeft === 0 ? 'Hari Ini' : $daysLeft . ' Hari Lagi' }}
                        </span>
                    </div>
                @empty
                    <div class="py-8 text-center text-xs text-slate-500 font-medium">
                        Tidak ada deadline tender dalam 14 hari ke depan.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Status Finansial & Gudang -->
        <div class="bg-white rounded-2xl border-2 border-slate-200/90 shadow-sm p-5 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-3.5 border-b border-slate-200">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-chart-simple text-blue-600 text-sm"></i>
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800">Status Finansial & Inventaris</h3>
                    </div>
                    @if($isOwner)
                        <a href="{{ route('laporan.index') }}" class="text-xs font-bold text-blue-600 hover:underline">Laporan Lengkap</a>
                    @endif
                </div>

                <div class="divide-y divide-slate-200 text-xs py-1">
                    <div class="py-3 flex justify-between items-center">
                        <div>
                            <span class="font-bold text-slate-800 block text-xs">Piutang Belum Lunas</span>
                            <span class="text-slate-500 text-[11px] font-medium"><span class="font-number font-semibold text-slate-700">{{ $unpaidInvoicesCount }}</span> invoice menunggu pembayaran</span>
                        </div>
                        <a href="{{ route('invoices.index') }}" class="font-bold text-sm text-slate-900 hover:text-blue-600 font-number">
                            Rp {{ number_format($totalOutstandingPiutang, 0, ',', '.') }}
                        </a>
                    </div>

                    <div class="py-3 flex justify-between items-center">
                        <div>
                            <span class="font-bold text-slate-800 block text-xs">Stok Gudang</span>
                            <span class="text-[11px] font-medium {{ $lowStockProducts->count() > 0 ? 'text-blue-600 font-semibold' : 'text-slate-500' }}">
                                {{ $lowStockProducts->count() > 0 ? $lowStockProducts->count() . ' produk perlu restock' : 'Semua stok aman' }}
                            </span>
                        </div>
                        <a href="{{ route('products.index') }}" class="font-bold text-sm text-slate-900 hover:text-blue-600 font-number">
                            {{ number_format($totalStockUnits, 0, ',', '.') }} unit
                        </a>
                    </div>

                    <div class="py-3 flex justify-between items-center">
                        <div>
                            <span class="font-bold text-slate-800 block text-xs">Pengadaan Suplai</span>
                            <span class="text-slate-500 text-[11px] font-medium"><span class="font-number font-semibold text-slate-700">{{ $topSuppliers->count() }}</span> supplier terdaftar</span>
                        </div>
                        <a href="{{ route('procurements.index') }}" class="font-bold text-sm text-slate-900 hover:text-blue-600 font-number">
                            Rp {{ number_format($totalProcurementCost, 0, ',', '.') }}
                        </a>
                    </div>
                </div>
            </div>

            <div class="pt-3.5 border-t border-slate-200 flex items-center justify-between text-xs text-slate-600 font-medium">
                <span>Total Kas Masuk: <strong class="text-slate-900 font-number">Rp {{ number_format($totalPaidAmount, 0, ',', '.') }}</strong></span>
                <span class="text-blue-600 font-bold"><span class="font-number">{{ $companyTotalActivities }}</span> total aktivitas</span>
            </div>
        </div>
    </div>

</div>
@endsection
