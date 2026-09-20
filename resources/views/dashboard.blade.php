@extends('layouts.app')

@section('title', 'Dashboard Executive')
@section('header-title', 'Dashboard Performa Perusahaan')

@section('content')
<div class="space-y-6">

    @if($notifications->isNotEmpty())
        <div class="bg-white rounded-2xl p-5 border border-amber-200 shadow-sm">
            <div class="flex items-center gap-2 mb-3">
                <i class="fa-solid fa-bell text-amber-600"></i>
                <h3 class="font-bold text-slate-800">Notifikasi Terbaru</h3>
            </div>
            <div class="space-y-2">
                @foreach($notifications as $notification)
                    <div class="rounded-xl bg-amber-50 px-4 py-3 text-sm text-slate-700">
                        <div class="font-semibold">{{ $notification->data['title'] ?? 'Notifikasi' }}</div>
                        <div class="text-xs text-slate-600">{{ $notification->data['message'] ?? '' }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- KPI Cards Overview Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">

        <!-- Card 1: Tender Pipeline & Win Rate -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm relative overflow-hidden">
            <div class="flex justify-between items-start mb-3">
                <div>
                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Tender Aktif / Win Rate</span>
                    <h3 class="text-2xl font-bold text-slate-800 mt-1">{{ $tendersActive }} <span class="text-xs text-slate-500 font-normal">Aktif</span></h3>
                </div>
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                    <i class="fa-solid fa-trophy text-lg"></i>
                </div>
            </div>
            <div class="flex items-center gap-2 pt-2 border-t border-slate-100 text-xs">
                <span class="px-2 py-0.5 rounded-full font-bold bg-emerald-100 text-emerald-700">Win Rate {{ $winRate }}%</span>
                <span class="text-slate-500">{{ $tendersWon }} Menang dari {{ $tendersWon + $tendersLost }} Selesai</span>
            </div>
        </div>

        <!-- Card 2: Potensi Nilai Tender -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm relative overflow-hidden">
            <div class="flex justify-between items-start mb-3">
                <div>
                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Total Nilai Tender</span>
                    <h3 class="text-2xl font-bold text-slate-800 mt-1">Rp {{ number_format($totalTenderValue, 0, ',', '.') }}</h3>
                </div>
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
                    <i class="fa-solid fa-file-contract text-lg"></i>
                </div>
            </div>
            <div class="flex items-center gap-2 pt-2 border-t border-slate-100 text-xs text-slate-500">
                <i class="fa-solid fa-clock text-amber-500"></i>
                <span>{{ count($upcomingDeadlines) }} mendekati deadline</span>
            </div>
        </div>

        <!-- Card 3: Jasa & Kontrak -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm relative overflow-hidden">
            <div class="flex justify-between items-start mb-3">
                <div>
                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Kontrak Jasa & Revenue</span>
                    <h3 class="text-2xl font-bold text-slate-800 mt-1">Rp {{ number_format($serviceRevenue, 0, ',', '.') }}</h3>
                </div>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                    <i class="fa-solid fa-briefcase text-lg"></i>
                </div>
            </div>
            <div class="flex items-center gap-2 pt-2 border-t border-slate-100 text-xs text-slate-500">
                <span class="font-semibold text-emerald-600">{{ $activeContracts }} Kontrak Aktif</span>
                <span>(Rp {{ number_format($totalContractValue, 0, ',', '.') }})</span>
            </div>
        </div>

        <!-- Card 4: Omzet Perdagangan & Piutang -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm relative overflow-hidden">
            <div class="flex justify-between items-start mb-3">
                <div>
                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Omzet Perdagangan</span>
                    <h3 class="text-2xl font-bold text-slate-800 mt-1">Rp {{ number_format($tradeRevenue, 0, ',', '.') }}</h3>
                </div>
                <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold">
                    <i class="fa-solid fa-cart-shopping text-lg"></i>
                </div>
            </div>
            <div class="flex items-center gap-2 pt-2 border-t border-slate-100 text-xs text-slate-500">
                <span class="font-semibold text-rose-600">Piutang: Rp {{ number_format($totalOutstandingPiutang, 0, ',', '.') }}</span>
            </div>
        </div>

    </div>

    <!-- Alert Section: Low Stock Warning & Deadlines -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Low Stock Warning Widget -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm lg:col-span-2">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-rose-100 text-rose-600 flex items-center justify-center font-bold">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-800 text-base">Peringatan Stok Menipis</h4>
                        <p class="text-xs text-slate-500">Produk yang berada di bawah stok minimum</p>
                    </div>
                </div>
                <a href="{{ route('products.index') }}" class="text-xs font-semibold text-blue-600 hover:underline">Kelola Stok &rarr;</a>
            </div>

            @if(count($lowStockProducts) > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-200">
                                <th class="p-2.5">SKU</th>
                                <th class="p-2.5">Nama Produk</th>
                                <th class="p-2.5">Stok Saat Ini</th>
                                <th class="p-2.5">Stok Minimum</th>
                                <th class="p-2.5">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($lowStockProducts as $p)
                                <tr>
                                    <td class="p-2.5 font-mono text-slate-600">{{ $p->sku }}</td>
                                    <td class="p-2.5 font-medium text-slate-800">{{ $p->name }}</td>
                                    <td class="p-2.5 font-bold text-rose-600">{{ number_format($p->stock, 0) }} {{ $p->unit }}</td>
                                    <td class="p-2.5 text-slate-500">{{ number_format($p->minimum_stock, 0) }} {{ $p->unit }}</td>
                                    <td class="p-2.5">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-700">Restock Restock</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-6 text-center text-slate-500 text-xs bg-slate-50 rounded-xl border border-dashed border-slate-200">
                    <i class="fa-solid fa-circle-check text-emerald-500 text-xl mb-1 block"></i>
                    Semua stok persediaan produk dalam tingkat aman.
                </div>
            @endif
        </div>

        <!-- Tender Deadline Reminder -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center font-bold">
                    <i class="fa-solid fa-clock"></i>
                </div>
                <div>
                    <h4 class="font-bold text-slate-800 text-base">Deadline Terdekat</h4>
                    <p class="text-xs text-slate-500">Tender mendekati batas waktu</p>
                </div>
            </div>

            <div class="space-y-3">
                @forelse($upcomingDeadlines as $t)
                    <div class="p-3 rounded-xl border border-amber-200 bg-amber-50/50 flex justify-between items-center">
                        <div>
                            <span class="text-[10px] font-mono font-semibold text-amber-800 block">{{ $t->tender_number }}</span>
                            <span class="text-xs font-bold text-slate-800 block truncate max-w-[180px]">{{ $t->name }}</span>
                            <span class="text-[10px] text-slate-500">{{ $t->client->name ?? 'N/A' }}</span>
                        </div>
                        <div class="text-right">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-200 text-amber-900 block mb-1">
                                {{ \Carbon\Carbon::parse($t->deadline)->format('d M Y') }}
                            </span>
                            <span class="text-[10px] text-amber-700 font-medium">
                                {{ \Carbon\Carbon::parse($t->deadline)->diffForHumans() }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-slate-400 text-xs">
                        Tidak ada tender mendekati deadline.
                    </div>
                @endforelse
            </div>
        </div>

    </div>

    <!-- Charts & Activity Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Chart 1: Revenue Comparison -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
            <h4 class="font-bold text-slate-800 text-base mb-4">Performa Pendapatan Perusahaan</h4>
            <div class="h-64">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <!-- Chart 2: Tender Pipeline Status -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
            <h4 class="font-bold text-slate-800 text-base mb-4">Distribusi Status Tender</h4>
            <div class="h-64 flex items-center justify-center">
                <canvas id="tenderStatusChart"></canvas>
            </div>
        </div>

    </div>

</div>
@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Bar Chart Revenue
        const ctxRev = document.getElementById('revenueChart').getContext('2d');
        new Chart(ctxRev, {
            type: 'bar',
            data: {
                labels: ['Pendapatan Jasa', 'Omzet Perdagangan', 'Total Biaya Pengadaan'],
                datasets: [{
                    label: 'Nilai (Rp)',
                    data: [{{ $serviceRevenue }}, {{ $tradeRevenue }}, {{ $totalProcurementCost }}],
                    backgroundColor: ['#10B981', '#2563EB', '#F59E0B'],
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } }
            }
        });

        // Donut Chart Tender
        const ctxTdr = document.getElementById('tenderStatusChart').getContext('2d');
        new Chart(ctxTdr, {
            type: 'doughnut',
            data: {
                labels: ['Aktif', 'Menang', 'Kalah'],
                datasets: [{
                    data: [{{ $tendersActive }}, {{ $tendersWon }}, {{ $tendersLost }}],
                    backgroundColor: ['#3B82F6', '#10B981', '#EF4444']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } }
            }
        });
    });
</script>
@endsection