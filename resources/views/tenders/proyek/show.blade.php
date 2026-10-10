@extends('layouts.app')

@section('title', 'Proyek Lapangan: ' . $tender->tender_number . ' - PT Signal Panca Utama')
@section('header-title', 'Detail Proyek & Pengendalian Lapangan')

@section('content')
<div class="space-y-6" x-data="{
    activeTab: 'logs',
    statusModal: false,
    vendorModal: false,
    addLogModal: false,
    allocateModal: false,
    useMaterialModal: false,
    assignmentModal: false,
    editAssignmentModal: false,
    photoModal: false,
    photoUrl: '',
    photoTitle: '',
    selectedRabItem: null,
    selectedAssignment: null,
    allocateQty: 1,
    usedQty: 1,
    usageNotes: '',
    openAllocate(item) {
        this.selectedRabItem = item;
        this.allocateQty = Math.max(1, Math.min(item.quantity - item.allocated_quantity, item.product ? item.product.stock : item.quantity));
        this.allocateModal = true;
    },
    openRecordUsage(item) {
        this.selectedRabItem = item;
        this.usedQty = Math.max(1, item.allocated_quantity - item.used_quantity);
        this.usageNotes = '';
        this.useMaterialModal = true;
    },
    openEditAssignment(assignment) {
        this.selectedAssignment = assignment;
        this.editAssignmentModal = true;
    },
    previewPhoto(url, title) {
        this.photoUrl = url;
        this.photoTitle = title;
        this.photoModal = true;
    }
}">

    <!-- Owner Read-Only Alert -->
    @if(auth()->user()->isOwner())
        <div class="border border-slate-200 bg-white rounded-lg p-3.5 flex flex-col md:flex-row items-start md:items-center justify-between gap-3 shadow-xs">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-md bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-eye text-sm"></i>
                </div>
                <div>
                    <span class="text-xs font-semibold text-slate-900 block leading-tight">Mode Pemantauan Eksekutif Proyek Lapangan (Read-Only)</span>
                    <p class="text-xs text-slate-500">Owner dapat memantau progres fisik, alokasi gudang, kehadiran tim teknisi, kendala operasional, dan realisasi biaya proyek.</p>
                </div>
            </div>
            <a href="{{ route('laporan.index', ['domain' => 'tender']) }}" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-xs font-semibold transition shadow-xs">
                Laporan Proyek
            </a>
        </div>
    @endif

    <!-- Breadcrumbs & Header Card -->
    <div class="bg-white rounded-lg p-4 border border-slate-200 shadow-xs flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('tender.index') }}" class="hover:text-blue-600 transition">Administrasi Tender</a>
                <span>/</span>
                <a href="{{ route('tender.proyek.index') }}" class="hover:text-blue-600 transition">Lapangan Proyek</a>
                <span>/</span>
                <span class="font-mono text-slate-800 font-semibold">{{ $tender->tender_number }}</span>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <h1 class="text-base font-bold text-slate-900 leading-tight">{{ $tender->name }}</h1>
                @if($tender->project_status === 'Selesai')
                    <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <i class="fa-solid fa-circle-check text-[10px] mr-1"></i>Selesai 100%
                    </span>
                @elseif($tender->project_status === 'Kendala')
                    <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                        <i class="fa-solid fa-triangle-exclamation text-[10px] mr-1"></i>Ada Kendala
                    </span>
                @elseif($tender->project_status === 'Dalam Pengerjaan')
                    <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                        <i class="fa-solid fa-spinner fa-spin text-[10px] mr-1"></i>Dalam Pengerjaan
                    </span>
                @else
                    <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                        <i class="fa-solid fa-clock text-[10px] mr-1"></i>Tahap Persiapan
                    </span>
                @endif
            </div>

            <div class="text-xs text-slate-500 mt-1 flex flex-wrap items-center gap-2">
                <span>Klien: <strong class="text-slate-700">{{ $tender->client->name ?? 'N/A' }}</strong></span>
                <span>•</span>
                <span>Lokasi: <strong class="text-slate-700">{{ $tender->project_location ?: 'Belum diatur' }}</strong></span>
                <span>•</span>
                <span>PIC: <strong class="text-slate-700">{{ $tender->project_pic ?: 'Belum ditetapkan' }}</strong></span>
                <span>•</span>
                <span>Jadwal: <strong class="text-slate-700">{{ $tender->project_start_date ? \Carbon\Carbon::parse($tender->project_start_date)->format('d/m/Y') : '-' }} s/d {{ $tender->project_end_date ? \Carbon\Carbon::parse($tender->project_end_date)->format('d/m/Y') : '-' }}</strong></span>
            </div>
        </div>

        <div class="flex items-center gap-2 w-full md:w-auto">
            <a href="{{ route('tender.rab.show', $tender) }}" class="px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 font-semibold text-xs rounded-md transition flex items-center gap-1.5">
                <i class="fa-solid fa-calculator text-[11px]"></i> Lihat RAB
            </a>

            @if(!auth()->user()->isOwner())
                <button type="button" @click="statusModal = true" class="px-3 py-1.5 bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs rounded-md transition flex items-center gap-1.5 shadow-xs">
                    <i class="fa-solid fa-pen-to-square text-[11px]"></i> Kelola Status & Jadwal
                </button>
            @endif
        </div>
    </div>

    <!-- Quick Metrics & Progress Tracker -->
    <div class="grid grid-cols-2 md:grid-cols-6 gap-3">
        <!-- 1. Progres Fisik -->
        <div class="bg-white p-3.5 rounded-lg border border-slate-200 shadow-xs">
            <div class="text-[11px] font-semibold text-blue-600 uppercase tracking-wider">Progres Pekerjaan</div>
            <div class="text-xl font-bold text-blue-700 mt-1 font-numeric">{{ $tender->project_progress ?? 0 }}%</div>
            <div class="w-full bg-slate-100 rounded-full h-1.5 mt-2 overflow-hidden">
                <div class="h-1.5 rounded-full {{ $tender->project_progress >= 100 ? 'bg-emerald-500' : ($tender->project_status === 'Kendala' ? 'bg-rose-500' : 'bg-blue-600') }}" style="width: {{ min(100, (int) $tender->project_progress) }}%"></div>
            </div>
        </div>

        <!-- 2. Status Operasional -->
        <div class="bg-white p-3.5 rounded-lg border border-slate-200 shadow-xs">
            <div class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Status Lapangan</div>
            <div class="text-base font-bold text-slate-800 mt-1 truncate">{{ $tender->project_status ?? 'Persiapan' }}</div>
            <div class="text-[10px] text-slate-400 mt-1">PIC: {{ $tender->project_pic ?: '-' }}</div>
        </div>

        <!-- 3. Pelaksanaan -->
        <div class="bg-white p-3.5 rounded-lg border border-slate-200 shadow-xs">
            <div class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Metode Pelaksanaan</div>
            @if($tender->metode_penanganan === 'vendor_relasi')
                <div class="text-sm font-bold text-amber-700 mt-1 truncate">Vendor Mitra</div>
                <div class="text-[10px] text-amber-600 mt-0.5 truncate">{{ $tender->nama_vendor_relasi ?: 'Relasi Eksternal' }}</div>
            @else
                <div class="text-sm font-bold text-blue-700 mt-1">Internal PT SPU</div>
                <div class="text-[10px] text-slate-400 mt-0.5">Dikerjakan mandiri</div>
            @endif
        </div>

        <!-- 4. Realisasi Biaya Aktual -->
        <div class="bg-white p-3.5 rounded-lg border border-slate-200 shadow-xs">
            <div class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Biaya Aktual Lapangan</div>
            <div class="text-base font-bold text-slate-900 mt-1 font-numeric">Rp {{ number_format($tender->actual_cost ?? 0, 0, ',', '.') }}</div>
            <div class="text-[10px] text-slate-400 mt-0.5">Total pengeluaran log</div>
        </div>

        <!-- 5. Anggaran RAB -->
        <div class="bg-white p-3.5 rounded-lg border border-slate-200 shadow-xs">
            <div class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Anggaran Modal RAB</div>
            <div class="text-base font-bold text-slate-800 mt-1 font-numeric">Rp {{ number_format($tender->total_rab_cost ?? 0, 0, ',', '.') }}</div>
            @php 
                $budget = (float) $tender->total_rab_cost;
                $spent = (float) ($tender->actual_cost ?? 0);
                $isOver = $budget > 0 && $spent > $budget;
            @endphp
            <div class="text-[10px] {{ $isOver ? 'text-rose-600 font-semibold' : 'text-emerald-600' }} mt-0.5">
                {{ $isOver ? 'Over budget Rp ' . number_format($spent - $budget, 0, ',', '.') : 'Sisa Rp ' . number_format(max(0, $budget - $spent), 0, ',', '.') }}
            </div>
        </div>

        <!-- 6. Tim & Teknisi -->
        <div class="bg-white p-3.5 rounded-lg border border-slate-200 shadow-xs">
            <div class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Tim Teknisi</div>
            <div class="text-xl font-bold text-slate-900 mt-1 font-numeric">{{ $tender->assignments->count() }} Orang</div>
            <div class="text-[10px] text-slate-400 mt-0.5">Modul Jasa terhubung</div>
        </div>
    </div>

    <!-- Vendor Progress Card (Conditional if vendor_relasi) -->
    @if($tender->metode_penanganan === 'vendor_relasi')
        <div class="bg-amber-50/70 border border-amber-200 rounded-lg p-4 shadow-xs">
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-handshake text-base"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-sm font-bold text-slate-900">Pemantauan Pekerjaan Vendor Mitra: {{ $tender->nama_vendor_relasi ?: 'Relasi Eksternal' }}</h2>
                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-200 text-amber-900">Eksternal</span>
                        </div>
                        <p class="text-xs text-slate-600 mt-0.5">
                            Progres saat ini: <strong class="text-amber-800">{{ $tender->vendor_progress ?? 0 }}%</strong>.
                            Catatan vendor: <span class="text-slate-700 italic">{{ $tender->vendor_notes ?: 'Belum ada catatan progres dari pihak vendor.' }}</span>
                        </p>
                    </div>
                </div>

                @if(!auth()->user()->isOwner())
                    <button type="button" @click="vendorModal = true" class="px-3 py-1.5 bg-amber-600 hover:bg-amber-700 text-white font-semibold text-xs rounded-md transition flex items-center gap-1.5 shadow-xs shrink-0">
                        <i class="fa-solid fa-pen-to-square text-[11px]"></i> Perbarui Progres Vendor
                    </button>
                @endif
            </div>

            <div class="w-full bg-amber-200/60 rounded-full h-2 mt-3 overflow-hidden">
                <div class="h-2 rounded-full bg-amber-600 transition-all duration-300" style="width: {{ min(100, (int) ($tender->vendor_progress ?? 0)) }}%"></div>
            </div>
        </div>
    @endif

    <!-- Navigation Tabs Proyek Lapangan -->
    <div class="border-b border-slate-200 bg-white rounded-t-lg px-4 pt-3 flex flex-wrap items-center justify-between gap-3 shadow-xs">
        <div class="flex items-center space-x-1 sm:space-x-4">
            <button type="button" @click="activeTab = 'logs'" :class="activeTab === 'logs' ? 'border-blue-600 text-blue-600 font-semibold' : 'border-transparent text-slate-500 hover:text-slate-700 font-medium'" class="pb-3 px-2 border-b-2 text-xs transition flex items-center gap-1.5">
                <i class="fa-solid fa-clipboard-list text-xs"></i>
                <span>Log & Kendala Lapangan</span>
                <span class="ml-1 px-1.5 py-0.2 rounded-full text-[10px] bg-slate-100 text-slate-700 font-numeric">{{ $tender->projectLogs->count() }}</span>
            </button>

            <button type="button" @click="activeTab = 'materials'" :class="activeTab === 'materials' ? 'border-blue-600 text-blue-600 font-semibold' : 'border-transparent text-slate-500 hover:text-slate-700 font-medium'" class="pb-3 px-2 border-b-2 text-xs transition flex items-center gap-1.5">
                <i class="fa-solid fa-boxes-stacked text-xs"></i>
                <span>Alokasi Gudang (Dagang)</span>
                @php $matCount = $tender->rabItems->where('category', 'barang')->count(); @endphp
                <span class="ml-1 px-1.5 py-0.2 rounded-full text-[10px] bg-slate-100 text-slate-700 font-numeric">{{ $matCount }}</span>
            </button>

            <button type="button" @click="activeTab = 'technicians'" :class="activeTab === 'technicians' ? 'border-blue-600 text-blue-600 font-semibold' : 'border-transparent text-slate-500 hover:text-slate-700 font-medium'" class="pb-3 px-2 border-b-2 text-xs transition flex items-center gap-1.5">
                <i class="fa-solid fa-users-gear text-xs"></i>
                <span>Teknisi & SDM (Jasa)</span>
                <span class="ml-1 px-1.5 py-0.2 rounded-full text-[10px] bg-slate-100 text-slate-700 font-numeric">{{ $tender->assignments->count() }}</span>
            </button>

            <button type="button" @click="activeTab = 'cost_analysis'" :class="activeTab === 'cost_analysis' ? 'border-blue-600 text-blue-600 font-semibold' : 'border-transparent text-slate-500 hover:text-slate-700 font-medium'" class="pb-3 px-2 border-b-2 text-xs transition flex items-center gap-1.5">
                <i class="fa-solid fa-chart-pie text-xs"></i>
                <span>Realisasi Biaya vs RAB</span>
            </button>
        </div>

        <div class="pb-3">
            @if(!auth()->user()->isOwner())
                <template x-if="activeTab === 'logs'">
                    <button type="button" @click="addLogModal = true" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs rounded-md transition flex items-center gap-1.5 shadow-xs">
                        <i class="fa-solid fa-plus text-[10px]"></i> Tambah Log & Bukti Foto
                    </button>
                </template>

                <template x-if="activeTab === 'technicians'">
                    <button type="button" @click="assignmentModal = true" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs rounded-md transition flex items-center gap-1.5 shadow-xs">
                        <i class="fa-solid fa-user-plus text-[10px]"></i> Tugaskan Teknisi
                    </button>
                </template>
            @endif
        </div>
    </div>

    <!-- TAB 1: LOG AKTIVITAS, KENDALA, DOKUMENTASI & BIAYA AKTUAL -->
    <div x-show="activeTab === 'logs'" class="space-y-4">
        @if($tender->projectLogs->isEmpty())
            <div class="bg-white rounded-lg border border-slate-200 p-8 text-center shadow-xs">
                <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                    <i class="fa-regular fa-calendar-xmark text-lg"></i>
                </div>
                <h3 class="text-sm font-bold text-slate-800">Belum Ada Catatan Log Harian</h3>
                <p class="text-xs text-slate-500 max-w-md mx-auto mt-1">
                    Dokumentasikan progres harian lapangan, kendala teknis yang dihadapi, solusi penanganan, serta bukti foto pekerjaan.
                </p>
                @if(!auth()->user()->isOwner())
                    <button type="button" @click="addLogModal = true" class="mt-4 px-3.5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-md shadow-xs transition">
                        <i class="fa-solid fa-plus text-[10px] mr-1"></i> Buat Log Perdana
                    </button>
                @endif
            </div>
        @else
            <div class="space-y-3">
                @foreach($tender->projectLogs->sortByDesc('log_date') as $log)
                    <div class="bg-white rounded-lg border border-slate-200 p-4 shadow-xs hover:border-slate-300 transition">
                        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-2 border-b border-slate-100 pb-3">
                            <div class="flex items-center gap-3">
                                <span class="px-2.5 py-1 rounded bg-blue-50 text-blue-700 font-semibold text-xs border border-blue-200 font-numeric">
                                    <i class="fa-regular fa-calendar text-[10px] mr-1"></i>{{ \Carbon\Carbon::parse($log->log_date)->format('d F Y') }}
                                </span>
                                <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 font-numeric">
                                    Progres: {{ $log->progress_percentage }}%
                                </span>
                                @if($log->actual_cost_spent > 0)
                                    <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-slate-100 text-slate-800 border border-slate-200 font-numeric">
                                        Biaya: Rp {{ number_format($log->actual_cost_spent, 0, ',', '.') }}
                                    </span>
                                @endif
                            </div>

                            <div class="flex items-center gap-2 text-xs text-slate-400">
                                <span>Dicatat oleh: <strong class="text-slate-600">{{ $log->logger->name ?? 'User Sistem' }}</strong></span>
                                @if(!auth()->user()->isOwner())
                                    <form method="POST" action="{{ route('tender.proyek.logs.destroy', [$tender, $log]) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus catatan log ini?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-500 hover:text-rose-700 ml-2" title="Hapus Log">
                                            <i class="fa-solid fa-trash-can text-xs"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>

                        <!-- Isi Deskripsi Aktivitas -->
                        <div class="mt-3 text-xs text-slate-800 leading-relaxed whitespace-pre-line">
                            {{ $log->activity_description }}
                        </div>

                        <!-- Kendala & Solusi (Jika ada) -->
                        @if($log->obstacles || $log->solutions)
                            <div class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-3 pt-3 border-t border-slate-100">
                                @if($log->obstacles)
                                    <div class="bg-rose-50/70 border border-rose-200 rounded p-2.5">
                                        <div class="text-[11px] font-bold text-rose-800 flex items-center gap-1.5">
                                            <i class="fa-solid fa-triangle-exclamation text-[10px]"></i> Kendala di Lapangan:
                                        </div>
                                        <div class="text-xs text-rose-900 mt-1 leading-normal">{{ $log->obstacles }}</div>
                                    </div>
                                @endif

                                @if($log->solutions)
                                    <div class="bg-emerald-50/70 border border-emerald-200 rounded p-2.5">
                                        <div class="text-[11px] font-bold text-emerald-800 flex items-center gap-1.5">
                                            <i class="fa-solid fa-circle-check text-[10px]"></i> Solusi & Tindak Lanjut:
                                        </div>
                                        <div class="text-xs text-emerald-900 mt-1 leading-normal">{{ $log->solutions }}</div>
                                    </div>
                                @endif
                            </div>
                        @endif

                        <!-- Lampiran Dokumentasi / Foto -->
                        @if($log->documentation_file)
                            <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-paperclip text-slate-400 text-xs"></i>
                                    <span class="text-xs text-slate-600 font-medium">Dokumentasi Bukti Lapangan</span>
                                </div>
                                @php 
                                    $fileExt = pathinfo($log->documentation_file, PATHINFO_EXTENSION);
                                    $isImage = in_array(strtolower($fileExt), ['jpg', 'jpeg', 'png', 'webp']);
                                    $fileUrl = asset('storage/' . $log->documentation_file);
                                @endphp
                                @if($isImage)
                                    <button type="button" @click="previewPhoto('{{ $fileUrl }}', 'Dokumentasi Tanggal {{ \Carbon\Carbon::parse($log->log_date)->format('d/m/Y') }}')" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded transition flex items-center gap-1">
                                        <i class="fa-regular fa-image text-[10px]"></i> Lihat Foto
                                    </button>
                                @else
                                    <a href="{{ $fileUrl }}" target="_blank" class="px-2.5 py-1 bg-blue-50 hover:bg-blue-100 text-blue-700 font-semibold text-xs rounded transition flex items-center gap-1">
                                        <i class="fa-solid fa-file-pdf text-[10px]"></i> Unduh Dokumen
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- TAB 2: ALOKASI & PENGGUNAAN BARANG GUDANG (MODUL DAGANG) -->
    <div x-show="activeTab === 'materials'" class="space-y-4">
        <!-- Notice integrasi Dagang -->
        <div class="bg-blue-50/70 border border-blue-200 rounded-lg p-3.5 flex items-start gap-3">
            <div class="w-8 h-8 rounded-md bg-blue-100 text-blue-700 flex items-center justify-center shrink-0 mt-0.5">
                <i class="fa-solid fa-boxes-stacked text-sm"></i>
            </div>
            <div>
                <h4 class="text-xs font-bold text-blue-900">Integrasi Otomatis Modul Dagang & Stok Gudang</h4>
                <p class="text-xs text-blue-800/90 mt-0.5 leading-relaxed">
                    Setiap barang yang dialokasikan dari gudang ke proyek ini akan secara otomatis memotong stok barang di Modul Dagang melalui pencatatan mutasi barang keluar (<em>StockMovement: OUT</em>).
                </p>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-slate-200 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-50 text-slate-600 font-semibold border-b border-slate-200 uppercase tracking-wider">
                            <th class="p-3">Nama Material / Barang</th>
                            <th class="p-3">Kebutuhan RAB</th>
                            <th class="p-3">Stok Gudang Dagang</th>
                            <th class="p-3">Teralokasi ke Proyek</th>
                            <th class="p-3">Terpakai Aktual</th>
                            <th class="p-3">Sisa di Lapangan</th>
                            <th class="p-3 text-center">Aksi Operasional</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @php $materialItems = $tender->rabItems->where('category', 'barang'); @endphp
                        @forelse($materialItems as $item)
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="p-3 min-w-[200px]">
                                    <div class="font-semibold text-slate-800">{{ $item->item_name }}</div>
                                    @if($item->product)
                                        <div class="text-[11px] text-blue-600 flex items-center gap-1 mt-0.5">
                                            <i class="fa-solid fa-barcode text-[10px]"></i>
                                            <span>SKU: {{ $item->product->sku ?? '-' }} | {{ $item->product->name }}</span>
                                        </div>
                                    @else
                                        <div class="text-[10px] text-amber-600 italic mt-0.5">
                                            Belum terhubung ke master produk
                                        </div>
                                    @endif
                                </td>

                                <td class="p-3 font-semibold text-slate-800 font-numeric whitespace-nowrap">
                                    {{ number_format($item->quantity, 0, ',', '.') }} {{ $item->unit }}
                                </td>

                                <td class="p-3 whitespace-nowrap font-numeric">
                                    @if($item->product)
                                        @php $availStock = (float) $item->product->stock; @endphp
                                        <span class="font-semibold {{ $availStock >= $item->quantity ? 'text-emerald-700' : 'text-rose-600' }}">
                                            {{ number_format($availStock, 0, ',', '.') }} {{ $item->unit }}
                                        </span>
                                        @if($availStock < $item->quantity)
                                            <span class="block text-[10px] text-rose-600 font-medium">Defisit: {{ number_format($item->quantity - $availStock, 0, ',', '.') }}</span>
                                        @endif
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>

                                <td class="p-3 font-semibold text-blue-700 font-numeric whitespace-nowrap">
                                    {{ number_format($item->allocated_quantity, 0, ',', '.') }} {{ $item->unit }}
                                </td>

                                <td class="p-3 font-semibold text-slate-700 font-numeric whitespace-nowrap">
                                    {{ number_format($item->used_quantity, 0, ',', '.') }} {{ $item->unit }}
                                </td>

                                <td class="p-3 font-semibold text-emerald-700 font-numeric whitespace-nowrap">
                                    {{ number_format($item->remaining_allocated, 0, ',', '.') }} {{ $item->unit }}
                                </td>

                                <td class="p-3 text-center whitespace-nowrap">
                                    @if(!auth()->user()->isOwner())
                                        <div class="inline-flex items-center gap-1.5">
                                            @if($item->product)
                                                <button type="button" @click="openAllocate({{ Js::from($item) }})" class="px-2.5 py-1 bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 font-semibold text-[11px] rounded transition flex items-center gap-1" title="Alokasikan barang dari gudang dagang">
                                                    <i class="fa-solid fa-dolly text-[10px]"></i> Alokasi Gudang
                                                </button>
                                            @endif

                                            @if($item->allocated_quantity > 0)
                                                <button type="button" @click="openRecordUsage({{ Js::from($item) }})" class="px-2.5 py-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 font-semibold text-[11px] rounded transition flex items-center gap-1" title="Catat penggunaan aktual material">
                                                    <i class="fa-solid fa-check text-[10px]"></i> Catat Terpakai
                                                </button>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-[11px] text-slate-400">Monitoring</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-8 text-center text-slate-400">
                                    <i class="fa-solid fa-box-open text-2xl mb-2 text-slate-300 block"></i>
                                    Tidak ada material / barang yang dicatat dalam RAB tender ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB 3: PENUGASAN TEKNISI & SDM (MODUL JASA) -->
    <div x-show="activeTab === 'technicians'" class="space-y-4">
        <!-- Notice integrasi Jasa -->
        <div class="bg-slate-50 border border-slate-200 rounded-lg p-3.5 flex items-start gap-3">
            <div class="w-8 h-8 rounded-md bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 mt-0.5">
                <i class="fa-solid fa-user-gear text-sm"></i>
            </div>
            <div>
                <h4 class="text-xs font-bold text-slate-900">Penugasan Teknisi & Personil Modul Jasa</h4>
                <p class="text-xs text-slate-600 mt-0.5 leading-relaxed">
                    Pengelolaan teknisi lapangan terhubung dengan data kompetensi dari Modul Jasa (seperti teknisi jaringan Wi-Fi, installer fiber optic, atau teknisi konfigurasi).
                </p>
            </div>
        </div>

        @if($tender->assignments->isEmpty())
            <div class="bg-white rounded-lg border border-slate-200 p-8 text-center shadow-xs">
                <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                    <i class="fa-solid fa-users-slash text-lg"></i>
                </div>
                <h3 class="text-sm font-bold text-slate-800">Belum Ada Teknisi Ditugaskan</h3>
                <p class="text-xs text-slate-500 max-w-md mx-auto mt-1">
                    Tugaskan teknisi internal atau tenaga ahli bersertifikat untuk melaksanakan pekerjaan lapangan ini.
                </p>
                @if(!auth()->user()->isOwner())
                    <button type="button" @click="assignmentModal = true" class="mt-4 px-3.5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-md shadow-xs transition">
                        <i class="fa-solid fa-user-plus text-[10px] mr-1"></i> Tugaskan Teknisi Sekarang
                    </button>
                @endif
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                @foreach($tender->assignments as $assign)
                    <div class="bg-white rounded-lg border border-slate-200 p-4 shadow-xs flex flex-col justify-between hover:border-slate-300 transition">
                        <div>
                            <div class="flex items-start justify-between gap-2">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-9 h-9 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-xs uppercase">
                                        {{ substr($assign->technician_name, 0, 2) }}
                                    </div>
                                    <div>
                                        <h4 class="text-xs font-bold text-slate-900 leading-tight">{{ $assign->technician_name }}</h4>
                                        <div class="text-[11px] text-blue-600 font-medium mt-0.5">
                                            {{ $assign->role_or_competency }}
                                        </div>
                                    </div>
                                </div>

                                @if($assign->status === 'Selesai')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">Selesai</span>
                                @elseif($assign->status === 'Sedang Bekerja')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">Sedang Bekerja</span>
                                @elseif($assign->status === 'Digantikan')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-600 border border-slate-200">Digantikan</span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">Ditugaskan</span>
                                @endif
                            </div>

                            <div class="mt-3 space-y-1.5 text-xs text-slate-600 border-t border-slate-100 pt-2.5">
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-phone text-[10px] text-slate-400 w-4"></i>
                                    <span>{{ $assign->contact_phone ?: 'Kontak belum diisi' }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <i class="fa-regular fa-calendar text-[10px] text-slate-400 w-4"></i>
                                    <span>Periode: {{ $assign->start_date ? \Carbon\Carbon::parse($assign->start_date)->format('d/m/Y') : '-' }} s/d {{ $assign->end_date ? \Carbon\Carbon::parse($assign->end_date)->format('d/m/Y') : '-' }}</span>
                                </div>
                                @if($assign->notes)
                                    <div class="text-[11px] text-slate-500 bg-slate-50 p-2 rounded mt-2">
                                        {{ $assign->notes }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        @if(!auth()->user()->isOwner())
                            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
                                <button type="button" @click="openEditAssignment({{ Js::from($assign) }})" class="text-xs text-blue-600 hover:text-blue-800 font-semibold flex items-center gap-1">
                                    <i class="fa-solid fa-pen text-[10px]"></i> Edit Status
                                </button>

                                <form method="POST" action="{{ route('tender.proyek.assignments.destroy', [$tender, $assign]) }}" onsubmit="return confirm('Hapus penugasan teknisi ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs text-rose-500 hover:text-rose-700">
                                        <i class="fa-solid fa-trash-can text-[11px]"></i>
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- TAB 4: REALISASI BIAYA VS ANGGARAN RAB -->
    <div x-show="activeTab === 'cost_analysis'" class="space-y-4">
        <div class="bg-white rounded-lg border border-slate-200 shadow-xs p-4">
            <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider mb-3">Rekapitulasi Anggaran vs Pengeluaran Lapangan</h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-4">
                <div class="p-3 rounded-lg bg-slate-50 border border-slate-200">
                    <span class="text-[11px] font-semibold text-slate-500 block">Total Anggaran HPP RAB</span>
                    <span class="text-base font-bold text-slate-900 font-numeric">Rp {{ number_format($tender->total_rab_cost ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="p-3 rounded-lg bg-blue-50 border border-blue-200">
                    <span class="text-[11px] font-semibold text-blue-700 block">Total Realisasi Biaya Aktual</span>
                    <span class="text-base font-bold text-blue-900 font-numeric">Rp {{ number_format($tender->actual_cost ?? 0, 0, ',', '.') }}</span>
                </div>
                @php 
                    $diff = (float) $tender->total_rab_cost - (float) ($tender->actual_cost ?? 0);
                @endphp
                <div class="p-3 rounded-lg {{ $diff >= 0 ? 'bg-emerald-50 border border-emerald-200' : 'bg-rose-50 border border-rose-200' }}">
                    <span class="text-[11px] font-semibold {{ $diff >= 0 ? 'text-emerald-700' : 'text-rose-700' }} block">
                        {{ $diff >= 0 ? 'Sisa Anggaran Efisiensi' : 'Over Budget (Defisit)' }}
                    </span>
                    <span class="text-base font-bold {{ $diff >= 0 ? 'text-emerald-900' : 'text-rose-900' }} font-numeric">
                        Rp {{ number_format(abs($diff), 0, ',', '.') }}
                    </span>
                </div>
            </div>

            <!-- Tabel Detail Item Biaya RAB -->
            <div class="border border-slate-200 rounded-lg overflow-hidden">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-50 text-slate-600 font-semibold border-b border-slate-200 uppercase tracking-wider">
                            <th class="p-3">Kategori</th>
                            <th class="p-3">Item Kebutuhan</th>
                            <th class="p-3">Volume RAB</th>
                            <th class="p-3">Anggaran HPP Subtotal</th>
                            <th class="p-3">Penawaran Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($tender->rabItems as $item)
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="p-3">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold uppercase bg-slate-100 text-slate-700">
                                        {{ $item->category }}
                                    </span>
                                </td>
                                <td class="p-3 font-semibold text-slate-800">
                                    {{ $item->item_name }}
                                </td>
                                <td class="p-3 font-numeric text-slate-700 whitespace-nowrap">
                                    {{ number_format($item->quantity, 0, ',', '.') }} {{ $item->unit }}
                                </td>
                                <td class="p-3 font-semibold text-slate-900 font-numeric whitespace-nowrap">
                                    Rp {{ number_format($item->subtotal_cost, 0, ',', '.') }}
                                </td>
                                <td class="p-3 font-semibold text-blue-700 font-numeric whitespace-nowrap">
                                    Rp {{ number_format($item->subtotal_price, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-6 text-center text-slate-400">
                                    Item rincian RAB belum dimasukkan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- MODAL 1: KELOLA STATUS, PROGRES & JADWAL PROYEK -->
    <div x-show="statusModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4" style="display: none;">
        <div class="bg-white rounded-lg shadow-xl max-w-lg w-full border border-slate-200 overflow-hidden" @click.away="statusModal = false">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/70">
                <h3 class="text-sm font-bold text-slate-900">Kelola Status & Jadwal Lapangan</h3>
                <button type="button" @click="statusModal = false" class="text-slate-400 hover:text-slate-600 text-lg">×</button>
            </div>

            <form method="POST" action="{{ route('tender.proyek.status', $tender) }}" class="p-5 space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Status Proyek <span class="text-rose-500">*</span></label>
                        <select name="project_status" required class="w-full text-xs rounded-md border border-slate-200 px-3 py-2 outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                            <option value="Persiapan" {{ $tender->project_status === 'Persiapan' ? 'selected' : '' }}>Persiapan</option>
                            <option value="Dalam Pengerjaan" {{ $tender->project_status === 'Dalam Pengerjaan' ? 'selected' : '' }}>Dalam Pengerjaan</option>
                            <option value="Kendala" {{ $tender->project_status === 'Kendala' ? 'selected' : '' }}>Kendala</option>
                            <option value="Selesai" {{ $tender->project_status === 'Selesai' ? 'selected' : '' }}>Selesai</option>
                            <option value="Dibatalkan" {{ $tender->project_status === 'Dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Progres Fisik (%) <span class="text-rose-500">*</span></label>
                        <input type="number" min="0" max="100" name="project_progress" value="{{ $tender->project_progress ?? 0 }}" required class="w-full text-xs rounded-md border border-slate-200 px-3 py-2 outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">PIC Lapangan</label>
                        <input type="text" name="project_pic" value="{{ $tender->project_pic }}" placeholder="Nama penanggung jawab..." class="w-full text-xs rounded-md border border-slate-200 px-3 py-2 outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Lokasi Pengerjaan</label>
                        <input type="text" name="project_location" value="{{ $tender->project_location }}" placeholder="Contoh: Gedung A Lt. 2..." class="w-full text-xs rounded-md border border-slate-200 px-3 py-2 outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Mulai Pelaksanaan</label>
                        <input type="date" name="project_start_date" value="{{ $tender->project_start_date ? \Carbon\Carbon::parse($tender->project_start_date)->format('Y-m-d') : '' }}" class="w-full text-xs rounded-md border border-slate-200 px-3 py-2 outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Target Selesai</label>
                        <input type="date" name="project_end_date" value="{{ $tender->project_end_date ? \Carbon\Carbon::parse($tender->project_end_date)->format('Y-m-d') : '' }}" class="w-full text-xs rounded-md border border-slate-200 px-3 py-2 outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button type="button" @click="statusModal = false" class="px-3 py-1.5 border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-medium rounded-md transition">Batal</button>
                    <button type="submit" class="px-3.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-md shadow-xs transition">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 2: PEMANTAUAN PROGRES VENDOR MITRA -->
    @if($tender->metode_penanganan === 'vendor_relasi')
        <div x-show="vendorModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4" style="display: none;">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full border border-slate-200 overflow-hidden" @click.away="vendorModal = false">
                <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-amber-50">
                    <h3 class="text-sm font-bold text-slate-900">Perbarui Progres Vendor Mitra</h3>
                    <button type="button" @click="vendorModal = false" class="text-slate-400 hover:text-slate-600 text-lg">×</button>
                </div>

                <form method="POST" action="{{ route('tender.proyek.vendor-progress', $tender) }}" class="p-5 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Vendor: <strong class="text-slate-900">{{ $tender->nama_vendor_relasi ?: 'Relasi Eksternal' }}</strong></label>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Progres Vendor (%) <span class="text-rose-500">*</span></label>
                        <input type="number" min="0" max="100" name="vendor_progress" value="{{ $tender->vendor_progress ?? 0 }}" required class="w-full text-xs rounded-md border border-slate-200 px-3 py-2 outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan Koordinasi & Status Vendor</label>
                        <textarea name="vendor_notes" rows="3" placeholder="Informasi kemajuan atau kendala dari vendor..." class="w-full text-xs rounded-md border border-slate-200 px-3 py-2 outline-none focus:ring-2 focus:ring-blue-500">{{ $tender->vendor_notes }}</textarea>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                        <button type="button" @click="vendorModal = false" class="px-3 py-1.5 border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-medium rounded-md transition">Batal</button>
                        <button type="submit" class="px-3.5 py-1.5 bg-amber-600 hover:bg-amber-700 text-white text-xs font-semibold rounded-md shadow-xs transition">Simpan Progres Vendor</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- MODAL 3: TAMBAH LOG HARIAN, KENDALA, SOLUSI & BUKTI FOTO -->
    <div x-show="addLogModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4" style="display: none;">
        <div class="bg-white rounded-lg shadow-xl max-w-xl w-full border border-slate-200 overflow-hidden" @click.away="addLogModal = false">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/70">
                <h3 class="text-sm font-bold text-slate-900">Catat Log Harian & Dokumentasi Lapangan</h3>
                <button type="button" @click="addLogModal = false" class="text-slate-400 hover:text-slate-600 text-lg">×</button>
            </div>

            <form method="POST" action="{{ route('tender.proyek.logs.store', $tender) }}" enctype="multipart/form-data" class="p-5 space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Catatan <span class="text-rose-500">*</span></label>
                        <input type="date" name="log_date" value="{{ date('Y-m-d') }}" required class="w-full text-xs rounded-md border border-slate-200 px-3 py-2 outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Progres Pekerjaan (%) <span class="text-rose-500">*</span></label>
                        <input type="number" min="0" max="100" name="progress_percentage" value="{{ $tender->project_progress ?? 0 }}" required class="w-full text-xs rounded-md border border-slate-200 px-3 py-2 outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Deskripsi Kegiatan & Hasil Pekerjaan <span class="text-rose-500">*</span></label>
                    <textarea name="activity_description" rows="3" required placeholder="Jelaskan pekerjaan yang telah diselesaikan pada hari ini..." class="w-full text-xs rounded-md border border-slate-200 px-3 py-2 outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-rose-700 mb-1">Kendala / Masalah Lapangan (Opsional)</label>
                        <textarea name="obstacles" rows="2" placeholder="Tuliskan kendala teknis atau lapangan jika ada..." class="w-full text-xs rounded-md border border-rose-200 px-3 py-2 outline-none focus:ring-2 focus:ring-rose-500"></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-emerald-700 mb-1">Solusi / Rekomendasi Penanganan (Opsional)</label>
                        <textarea name="solutions" rows="2" placeholder="Solusi yang diterapkan atau langkah mitigasi..." class="w-full text-xs rounded-md border border-emerald-200 px-3 py-2 outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Biaya Terpakai (Rp) Opsional</label>
                        <input type="number" min="0" name="actual_cost_spent" value="0" placeholder="0" class="w-full text-xs rounded-md border border-slate-200 px-3 py-2 outline-none focus:ring-2 focus:ring-blue-500">
                        <span class="text-[10px] text-slate-400">Akan ditambahkan ke akumulasi biaya aktual proyek.</span>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Upload Foto / Dokumen Pendukung</label>
                        <input type="file" name="documentation_file" accept="image/*,.pdf" class="w-full text-xs rounded-md border border-slate-200 px-3 py-1.5 outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                        <span class="text-[10px] text-slate-400">JPG, PNG, atau PDF (Maks. 10MB)</span>
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button type="button" @click="addLogModal = false" class="px-3 py-1.5 border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-medium rounded-md transition">Batal</button>
                    <button type="submit" class="px-3.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-md shadow-xs transition">Simpan Log Lapangan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 4: ALOKASIKAN BARANG DARI GUDANG (MODUL DAGANG) -->
    <div x-show="allocateModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4" style="display: none;">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full border border-slate-200 overflow-hidden" @click.away="allocateModal = false">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-blue-50">
                <h3 class="text-sm font-bold text-slate-900">Alokasikan Barang dari Gudang Dagang</h3>
                <button type="button" @click="allocateModal = false" class="text-slate-400 hover:text-slate-600 text-lg">×</button>
            </div>

            <form method="POST" action="{{ route('tender.proyek.allocate-material', $tender) }}" class="p-5 space-y-4">
                @csrf
                <input type="hidden" name="rab_item_id" :value="selectedRabItem ? selectedRabItem.id : ''">

                <div class="bg-slate-50 p-3 rounded-md border border-slate-200 text-xs space-y-1">
                    <div>Barang: <strong class="text-slate-900" x-text="selectedRabItem ? selectedRabItem.item_name : ''"></strong></div>
                    <div>Kebutuhan RAB: <span class="font-semibold text-slate-700" x-text="selectedRabItem ? (selectedRabItem.quantity + ' ' + selectedRabItem.unit) : ''"></span></div>
                    <div>Stok Gudang Tersedia: <span class="font-semibold text-emerald-700" x-text="selectedRabItem && selectedRabItem.product ? (selectedRabItem.product.stock + ' ' + selectedRabItem.unit) : '0'"></span></div>
                    <div>Sudah Dialokasikan: <span class="font-semibold text-blue-700" x-text="selectedRabItem ? (selectedRabItem.allocated_quantity + ' ' + selectedRabItem.unit) : '0'"></span></div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Jumlah yang Akan Dialokasikan <span class="text-rose-500">*</span></label>
                    <input type="number" step="0.01" min="0.01" name="quantity" x-model="allocateQty" required class="w-full text-xs rounded-md border border-slate-200 px-3 py-2 outline-none focus:ring-2 focus:ring-blue-500">
                    <span class="text-[10px] text-slate-500 mt-1 block">Stok gudang akan otomatis dipotong sesuai kuantitas ini.</span>
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button type="button" @click="allocateModal = false" class="px-3 py-1.5 border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-medium rounded-md transition">Batal</button>
                    <button type="submit" class="px-3.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-md shadow-xs transition">Proses Alokasi Gudang</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 5: CATAT PENGGUNAAN MATERIAL AKTUAL -->
    <div x-show="useMaterialModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4" style="display: none;">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full border border-slate-200 overflow-hidden" @click.away="useMaterialModal = false">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-emerald-50">
                <h3 class="text-sm font-bold text-slate-900">Catat Penggunaan Aktual di Lapangan</h3>
                <button type="button" @click="useMaterialModal = false" class="text-slate-400 hover:text-slate-600 text-lg">×</button>
            </div>

            <form method="POST" action="{{ route('tender.proyek.record-material-usage', $tender) }}" class="p-5 space-y-4">
                @csrf
                <input type="hidden" name="rab_item_id" :value="selectedRabItem ? selectedRabItem.id : ''">

                <div class="bg-slate-50 p-3 rounded-md border border-slate-200 text-xs space-y-1">
                    <div>Barang: <strong class="text-slate-900" x-text="selectedRabItem ? selectedRabItem.item_name : ''"></strong></div>
                    <div>Total Dialokasikan: <span class="font-semibold text-blue-700" x-text="selectedRabItem ? (selectedRabItem.allocated_quantity + ' ' + selectedRabItem.unit) : ''"></span></div>
                    <div>Sudah Terpakai Sebelumnya: <span class="font-semibold text-slate-700" x-text="selectedRabItem ? (selectedRabItem.used_quantity + ' ' + selectedRabItem.unit) : '0'"></span></div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Jumlah Tambahan yang Digunakan <span class="text-rose-500">*</span></label>
                    <input type="number" step="0.01" min="0.01" name="used_quantity" x-model="usedQty" required class="w-full text-xs rounded-md border border-slate-200 px-3 py-2 outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Keterangan / Lokasi Pemasangan</label>
                    <input type="text" name="notes" x-model="usageNotes" placeholder="Contoh: Terpasang di lantai 2 titik A-B..." class="w-full text-xs rounded-md border border-slate-200 px-3 py-2 outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button type="button" @click="useMaterialModal = false" class="px-3 py-1.5 border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-medium rounded-md transition">Batal</button>
                    <button type="submit" class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-md shadow-xs transition">Simpan Penggunaan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 6: TUGASKAN TEKNISI / PERSONIL (MODUL JASA) -->
    <div x-show="assignmentModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4" style="display: none;">
        <div class="bg-white rounded-lg shadow-xl max-w-lg w-full border border-slate-200 overflow-hidden" @click.away="assignmentModal = false">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/70">
                <h3 class="text-sm font-bold text-slate-900">Tugaskan Teknisi / Personil Modul Jasa</h3>
                <button type="button" @click="assignmentModal = false" class="text-slate-400 hover:text-slate-600 text-lg">×</button>
            </div>

            <form method="POST" action="{{ route('tender.proyek.assignments.store', $tender) }}" class="p-5 space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Pilih Karyawan / User (Opsional)</label>
                        <select name="user_id" onchange="const selectedText = this.options[this.selectedIndex].getAttribute('data-name'); if(selectedText) { document.getElementById('assign_tech_name').value = selectedText; }" class="w-full text-xs rounded-md border border-slate-200 px-3 py-2 outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                            <option value="">Pilih dari User Sistem</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" data-name="{{ $user->name }}">{{ $user->name }} ({{ $user->role->name ?? 'User' }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Teknisi <span class="text-rose-500">*</span></label>
                        <input type="text" id="assign_tech_name" name="technician_name" required placeholder="Nama personil..." class="w-full text-xs rounded-md border border-slate-200 px-3 py-2 outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Peran / Kompetensi <span class="text-rose-500">*</span></label>
                        <input type="text" name="role_or_competency" required placeholder="Contoh: Network Engineer / Installer FO..." class="w-full text-xs rounded-md border border-slate-200 px-3 py-2 outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">No. Kontak / HP</label>
                        <input type="text" name="contact_phone" placeholder="08xxxxxxxxxx" class="w-full text-xs rounded-md border border-slate-200 px-3 py-2 outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Mulai Tugas</label>
                        <input type="date" name="start_date" value="{{ date('Y-m-d') }}" class="w-full text-xs rounded-md border border-slate-200 px-3 py-2 outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Selesai Tugas</label>
                        <input type="date" name="end_date" class="w-full text-xs rounded-md border border-slate-200 px-3 py-2 outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Status Penugasan <span class="text-rose-500">*</span></label>
                        <select name="status" required class="w-full text-xs rounded-md border border-slate-200 px-3 py-2 outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                            <option value="Ditugaskan">Ditugaskan</option>
                            <option value="Sedang Bekerja">Sedang Bekerja</option>
                            <option value="Selesai">Selesai</option>
                            <option value="Digantikan">Digantikan</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan Tugas / Spesifikasi Pekerjaan</label>
                    <textarea name="notes" rows="2" placeholder="Tanggung jawab pekerjaan yang diberikan..." class="w-full text-xs rounded-md border border-slate-200 px-3 py-2 outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button type="button" @click="assignmentModal = false" class="px-3 py-1.5 border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-medium rounded-md transition">Batal</button>
                    <button type="submit" class="px-3.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-md shadow-xs transition">Tugaskan Personil</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 7: EDIT STATUS PENUGASAN TEKNISI -->
    <div x-show="editAssignmentModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4" style="display: none;">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full border border-slate-200 overflow-hidden" @click.away="editAssignmentModal = false">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/70">
                <h3 class="text-sm font-bold text-slate-900">Perbarui Status Penugasan Teknisi</h3>
                <button type="button" @click="editAssignmentModal = false" class="text-slate-400 hover:text-slate-600 text-lg">×</button>
            </div>

            <form method="POST" :action="selectedAssignment ? ('{{ url('/tender/' . $tender->id . '/proyek/assignments') }}/' + selectedAssignment.id) : '#'" class="p-5 space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <span class="text-xs text-slate-500">Teknisi:</span>
                    <div class="text-sm font-bold text-slate-900" x-text="selectedAssignment ? selectedAssignment.technician_name : ''"></div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Peran / Kompetensi <span class="text-rose-500">*</span></label>
                    <input type="text" name="role_or_competency" :value="selectedAssignment ? selectedAssignment.role_or_competency : ''" required class="w-full text-xs rounded-md border border-slate-200 px-3 py-2 outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">No. Kontak / HP</label>
                    <input type="text" name="contact_phone" :value="selectedAssignment ? selectedAssignment.contact_phone : ''" class="w-full text-xs rounded-md border border-slate-200 px-3 py-2 outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Status Penugasan <span class="text-rose-500">*</span></label>
                    <select name="status" required class="w-full text-xs rounded-md border border-slate-200 px-3 py-2 outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                        <option value="Ditugaskan" :selected="selectedAssignment && selectedAssignment.status === 'Ditugaskan'">Ditugaskan</option>
                        <option value="Sedang Bekerja" :selected="selectedAssignment && selectedAssignment.status === 'Sedang Bekerja'">Sedang Bekerja</option>
                        <option value="Selesai" :selected="selectedAssignment && selectedAssignment.status === 'Selesai'">Selesai</option>
                        <option value="Digantikan" :selected="selectedAssignment && selectedAssignment.status === 'Digantikan'">Digantikan</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan</label>
                    <textarea name="notes" rows="2" :value="selectedAssignment ? selectedAssignment.notes : ''" class="w-full text-xs rounded-md border border-slate-200 px-3 py-2 outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button type="button" @click="editAssignmentModal = false" class="px-3 py-1.5 border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-medium rounded-md transition">Batal</button>
                    <button type="submit" class="px-3.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-md shadow-xs transition">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 8: PREVIEW FOTO DOKUMENTASI -->
    <div x-show="photoModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/80 backdrop-blur-xs flex items-center justify-center p-4" style="display: none;">
        <div class="bg-white rounded-lg shadow-2xl max-w-3xl w-full border border-slate-200 overflow-hidden" @click.away="photoModal = false">
            <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between bg-slate-50">
                <span class="text-xs font-bold text-slate-800" x-text="photoTitle"></span>
                <button type="button" @click="photoModal = false" class="text-slate-400 hover:text-slate-600 text-lg font-bold">×</button>
            </div>
            <div class="p-3 bg-slate-950 flex items-center justify-center max-h-[75vh] overflow-hidden">
                <img :src="photoUrl" alt="Dokumentasi Lapangan" class="max-h-[70vh] max-w-full object-contain rounded">
            </div>
            <div class="p-3 border-t border-slate-100 text-right bg-white">
                <a :href="photoUrl" target="_blank" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs rounded transition inline-flex items-center gap-1.5 shadow-xs">
                    <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i> Buka Ukuran Penuh
                </a>
            </div>
        </div>
    </div>

</div>
@endsection
