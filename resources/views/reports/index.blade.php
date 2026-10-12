@extends('layouts.app')

@section('title', 'Laporan Bisnis')
@section('header-title', 'Laporan Bisnis')

@section('content')
@php
    $user = Auth::user();
    $isOwner = $user->isOwner();
    $isManager = $user->isManager();
    $isAdmin = $user->isAdmin();
@endphp

<div class="space-y-5">

    <!-- 1. Tab Seleksi Unit Bisnis (Corporate Blue & White Tabs) -->
    <div class="border-b border-slate-200 flex flex-wrap gap-6 text-xs md:text-sm font-medium print:hidden">
        <a href="{{ route('laporan.index', array_merge(request()->query(), ['domain' => 'tender'])) }}"
           class="pb-3 px-1 border-b-2 transition-colors flex items-center gap-2 {{ $domain === 'tender' ? 'border-blue-600 text-blue-600 font-semibold' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' }}">
            <i class="fa-solid fa-file-contract text-xs"></i>
            <span>Bisnis Tender</span>
        </a>

        <a href="{{ route('laporan.index', array_merge(request()->query(), ['domain' => 'jasa'])) }}"
           class="pb-3 px-1 border-b-2 transition-colors flex items-center gap-2 {{ $domain === 'jasa' ? 'border-emerald-600 text-emerald-600 font-semibold' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' }}">
            <i class="fa-solid fa-wrench text-xs"></i>
            <span>Bisnis Jasa</span>
        </a>

        <a href="{{ route('laporan.index', array_merge(request()->query(), ['domain' => 'barang'])) }}"
           class="pb-3 px-1 border-b-2 transition-colors flex items-center gap-2 {{ $domain === 'barang' ? 'border-purple-600 text-purple-600 font-semibold' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' }}">
            <i class="fa-solid fa-boxes-stacked text-xs"></i>
            <span>Bisnis Dagang (Barang)</span>
        </a>

        <a href="{{ route('laporan.index', array_merge(request()->query(), ['domain' => 'semua'])) }}"
           class="pb-3 px-1 border-b-2 transition-colors flex items-center gap-2 {{ $domain === 'semua' ? 'border-slate-800 text-slate-900 font-semibold' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' }}">
            <i class="fa-solid fa-chart-pie text-xs"></i>
            <span>Ringkasan Tiga Bisnis</span>
        </a>
    </div>

    <!-- 2. Filter Toolbar (Responsif & Sesuai Token Design System) -->
    <div class="bg-white rounded-lg p-3 border border-slate-200/90 shadow-xs print:hidden"
         x-data="{
             periode: '{{ $periode }}',
             isCustom: {{ $periode === 'kustom' ? 'true' : 'false' }},
             onPeriodeChange(val) {
                 if (val === 'kustom') {
                     this.isCustom = true;
                 } else {
                     this.isCustom = false;
                     $nextTick(() => { $refs.filterForm.submit(); });
                 }
             }
         }">
        <form x-ref="filterForm" method="GET" action="{{ route('laporan.index') }}" class="flex flex-wrap items-center justify-between gap-3">
            <input type="hidden" name="domain" value="{{ $domain }}">

            <div class="flex flex-wrap items-center gap-2">
                <!-- Dropdown Periode (Per Hari, Per Minggu, Per Bulan, Per Tahun, dll) -->
                <div class="relative">
                    <select name="periode"
                            x-model="periode"
                            @change="onPeriodeChange($event.target.value)"
                            class="h-9 px-3 rounded-md border border-slate-200 text-xs font-medium text-slate-700 bg-white hover:bg-slate-50 outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100 transition cursor-pointer">
                        <option value="per_hari" {{ in_array($periode, ['per_hari', 'perhari', 'hari_ini']) ? 'selected' : '' }}>Per Hari</option>
                        <option value="per_minggu" {{ in_array($periode, ['per_minggu', 'perminggu', 'minggu_ini']) ? 'selected' : '' }}>Per Minggu</option>
                        <option value="per_bulan" {{ in_array($periode, ['per_bulan', 'perbulan', 'bulan_ini']) ? 'selected' : '' }}>Per Bulan</option>
                        <option value="per_tahun" {{ in_array($periode, ['per_tahun', 'pertahun', 'tahun_ini']) ? 'selected' : '' }}>Per Tahun</option>
                        <option value="semua" {{ $periode === 'semua' ? 'selected' : '' }}>Semua Periode</option>
                        <option value="kustom" {{ $periode === 'kustom' ? 'selected' : '' }}>Rentang Kustom</option>
                    </select>
                </div>

                <!-- Sub-Picker: Jika Per Hari, Input Tanggal Spesifik (Default Hari Ini) -->
                <div x-show="periode === 'per_hari'" x-cloak class="flex items-center gap-1.5">
                    <input type="date"
                           name="start_date"
                           value="{{ $startDate }}"
                           @change="$refs.filterForm.submit()"
                           title="Pilih tanggal per hari"
                           class="h-9 px-2.5 rounded-md border border-slate-200 text-xs text-slate-700 bg-white hover:bg-slate-50 outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100 transition cursor-pointer">
                </div>

                <!-- Sub-Picker: Jika Per Bulan, Input Bulan Spesifik (Default Bulan Ini) -->
                <div x-show="periode === 'per_bulan'" x-cloak class="flex items-center gap-1.5">
                    <input type="month"
                           name="bulan"
                           value="{{ substr($startDate, 0, 7) }}"
                           @change="$refs.filterForm.submit()"
                           title="Pilih bulan spesifik"
                           class="h-9 px-2.5 rounded-md border border-slate-200 text-xs text-slate-700 bg-white hover:bg-slate-50 outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100 transition cursor-pointer">
                </div>

                <!-- Sub-Picker: Jika Per Tahun, Input Tahun Spesifik (Default Tahun Ini) -->
                <div x-show="periode === 'per_tahun'" x-cloak class="flex items-center gap-1.5">
                    <select name="tahun"
                            @change="$refs.filterForm.submit()"
                            title="Pilih tahun spesifik"
                            class="h-9 px-2.5 rounded-md border border-slate-200 text-xs font-medium text-slate-700 bg-white hover:bg-slate-50 outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100 transition cursor-pointer">
                        @php
                            $selectedYear = (int) substr($startDate, 0, 4);
                            $currentYear = (int) date('Y');
                        @endphp
                        @for($y = $currentYear + 1; $y >= $currentYear - 4; $y--)
                            <option value="{{ $y }}" {{ $selectedYear === $y ? 'selected' : '' }}>Tahun {{ $y }}</option>
                        @endfor
                    </select>
                </div>

                <!-- Sub-Picker: Jika Rentang Kustom, Input Tanggal Mulai s/d Selesai -->
                <div x-show="isCustom" x-cloak class="flex items-center gap-1.5">
                    <input type="date"
                           name="custom_start_date"
                           value="{{ $startDate }}"
                           class="h-9 px-2.5 rounded-md border border-slate-200 text-xs text-slate-700 bg-white outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100 transition"
                           placeholder="Mulai">
                    <span class="text-xs text-slate-400 font-medium">s/d</span>
                    <input type="date"
                           name="custom_end_date"
                           value="{{ $endDate }}"
                           class="h-9 px-2.5 rounded-md border border-slate-200 text-xs text-slate-700 bg-white outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100 transition"
                           placeholder="Selesai">
                </div>

                <!-- Indikator Rentang Tanggal Aktif -->
                <div x-show="!isCustom" class="hidden lg:inline-flex items-center gap-1.5 px-2.5 h-9 rounded-md bg-slate-50 border border-slate-200 text-slate-600 text-xs font-medium">
                    <i class="fa-regular fa-calendar text-slate-400 text-xs"></i>
                    @if($periode === 'semua')
                        <span>Semua Riwayat Data</span>
                    @elseif($startDate === $endDate)
                        <span>{{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }}</span>
                    @else
                        <span>{{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</span>
                    @endif
                </div>

                <!-- Status Approval -->
                <select name="status"
                        @change="$refs.filterForm.submit()"
                        class="h-9 px-2.5 rounded-md border border-slate-200 text-xs font-medium text-slate-700 bg-white hover:bg-slate-50 outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100 transition cursor-pointer">
                    <option value="semua" {{ $status === 'semua' ? 'selected' : '' }}>Semua Status</option>
                    <option value="approved" {{ $status === 'approved' ? 'selected' : '' }}>Disetujui</option>
                    <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                </select>

                <!-- Pencarian Cepat -->
                <div class="relative">
                    <input type="text"
                           name="search"
                           value="{{ $search }}"
                           placeholder="Cari nomor / nama..."
                           class="h-9 pl-8 pr-3 rounded-md border border-slate-200 text-xs w-44 md:w-52 text-slate-800 placeholder:text-slate-400 bg-white outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100 transition">
                    <i class="fa-solid fa-magnifying-glass text-[11px] text-slate-400 absolute left-2.5 top-1/2 -translate-y-1/2"></i>
                </div>

                <!-- Tombol Submit Filter -->
                <button type="submit"
                        class="h-9 px-3.5 bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 text-xs font-semibold rounded-md transition flex items-center gap-1.5 cursor-pointer">
                    <i class="fa-solid fa-filter text-[11px]"></i>
                    <span>Filter</span>
                </button>

                @if(request()->hasAny(['search', 'status', 'start_date', 'custom_start_date', 'custom_end_date', 'bulan', 'tahun']) || !in_array($periode, ['per_bulan', 'bulan_ini']))
                    <a href="{{ route('laporan.index', ['domain' => $domain]) }}"
                       title="Reset filter"
                       class="h-9 px-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-medium rounded-md transition flex items-center gap-1">
                        <i class="fa-solid fa-rotate-left text-[11px]"></i>
                        <span class="hidden sm:inline">Reset</span>
                    </a>
                @endif
            </div>

            <!-- Tombol Cetak & Ekspor CSV -->
            <div class="flex items-center gap-2">
                <button type="button"
                        onclick="window.print()"
                        class="h-9 px-3 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 rounded-md text-xs font-medium transition flex items-center gap-1.5 cursor-pointer">
                    <i class="fa-solid fa-print text-xs text-slate-500"></i>
                    <span>Cetak Laporan</span>
                </button>
                <a href="{{ route('laporan.export', request()->query()) }}"
                   class="h-9 px-3.5 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-xs font-semibold transition flex items-center gap-1.5 shadow-xs">
                    <i class="fa-solid fa-download text-xs"></i>
                    <span>Ekspor CSV</span>
                </a>
            </div>
        </form>
    </div>

    <!-- 3. Kartu KPI Sesuai Domain Terpilih -->
    @if($domain === 'tender')
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
            <div class="bg-white p-5 rounded-2xl border-2 border-slate-200/90 shadow-sm hover:border-blue-400 hover:shadow transition duration-150">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 block">Total Proyek Tender</span>
                <div class="text-xl sm:text-2xl font-extrabold font-number text-slate-900 mt-2">{{ $kpi['total_tenders'] }} Proyek</div>
                <span class="text-xs text-slate-600 mt-1.5 block font-medium">Lelang terdaftar</span>
            </div>
            <div class="bg-white p-5 rounded-2xl border-2 border-slate-200/90 shadow-sm hover:border-blue-400 hover:shadow transition duration-150">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 block">Total Nilai Penawaran</span>
                <div class="text-xl sm:text-2xl font-extrabold font-number text-slate-900 mt-2 truncate" title="Rp {{ number_format($kpi['tenders_value'], 0, ',', '.') }}">Rp {{ number_format($kpi['tenders_value'], 0, ',', '.') }}</div>
                <span class="text-xs text-slate-600 mt-1.5 block font-medium">Akumulasi bidding</span>
            </div>
            <div class="bg-white p-5 rounded-2xl border-2 border-slate-200/90 shadow-sm hover:border-blue-400 hover:shadow transition duration-150">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 block">Tender Disetujui</span>
                <div class="text-xl sm:text-2xl font-extrabold font-number text-emerald-700 mt-2">{{ $tenders->where('approval_status', 'approved')->count() }}</div>
                <span class="text-xs text-emerald-700 mt-1.5 block font-semibold">Disetujui Manager</span>
            </div>
            <div class="bg-white p-5 rounded-2xl border-2 border-slate-200/90 shadow-sm hover:border-blue-400 hover:shadow transition duration-150">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 block">Tender Pending</span>
                <div class="text-xl sm:text-2xl font-extrabold font-number text-amber-700 mt-2">{{ $tenders->where('approval_status', 'pending')->count() }}</div>
                <span class="text-xs text-amber-700 mt-1.5 block font-semibold">Menunggu verifikasi</span>
            </div>
        </div>

    @elseif($domain === 'jasa')
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
            <div class="bg-white p-5 rounded-2xl border-2 border-slate-200/90 shadow-sm hover:border-blue-400 hover:shadow transition duration-150">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 block">Total Pekerjaan Jasa</span>
                <div class="text-xl sm:text-2xl font-extrabold font-number text-slate-900 mt-2">{{ $kpi['total_services'] }} Pekerjaan</div>
                <span class="text-xs text-slate-600 mt-1.5 block font-medium">Servis & maintenance</span>
            </div>
            <div class="bg-white p-5 rounded-2xl border-2 border-slate-200/90 shadow-sm hover:border-blue-400 hover:shadow transition duration-150">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 block">Total Biaya Layanan</span>
                <div class="text-xl sm:text-2xl font-extrabold font-number text-slate-900 mt-2 truncate" title="Rp {{ number_format($kpi['services_value'], 0, ',', '.') }}">Rp {{ number_format($kpi['services_value'], 0, ',', '.') }}</div>
                <span class="text-xs text-slate-600 mt-1.5 block font-medium">Biaya pekerjaan teknis</span>
            </div>
            <div class="bg-white p-5 rounded-2xl border-2 border-slate-200/90 shadow-sm hover:border-blue-400 hover:shadow transition duration-150">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 block">Pekerjaan Disetujui</span>
                <div class="text-xl sm:text-2xl font-extrabold font-number text-emerald-700 mt-2">{{ $services->where('approval_status', 'approved')->count() }}</div>
                <span class="text-xs text-emerald-700 mt-1.5 block font-semibold">Disetujui Manager</span>
            </div>
            <div class="bg-white p-5 rounded-2xl border-2 border-slate-200/90 shadow-sm hover:border-blue-400 hover:shadow transition duration-150">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 block">Pekerjaan Pending</span>
                <div class="text-xl sm:text-2xl font-extrabold font-number text-amber-700 mt-2">{{ $services->where('approval_status', 'pending')->count() }}</div>
                <span class="text-xs text-amber-700 mt-1.5 block font-semibold">Menunggu verifikasi</span>
            </div>
        </div>

    @elseif($domain === 'barang')
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
            <div class="bg-white p-5 rounded-2xl border-2 border-slate-200/90 shadow-sm hover:border-blue-400 hover:shadow transition duration-150">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 block">Total Transaksi Dagang</span>
                <div class="text-xl sm:text-2xl font-extrabold font-number text-slate-900 mt-2">{{ $kpi['total_sales'] }} Penjualan</div>
                <span class="text-xs text-slate-600 mt-1.5 block font-medium">Penjualan barang fisik</span>
            </div>
            <div class="bg-white p-5 rounded-2xl border-2 border-slate-200/90 shadow-sm hover:border-blue-400 hover:shadow transition duration-150">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 block">Total Omzet Penjualan</span>
                <div class="text-xl sm:text-2xl font-extrabold font-number text-slate-900 mt-2 truncate" title="Rp {{ number_format($kpi['sales_value'], 0, ',', '.') }}">Rp {{ number_format($kpi['sales_value'], 0, ',', '.') }}</div>
                <span class="text-xs text-slate-600 mt-1.5 block font-medium">Nilai transaksi barang</span>
            </div>
            <div class="bg-white p-5 rounded-2xl border-2 border-slate-200/90 shadow-sm hover:border-blue-400 hover:shadow transition duration-150">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 block">Transaksi Disetujui</span>
                <div class="text-xl sm:text-2xl font-extrabold font-number text-emerald-700 mt-2">{{ $sales->where('approval_status', 'approved')->count() }}</div>
                <span class="text-xs text-emerald-700 mt-1.5 block font-semibold">Disetujui Manager</span>
            </div>
            <div class="bg-white p-5 rounded-2xl border-2 border-slate-200/90 shadow-sm hover:border-blue-400 hover:shadow transition duration-150">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 block">Transaksi Pending</span>
                <div class="text-xl sm:text-2xl font-extrabold font-number text-amber-700 mt-2">{{ $sales->where('approval_status', 'pending')->count() }}</div>
                <span class="text-xs text-amber-700 mt-1.5 block font-semibold">Menunggu verifikasi</span>
            </div>
        </div>

    @else
        <!-- Ringkasan Perbandingan 3 Bisnis Mandiri -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-5">
            <!-- Bisnis 1: Tender -->
            <div class="bg-white p-5 rounded-2xl border-2 border-slate-200/90 shadow-sm hover:border-blue-400 hover:shadow transition duration-150 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Bisnis 1: Tender</span>
                        <div class="w-8 h-8 rounded-lg bg-blue-50 border border-blue-200 flex items-center justify-center text-blue-600 text-xs">
                            <i class="fa-solid fa-file-contract"></i>
                        </div>
                    </div>
                    <div class="text-xl sm:text-2xl font-extrabold font-number text-slate-900 mt-1">{{ $kpi['total_tenders'] }} Proyek</div>
                    <div class="text-sm font-bold font-number text-slate-700 mt-1.5">Rp {{ number_format($kpi['tenders_value'], 0, ',', '.') }}</div>
                    <p class="text-xs text-slate-500 mt-2 font-medium">Lelang pengadaan resmi (Internal & Vendor Relasi).</p>
                </div>
                <a href="{{ route('laporan.index', array_merge(request()->query(), ['domain' => 'tender'])) }}" class="mt-4 w-full py-2 bg-slate-50 hover:bg-slate-100 border border-slate-200 text-slate-800 font-bold text-xs rounded-xl text-center transition flex items-center justify-center gap-1.5">
                    <span>Rincian Laporan Tender</span>
                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </a>
            </div>

            <!-- Bisnis 2: Jasa -->
            <div class="bg-white p-5 rounded-2xl border-2 border-slate-200/90 shadow-sm hover:border-blue-400 hover:shadow transition duration-150 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Bisnis 2: Jasa</span>
                        <div class="w-8 h-8 rounded-lg bg-emerald-50 border border-emerald-200 flex items-center justify-center text-emerald-600 text-xs">
                            <i class="fa-solid fa-wrench"></i>
                        </div>
                    </div>
                    <div class="text-xl sm:text-2xl font-extrabold font-number text-slate-900 mt-1">{{ $kpi['total_services'] }} Pekerjaan</div>
                    <div class="text-sm font-bold font-number text-slate-700 mt-1.5">Rp {{ number_format($kpi['services_value'], 0, ',', '.') }}</div>
                    <p class="text-xs text-slate-500 mt-2 font-medium">Layanan teknis, pemeliharaan, dan instalasi.</p>
                </div>
                <a href="{{ route('laporan.index', array_merge(request()->query(), ['domain' => 'jasa'])) }}" class="mt-4 w-full py-2 bg-slate-50 hover:bg-slate-100 border border-slate-200 text-slate-800 font-bold text-xs rounded-xl text-center transition flex items-center justify-center gap-1.5">
                    <span>Rincian Laporan Jasa</span>
                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </a>
            </div>

            <!-- Bisnis 3: Dagang -->
            <div class="bg-white p-5 rounded-2xl border-2 border-slate-200/90 shadow-sm hover:border-blue-400 hover:shadow transition duration-150 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Bisnis 3: Dagang</span>
                        <div class="w-8 h-8 rounded-lg bg-purple-50 border border-purple-200 flex items-center justify-center text-purple-600 text-xs">
                            <i class="fa-solid fa-boxes-stacked"></i>
                        </div>
                    </div>
                    <div class="text-xl sm:text-2xl font-extrabold font-number text-slate-900 mt-1">{{ $kpi['total_sales'] }} Penjualan</div>
                    <div class="text-sm font-bold font-number text-slate-700 mt-1.5">Rp {{ number_format($kpi['sales_value'], 0, ',', '.') }}</div>
                    <p class="text-xs text-slate-500 mt-2 font-medium">Perdagangan produk fisik, percetakan, dan aksesoris.</p>
                </div>
                <a href="{{ route('laporan.index', array_merge(request()->query(), ['domain' => 'barang'])) }}" class="mt-4 w-full py-2 bg-slate-50 hover:bg-slate-100 border border-slate-200 text-slate-800 font-bold text-xs rounded-xl text-center transition flex items-center justify-center gap-1.5">
                    <span>Rincian Laporan Dagang</span>
                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </a>
            </div>
        </div>
    @endif

    <!-- 4. Tabel Data Sesuai Domain Terpilih -->
    <div class="bg-white rounded-lg border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-3.5 border-b border-slate-200 flex items-center justify-between bg-slate-50">
            <h3 class="font-semibold text-xs text-slate-800 uppercase tracking-wider flex items-center gap-2">
                @if($domain === 'tender')
                    <i class="fa-solid fa-file-contract text-slate-500"></i> Data Laporan Bisnis 1: Tender
                @elseif($domain === 'jasa')
                    <i class="fa-solid fa-wrench text-slate-500"></i> Data Laporan Bisnis 2: Jasa
                @elseif($domain === 'barang')
                    <i class="fa-solid fa-boxes-stacked text-slate-500"></i> Data Laporan Bisnis 3: Dagang (Barang)
                @else
                    <i class="fa-solid fa-chart-pie text-slate-500"></i> Ringkasan Nilai Finansial 3 Bisnis
                @endif
            </h3>
            <span class="text-xs text-slate-500">
                @if($domain === 'tender') {{ $tenders->count() }} data proyek
                @elseif($domain === 'jasa') {{ $services->count() }} data pekerjaan
                @elseif($domain === 'barang') {{ $sales->count() }} data transaksi
                @else 3 Bidang Usaha
                @endif
            </span>
        </div>

        <div class="overflow-x-auto">
            @if($domain === 'tender')
                <!-- TABEL TENDER -->
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-600 font-semibold border-b border-slate-200 uppercase tracking-wider">
                        <tr>
                            <th class="p-3">No. Tender</th>
                            <th class="p-3">Nama Tender</th>
                            <th class="p-3">Klien / Instansi</th>
                            <th class="p-3">Pengerjaan</th>
                            <th class="p-3 text-right">Nilai Kontrak/Bid</th>
                            <th class="p-3 text-center">Status Lelang</th>
                            <th class="p-3 text-center">Approval</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($tenders as $t)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="p-3 font-mono font-medium text-slate-900">{{ $t->tender_number }}</td>
                                <td class="p-3 font-medium text-slate-800">{{ $t->name }}</td>
                                <td class="p-3 text-slate-600">{{ $t->client->name ?? '-' }}</td>
                                <td class="p-3 text-slate-600">
                                    @if($t->metode_penanganan === 'vendor_relasi')
                                        <span>Vendor: {{ $t->nama_vendor_relasi ?: 'Mitra' }}</span>
                                    @else
                                        <span>Internal SPU</span>
                                    @endif
                                </td>
                                <td class="p-3 text-right font-mono font-medium text-slate-900">Rp {{ number_format($t->bid_value ?? $t->estimated_value, 0, ',', '.') }}</td>
                                <td class="p-3 text-center">
                                    <span class="px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 text-slate-700">{{ $t->status }}</span>
                                </td>
                                <td class="p-3 text-center">
                                    @if($t->approval_status === 'approved')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Disetujui
                                        </span>
                                    @elseif($t->approval_status === 'rejected')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-medium bg-rose-50 text-rose-700 border border-rose-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Ditolak
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-medium bg-amber-50 text-amber-700 border border-amber-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Pending
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="p-6 text-center text-slate-400">Tidak ada data tender pada periode ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>

            @elseif($domain === 'jasa')
                <!-- TABEL JASA -->
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-600 font-semibold border-b border-slate-200 uppercase tracking-wider">
                        <tr>
                            <th class="p-3">No. Job</th>
                            <th class="p-3">Nama Layanan Jasa</th>
                            <th class="p-3">Nama Klien</th>
                            <th class="p-3 text-right">Biaya Layanan (Rp)</th>
                            <th class="p-3 text-center">Pelaksanaan</th>
                            <th class="p-3 text-center">Approval</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($services as $j)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="p-3 font-mono font-medium text-slate-900">{{ $j->job_number }}</td>
                                <td class="p-3 font-medium text-slate-800">{{ $j->name }}</td>
                                <td class="p-3 text-slate-600">{{ $j->klien ?: ($j->contract?->client?->name ?: '-') }}</td>
                                <td class="p-3 text-right font-mono font-medium text-slate-900">Rp {{ number_format($j->biaya ?? ($j->contract?->contract_value ?? 0), 0, ',', '.') }}</td>
                                <td class="p-3 text-center">
                                    <span class="px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 text-slate-700">{{ $j->status }}</span>
                                </td>
                                <td class="p-3 text-center">
                                    @if($j->approval_status === 'approved')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Disetujui
                                        </span>
                                    @elseif($j->approval_status === 'rejected')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-medium bg-rose-50 text-rose-700 border border-rose-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Ditolak
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-medium bg-amber-50 text-amber-700 border border-amber-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Pending
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="p-6 text-center text-slate-400">Tidak ada data pekerjaan jasa pada periode ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>

            @elseif($domain === 'barang')
                <!-- TABEL DAGANG -->
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-600 font-semibold border-b border-slate-200 uppercase tracking-wider">
                        <tr>
                            <th class="p-3">No. Transaksi</th>
                            <th class="p-3">Nama Pelanggan</th>
                            <th class="p-3">Nama Barang</th>
                            <th class="p-3 text-right">Qty</th>
                            <th class="p-3 text-right">Harga Satuan</th>
                            <th class="p-3 text-right">Total Transaksi (Rp)</th>
                            <th class="p-3 text-center">Approval</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($sales as $s)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="p-3 font-mono font-medium text-slate-900">{{ $s->sale_number }}</td>
                                <td class="p-3 font-medium text-slate-800">{{ $s->customer_name }}</td>
                                <td class="p-3 text-slate-700">{{ $s->nama_barang ?: ($s->items->first()?->product?->name ?? 'Barang Dagang') }}</td>
                                <td class="p-3 text-right font-mono">{{ number_format($s->kuantitas ?? 1, 0, ',', '.') }} unit</td>
                                <td class="p-3 text-right font-mono text-slate-600">Rp {{ number_format($s->harga_satuan ?? ($s->total_amount / max(1, $s->kuantitas ?? 1)), 0, ',', '.') }}</td>
                                <td class="p-3 text-right font-mono font-medium text-slate-900">Rp {{ number_format($s->total_amount, 0, ',', '.') }}</td>
                                <td class="p-3 text-center">
                                    @if($s->approval_status === 'approved')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Disetujui
                                        </span>
                                    @elseif($s->approval_status === 'rejected')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-medium bg-rose-50 text-rose-700 border border-rose-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Ditolak
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-medium bg-amber-50 text-amber-700 border border-amber-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Pending
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="p-6 text-center text-slate-400">Tidak ada transaksi penjualan dagang pada periode ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>

            @else
                <!-- TABEL PERBANDINGAN TIGA BISNIS -->
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-600 font-semibold border-b border-slate-200 uppercase tracking-wider">
                        <tr>
                            <th class="p-3.5">Unit Bisnis</th>
                            <th class="p-3.5">Fokus Operasional</th>
                            <th class="p-3.5 text-center">Volume Aktivitas</th>
                            <th class="p-3.5 text-right">Total Nilai Finansial (Rp)</th>
                            <th class="p-3.5 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="p-3.5 font-semibold text-slate-900">
                                Bisnis 1: Tender
                            </td>
                            <td class="p-3.5 text-slate-600">Proyek pengadaan & lelang resmi (Internal & Vendor Relasi)</td>
                            <td class="p-3.5 text-center font-mono font-medium text-slate-800">{{ $kpi['total_tenders'] }} Proyek</td>
                            <td class="p-3.5 text-right font-mono font-semibold text-slate-900">Rp {{ number_format($kpi['tenders_value'], 0, ',', '.') }}</td>
                            <td class="p-3.5 text-center">
                                <a href="{{ route('laporan.index', array_merge(request()->query(), ['domain' => 'tender'])) }}" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded text-xs font-medium transition">
                                    Buka
                                </a>
                            </td>
                        </tr>
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="p-3.5 font-semibold text-slate-900">
                                Bisnis 2: Jasa
                            </td>
                            <td class="p-3.5 text-slate-600">Layanan jasa teknis, maintenance, dan instalasi perangkat</td>
                            <td class="p-3.5 text-center font-mono font-medium text-slate-800">{{ $kpi['total_services'] }} Pekerjaan</td>
                            <td class="p-3.5 text-right font-mono font-semibold text-slate-900">Rp {{ number_format($kpi['services_value'], 0, ',', '.') }}</td>
                            <td class="p-3.5 text-center">
                                <a href="{{ route('laporan.index', array_merge(request()->query(), ['domain' => 'jasa'])) }}" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded text-xs font-medium transition">
                                    Buka
                                </a>
                            </td>
                        </tr>
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="p-3.5 font-semibold text-slate-900">
                                Bisnis 3: Dagang (Barang)
                            </td>
                            <td class="p-3.5 text-slate-600">Perdagangan produk fisik, percetakan, dan aksesoris</td>
                            <td class="p-3.5 text-center font-mono font-medium text-slate-800">{{ $kpi['total_sales'] }} Penjualan</td>
                            <td class="p-3.5 text-right font-mono font-semibold text-slate-900">Rp {{ number_format($kpi['sales_value'], 0, ',', '.') }}</td>
                            <td class="p-3.5 text-center">
                                <a href="{{ route('laporan.index', array_merge(request()->query(), ['domain' => 'barang'])) }}" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded text-xs font-medium transition">
                                    Buka
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            @endif
        </div>
    </div>

</div>
@endsection
