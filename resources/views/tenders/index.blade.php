@extends('layouts.app')

@section('title', 'Manajemen Tender')
@section('header-title', 'Pipeline & Dokumen Tender')

@section('content')
<div class="space-y-6" x-data="{
    createModal: false,
    uploadModal: false,
    convertModal: false,
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

    <!-- Action & Filter Bar -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex flex-col md:flex-row justify-between items-center gap-4">
        <form method="GET" action="{{ route('tender.index') }}" class="flex flex-wrap items-center gap-3 w-full md:w-auto">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nomor tender / nama..." class="px-4 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500 outline-none w-64">
            
            <select name="status" onchange="this.form.submit()" class="px-4 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                <option value="">Semua Status Pipeline</option>
                <option value="Ditemukan" {{ request('status') == 'Ditemukan' ? 'selected' : '' }}>Ditemukan</option>
                <option value="Evaluasi" {{ request('status') == 'Evaluasi' ? 'selected' : '' }}>Evaluasi</option>
                <option value="Persiapan Dokumen" {{ request('status') == 'Persiapan Dokumen' ? 'selected' : '' }}>Persiapan Dokumen</option>
                <option value="Penawaran" {{ request('status') == 'Penawaran' ? 'selected' : '' }}>Penawaran</option>
                <option value="Menang" {{ request('status') == 'Menang' ? 'selected' : '' }}>Menang</option>
                <option value="Kalah" {{ request('status') == 'Kalah' ? 'selected' : '' }}>Kalah</option>
                <option value="Kontrak" {{ request('status') == 'Kontrak' ? 'selected' : '' }}>Kontrak</option>
            </select>

            <button type="submit" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm rounded-xl transition">
                <i class="fa-solid fa-filter"></i> Filter
            </button>
        </form>

        <button @click="createModal = true" class="w-full md:w-auto px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-xl shadow-md transition flex items-center justify-center gap-2">
            <i class="fa-solid fa-plus"></i> Tambah Tender Baru
        </button>
    </div>

    <!-- Data Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-200">
                        <th class="p-4">No. Tender</th>
                        <th class="p-4">Nama Tender & Klien</th>
                        <th class="p-4">Est. Nilai / Penawaran</th>
                        <th class="p-4">Deadline</th>
                        <th class="p-4">Status Pipeline</th>
                        <th class="p-4">Dokumen</th>
                        <th class="p-4">Kebutuhan Barang</th>
                        <th class="p-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($tenders as $tender)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="p-4 font-mono font-bold text-blue-600">
                                {{ $tender->tender_number }}
                            </td>
                            <td class="p-4">
                                <div class="font-bold text-slate-800">{{ $tender->name }}</div>
                                <div class="text-xs text-slate-500"><i class="fa-solid fa-building"></i> {{ $tender->client->name ?? 'Instansi N/A' }}</div>
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
                            <td class="p-4 whitespace-nowrap">
                                @php
                                    $badgeColor = match($tender->status) {
                                        'Ditemukan' => 'bg-slate-100 text-slate-700 border-slate-300 hover:bg-slate-200',
                                        'Evaluasi' => 'bg-amber-50 text-amber-700 border-amber-200 hover:bg-amber-100',
                                        'Persiapan Dokumen' => 'bg-blue-50 text-blue-700 border-blue-200 hover:bg-blue-100',
                                        'Penawaran' => 'bg-purple-50 text-purple-700 border-purple-200 hover:bg-purple-100',
                                        'Menang' => 'bg-emerald-100 text-emerald-800 border-emerald-300 font-bold hover:bg-emerald-200',
                                        'Kalah' => 'bg-rose-100 text-rose-800 border-rose-200 hover:bg-rose-200',
                                        'Kontrak' => 'bg-indigo-100 text-indigo-800 border-indigo-300 hover:bg-indigo-200',
                                        default => 'bg-slate-100 text-slate-700 hover:bg-slate-200'
                                    };
                                @endphp
                                <button type="button" @click="openStatusModal({{ $tender }})" class="group inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold border {{ $badgeColor }} transition cursor-pointer shadow-sm hover:scale-105" title="Klik untuk mengubah status pipeline">
                                    <span>{{ $tender->status }}</span>
                                    <i class="fa-solid fa-pen text-[10px] opacity-40 group-hover:opacity-100 transition"></i>
                                </button>
                            </td>
                            <td class="p-4">
                                @if($tender->documents->isNotEmpty())
                                    <div class="space-y-1.5">
                                        @foreach($tender->documents as $document)
                                            <a href="{{ route('tender.documents.view', [$tender, $document]) }}" target="_blank" rel="noopener" class="flex items-center gap-1.5 max-w-40 text-xs font-semibold text-blue-600 hover:text-blue-800 hover:underline" title="Buka dokumen terbaru: {{ $document->document_name }}">
                                                <i class="fa-solid fa-file-arrow-up text-slate-400 shrink-0"></i>
                                                <span class="truncate">{{ $document->document_name }}</span>
                                            </a>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-xs text-slate-400">
                                        <i class="fa-solid fa-paperclip text-slate-400"></i> Belum ada dokumen
                                    </span>
                                @endif
                            </td>
                            <td class="p-4">
                                @if($tender->items->isNotEmpty())
                                    <div class="text-xs text-slate-600 space-y-1">
                                        @foreach($tender->items->take(2) as $item)
                                            <div>{{ $item->item_name }} <span class="text-slate-400">({{ rtrim(rtrim(number_format($item->quantity, 2, ',', '.'), '0'), ',') }} {{ $item->unit }})</span></div>
                                        @endforeach
                                        @if($tender->items->count() > 2)<div class="text-blue-600">+{{ $tender->items->count() - 2 }} item lain</div>@endif
                                    </div>
                                @else
                                    <span class="text-xs text-slate-400">Belum diisi</span>
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

                                    @if(auth()->user()->role?->name === 'super_admin')
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
    <div x-show="createModal" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4" x-cloak>
        <div class="bg-white rounded-2xl max-w-xl w-full p-6 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center border-b pb-3">
                <h3 class="font-bold text-lg text-slate-800">Input Tender Baru</h3>
                <button @click="createModal = false" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>

            <form action="{{ route('tender.store') }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Nomor Tender</label>
                        <input type="text" name="tender_number" required value="TDR-{{ date('Ymd') }}-{{ rand(100,999) }}" class="w-full px-3 py-2 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Klien / Instansi</label>
                        <select name="client_id" required class="w-full px-3 py-2 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500">
                            @foreach($clients as $c)
                                <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->company_name ?? '-' }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Nama Tender Pekerjaan</label>
                    <input type="text" name="name" required placeholder="Pengadaan Sistem Informasi..." class="w-full px-3 py-2 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Tanggal Ditemukan</label>
                        <input type="date" name="found_date" required value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Deadline Tender</label>
                        <input type="date" name="deadline" class="w-full px-3 py-2 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Nilai Estimasi (Rp)</label>
                        <input type="number" name="estimated_value" required placeholder="500000000" class="w-full px-3 py-2 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Nilai Penawaran (Rp)</label>
                        <input type="number" name="bid_value" placeholder="480000000" class="w-full px-3 py-2 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Status Pipeline Tahap Awal</label>
                    <select name="status" required class="w-full px-3 py-2 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="Ditemukan">Ditemukan</option>
                        <option value="Evaluasi">Evaluasi</option>
                        <option value="Persiapan Dokumen">Persiapan Dokumen</option>
                        <option value="Penawaran">Penawaran</option>
                        <option value="Menang">Menang</option>
                        <option value="Kalah">Kalah</option>
                    </select>
                </div>

                <div class="border-t pt-4">
                    <label class="block text-xs font-semibold text-slate-700 uppercase mb-2">Kebutuhan Barang Tender <span class="font-normal normal-case text-slate-400">(opsional)</span></label>
                    <div class="grid grid-cols-[1fr_90px_90px] gap-2">
                        <input name="items[0][item_name]" placeholder="Laptop / Printer / ATK" class="px-3 py-2 border rounded-xl text-sm">
                        <input name="items[0][quantity]" type="number" min="0.01" step="0.01" placeholder="Qty" class="px-3 py-2 border rounded-xl text-sm">
                        <input name="items[0][unit]" placeholder="Unit" class="px-3 py-2 border rounded-xl text-sm">
                    </div>
                    <p class="mt-1 text-[11px] text-slate-400">Tambahkan item lain setelah tender tersimpan melalui modul pengadaan.</p>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t">
                    <button type="button" @click="createModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm rounded-xl">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-xl shadow">Simpan Tender</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal 2: Convert to Contract -->
    <div x-show="convertModal" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4" x-cloak>
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4">
            <div class="flex justify-between items-center border-b pb-3">
                <h3 class="font-bold text-lg text-slate-800">Konversi Tender ke Kontrak Jasa</h3>
                <button @click="convertModal = false" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>

            <form x-bind:action="'/tender/' + (selectedTender ? selectedTender.id : 0) + '/convert-contract'" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Nomor Kontrak Baru</label>
                    <input type="text" name="contract_number" required x-bind:value="'CTR-' + (selectedTender ? selectedTender.tender_number : '')" class="w-full px-3 py-2 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Tanggal Mulai</label>
                        <input type="date" name="start_date" required value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Tanggal Berakhir</label>
                        <input type="date" name="end_date" required value="{{ date('Y-m-d', strtotime('+1 year')) }}" class="w-full px-3 py-2 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Nilai Kontrak (Rp)</label>
                        <input type="number" name="contract_value" required x-bind:value="selectedTender ? selectedTender.bid_value : 0" class="w-full px-3 py-2 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Komisi / Fee (%)</label>
                        <input type="number" step="0.1" name="fee_percentage" value="5" class="w-full px-3 py-2 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t">
                    <button type="button" @click="convertModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm rounded-xl">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm rounded-xl shadow">Generate Kontrak</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal 3: Upload Document -->
    <div x-show="uploadModal" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4" x-cloak>
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
            <div class="flex justify-between items-center border-b pb-3">
                <h3 class="font-bold text-lg text-slate-800">Unggah Dokumen Tender</h3>
                <button @click="uploadModal = false" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>

            <form x-bind:action="'/tender/' + (selectedTender ? selectedTender.id : 0) + '/upload'" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Nama Dokumen</label>
                    <input type="text" name="document_name" required placeholder="Proposal Penawaran / Spesifikasi..." class="w-full px-3 py-2 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">File Dokumen (PDF, Docx, Zip)</label>
                    <input type="file" name="file" required class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t">
                    <button type="button" @click="uploadModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm rounded-xl">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-xl shadow">Upload File</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal 4: Evaluation Tender -->
    <div x-show="evaluationModal" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4" x-cloak>
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
            <div class="flex justify-between items-center border-b pb-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center font-bold">
                        <i class="fa-solid fa-clipboard-check text-base"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-base text-slate-800">Evaluasi Tender</h3>
                        <p class="text-xs text-slate-500 font-mono" x-text="selectedTender ? selectedTender.tender_number : ''"></p>
                    </div>
                </div>
                <button type="button" @click="evaluationModal = false" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>

            <form x-bind:action="'/tender/' + (selectedTender ? selectedTender.id : 0) + '/evaluations'" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Skor Evaluasi (0 - 100)</label>
                    <input type="number" name="score" min="0" max="100" step="0.01" required placeholder="Contoh: 85.5" class="w-full px-3 py-2 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-amber-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Keputusan Evaluasi</label>
                    <select name="decision" required class="w-full px-3 py-2 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-amber-500 bg-white">
                        <option value="Proceed">Lanjut (Proceed ke Penawaran)</option>
                        <option value="Hold">Tunda (Hold / Butuh Klarifikasi)</option>
                        <option value="Reject">Tolak (Reject / Tidak Memenuhi Syarat)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Catatan Teknis & Komersial</label>
                    <textarea name="notes" rows="3" placeholder="Tuliskan catatan kelayakan tender..." class="w-full px-3 py-2 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-amber-500"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t">
                    <button type="button" @click="evaluationModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm rounded-xl">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-amber-600 hover:bg-amber-700 text-white font-semibold text-sm rounded-xl shadow">Simpan Evaluasi</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal 5: Update Pipeline Status -->
    <div x-show="statusModal" x-cloak style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" @keydown.escape.window="statusModal = false">
        <div @click.outside="statusModal = false" class="w-full max-w-lg overflow-hidden rounded-2xl bg-white shadow-2xl p-6 space-y-4">
            <div class="flex items-start justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                        <i class="fa-solid fa-arrows-spin text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">Update Status Pipeline Tender</h3>
                        <p class="text-xs font-medium text-slate-400">Pindahkan tahapan tender sesuai progres lelang saat ini</p>
                    </div>
                </div>
                <button type="button" @click="statusModal = false" class="text-slate-400 hover:text-slate-600 transition" aria-label="Tutup modal status">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <div class="rounded-xl border border-slate-200 bg-slate-50/70 p-3.5 space-y-1 text-xs">
                <div class="flex justify-between">
                    <span class="text-slate-500">Nomor Tender:</span>
                    <span class="font-mono font-bold text-blue-700" x-text="selectedTender?.tender_number"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Nama Tender:</span>
                    <span class="font-semibold text-slate-800 text-right truncate max-w-[280px]" x-text="selectedTender?.name"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Status Saat Ini:</span>
                    <span class="font-bold text-purple-700" x-text="selectedTender?.status"></span>
                </div>
            </div>

            <form x-bind:action="selectedTender ? '/tender/' + selectedTender.id : '#'" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase mb-1.5">Pilih Tahapan Pipeline Baru <span class="text-rose-500">*</span></label>
                    <select name="status" x-model="statusForm.status" required class="w-full px-3.5 py-2.5 border rounded-xl text-sm font-medium outline-none focus:ring-2 focus:ring-blue-500 bg-white border-slate-200">
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
                    <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Nilai Penawaran Final (Rp)</label>
                    <input type="number" name="bid_value" x-model="statusForm.bid_value" min="0" placeholder="Contoh: 4850000" class="w-full px-3.5 py-2 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500 border-slate-200">
                    <p class="text-[11px] text-slate-400">Update nilai penawaran riil yang diajukan atau disepakati.</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Catatan Progres / Alasan Perubahan</label>
                    <textarea name="notes" x-model="statusForm.notes" rows="3" placeholder="Contoh: Panitia mengumumkan hasil evaluasi dokumen penawaran harga..." class="w-full px-3.5 py-2 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500 border-slate-200"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="button" @click="statusModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm rounded-xl transition">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-xl shadow transition flex items-center gap-1.5">
                        <i class="fa-solid fa-check"></i> Simpan Status Baru
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal 6: Edit Full Tender Data -->
    <div x-show="editModal" x-cloak style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" @keydown.escape.window="editModal = false">
        <div @click.outside="editModal = false" class="w-full max-w-xl overflow-hidden rounded-2xl bg-white shadow-2xl p-6 space-y-4 max-h-[90vh] overflow-y-auto">
            <div class="flex items-start justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 text-amber-600">
                        <i class="fa-solid fa-pen-to-square text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">Edit Data Tender</h3>
                        <p class="text-xs font-medium text-slate-400">Edit informasi dan tahapan tender</p>
                    </div>
                </div>
                <button type="button" @click="editModal = false" class="text-slate-400 hover:text-slate-600 transition" aria-label="Tutup modal edit">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form x-bind:action="selectedTender ? '/tender/' + selectedTender.id : '#'" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Nomor Tender</label>
                        <input type="text" disabled x-bind:value="selectedTender?.tender_number" class="w-full px-3 py-2 border rounded-xl text-sm bg-slate-100 text-slate-500 font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Klien / Instansi</label>
                        <select name="client_id" required class="w-full px-3 py-2 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                            @foreach($clients as $c)
                                <option value="{{ $c->id }}" x-bind:selected="selectedTender?.client_id == {{ $c->id }}">{{ $c->name }} ({{ $c->company_name ?? '-' }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Nama Tender Pekerjaan <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" required x-bind:value="selectedTender?.name" class="w-full px-3 py-2 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Tanggal Ditemukan</label>
                        <input type="date" name="found_date" required x-bind:value="selectedTender?.found_date ? selectedTender.found_date.substring(0,10) : ''" class="w-full px-3 py-2 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Deadline Tender</label>
                        <input type="date" name="deadline" x-bind:value="selectedTender?.deadline ? selectedTender.deadline.substring(0,10) : ''" class="w-full px-3 py-2 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Nilai Estimasi (Rp) <span class="text-rose-500">*</span></label>
                        <input type="number" name="estimated_value" required min="0" x-bind:value="selectedTender?.estimated_value" class="w-full px-3 py-2 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Nilai Penawaran (Rp)</label>
                        <input type="number" name="bid_value" min="0" x-bind:value="selectedTender?.bid_value" class="w-full px-3 py-2 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Status Pipeline <span class="text-rose-500">*</span></label>
                    <select name="status" required class="w-full px-3 py-2 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500 bg-white">
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
                    <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Catatan</label>
                    <textarea name="notes" rows="2" x-bind:value="selectedTender?.notes" class="w-full px-3 py-2 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="button" @click="editModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm rounded-xl transition">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-amber-600 hover:bg-amber-700 text-white font-semibold text-sm rounded-xl shadow transition flex items-center gap-1.5">
                        <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
