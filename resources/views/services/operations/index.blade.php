@extends('layouts.app')

@section('title', $title)
@section('header-title', $title . ' & Monitoring Pekerjaan')

@section('content')
@php $isInstallation = $serviceType === 'installation'; @endphp
<div class="space-y-5">
    @if(session('success'))<div class="px-4 py-3 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="px-4 py-3 rounded-lg bg-rose-50 border border-rose-200 text-rose-800 text-xs">{{ $errors->first() }}</div>@endif

    @if(!auth()->user()->isTechnician())
    <section class="bg-white rounded-lg border border-slate-200 p-4 shadow-xs">
        <div class="flex items-center justify-between mb-3"><div><h2 class="text-sm font-bold text-slate-900">Permintaan {{ $title }}</h2><p class="text-xs text-slate-500 mt-1">Hubungkan pekerjaan dengan pelanggan, proyek Tender, SDM, dan material.</p></div><span class="px-2 py-1 rounded bg-blue-50 text-blue-700 text-xs font-semibold">{{ $jobs->total() }} pekerjaan</span></div>
        <form method="POST" action="{{ route($isInstallation ? 'jasa.instalasi.store' : 'jasa.maintenance.store') }}" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-2.5">@csrf
            <input name="name" required placeholder="Nama pekerjaan / perangkat" class="field md:col-span-2">
            <select name="client_id" class="field"><option value="">Pilih pelanggan</option>@foreach($clients as $client)<option value="{{ $client->id }}">{{ $client->name }}</option>@endforeach</select>
            <select name="tender_id" class="field"><option value="">ID Tender (opsional)</option>@foreach($tenders as $tender)<option value="{{ $tender->id }}">{{ $tender->tender_number }} - {{ $tender->name }}</option>@endforeach</select>
            <input name="location" placeholder="Lokasi pekerjaan" class="field">
            <input name="work_type" placeholder="Jenis pekerjaan" class="field">
            <input name="required_competency" placeholder="Kompetensi yang diperlukan" class="field md:col-span-2">
            <input type="date" name="start_date" required class="field"><input type="date" name="end_date" required class="field">
            <input type="number" step="0.01" name="estimated_labor_hours" placeholder="Estimasi jam kerja" class="field"><input type="number" step="0.01" name="estimated_labor_cost" placeholder="Estimasi biaya tenaga" class="field">
            @if(!$isInstallation)<textarea name="diagnosis" placeholder="Diagnosis awal" class="field"></textarea><textarea name="parts_needed" placeholder="Suku cadang yang dibutuhkan" class="field"></textarea>@endif
            <textarea name="notes" placeholder="Catatan / kendala awal" class="field md:col-span-2"></textarea>
            <button class="md:col-span-4 px-3 py-2 rounded-md bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold">Simpan Permintaan</button>
        </form>
    </section>
    @endif

    <section class="space-y-3">
        @forelse($jobs as $job)
            <article class="bg-white rounded-lg border border-slate-200 shadow-xs overflow-hidden">
                <div class="p-4 flex flex-col lg:flex-row lg:items-start lg:justify-between gap-3 border-b border-slate-100">
                    <div>
                        <div class="flex items-center flex-wrap gap-2">
                            <span class="font-mono text-xs font-bold text-blue-700">{{ $job->job_number }}</span>

                            {{-- Status Pengerjaan Lapangan --}}
                            @if($job->status === 'Selesai')
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block mr-1"></span>Selesai
                                </span>
                            @elseif($job->status === 'Dalam Pelaksanaan')
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse inline-block mr-1"></span>Dalam Pelaksanaan
                                </span>
                            @elseif($job->status === 'Tertunda')
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500 inline-block mr-1"></span>Tertunda
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400 inline-block mr-1"></span>Pending
                                </span>
                            @endif

                            {{-- Status Persetujuan Pengajuan Dokumen --}}
                            @if($job->status === 'Selesai' || $job->approval_status === 'approved')
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200" title="Dokumen pengajuan pekerjaan disetujui">
                                    <i class="fa-solid fa-check text-[9px] mr-1"></i>Disetujui
                                </span>
                            @elseif($job->approval_status === 'rejected')
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-rose-50 text-rose-700 border border-rose-200" title="Pengajuan pekerjaan ditolak">
                                    <i class="fa-solid fa-xmark text-[9px] mr-1"></i>Pengajuan Ditolak
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-200" title="Menunggu verifikasi persetujuan Manager">
                                    <i class="fa-solid fa-clock text-[9px] mr-1"></i>Persetujuan: Menunggu
                                </span>
                            @endif
                        </div>
                        <h3 class="text-sm font-bold text-slate-900 mt-1">{{ $job->name }}</h3>
                        <p class="text-xs text-slate-500 mt-1">{{ $job->client?->name ?: $job->klien ?: 'Pelanggan belum dipilih' }} · {{ $job->location ?: 'Lokasi belum diisi' }} · {{ $job->start_date?->format('d M Y') }} - {{ $job->end_date?->format('d M Y') }}</p>
                        @if($job->tender)
                            <p class="text-xs text-blue-600 mt-1">Terhubung Tender: {{ $job->tender->tender_number }} - {{ $job->tender->name }}</p>
                        @endif
                    </div>
                    <div class="w-full lg:w-48">
                        <div class="flex justify-between text-[10px] text-slate-500 mb-1">
                            <span>Progress</span><strong>{{ $job->progress }}%</strong>
                        </div>
                        <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full bg-blue-600" style="width: {{ $job->progress }}%"></div>
                        </div>
                    </div>
                </div>
                <div class="p-4 grid grid-cols-1 lg:grid-cols-3 gap-4">
                    <div><div class="label">Teknisi bertugas</div>@forelse($job->assignments as $assignment)<div class="text-xs text-slate-700">{{ $assignment->technician->name }} <span class="text-slate-400">({{ $assignment->start_date->format('d/m') }} - {{ $assignment->end_date->format('d/m') }})</span></div>@empty<div class="text-xs text-slate-400">Belum ada teknisi.</div>@endforelse</div>
                    <div><div class="label">Material / suku cadang</div>@forelse($job->materialRequests as $material)<div class="text-xs text-slate-700">{{ $material->item_name }} x {{ $material->quantity }} <span class="text-slate-400">({{ $material->status }})</span></div>@empty<div class="text-xs text-slate-400">Belum ada permintaan.</div>@endforelse</div>
                    <div><div class="label">Biaya</div><div class="text-xs text-slate-700">Estimasi: Rp {{ number_format($job->estimated_labor_cost, 0, ',', '.') }}</div><div class="text-xs text-slate-700">Aktual: Rp {{ number_format($job->actual_cost, 0, ',', '.') }}</div></div>
                </div>
                <div class="p-4 pt-0 grid grid-cols-1 md:grid-cols-3 gap-3 border-t border-slate-100 mt-2">
                    @if(!auth()->user()->isTechnician())
                        <!-- Form 1: Tugaskan Teknisi -->
                        <form method="POST" action="{{ route('jasa.operasional.assign', $job) }}" class="pt-3 space-y-2">
                            @csrf
                            <div>
                                <label class="form-sublabel">Pilih Teknisi</label>
                                <select name="technician_id" required class="field">
                                    <option value="">-- Pilih teknisi --</option>
                                    @foreach($technicians as $technician)
                                        <option value="{{ $technician->id }}">{{ $technician->name }} · {{ $technician->competencies }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="form-sublabel">Tgl Mulai</label>
                                        <input type="date" name="start_date" required class="field" value="{{ $job->start_date?->format('Y-m-d') }}">
                                    </div>
                                    <div>
                                        <label class="form-sublabel">Tgl Selesai</label>
                                        <input type="date" name="end_date" required class="field" value="{{ $job->end_date?->format('Y-m-d') }}">
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="w-full px-3 py-2 bg-blue-50 hover:bg-blue-100 border border-blue-200 text-blue-700 rounded-xl text-xs font-semibold transition flex items-center justify-center gap-1.5 shadow-xs">
                                <i class="fa-solid fa-user-check text-[11px]"></i> Tugaskan Teknisi
                            </button>
                        </form>
                    @endif

                    <!-- Form 2: Perbarui Progres Pekerjaan -->
                    <form method="POST" action="{{ route('jasa.operasional.progress', $job) }}" class="pt-3 space-y-2">
                        @csrf
                        <div>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="form-sublabel">Progress Kerja (%)</label>
                                    <input type="number" min="0" max="100" name="progress" value="{{ $job->progress }}" placeholder="Progress kerja" class="field" title="Progress kerja (0-100%)">
                                </div>
                                <div>
                                    <label class="form-sublabel">Status Pekerjaan</label>
                                    <select name="status" class="field">
                                        <option value="Pending" {{ $job->status === 'Pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="Dalam Pelaksanaan" {{ $job->status === 'Dalam Pelaksanaan' ? 'selected' : '' }}>Dalam Pelaksanaan</option>
                                        <option value="Tertunda" {{ $job->status === 'Tertunda' ? 'selected' : '' }}>Tertunda</option>
                                        <option value="Selesai" {{ $job->status === 'Selesai' ? 'selected' : '' }}>Selesai</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="form-sublabel">Biaya Aktual (opsional)</label>
                            <input type="number" step="0.01" min="0" name="actual_cost" value="{{ $job->actual_cost > 0 ? $job->actual_cost : '' }}" placeholder="Biaya Aktual (opsional)" class="field">
                        </div>
                        <div>
                            <label class="form-sublabel">Hasil / Kendala</label>
                            <textarea name="result_notes" rows="2" placeholder="Hasil pengerjaan atau kendala lapangan..." class="field">{{ $job->result_notes }}</textarea>
                        </div>
                        <button type="submit" class="w-full px-3 py-2 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 text-emerald-700 rounded-xl text-xs font-semibold transition flex items-center justify-center gap-1.5 shadow-xs">
                            <i class="fa-solid fa-arrows-rotate text-[11px]"></i> Perbarui Progres
                        </button>
                    </form>

                    @if(!auth()->user()->isTechnician())
                        <!-- Form 3: Ajukan Material ke Dagang -->
                        <form method="POST" action="{{ route('jasa.operasional.materials', $job) }}" class="pt-3 space-y-2">
                            @csrf
                            <div>
                                <label class="form-sublabel">Pilih Barang dari Dagang</label>
                                <select name="product_id" class="field">
                                    <option value="">-- Pilih barang Dagang (opsional) --</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->name }} (Stok: {{ (float) $product->stock }} {{ $product->unit }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="form-sublabel">Nama Material / Suku Cadang</label>
                                <input name="item_name" required placeholder="Nama material / suku cadang" class="field">
                            </div>
                            <div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="form-sublabel">Jumlah Qty</label>
                                        <input type="number" step="0.01" min="0.01" name="quantity" value="1" placeholder="Qty" class="field">
                                    </div>
                                    <div>
                                        <label class="form-sublabel">Satuan</label>
                                        <input name="unit" placeholder="Unit, Roll, Pcs" class="field">
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="w-full px-3 py-2 bg-amber-50 hover:bg-amber-100 border border-amber-200 text-amber-700 rounded-xl text-xs font-semibold transition flex items-center justify-center gap-1.5 shadow-xs">
                                <i class="fa-solid fa-boxes-packing text-[11px]"></i> Ajukan Material ke Dagang
                            </button>
                        </form>
                    @endif
                </div>
            </article>
        @empty<div class="bg-white border border-slate-200 rounded-lg p-10 text-center text-xs text-slate-500">Belum ada permintaan {{ strtolower($title) }}.</div>@endforelse
        {{ $jobs->links() }}
    </section>
</div>
<style>
.field {
    width: 100%;
    padding: 0.5rem 0.75rem;
    border: 1px solid #cbd5e1;
    border-radius: 0.75rem;
    font-size: 0.75rem;
    background: #fff;
    outline: none;
    transition: all 0.15s ease-in-out;
}
.field:focus {
    border-color: #2563eb;
    box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.15);
}
.label {
    font-size: 0.625rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #64748b;
    margin-bottom: 0.35rem;
}
.form-sublabel {
    font-size: 0.6875rem;
    font-weight: 600;
    color: #475569;
    margin-bottom: 0.25rem;
    display: block;
}
</style>
@endsection
