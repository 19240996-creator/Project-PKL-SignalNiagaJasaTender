@extends('layouts.app')

@section('title', 'Pekerjaan Jasa')
@section('header-title', 'Pelaksanaan Layanan Jasa & Verifikasi')

@section('content')
<div class="space-y-5" x-data="{
    createModal: false,
    billModal: false,
    editModal: false,
    rejectModal: false,
    selectedJob: null,
    rejectActionUrl: '',
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
    openRejectModal(jobId) {
        this.rejectActionUrl = '/jasa/' + jobId + '/reject';
        this.rejectModal = true;
    },
    setBillPercent(percent) {
        if (this.selectedJob) {
            const baseVal = parseFloat(this.selectedJob.biaya || (this.selectedJob.contract ? this.selectedJob.contract.contract_value : 0));
            if (baseVal > 0) {
                this.billAmount = Math.round(baseVal * (percent / 100));
            }
        }
    }
}">

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-lg flex items-center justify-between text-xs shadow-xs">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-circle-check text-emerald-600"></i>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700"><i class="fa-solid fa-xmark"></i></button>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-lg flex items-center justify-between text-xs shadow-xs">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-triangle-exclamation text-rose-600"></i>
                <span class="font-medium">{{ session('error') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700"><i class="fa-solid fa-xmark"></i></button>
        </div>
    @endif

    @if(auth()->user()->isOwner())
        <div class="border border-slate-200 bg-white rounded-lg p-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-xs">
            <div>
                <span class="text-xs font-semibold text-slate-900 block leading-tight">Pemantauan Eksekutif: Layanan Jasa</span>
                <span class="text-xs text-slate-500">Sebagai Owner, Anda memiliki akses peninjauan progres pekerjaan jasa teknis tanpa kewenangan edit langsung.</span>
            </div>
            <a href="{{ route('laporan.index', ['domain' => 'jasa']) }}" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-md text-xs font-semibold transition inline-flex items-center gap-1.5 self-start sm:self-auto shadow-xs">
                <i class="fa-solid fa-chart-pie"></i> Buka Laporan Jasa
            </a>
        </div>
    @elseif(auth()->user()->isManager() && ($pendingCount ?? 0) > 0)
        <div class="border-l-4 border-amber-500 bg-white border border-slate-200 rounded-lg p-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-xs">
            <div>
                <span class="text-xs font-semibold text-slate-900 block leading-tight">Verifikasi Pekerjaan Jasa</span>
                <span class="text-xs text-slate-500">Terdapat <span class="font-semibold text-slate-800">{{ $pendingCount }} pekerjaan jasa</span> menunggu persetujuan (Approval) Manager.</span>
            </div>
            <a href="{{ route('jasa.index', ['approval_status' => 'pending']) }}" class="px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white rounded-md text-xs font-medium transition inline-flex items-center gap-1.5 self-start sm:self-auto">
                Tampilkan Pending
            </a>
        </div>
    @endif

    <!-- Toolbar & Filter Bar -->
    <div class="bg-white rounded-lg p-4 border border-slate-200 shadow-xs flex flex-col md:flex-row justify-between items-center gap-3">
        <form method="GET" action="{{ route('jasa.index') }}" class="flex flex-wrap items-center gap-2.5 w-full md:w-auto">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nomor job / klien..." class="px-3 py-1.5 rounded-md border border-slate-200 text-xs w-52 outline-none">
            
            <select name="approval_status" onchange="this.form.submit()" class="px-2.5 py-1.5 rounded-md border border-slate-200 text-xs font-medium text-slate-700 bg-white hover:bg-slate-50 outline-none">
                <option value="">Semua Approval</option>
                <option value="pending" {{ request('approval_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ request('approval_status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                <option value="rejected" {{ request('approval_status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
            </select>

            <select name="status" onchange="this.form.submit()" class="px-2.5 py-1.5 rounded-md border border-slate-200 text-xs font-medium text-slate-700 bg-white hover:bg-slate-50 outline-none">
                <option value="">Semua Status Pelaksanaan</option>
                <option value="Dalam Pelaksanaan" {{ request('status') == 'Dalam Pelaksanaan' ? 'selected' : '' }}>Dalam Pelaksanaan</option>
                <option value="Selesai" {{ request('status') == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                <option value="Tertunda" {{ request('status') == 'Tertunda' ? 'selected' : '' }}>Tertunda</option>
            </select>

            <button type="submit" class="px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 font-semibold text-xs rounded-md transition">
                <i class="fa-solid fa-filter mr-1"></i> Filter
            </button>
            @if(request()->hasAny(['search', 'approval_status', 'status']))
                <a href="{{ route('jasa.index') }}" class="px-2.5 py-1.5 text-slate-500 hover:text-slate-800 text-xs font-medium rounded-md">Reset</a>
            @endif
        </form>

        @if(!auth()->user()->isOwner())
            <button @click="createModal = true" class="w-full md:w-auto px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs rounded-md shadow-xs transition flex items-center justify-center gap-1.5">
                <i class="fa-solid fa-plus text-[11px]"></i> Tambah Jasa
            </button>
        @endif
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-lg border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-200 uppercase tracking-wider text-[11px]">
                        <th class="p-3.5 whitespace-nowrap">No. Job</th>
                        <th class="p-3.5">Nama Layanan & Deskripsi</th>
                        <th class="p-3.5">Klien</th>
                        <th class="p-3.5 whitespace-nowrap">Biaya Layanan</th>
                        <th class="p-3.5 whitespace-nowrap">Tgl Pengerjaan</th>
                        <th class="p-3.5 text-center whitespace-nowrap">Pelaksanaan</th>
                        <th class="p-3.5 text-center whitespace-nowrap">Approval</th>
                        <th class="p-3.5 text-center whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($serviceJobs as $job)
                        @php
                            $effectiveBiaya = $job->biaya ?? ($job->contract?->contract_value ?? 0);
                            $clientDisplay = $job->klien ?: ($job->contract?->client?->name ?: ($job->client?->name ?: '-'));
                        @endphp
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="p-3.5 font-mono font-medium text-slate-900 whitespace-nowrap">
                                {{ $job->job_number }}
                            </td>
                            <td class="p-3.5 max-w-xs">
                                <div class="font-semibold text-slate-800">{{ $job->name }}</div>
                                @if($job->deskripsi_pekerjaan)
                                    <div class="text-[11px] text-slate-500 mt-0.5 line-clamp-1" title="{{ $job->deskripsi_pekerjaan }}">
                                        {{ $job->deskripsi_pekerjaan }}
                                    </div>
                                @endif
                            </td>
                            <td class="p-3.5 font-medium text-slate-700 whitespace-nowrap">
                                {{ $clientDisplay }}
                            </td>
                            <td class="p-3.5 font-mono font-semibold text-slate-900 whitespace-nowrap">
                                Rp {{ number_format($effectiveBiaya, 0, ',', '.') }}
                            </td>
                            <td class="p-3.5 text-slate-600 whitespace-nowrap">
                                <div>{{ $job->start_date ? \Carbon\Carbon::parse($job->start_date)->format('d M Y') : '-' }}</div>
                                @if($job->end_date)
                                    <div class="text-[11px] text-slate-400">s/d {{ \Carbon\Carbon::parse($job->end_date)->format('d M Y') }}</div>
                                @endif
                            </td>
                            <td class="p-3.5 text-center whitespace-nowrap">
                                @if($job->status === 'Selesai')
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Selesai
                                    </span>
                                @elseif($job->status === 'Dalam Pelaksanaan')
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span> Pelaksanaan
                                    </span>
                                @elseif($job->status === 'Tertunda')
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Tertunda
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-700">
                                        {{ $job->status }}
                                    </span>
                                @endif
                            </td>
                            <td class="p-3.5 text-center whitespace-nowrap">
                                @if($job->approval_status === 'approved')
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Disetujui
                                    </span>
                                    @if($job->approver)
                                        <div class="text-[10px] text-slate-400 mt-0.5 font-medium">oleh {{ $job->approver->name }}</div>
                                    @endif
                                @elseif($job->approval_status === 'rejected')
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-xs font-medium bg-rose-50 text-rose-700 border border-rose-200" title="{{ $job->approval_notes }}">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Ditolak
                                    </span>
                                    @if($job->approval_notes)
                                        <div class="text-[10px] text-rose-500 mt-0.5 max-w-[120px] truncate mx-auto" title="{{ $job->approval_notes }}">{{ $job->approval_notes }}</div>
                                    @endif
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Menunggu
                                    </span>
                                    <div class="text-[10px] text-slate-400 mt-0.5">Menunggu Manager</div>
                                @endif
                            </td>
                            <td class="p-3.5 text-center whitespace-nowrap">
                                <div class="inline-flex items-center justify-center gap-1.5">
                                    @if(auth()->user()->isManager() && $job->approval_status === 'pending')
                                        <!-- Quick Approve Button -->
                                        <form action="{{ route('jasa.approve', $job->id) }}" method="POST" class="inline" onsubmit="return confirm('Setujui pekerjaan jasa ini?')">
                                            @csrf
                                            <button type="submit" class="px-2 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded text-xs font-medium transition" title="Setujui">
                                                <i class="fa-solid fa-check text-[10px]"></i> Setujui
                                            </button>
                                        </form>

                                        <!-- Quick Reject Button -->
                                        <button type="button" @click="openRejectModal({{ $job->id }})" class="px-2 py-1 bg-rose-600 hover:bg-rose-700 text-white rounded text-xs font-medium transition" title="Tolak">
                                            <i class="fa-solid fa-xmark text-[10px]"></i> Tolak
                                        </button>
                                    @endif

                                    @if(!auth()->user()->isOwner())
                                        @if($job->approval_status === 'approved')
                                            <button @click="openBillModal({{ $job }})" class="px-2 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded text-xs font-medium transition inline-flex items-center gap-1" title="Terbitkan Tagihan / Invoice Jasa">
                                                <i class="fa-solid fa-file-invoice text-slate-500 text-[10px]"></i> Tagih
                                            </button>
                                        @endif

                                        <button @click="openEditModal({{ $job }})" class="w-6 h-6 flex items-center justify-center bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs rounded transition" title="Edit Data">
                                            <i class="fa-solid fa-pen-to-square text-[11px]"></i>
                                        </button>

                                        <form action="{{ route('jasa.destroy', $job->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pekerjaan jasa ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-6 h-6 flex items-center justify-center bg-slate-100 hover:bg-rose-50 text-slate-500 hover:text-rose-600 text-xs rounded transition" title="Hapus Data">
                                                <i class="fa-solid fa-trash-can text-[11px]"></i>
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-xs text-slate-400">Read-only</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-slate-400">
                                <p class="text-xs">Belum ada data pekerjaan jasa yang sesuai filter.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3.5 border-t border-slate-100">
            {{ $serviceJobs->links() }}
        </div>
    </div>

    <!-- Modal Create Service Job (PRD Standard) -->
    <div x-show="createModal" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4" x-cloak>
        <div class="bg-white rounded-lg border border-slate-200 max-w-xl w-full p-5 shadow-xl space-y-4 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center border-b border-slate-200 pb-3">
                <div>
                    <h3 class="font-bold text-sm text-slate-900">Tambah Layanan Jasa Baru</h3>
                    <p class="text-xs text-slate-500">Pekerjaan operasional jasa & verifikasi manager</p>
                </div>
                <button @click="createModal = false" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-sm"></i></button>
            </div>

            <form action="{{ route('jasa.store') }}" method="POST" class="space-y-3.5">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Nomor Job</label>
                    <input type="text" name="job_number" required value="JOB-{{ date('Ymd') }}-{{ rand(100,999) }}" class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500 font-mono bg-slate-50">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Nama Layanan Jasa <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" required placeholder="Contoh: Instalasi & Maintenance Server Jaringan" class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Nama Klien / Perusahaan <span class="text-rose-500">*</span></label>
                        <input type="text" name="klien" required placeholder="Nama Klien atau Instansi" class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Biaya Layanan Jasa (Rp) <span class="text-rose-500">*</span></label>
                        <input type="number" name="biaya" required min="0" step="1000" placeholder="Contoh: 15000000" class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500 font-mono">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Tanggal Mulai Pengerjaan</label>
                        <input type="date" name="start_date" value="{{ date('Y-m-d') }}" class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Target Selesai</label>
                        <input type="date" name="end_date" class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Deskripsi Ruang Lingkup Pekerjaan</label>
                    <textarea name="deskripsi_pekerjaan" rows="2" placeholder="Jelaskan spesifikasi teknis atau deliverable yang disepakati..." class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>

                @if(auth()->user()->isAdmin())
                    <div class="rounded-md bg-amber-50 border border-amber-200 p-2.5 text-xs text-amber-800 flex items-start gap-2">
                        <i class="fa-solid fa-circle-info text-amber-600 mt-0.5"></i>
                        <span>Input Admin berstatus <strong>Pending</strong> dan memerlukan persetujuan Manager sebelum penagihan termin dapat diterbitkan.</span>
                    </div>
                @endif

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-200">
                    <button type="button" @click="createModal = false" class="px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium text-xs rounded-md transition">Batal</button>
                    <button type="submit" class="px-4 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs rounded-md transition shadow-xs">Simpan & Ajukan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Generate Bill / Invoice -->
    <div x-show="billModal" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4" x-cloak>
        <div class="bg-white rounded-lg border border-slate-200 max-w-lg w-full p-5 shadow-xl space-y-4">
            <div class="flex justify-between items-center border-b border-slate-200 pb-3">
                <div>
                    <h3 class="font-bold text-sm text-slate-900">Terbitkan Tagihan Jasa</h3>
                    <p class="text-xs text-slate-500 font-mono" x-text="selectedJob ? selectedJob.job_number + ' • ' + selectedJob.name : ''"></p>
                </div>
                <button @click="billModal = false" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-sm"></i></button>
            </div>

            <!-- Ringkasan Biaya -->
            <div class="rounded-md border border-slate-200 bg-slate-50 p-3 space-y-1 text-xs">
                <div class="flex justify-between">
                    <span class="text-slate-500">Biaya Layanan Jasa:</span>
                    <span class="font-mono font-semibold text-slate-900" x-text="selectedJob ? 'Rp ' + Number(selectedJob.biaya || (selectedJob.contract ? selectedJob.contract.contract_value : 0)).toLocaleString('id-ID') : '-'"></span>
                </div>
            </div>

            <form x-bind:action="'/jasa/' + (selectedJob ? selectedJob.id : 0) + '/bill'" method="POST" class="space-y-3.5">
                @csrf
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Nilai Tagihan (Rp) <span class="text-rose-500">*</span></label>
                        <div class="flex gap-1">
                            <button type="button" @click="setBillPercent(25)" class="px-2 py-0.5 text-[10px] bg-slate-100 hover:bg-slate-200 text-slate-700 rounded font-medium">25%</button>
                            <button type="button" @click="setBillPercent(50)" class="px-2 py-0.5 text-[10px] bg-slate-100 hover:bg-slate-200 text-slate-700 rounded font-medium">50%</button>
                            <button type="button" @click="setBillPercent(100)" class="px-2 py-0.5 text-[10px] bg-slate-100 hover:bg-slate-200 text-slate-700 rounded font-medium">100%</button>
                        </div>
                    </div>
                    <input type="number" name="amount" x-model="billAmount" required min="0.01" step="0.01" placeholder="Nominal tagihan..." class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500 font-mono">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Tanggal Jatuh Tempo <span class="text-rose-500">*</span></label>
                    <input type="date" name="due_date" required value="{{ date('Y-m-d', strtotime('+30 days')) }}" class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Catatan Tagihan</label>
                    <textarea name="notes" rows="2" class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500" placeholder="Tagihan termin pekerjaan jasa..."></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-200">
                    <button type="button" @click="billModal = false" class="px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium text-xs rounded-md transition">Batal</button>
                    <button type="submit" class="px-4 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs rounded-md transition flex items-center gap-1.5 shadow-xs">
                        <i class="fa-solid fa-file-invoice text-[11px]"></i> Terbitkan Invoice
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Service Job -->
    <div x-show="editModal" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4" x-cloak>
        <div class="bg-white rounded-lg border border-slate-200 max-w-lg w-full p-5 shadow-xl space-y-4">
            <div class="flex justify-between items-center border-b border-slate-200 pb-3">
                <div>
                    <h3 class="font-bold text-sm text-slate-900">Edit Pekerjaan Jasa</h3>
                    <p class="text-xs text-slate-500 font-mono" x-text="selectedJob ? selectedJob.job_number : ''"></p>
                </div>
                <button @click="editModal = false" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-sm"></i></button>
            </div>

            <form x-bind:action="'/jasa/' + (selectedJob ? selectedJob.id : 0)" method="POST" class="space-y-3.5">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Nama Layanan Jasa</label>
                    <input type="text" name="name" required x-bind:value="selectedJob ? selectedJob.name : ''" class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Tanggal Mulai</label>
                        <input type="date" name="start_date" x-bind:value="selectedJob && selectedJob.start_date ? selectedJob.start_date.substring(0,10) : ''" class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Tanggal Selesai</label>
                        <input type="date" name="end_date" x-bind:value="selectedJob && selectedJob.end_date ? selectedJob.end_date.substring(0,10) : ''" class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Status Pelaksanaan</label>
                    <select name="status" required class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                        <option value="Dalam Pelaksanaan" x-bind:selected="selectedJob && selectedJob.status === 'Dalam Pelaksanaan'">Dalam Pelaksanaan</option>
                        <option value="Pending" x-bind:selected="selectedJob && selectedJob.status === 'Pending'">Pending</option>
                        <option value="Tertunda" x-bind:selected="selectedJob && selectedJob.status === 'Tertunda'">Tertunda</option>
                        <option value="Selesai" x-bind:selected="selectedJob && selectedJob.status === 'Selesai'">Selesai</option>
                        <option value="Dibatalkan" x-bind:selected="selectedJob && selectedJob.status === 'Dibatalkan'">Dibatalkan</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Deskripsi Ruang Lingkup</label>
                    <textarea name="deskripsi_pekerjaan" rows="2" x-bind:value="selectedJob ? selectedJob.deskripsi_pekerjaan : ''" class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-200">
                    <button type="button" @click="editModal = false" class="px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium text-xs rounded-md transition">Batal</button>
                    <button type="submit" class="px-4 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs rounded-md transition shadow-xs">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Rejection Notes -->
    <div x-show="rejectModal" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4" x-cloak>
        <div class="bg-white rounded-lg border border-slate-200 max-w-md w-full p-5 shadow-xl space-y-4">
            <div class="flex items-start justify-between border-b border-slate-200 pb-3">
                <div>
                    <h3 class="font-bold text-sm text-slate-900">Tolak Pekerjaan Jasa</h3>
                    <p class="text-xs text-slate-500">Berikan catatan alasan penolakan</p>
                </div>
                <button @click="rejectModal = false" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-sm"></i></button>
            </div>

            <form :action="rejectActionUrl" method="POST" class="space-y-3.5">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Alasan Penolakan</label>
                    <textarea name="notes" rows="3" required placeholder="Contoh: Dokumen spesifikasi teknis belum lengkap..." class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-rose-500"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-2 border-t border-slate-200">
                    <button type="button" @click="rejectModal = false" class="px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium text-xs rounded-md transition">Batal</button>
                    <button type="submit" class="px-4 py-1.5 bg-rose-600 hover:bg-rose-700 text-white font-medium text-xs rounded-md transition">Tolak Jasa</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
