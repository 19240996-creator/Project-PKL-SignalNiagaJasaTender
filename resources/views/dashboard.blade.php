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
@endphp

<div class="space-y-10 pb-10">
    <section class="border-b border-slate-200 pb-8 pt-2">
        <div class="flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
            <div class="max-w-2xl">
                <p class="mb-4 text-[11px] font-semibold uppercase tracking-[0.18em] text-blue-600">Ringkasan perusahaan</p>
                <h2 class="text-3xl font-semibold tracking-[-0.04em] text-slate-950 md:text-5xl">Selamat datang, {{ $firstName }}.</h2>
                <p class="mt-4 max-w-xl text-sm leading-6 text-slate-500">Satu ruang untuk melihat arah tender, jasa, dan perdagangan hari ini.</p>
            </div>
            <form method="GET" action="{{ route('dashboard') }}" class="flex items-center gap-3">
                <label for="period" class="text-xs font-medium text-slate-500">Rentang data</label>
                <select id="period" name="period" onchange="this.form.submit()" class="rounded-md border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-700 outline-none transition focus:border-blue-600 focus:ring-1 focus:ring-blue-600">
                    <option value="all" {{ ($period ?? 'all') === 'all' ? 'selected' : '' }}>Semua waktu</option>
                    <option value="month" {{ ($period ?? '') === 'month' ? 'selected' : '' }}>Bulan ini</option>
                    <option value="year" {{ ($period ?? '') === 'year' ? 'selected' : '' }}>Tahun ini</option>
                </select>
            </form>
        </div>
    </section>

    @if($isManager && ($totalPendingApprovals ?? 0) > 0)
        <section class="flex flex-col gap-4 border-l-2 border-blue-600 bg-white px-5 py-4 shadow-sm md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-sm font-semibold text-slate-900">{{ $totalPendingApprovals }} pengajuan menunggu verifikasi</p>
                <p class="mt-1 text-xs text-slate-500">Tinjau transaksi sebelum dilanjutkan ke proses berikutnya.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                @if(($pendingTendersCount ?? 0) > 0)
                    <a href="{{ route('tender.index', ['approval_status' => 'pending']) }}" class="rounded-md border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 transition hover:border-blue-600 hover:text-blue-600">Tender {{ $pendingTendersCount }}</a>
                @endif
                @if(($pendingServicesCount ?? 0) > 0)
                    <a href="{{ route('jasa.index', ['approval_status' => 'pending']) }}" class="rounded-md border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 transition hover:border-blue-600 hover:text-blue-600">Jasa {{ $pendingServicesCount }}</a>
                @endif
                @if(($pendingSalesCount ?? 0) > 0)
                    <a href="{{ route('sales.index', ['approval_status' => 'pending']) }}" class="rounded-md border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 transition hover:border-blue-600 hover:text-blue-600">Dagang {{ $pendingSalesCount }}</a>
                @endif
            </div>
        </section>
    @endif

    <section>
        <div class="flex flex-col gap-2 border-b border-slate-200 pb-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400">Sekilas</p>
                <h3 class="mt-1 text-lg font-semibold tracking-tight text-slate-900">Angka utama</h3>
            </div>
            <p class="text-xs text-slate-400">PT Signal Panca Utama</p>
        </div>
        <div class="grid grid-cols-1 divide-y divide-slate-200 sm:grid-cols-2 sm:divide-x sm:divide-y-0 lg:grid-cols-4">
            <div class="py-5 sm:pr-5">
                <p class="text-xs text-slate-500">Nilai transaksi</p>
                <p class="mt-3 truncate font-mono text-2xl font-semibold tracking-tight text-slate-950" title="Rp {{ number_format($companyTotalTransactionValue, 0, ',', '.') }}">Rp {{ number_format($companyTotalTransactionValue, 0, ',', '.') }}</p>
                <p class="mt-2 text-xs text-slate-400">{{ $companyTotalActivities }} aktivitas tercatat</p>
            </div>
            <div class="py-5 sm:px-5">
                <p class="text-xs text-slate-500">Tender berjalan</p>
                <p class="mt-3 font-mono text-2xl font-semibold tracking-tight text-slate-950">{{ $tendersActive }}</p>
                <p class="mt-2 text-xs text-slate-400">{{ $tendersWon }} tender menang</p>
            </div>
            <div class="py-5 sm:px-5">
                <p class="text-xs text-slate-500">Pendapatan terealisasi</p>
                <p class="mt-3 truncate font-mono text-2xl font-semibold tracking-tight text-slate-950" title="Rp {{ number_format($companyTotalRevenue, 0, ',', '.') }}">Rp {{ number_format($companyTotalRevenue, 0, ',', '.') }}</p>
                <p class="mt-2 text-xs text-slate-400">{{ $unpaidInvoicesCount }} invoice belum lunas</p>
            </div>
            <div class="py-5 sm:pl-5">
                <p class="text-xs text-slate-500">Aktivitas aktif</p>
                <p class="mt-3 font-mono text-2xl font-semibold tracking-tight text-slate-950">{{ $companyActiveActivities }}</p>
                <p class="mt-2 text-xs text-slate-400">{{ $totalProductItems }} jenis produk</p>
            </div>
        </div>
    </section>

    <section class="grid grid-cols-1 gap-8 lg:grid-cols-[1.15fr_0.85fr]">
        <div>
            <div class="flex items-end justify-between border-b border-slate-200 pb-4">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400">Perlu dilihat</p>
                    <h3 class="mt-1 text-lg font-semibold tracking-tight text-slate-900">Deadline tender</h3>
                </div>
                <a href="{{ route('tender.index') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-800">Semua tender <i class="fa-solid fa-arrow-up-right-from-square ml-1 text-[10px]"></i></a>
            </div>
            <div class="mt-2">
                @forelse($upcomingDeadlines as $tender)
                    @php $daysLeft = now()->startOfDay()->diffInDays($tender->deadline, false); @endphp
                    <a href="{{ route('tender.index') }}" class="group flex items-center gap-4 border-b border-slate-100 py-4 transition hover:pl-1">
                        <div class="w-12 shrink-0 text-center">
                            <p class="font-mono text-xl font-semibold leading-none text-slate-900">{{ $tender->deadline->format('d') }}</p>
                            <p class="mt-1 text-[10px] font-semibold uppercase tracking-wide text-slate-400">{{ $tender->deadline->format('M') }}</p>
                        </div>
                        <div class="min-w-0 flex-1 border-l border-slate-200 pl-4">
                            <p class="truncate text-sm font-semibold text-slate-800 group-hover:text-blue-600">{{ $tender->name ?: $tender->tender_number }}</p>
                            <p class="mt-1 truncate text-xs text-slate-500">{{ $tender->client?->name ?? 'Klien belum diisi' }}</p>
                        </div>
                        <span class="shrink-0 text-[11px] font-semibold {{ $daysLeft <= 3 ? 'text-blue-600' : 'text-slate-400' }}">{{ $daysLeft === 0 ? 'Hari ini' : $daysLeft . ' hari' }}</span>
                    </a>
                @empty
                    <div class="border-b border-slate-100 py-10 text-center">
                        <i class="fa-regular fa-calendar text-xl text-slate-300"></i>
                        <p class="mt-3 text-sm font-medium text-slate-700">Belum ada deadline terdekat</p>
                        <p class="mt-1 text-xs text-slate-500">Tender aktif dalam 14 hari akan muncul di sini.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="border-l-0 border-slate-200 lg:border-l lg:pl-8">
            <div class="border-b border-slate-200 pb-4">
                <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400">Tiga arah kerja</p>
                <h3 class="mt-1 text-lg font-semibold tracking-tight text-slate-900">Unit bisnis</h3>
            </div>
            <div class="mt-2">
                <a href="{{ $isOwner ? route('laporan.index', ['domain' => 'tender']) : route('tender.index') }}" class="group flex items-center justify-between border-b border-slate-100 py-4">
                    <div><p class="text-sm font-semibold text-slate-800 group-hover:text-blue-600">Tender & Lelang</p><p class="mt-1 text-xs text-slate-500">{{ $tendersActive }} tender berjalan</p></div>
                    <i class="fa-solid fa-arrow-right text-xs text-slate-300 transition group-hover:translate-x-1 group-hover:text-blue-600"></i>
                </a>
                <a href="{{ $isOwner ? route('laporan.index', ['domain' => 'jasa']) : route('jasa.index') }}" class="group flex items-center justify-between border-b border-slate-100 py-4">
                    <div><p class="text-sm font-semibold text-slate-800 group-hover:text-blue-600">Layanan Jasa</p><p class="mt-1 text-xs text-slate-500">{{ $servicesActive }} pekerjaan aktif</p></div>
                    <i class="fa-solid fa-arrow-right text-xs text-slate-300 transition group-hover:translate-x-1 group-hover:text-blue-600"></i>
                </a>
                <a href="{{ $isOwner ? route('laporan.index', ['domain' => 'barang']) : route('sales.index') }}" class="group flex items-center justify-between border-b border-slate-100 py-4">
                    <div><p class="text-sm font-semibold text-slate-800 group-hover:text-blue-600">Dagang Barang</p><p class="mt-1 text-xs text-slate-500">{{ $totalSales }} transaksi penjualan</p></div>
                    <i class="fa-solid fa-arrow-right text-xs text-slate-300 transition group-hover:translate-x-1 group-hover:text-blue-600"></i>
                </a>
            </div>
            @if($isOwner)
                <a href="{{ route('laporan.index', ['domain' => 'semua']) }}" class="mt-5 inline-flex items-center text-xs font-semibold text-blue-600 hover:text-blue-800">Buka ringkasan bisnis <i class="fa-solid fa-arrow-right ml-2 text-[10px]"></i></a>
            @endif
        </div>
    </section>

    <section class="border-t border-slate-200 pt-6">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-xs text-slate-500">Stok minimum: <span class="font-semibold text-slate-800">{{ $lowStockProducts->count() }} produk perlu perhatian</span></p>
            <a href="{{ route('products.index') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-800">Buka inventaris <i class="fa-solid fa-arrow-right ml-1 text-[10px]"></i></a>
        </div>
    </section>
</div>
@endsection
