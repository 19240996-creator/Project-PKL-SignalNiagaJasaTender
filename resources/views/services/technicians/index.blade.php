@extends('layouts.app')

@section('title', 'Teknisi')
@section('header-title', 'Teknisi & Ketersediaan SDM')

@section('content')
@php
    $currentUser = auth()->user();
    $isAdmin = $currentUser && $currentUser->isAdmin();
    $isManagerOrOwner = $currentUser && ($currentUser->isManager() || $currentUser->isOwner());
    $isTechnician = $currentUser && $currentUser->isTechnician();
@endphp

<div x-data="{
    requestDeleteModal: false,
    approveDeleteModal: false,
    rejectDeleteModal: false,
    directDeleteModal: false,
    selectedTechnician: null,
    deletionReason: '',
    rejectNotes: '',
    openRequestDelete(tech) {
        this.selectedTechnician = tech;
        this.deletionReason = '';
        this.requestDeleteModal = true;
    },
    openApproveDelete(tech) {
        this.selectedTechnician = tech;
        this.approveDeleteModal = true;
    },
    openRejectDelete(tech) {
        this.selectedTechnician = tech;
        this.rejectNotes = '';
        this.rejectDeleteModal = true;
    },
    openDirectDelete(tech) {
        this.selectedTechnician = tech;
        this.directDeleteModal = true;
    }
}" class="space-y-5">

    {{-- Alert Notifikasi --}}
    @if(session('success'))
        <div class="px-4 py-3 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs flex items-center gap-2">
            <i class="fa-solid fa-circle-check text-emerald-600"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="px-4 py-3 rounded-lg bg-rose-50 border border-rose-200 text-rose-800 text-xs flex items-center gap-2">
            <i class="fa-solid fa-circle-exclamation text-rose-600"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif
    @if(isset($errors) && $errors->any())
        <div class="px-4 py-3 rounded-lg bg-rose-50 border border-rose-200 text-rose-800 text-xs flex items-center gap-2">
            <i class="fa-solid fa-circle-exclamation text-rose-600"></i>
            <span>{{ $errors->first() }}</span>
        </div>
    @endif

    {{-- Banner Khusus Persetujuan Hapus Teknisi (Untuk Manager & Owner) --}}
    @if($isManagerOrOwner && ($pendingDeletionCount ?? 0) > 0)
        <div class="rounded-xl border border-amber-300 bg-amber-50 p-4 text-amber-900 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-start sm:items-center gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-200/80 text-amber-800">
                    <i class="fa-solid fa-user-clock text-base"></i>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-slate-900">Permohonan Izin Hapus Teknisi</h4>
                    <p class="text-xs text-amber-800 mt-0.5">
                        Terdapat <strong>{{ $pendingDeletionCount }}</strong> permohonan penghapusan teknisi dari Admin yang menunggu persetujuan Anda.
                    </p>
                </div>
            </div>
            <div class="text-xs font-semibold text-amber-800 bg-amber-100/90 px-3 py-1.5 rounded-lg border border-amber-200 self-start sm:self-auto">
                {{ $pendingDeletionCount }} Menunggu Review
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-5">
        {{-- Form Tambah Teknisi (Hanya Admin / Manajemen) --}}
        @if(!$isTechnician)
        <section class="xl:col-span-1 bg-white rounded-lg border border-slate-200 p-4 shadow-xs">
            <h2 class="text-sm font-bold text-slate-900 mb-3 flex items-center gap-2">
                <i class="fa-solid fa-user-plus text-blue-600"></i> Tambah Teknisi
            </h2>
            <form method="POST" action="{{ route('jasa.teknisi.store') }}" class="space-y-2.5">
                @csrf
                <div>
                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Nama Lengkap <span class="text-rose-500">*</span></label>
                    <input name="name" required placeholder="Nama teknisi" class="field">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Kontak Telepon</label>
                    <input name="phone" placeholder="Kontak telepon (08...)" class="field">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Email</label>
                    <input name="email" type="email" placeholder="Email teknisi" class="field">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">ID Akun Pengguna (Opsional)</label>
                    <input name="user_id" type="number" placeholder="ID akun pengguna (opsional)" class="field">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Kompetensi</label>
                    <textarea name="competencies" rows="2" placeholder="Kompetensi, pisahkan dengan koma" class="field"></textarea>
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Status</label>
                    <select name="status" class="field">
                        <option value="active">Aktif</option>
                        <option value="inactive">Tidak aktif</option>
                    </select>
                </div>
                <button type="submit" class="w-full px-3 py-2 rounded-md bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold shadow-xs transition">
                    Simpan Teknisi
                </button>
            </form>
        </section>
        @endif

        {{-- Daftar SDM Teknis --}}
        <section class="{{ $isTechnician ? 'xl:col-span-3' : 'xl:col-span-2' }} bg-white rounded-lg border border-slate-200 overflow-hidden shadow-xs">
            <div class="p-4 border-b border-slate-200 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Daftar SDM Teknis</h2>
                    <p class="text-xs text-slate-500 mt-1">Beban aktif, riwayat penugasan, dan status ketersediaan per teknisi.</p>
                </div>
                <span class="text-xs font-semibold text-slate-600 bg-slate-100 px-2.5 py-1 rounded-md border border-slate-200">
                    {{ $technicians->count() }} teknisi
                </span>
            </div>

            <div class="divide-y divide-slate-100">
                @forelse($technicians as $technician)
                    @php
                        $isPendingDelete = $technician->isPendingDeletion();
                        $techData = [
                            'id' => $technician->id,
                            'name' => $technician->name,
                            'phone' => $technician->phone ?? '',
                            'email' => $technician->email ?? '',
                            'competencies' => $technician->competencies ?? '',
                            'deletion_status' => $technician->deletion_status,
                            'deletion_reason' => $technician->deletion_reason ?? '',
                            'requester_name' => $technician->deletionRequester?->name ?? 'Admin',
                            'requested_at' => $technician->deletion_requested_at ? $technician->deletion_requested_at->format('d M Y H:i') : null,
                            'active_assignments' => (int) ($technician->active_assignments ?? 0),
                        ];
                    @endphp

                    <div class="p-4 transition {{ $isPendingDelete ? 'bg-rose-50/20' : '' }}">
                        <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-3">
                            <div class="space-y-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="font-bold text-sm text-slate-900">{{ $technician->name }}</span>
                                    
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold {{ $technician->status === 'active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-500 border border-slate-200' }}">
                                        {{ $technician->status === 'active' ? 'Aktif' : 'Tidak aktif' }}
                                    </span>

                                    @if($isPendingDelete)
                                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-rose-100 text-rose-800 border border-rose-200 inline-flex items-center gap-1">
                                            <i class="fa-solid fa-hourglass-half text-[9px]"></i> Menunggu Izin Hapus Manager
                                        </span>
                                    @endif
                                </div>

                                <div class="text-xs text-slate-500">
                                    {{ $technician->phone ?: 'Kontak belum diisi' }} · {{ $technician->email ?: 'Email belum diisi' }}
                                </div>

                                <div class="text-xs text-slate-600">
                                    <span class="font-semibold text-slate-700">Kompetensi:</span> {{ $technician->competencies ?: 'Belum dicatat' }}
                                </div>
                            </div>

                            <div class="flex items-center md:items-end flex-col gap-2">
                                <div class="text-left md:text-right">
                                    <div class="text-xl font-bold text-blue-700">{{ $technician->active_assignments }}</div>
                                    <div class="text-[10px] uppercase tracking-wide text-slate-500">Tugas aktif</div>
                                </div>

                                {{-- Tombol Aksi Hapus / Persetujuan --}}
                                @if(!$isTechnician)
                                    <div class="flex items-center gap-1.5 mt-1">
                                        @if($isPendingDelete)
                                            @if($isAdmin)
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[11px] font-medium bg-rose-50 text-rose-700 border border-rose-200" title="Permohonan hapus telah diajukan dan sedang menunggu tinjauan Manager">
                                                    <i class="fa-solid fa-clock text-[10px]"></i> Menunggu Manager
                                                </span>
                                            @elseif($isManagerOrOwner)
                                                <button type="button" 
                                                        @click="openApproveDelete({{ json_encode($techData) }})" 
                                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-semibold bg-rose-600 hover:bg-rose-700 text-white shadow-xs transition" 
                                                        title="Setujui permohonan hapus teknisi">
                                                    <i class="fa-solid fa-check text-[10px]"></i> Setujui Hapus
                                                </button>
                                                <button type="button" 
                                                        @click="openRejectDelete({{ json_encode($techData) }})" 
                                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-300 transition" 
                                                        title="Tolak permohonan hapus">
                                                    <i class="fa-solid fa-xmark text-[10px]"></i> Tolak
                                                </button>
                                            @endif
                                        @else
                                            @if($isAdmin)
                                                @if($technician->active_assignments > 0)
                                                    <button type="button" 
                                                            disabled 
                                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-medium bg-slate-100 text-slate-400 border border-slate-200 cursor-not-allowed" 
                                                            title="Teknisi masih memiliki {{ $technician->active_assignments }} tugas aktif. Selesaikan tugas sebelum dihapus.">
                                                        <i class="fa-solid fa-trash text-[10px]"></i> Hapus
                                                    </button>
                                                @else
                                                    <button type="button" 
                                                            @click="openRequestDelete({{ json_encode($techData) }})" 
                                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-semibold bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 transition" 
                                                            title="Ajukan permohonan hapus teknisi ke Manager">
                                                        <i class="fa-solid fa-trash text-[10px]"></i> Hapus
                                                    </button>
                                                @endif
                                            @elseif($isManagerOrOwner)
                                                @if($technician->active_assignments > 0)
                                                    <button type="button" 
                                                            disabled 
                                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-medium bg-slate-100 text-slate-400 border border-slate-200 cursor-not-allowed" 
                                                            title="Teknisi masih memiliki {{ $technician->active_assignments }} tugas aktif.">
                                                        <i class="fa-solid fa-trash text-[10px]"></i> Hapus
                                                    </button>
                                                @else
                                                    <button type="button" 
                                                            @click="openDirectDelete({{ json_encode($techData) }})" 
                                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-semibold bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 transition" 
                                                            title="Hapus teknisi langsung oleh Manajemen">
                                                        <i class="fa-solid fa-trash text-[10px]"></i> Hapus
                                                    </button>
                                                @endif
                                            @endif
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Catatan Pengajuan Hapus jika Status Pending --}}
                        @if($isPendingDelete)
                            <div class="mt-3 rounded-lg border border-rose-200 bg-rose-50/70 p-3 text-xs space-y-1">
                                <div class="flex items-center justify-between text-rose-900 font-semibold text-[11px]">
                                    <span class="flex items-center gap-1.5">
                                        <i class="fa-solid fa-circle-exclamation text-rose-600"></i> Pengajuan Hapus oleh: {{ $technician->deletionRequester?->name ?? 'Admin' }}
                                    </span>
                                    <span class="text-slate-500 font-normal">
                                        {{ $technician->deletion_requested_at ? $technician->deletion_requested_at->format('d M Y H:i') : '' }}
                                    </span>
                                </div>
                                <div class="mt-1 p-2 bg-white rounded border border-rose-200/80 text-slate-800 text-xs italic">
                                    "{{ $technician->deletion_reason ?: 'Tidak ada catatan alasan yang dilampirkan.' }}"
                                </div>
                            </div>
                        @endif

                        <div class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div class="rounded-md bg-slate-50 p-3">
                                <div class="text-[10px] font-bold uppercase text-slate-500 mb-2">Ketersediaan</div>
                                @forelse($technician->availabilities->sortByDesc('available_from')->take(2) as $availability)
                                    <div class="text-xs text-slate-700">
                                        {{ $availability->available_from->format('d M Y') }} - {{ $availability->available_until->format('d M Y') }} 
                                        <span class="text-slate-400">({{ $availability->status }})</span>
                                    </div>
                                @empty
                                    <div class="text-xs text-slate-400">Belum ada jadwal.</div>
                                @endforelse
                            </div>

                            <div class="rounded-md bg-slate-50 p-3">
                                <div class="text-[10px] font-bold uppercase text-slate-500 mb-2">Riwayat pekerjaan</div>
                                @forelse($technician->assignments->sortByDesc('start_date')->take(2) as $assignment)
                                    <div class="text-xs text-slate-700 truncate">
                                        {{ $assignment->job?->job_number }} · {{ $assignment->job?->name }}
                                    </div>
                                @empty
                                    <div class="text-xs text-slate-400">Belum ada assignment.</div>
                                @endforelse
                            </div>
                        </div>

                        {{-- Form Catat Jadwal Ketersediaan --}}
                        <form method="POST" action="{{ route('jasa.teknisi.availability', $technician) }}" class="mt-3 flex flex-wrap gap-2 items-end">
                            @csrf
                            <input type="date" name="available_from" required class="field" title="Tanggal mulai">
                            <input type="date" name="available_until" required class="field" title="Tanggal selesai">
                            <select name="status" class="field">
                                <option value="available">Tersedia</option>
                                <option value="leave">Cuti</option>
                                <option value="unavailable">Tidak tersedia</option>
                            </select>
                            <button type="submit" class="px-3 py-2 rounded-md border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                                Catat Jadwal
                            </button>
                        </form>
                    </div>
                @empty
                    <div class="p-8 text-center text-xs text-slate-500">
                        Belum ada data teknisi.
                    </div>
                @endforelse
            </div>
        </section>
    </div>

    {{-- ======================================================== --}}
    {{-- MODAL 1: PERMOHONAN IZIN HAPUS TEKNISI (Admin ke Manager) --}}
    {{-- ======================================================== --}}
    <div x-show="requestDeleteModal" x-cloak style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs" @keydown.escape.window="requestDeleteModal = false">
        <div @click.outside="requestDeleteModal = false" class="w-full max-w-md overflow-hidden rounded-lg bg-white border border-slate-200 shadow-xl p-5 space-y-4">
            <div class="flex items-start justify-between border-b border-slate-200 pb-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-lg bg-rose-50 border border-rose-200 text-rose-600 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-user-minus text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 leading-tight">Konfirmasi Pengajuan Hapus Teknisi</h3>
                        <p class="text-[11px] text-slate-500 mt-0.5">Memerlukan persetujuan Manager</p>
                    </div>
                </div>
                <button type="button" @click="requestDeleteModal = false" class="text-slate-400 hover:text-slate-600 transition" aria-label="Tutup modal">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <!-- Ringkasan Teknisi -->
            <div class="bg-slate-50 rounded-lg p-3 border border-slate-200 text-xs space-y-1.5">
                <div class="flex items-center justify-between">
                    <span class="text-slate-500">Nama Teknisi:</span>
                    <span class="font-bold text-slate-800" x-text="selectedTechnician?.name"></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-500">Kontak:</span>
                    <span class="text-slate-700" x-text="selectedTechnician?.phone || 'Belum diisi'"></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-500">Email:</span>
                    <span class="text-slate-700" x-text="selectedTechnician?.email || 'Belum diisi'"></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-500">Kompetensi:</span>
                    <span class="text-slate-700" x-text="selectedTechnician?.competencies || 'Belum dicatat'"></span>
                </div>
            </div>

            <!-- Peringatan SOP -->
            <div class="bg-rose-50/70 border border-rose-200 rounded-lg p-3 text-xs text-rose-900 leading-relaxed">
                <div class="flex items-start gap-2">
                    <i class="fa-solid fa-circle-question text-rose-600 text-xs mt-0.5 shrink-0"></i>
                    <div>
                        <span class="font-bold">Apakah Anda yakin ingin menghapus teknisi ini?</span>
                        <p class="mt-0.5 text-rose-800/90 text-[11px]">
                            Sesuai SOP sistem, penghapusan teknisi oleh Admin memerlukan validasi dan persetujuan dari Manager. Harap cantumkan alasan jelas di bawah ini.
                        </p>
                    </div>
                </div>
            </div>

            <form x-bind:action="selectedTechnician ? ('/jasa/teknisi/' + selectedTechnician.id + '/request-deletion') : '#'" method="POST" class="space-y-3.5">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                        Alasan / Catatan Penghapusan <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="deletion_reason" 
                              x-model="deletionReason" 
                              required 
                              minlength="5" 
                              rows="3" 
                              placeholder="Contoh: Mengundurkan diri per akhir bulan, berpindah domisili, atau salah input data..." 
                              class="w-full px-3 py-2 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-rose-500"></textarea>
                    <p class="text-[11px] text-slate-400 mt-1">Minimal 5 karakter. Catatan ini akan ditinjau langsung oleh Manager.</p>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-200">
                    <button type="button" @click="requestDeleteModal = false" class="px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium text-xs rounded-md transition">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-1.5 bg-rose-600 hover:bg-rose-700 text-white font-semibold text-xs rounded-md transition flex items-center gap-1.5 shadow-xs">
                        <i class="fa-solid fa-paper-plane text-[11px]"></i> Kirim Pengajuan ke Manager
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ========================================================== --}}
    {{-- MODAL 2: PERSETUJUAN HAPUS TEKNISI (Khusus Manager / Owner) --}}
    {{-- ========================================================== --}}
    @if($isManagerOrOwner)
    <div x-show="approveDeleteModal" x-cloak style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs" @keydown.escape.window="approveDeleteModal = false">
        <div @click.outside="approveDeleteModal = false" class="w-full max-w-md overflow-hidden rounded-lg bg-white border border-slate-200 shadow-xl p-5 space-y-4">
            <div class="flex items-start justify-between border-b border-slate-200 pb-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-lg bg-rose-50 border border-rose-200 text-rose-600 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-user-check text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 leading-tight">Persetujuan Hapus Teknisi</h3>
                        <p class="text-[11px] text-rose-600 font-medium mt-0.5">Persetujuan Manajemen Operasional</p>
                    </div>
                </div>
                <button type="button" @click="approveDeleteModal = false" class="text-slate-400 hover:text-slate-600 transition" aria-label="Tutup modal">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <!-- Detail Data & Catatan Admin -->
            <div class="bg-slate-50 rounded-lg p-3 border border-slate-200 text-xs space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-slate-500">Nama Teknisi:</span>
                    <span class="font-bold text-slate-800" x-text="selectedTechnician?.name"></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-500">Kontak:</span>
                    <span class="text-slate-700" x-text="selectedTechnician?.phone || 'Belum diisi'"></span>
                </div>
                <div class="pt-2 border-t border-slate-200">
                    <span class="text-slate-500 block text-[11px]">Catatan Alasan oleh Admin:</span>
                    <p class="mt-1 p-2 bg-white rounded border border-slate-200 text-slate-800 font-medium italic text-[11px]" x-text="selectedTechnician?.deletion_reason || '-'"></p>
                </div>
                <div class="text-[10px] text-slate-400">
                    Diajukan oleh: <span class="text-slate-600 font-medium" x-text="selectedTechnician?.requester_name || 'Admin'"></span>
                </div>
            </div>

            <div class="bg-rose-50 border border-rose-200 rounded-lg p-3 text-xs text-rose-800 leading-relaxed flex items-start gap-2">
                <i class="fa-solid fa-circle-exclamation text-rose-600 text-xs mt-0.5 shrink-0"></i>
                <p class="text-[11px]">
                    Dengan menyetujui, data teknisi ini akan dinonaktifkan/dihapus dari daftar SDM aktif. Riwayat penugasan historis tetap tersimpan di basis data.
                </p>
            </div>

            <form x-bind:action="selectedTechnician ? ('/jasa/teknisi/' + selectedTechnician.id + '/approve-deletion') : '#'" method="POST">
                @csrf
                <div class="flex justify-end gap-2 pt-3 border-t border-slate-200">
                    <button type="button" @click="approveDeleteModal = false" class="px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium text-xs rounded-md transition">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-1.5 bg-rose-600 hover:bg-rose-700 text-white font-semibold text-xs rounded-md transition flex items-center gap-1.5 shadow-xs">
                        <i class="fa-solid fa-check text-[11px]"></i> Setujui & Hapus Teknisi
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ============================================================== --}}
    {{-- MODAL 3: TOLAK PERMOHONAN HAPUS TEKNISI (Khusus Manager / Owner) --}}
    {{-- ============================================================== --}}
    <div x-show="rejectDeleteModal" x-cloak style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs" @keydown.escape.window="rejectDeleteModal = false">
        <div @click.outside="rejectDeleteModal = false" class="w-full max-w-md overflow-hidden rounded-lg bg-white border border-slate-200 shadow-xl p-5 space-y-4">
            <div class="flex items-start justify-between border-b border-slate-200 pb-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-lg bg-slate-100 border border-slate-200 text-slate-700 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-shield-halved text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 leading-tight">Tolak Permohonan Hapus</h3>
                        <p class="text-[11px] text-slate-500 mt-0.5">Pertahankan teknisi ini dalam tim</p>
                    </div>
                </div>
                <button type="button" @click="rejectDeleteModal = false" class="text-slate-400 hover:text-slate-600 transition" aria-label="Tutup modal">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <!-- Detail Teknisi -->
            <div class="bg-slate-50 rounded-lg p-3 border border-slate-200 text-xs space-y-1.5">
                <div class="flex items-center justify-between">
                    <span class="text-slate-500">Nama Teknisi:</span>
                    <span class="font-bold text-slate-800" x-text="selectedTechnician?.name"></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-500">Kontak:</span>
                    <span class="text-slate-700" x-text="selectedTechnician?.phone || 'Belum diisi'"></span>
                </div>
            </div>

            <p class="text-xs text-slate-600 leading-relaxed">
                Permohonan penghapusan untuk teknisi <strong class="text-slate-800" x-text="selectedTechnician?.name"></strong> akan ditolak dan statusnya dikembalikan menjadi aktif.
            </p>

            <form x-bind:action="selectedTechnician ? ('/jasa/teknisi/' + selectedTechnician.id + '/reject-deletion') : '#'" method="POST" class="space-y-3.5">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                        Catatan Penolakan (Opsional)
                    </label>
                    <textarea name="notes" 
                              x-model="rejectNotes" 
                              rows="2" 
                              placeholder="Tuliskan catatan alasan penolakan jika ada..." 
                              class="w-full px-3 py-2 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-slate-400"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-200">
                    <button type="button" @click="rejectDeleteModal = false" class="px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium text-xs rounded-md transition">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-1.5 bg-slate-700 hover:bg-slate-800 text-white font-semibold text-xs rounded-md transition flex items-center gap-1.5 shadow-xs">
                        <i class="fa-solid fa-ban text-[11px]"></i> Tolak Permohonan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ======================================================== --}}
    {{-- MODAL 4: HAPUS LANGSUNG TEKNISI (Khusus Manager / Owner) --}}
    {{-- ======================================================== --}}
    <div x-show="directDeleteModal" x-cloak style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs" @keydown.escape.window="directDeleteModal = false">
        <div @click.outside="directDeleteModal = false" class="w-full max-w-md overflow-hidden rounded-lg bg-white border border-slate-200 shadow-xl p-5 space-y-4">
            <div class="flex items-start justify-between border-b border-slate-200 pb-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-lg bg-rose-50 border border-rose-200 text-rose-600 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-triangle-exclamation text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 leading-tight">Konfirmasi Hapus Teknisi</h3>
                        <p class="text-[11px] text-rose-600 font-medium mt-0.5">Wewenang Manajemen</p>
                    </div>
                </div>
                <button type="button" @click="directDeleteModal = false" class="text-slate-400 hover:text-slate-600 transition" aria-label="Tutup modal">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <!-- Detail Teknisi -->
            <div class="bg-slate-50 rounded-lg p-3 border border-slate-200 text-xs space-y-1.5">
                <div class="flex items-center justify-between">
                    <span class="text-slate-500">Nama Teknisi:</span>
                    <span class="font-bold text-slate-800" x-text="selectedTechnician?.name"></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-500">Kontak:</span>
                    <span class="text-slate-700" x-text="selectedTechnician?.phone || 'Belum diisi'"></span>
                </div>
            </div>

            <p class="text-xs text-slate-600 leading-relaxed">
                Apakah Anda yakin ingin menghapus data teknisi <strong class="text-slate-800" x-text="selectedTechnician?.name"></strong>? Data akan dihapus dari daftar operasional aktif.
            </p>

            <form x-bind:action="selectedTechnician ? ('/jasa/teknisi/' + selectedTechnician.id) : '#'" method="POST">
                @csrf
                @method('DELETE')
                <div class="flex justify-end gap-2 pt-3 border-t border-slate-200">
                    <button type="button" @click="directDeleteModal = false" class="px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium text-xs rounded-md transition">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-1.5 bg-rose-600 hover:bg-rose-700 text-white font-semibold text-xs rounded-md transition flex items-center gap-1.5 shadow-xs">
                        <i class="fa-solid fa-trash text-[11px]"></i> Ya, Hapus Teknisi
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

</div>

<style>
.field {
    width: 100%;
    padding: .5rem .65rem;
    border: 1px solid #cbd5e1;
    border-radius: .375rem;
    font-size: .75rem;
    background: #fff;
    outline: none;
    transition: all .15s ease-in-out;
}
.field:focus {
    border-color: #2563eb;
    box-shadow: 0 0 0 2px #dbeafe;
}
</style>
@endsection
