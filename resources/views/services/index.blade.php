@extends('layouts.app')

@section('title', 'Pekerjaan Jasa')
@section('header-title', 'Pelaksanaan Pekerjaan Jasa & Tagihan')

@section('content')
<div class="space-y-6" x-data="{
    createModal: false,
    billModal: false,
    editModal: false,
    selectedJob: null,
    billAmount: '',
    openBillModal(job) {
        this.selectedJob = job;
        this.billAmount = '';
        this.billModal = true;
    },
    openEditModal(job) {
        this.selectedJob = job;
        this.editModal = true;
    },
    setBillPercent(percent) {
        if (this.selectedJob && this.selectedJob.contract && this.selectedJob.contract.contract_value) {
            const contractVal = parseFloat(this.selectedJob.contract.contract_value);
            this.billAmount = Math.round(contractVal * (percent / 100));
        }
    }
}">

    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex flex-col md:flex-row justify-between items-center gap-4">
        <form method="GET" action="{{ route('jasa.index') }}" class="flex items-center gap-3 w-full md:w-auto">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nomor job / nama pekerjaan..." class="px-4 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500 outline-none w-64">
            <button type="submit" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm rounded-xl">Filter</button>
        </form>

        <button @click="createModal = true" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-xl shadow transition flex items-center gap-2">
            <i class="fa-solid fa-plus"></i> Tambah Pekerjaan Jasa
        </button>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-200 text-xs">
                        <th class="p-4 whitespace-nowrap">No. Job</th>
                        <th class="p-4">Nama Pekerjaan & Kontrak</th>
                        <th class="p-4">Klien</th>
                        <th class="p-4 whitespace-nowrap w-48">Progress (%)</th>
                        <th class="p-4 text-center whitespace-nowrap">Status</th>
                        <th class="p-4 text-center whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($serviceJobs as $job)
                        @php
                            $billed = $job->invoices->where('status', '!=', 'Cancelled')->sum('subtotal');
                            $contractVal = (float) ($job->contract?->contract_value ?? 0);
                            $formatCompact = function($num) {
                                if ($num >= 1000000000) {
                                    return rtrim(rtrim(number_format($num / 1000000000, 2, ',', '.'), '0'), ',') . ' M';
                                }
                                if ($num >= 1000000) {
                                    return rtrim(rtrim(number_format($num / 1000000, 1, ',', '.'), '0'), ',') . ' Jt';
                                }
                                return number_format($num, 0, ',', '.');
                            };
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="p-4 font-mono font-bold text-blue-600 whitespace-nowrap">{{ $job->job_number }}</td>
                            <td class="p-4">
                                <div class="font-bold text-slate-800">{{ $job->name }}</div>
                                <div class="text-xs text-slate-500 mt-0.5"><i class="fa-solid fa-file-contract text-slate-400"></i> Kontrak: {{ $job->contract->contract_number ?? '-' }}</div>
                            </td>
                            <td class="p-4 font-medium text-slate-700">
                                {{ $job->contract->client->name ?? '-' }}
                            </td>
                            <td class="p-4 whitespace-nowrap w-48">
                                <div class="flex items-center justify-between text-xs mb-1 font-semibold text-slate-700">
                                    <span class="text-blue-700 font-bold">{{ $job->progress }}%</span>
                                    <span class="text-[11px] text-slate-400 font-normal" title="Total Ditagih: Rp {{ number_format($billed, 0, ',', '.') }} dari Nilai Kontrak: Rp {{ number_format($contractVal, 0, ',', '.') }}">
                                        {{ $formatCompact($billed) }} / {{ $formatCompact($contractVal) }}
                                    </span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden" title="Progress otomatis {{ $job->progress }}% berdasarkan akumulasi tagihan terhadap kontrak">
                                    <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: {{ min(100, $job->progress) }}%"></div>
                                </div>
                            </td>
                            <td class="p-4 text-center whitespace-nowrap">
                                @if($job->status === 'Selesai')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Selesai
                                    </span>
                                @elseif($job->status === 'Dalam Pelaksanaan')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span> Dalam Pelaksanaan
                                    </span>
                                @elseif($job->status === 'Tertunda')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Tertunda
                                    </span>
                                @elseif($job->status === 'Dibatalkan')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Dibatalkan
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> {{ $job->status }}
                                    </span>
                                @endif
                            </td>
                            <td class="p-4 text-center whitespace-nowrap">
                                <div class="inline-flex items-center justify-center gap-1.5">
                                    <button @click="openBillModal({{ $job }})" class="px-3 py-1.5 bg-purple-600 hover:bg-purple-700 text-white font-semibold text-xs rounded-xl shadow-sm transition inline-flex items-center gap-1.5" title="Terbitkan Tagihan / Invoice Jasa">
                                        <i class="fa-solid fa-file-invoice"></i> Terbitkan Tagihan
                                    </button>

                                    <button @click="openEditModal({{ $job }})" class="w-8 h-8 flex items-center justify-center bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs rounded-xl transition" title="Edit Data Pekerjaan Jasa">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-400">Belum ada pekerjaan jasa.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100">
            {{ $serviceJobs->links() }}
        </div>
    </div>

    <!-- Modal Create Service Job -->
    <div x-show="createModal" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4" x-cloak>
        <div class="bg-white rounded-2xl max-w-xl w-full p-6 shadow-2xl space-y-4">
            <div class="flex justify-between items-center border-b pb-3">
                <h3 class="font-bold text-lg text-slate-800">Tambah Pekerjaan Jasa Baru</h3>
                <button @click="createModal = false" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>

            <form action="{{ route('jasa.store') }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Nomor Job</label>
                        <input type="text" name="job_number" required value="JOB-{{ date('Ymd') }}-{{ rand(100,999) }}" class="w-full px-3 py-2 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500 font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Pilih Kontrak Induk</label>
                        <select name="contract_id" required class="w-full px-3 py-2 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                            @foreach($contracts as $ctr)
                                <option value="{{ $ctr->id }}">{{ $ctr->contract_number }} - {{ $ctr->client->name }} (Rp {{ number_format($ctr->contract_value, 0, ',', '.') }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Nama Pekerjaan Jasa</label>
                    <input type="text" name="name" required placeholder="Instalasi & Maintenance Server..." class="w-full px-3 py-2 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Tanggal Mulai</label>
                        <input type="date" name="start_date" value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Tanggal Selesai</label>
                        <input type="date" name="end_date" value="{{ date('Y-m-d', strtotime('+30 days')) }}" class="w-full px-3 py-2 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Status Pekerjaan</label>
                    <select name="status" required class="w-full px-3 py-2 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                        <option value="Dalam Pelaksanaan">Dalam Pelaksanaan</option>
                        <option value="Pending">Pending</option>
                        <option value="Selesai">Selesai</option>
                    </select>
                </div>

                <div class="rounded-xl bg-blue-50 border border-blue-200 p-3.5 text-xs text-blue-800 flex items-start gap-2.5">
                    <i class="fa-solid fa-circle-info text-blue-600 mt-0.5 shrink-0 text-sm"></i>
                    <div>
                        <span class="font-bold">Progress Otomatis:</span> Persentase progress pekerjaan tidak perlu diinput manual. Sistem akan menghitung otomatis (0% – 100%) berdasarkan akumulasi tagihan termin yang diterbitkan terhadap nilai kontrak.
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Catatan Tambahan (Opsional)</label>
                    <textarea name="notes" rows="2" class="w-full px-3 py-2 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500" placeholder="Catatan spesifikasi atau teknis pelaksanaan..."></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t">
                    <button type="button" @click="createModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm rounded-xl">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-xl shadow">Simpan Pekerjaan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Generate Bill / Invoice -->
    <div x-show="billModal" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4" x-cloak>
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4">
            <div class="flex justify-between items-center border-b pb-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center font-bold">
                        <i class="fa-solid fa-file-invoice text-base"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-base text-slate-800">Terbitkan Tagihan Jasa</h3>
                        <p class="text-xs text-slate-500 font-mono" x-text="selectedJob ? selectedJob.job_number + ' • ' + selectedJob.name : ''"></p>
                    </div>
                </div>
                <button @click="billModal = false" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>

            <!-- Ringkasan Kontrak & Progres -->
            <div class="rounded-xl border border-slate-200 bg-slate-50/70 p-3.5 space-y-1.5 text-xs">
                <div class="flex justify-between">
                    <span class="text-slate-500">Nilai Kontrak:</span>
                    <span class="font-semibold text-slate-800" x-text="selectedJob?.contract ? 'Rp ' + Number(selectedJob.contract.contract_value).toLocaleString('id-ID') : '-'"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Progress Saat Ini:</span>
                    <span class="font-bold text-blue-700" x-text="(selectedJob?.progress || 0) + '%'"></span>
                </div>
                <p class="text-[11px] text-slate-400 pt-1 border-t border-slate-200">
                    * Menerbitkan tagihan akan otomatis menambah persentase progres pekerjaan sesuai akumulasi nilai tagihan.
                </p>
            </div>

            <form x-bind:action="'/jasa/' + (selectedJob ? selectedJob.id : 0) + '/bill'" method="POST" class="space-y-4">
                @csrf
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <label class="block text-xs font-semibold text-slate-700 uppercase">Nilai Tagihan / Subtotal (Rp) <span class="text-rose-500">*</span></label>
                        <div class="flex gap-1">
                            <button type="button" @click="setBillPercent(25)" class="px-2 py-0.5 text-[10px] bg-slate-100 hover:bg-slate-200 text-slate-700 rounded font-medium">25%</button>
                            <button type="button" @click="setBillPercent(50)" class="px-2 py-0.5 text-[10px] bg-slate-100 hover:bg-slate-200 text-slate-700 rounded font-medium">50%</button>
                            <button type="button" @click="setBillPercent(100)" class="px-2 py-0.5 text-[10px] bg-slate-100 hover:bg-slate-200 text-slate-700 rounded font-medium">100%</button>
                        </div>
                    </div>
                    <input type="number" name="amount" x-model="billAmount" required min="0.01" step="0.01" placeholder="Masukkan nominal tagihan (contoh: 50000000)" class="w-full px-3 py-2 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-purple-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Tanggal Jatuh Tempo <span class="text-rose-500">*</span></label>
                    <input type="date" name="due_date" required value="{{ date('Y-m-d', strtotime('+30 days')) }}" class="w-full px-3 py-2 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-purple-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Catatan Tagihan</label>
                    <textarea name="notes" rows="2" class="w-full px-3 py-2 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-purple-500" placeholder="Tagihan termin 1 pekerjaan jasa..."></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t">
                    <button type="button" @click="billModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm rounded-xl">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-purple-600 hover:bg-purple-700 text-white font-semibold text-sm rounded-xl shadow flex items-center gap-1.5">
                        <i class="fa-solid fa-file-invoice"></i> Terbitkan Invoice
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Service Job -->
    <div x-show="editModal" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4" x-cloak>
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4">
            <div class="flex justify-between items-center border-b pb-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center font-bold">
                        <i class="fa-solid fa-pen-to-square text-base"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-base text-slate-800">Edit Pekerjaan Jasa</h3>
                        <p class="text-xs text-slate-500 font-mono" x-text="selectedJob ? selectedJob.job_number : ''"></p>
                    </div>
                </div>
                <button @click="editModal = false" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>

            <form x-bind:action="'/jasa/' + (selectedJob ? selectedJob.id : 0)" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Nama Pekerjaan Jasa</label>
                    <input type="text" name="name" required x-bind:value="selectedJob ? selectedJob.name : ''" class="w-full px-3 py-2 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-amber-500">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Tanggal Mulai</label>
                        <input type="date" name="start_date" x-bind:value="selectedJob && selectedJob.start_date ? selectedJob.start_date.substring(0,10) : ''" class="w-full px-3 py-2 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-amber-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Tanggal Selesai</label>
                        <input type="date" name="end_date" x-bind:value="selectedJob && selectedJob.end_date ? selectedJob.end_date.substring(0,10) : ''" class="w-full px-3 py-2 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-amber-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Status Pekerjaan</label>
                    <select name="status" required class="w-full px-3 py-2 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-amber-500 bg-white">
                        <option value="Dalam Pelaksanaan" x-bind:selected="selectedJob && selectedJob.status === 'Dalam Pelaksanaan'">Dalam Pelaksanaan</option>
                        <option value="Pending" x-bind:selected="selectedJob && selectedJob.status === 'Pending'">Pending</option>
                        <option value="Tertunda" x-bind:selected="selectedJob && selectedJob.status === 'Tertunda'">Tertunda</option>
                        <option value="Selesai" x-bind:selected="selectedJob && selectedJob.status === 'Selesai'">Selesai (Otomatis Progress 100%)</option>
                        <option value="Dibatalkan" x-bind:selected="selectedJob && selectedJob.status === 'Dibatalkan'">Dibatalkan</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Catatan</label>
                    <textarea name="notes" rows="2" x-bind:value="selectedJob ? selectedJob.notes : ''" class="w-full px-3 py-2 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-amber-500"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t">
                    <button type="button" @click="editModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm rounded-xl">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-amber-600 hover:bg-amber-700 text-white font-semibold text-sm rounded-xl shadow flex items-center gap-1.5">
                        <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
