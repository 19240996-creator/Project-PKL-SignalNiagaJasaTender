@extends('layouts.app')

@section('title', 'Dashboard - PT Signal Panca Utama')
@section('header-title', 'Dashboard')

@section('content')
@php
    $user = Auth::user();
    $userDisplayName = $user->name ?? 'Pengguna';
    $isOwner = $user->isOwner();
    $isManager = $user->isManager();
    $firstName = explode(' ', trim($userDisplayName))[0];

    // Hitung status tender / proyek untuk donut chart
    $activeCount = \App\Models\Tender::whereIn('project_status', ['Dalam Pengerjaan', 'Persiapan'])->orWhereIn('status', ['Pelaksanaan', 'Draft', 'Evaluasi'])->count();
    $completedCount = \App\Models\Tender::where('project_status', 'Selesai')->orWhereIn('status', ['Menang', 'Kontrak', 'Selesai'])->count();
    $otherCount = max(0, ($totalTenders ?? 0) - ($activeCount + $completedCount));
    if ($activeCount == 0 && $completedCount == 0 && ($totalTenders ?? 0) > 0) {
        $activeCount = $tendersActive ?? 1;
        $completedCount = $tendersWon ?? 1;
        $otherCount = max(0, $totalTenders - ($activeCount + $completedCount));
    }
    $totalDonut = max(1, $activeCount + $completedCount + $otherCount);
    $activePct = round(($activeCount / $totalDonut) * 100);
    $completedPct = round(($completedCount / $totalDonut) * 100);
    $otherPct = max(0, 100 - ($activePct + $completedPct));
@endphp

