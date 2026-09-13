@extends('layouts.app')

@section('title', 'Pekerjaan Jasa')
@section('header-title', 'Pelaksanaan Pekerjaan Jasa & Tagihan')

@section('content')
<div class="space-y-6" x-data="{ createModal: false, billModal: false, selectedJob: null }">

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
                    <tr class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-200">
                        <th class="p-4">No. Job</th>
                        <th class="p-4">Nama Pekerjaan & Kontrak</th>
                        <th class="p-4">Klien</th>
                        <th class="p-4">Progress (%)</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($serviceJobs as $job)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="p-4 font-mono font-bold text-blue-600">{{ $job->job_number }}</td>
                            <td class="p-4">
                                <div class="font-bold text-slate-800">{{ $job->name }}</div>
                                <div class="text-xs text-slate-500">Kontrak: {{ $job->contract->contract_number ?? '-' }}</div>
                            </td>
                            <td class="p-4 font-medium text-slate-700">
                                {{ $job->contract->client->name ?? '-' }}
                            </td>
                            <td class="p-4 w-48">
                                <div class="flex items-center justify-between text-xs mb-1 font-semibold text-slate-700">
                                    <span>{{ $job->progress }}%</span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                    <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: {{ $job->progress }}%"></div>
                                </div>
                            </td>
                            <td class="p-4">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $job->status === 'Selesai' ? 'bg-emerald-100 text-emerald-800' : 'bg-blue-50 text-blue-700' }}">
                                    {{ $job->status }}
                                </span>
                            </td>
                            <td class="p-4 text-center">
                                <button @click="selectedJob = {{ $job }}; billModal = true" class="px-3 py-1 bg-purple-600 hover:bg-purple-700 text-white font-semibold text-xs rounded-lg shadow transition" title="Terbitkan Tagihan / Invoice Jasa">
                                    <i class="fa-solid fa-file-invoice"></i> Terbitkan Tagihan
                                </button>
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
                        <input type="text" name="job_number" required value="JOB-{{ date('Ymd') }}-{{ rand(100,999) }}" class="w-full px-3 py-2 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Pilih Kontrak Induk</label>
                        <select name="contract_id" required class="w-full px-3 py-2 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500">
                            @foreach($contracts as $ctr)
                                <option value="{{ $ctr->id }}">{{ $ctr->contract_number }} - {{ $ctr->client->name }}</option>
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
                        <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Progress Awal (%)</label>
                        <input type="number" name="progress" required value="0" min="0" max="100" class="w-full px-3 py-2 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Status Pekerjaan</label>
                        <select name="status" required class="w-full px-3 py-2 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="Dalam Pelaksanaan">Dalam Pelaksanaan</option>
                            <option value="Selesai">Selesai</option>
                            <option value="Pending">Pending</option>
                        </select>
                    </div>
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
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
            <div class="flex justify-between items-center border-b pb-3">
                <h3 class="font-bold text-lg text-slate-800">Terbitkan Tagihan Jasa</h3>
                <button @click="billModal = false" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>

            <form x-bind:action="'/jasa/' + (selectedJob ? selectedJob.id : 0) + '/bill'" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Nilai Tagihan / Subtotal (Rp)</label>
                    <input type="number" name="amount" required placeholder="50000000" class="w-full px-3 py-2 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Tanggal Jatuh Tempo</label>
                    <input type="date" name="due_date" required value="{{ date('Y-m-d', strtotime('+30 days')) }}" class="w-full px-3 py-2 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Catatan Tagihan</label>
                    <textarea name="notes" rows="2" class="w-full px-3 py-2 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500" placeholder="Tagihan termin 1 pekerjaan jasa..."></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t">
                    <button type="button" @click="billModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm rounded-xl">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-purple-600 hover:bg-purple-700 text-white font-semibold text-sm rounded-xl shadow">Terbitkan Invoice</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
