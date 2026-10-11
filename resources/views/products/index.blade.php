@extends('layouts.app')

@section('title', 'Master Produk & Stok')
@section('header-title', 'Persediaan Stok Barang & Katalog Produk')

@section('content')
@php
    $userRole = auth()->user()->role?->name ?? 'guest';
    $isAdmin = $userRole === 'admin';
    $isManagerOrOwner = in_array($userRole, ['manager', 'owner'], true);
    $currentFilter = request('filter') ?? (request('deletion_status') === 'pending_deletion' ? 'pending_deletion' : null);
@endphp

<div class="space-y-6" x-data="{
    createModal: false,
    adjustModal: false,
    deleteModal: false,
    editModal: false,
    requestDeleteModal: false,
    approveDeleteModal: false,
    rejectDeleteModal: false,
    reviewAdjustmentModal: false,
    reviewEditModal: false,
    selectedProduct: null,
    selectedChangeRequest: null,
    deletionReason: '',
    editReason: '',
    adjustNotes: '',
    rejectNotes: '',
    isAdminUser: {{ $isAdmin ? 'true' : 'false' }}
}">

    <!-- Tab Filter Status & Bar Aksi -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-200 pb-3">
        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('products.index') }}" 
               class="px-4 py-2 rounded-xl text-xs font-semibold transition {{ !$currentFilter ? 'bg-blue-600 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
                Semua Produk ({{ $totalProductsCount ?? $products->total() }})
            </a>

            <a href="{{ route('products.index', ['filter' => 'all_pending']) }}" 
               class="px-4 py-2 rounded-xl text-xs font-semibold transition flex items-center gap-2 {{ $currentFilter === 'all_pending' ? 'bg-amber-600 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
                <i class="fa-solid fa-clock-rotate-left"></i>
                Semua Menunggu Persetujuan
                @if(($totalPendingCount ?? 0) > 0)
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $currentFilter === 'all_pending' ? 'bg-amber-800 text-white' : 'bg-amber-100 text-amber-800' }}">
                        {{ $totalPendingCount }}
                    </span>
                @endif
            </a>

            <a href="{{ route('products.index', ['filter' => 'pending_adjustment']) }}" 
               class="px-3.5 py-2 rounded-xl text-xs font-semibold transition flex items-center gap-1.5 {{ $currentFilter === 'pending_adjustment' ? 'bg-amber-600 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
                <i class="fa-solid fa-boxes-packing text-[11px]"></i>
                Stok
                @if(($pendingAdjustmentCount ?? 0) > 0)
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold {{ $currentFilter === 'pending_adjustment' ? 'bg-amber-800 text-white' : 'bg-amber-100 text-amber-800' }}">
                        {{ $pendingAdjustmentCount }}
                    </span>
                @endif
            </a>

            <a href="{{ route('products.index', ['filter' => 'pending_edit']) }}" 
               class="px-3.5 py-2 rounded-xl text-xs font-semibold transition flex items-center gap-1.5 {{ $currentFilter === 'pending_edit' ? 'bg-amber-600 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
                <i class="fa-solid fa-pen-to-square text-[11px]"></i>
                Harga / Data
                @if(($pendingEditCount ?? 0) > 0)
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold {{ $currentFilter === 'pending_edit' ? 'bg-amber-800 text-white' : 'bg-amber-100 text-amber-800' }}">
                        {{ $pendingEditCount }}
                    </span>
                @endif
            </a>

            <a href="{{ route('products.index', ['filter' => 'pending_deletion']) }}" 
               class="px-3.5 py-2 rounded-xl text-xs font-semibold transition flex items-center gap-1.5 {{ $currentFilter === 'pending_deletion' ? 'bg-rose-600 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
                <i class="fa-solid fa-trash text-[11px]"></i>
                Hapus
                @if(($pendingDeletionCount ?? 0) > 0)
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold {{ $currentFilter === 'pending_deletion' ? 'bg-rose-800 text-white' : 'bg-rose-100 text-rose-800' }}">
                        {{ $pendingDeletionCount }}
                    </span>
                @endif
            </a>
        </div>
    </div>

    <!-- Search & Tambah Produk -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex flex-col md:flex-row justify-between items-center gap-4">
        <form method="GET" action="{{ route('products.index') }}" class="flex items-center gap-3 w-full md:w-auto">
            @if($currentFilter)
                <input type="hidden" name="filter" value="{{ $currentFilter }}">
            @endif
            <input type="text" 
                   name="search" 
                   value="{{ request('search') }}" 
                   placeholder="Cari SKU, nama produk, kategori..." 
                   class="px-4 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500 outline-none w-64 text-slate-800">
            <button type="submit" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm rounded-xl transition">Cari</button>
            @if(request('search'))
                <a href="{{ route('products.index', $currentFilter ? ['filter' => $currentFilter] : []) }}" class="text-xs text-slate-500 hover:text-slate-800 underline">Reset</a>
            @endif
        </form>

        <button @click="createModal = true" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-xl shadow transition flex items-center gap-2">
            <i class="fa-solid fa-plus"></i> Tambah Produk Baru
        </button>
    </div>

    <!-- Tabel Produk -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-200">
                        <th class="p-4">SKU</th>
                        <th class="p-4">Nama Produk & Kategori</th>
                        <th class="p-4">Harga Beli</th>
                        <th class="p-4">Harga Jual</th>
                        <th class="p-4">Stok Saat Ini</th>
                        <th class="p-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($products as $p)
                        <tr class="hover:bg-slate-50/80 transition {{ $p->deletion_status === 'pending_deletion' ? 'bg-rose-50/20' : ($p->pendingAdjustment ? 'bg-amber-50/20' : ($p->pendingEdit ? 'bg-indigo-50/20' : '')) }}">
                            <td class="p-4 font-mono font-bold text-blue-600 whitespace-nowrap">{{ $p->sku }}</td>
                            <td class="p-4">
                                <div class="font-bold text-slate-800">{{ $p->name }}</div>
                                <div class="text-xs text-slate-500"><i class="fa-solid fa-tag"></i> {{ $p->category ?? 'Umum' }}</div>

                                <!-- Badge Status: Menunggu Persetujuan Hapus -->
                                @if($p->deletion_status === 'pending_deletion')
                                    <div class="mt-2 space-y-1">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-800 border border-rose-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-pulse"></span>
                                            Menunggu Persetujuan Hapus
                                        </span>
                                        <div class="text-[11px] text-slate-500">
                                            Diajukan oleh: <strong class="text-slate-700">{{ $p->deletionRequester?->name ?? 'Admin' }}</strong>
                                        </div>
                                        @if($p->deletion_reason)
                                            <div class="p-2 rounded-lg bg-rose-50/80 border border-rose-200 text-[11px] text-rose-900 leading-snug">
                                                <span class="font-semibold text-rose-950">Alasan:</span> "{{ $p->deletion_reason }}"
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                <!-- Badge Status: Menunggu Penyesuaian Stok -->
                                @if($p->pendingAdjustment)
                                    <div class="mt-2 space-y-1">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                            Menunggu Persetujuan Stok
                                        </span>
                                        <div class="text-[11px] text-slate-500">
                                            Diajukan oleh: <strong class="text-slate-700">{{ $p->pendingAdjustment->requester?->name ?? 'Admin' }}</strong>
                                            ·
                                            <span class="font-semibold text-amber-900">
                                                {{ $p->pendingAdjustment->proposed_data['movement_type'] === 'IN' ? 'Masuk (+)' : ($p->pendingAdjustment->proposed_data['movement_type'] === 'OUT' ? 'Keluar (-)' : 'Koreksi') }}
                                                {{ number_format($p->pendingAdjustment->proposed_data['quantity'] ?? 0, 0) }} {{ $p->unit }}
                                            </span>
                                        </div>
                                        @if($p->pendingAdjustment->reason)
                                            <div class="p-2 rounded-lg bg-amber-50/80 border border-amber-200 text-[11px] text-amber-900 leading-snug">
                                                <span class="font-semibold text-amber-950">Alasan:</span> "{{ $p->pendingAdjustment->reason }}"
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                <!-- Badge Status: Menunggu Perubahan Harga & Data -->
                                @if($p->pendingEdit)
                                    <div class="mt-2 space-y-1">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-indigo-50 text-indigo-800 border border-indigo-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-indigo-500 animate-pulse"></span>
                                            Menunggu Persetujuan Harga
                                        </span>
                                        <div class="text-[11px] text-slate-500">
                                            Diajukan oleh: <strong class="text-slate-700">{{ $p->pendingEdit->requester?->name ?? 'Admin' }}</strong>
                                            ·
                                            <span class="font-semibold text-indigo-900">
                                                Beli: Rp {{ number_format($p->pendingEdit->proposed_data['purchase_price'] ?? 0, 0, ',', '.') }} | Jual: Rp {{ number_format($p->pendingEdit->proposed_data['selling_price'] ?? 0, 0, ',', '.') }}
                                            </span>
                                        </div>
                                        @if($p->pendingEdit->reason)
                                            <div class="p-2 rounded-lg bg-indigo-50/80 border border-indigo-200 text-[11px] text-indigo-900 leading-snug">
                                                <span class="font-semibold text-indigo-950">Alasan:</span> "{{ $p->pendingEdit->reason }}"
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </td>

                            <td class="p-4 text-slate-700 whitespace-nowrap">Rp {{ number_format($p->purchase_price, 0, ',', '.') }}</td>
                            <td class="p-4 font-bold text-emerald-600 whitespace-nowrap">Rp {{ number_format($p->selling_price, 0, ',', '.') }}</td>

                            <td class="p-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-base {{ $p->isLowStock() ? 'text-rose-600' : 'text-slate-900' }}">
                                        {{ number_format($p->stock, 0) }}
                                    </span>
                                    <span class="text-xs font-medium text-slate-500">{{ $p->unit }}</span>
                                    @if($p->isLowStock())
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-pulse"></span> Stok Menipis
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aman
                                        </span>
                                    @endif
                                </div>
                                <div class="text-[11px] text-slate-400 mt-1">
                                    Batas minimum: {{ number_format($p->minimum_stock, 0) }} {{ $p->unit }}
                                </div>
                            </td>

                            <td class="p-4 text-center whitespace-nowrap">
                                <div class="flex justify-center items-center gap-2">
                                    {{-- 1. Aksi Adjust Stok --}}
                                    @if($p->pendingAdjustment)
                                        @if($isAdmin)
                                            <span class="px-3 py-1 bg-amber-50 text-amber-700 border border-amber-200 font-semibold text-xs rounded-lg flex items-center gap-1" title="Pengajuan penyesuaian stok sedang menunggu persetujuan Manager">
                                                <i class="fa-solid fa-hourglass-half text-[11px]"></i> Stok Menunggu Manager
                                            </span>
                                        @else
                                            <button type="button" 
                                                    @click="selectedProduct = {{ json_encode($p) }}; selectedChangeRequest = {{ json_encode($p->pendingAdjustment) }}; rejectNotes = ''; reviewAdjustmentModal = true" 
                                                    class="px-3 py-1 bg-amber-600 hover:bg-amber-700 text-white font-semibold text-xs rounded-lg transition flex items-center gap-1 shadow-xs" 
                                                    title="Tinjau & Setujui Penyesuaian Stok">
                                                <i class="fa-solid fa-boxes-packing text-[11px]"></i> Tinjau Stok
                                            </button>
                                        @endif
                                    @else
                                        <button type="button" 
                                                @click="selectedProduct = {{ json_encode($p) }}; adjustNotes = ''; adjustModal = true" 
                                                class="px-3 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-lg transition flex items-center gap-1" 
                                                title="{{ $isAdmin ? 'Ajukan Penyesuaian Stok ke Manager' : 'Adjust Stok Barang' }}">
                                            <i class="fa-solid fa-boxes-packing"></i> Adjust Stok
                                        </button>
                                    @endif

                                    {{-- 2. Aksi Edit Produk --}}
                                    @if(in_array($userRole, ['owner', 'manager', 'admin']))
                                        @if($p->deletion_status !== 'pending_deletion')
                                            @if($p->pendingEdit)
                                                @if($isAdmin)
                                                    <span class="px-3 py-1 bg-indigo-50 text-indigo-700 border border-indigo-200 font-semibold text-xs rounded-lg flex items-center gap-1" title="Pengajuan perubahan harga sedang menunggu persetujuan Manager">
                                                        <i class="fa-solid fa-hourglass-half text-[11px]"></i> Harga Menunggu Manager
                                                    </span>
                                                @else
                                                    <button type="button" 
                                                            @click="selectedProduct = {{ json_encode($p) }}; selectedChangeRequest = {{ json_encode($p->pendingEdit) }}; rejectNotes = ''; reviewEditModal = true" 
                                                            class="px-3 py-1 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs rounded-lg transition flex items-center gap-1 shadow-xs" 
                                                            title="Tinjau & Setujui Perubahan Harga">
                                                        <i class="fa-solid fa-clipboard-check text-[11px]"></i> Tinjau Harga
                                                    </button>
                                                @endif
                                            @else
                                                <button type="button" 
                                                        @click="selectedProduct = {{ json_encode($p) }}; editReason = ''; editModal = true" 
                                                        class="px-3 py-1 bg-amber-50 hover:bg-amber-100 text-amber-700 font-semibold text-xs rounded-lg transition flex items-center gap-1" 
                                                        title="{{ $isAdmin ? 'Ajukan Perubahan Data / Harga ke Manager' : 'Edit Data Produk (Manajemen)' }}">
                                                    <i class="fa-solid fa-pen-to-square"></i> Edit
                                                </button>
                                            @endif
                                        @endif

                                        {{-- 3. Aksi Hapus Produk --}}
                                        @if($p->deletion_status === 'pending_deletion')
                                            @if($isAdmin)
                                                <span class="px-3 py-1 bg-rose-50 text-rose-700 border border-rose-200 font-semibold text-xs rounded-lg flex items-center gap-1" title="Permohonan penghapusan sedang menunggu persetujuan Manager">
                                                    <i class="fa-solid fa-hourglass-half text-[11px]"></i> Menunggu Manager
                                                </span>
                                            @else
                                                <button type="button" 
                                                        @click="selectedProduct = {{ json_encode($p) }}; approveDeleteModal = true" 
                                                        class="px-3 py-1 bg-rose-600 hover:bg-rose-700 text-white font-semibold text-xs rounded-lg transition flex items-center gap-1 shadow-xs" 
                                                        title="Tinjau & Setujui Hapus Produk">
                                                    <i class="fa-solid fa-check text-[11px]"></i> Setujui Hapus
                                                </button>
                                                <button type="button" 
                                                        @click="selectedProduct = {{ json_encode($p) }}; rejectDeleteModal = true" 
                                                        class="px-3 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-lg transition flex items-center gap-1" 
                                                        title="Tolak Permohonan Hapus">
                                                    <i class="fa-solid fa-xmark text-[11px]"></i> Tolak
                                                </button>
                                            @endif
                                        @else
                                            @if($isAdmin)
                                                <button type="button" 
                                                        @click="selectedProduct = {{ json_encode($p) }}; deletionReason = ''; requestDeleteModal = true" 
                                                        class="px-3 py-1 bg-rose-50 hover:bg-rose-100 text-rose-700 font-semibold text-xs rounded-lg transition flex items-center gap-1" 
                                                        title="Ajukan Hapus ke Manager">
                                                    <i class="fa-solid fa-trash"></i> Hapus
                                                </button>
                                            @else
                                                <button type="button" 
                                                        @click="selectedProduct = {{ json_encode($p) }}; deleteModal = true" 
                                                        class="px-3 py-1 bg-rose-50 hover:bg-rose-100 text-rose-700 font-semibold text-xs rounded-lg transition flex items-center gap-1" 
                                                        title="Hapus Produk (Manajemen)">
                                                    <i class="fa-solid fa-trash"></i> Hapus
                                                </button>
                                            @endif
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-400">
                                @if($currentFilter === 'pending_deletion')
                                    Tidak ada produk yang sedang menunggu persetujuan hapus.
                                @elseif($currentFilter === 'pending_adjustment')
                                    Tidak ada produk yang sedang menunggu persetujuan penyesuaian stok.
                                @elseif($currentFilter === 'pending_edit')
                                    Tidak ada produk yang sedang menunggu persetujuan perubahan harga atau data.
                                @elseif($currentFilter === 'all_pending')
                                    Tidak ada permohonan persetujuan yang sedang pending saat ini.
                                @else
                                    Belum ada data produk.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100">
            {{ $products->links() }}
        </div>
    </div>

    {{-- MODAL 1: EDIT PRODUK (Admin ajukan izin / Manajemen langsung update) --}}
    @if(in_array($userRole, ['owner', 'manager', 'admin']))
    <div x-show="editModal" x-cloak style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" @keydown.escape.window="editModal = false">
        <div @click.outside="editModal = false" class="w-full max-w-lg overflow-hidden rounded-2xl bg-white shadow-2xl space-y-4 p-6">
            <div class="flex items-start justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 text-amber-600">
                        <i class="fa-solid fa-pen-to-square text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-900" x-text="isAdminUser ? 'Pengajuan Perubahan Harga & Data' : 'Edit Data Produk'"></h3>
                        <p class="text-xs font-medium text-slate-500" x-text="isAdminUser ? 'Wajib persetujuan Manager sebelum data diperbarui' : 'Khusus Manajemen. Perubahan tercatat di Log Aktivitas'"></p>
                    </div>
                </div>
                <button type="button" @click="editModal = false" class="text-slate-400 hover:text-slate-600 transition" aria-label="Tutup form edit">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- Pemberitahuan SOP untuk Admin -->
            <div x-show="isAdminUser" class="flex gap-3 rounded-xl border border-amber-200 bg-amber-50/80 p-3.5 text-amber-900">
                <i class="fa-solid fa-circle-info mt-0.5 text-amber-600 shrink-0"></i>
                <div class="text-xs leading-5">
                    <span class="font-bold">Kebijakan SOP PT Signal Panca Utama:</span>
                    <p class="mt-0.5 text-amber-800/90 text-[11px]">
                        Perubahan harga beli, harga jual, atau nama produk oleh Admin wajib memperoleh persetujuan Manager untuk mencegah manipulasi data. Catatan alasan perubahan wajib disertakan.
                    </p>
                </div>
            </div>

            <form x-bind:action="selectedProduct ? (isAdminUser ? ('/products/' + selectedProduct.id + '/request-edit') : ('/products/' + selectedProduct.id)) : '#'" method="POST" class="space-y-4">
                @csrf
                <template x-if="!isAdminUser">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div class="rounded-xl border border-blue-100 bg-blue-50/70 p-3 text-xs text-blue-800 flex items-center gap-2">
                    <i class="fa-solid fa-shield-halved text-blue-600"></i>
                    <span>SKU: <strong class="font-mono text-blue-900" x-text="selectedProduct?.sku"></strong> · Kategori: <strong x-text="selectedProduct?.category || '-'"></strong></span>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Nama Produk <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" required x-bind:value="selectedProduct?.name" class="w-full px-3 py-2 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-amber-500 border-slate-200 text-slate-800">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Harga Beli (Rp) <span class="text-rose-500">*</span></label>
                        <input type="number" name="purchase_price" required min="0" x-bind:value="selectedProduct?.purchase_price" class="w-full px-3 py-2 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-amber-500 border-slate-200 text-slate-800">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Harga Jual (Rp) <span class="text-rose-500">*</span></label>
                        <input type="number" name="selling_price" required min="0" x-bind:value="selectedProduct?.selling_price" class="w-full px-3 py-2 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-amber-500 border-slate-200 text-slate-800">
                    </div>
                </div>

                <!-- Input Alasan / Catatan Perubahan Harga (Wajib untuk Admin) -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">
                        Alasan / Catatan Perubahan Harga & Data <span class="text-rose-500" x-show="isAdminUser">*</span>
                    </label>
                    <textarea name="reason" 
                              x-model="editReason"
                              x-bind:required="isAdminUser" 
                              minlength="5" 
                              rows="3" 
                              placeholder="Tuliskan alasan jelas mengapa harga beli/jual atau nama produk diubah (contoh: penyesuaian dari supplier per batch baru, kenaikan margin laba tender)..." 
                              class="w-full px-3 py-2 border rounded-xl text-xs outline-none focus:ring-2 focus:ring-amber-500 border-slate-200 text-slate-800"></textarea>
                    <p class="text-[11px] text-slate-400 mt-1" x-show="isAdminUser">Minimal 5 karakter. Catatan ini akan ditinjau langsung oleh Manager.</p>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="button" @click="editModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm rounded-xl transition">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-amber-600 hover:bg-amber-700 text-white font-semibold text-sm rounded-xl shadow transition flex items-center gap-1.5">
                        <i x-bind:class="isAdminUser ? 'fa-solid fa-paper-plane' : 'fa-solid fa-floppy-disk'"></i>
                        <span x-text="isAdminUser ? 'Kirim Pengajuan ke Manager' : 'Simpan Perubahan'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- MODAL 2: ADJUST STOK (Admin ajukan izin / Manajemen langsung update) --}}
    <div x-show="adjustModal" x-cloak style="display: none;" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4" @keydown.escape.window="adjustModal = false">
        <div @click.outside="adjustModal = false" class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
            <div class="flex justify-between items-center border-b pb-3">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 text-amber-600">
                        <i class="fa-solid fa-boxes-packing text-lg"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-lg text-slate-800" x-text="isAdminUser ? 'Pengajuan Penyesuaian Stok' : 'Adjust / Penyesuaian Stok'"></h3>
                        <p class="text-xs font-medium text-slate-400" x-text="isAdminUser ? 'Wajib persetujuan Manager sebelum stok berubah' : 'Tindakan langsung Manajemen gudang'"></p>
                    </div>
                </div>
                <button type="button" @click="adjustModal = false" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>

            <!-- Info Produk Saat Ini -->
            <div class="rounded-xl border border-slate-200 bg-slate-50/70 p-3 text-xs space-y-1">
                <div class="flex items-center justify-between">
                    <span class="text-slate-500">Produk:</span>
                    <span class="font-bold text-slate-800" x-text="selectedProduct?.name"></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-500">Stok Saat Ini:</span>
                    <span class="font-semibold text-slate-900" x-text="(selectedProduct?.stock ?? 0) + ' ' + (selectedProduct?.unit ?? 'Unit')"></span>
                </div>
            </div>

            <!-- Pemberitahuan SOP untuk Admin -->
            <div x-show="isAdminUser" class="flex gap-3 rounded-xl border border-amber-200 bg-amber-50/80 p-3 text-amber-900">
                <i class="fa-solid fa-circle-info mt-0.5 text-amber-600 shrink-0"></i>
                <div class="text-xs leading-relaxed">
                    <span class="font-bold">Kebijakan SOP:</span>
                    <p class="mt-0.5 text-amber-800/90 text-[11px]">
                        Setiap penambahan atau pengurangan stok manual oleh Admin wajib diverifikasi oleh Manager guna menghindari penyalahgunaan stok fisik gudang.
                    </p>
                </div>
            </div>

            <form x-bind:action="selectedProduct ? (isAdminUser ? ('/products/' + selectedProduct.id + '/request-adjustment') : ('/products/' + selectedProduct.id + '/adjust')) : '#'" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Jenis Pergerakan <span class="text-rose-500">*</span></label>
                    <select name="movement_type" required class="w-full px-3 py-2 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-amber-500 border-slate-200 text-slate-800">
                        <option value="IN">Stok Masuk (+)</option>
                        <option value="OUT">Stok Keluar (-)</option>
                        <option value="ADJUSTMENT">Koreksi / Stock Opname</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Jumlah Kuantitas <span class="text-rose-500">*</span></label>
                    <input type="number" step="0.01" min="0.01" name="quantity" required placeholder="Contoh: 5" class="w-full px-3 py-2 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-amber-500 border-slate-200 text-slate-800">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">
                        Alasan Penyesuaian / Catatan <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="notes" 
                              x-model="adjustNotes"
                              required 
                              minlength="5" 
                              rows="3" 
                              class="w-full px-3 py-2 border rounded-xl text-xs outline-none focus:ring-2 focus:ring-amber-500 border-slate-200 text-slate-800" 
                              placeholder="Tuliskan catatan detail mengapa stok di-adjust (contoh: hasil stock opname fisik tanggal 10, barang retur customer, atau barang rusak)..."></textarea>
                    <p class="text-[11px] text-slate-400 mt-1">Minimal 5 karakter. Catatan ini akan diperiksa oleh Manager.</p>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="button" @click="adjustModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm rounded-xl">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-amber-600 hover:bg-amber-700 text-white font-semibold text-sm rounded-xl shadow flex items-center gap-1.5">
                        <i x-bind:class="isAdminUser ? 'fa-solid fa-paper-plane' : 'fa-solid fa-boxes-packing'"></i>
                        <span x-text="isAdminUser ? 'Kirim Pengajuan ke Manager' : 'Update Stok'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL 3: TINJAU PENYESUAIAN STOK (Khusus Manager / Owner) --}}
    @if($isManagerOrOwner)
    <div x-show="reviewAdjustmentModal" x-cloak style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" @keydown.escape.window="reviewAdjustmentModal = false">
        <div @click.outside="reviewAdjustmentModal = false" class="w-full max-w-lg overflow-hidden rounded-2xl bg-white shadow-2xl">
            <div class="flex items-start justify-between border-b border-slate-100 px-6 py-5">
                <div class="flex items-center gap-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-amber-50 text-amber-600">
                        <i class="fa-solid fa-boxes-packing text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">Tinjau Permohonan Penyesuaian Stok</h3>
                        <p class="text-xs font-medium text-amber-700">Persetujuan Manajemen Inventaris Gudang</p>
                    </div>
                </div>
                <button type="button" @click="reviewAdjustmentModal = false" class="text-slate-400 transition hover:text-slate-700" aria-label="Tutup tinjau stok">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <div class="space-y-4 px-6 py-5">
                <!-- Detail Produk -->
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 text-xs space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">SKU Produk:</span>
                        <span class="font-mono font-bold text-slate-900" x-text="selectedProduct?.sku"></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Nama Produk:</span>
                        <span class="font-bold text-slate-800" x-text="selectedProduct?.name"></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Stok Saat Ini:</span>
                        <span class="font-semibold text-slate-800" x-text="(selectedProduct?.stock ?? 0) + ' ' + (selectedProduct?.unit ?? 'Unit')"></span>
                    </div>
                </div>

                <!-- Detail Pengajuan Perubahan Stok -->
                <div class="rounded-xl border border-amber-200 bg-amber-50/70 p-4 text-xs space-y-2.5">
                    <div class="flex items-center justify-between">
                        <span class="text-amber-900 font-semibold">Jenis Pergerakan:</span>
                        <span class="font-bold px-2 py-0.5 rounded text-[11px]" 
                              x-bind:class="{
                                  'bg-emerald-100 text-emerald-800': selectedChangeRequest?.proposed_data?.movement_type === 'IN',
                                  'bg-rose-100 text-rose-800': selectedChangeRequest?.proposed_data?.movement_type === 'OUT',
                                  'bg-blue-100 text-blue-800': selectedChangeRequest?.proposed_data?.movement_type === 'ADJUSTMENT'
                              }"
                              x-text="selectedChangeRequest?.proposed_data?.movement_type === 'IN' ? 'Stok Masuk (+)' : (selectedChangeRequest?.proposed_data?.movement_type === 'OUT' ? 'Stok Keluar (-)' : 'Koreksi Opname')"></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-amber-900 font-semibold">Jumlah Kuantitas:</span>
                        <span class="font-bold text-slate-900 text-sm" x-text="(selectedChangeRequest?.proposed_data?.quantity ?? 0) + ' ' + (selectedProduct?.unit ?? 'Unit')"></span>
                    </div>
                    <div class="pt-2 border-t border-amber-200">
                        <span class="text-amber-950 font-semibold block text-[11px]">Alasan Penyesuaian oleh Admin:</span>
                        <div class="mt-1 p-3 bg-white rounded-lg border border-amber-200 text-slate-800 italic text-xs leading-relaxed" x-text="selectedChangeRequest?.reason || 'Tidak ada catatan'"></div>
                    </div>
                    <div class="text-[11px] text-slate-500 pt-1">
                        Diajukan oleh: <strong class="text-slate-700" x-text="selectedChangeRequest?.requester?.name || 'Admin'"></strong>
                    </div>
                </div>

                <!-- Input Catatan Penolakan (jika ditolak) -->
                <form x-bind:action="selectedChangeRequest ? ('/products/change-requests/' + selectedChangeRequest.id + '/reject') : '#'" method="POST" id="rejectAdjustmentForm" class="space-y-2">
                    @csrf
                    <label class="block text-xs font-semibold text-slate-700 uppercase">
                        Catatan Penolakan (Opsional jika ingin menolak)
                    </label>
                    <input type="text" 
                           name="notes" 
                           x-model="rejectNotes" 
                           placeholder="Contoh: Jumlah fisik di gudang tidak sesuai atau belum diverifikasi supervisor..." 
                           class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs outline-none focus:ring-2 focus:ring-slate-400 text-slate-800">
                </form>
            </div>

            <div class="flex justify-end items-center gap-2 border-t border-slate-100 bg-slate-50 px-6 py-4">
                <button type="button" @click="reviewAdjustmentModal = false" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-100">
                    Batal
                </button>

                <button type="submit" form="rejectAdjustmentForm" class="rounded-xl bg-slate-800 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-900 flex items-center gap-1.5 shadow-sm">
                    <i class="fa-solid fa-xmark text-xs"></i> Tolak Pengajuan
                </button>

                <form x-bind:action="selectedChangeRequest ? ('/products/change-requests/' + selectedChangeRequest.id + '/approve') : '#'" method="POST">
                    @csrf
                    <button type="submit" class="rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700 flex items-center gap-1.5">
                        <i class="fa-solid fa-check text-xs"></i> Setujui & Update Stok
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL 4: TINJAU PERUBAHAN HARGA & DATA (Khusus Manager / Owner) --}}
    <div x-show="reviewEditModal" x-cloak style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" @keydown.escape.window="reviewEditModal = false">
        <div @click.outside="reviewEditModal = false" class="w-full max-w-lg overflow-hidden rounded-2xl bg-white shadow-2xl">
            <div class="flex items-start justify-between border-b border-slate-100 px-6 py-5">
                <div class="flex items-center gap-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600">
                        <i class="fa-solid fa-pen-to-square text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">Tinjau Permohonan Perubahan Harga & Data</h3>
                        <p class="text-xs font-medium text-indigo-700">Persetujuan Manajemen Katalog Produk</p>
                    </div>
                </div>
                <button type="button" @click="reviewEditModal = false" class="text-slate-400 transition hover:text-slate-700" aria-label="Tutup tinjau harga">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <div class="space-y-4 px-6 py-5">
                <!-- Info Header Produk -->
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-3.5 text-xs flex items-center justify-between">
                    <div>
                        <span class="text-slate-500">SKU:</span>
                        <strong class="font-mono text-blue-700 ml-1" x-text="selectedProduct?.sku"></strong>
                    </div>
                    <div>
                        <span class="text-slate-500">Kategori:</span>
                        <strong class="text-slate-800 ml-1" x-text="selectedProduct?.category || 'Umum'"></strong>
                    </div>
                </div>

                <!-- Perbandingan Side-by-Side: Saat Ini vs Pengajuan Baru -->
                <div class="rounded-xl border border-indigo-200 overflow-hidden text-xs">
                    <div class="bg-indigo-50/80 px-3 py-2 border-b border-indigo-100 font-bold text-indigo-900 flex items-center justify-between">
                        <span>Perbandingan Data Produk</span>
                        <span class="text-[11px] font-normal text-indigo-700">Sebelum vs Pengajuan Baru</span>
                    </div>
                    <div class="divide-y divide-slate-100 bg-white">
                        <div class="p-3">
                            <span class="text-slate-500 text-[11px] block">Nama Produk:</span>
                            <div class="mt-1 grid grid-cols-2 gap-2">
                                <div class="text-slate-600 line-through" x-text="selectedProduct?.name"></div>
                                <div class="font-bold text-indigo-900" x-text="selectedChangeRequest?.proposed_data?.name"></div>
                            </div>
                        </div>
                        <div class="p-3">
                            <span class="text-slate-500 text-[11px] block">Harga Beli:</span>
                            <div class="mt-1 grid grid-cols-2 gap-2">
                                <div class="text-slate-600" x-text="'Rp ' + Number(selectedProduct?.purchase_price ?? 0).toLocaleString('id-ID')"></div>
                                <div class="font-bold text-amber-700" x-text="'Rp ' + Number(selectedChangeRequest?.proposed_data?.purchase_price ?? 0).toLocaleString('id-ID')"></div>
                            </div>
                        </div>
                        <div class="p-3">
                            <span class="text-slate-500 text-[11px] block">Harga Jual:</span>
                            <div class="mt-1 grid grid-cols-2 gap-2">
                                <div class="text-slate-600" x-text="'Rp ' + Number(selectedProduct?.selling_price ?? 0).toLocaleString('id-ID')"></div>
                                <div class="font-bold text-emerald-700" x-text="'Rp ' + Number(selectedChangeRequest?.proposed_data?.selling_price ?? 0).toLocaleString('id-ID')"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Catatan Alasan dari Admin -->
                <div class="rounded-xl border border-amber-200 bg-amber-50/70 p-3.5 text-xs space-y-1.5">
                    <span class="font-semibold text-amber-950 block text-[11px]">Alasan Perubahan oleh Admin:</span>
                    <div class="p-3 bg-white rounded-lg border border-amber-200 text-slate-800 italic text-xs leading-relaxed" x-text="selectedChangeRequest?.reason || 'Tidak ada catatan'"></div>
                    <div class="text-[11px] text-slate-500 pt-1">
                        Diajukan oleh: <strong class="text-slate-700" x-text="selectedChangeRequest?.requester?.name || 'Admin'"></strong>
                    </div>
                </div>

                <!-- Input Catatan Penolakan (jika ditolak) -->
                <form x-bind:action="selectedChangeRequest ? ('/products/change-requests/' + selectedChangeRequest.id + '/reject') : '#'" method="POST" id="rejectEditForm" class="space-y-2">
                    @csrf
                    <label class="block text-xs font-semibold text-slate-700 uppercase">
                        Catatan Penolakan (Opsional jika ingin menolak)
                    </label>
                    <input type="text" 
                           name="notes" 
                           x-model="rejectNotes" 
                           placeholder="Contoh: Harga jual belum diverifikasi dengan direksi atau selisih margin terlalu tipis..." 
                           class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs outline-none focus:ring-2 focus:ring-slate-400 text-slate-800">
                </form>
            </div>

            <div class="flex justify-end items-center gap-2 border-t border-slate-100 bg-slate-50 px-6 py-4">
                <button type="button" @click="reviewEditModal = false" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-100">
                    Batal
                </button>

                <button type="submit" form="rejectEditForm" class="rounded-xl bg-slate-800 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-900 flex items-center gap-1.5 shadow-sm">
                    <i class="fa-solid fa-xmark text-xs"></i> Tolak Perubahan
                </button>

                <form x-bind:action="selectedChangeRequest ? ('/products/change-requests/' + selectedChangeRequest.id + '/approve') : '#'" method="POST">
                    @csrf
                    <button type="submit" class="rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700 flex items-center gap-1.5">
                        <i class="fa-solid fa-check text-xs"></i> Setujui & Terapkan Perubahan
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL 5: PERMOHONAN IZIN HAPUS (Admin ke Manager) --}}
    <div x-show="requestDeleteModal" x-cloak style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" @keydown.escape.window="requestDeleteModal = false">
        <div @click.outside="requestDeleteModal = false" class="w-full max-w-lg overflow-hidden rounded-2xl bg-white shadow-2xl">
            <div class="flex items-start justify-between border-b border-slate-100 px-6 py-5">
                <div class="flex items-center gap-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-rose-50 text-rose-600">
                        <i class="fa-solid fa-clipboard-question text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">Permohonan Izin Hapus Produk</h3>
                        <p class="text-xs font-medium text-slate-500">Memerlukan persetujuan Manager sebelum produk dihapus</p>
                    </div>
                </div>
                <button type="button" @click="requestDeleteModal = false" class="text-slate-400 transition hover:text-slate-700" aria-label="Tutup form permohonan hapus">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <div class="space-y-4 px-6 py-5">
                <!-- Ringkasan Produk yang Mau Dihapus -->
                <div class="rounded-xl border border-slate-200 bg-slate-50/70 p-3.5 text-xs space-y-1.5">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">SKU Produk:</span>
                        <span class="font-mono font-bold text-blue-700" x-text="selectedProduct?.sku"></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Nama Produk:</span>
                        <span class="font-bold text-slate-800" x-text="selectedProduct?.name"></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Kategori:</span>
                        <span class="text-slate-700" x-text="selectedProduct?.category || 'Umum'"></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Stok Saat Ini:</span>
                        <span class="font-semibold text-slate-800" x-text="(selectedProduct?.stock ?? 0) + ' ' + (selectedProduct?.unit ?? 'Unit')"></span>
                    </div>
                </div>

                <!-- Pemberitahuan SOP Kebijakan -->
                <div class="flex gap-3 rounded-xl border border-rose-200 bg-rose-50/80 p-3.5 text-rose-900">
                    <i class="fa-solid fa-circle-info mt-0.5 text-rose-600 shrink-0"></i>
                    <div class="text-xs leading-5">
                        <span class="font-bold">Kebijakan SOP PT Signal Panca Utama:</span>
                        <p class="mt-0.5 text-rose-800/90 text-[11px]">
                            Penghapusan barang gudang oleh Admin wajib memperoleh izin dari Manager. Anda wajib memberi tahu alasan penghapusan barang. Data tetap tersimpan hingga disetujui Manager.
                        </p>
                    </div>
                </div>

                <!-- Form Permohonan Hapus -->
                <form x-bind:action="selectedProduct ? ('/products/' + selectedProduct.id + '/request-deletion') : '#'" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">
                            Alasan Penghapusan Barang <span class="text-rose-500">*</span>
                        </label>
                        <textarea name="deletion_reason" 
                                  x-model="deletionReason" 
                                  required 
                                  minlength="5" 
                                  rows="3" 
                                  placeholder="Tuliskan alasan jelas mengapa barang ini mau dihapus dari gudang (contoh: barang rusak fisik, duplikasi data input, atau sudah tidak dipasarkan)..." 
                                  class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs outline-none focus:ring-2 focus:ring-rose-500 text-slate-800"></textarea>
                        <p class="text-[11px] text-slate-400 mt-1">Minimal 5 karakter. Alasan ini akan ditinjau langsung oleh Manager.</p>
                    </div>

                    <div class="flex justify-end gap-3 pt-3 border-t border-slate-100">
                        <button type="button" @click="requestDeleteModal = false" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-100">
                            Batal
                        </button>
                        <button type="submit" class="rounded-xl bg-rose-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-rose-700 flex items-center gap-1.5">
                            <i class="fa-solid fa-paper-plane text-xs"></i> Kirim Permohonan ke Manager
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL 6: PERSETUJUAN HAPUS PRODUK (Khusus Manager / Owner) --}}
    @if($isManagerOrOwner)
    <div x-show="approveDeleteModal" x-cloak style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" @keydown.escape.window="approveDeleteModal = false">
        <div @click.outside="approveDeleteModal = false" class="w-full max-w-lg overflow-hidden rounded-2xl bg-white shadow-2xl">
            <div class="flex items-start justify-between border-b border-slate-100 px-6 py-5">
                <div class="flex items-center gap-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-rose-50 text-rose-600">
                        <i class="fa-solid fa-triangle-exclamation text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">Persetujuan Hapus Produk Gudang</h3>
                        <p class="text-xs font-medium text-rose-600">Persetujuan Manajemen (Penghapusan Permanen)</p>
                    </div>
                </div>
                <button type="button" @click="approveDeleteModal = false" class="text-slate-400 transition hover:text-slate-700" aria-label="Tutup persetujuan hapus">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <div class="space-y-4 px-6 py-5">
                <!-- Detail Produk & Alasan Admin -->
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 text-xs space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">SKU Produk:</span>
                        <span class="font-mono font-bold text-slate-900" x-text="selectedProduct?.sku"></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Nama Produk:</span>
                        <span class="font-bold text-slate-800" x-text="selectedProduct?.name"></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Kategori:</span>
                        <span class="text-slate-700" x-text="selectedProduct?.category || '-'"></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Stok Saat Ini:</span>
                        <span class="font-semibold text-slate-800" x-text="(selectedProduct?.stock ?? 0) + ' ' + (selectedProduct?.unit ?? 'Unit')"></span>
                    </div>
                    <div class="pt-2 border-t border-slate-200">
                        <span class="text-slate-600 font-semibold block text-[11px]">Alasan Penghapusan oleh Admin:</span>
                        <div class="mt-1 p-3 bg-white rounded-lg border border-slate-200 text-slate-800 italic text-xs leading-relaxed" x-text="selectedProduct?.deletion_reason || 'Tidak ada keterangan'"></div>
                    </div>
                    <div class="text-[11px] text-slate-500 pt-1" x-show="selectedProduct && selectedProduct.deletion_requester">
                        Diajukan oleh: <strong class="text-slate-700" x-text="selectedProduct?.deletion_requester?.name || 'Admin'"></strong>
                    </div>
                </div>

                <div class="flex gap-3 rounded-xl border border-rose-200 bg-rose-50 p-3.5 text-rose-900">
                    <i class="fa-solid fa-circle-exclamation mt-0.5 text-rose-600 shrink-0"></i>
                    <p class="text-xs leading-5">
                        Dengan menyetujui, produk ini akan dihapus dari katalog gudang dan pergerakan stok historis akan dicatat ke log audit.
                    </p>
                </div>
            </div>

            <div class="flex justify-end gap-3 border-t border-slate-100 bg-slate-50 px-6 py-4">
                <button type="button" @click="approveDeleteModal = false" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-100">
                    Batal
                </button>
                <form x-bind:action="selectedProduct ? ('/products/' + selectedProduct.id + '/approve-deletion') : '#'" method="POST">
                    @csrf
                    <button type="submit" class="rounded-xl bg-rose-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-rose-700 flex items-center gap-1.5">
                        <i class="fa-solid fa-check text-xs"></i> Setujui & Hapus Produk
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL 7: TOLAK PERMOHONAN HAPUS PRODUK (Khusus Manager / Owner) --}}
    <div x-show="rejectDeleteModal" x-cloak style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" @keydown.escape.window="rejectDeleteModal = false">
        <div @click.outside="rejectDeleteModal = false" class="w-full max-w-md overflow-hidden rounded-2xl bg-white shadow-2xl">
            <div class="flex items-start justify-between border-b border-slate-100 px-6 py-5">
                <div class="flex items-center gap-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-slate-100 text-slate-700">
                        <i class="fa-solid fa-shield-halved text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">Tolak Permohonan Hapus</h3>
                        <p class="text-xs font-medium text-slate-500">Pertahankan produk ini di dalam gudang</p>
                    </div>
                </div>
                <button type="button" @click="rejectDeleteModal = false" class="text-slate-400 transition hover:text-slate-700" aria-label="Tutup form tolak hapus">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form x-bind:action="selectedProduct ? ('/products/' + selectedProduct.id + '/reject-deletion') : '#'" method="POST" class="space-y-4 px-6 py-5">
                @csrf
                <p class="text-xs text-slate-600 leading-relaxed">
                    Permohonan penghapusan produk <strong class="text-slate-900" x-text="selectedProduct?.name"></strong> akan ditolak, dan produk tetap aktif di katalog persediaan.
                </p>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">
                        Catatan Penolakan (Opsional)
                    </label>
                    <textarea name="notes" rows="3" placeholder="Tuliskan catatan mengapa permohonan ini ditolak (misal: barang masih memiliki stok aktif atau proses retur belum selesai)..." class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs outline-none focus:ring-2 focus:ring-slate-400 text-slate-800"></textarea>
                </div>

                <div class="flex justify-end gap-3 pt-3 border-t border-slate-100">
                    <button type="button" @click="rejectDeleteModal = false" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-100">
                        Batal
                    </button>
                    <button type="submit" class="rounded-xl bg-slate-800 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-900 flex items-center gap-1.5">
                        <i class="fa-solid fa-ban text-xs"></i> Tolak Permohonan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL 8: HAPUS PRODUK LANGSUNG (Khusus Manajemen: Manager / Owner) --}}
    <div x-show="deleteModal" x-cloak style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" @keydown.escape.window="deleteModal = false">
        <div @click.outside="deleteModal = false" class="w-full max-w-md overflow-hidden rounded-2xl bg-white shadow-2xl">
            <div class="flex items-start justify-between border-b border-slate-100 px-6 py-5">
                <div class="flex items-center gap-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-rose-50 text-rose-600">
                        <i class="fa-solid fa-triangle-exclamation text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">Hapus Produk?</h3>
                        <p class="text-xs font-medium text-slate-400">Konfirmasi tindakan Manajemen</p>
                    </div>
                </div>
                <button type="button" @click="deleteModal = false" class="text-slate-400 transition hover:text-slate-700" aria-label="Tutup konfirmasi hapus">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <div class="space-y-4 px-6 py-5">
                <p class="text-sm leading-6 text-slate-600">
                    Anda akan menghapus produk <strong class="text-slate-900" x-text="selectedProduct?.name"></strong> dari katalog gudang.
                </p>
                <div class="flex gap-3 rounded-xl border border-amber-200 bg-amber-50 p-3.5 text-amber-800">
                    <i class="fa-solid fa-circle-info mt-0.5"></i>
                    <p class="text-xs leading-5">Tindakan ini akan dicatat ke log audit beserta data produk sebelum dihapus.</p>
                </div>
            </div>

            <div class="flex justify-end gap-3 border-t border-slate-100 bg-slate-50 px-6 py-4">
                <button type="button" @click="deleteModal = false" class="rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-100">Batal</button>
                <form x-bind:action="selectedProduct ? ('/products/' + selectedProduct.id) : '#'" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="rounded-xl bg-rose-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-rose-700">
                        <i class="fa-solid fa-trash-can mr-1"></i> Hapus Produk
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL 9: CREATE PRODUK BARU --}}
    <div x-show="createModal" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4" x-cloak @keydown.escape.window="createModal = false">
        <div @click.outside="createModal = false" class="bg-white rounded-2xl max-w-xl w-full p-6 shadow-2xl space-y-4">
            <div class="flex justify-between items-center border-b pb-3">
                <h3 class="font-bold text-lg text-slate-800">Tambah Produk Master Baru</h3>
                <button @click="createModal = false" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>

            <form action="{{ route('products.store') }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">SKU Produk</label>
                        <input type="text" name="sku" required value="SKU-{{ rand(1000,9999) }}" class="w-full px-3 py-2 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Kategori</label>
                        <input type="text" name="category" placeholder="Komputer / Printer / ATK" class="w-full px-3 py-2 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Nama Produk</label>
                    <input type="text" name="name" required placeholder="Laptop Asus Core i7..." class="w-full px-3 py-2 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Satuan</label>
                        <input type="text" name="unit" required value="Unit" class="w-full px-3 py-2 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Harga Beli (Rp)</label>
                        <input type="number" name="purchase_price" required placeholder="8000000" class="w-full px-3 py-2 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Harga Jual (Rp)</label>
                        <input type="number" name="selling_price" required placeholder="9500000" class="w-full px-3 py-2 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Stok Minimum Warning</label>
                        <input type="number" name="minimum_stock" required value="5" class="w-full px-3 py-2 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Stok Awal</label>
                        <input type="number" name="initial_stock" value="10" class="w-full px-3 py-2 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t">
                    <button type="button" @click="createModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm rounded-xl">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-xl shadow">Simpan Produk</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
