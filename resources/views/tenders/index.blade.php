@extends('layouts.app')

@section('title', 'Manajemen Tender')
@section('header-title', 'Pipeline & Dokumen Tender')

@section('content')
<div class="space-y-6" x-data="{
    createModal: false,
    uploadModal: false,
    evaluationModal: false,
    statusModal: false,
    editModal: false,
    selectedTender: null,
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
        this.editModal = true;
    }
}">

    @if(auth()->user()->isOwner())
        <div class="border border-slate-200 bg-white rounded-lg p-3.5 flex items-center justify-between shadow-xs">
            <div>
                <span class="text-xs font-semibold text-slate-900 block leading-tight">Akses Pemantauan Tender (Read-Only)</span>
                <p class="text-xs text-slate-500">Sebagai Owner, Anda memiliki akses peninjauan status pipeline dan histori tender tanpa kewenangan edit langsung.</p>
            </div>
            <a href="{{ route('laporan.index', ['domain' => 'tender']) }}" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-xs font-semibold transition shadow-xs">
                Buka Laporan Tender
            </a>
        </div>
    @endif

    <!-- Action & Filter Bar -->
    <div class="bg-white rounded-lg p-3 border border-slate-200 shadow-xs flex flex-col md:flex-row justify-between items-center gap-3">
        <form method="GET" action="{{ route('tender.index') }}" class="flex flex-wrap items-center gap-2 w-full md:w-auto">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nomor tender / nama..." class="px-2.5 py-1.5 rounded-md border border-slate-200 text-xs w-56 outline-none">
            
            <select name="status" onchange="this.form.submit()" class="px-2.5 py-1.5 rounded-md border border-slate-200 text-xs font-medium text-slate-700 bg-white hover:bg-slate-50 outline-none">
                <option value="">Semua Status Pipeline</option>
                <option value="Ditemukan" {{ request('status') == 'Ditemukan' ? 'selected' : '' }}>Ditemukan</option>
                <option value="Evaluasi" {{ request('status') == 'Evaluasi' ? 'selected' : '' }}>Evaluasi</option>
                <option value="Persiapan Dokumen" {{ request('status') == 'Persiapan Dokumen' ? 'selected' : '' }}>Persiapan Dokumen</option>
                <option value="Penawaran" {{ request('status') == 'Penawaran' ? 'selected' : '' }}>Penawaran</option>
                <option value="Menang" {{ request('status') == 'Menang' ? 'selected' : '' }}>Menang</option>
                <option value="Kalah" {{ request('status') == 'Kalah' ? 'selected' : '' }}>Kalah</option>
                <option value="Kontrak" {{ request('status') == 'Kontrak' ? 'selected' : '' }}>Kontrak</option>
            </select>

            <button type="submit" class="px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 font-semibold text-xs rounded-md transition">
                Filter
            </button>
        </form>

        @if(auth()->user()->role?->name !== 'owner')
            <button @click="createModal = true" class="w-full md:w-auto px-3.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs rounded-md shadow-xs transition flex items-center justify-center gap-1.5">
                <i class="fa-solid fa-plus text-[11px]"></i> Tambah Tender
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
                        <th class="p-3">Pengerjaan & Approval</th>
                        <th class="p-3">Est. Nilai / Penawaran</th>
                        <th class="p-3">Deadline</th>
                        <th class="p-3">Status Pipeline</th>
                        <th class="p-3">Dokumen</th>
                        <th class="p-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($tenders as $tender)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="p-3 font-mono font-medium text-slate-900">
                                {{ $tender->tender_number }}
                            </td>
                            <td class="p-3">
                                <div class="font-medium text-slate-800">{{ $tender->name }}</div>
                                <div class="text-[11px] text-slate-500">{{ $tender->client->name ?? 'Instansi N/A' }}</div>
                            </td>
                            <td class="p-3">
                                <div class="space-y-1">
                                    @if($tender->metode_penanganan === 'vendor_relasi')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-medium bg-amber-50 text-amber-800 border border-amber-200">
                                            Vendor: {{ $tender->nama_vendor_relasi ?: 'Relasi' }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                            Internal SPU
                                        </span>
                                    @endif

                                    <div>
                                        @if(($tender->approval_status ?? 'approved') === 'pending')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-medium bg-amber-50 text-amber-700 border border-amber-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Pending
                                            </span>
                                        @elseif($tender->approval_status === 'approved')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Disetujui
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-medium bg-rose-50 text-rose-700 border border-rose-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Ditolak
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="p-4">
                                <div class="font-bold text-slate-800">Rp {{ number_format($tender->bid_value, 0, ',', '.') }}</div>
                                <div class="text-[11px] text-slate-400">Est: Rp {{ number_format($tender->estimated_value, 0, ',', '.') }}</div>
                            </td>
                            <td class="p-4">
                                @if($tender->deadline)
                                    <div class="font-medium text-slate-700">{{ \Carbon\Carbon::parse($tender->deadline)->format('d M Y') }}</div>
                                    <div class="text-[11px] text-amber-600 font-medium">{{ \Carbon\Carbon::parse($tender->deadline)->diffForHumans() }}</div>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="p-3 whitespace-nowrap">
                                <button type="button" @click="openStatusModal({{ $tender }})" class="inline-flex items-center gap-1 px-2.5 py-1 rounded text-xs font-medium bg-slate-100 hover:bg-slate-200 text-slate-700 transition cursor-pointer" title="Klik untuk mengubah tahapan pipeline">
                                    <span>{{ $tender->status }}</span>
                                    <i class="fa-solid fa-pen text-[9px] text-slate-400"></i>
                                </button>
                            </td>
                            <td class="p-3">
                                @if($tender->documents->isNotEmpty())
                                    <div class="space-y-1">
                                        @foreach($tender->documents as $document)
                                            <a href="{{ route('tender.documents.view', [$tender, $document]) }}" target="_blank" rel="noopener" class="flex items-center gap-1 max-w-40 text-xs text-slate-700 hover:text-slate-900 hover:underline" title="Dokumen: {{ $document->document_name }}">
                                                <i class="fa-solid fa-file text-slate-400 shrink-0 text-[11px]"></i>
                                                <span class="truncate">{{ $document->document_name }}</span>
                                            </a>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-xs text-slate-400">Tidak ada dokumen</span>
                                @endif
                            </td>
                            <td class="p-4 text-center whitespace-nowrap">
                                <div class="inline-flex items-center justify-center gap-1.5">
                                    @if($tender->status === 'Evaluasi')
                                        <button type="button" @click="selectedTender = {{ $tender }}; evaluationModal = true" class="px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white font-semibold text-xs rounded-lg shadow-sm transition inline-flex items-center gap-1" title="Form Evaluasi Tender">
                                            <i class="fa-solid fa-clipboard-check text-[11px]"></i> Evaluasi
                                        </button>
                                    @endif

                                    <button type="button" @click="openEditModal({{ $tender }})" class="w-8 h-8 flex items-center justify-center bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs rounded-lg transition" title="Edit Data Tender">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>

                                    <button @click="selectedTender = {{ $tender }}; uploadModal = true" class="w-8 h-8 flex items-center justify-center bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs rounded-lg transition" title="Upload Dokumen">
                                        <i class="fa-solid fa-upload"></i>
                                    </button>

                                    @if(auth()->user()->role?->name === 'manager' && ($tender->approval_status ?? 'approved') === 'pending')
                                        <form action="{{ route('tender.approve', $tender) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="px-2 py-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-lg transition shadow-xs flex items-center gap-1" title="Setujui Tender">
                                                <i class="fa-solid fa-check"></i> Setujui
                                            </button>
                                        </form>
                                        <form action="{{ route('tender.reject', $tender) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menolak tender ini?');">
                                            @csrf
                                            <button type="submit" class="px-2 py-1 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs rounded-lg transition shadow-xs flex items-center gap-1" title="Tolak Tender">
                                                <i class="fa-solid fa-xmark"></i> Tolak
                                            </button>
                                        </form>
                                    @endif

                                    @if(auth()->user()->role?->name !== 'owner')
                                        <form action="{{ route('tender.destroy', $tender) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data tender {{ $tender->tender_number }}?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-8 h-8 flex items-center justify-center bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs rounded-lg transition" title="Hapus Tender">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-slate-400 text-sm">Belum ada data tender. Silakan tambah tender baru.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100">
            {{ $tenders->links() }}
        </div>
    </div>

    <!-- Modal 1: Create Tender -->
    <div x-show="createModal" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4" x-cloak>
        <div class="bg-white rounded-lg border border-slate-200 max-w-xl w-full p-5 shadow-xl space-y-4 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center border-b border-slate-200 pb-3">
                <h3 class="font-bold text-sm text-slate-900">Input Tender Baru</h3>
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
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Klien / Instansi</label>
                        <select name="client_id" required class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                            @foreach($clients as $c)
                                <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->company_name ?? '-' }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Nama Tender Pekerjaan</label>
                    <input type="text" name="name" required placeholder="Pengadaan Sistem Informasi..." class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Tanggal Ditemukan</label>
                        <input type="date" name="found_date" required value="{{ date('Y-m-d') }}" class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Deadline Tender</label>
                        <input type="date" name="deadline" class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Nilai Estimasi (Rp)</label>
                        <input type="number" name="estimated_value" required placeholder="500000000" class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500 font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Nilai Penawaran (Rp)</label>
                        <input type="number" name="bid_value" placeholder="480000000" class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500 font-mono">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Status Pipeline Awal</label>
                    <select name="status" required class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                        <option value="Ditemukan">Ditemukan</option>
                        <option value="Evaluasi">Evaluasi</option>
                        <option value="Persiapan Dokumen">Persiapan Dokumen</option>
                        <option value="Penawaran">Penawaran</option>
                        <option value="Menang">Menang</option>
                        <option value="Kalah">Kalah</option>
                    </select>
                </div>

                <!-- PRD Section 3: Metode Penanganan Pengerjaan -->
                <div x-data="{ metode: 'internal' }" class="border-t border-slate-200 pt-3">
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Metode Pengerjaan Tender</label>
                    <div class="grid grid-cols-2 gap-3 mb-2">
                        <label class="flex items-center gap-2 p-2.5 rounded-md border border-slate-200 cursor-pointer hover:bg-slate-50 transition" :class="metode === 'internal' ? 'border-slate-900 bg-slate-50 font-medium text-slate-900' : 'text-slate-600'">
                            <input type="radio" name="metode_penanganan" value="internal" x-model="metode" class="text-slate-900">
                            <span class="text-xs">Internal SPU (Sendiri)</span>
                        </label>
                        <label class="flex items-center gap-2 p-2.5 rounded-md border border-slate-200 cursor-pointer hover:bg-slate-50 transition" :class="metode === 'vendor_relasi' ? 'border-slate-900 bg-slate-50 font-medium text-slate-900' : 'text-slate-600'">
                            <input type="radio" name="metode_penanganan" value="vendor_relasi" x-model="metode" class="text-slate-900">
                            <span class="text-xs">Vendor Relasi (Mitra)</span>
                        </label>
                    </div>

                    <div x-show="metode === 'vendor_relasi'" class="space-y-1">
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Nama Vendor Relasi <span class="text-rose-500">*</span></label>
                        <input type="text" name="nama_vendor_relasi" placeholder="Nama mitra / sub-kontraktor eksternal..." class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-200">
                    <button type="button" @click="createModal = false" class="px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium text-xs rounded-md transition">Batal</button>
                    <button type="submit" class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs rounded-md transition shadow-xs">Simpan Tender</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal 3: Upload Document -->
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
                    <input type="text" name="document_name" required placeholder="Proposal Penawaran / Spesifikasi..." class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">File Dokumen (PDF, Docx, Zip)</label>
                    <input type="file" name="file" required class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-medium file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200">
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-200">
                    <button type="button" @click="uploadModal = false" class="px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium text-xs rounded-md transition">Batal</button>
                    <button type="submit" class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs rounded-md transition shadow-xs">Upload File</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal 4: Evaluation Tender -->
    <div x-show="evaluationModal" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4" x-cloak>
        <div class="bg-white rounded-lg border border-slate-200 max-w-md w-full p-5 shadow-xl space-y-4">
            <div class="flex justify-between items-center border-b border-slate-200 pb-3">
                <div>
                    <h3 class="font-bold text-sm text-slate-900">Evaluasi Tender</h3>
                    <p class="text-xs text-slate-500 font-mono" x-text="selectedTender ? selectedTender.tender_number : ''"></p>
                </div>
                <button type="button" @click="evaluationModal = false" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-sm"></i></button>
            </div>

            <form x-bind:action="'/tender/' + (selectedTender ? selectedTender.id : 0) + '/evaluations'" method="POST" class="space-y-3.5">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Skor Evaluasi (0 - 100)</label>
                    <input type="number" name="score" min="0" max="100" step="0.01" required placeholder="Contoh: 85.5" class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500 font-mono">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Keputusan Evaluasi</label>
                    <select name="decision" required class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                        <option value="Proceed">Lanjut (Proceed ke Penawaran)</option>
                        <option value="Hold">Tunda (Hold / Butuh Klarifikasi)</option>
                        <option value="Reject">Tolak (Reject / Tidak Memenuhi Syarat)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Catatan Teknis & Komersial</label>
                    <textarea name="notes" rows="3" placeholder="Tuliskan catatan kelayakan tender..." class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-200">
                    <button type="button" @click="evaluationModal = false" class="px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium text-xs rounded-md transition">Batal</button>
                    <button type="submit" class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs rounded-md transition shadow-xs">Simpan Evaluasi</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal 5: Update Pipeline Status -->
    <div x-show="statusModal" x-cloak style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs" @keydown.escape.window="statusModal = false">
        <div @click.outside="statusModal = false" class="w-full max-w-lg overflow-hidden rounded-lg bg-white border border-slate-200 shadow-xl p-5 space-y-4">
            <div class="flex items-start justify-between border-b border-slate-200 pb-3">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Update Status Pipeline Tender</h3>
                    <p class="text-xs text-slate-500">Pindahkan tahapan tender sesuai progres lelang saat ini</p>
                </div>
                <button type="button" @click="statusModal = false" class="text-slate-400 hover:text-slate-600 transition" aria-label="Tutup modal status">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <div class="rounded-md border border-slate-200 bg-slate-50 p-3 space-y-1 text-xs">
                <div class="flex justify-between">
                    <span class="text-slate-500">Nomor Tender:</span>
                    <span class="font-mono font-medium text-slate-900" x-text="selectedTender?.tender_number"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Nama Tender:</span>
                    <span class="font-semibold text-slate-800 text-right truncate max-w-[280px]" x-text="selectedTender?.name"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Status Saat Ini:</span>
                    <span class="font-semibold text-slate-900" x-text="selectedTender?.status"></span>
                </div>
            </div>

            <form x-bind:action="selectedTender ? '/tender/' + selectedTender.id : '#'" method="POST" class="space-y-3.5">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Pilih Tahapan Pipeline Baru <span class="text-rose-500">*</span></label>
                    <select name="status" x-model="statusForm.status" required class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs font-medium outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                        <option value="Ditemukan">1. Ditemukan (Info tender baru diidentifikasi)</option>
                        <option value="Evaluasi">2. Evaluasi (Kajian teknis & kelayakan bisnis)</option>
                        <option value="Persiapan Dokumen">3. Persiapan Dokumen (Penyusunan berkas & administrasi)</option>
                        <option value="Penawaran">4. Penawaran (Dokumen penawaran harga diajukan)</option>
                        <option value="Menang">5. Menang (Dinyatakan menang, lanjut ke kontrak)</option>
                        <option value="Kalah">6. Kalah (Gagal / tidak terpilih)</option>
                        <option value="Kontrak">7. Kontrak (Resmi menjadi kontrak kerja)</option>
                        <option value="Batal">8. Batal (Tender dibatalkan oleh pihak klien)</option>
                    </select>
                </div>

                <div x-show="statusForm.status === 'Penawaran' || statusForm.status === 'Menang'" x-transition class="space-y-1">
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Nilai Penawaran Final (Rp)</label>
                    <input type="number" name="bid_value" x-model="statusForm.bid_value" min="0" placeholder="Contoh: 4850000" class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500 font-mono">
                    <p class="text-[11px] text-slate-400">Update nilai penawaran riil yang diajukan atau disepakati.</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Catatan Progres / Alasan Perubahan</label>
                    <textarea name="notes" x-model="statusForm.notes" rows="3" placeholder="Contoh: Panitia mengumumkan hasil evaluasi dokumen penawaran harga..." class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500"></textarea>
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

    <!-- Modal 6: Edit Full Tender Data -->
    <div x-show="editModal" x-cloak style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs" @keydown.escape.window="editModal = false">
        <div @click.outside="editModal = false" class="w-full max-w-xl overflow-hidden rounded-lg bg-white border border-slate-200 shadow-xl p-5 space-y-4 max-h-[90vh] overflow-y-auto">
            <div class="flex items-start justify-between border-b border-slate-200 pb-3">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Edit Data Tender</h3>
                    <p class="text-xs text-slate-500">Edit informasi dan tahapan tender</p>
                </div>
                <button type="button" @click="editModal = false" class="text-slate-400 hover:text-slate-600 transition" aria-label="Tutup modal edit">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
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
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Klien / Instansi</label>
                        <select name="client_id" required class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                            @foreach($clients as $c)
                                <option value="{{ $c->id }}" x-bind:selected="selectedTender?.client_id == {{ $c->id }}">{{ $c->name }} ({{ $c->company_name ?? '-' }})</option>
                            @endforeach
                        </select>
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
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Deadline Tender</label>
                        <input type="date" name="deadline" x-bind:value="selectedTender?.deadline ? selectedTender.deadline.substring(0,10) : ''" class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Nilai Estimasi (Rp) <span class="text-rose-500">*</span></label>
                        <input type="number" name="estimated_value" required min="0" x-bind:value="selectedTender?.estimated_value" class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500 font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Nilai Penawaran (Rp)</label>
                        <input type="number" name="bid_value" min="0" x-bind:value="selectedTender?.bid_value" class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500 font-mono">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Status Pipeline <span class="text-rose-500">*</span></label>
                    <select name="status" required class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                        <option value="Ditemukan" x-bind:selected="selectedTender?.status === 'Ditemukan'">Ditemukan</option>
                        <option value="Evaluasi" x-bind:selected="selectedTender?.status === 'Evaluasi'">Evaluasi</option>
                        <option value="Persiapan Dokumen" x-bind:selected="selectedTender?.status === 'Persiapan Dokumen'">Persiapan Dokumen</option>
                        <option value="Penawaran" x-bind:selected="selectedTender?.status === 'Penawaran'">Penawaran</option>
                        <option value="Menang" x-bind:selected="selectedTender?.status === 'Menang'">Menang</option>
                        <option value="Kalah" x-bind:selected="selectedTender?.status === 'Kalah'">Kalah</option>
                        <option value="Kontrak" x-bind:selected="selectedTender?.status === 'Kontrak'">Kontrak</option>
                        <option value="Batal" x-bind:selected="selectedTender?.status === 'Batal'">Batal</option>
                    </select>
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

</div>
@endsection
