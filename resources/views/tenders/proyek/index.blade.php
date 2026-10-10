@extends('layouts.app')

@section('title', 'Lapangan / Proyek - PT Signal Panca Utama')
@section('header-title', 'Pelaksanaan Lapangan & Monitoring Proyek')

@section('content')
<div class="space-y-6">

    <!-- Overview Banner -->
    <div class="border border-slate-200 bg-white rounded-lg p-4 flex flex-col md:flex-row items-start md:items-center justify-between gap-4 shadow-xs">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                <i class="fa-solid fa-helmet-safety text-base"></i>
            </div>
            <div>
                <h2 class="text-sm font-bold text-slate-900 leading-tight">Pengendalian Proyek, Alokasi Gudang, & Teknisi Lapangan</h2>
                <p class="text-xs text-slate-500 mt-0.5">Memantau progres aktual pekerjaan, menghubungkan material dari Gudang Dagang, penugasan teknisi dari Modul Jasa, log kendala & solusi, hingga pemantauan vendor mitra.</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('tender.index') }}" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-md transition flex items-center gap-1.5">
                <i class="fa-solid fa-folder-tree text-[11px]"></i> Administrasi Tender
            </a>
            <a href="{{ route('tender.rab.index') }}" class="px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 font-semibold text-xs rounded-md transition flex items-center gap-1.5">
                <i class="fa-solid fa-calculator text-[11px]"></i> Estimasi RAB
            </a>
        </div>
    </div>

    <!-- Status KPI Proyek Lapangan -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
        <a href="{{ route('tender.proyek.index') }}" class="bg-white p-3.5 rounded-lg border border-slate-200 shadow-xs hover:border-blue-400 transition {{ !request('project_status') ? 'ring-2 ring-blue-500/20' : '' }}">
            <div class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Total Proyek</div>
            <div class="text-xl font-bold text-slate-900 mt-1 font-numeric">{{ $projectCounts['total'] ?? 0 }}</div>
            <div class="text-[10px] text-slate-400 mt-0.5">Tender siap & berjalan</div>
        </a>
        <a href="{{ route('tender.proyek.index', ['project_status' => 'Persiapan']) }}" class="bg-white p-3.5 rounded-lg border border-slate-200 shadow-xs hover:border-slate-400 transition {{ request('project_status') === 'Persiapan' ? 'ring-2 ring-slate-400' : '' }}">
            <div class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Tahap Persiapan</div>
            <div class="text-xl font-bold text-slate-700 mt-1 font-numeric">{{ $projectCounts['persiapan'] ?? 0 }}</div>
            <div class="text-[10px] text-slate-400 mt-0.5">Alokasi & penugasan</div>
        </a>
        <a href="{{ route('tender.proyek.index', ['project_status' => 'Dalam Pengerjaan']) }}" class="bg-white p-3.5 rounded-lg border border-slate-200 shadow-xs hover:border-blue-400 transition {{ request('project_status') === 'Dalam Pengerjaan' ? 'ring-2 ring-blue-400' : '' }}">
            <div class="text-[11px] font-semibold text-blue-600 uppercase tracking-wider">Dalam Pengerjaan</div>
            <div class="text-xl font-bold text-blue-700 mt-1 font-numeric">{{ $projectCounts['dalam_pengerjaan'] ?? 0 }}</div>
            <div class="text-[10px] text-blue-500 mt-0.5">Sedang dikerjakan</div>
        </a>
        <a href="{{ route('tender.proyek.index', ['project_status' => 'Kendala']) }}" class="bg-white p-3.5 rounded-lg border border-slate-200 shadow-xs hover:border-rose-400 transition {{ request('project_status') === 'Kendala' ? 'ring-2 ring-rose-400' : '' }}">
            <div class="text-[11px] font-semibold text-rose-600 uppercase tracking-wider">Ada Kendala</div>
            <div class="text-xl font-bold text-rose-700 mt-1 font-numeric">{{ $projectCounts['kendala'] ?? 0 }}</div>
            <div class="text-[10px] text-rose-500 mt-0.5">Perlu perhatian khusus</div>
        </a>
        <a href="{{ route('tender.proyek.index', ['project_status' => 'Selesai']) }}" class="bg-white p-3.5 rounded-lg border border-slate-200 shadow-xs hover:border-emerald-400 transition {{ request('project_status') === 'Selesai' ? 'ring-2 ring-emerald-400' : '' }}">
            <div class="text-[11px] font-semibold text-emerald-600 uppercase tracking-wider">Proyek Selesai</div>
            <div class="text-xl font-bold text-emerald-700 mt-1 font-numeric">{{ $projectCounts['selesai'] ?? 0 }}</div>
            <div class="text-[10px] text-emerald-600/80 mt-0.5">Selesai 100%</div>
        </a>
    </div>

    <!-- Filter & Action Bar -->
    <div class="bg-white rounded-lg p-3.5 border border-slate-200 shadow-xs flex flex-col md:flex-row justify-between items-center gap-3">
        <form method="GET" action="{{ route('tender.proyek.index') }}" class="flex flex-wrap items-center gap-2 w-full md:w-auto">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nomor tender / nama proyek / PIC / lokasi..." class="px-3 py-1.5 rounded-md border border-slate-200 text-xs w-64 outline-none focus:ring-2 focus:ring-blue-500">
            
            <select name="project_status" onchange="this.form.submit()" class="px-2.5 py-1.5 rounded-md border border-slate-200 text-xs font-medium text-slate-700 bg-white hover:bg-slate-50 outline-none">
                <option value="">Semua Status Proyek</option>
                <option value="Persiapan" {{ request('project_status') == 'Persiapan' ? 'selected' : '' }}>Persiapan</option>
                <option value="Dalam Pengerjaan" {{ request('project_status') == 'Dalam Pengerjaan' ? 'selected' : '' }}>Dalam Pengerjaan</option>
                <option value="Kendala" {{ request('project_status') == 'Kendala' ? 'selected' : '' }}>Kendala</option>
                <option value="Selesai" {{ request('project_status') == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                <option value="Dibatalkan" {{ request('project_status') == 'Dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
            </select>

            <select name="metode_penanganan" onchange="this.form.submit()" class="px-2.5 py-1.5 rounded-md border border-slate-200 text-xs font-medium text-slate-700 bg-white hover:bg-slate-50 outline-none">
                <option value="">Semua Pelaksanaan</option>
                <option value="internal" {{ request('metode_penanganan') == 'internal' ? 'selected' : '' }}>Internal PT SPU</option>
                <option value="vendor_relasi" {{ request('metode_penanganan') == 'vendor_relasi' ? 'selected' : '' }}>Vendor Relasi</option>
            </select>

            <button type="submit" class="px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 font-semibold text-xs rounded-md transition">
                Filter
            </button>
            @if(request()->hasAny(['search', 'project_status', 'metode_penanganan']))
                <a href="{{ route('tender.proyek.index') }}" class="px-2.5 py-1.5 text-xs text-slate-500 hover:text-slate-800 transition">Reset</a>
            @endif
        </form>

        <span class="text-xs text-slate-500">
            Klik kelola untuk memantau alokasi gudang, teknisi, dan kendala lapangan.
        </span>
    </div>

    <!-- Data Table Card Proyek Lapangan -->
    <div class="bg-white rounded-lg border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-600 font-semibold border-b border-slate-200 uppercase tracking-wider">
                        <th class="p-3">No. Tender & Nama Proyek</th>
                        <th class="p-3">Pelaksanaan</th>
                        <th class="p-3">Lokasi & PIC Lapangan</th>
                        <th class="p-3">Jadwal Pelaksanaan</th>
                        <th class="p-3">Progres Pekerjaan</th>
                        <th class="p-3">Status Proyek</th>
                        <th class="p-3">Biaya Aktual vs RAB</th>
                        <th class="p-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($tenders as $tender)
                        <tr class="hover:bg-slate-50/70 transition">
                            <!-- No Tender & Nama Proyek -->
                            <td class="p-3 min-w-[200px]">
                                <span class="font-mono font-medium text-slate-900 block">{{ $tender->tender_number }}</span>
                                <div class="font-semibold text-slate-800 leading-snug mt-0.5">{{ $tender->name }}</div>
                                <div class="text-[11px] text-slate-500 flex items-center gap-1 mt-0.5">
                                    <i class="fa-regular fa-building text-[10px] text-slate-400"></i>
                                    <span>{{ $tender->client->name ?? 'Instansi N/A' }}</span>
                                </div>
                            </td>

                            <!-- Pelaksanaan (Internal vs Vendor) -->
                            <td class="p-3 whitespace-nowrap">
                                @if($tender->metode_penanganan === 'vendor_relasi')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                                        <i class="fa-solid fa-handshake text-[10px]"></i> Vendor: {{ $tender->nama_vendor_relasi ?: 'Relasi Eksternal' }}
                                    </span>
                                    <div class="text-[10px] text-amber-700 mt-1">Progres Vendor: {{ $tender->vendor_progress ?? 0 }}%</div>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-semibold bg-blue-50 text-blue-800 border border-blue-200">
                                        <i class="fa-solid fa-building-user text-[10px]"></i> Internal PT SPU
                                    </span>
                                    <div class="text-[10px] text-slate-500 mt-1">{{ $tender->assignments->count() }} Teknisi ditugaskan</div>
                                @endif
                            </td>

                            <!-- Lokasi & PIC -->
                            <td class="p-3 whitespace-nowrap">
                                <div class="text-slate-800 font-medium">
                                    <i class="fa-solid fa-location-dot text-slate-400 text-[10px] mr-1"></i>
                                    {{ $tender->project_location ?: 'Lokasi belum diatur' }}
                                </div>
                                <div class="text-[11px] text-slate-500 mt-0.5">
                                    <i class="fa-solid fa-user-shield text-slate-400 text-[10px] mr-1"></i>
                                    PIC: {{ $tender->project_pic ?: 'Belum ditetapkan' }}
                                </div>
                            </td>

                            <!-- Jadwal -->
                            <td class="p-3 whitespace-nowrap">
                                @if($tender->project_start_date || $tender->project_end_date)
                                    <div class="font-medium text-slate-800">
                                        {{ $tender->project_start_date ? \Carbon\Carbon::parse($tender->project_start_date)->format('d M Y') : 'Mulai TBD' }}
                                    </div>
                                    <div class="text-[11px] text-slate-500">
                                        s/d {{ $tender->project_end_date ? \Carbon\Carbon::parse($tender->project_end_date)->format('d M Y') : 'Selesai TBD' }}
                                    </div>
                                @else
                                    <span class="text-slate-400 text-xs">- Jadwal belum diatur -</span>
                                @endif
                            </td>

                            <!-- Progres Pekerjaan Bar -->
                            <td class="p-3 whitespace-nowrap min-w-[150px]">
                                <div class="flex items-center justify-between text-xs mb-1 font-numeric">
                                    <span class="font-bold text-slate-800">{{ $tender->project_progress ?? 0 }}%</span>
                                    <span class="text-[10px] text-slate-400">{{ $tender->projectLogs->count() }} log aktivitas</span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden border border-slate-200">
                                    <div class="h-full rounded-full transition-all duration-300 {{ ($tender->project_progress ?? 0) >= 100 ? 'bg-emerald-500' : (($tender->project_progress ?? 0) >= 50 ? 'bg-blue-600' : 'bg-amber-500') }}"
                                         style="width: {{ min(100, $tender->project_progress ?? 0) }}%"></div>
                                </div>
                            </td>

                            <!-- Status Proyek -->
                            <td class="p-3 whitespace-nowrap">
                                @php
                                    $pStatus = $tender->project_status ?: 'Persiapan';
                                @endphp
                                @if($pStatus === 'Persiapan')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Persiapan
                                    </span>
                                @elseif($pStatus === 'Dalam Pengerjaan')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-semibold bg-blue-50 text-blue-800 border border-blue-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span> Dalam Pengerjaan
                                    </span>
                                @elseif($pStatus === 'Kendala')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-semibold bg-rose-50 text-rose-800 border border-rose-200 animate-pulse">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Kendala
                                    </span>
                                @elseif($pStatus === 'Selesai')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Selesai
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-semibold bg-slate-100 text-slate-500 border border-slate-200">
                                        Dibatalkan
                                    </span>
                                @endif
                            </td>

                            <!-- Biaya Aktual vs RAB -->
                            <td class="p-3 whitespace-nowrap font-numeric">
                                <div class="font-bold text-slate-900">Rp {{ number_format($tender->actual_cost ?? 0, 0, ',', '.') }}</div>
                                <div class="text-[10px] text-slate-400">RAB: Rp {{ number_format($tender->total_rab_cost, 0, ',', '.') }}</div>
                            </td>

                            <!-- Aksi -->
                            <td class="p-3 text-center whitespace-nowrap">
                                <a href="{{ route('tender.proyek.show', $tender) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs rounded-md shadow-xs transition">
                                    <i class="fa-solid fa-chart-line text-[10px]"></i> Monitoring Proyek
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-slate-400 text-sm">
                                <div class="flex flex-col items-center justify-center space-y-2">
                                    <i class="fa-solid fa-helmet-safety text-2xl text-slate-300"></i>
                                    <span>Belum ada proyek lapangan yang berjalan. Proyek muncul setelah tender berstatus disetujui, menang, atau kontrak.</span>
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
