@extends('layouts.app')

@section('title', 'Administrasi Tender - PT Signal Panca Utama')
@section('header-title', 'Administrasi Tender & Proyek')

@section('content')
<div class="space-y-6" x-data="{
    createModal: false,
    uploadModal: false,
    evaluationModal: false,
    statusModal: false,
    editModal: false,
    historyModal: false,
    reviseModal: false,
    requestDeleteModal: false,
    approveDeleteModal: false,
    rejectDeleteModal: false,
    createClientMode: 'select',
    editClientMode: 'select',
    selectedClientId: '',
    editSelectedClientId: '',
    newClientName: '',
    editNewClientName: '',
    selectedTender: null,
    selectedHistory: [],
    deletionReason: '',
    rejectReason: '',
    openCreateModal() {
        this.createClientMode = 'select';
        this.selectedClientId = '';
        this.newClientName = '';
        this.createModal = true;
        this.$nextTick(() => {
            const el = document.getElementById('create_client_select');
            if (el) el.focus();
        });
    },
    switchCreateClient(mode) {
        this.createClientMode = mode;
        if (mode === 'manual') {
            this.selectedClientId = '';
            this.$nextTick(() => {
                const el = document.getElementById('create_new_client_name');
                if (el) el.focus();
            });
        } else {
            this.newClientName = '';
            this.$nextTick(() => {
                const el = document.getElementById('create_client_select');
                if (el) el.focus();
            });
        }
    },
    switchEditClient(mode) {
        this.editClientMode = mode;
        if (mode === 'manual') {
            this.editSelectedClientId = '';
            this.$nextTick(() => {
                const el = document.getElementById('edit_new_client_name');
                if (el) el.focus();
            });
        } else {
            this.editNewClientName = '';
            this.$nextTick(() => {
                const el = document.getElementById('edit_client_select');
                if (el) el.focus();
            });
        }
    },
    statusForm: {
        status: '',
        bid_value: '',
        notes: ''
    },
    openStatusModal(tender) {
        this.selectedTender = tender;
        this.statusForm.status = tender.status;
        this.statusForm.bid_value = tender.bid_value || tender.estimated_value || '';
        this.statusForm.notes = tender.notes || '';
        this.statusModal = true;
    },
    openEditModal(tender) {
        this.selectedTender = tender;
        this.editClientMode = 'select';
        this.editSelectedClientId = tender ? tender.client_id : '';
        this.editNewClientName = '';
        this.editModal = true;
    },
    openHistoryModal(tender) {
        this.selectedTender = tender;
        this.selectedHistory = tender.approval_histories || [];
        this.historyModal = true;
    },
    openReviseModal(tender) {
        this.selectedTender = tender;
        this.reviseModal = true;
    },
    openRequestDeleteModal(tender) {
        this.selectedTender = tender;
        this.deletionReason = '';
        this.requestDeleteModal = true;
    },
    openApproveDeleteModal(tender) {
        this.selectedTender = tender;
        this.approveDeleteModal = true;
    },
    openRejectDeleteModal(tender) {
        this.selectedTender = tender;
        this.rejectReason = '';
        this.rejectDeleteModal = true;
    }
}">

    <!-- Banner Mode Monitoring untuk Owner -->
    @if(auth()->user()->isOwner())
        <div class="border border-slate-200 bg-white rounded-lg p-3.5 flex flex-col md:flex-row items-start md:items-center justify-between gap-3 shadow-xs">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-md bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-eye text-sm"></i>
                </div>
                <div>
                    <span class="text-xs font-semibold text-slate-900 block leading-tight">Pemantauan Administrasi Tender (Read-Only)</span>
                    <p class="text-xs text-slate-500">Anda dapat memantau status lelang, dokumen legalitas, metode penanganan, dan riwayat persetujuan manajemen.</p>
                </div>
            </div>
            <a href="{{ route('laporan.index', ['domain' => 'tender']) }}" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-xs font-semibold transition shadow-xs">
                Buka Laporan Tender
            </a>
        </div>
    @endif

    <!-- Status Cards KPI (Administrasi Tender) -->
    <div class="grid grid-cols-2 md:grid-cols-6 gap-3">
        <a href="{{ route('tender.index') }}" class="bg-white p-3.5 rounded-lg border border-slate-200 shadow-xs hover:border-blue-400 transition {{ !request('submission_status') ? 'ring-2 ring-blue-500/20' : '' }}">
            <div class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Total Tender</div>
            <div class="text-xl font-bold text-slate-900 mt-1 font-numeric">{{ $statusCounts['total'] ?? 0 }}</div>
            <div class="text-[10px] text-slate-400 mt-0.5">Semua data terdaftar</div>
        </a>
        <a href="{{ route('tender.index', ['submission_status' => 'draft']) }}" class="bg-white p-3.5 rounded-lg border border-slate-200 shadow-xs hover:border-slate-400 transition {{ request('submission_status') === 'draft' ? 'ring-2 ring-slate-400' : '' }}">
            <div class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Draft</div>
            <div class="text-xl font-bold text-slate-700 mt-1 font-numeric">{{ $statusCounts['draft'] ?? 0 }}</div>
            <div class="text-[10px] text-slate-400 mt-0.5">Belum diajukan</div>
        </a>
        <a href="{{ route('tender.index', ['submission_status' => 'diajukan']) }}" class="bg-white p-3.5 rounded-lg border border-slate-200 shadow-xs hover:border-amber-400 transition {{ request('submission_status') === 'diajukan' ? 'ring-2 ring-amber-400' : '' }}">
            <div class="text-[11px] font-semibold text-amber-600 uppercase tracking-wider">Diajukan</div>
            <div class="text-xl font-bold text-amber-700 mt-1 font-numeric">{{ $statusCounts['diajukan'] ?? 0 }}</div>
            <div class="text-[10px] text-amber-600/80 mt-0.5">Menunggu Manajemen</div>
        </a>
        <a href="{{ route('tender.index', ['submission_status' => 'disetujui']) }}" class="bg-white p-3.5 rounded-lg border border-slate-200 shadow-xs hover:border-emerald-400 transition {{ request('submission_status') === 'disetujui' ? 'ring-2 ring-emerald-400' : '' }}">
            <div class="text-[11px] font-semibold text-emerald-600 uppercase tracking-wider">Disetujui</div>
            <div class="text-xl font-bold text-emerald-700 mt-1 font-numeric">{{ $statusCounts['disetujui'] ?? 0 }}</div>
            <div class="text-[10px] text-emerald-600/80 mt-0.5">Siap pelaksanaan</div>
        </a>
        <a href="{{ route('tender.index', ['submission_status' => 'perlu_revisi']) }}" class="bg-white p-3.5 rounded-lg border border-slate-200 shadow-xs hover:border-amber-400 transition {{ request('submission_status') === 'perlu_revisi' ? 'ring-2 ring-amber-400' : '' }}">
            <div class="text-[11px] font-semibold text-amber-700 uppercase tracking-wider">Perlu Revisi</div>
            <div class="text-xl font-bold text-amber-800 mt-1 font-numeric">{{ $statusCounts['perlu_revisi'] ?? 0 }}</div>
            <div class="text-[10px] text-amber-700/80 mt-0.5">Perlu perbaikan</div>
        </a>
        <a href="{{ route('tender.index', ['submission_status' => 'ditolak']) }}" class="bg-white p-3.5 rounded-lg border border-slate-200 shadow-xs hover:border-rose-400 transition {{ request('submission_status') === 'ditolak' ? 'ring-2 ring-rose-400' : '' }}">
            <div class="text-[11px] font-semibold text-rose-600 uppercase tracking-wider">Ditolak</div>
            <div class="text-xl font-bold text-rose-700 mt-1 font-numeric">{{ $statusCounts['ditolak'] ?? 0 }}</div>
            <div class="text-[10px] text-rose-500 mt-0.5">Tidak disetujui</div>
        </a>
    </div>

    <!-- Banner Notifikasi Permohonan Hapus Data untuk Manajemen -->
    @if(($statusCounts['pending_deletion'] ?? 0) > 0)
        <div class="border border-rose-200 bg-rose-50/70 rounded-lg p-3.5 flex flex-col md:flex-row items-start md:items-center justify-between gap-3 shadow-xs">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-md bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-triangle-exclamation text-sm"></i>
                </div>
                <div>
                    <span class="text-xs font-bold text-rose-900 block leading-tight">
                        Terdapat {{ $statusCounts['pending_deletion'] }} Permohonan Izin Hapus Data Tender
                    </span>
                    <p class="text-xs text-rose-700/90 mt-0.5">
                        Permohonan penghapusan yang diajukan oleh Admin memerlukan persetujuan resmi Manajemen sebelum data dihapus dari sistem.
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('tender.index', ['deletion_status' => 'pending_deletion']) }}" class="px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white rounded-md text-xs font-semibold transition shadow-xs whitespace-nowrap flex items-center gap-1.5">
                    <i class="fa-solid fa-shield-halved text-[11px]"></i> Tinjau Permohonan Hapus
                </a>
                @if(request('deletion_status') === 'pending_deletion')
                    <a href="{{ route('tender.index') }}" class="px-2.5 py-1.5 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 rounded-md text-xs font-medium transition">
                        Lihat Semua
                    </a>
                @endif
            </div>
        </div>
    @endif

    <!-- Filter & Action Bar -->
    <div class="bg-white rounded-lg p-3.5 border border-slate-200 shadow-xs flex flex-col md:flex-row justify-between items-center gap-3">
        <form method="GET" action="{{ route('tender.index') }}" class="flex flex-wrap items-center gap-2 w-full md:w-auto">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nomor tender / nama / klien..." class="px-3 py-1.5 rounded-md border border-slate-200 text-xs w-60 outline-none focus:ring-2 focus:ring-blue-500">
            
            <select name="status" onchange="this.form.submit()" class="px-2.5 py-1.5 rounded-md border border-slate-200 text-xs font-medium text-slate-700 bg-white hover:bg-slate-50 outline-none">
                <option value="">Semua Pipeline</option>
                <option value="Ditemukan" {{ request('status') == 'Ditemukan' ? 'selected' : '' }}>Ditemukan</option>
                <option value="Evaluasi" {{ request('status') == 'Evaluasi' ? 'selected' : '' }}>Evaluasi</option>
                <option value="Persiapan Dokumen" {{ request('status') == 'Persiapan Dokumen' ? 'selected' : '' }}>Persiapan Dokumen</option>
                <option value="Penawaran" {{ request('status') == 'Penawaran' ? 'selected' : '' }}>Penawaran</option>
                <option value="Menang" {{ request('status') == 'Menang' ? 'selected' : '' }}>Menang</option>
                <option value="Kalah" {{ request('status') == 'Kalah' ? 'selected' : '' }}>Kalah</option>
                <option value="Kontrak" {{ request('status') == 'Kontrak' ? 'selected' : '' }}>Kontrak</option>
                <option value="Pelaksanaan" {{ request('status') == 'Pelaksanaan' ? 'selected' : '' }}>Pelaksanaan</option>
                <option value="Selesai" {{ request('status') == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                <option value="Batal" {{ request('status') == 'Batal' ? 'selected' : '' }}>Batal</option>
            </select>

            <select name="submission_status" onchange="this.form.submit()" class="px-2.5 py-1.5 rounded-md border border-slate-200 text-xs font-medium text-slate-700 bg-white hover:bg-slate-50 outline-none">
                <option value="">Semua Status Pengajuan</option>
                <option value="draft" {{ request('submission_status') == 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="diajukan" {{ request('submission_status') == 'diajukan' ? 'selected' : '' }}>Diajukan</option>
                <option value="disetujui" {{ request('submission_status') == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                <option value="perlu_revisi" {{ request('submission_status') == 'perlu_revisi' ? 'selected' : '' }}>Perlu Revisi</option>
                <option value="ditolak" {{ request('submission_status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
            </select>

            <select name="metode_penanganan" onchange="this.form.submit()" class="px-2.5 py-1.5 rounded-md border border-slate-200 text-xs font-medium text-slate-700 bg-white hover:bg-slate-50 outline-none">
                <option value="">Semua Metode</option>
                <option value="internal" {{ request('metode_penanganan') == 'internal' ? 'selected' : '' }}>Internal SPU</option>
                <option value="vendor_relasi" {{ request('metode_penanganan') == 'vendor_relasi' ? 'selected' : '' }}>Vendor Relasi</option>
            </select>

            <select name="deletion_status" onchange="this.form.submit()" class="px-2.5 py-1.5 rounded-md border border-slate-200 text-xs font-medium text-slate-700 bg-white hover:bg-slate-50 outline-none">
                <option value="">Semua Status Izin Hapus</option>
                <option value="pending_deletion" {{ request('deletion_status') == 'pending_deletion' ? 'selected' : '' }}>Menunggu Izin Hapus ({{ $statusCounts['pending_deletion'] ?? 0 }})</option>
            </select>

            <button type="submit" class="px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 font-semibold text-xs rounded-md transition">
                Filter
            </button>
            @if(request()->hasAny(['search', 'status', 'submission_status', 'metode_penanganan', 'deletion_status']))
                <a href="{{ route('tender.index') }}" class="px-2.5 py-1.5 text-xs text-slate-500 hover:text-slate-800 transition">Reset</a>
            @endif
        </form>

        @if(auth()->user()->role?->name !== 'owner')
            <button @click="openCreateModal()" class="w-full md:w-auto px-3.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs rounded-md shadow-xs transition flex items-center justify-center gap-1.5 cursor-pointer">
                <i class="fa-solid fa-plus text-[11px]"></i> Tambah Tender Baru
            </button>
        @else
            <span class="px-2.5 py-1 rounded text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                Mode Peninjauan (Read-Only)
            </span>
        @endif
    </div>

    <!-- Data Table Card -->
    <div class="bg-white rounded-lg border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-600 font-semibold border-b border-slate-200 uppercase tracking-wider">
                        <th class="p-3">No. Tender</th>
                        <th class="p-3">Nama Tender & Klien</th>
                        <th class="p-3">Pelaksanaan & Alasan</th>
                        <th class="p-3">Nilai Tender (Rp)</th>
                        <th class="p-3">Deadline</th>
                        <th class="p-3">Status Pipeline</th>
                        <th class="p-3">Persetujuan Manajemen</th>
                        <th class="p-3">Submodul Terhubung</th>
                        <th class="p-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($tenders as $tender)
                        <tr class="hover:bg-slate-50/70 transition">
                            <!-- No. Tender -->
                            <td class="p-3 font-mono font-medium text-slate-900 whitespace-nowrap">
                                {{ $tender->tender_number }}
                                <div class="text-[10px] text-slate-400 font-sans mt-0.5">Oleh: {{ $tender->creator->name ?? 'Admin' }}</div>
                            </td>

                            <!-- Nama Tender & Klien -->
                            <td class="p-3 min-w-[200px]">
                                <div class="font-semibold text-slate-900 leading-snug">{{ $tender->name }}</div>
                                <div class="text-[11px] text-slate-500 mt-0.5 flex items-center gap-1">
                                    <i class="fa-regular fa-building text-[10px] text-slate-400"></i>
                                    <span>{{ $tender->client->name ?? 'Instansi N/A' }}</span>
                                </div>
                            </td>

                            <!-- Pelaksanaan & Alasan Keputusan -->
                            <td class="p-3 whitespace-nowrap">
                                <div class="space-y-1">
                                    @if($tender->metode_penanganan === 'vendor_relasi')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-medium bg-amber-50 text-amber-800 border border-amber-200">
                                            <i class="fa-solid fa-handshake text-[10px]"></i> Vendor: {{ $tender->nama_vendor_relasi ?: 'Relasi Eksternal' }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-medium bg-blue-50 text-blue-800 border border-blue-200">
                                            <i class="fa-solid fa-building-user text-[10px]"></i> Internal PT SPU
                                        </span>
                                    @endif

                                    @if($tender->alasan_metode)
                                        <div class="text-[10px] text-slate-500 max-w-[200px] truncate" title="Alasan: {{ $tender->alasan_metode }}">
                                            <span class="font-medium text-slate-600">Alasan:</span> {{ $tender->alasan_metode }}
                                        </div>
                                    @endif
                                </div>
                            </td>

                            <!-- Nilai Tender -->
                            <td class="p-3 whitespace-nowrap font-numeric">
                                <div class="font-bold text-slate-900">Rp {{ number_format($tender->bid_value > 0 ? $tender->bid_value : $tender->estimated_value, 0, ',', '.') }}</div>
                                <div class="text-[11px] text-slate-400">Est: Rp {{ number_format($tender->estimated_value, 0, ',', '.') }}</div>
                            </td>

                            <!-- Deadline -->
                            <td class="p-3 whitespace-nowrap">
                                @if($tender->deadline)
                                    <div class="font-medium text-slate-700">{{ \Carbon\Carbon::parse($tender->deadline)->format('d M Y') }}</div>
                                    <div class="text-[10px] text-amber-600 font-medium">{{ \Carbon\Carbon::parse($tender->deadline)->diffForHumans() }}</div>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>

                            <!-- Status Pipeline -->
                            <td class="p-3 whitespace-nowrap">
                                <button type="button" @click="openStatusModal({{ $tender }})" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded text-xs font-medium bg-slate-100 hover:bg-slate-200 text-slate-700 transition" title="Klik untuk mengubah tahapan pipeline">
                                    <span>{{ $tender->status }}</span>
                                    <i class="fa-solid fa-pen text-[9px] text-slate-400"></i>
                                </button>
                            </td>

                            <!-- Status Pengajuan & Persetujuan Manajemen -->
                            <td class="p-3 whitespace-nowrap">
                                <div class="space-y-1">
                                    @php
                                        $subStatus = $tender->submission_status ?: ($tender->approval_status === 'pending' ? 'diajukan' : ($tender->approval_status === 'approved' ? 'disetujui' : 'draft'));
                                    @endphp

                                    @if($tender->deletion_status === 'pending_deletion')
                                        <div>
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-semibold bg-rose-50 text-rose-800 border border-rose-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-pulse"></span> Izin Hapus Diajukan
                                            </span>
                                            @if($tender->deletion_reason)
                                                <div class="text-[10px] text-slate-500 max-w-[200px] truncate mt-0.5" title="Alasan Hapus: {{ $tender->deletion_reason }}">
                                                    <span class="font-medium text-slate-600">Alasan:</span> {{ $tender->deletion_reason }}
                                                </div>
                                            @endif
                                            @if($tender->deletionRequester)
                                                <div class="text-[10px] text-slate-400">
                                                    Oleh: {{ $tender->deletionRequester->name }}
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        @if($subStatus === 'draft')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Draft
                                            </span>
                                        @elseif($subStatus === 'diajukan')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-medium bg-amber-50 text-amber-800 border border-amber-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span> Diajukan
                                            </span>
                                        @elseif($subStatus === 'disetujui')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-medium bg-emerald-50 text-emerald-800 border border-emerald-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Disetujui
                                            </span>
                                        @elseif($subStatus === 'perlu_revisi')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-medium bg-amber-100 text-amber-900 border border-amber-300">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span> Perlu Revisi
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-medium bg-rose-50 text-rose-800 border border-rose-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Ditolak
                                            </span>
                                        @endif
                                    @endif

                                    <!-- Tombol Riwayat Persetujuan -->
                                    <div>
                                        <button type="button" @click="openHistoryModal({{ $tender }})" class="text-[10px] text-blue-600 hover:underline flex items-center gap-1">
                                            <i class="fa-solid fa-clock-rotate-left text-[9px]"></i> Riwayat Keputusan
                                        </button>
                                    </div>
                                </div>
                            </td>

                            <!-- Submodul Terhubung (RAB & Proyek) -->
                            <td class="p-3 whitespace-nowrap">
                                <div class="flex items-center gap-1.5">
                                    <a href="{{ route('tender.rab.show', $tender) }}" class="inline-flex items-center gap-1 px-2 py-1 rounded text-[11px] font-semibold bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200 transition" title="Buka Estimasi / RAB">
                                        <i class="fa-solid fa-calculator text-[10px]"></i> RAB
                                        <span class="text-[9px] px-1 py-0.2 bg-white rounded border border-blue-200">{{ $tender->rab_status ?: 'draft' }}</span>
                                    </a>
                                    <a href="{{ route('tender.proyek.show', $tender) }}" class="inline-flex items-center gap-1 px-2 py-1 rounded text-[11px] font-semibold bg-slate-50 text-slate-700 hover:bg-slate-100 border border-slate-200 transition" title="Buka Pelaksanaan Lapangan / Proyek">
                                        <i class="fa-solid fa-helmet-safety text-[10px]"></i> Proyek
                                        <span class="text-[9px] px-1 py-0.2 bg-white rounded border border-slate-200">{{ $tender->project_progress ?? 0 }}%</span>
                                    </a>
                                </div>
                            </td>

                            <!-- Aksi -->
                            <td class="p-3 text-center whitespace-nowrap">
                                <div class="inline-flex items-center justify-center gap-1.5">
                                    <!-- Aksi Izin Hapus untuk Manajemen (Manager / Owner) -->
                                    @if(in_array(auth()->user()->role?->name, ['manager', 'owner'], true) && $tender->deletion_status === 'pending_deletion')
                                        <button type="button" @click="openApproveDeleteModal({{ $tender }})" class="px-2 py-1 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs rounded-md transition shadow-xs flex items-center gap-1" title="Setujui Izin Hapus Data">
                                            <i class="fa-solid fa-check text-[10px]"></i> Izinkan Hapus
                                        </button>
                                        <button type="button" @click="openRejectDeleteModal({{ $tender }})" class="px-2 py-1 bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold text-xs rounded-md transition shadow-xs flex items-center gap-1" title="Tolak Izin Hapus Data">
                                            <i class="fa-solid fa-xmark text-[10px]"></i> Tolak Izin
                                        </button>
                                    @endif

                                    <!-- Ajukan ke Manajemen (Admin action jika draft atau perlu revisi) -->
                                    @if(auth()->user()->role?->name === 'admin' && in_array($tender->submission_status, ['draft', 'perlu_revisi', null], true) && $tender->deletion_status !== 'pending_deletion')
                                        <form action="{{ route('tender.submit', $tender) }}" method="POST" class="inline" onsubmit="return confirm('Ajukan tender ini ke Manajemen untuk mendapatkan persetujuan?');">
                                            @csrf
                                            <button type="submit" class="px-2 py-1 bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs rounded-md shadow-xs transition inline-flex items-center gap-1" title="Ajukan ke Manajemen">
                                                <i class="fa-solid fa-paper-plane text-[10px]"></i> Ajukan
                                            </button>
                                        </form>
                                    @endif

                                    <!-- Keputusan Manajemen: Setujui, Tolak, Perlu Revisi (jika diajukan) -->
                                    @if(auth()->user()->role?->name === 'manager' && in_array($tender->submission_status, ['diajukan', 'pending'], true) && $tender->deletion_status !== 'pending_deletion')
                                        <form action="{{ route('tender.approve', $tender) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="px-2 py-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-md transition shadow-xs flex items-center gap-1" title="Setujui Tender">
                                                <i class="fa-solid fa-check text-[10px]"></i> Setujui
                                            </button>
                                        </form>
                                        <button type="button" @click="openReviseModal({{ $tender }})" class="px-2 py-1 bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs rounded-md transition shadow-xs flex items-center gap-1" title="Minta Revisi Tender">
                                            <i class="fa-solid fa-rotate-left text-[10px]"></i> Revisi
                                        </button>
                                        <form action="{{ route('tender.reject', $tender) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menolak tender ini?');">
                                            @csrf
                                            <button type="submit" class="px-2 py-1 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs rounded-md transition shadow-xs flex items-center gap-1" title="Tolak Tender">
                                                <i class="fa-solid fa-xmark text-[10px]"></i> Tolak
                                            </button>
                                        </form>
                                    @endif

                                    <!-- Edit Tender -->
                                    @if(auth()->user()->role?->name !== 'owner' && $tender->deletion_status !== 'pending_deletion')
                                        <button type="button" @click="openEditModal({{ $tender }})" class="w-7 h-7 flex items-center justify-center bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs rounded-md transition" title="Edit Data Tender">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                    @endif

                                    <!-- Upload Dokumen -->
                                    @if($tender->deletion_status !== 'pending_deletion')
                                        <button @click="selectedTender = {{ $tender }}; uploadModal = true" class="w-7 h-7 flex items-center justify-center bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs rounded-md transition" title="Upload Dokumen Tender">
                                            <i class="fa-solid fa-upload"></i>
                                        </button>
                                    @endif

                                    <!-- Hapus Data: Admin wajib meminta izin Manajemen, Manager/Owner dapat menghapus langsung -->
                                    @if(auth()->user()->role?->name === 'admin')
                                        @if($tender->deletion_status === 'pending_deletion')
                                            <span class="px-2 py-1 bg-rose-50 text-rose-600 border border-rose-200 rounded text-[10px] font-semibold flex items-center gap-1" title="Permohonan hapus sedang menunggu izin Manajemen">
                                                <i class="fa-solid fa-hourglass-half text-[9px]"></i> Menunggu Izin
                                            </span>
                                        @else
                                            <button type="button" @click="openRequestDeleteModal({{ $tender }})" class="w-7 h-7 flex items-center justify-center bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs rounded-md transition" title="Ajukan Permohonan Hapus ke Manajemen">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        @endif
                                    @elseif(in_array(auth()->user()->role?->name, ['manager', 'owner'], true) && $tender->deletion_status !== 'pending_deletion')
                                        <form action="{{ route('tender.destroy', $tender) }}" method="POST" data-confirm="Apakah Anda yakin ingin menghapus data tender {{ $tender->tender_number }}? Seluruh berkas dan riwayat terkait akan dihapus permanen." class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-7 h-7 flex items-center justify-center bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs rounded-md transition" title="Hapus Data (Manajemen)">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="p-8 text-center text-slate-400 text-sm">
                                <div class="flex flex-col items-center justify-center space-y-2">
                                    <i class="fa-solid fa-folder-open text-2xl text-slate-300"></i>
                                    <span>Tidak ada data tender yang sesuai dengan filter pencarian.</span>
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

    <!-- Datalist Autocomplete Klien / Instansi -->
    <datalist id="existingClientsList">
        @foreach($clients as $c)
            <option value="{{ $c->name }}">{{ $c->company_name ?? '' }}</option>
        @endforeach
    </datalist>

    <!-- Modal 1: Input Tender Baru -->
    <div x-show="createModal" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4" x-cloak>
        <div class="bg-white rounded-lg border border-slate-200 max-w-xl w-full p-5 shadow-xl space-y-4 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center border-b border-slate-200 pb-3">
                <div>
                    <h3 class="font-bold text-sm text-slate-900">Input Data Tender Baru</h3>
                    <p class="text-xs text-slate-500">Isi data administrasi tender dan tetapkan metode pelaksanaan</p>
                </div>
                <button @click="createModal = false" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-sm"></i></button>
            </div>

            <form action="{{ route('tender.store') }}" method="POST" class="space-y-3.5">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Nomor Tender</label>
                        <input type="text" name="tender_number" required value="TDR-{{ date('Ymd') }}-{{ rand(100,999) }}" class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500 font-mono bg-slate-50">
                    </div>
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">
                                Klien / Instansi <span class="text-rose-500">*</span>
                            </label>
                            <!-- Segmented Switch Tab -->
                            <div class="inline-flex p-0.5 bg-slate-100 rounded-md border border-slate-200 text-[11px]">
                                <button type="button" 
                                        @click="switchCreateClient('select')"
                                        :class="createClientMode === 'select' ? 'bg-white text-blue-600 font-semibold shadow-xs border border-slate-200/80' : 'text-slate-500 hover:text-slate-800 font-medium'"
                                        class="px-2.5 py-0.5 rounded transition cursor-pointer flex items-center gap-1">
                                    <i class="fa-solid fa-list-ul text-[10px]"></i> Pilih Daftar
                                </button>
                                <button type="button" 
                                        @click="switchCreateClient('manual')"
                                        :class="createClientMode === 'manual' ? 'bg-white text-blue-600 font-semibold shadow-xs border border-slate-200/80' : 'text-slate-500 hover:text-slate-800 font-medium'"
                                        class="px-2.5 py-0.5 rounded transition cursor-pointer flex items-center gap-1">
                                    <i class="fa-solid fa-plus text-[10px]"></i> Ketik Baru
                                </button>
                            </div>
                        </div>

                        <!-- Mode 1: Pilih Dari Daftar Klien -->
                        <div x-show="createClientMode === 'select'">
                            <select id="create_client_select"
                                    name="client_id" 
                                    x-ref="createClientSelect"
                                    x-model="selectedClientId"
                                    :disabled="createClientMode !== 'select'"
                                    @change="if($event.target.value === '__new__') { switchCreateClient('manual'); }"
                                    class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                                <option value="">-- Pilih Klien / Instansi Terdaftar --</option>
                                @foreach($clients as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->company_name ?? '-' }})</option>
                                @endforeach
                                <option value="__new__" class="font-bold text-blue-600 bg-blue-50">+ Ketik Klien / Instansi Baru...</option>
                            </select>
                            <div class="mt-1 flex items-center justify-between text-[11px]">
                                <span class="text-slate-400">Instansi belum ada di daftar?</span>
                                <button type="button" 
                                        @click="switchCreateClient('manual')" 
                                        class="font-medium text-blue-600 hover:text-blue-800 hover:underline flex items-center gap-1 cursor-pointer">
                                    <i class="fa-solid fa-pen-to-square text-[10px]"></i> Klik di sini untuk ketik manual
                                </button>
                            </div>
                        </div>

                        <!-- Mode 2: Ketik Manual Klien Baru -->
                        <div x-show="createClientMode === 'manual'" x-cloak class="space-y-1">
                            <div class="relative">
                                <input type="text" 
                                       id="create_new_client_name"
                                       name="new_client_name" 
                                       x-ref="createClientManual"
                                       x-model="newClientName"
                                       :disabled="createClientMode !== 'manual'"
                                       list="existingClientsList"
                                       placeholder="Ketik nama instansi / klien baru..." 
                                       class="w-full px-3 py-1.5 border border-blue-400 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500 bg-blue-50/20 text-slate-900 font-medium placeholder-slate-400">
                            </div>
                            <div class="flex items-center justify-between text-[11px]">
                                <span class="text-blue-600 font-medium flex items-center gap-1">
                                    <i class="fa-solid fa-circle-info text-[10px]"></i> Instansi baru akan otomatis tersimpan ke master.
                                </span>
                                <button type="button" 
                                        @click="switchCreateClient('select')" 
                                        class="text-slate-500 hover:text-slate-800 underline cursor-pointer">
                                    Kembali ke daftar pilihan
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Nama Tender Pekerjaan <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" required placeholder="Contoh: Pengadaan & Pemasangan Jaringan Wi-Fi Gedung B..." class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Sumber Informasi Tender</label>
                        <input type="text" name="source" placeholder="Contoh: LPSE, Surat Undangan..." class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Status Pipeline Awal</label>
                        <select name="status" required class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                            <option value="Ditemukan">1. Ditemukan</option>
                            <option value="Evaluasi">2. Evaluasi</option>
                            <option value="Persiapan Dokumen">3. Persiapan Dokumen</option>
                            <option value="Penawaran">4. Penawaran</option>
                            <option value="Menang">5. Menang</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Tanggal Ditemukan</label>
                        <input type="date" name="found_date" required value="{{ date('Y-m-d') }}" class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Tenggat Waktu (Deadline)</label>
                        <input type="date" name="deadline" class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Nilai Estimasi / Pagu (Rp)</label>
                        <input type="number" name="estimated_value" required placeholder="500000000" class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500 font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Nilai Penawaran / Kontrak (Rp)</label>
                        <input type="number" name="bid_value" placeholder="480000000" class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500 font-mono">
                    </div>
                </div>

                <!-- Bagian B: Metode Pelaksanaan & Alasan Keputusan -->
                <div x-data="{ metode: 'internal' }" class="border-t border-slate-200 pt-3 space-y-3">
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Jenis Pelaksanaan Pekerjaan</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="flex items-start gap-2.5 p-3 rounded-md border border-slate-200 cursor-pointer hover:bg-slate-50 transition" :class="metode === 'internal' ? 'border-blue-600 bg-blue-50/50 text-slate-900 font-semibold' : 'text-slate-600'">
                            <input type="radio" name="metode_penanganan" value="internal" x-model="metode" class="mt-0.5 text-blue-600">
                            <div>
                                <span class="text-xs block">Dikerjakan Sendiri (Internal)</span>
                                <span class="text-[10px] text-slate-400 font-normal">Dikerjakan mandiri oleh tim teknisi PT Signal Panca Utama</span>
                            </div>
                        </label>
                        <label class="flex items-start gap-2.5 p-3 rounded-md border border-slate-200 cursor-pointer hover:bg-slate-50 transition" :class="metode === 'vendor_relasi' ? 'border-amber-600 bg-amber-50/50 text-slate-900 font-semibold' : 'text-slate-600'">
                            <input type="radio" name="metode_penanganan" value="vendor_relasi" x-model="metode" class="mt-0.5 text-amber-600">
                            <div>
                                <span class="text-xs block">Vendor / Partner Bisnis</span>
                                <span class="text-[10px] text-slate-400 font-normal">Dilimpahkan ke mitra eksternal / sub-kontraktor</span>
                            </div>
                        </label>
                    </div>

                    <div x-show="metode === 'vendor_relasi'" class="space-y-1">
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Nama Vendor / Partner Bisnis <span class="text-rose-500">*</span></label>
                        <input type="text" name="nama_vendor_relasi" placeholder="Nama perusahaan mitra / sub-kontraktor eksternal..." class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Alasan Keputusan Internal / Vendor</label>
                        <textarea name="alasan_metode" rows="2" placeholder="Jelaskan dasar pertimbangan kapasitas SDM, ketersediaan alat, atau kapabilitas vendor..." class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Catatan Tambahan</label>
                    <textarea name="notes" rows="2" placeholder="Catatan kualifikasi, syarat jaminan tender, dsb..." class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>

                <div class="flex items-center justify-between pt-3 border-t border-slate-200">
                    <button type="button" @click="createModal = false" class="px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium text-xs rounded-md transition">Batal</button>
                    <div class="flex items-center gap-2">
                        <button type="submit" name="action_type" value="draft" class="px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-800 font-semibold text-xs rounded-md transition border border-slate-300">
                            Simpan sebagai Draft
                        </button>
                        <button type="submit" name="action_type" value="submit" class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs rounded-md transition shadow-xs flex items-center gap-1.5">
                            <i class="fa-solid fa-paper-plane text-[10px]"></i> Simpan & Ajukan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal 2: Edit Data Tender -->
    <div x-show="editModal" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4" x-cloak>
        <div class="bg-white rounded-lg border border-slate-200 max-w-xl w-full p-5 shadow-xl space-y-4 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center border-b border-slate-200 pb-3">
                <div>
                    <h3 class="font-bold text-sm text-slate-900">Edit Data Tender</h3>
                    <p class="text-xs text-slate-500 font-mono" x-text="selectedTender ? selectedTender.tender_number : ''"></p>
                </div>
                <button @click="editModal = false" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-sm"></i></button>
            </div>

            <form x-bind:action="selectedTender ? '/tender/' + selectedTender.id : '#'" method="POST" class="space-y-3.5">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Nomor Tender</label>
                        <input type="text" disabled x-bind:value="selectedTender?.tender_number" class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs bg-slate-100 text-slate-500 font-mono">
                    </div>
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">
                                Klien / Instansi <span class="text-rose-500">*</span>
                            </label>
                            <!-- Segmented Switch Tab -->
                            <div class="inline-flex p-0.5 bg-slate-100 rounded-md border border-slate-200 text-[11px]">
                                <button type="button" 
                                        @click="switchEditClient('select')"
                                        :class="editClientMode === 'select' ? 'bg-white text-blue-600 font-semibold shadow-xs border border-slate-200/80' : 'text-slate-500 hover:text-slate-800 font-medium'"
                                        class="px-2.5 py-0.5 rounded transition cursor-pointer flex items-center gap-1">
                                    <i class="fa-solid fa-list-ul text-[10px]"></i> Pilih Daftar
                                </button>
                                <button type="button" 
                                        @click="switchEditClient('manual')"
                                        :class="editClientMode === 'manual' ? 'bg-white text-blue-600 font-semibold shadow-xs border border-slate-200/80' : 'text-slate-500 hover:text-slate-800 font-medium'"
                                        class="px-2.5 py-0.5 rounded transition cursor-pointer flex items-center gap-1">
                                    <i class="fa-solid fa-plus text-[10px]"></i> Ketik Baru
                                </button>
                            </div>
                        </div>

                        <!-- Mode 1: Pilih Dari Daftar Klien -->
                        <div x-show="editClientMode === 'select'">
                            <select id="edit_client_select"
                                    name="client_id" 
                                    x-ref="editClientSelect"
                                    x-model="editSelectedClientId"
                                    :disabled="editClientMode !== 'select'"
                                    @change="if($event.target.value === '__new__') { switchEditClient('manual'); }"
                                    class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                                <option value="">-- Pilih Klien / Instansi Terdaftar --</option>
                                @foreach($clients as $c)
                                    <option value="{{ $c->id }}" x-bind:selected="selectedTender?.client_id == {{ $c->id }}">{{ $c->name }} ({{ $c->company_name ?? '-' }})</option>
                                @endforeach
                                <option value="__new__" class="font-bold text-blue-600 bg-blue-50">+ Ketik Nama Klien Baru...</option>
                            </select>
                            <div class="mt-1 flex items-center justify-between text-[11px]">
                                <span class="text-slate-400">Instansi belum ada di daftar?</span>
                                <button type="button" 
                                        @click="switchEditClient('manual')" 
                                        class="font-medium text-blue-600 hover:text-blue-800 hover:underline flex items-center gap-1 cursor-pointer">
                                    <i class="fa-solid fa-pen-to-square text-[10px]"></i> Klik di sini untuk ketik manual
                                </button>
                            </div>
                        </div>

                        <!-- Mode 2: Ketik Manual Klien Baru -->
                        <div x-show="editClientMode === 'manual'" x-cloak class="space-y-1">
                            <div class="relative">
                                <input type="text" 
                                       id="edit_new_client_name"
                                       name="new_client_name" 
                                       x-ref="editClientManual"
                                       x-model="editNewClientName"
                                       :disabled="editClientMode !== 'manual'"
                                       list="existingClientsList"
                                       placeholder="Ketik nama instansi / klien baru..." 
                                       class="w-full px-3 py-1.5 border border-blue-400 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500 bg-blue-50/20 text-slate-900 font-medium placeholder-slate-400">
                            </div>
                            <div class="flex items-center justify-between text-[11px]">
                                <span class="text-blue-600 font-medium flex items-center gap-1">
                                    <i class="fa-solid fa-circle-info text-[10px]"></i> Instansi baru akan otomatis tersimpan ke master.
                                </span>
                                <button type="button" 
                                        @click="switchEditClient('select')" 
                                        class="text-slate-500 hover:text-slate-800 underline cursor-pointer">
                                    Kembali ke daftar pilihan
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Nama Tender Pekerjaan <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" required x-bind:value="selectedTender?.name" class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Tanggal Ditemukan</label>
                        <input type="date" name="found_date" required x-bind:value="selectedTender?.found_date ? selectedTender.found_date.substring(0,10) : ''" class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Tenggat Waktu (Deadline)</label>
                        <input type="date" name="deadline" x-bind:value="selectedTender?.deadline ? selectedTender.deadline.substring(0,10) : ''" class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Nilai Estimasi (Rp)</label>
                        <input type="number" name="estimated_value" required min="0" x-bind:value="selectedTender?.estimated_value" class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500 font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Nilai Penawaran (Rp)</label>
                        <input type="number" name="bid_value" min="0" x-bind:value="selectedTender?.bid_value" class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500 font-mono">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Status Pipeline</label>
                    <select name="status" required class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                        <option value="Ditemukan" x-bind:selected="selectedTender?.status === 'Ditemukan'">Ditemukan</option>
                        <option value="Evaluasi" x-bind:selected="selectedTender?.status === 'Evaluasi'">Evaluasi</option>
                        <option value="Persiapan Dokumen" x-bind:selected="selectedTender?.status === 'Persiapan Dokumen'">Persiapan Dokumen</option>
                        <option value="Penawaran" x-bind:selected="selectedTender?.status === 'Penawaran'">Penawaran</option>
                        <option value="Menang" x-bind:selected="selectedTender?.status === 'Menang'">Menang</option>
                        <option value="Kalah" x-bind:selected="selectedTender?.status === 'Kalah'">Kalah</option>
                        <option value="Kontrak" x-bind:selected="selectedTender?.status === 'Kontrak'">Kontrak</option>
                        <option value="Pelaksanaan" x-bind:selected="selectedTender?.status === 'Pelaksanaan'">Pelaksanaan</option>
                        <option value="Selesai" x-bind:selected="selectedTender?.status === 'Selesai'">Selesai</option>
                        <option value="Batal" x-bind:selected="selectedTender?.status === 'Batal'">Batal</option>
                    </select>
                </div>

                <!-- Metode & Alasan -->
                <div class="border-t border-slate-200 pt-3 space-y-2">
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Metode Pelaksanaan</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="flex items-center gap-2 p-2 border border-slate-200 rounded-md text-xs cursor-pointer">
                            <input type="radio" name="metode_penanganan" value="internal" x-bind:checked="selectedTender?.metode_penanganan === 'internal'">
                            <span>Internal PT SPU</span>
                        </label>
                        <label class="flex items-center gap-2 p-2 border border-slate-200 rounded-md text-xs cursor-pointer">
                            <input type="radio" name="metode_penanganan" value="vendor_relasi" x-bind:checked="selectedTender?.metode_penanganan === 'vendor_relasi'">
                            <span>Vendor Relasi</span>
                        </label>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Nama Vendor (Jika Vendor Relasi)</label>
                        <input type="text" name="nama_vendor_relasi" x-bind:value="selectedTender?.nama_vendor_relasi" class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Alasan Keputusan Internal / Vendor</label>
                        <textarea name="alasan_metode" rows="2" x-bind:value="selectedTender?.alasan_metode" class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Catatan</label>
                    <textarea name="notes" rows="2" x-bind:value="selectedTender?.notes" class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-200">
                    <button type="button" @click="editModal = false" class="px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium text-xs rounded-md transition">Batal</button>
                    <button type="submit" class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs rounded-md transition shadow-xs">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal 3: Riwayat Persetujuan & Keputusan Manajemen -->
    <div x-show="historyModal" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4" x-cloak>
        <div class="bg-white rounded-lg border border-slate-200 max-w-lg w-full p-5 shadow-xl space-y-4 max-h-[85vh] overflow-y-auto">
            <div class="flex justify-between items-center border-b border-slate-200 pb-3">
                <div>
                    <h3 class="font-bold text-sm text-slate-900">Riwayat Persetujuan & Keputusan</h3>
                    <p class="text-xs text-slate-500 font-mono" x-text="selectedTender ? selectedTender.tender_number : ''"></p>
                </div>
                <button @click="historyModal = false" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-sm"></i></button>
            </div>

            <div class="space-y-3">
                <template x-if="selectedHistory && selectedHistory.length > 0">
                    <div class="relative pl-6 space-y-4 before:absolute before:left-2 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-200">
                        <template x-for="item in selectedHistory" :key="item.id">
                            <div class="relative">
                                <span class="absolute -left-6 top-1 w-2.5 h-2.5 rounded-full border-2 border-white"
                                      :class="{
                                          'bg-amber-500': item.action === 'diajukan',
                                          'bg-emerald-500': item.action === 'disetujui',
                                          'bg-rose-500': item.action === 'ditolak',
                                          'bg-amber-600': item.action === 'perlu_revisi',
                                          'bg-rose-400': item.action === 'diajukan_hapus',
                                          'bg-rose-700': item.action === 'disetujui_hapus',
                                          'bg-slate-500': item.action === 'ditolak_hapus'
                                      }"></span>
                                <div class="bg-slate-50 p-3 rounded-md border border-slate-200 text-xs">
                                    <div class="flex items-center justify-between font-semibold text-slate-800">
                                        <span class="capitalize" x-text="item.action.replace('_', ' ')"></span>
                                        <span class="text-[10px] text-slate-400 font-normal" x-text="item.created_at ? new Date(item.created_at).toLocaleDateString('id-ID', {day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit'}) : '-'"></span>
                                    </div>
                                    <div class="text-[11px] text-slate-500 mt-0.5" x-text="'Oleh: ' + (item.user ? item.user.name : 'Pengguna')"></div>
                                    <div class="mt-1 text-slate-700 bg-white p-2 rounded border border-slate-100 text-[11px]" x-show="item.notes" x-text="item.notes"></div>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>

                <template x-if="!selectedHistory || selectedHistory.length === 0">
                    <div class="p-6 text-center text-slate-400 text-xs">
                        <i class="fa-solid fa-clock-rotate-left text-xl block mb-1"></i>
                        <span>Belum ada catatan riwayat persetujuan untuk tender ini.</span>
                    </div>
                </template>
            </div>

            <div class="flex justify-end pt-3 border-t border-slate-200">
                <button type="button" @click="historyModal = false" class="px-4 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-md transition">Tutup</button>
            </div>
        </div>
    </div>

    <!-- Modal 4: Permintaan Revisi Tender (Manajemen) -->
    <div x-show="reviseModal" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4" x-cloak>
        <div class="bg-white rounded-lg border border-slate-200 max-w-md w-full p-5 shadow-xl space-y-4">
            <div class="flex justify-between items-center border-b border-slate-200 pb-3">
                <div>
                    <h3 class="font-bold text-sm text-slate-900">Minta Revisi Tender</h3>
                    <p class="text-xs text-slate-500 font-mono" x-text="selectedTender ? selectedTender.tender_number : ''"></p>
                </div>
                <button @click="reviseModal = false" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-sm"></i></button>
            </div>

            <form x-bind:action="selectedTender ? '/tender/' + selectedTender.id + '/revise' : '#'" method="POST" class="space-y-3.5">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Catatan Poin Revisi <span class="text-rose-500">*</span></label>
                    <textarea name="revisi_notes" required rows="4" placeholder="Tuliskan bagian yang perlu diperbaiki oleh Kepala Unit / Admin (misal kelengkapan TOR, penyesuaian nilai penawaran, dsb)..." class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-200">
                    <button type="button" @click="reviseModal = false" class="px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium text-xs rounded-md transition">Batal</button>
                    <button type="submit" class="px-4 py-1.5 bg-amber-600 hover:bg-amber-700 text-white font-semibold text-xs rounded-md transition shadow-xs flex items-center gap-1">
                        <i class="fa-solid fa-rotate-left text-[10px]"></i> Kirim Permintaan Revisi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal 5: Upload Dokumen -->
    <div x-show="uploadModal" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4" x-cloak>
        <div class="bg-white rounded-lg border border-slate-200 max-w-md w-full p-5 shadow-xl space-y-4">
            <div class="flex justify-between items-center border-b border-slate-200 pb-3">
                <h3 class="font-bold text-sm text-slate-900">Unggah Dokumen Tender</h3>
                <button @click="uploadModal = false" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-sm"></i></button>
            </div>

            <form x-bind:action="'/tender/' + (selectedTender ? selectedTender.id : 0) + '/upload'" method="POST" enctype="multipart/form-data" class="space-y-3.5">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Nama Dokumen</label>
                    <input type="text" name="document_name" required placeholder="Contoh: RKS, Dokumen Penawaran, TOR..." class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Pilih File (PDF, Word, Zip, maks 10MB)</label>
                    <input type="file" name="file" required class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-medium file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200">
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-200">
                    <button type="button" @click="uploadModal = false" class="px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium text-xs rounded-md transition">Batal</button>
                    <button type="submit" class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs rounded-md transition shadow-xs">Unggah File</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal 6: Update Pipeline Status -->
    <div x-show="statusModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs" x-cloak>
        <div class="w-full max-w-lg overflow-hidden rounded-lg bg-white border border-slate-200 shadow-xl p-5 space-y-4">
            <div class="flex items-start justify-between border-b border-slate-200 pb-3">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Update Status Pipeline Tender</h3>
                    <p class="text-xs text-slate-500">Pindahkan tahapan lelang sesuai progres riil saat ini</p>
                </div>
                <button type="button" @click="statusModal = false" class="text-slate-400 hover:text-slate-600 transition">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <form x-bind:action="selectedTender ? '/tender/' + selectedTender.id : '#'" method="POST" class="space-y-3.5">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Tahapan Pipeline Baru <span class="text-rose-500">*</span></label>
                    <select name="status" x-model="statusForm.status" required class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs font-medium outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                        <option value="Ditemukan">1. Ditemukan (Info tender diidentifikasi)</option>
                        <option value="Evaluasi">2. Evaluasi (Kajian kelayakan teknis)</option>
                        <option value="Persiapan Dokumen">3. Persiapan Dokumen (Penyusunan proposal & berkas)</option>
                        <option value="Penawaran">4. Penawaran (Dokumen penawaran diajukan)</option>
                        <option value="Menang">5. Menang (Dinyatakan menang lelang)</option>
                        <option value="Kalah">6. Kalah (Tidak terpilih)</option>
                        <option value="Kontrak">7. Kontrak (Menjadi kontrak resmi)</option>
                        <option value="Pelaksanaan">8. Pelaksanaan (Pekerjaan sedang berjalan)</option>
                        <option value="Selesai">9. Selesai (Pekerjaan selesai 100%)</option>
                        <option value="Batal">10. Batal (Dibatalkan oleh panitia/klien)</option>
                    </select>
                </div>

                <div x-show="statusForm.status === 'Penawaran' || statusForm.status === 'Menang'" class="space-y-1">
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Nilai Penawaran / Kontrak Akhir (Rp)</label>
                    <input type="number" name="bid_value" x-model="statusForm.bid_value" min="0" placeholder="Contoh: 485000000" class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500 font-mono">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Catatan Progres</label>
                    <textarea name="notes" x-model="statusForm.notes" rows="3" placeholder="Uraikan perkembangan lelang..." class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-200">
                    <button type="button" @click="statusModal = false" class="px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium text-xs rounded-md transition">Batal</button>
                    <button type="submit" class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs rounded-md transition flex items-center gap-1.5 shadow-xs">
                        <i class="fa-solid fa-check text-[11px]"></i> Simpan Status Baru
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal 7: Permohonan Izin Hapus (Admin) -->
    <div x-show="requestDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs" x-cloak>
        <div class="w-full max-w-md overflow-hidden rounded-lg bg-white border border-slate-200 shadow-xl p-5 space-y-4" @click.away="requestDeleteModal = false">
            <div class="flex items-start justify-between border-b border-slate-200 pb-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-lg bg-amber-50 border border-amber-200 text-amber-600 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-file-shield text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 leading-tight">Permohonan Izin Hapus Tender</h3>
                        <p class="text-[11px] text-slate-500 mt-0.5">Memerlukan persetujuan Manajemen</p>
                    </div>
                </div>
                <button type="button" @click="requestDeleteModal = false" class="text-slate-400 hover:text-slate-600 transition">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <!-- Detail Tender yang Dimintakan Hapus -->
            <div class="bg-slate-50 rounded-lg p-3 border border-slate-200 text-xs space-y-1.5">
                <div class="flex items-center justify-between">
                    <span class="text-slate-500">No. Tender:</span>
                    <span class="font-mono font-bold text-slate-900" x-text="selectedTender ? selectedTender.tender_number : ''"></span>
                </div>
                <div class="flex items-start justify-between gap-2">
                    <span class="text-slate-500 shrink-0">Nama Pekerjaan:</span>
                    <span class="font-semibold text-slate-800 text-right" x-text="selectedTender ? selectedTender.name : ''"></span>
                </div>
            </div>

            <!-- Catatan SOP Perusahaan -->
            <div class="bg-amber-50/70 border border-amber-200 rounded-lg p-3 text-xs text-amber-900 leading-relaxed">
                <div class="flex items-start gap-2">
                    <i class="fa-solid fa-circle-info text-amber-600 text-xs mt-0.5 shrink-0"></i>
                    <div>
                        <span class="font-bold">Kebijakan SOP PT Signal Panca Utama:</span>
                        <p class="mt-0.5 text-amber-800/90 text-[11px]">
                            Penghapusan data oleh Admin wajib memperoleh izin dari Manajemen (Manager atau Owner). Data tidak langsung terhapus sampai Manajemen menyetujuinya.
                        </p>
                    </div>
                </div>
            </div>

            <form x-bind:action="selectedTender ? '/tender/' + selectedTender.id + '/request-deletion' : '#'" method="POST" class="space-y-3.5">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                        Alasan Permohonan Penghapusan <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="deletion_reason" 
                              x-model="deletionReason" 
                              required 
                              minlength="5" 
                              rows="3" 
                              placeholder="Uraikan alasan jelas penghapusan (misal: data tender duplikat, salah input klien, atau lelang resmi dibatalkan panitia)..." 
                              class="w-full px-3 py-2 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-amber-500"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-200">
                    <button type="button" @click="requestDeleteModal = false" class="px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium text-xs rounded-md transition">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-1.5 bg-amber-600 hover:bg-amber-700 text-white font-semibold text-xs rounded-md transition flex items-center gap-1.5 shadow-xs">
                        <i class="fa-solid fa-paper-plane text-[11px]"></i> Kirim Permohonan ke Manajemen
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal 8: Persetujuan Izin Hapus (Manager / Owner) -->
    <div x-show="approveDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs" x-cloak>
        <div class="w-full max-w-md overflow-hidden rounded-lg bg-white border border-slate-200 shadow-xl p-5 space-y-4" @click.away="approveDeleteModal = false">
            <div class="flex items-start justify-between border-b border-slate-200 pb-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-lg bg-rose-50 border border-rose-200 text-rose-600 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-triangle-exclamation text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 leading-tight">Persetujuan Izin Hapus Data Tender</h3>
                        <p class="text-[11px] text-rose-600 font-medium mt-0.5">Tindakan Penghapusan Permanen</p>
                    </div>
                </div>
                <button type="button" @click="approveDeleteModal = false" class="text-slate-400 hover:text-slate-600 transition">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <!-- Detail Data & Alasan dari Admin -->
            <div class="bg-slate-50 rounded-lg p-3 border border-slate-200 text-xs space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-slate-500">No. Tender:</span>
                    <span class="font-mono font-bold text-slate-900" x-text="selectedTender ? selectedTender.tender_number : ''"></span>
                </div>
                <div class="flex items-start justify-between gap-2">
                    <span class="text-slate-500 shrink-0">Nama Tender:</span>
                    <span class="font-semibold text-slate-800 text-right" x-text="selectedTender ? selectedTender.name : ''"></span>
                </div>
                <div class="pt-2 border-t border-slate-200">
                    <span class="text-slate-500 block text-[11px]">Alasan Permohonan Hapus oleh Admin:</span>
                    <p class="mt-1 p-2 bg-white rounded border border-slate-200 text-slate-800 font-medium italic text-[11px]" x-text="selectedTender ? selectedTender.deletion_reason : '-'"></p>
                </div>
                <div class="text-[10px] text-slate-400" x-show="selectedTender && selectedTender.deletion_requester">
                    Diajukan oleh: <span class="text-slate-600 font-medium" x-text="selectedTender?.deletion_requester?.name || 'Admin'"></span>
                </div>
            </div>

            <div class="bg-rose-50 border border-rose-200 rounded-lg p-3 text-xs text-rose-800 leading-relaxed flex items-start gap-2">
                <i class="fa-solid fa-circle-exclamation text-rose-600 text-xs mt-0.5 shrink-0"></i>
                <p class="text-[11px]">
                    Dengan menyetujui, data tender beserta seluruh dokumen lampiran dan estimasi RAB akan <strong>dihapus secara permanen</strong> dari basis data.
                </p>
            </div>

            <form x-bind:action="selectedTender ? '/tender/' + selectedTender.id + '/approve-deletion' : '#'" method="POST">
                @csrf
                <div class="flex justify-end gap-2 pt-3 border-t border-slate-200">
                    <button type="button" @click="approveDeleteModal = false" class="px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium text-xs rounded-md transition">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-1.5 bg-rose-600 hover:bg-rose-700 text-white font-semibold text-xs rounded-md transition flex items-center gap-1.5 shadow-xs">
                        <i class="fa-solid fa-trash-can text-[11px]"></i> Ya, Izinkan & Hapus Permanen
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal 9: Tolak Izin Hapus (Manager / Owner) -->
    <div x-show="rejectDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs" x-cloak>
        <div class="w-full max-w-md overflow-hidden rounded-lg bg-white border border-slate-200 shadow-xl p-5 space-y-4" @click.away="rejectDeleteModal = false">
            <div class="flex items-start justify-between border-b border-slate-200 pb-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-lg bg-slate-100 border border-slate-200 text-slate-700 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-shield-halved text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 leading-tight">Tolak Permohonan Izin Hapus</h3>
                        <p class="text-[11px] text-slate-500 mt-0.5">Pertahankan data tender di sistem</p>
                    </div>
                </div>
                <button type="button" @click="rejectDeleteModal = false" class="text-slate-400 hover:text-slate-600 transition">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <div class="bg-slate-50 rounded-lg p-3 border border-slate-200 text-xs space-y-1.5">
                <div class="flex items-center justify-between">
                    <span class="text-slate-500">No. Tender:</span>
                    <span class="font-mono font-bold text-slate-900" x-text="selectedTender ? selectedTender.tender_number : ''"></span>
                </div>
                <div class="flex items-start justify-between gap-2">
                    <span class="text-slate-500 shrink-0">Nama Tender:</span>
                    <span class="font-semibold text-slate-800 text-right" x-text="selectedTender ? selectedTender.name : ''"></span>
                </div>
            </div>

            <form x-bind:action="selectedTender ? '/tender/' + selectedTender.id + '/reject-deletion' : '#'" method="POST" class="space-y-3.5">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                        Catatan Penolakan untuk Admin (Opsional)
                    </label>
                    <textarea name="notes" 
                              x-model="rejectReason" 
                              rows="3" 
                              placeholder="Tuliskan catatan alasan mengapa permohonan hapus data ini ditolak..." 
                              class="w-full px-3 py-2 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-slate-400"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-200">
                    <button type="button" @click="rejectDeleteModal = false" class="px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium text-xs rounded-md transition">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-1.5 bg-slate-700 hover:bg-slate-800 text-white font-semibold text-xs rounded-md transition flex items-center gap-1.5 shadow-xs">
                        <i class="fa-solid fa-ban text-[11px]"></i> Tolak Permohonan Hapus
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