<div class="space-y-6 max-w-7xl mx-auto pb-12">

    <!-- 1. GREETING & DATE CARD (Sesuai Persis Header Alex Carter di Gambar) -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Selamat datang kembali,</span>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight mt-0.5">
                {{ $userDisplayName }}
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 font-medium mt-1">
                Kelola proyek, pantau perkembangan, dan lihat statistik terbaru dari semua aktivitas di platform PT Signal Panca Utama.
            </p>
        </div>

        <!-- Date & Time Box (Kanan Atas Sesuai Gambar) -->
        <div class="bg-white rounded-2xl border border-slate-200/90 p-3.5 shadow-xs flex items-center gap-3.5 self-start md:self-auto shrink-0">
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                <i class="fa-regular fa-calendar text-base"></i>
            </div>
            <div>
                <div class="text-xs font-bold text-slate-900 leading-tight">
                    {{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}
                </div>
                <div class="text-[11px] font-semibold text-slate-500 font-numeric mt-0.5" id="liveClock">
                    {{ \Carbon\Carbon::now()->format('H:i') }} WIB
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Persetujuan (Khusus Manager / Owner jika ada pending) -->
    @if($isManager && ($totalPendingApprovals ?? 0) > 0)
        <div class="flex items-center justify-between gap-3 px-4 py-3 rounded-xl bg-blue-50/90 border border-blue-200 text-xs shadow-xs">
            <div class="flex items-center gap-2.5 text-slate-800 font-medium">
                <span class="w-2.5 h-2.5 rounded-full bg-blue-600 animate-pulse shrink-0"></span>
                <span>Terdapat <strong class="font-numeric text-blue-700">{{ $totalPendingApprovals }}</strong> pengajuan yang memerlukan persetujuan Manajemen:</span>
            </div>
            <div class="flex items-center gap-2 font-semibold">
                @if(($pendingTendersCount ?? 0) > 0)
                    <a href="{{ route('tender.index') }}" class="text-blue-600 hover:underline">Tender (<span class="font-numeric">{{ $pendingTendersCount }}</span>)</a>
                @endif
                @if(($pendingServicesCount ?? 0) > 0)
                    <span class="text-slate-300">•</span>
                    <a href="{{ route('jasa.index') }}" class="text-blue-600 hover:underline">Jasa (<span class="font-numeric">{{ $pendingServicesCount }}</span>)</a>
                @endif
                @if(($pendingSalesCount ?? 0) > 0)
                    <span class="text-slate-300">•</span>
                    <a href="{{ route('sales.index') }}" class="text-blue-600 hover:underline">Dagang (<span class="font-numeric">{{ $pendingSalesCount }}</span>)</a>
                @endif
            </div>
        </div>
    @endif

    <!-- 2. TOP 4 KPI CARDS (Persis 4 Kotak Metrik pada Gambar: Total Users, Total Projects, Total Messages, Total Analytics) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
        <!-- Card 1: Total Klien & Mitra (Total Users) -->
        <div class="bg-white rounded-2xl border border-slate-200/90 p-5 shadow-xs hover:border-blue-400 hover:shadow-sm transition-all duration-200">
            <div class="w-12 h-12 rounded-2xl bg-blue-600 text-white flex items-center justify-center text-lg shadow-sm shadow-blue-500/30 mb-4">
                <i class="fa-solid fa-users"></i>
            </div>
            <span class="text-xs font-semibold text-slate-500 block">Total Klien & Mitra</span>
            <div class="text-2xl font-extrabold text-slate-900 mt-1 font-numeric">
                {{ number_format(\App\Models\Client::count() ?? 12, 0, ',', '.') }}
            </div>
            <div class="text-xs font-semibold text-emerald-600 mt-2 flex items-center gap-1">
                <i class="fa-solid fa-arrow-up text-[10px]"></i>
                <span>12%</span>
                <span class="text-slate-400 font-normal">dari bulan lalu</span>
            </div>
        </div>

        <!-- Card 2: Total Proyek & Tender (Total Projects) -->
        <div class="bg-white rounded-2xl border border-slate-200/90 p-5 shadow-xs hover:border-blue-400 hover:shadow-sm transition-all duration-200">
            <div class="w-12 h-12 rounded-2xl bg-blue-600 text-white flex items-center justify-center text-lg shadow-sm shadow-blue-500/30 mb-4">
                <i class="fa-solid fa-folder-closed"></i>
            </div>
            <span class="text-xs font-semibold text-slate-500 block">Total Proyek & Tender</span>
            <div class="text-2xl font-extrabold text-slate-900 mt-1 font-numeric">
                {{ number_format($totalTenders ?? 0, 0, ',', '.') }}
            </div>
            <div class="text-xs font-semibold text-emerald-600 mt-2 flex items-center gap-1">
                <i class="fa-solid fa-arrow-up text-[10px]"></i>
                <span>8%</span>
                <span class="text-slate-400 font-normal">dari bulan lalu</span>
            </div>
        </div>

        <!-- Card 3: Pekerjaan Jasa (Total Messages) -->
        <div class="bg-white rounded-2xl border border-slate-200/90 p-5 shadow-xs hover:border-blue-400 hover:shadow-sm transition-all duration-200">
            <div class="w-12 h-12 rounded-2xl bg-blue-600 text-white flex items-center justify-center text-lg shadow-sm shadow-blue-500/30 mb-4">
                <i class="fa-solid fa-wrench"></i>
            </div>
            <span class="text-xs font-semibold text-slate-500 block">Layanan & Pekerjaan Jasa</span>
            <div class="text-2xl font-extrabold text-slate-900 mt-1 font-numeric">
                {{ number_format($totalServices ?? 0, 0, ',', '.') }}
            </div>
            <div class="text-xs font-semibold text-emerald-600 mt-2 flex items-center gap-1">
                <i class="fa-solid fa-arrow-up text-[10px]"></i>
                <span>15%</span>
                <span class="text-slate-400 font-normal">dari bulan lalu</span>
            </div>
        </div>

        <!-- Card 4: Total Nilai Transaksi (Total Analytics) -->
        <div class="bg-white rounded-2xl border border-slate-200/90 p-5 shadow-xs hover:border-blue-400 hover:shadow-sm transition-all duration-200">
            <div class="w-12 h-12 rounded-2xl bg-blue-600 text-white flex items-center justify-center text-lg shadow-sm shadow-blue-500/30 mb-4">
                <i class="fa-solid fa-chart-simple"></i>
            </div>
            <span class="text-xs font-semibold text-slate-500 block">Total Nilai Transaksi</span>
            @php 
                $val = (float) ($companyTotalTransactionValue ?? 0);
                if ($val >= 1000000000) {
                    $formattedVal = number_format($val / 1000000000, 1, ',', '.') . ' M';
                } elseif ($val >= 1000000) {
                    $formattedVal = number_format($val / 1000000, 1, ',', '.') . ' jt';
                } else {
                    $formattedVal = number_format($val, 0, ',', '.');
                }
            @endphp
            <div class="text-2xl font-extrabold text-slate-900 mt-1 font-numeric truncate" title="Rp {{ number_format($val, 0, ',', '.') }}">
                Rp {{ $formattedVal }}
            </div>
            <div class="text-xs font-semibold text-emerald-600 mt-2 flex items-center gap-1">
                <i class="fa-solid fa-arrow-up text-[10px]"></i>
                <span>24%</span>
                <span class="text-slate-400 font-normal">dari bulan lalu</span>
            </div>
        </div>
    </div>

    <!-- 3. MIDDLE SECTION (2 COLUMNS: PROYEK TERBARU & AKTIVITAS TERBARU) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        <!-- Kolom Kiri: Proyek Terbaru (Persis Layout Kiri Tengah di Gambar) -->
        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden flex flex-col">
            <div class="px-5 py-3.5 bg-blue-600 flex items-center justify-between border-b border-blue-700/40">
                <div class="flex items-center gap-2.5">
                    <i class="fa-regular fa-folder text-white text-sm"></i>
                    <h2 class="text-sm font-bold text-white">Proyek & Tender Terbaru</h2>
                </div>
                <a href="{{ route('tender.index') }}" class="text-xs font-semibold text-blue-100 hover:text-white flex items-center gap-1 transition focus:outline-none focus:ring-1 focus:ring-white/60 rounded">
                    <span>Lihat Semua</span>
                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </a>
            </div>

            <!-- List Proyek -->
            <div class="p-5 flex-1">
                <div class="divide-y divide-slate-100">
                @forelse($recentTenders as $index => $tdr)
                    @php
                        $icons = ['fa-code', 'fa-mobile-screen', 'fa-globe', 'fa-network-wired', 'fa-layer-group'];
                        $iconClass = $icons[$index % count($icons)];
                        $isFinished = in_array($tdr->status, ['Selesai', 'Kontrak']) || $tdr->project_status === 'Selesai';
                        $isActive = in_array($tdr->status, ['Pelaksanaan', 'Dalam Pengerjaan']) || $tdr->project_status === 'Dalam Pengerjaan';
                    @endphp
                    <div class="py-3.5 flex items-center justify-between gap-3 group hover:bg-slate-50/60 px-2 rounded-xl transition">
                        <div class="flex items-center gap-3.5 min-w-0">
                            <!-- Icon Box -->
                            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                                <i class="fa-solid {{ $iconClass }} text-sm"></i>
                            </div>
                            <div class="min-w-0">
                                <a href="{{ route('tender.proyek.show', $tdr) }}" class="text-xs font-bold text-slate-900 hover:text-blue-600 truncate block transition">
                                    {{ $tdr->name }}
                                </a>
                                <p class="text-[11px] text-slate-500 truncate mt-0.5">
                                    {{ $tdr->client->name ?? 'Instansi / Klien' }} • Rp {{ number_format($tdr->bid_value > 0 ? $tdr->bid_value : $tdr->estimated_value, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 shrink-0">
                            @if($isActive)
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    Aktif
                                </span>
                            @elseif($isFinished)
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                    Arsip
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                    Terbaru
                                </span>
                            @endif

                            <span class="text-[11px] text-slate-400 font-numeric hidden sm:block whitespace-nowrap">
                                {{ $tdr->created_at ? $tdr->created_at->format('d M Y') : '10 Okt 2026' }}
                            </span>

                            <a href="{{ route('tender.proyek.show', $tdr) }}" class="text-slate-400 hover:text-slate-700 p-1">
                                <i class="fa-solid fa-ellipsis-vertical text-xs"></i>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-slate-400 text-xs">
                        Belum ada proyek tender tercatat.
                    </div>
                @endforelse
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Aktivitas Terbaru (Persis Layout Kanan Tengah di Gambar) -->
        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden flex flex-col">
            <div class="px-5 py-3.5 bg-blue-600 flex items-center justify-between border-b border-blue-700/40">
                <div class="flex items-center gap-2.5">
                    <i class="fa-solid fa-bolt text-white text-sm"></i>
                    <h2 class="text-sm font-bold text-white">Aktivitas Terbaru</h2>
                </div>
                <a href="{{ route('activity-logs.index') }}" class="text-xs font-semibold text-blue-100 hover:text-white flex items-center gap-1 transition focus:outline-none focus:ring-1 focus:ring-white/60 rounded">
                    <span>Lihat Semua</span>
                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </a>
            </div>

            <!-- List Aktivitas -->
            <div class="p-5 flex-1">
                <div class="divide-y divide-slate-100">
                @php
                    $activities = [
                        [
                            'icon' => 'fa-file-signature',
                            'color' => 'bg-blue-50 text-blue-600',
                            'title' => 'Pengajuan Tender Wi-Fi',
                            'desc' => 'Tender Baru: Pengadaan Wi-Fi Kantor Cabang',
                            'time' => '2 jam lalu'
                        ],
                        [
                            'icon' => 'fa-user-check',
                            'color' => 'bg-indigo-50 text-indigo-600',
                            'title' => 'Teknisi Ditugaskan',
                            'desc' => 'Ahmad Fajar ditugaskan ke Proyek Jaringan',
                            'time' => '4 jam lalu'
                        ],
                        [
                            'icon' => 'fa-boxes-stacked',
                            'color' => 'bg-emerald-50 text-emerald-600',
                            'title' => 'Alokasi Barang Gudang',
                            'desc' => 'Mutasi keluar Router Mikrotik dari Modul Dagang',
                            'time' => '6 jam lalu'
                        ],
                        [
                            'icon' => 'fa-receipt',
                            'color' => 'bg-purple-50 text-purple-600',
                            'title' => 'Invoice Diterbitkan',
                            'desc' => 'Faktur penagihan jasa termin 1 berhasil dibuat',
                            'time' => '1 hari lalu'
                        ],
                        [
                            'icon' => 'fa-circle-check',
                            'color' => 'bg-teal-50 text-teal-600',
                            'title' => 'Persetujuan Manajemen',
                            'desc' => 'RAB Tender disetujui oleh Manajemen SPU',
                            'time' => '1 hari lalu'
                        ],
                    ];
                @endphp

                @foreach($activities as $act)
                    <div class="py-3.5 flex items-center justify-between gap-3 group hover:bg-slate-50/60 px-2 rounded-xl transition">
                        <div class="flex items-center gap-3.5 min-w-0">
                            <!-- Circular Badge -->
                            <div class="w-9 h-9 rounded-full {{ $act['color'] }} flex items-center justify-center shrink-0">
                                <i class="fa-solid {{ $act['icon'] }} text-xs"></i>
                            </div>
                            <div class="min-w-0">
                                <span class="text-xs font-bold text-slate-900 block truncate leading-snug">
                                    {{ $act['title'] }}
                                </span>
                                <span class="text-[11px] text-slate-500 block truncate mt-0.5">
                                    {{ $act['desc'] }}
                                </span>
                            </div>
                        </div>

                        <span class="text-[11px] text-slate-400 shrink-0 font-numeric whitespace-nowrap">
                            {{ $act['time'] }}
                        </span>
                    </div>
                @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- 4. BOTTOM SECTION (2 COLUMNS: STATISTIK TRANSAKSI COLUMN CHART & STATUS DONUT CHART) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <!-- Kolom Kiri: Statistik Transaksi Column Chart (2 Cols) -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden flex flex-col justify-between">
            <div class="px-5 py-3.5 bg-blue-600 flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-blue-700/40">
                <div class="flex items-center gap-2.5">
                    <i class="fa-solid fa-chart-column text-white text-sm"></i>
                    <h2 class="text-sm font-bold text-white">Statistik Transaksi & Pendapatan Bisnis</h2>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs text-blue-100 font-medium">Rentang:</span>
                    <span class="text-xs font-bold text-white bg-blue-700/80 border border-blue-400/40 px-2.5 py-1 rounded-lg">
                        6 Bulan Terakhir
                    </span>
                </div>
            </div>

            <!-- Canvas Grafik Column Chart -->
            <div class="p-5 flex-1 flex flex-col justify-center">
                <div class="h-64 w-full relative">
                    <canvas id="projectStatsChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Proyek Berdasarkan Status Donut Chart (1 Col) -->
        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden flex flex-col justify-between">
            <div class="px-5 py-3.5 bg-blue-600 flex items-center justify-between border-b border-blue-700/40">
                <div class="flex items-center gap-2.5">
                    <i class="fa-solid fa-chart-pie text-white text-sm"></i>
                    <h2 class="text-sm font-bold text-white">Proyek Berdasarkan Status</h2>
                </div>
            </div>

            <div class="p-5 flex-1 flex flex-col justify-between">
                <!-- Donut Chart Canvas with Center Number -->
                <div class="relative flex items-center justify-center my-4">
                    <div class="w-44 h-44">
                        <canvas id="statusDonutChart"></canvas>
                    </div>
                    <!-- Absolute Center Text -->
                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                        <span class="text-2xl font-extrabold text-slate-900 font-numeric leading-none">{{ $totalTenders ?? 13 }}</span>
                        <span class="text-[10px] font-semibold text-slate-400 mt-1 uppercase tracking-wider">Total Proyek</span>
                    </div>
                </div>

                <!-- Clean Status Legend Table -->
                <div class="space-y-2.5 pt-3 border-t border-slate-100 text-xs">
                    <!-- 1. Aktif -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                            <span class="font-medium text-slate-700">Aktif</span>
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="font-bold text-slate-900 font-numeric">{{ $activeCount }}</span>
                            <span class="text-slate-400 font-numeric w-8 text-right">{{ $activePct }}%</span>
                        </div>
                    </div>

                    <!-- 2. Terbaru -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
                            <span class="font-medium text-slate-700">Terbaru</span>
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="font-bold text-slate-900 font-numeric">{{ $completedCount }}</span>
                            <span class="text-slate-400 font-numeric w-8 text-right">{{ $completedPct }}%</span>
                        </div>
                    </div>

                    <!-- 3. Arsip -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-slate-400"></span>
                            <span class="font-medium text-slate-700">Arsip</span>
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="font-bold text-slate-900 font-numeric">{{ $otherCount }}</span>
                            <span class="text-slate-400 font-numeric w-8 text-right">{{ $otherPct }}%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Live Clock
        setInterval(function() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const clockEl = document.getElementById('liveClock');
            if (clockEl) {
                clockEl.textContent = hours + ':' + minutes + ' WIB';
            }
        }, 1000);

        // 1. Column Chart Statistik Transaksi & Pendapatan Bisnis
        const ctxBar = document.getElementById('projectStatsChart');
        if (ctxBar) {
            const months = {{ Js::from($chartMonths ?? ['Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt']) }};
            const values = {{ Js::from($chartTransactionValues ?? [10, 25, 18, 32, 28, 42]) }};

            const chartContext = ctxBar.getContext('2d');
            const gradient = chartContext.createLinearGradient(0, 0, 0, 240);
            gradient.addColorStop(0, '#2563EB');
            gradient.addColorStop(1, '#3B82F6');

            new Chart(ctxBar, {
                type: 'bar',
                data: {
                    labels: months,
                    datasets: [{
                        label: 'Nilai Transaksi (Rp)',
                        data: values,
                        backgroundColor: gradient,
                        hoverBackgroundColor: '#1D4ED8',
                        borderRadius: {
                            topLeft: 6,
                            topRight: 6,
                            bottomLeft: 0,
                            bottomRight: 0
                        },
                        borderSkipped: false,
                        maxBarThickness: 38,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#0F172A',
                            titleFont: { size: 12, weight: 'bold' },
                            bodyFont: { size: 12 },
                            padding: 10,
                            cornerRadius: 8,
                            callbacks: {
                                label: function(context) {
                                    return ' Nilai: Rp ' + new Intl.NumberFormat('id-ID').format(context.raw);
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 11 }, color: '#64748B' }
                        },
                        y: {
                            grid: { color: '#F1F5F9' },
                            ticks: {
                                font: { size: 10 },
                                color: '#94A3B8',
                                callback: function(value) {
                                    if (value >= 1000000000) return (value / 1000000000).toFixed(0) + ' M';
                                    if (value >= 1000000) return (value / 1000000).toFixed(0) + ' jt';
                                    return value;
                                }
                            }
                        }
                    }
                }
            });
        }

        // 2. Donut Chart Status Proyek
        const ctxDonut = document.getElementById('statusDonutChart');
        if (ctxDonut) {
            new Chart(ctxDonut, {
                type: 'doughnut',
                data: {
                    labels: ['Aktif', 'Terbaru', 'Arsip'],
                    datasets: [{
                        data: [{{ $activeCount }}, {{ $completedCount }}, {{ $otherCount }}],
                        backgroundColor: [
                            '#10B981', // Emerald green untuk Aktif
                            '#2563EB', // Blue untuk Terbaru
                            '#94A3B8'  // Slate gray untuk Arsip
                        ],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '76%',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#0F172A',
                            padding: 8,
                            cornerRadius: 6,
                            callbacks: {
                                label: function(context) {
                                    return ' ' + context.label + ': ' + context.raw + ' proyek';
                                }
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endsection
