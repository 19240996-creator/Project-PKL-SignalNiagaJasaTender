@extends('layouts.app')

@section('title', 'Manajemen Tender')
@section('header-title', 'Pipeline & Dokumen Tender')

@section('content')
<div class="space-y-6" x-data="{ createModal: false, uploadModal: false, convertModal: false, selectedTender: null }">

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
                            <td class="p-4">
                                @php
                                    $badgeColor = match($tender->status) {
                                        'Ditemukan' => 'bg-slate-100 text-slate-700 border-slate-300',
                                        'Evaluasi' => 'bg-amber-50 text-amber-700 border-amber-200',
                                        'Persiapan Dokumen' => 'bg-blue-50 text-blue-700 border-blue-200',
                                        'Penawaran' => 'bg-purple-50 text-purple-700 border-purple-200',
                                        'Menang' => 'bg-emerald-100 text-emerald-800 border-emerald-300 font-bold',
                                        'Kalah' => 'bg-rose-100 text-rose-800 border-rose-200',
                                        'Kontrak' => 'bg-indigo-100 text-indigo-800 border-indigo-300',
                                        default => 'bg-slate-100 text-slate-700'
                                    };
                                @endphp
                                <span class="px-3 py-1 rounded-full text-xs font-medium border {{ $badgeColor }}">
                                    {{ $tender->status }}
                                </span>
                            </td>
                            <td class="p-4">
                                <span class="text-xs font-semibold text-slate-600">
                                    <i class="fa-solid fa-paperclip text-slate-400"></i> {{ $tender->documents->count() }} Dokumen
                                </span>
                            </td>
                            <td class="p-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    @if($tender->status === 'Menang' || $tender->result === 'Menang')
                                        <button @click="selectedTender = {{ $tender }}; convertModal = true" class="px-3 py-1 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs rounded-lg shadow transition" title="Konversi ke Kontrak Jasa">
                                            <i class="fa-solid fa-file-signature"></i> Buat Kontrak
                                        </button>
                                    @endif

                                    <button @click="selectedTender = {{ $tender }}; uploadModal = true" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs rounded-lg transition" title="Upload Dokumen">
                                        <i class="fa-solid fa-upload"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-slate-400 text-sm">Belum ada data tender. Silakan tambah tender baru.</td>
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

</div>
@endsection
