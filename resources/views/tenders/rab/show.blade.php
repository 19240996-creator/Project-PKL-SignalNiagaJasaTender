@extends('layouts.app')

@section('title', 'RAB Tender: ' . $tender->tender_number . ' - PT Signal Panca Utama')
@section('header-title', 'Rincian Estimasi & RAB Tender')

@section('content')
<div class="space-y-6" x-data="{
    addItemModal: false,
    editItemModal: false,
    reviseModal: false,
    selectedItem: null,
    activeTab: 'all',
    products: {{ Js::from($products) }},
    formItem: {
        category: 'barang',
        product_id: '',
        item_name: '',
        quantity: 1,
        unit: 'Unit',
        unit_cost: 0,
        unit_price: 0,
        notes: ''
    },
    onProductChange(prodId) {
        if (!prodId) return;
        const prod = this.products.find(p => p.id == prodId);
        if (prod) {
            this.formItem.item_name = prod.name;
            this.formItem.unit = prod.unit || 'Unit';
            this.formItem.unit_cost = parseFloat(prod.purchase_price) || 0;
            if (!this.formItem.unit_price || this.formItem.unit_price == 0) {
                this.formItem.unit_price = parseFloat(prod.selling_price) || 0;
            }
        }
    },
    openEditItem(item) {
        this.selectedItem = item;
        this.formItem.category = item.category;
        this.formItem.product_id = item.product_id || '';
        this.formItem.item_name = item.item_name;
        this.formItem.quantity = parseFloat(item.quantity) || 1;
        this.formItem.unit = item.unit || 'Unit';
        this.formItem.unit_cost = parseFloat(item.unit_cost) || 0;
        this.formItem.unit_price = parseFloat(item.unit_price) || 0;
        this.formItem.notes = item.notes || '';
        this.editItemModal = true;
    }
}">

    <!-- Breadcrumb & Header Nav -->
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-3 bg-white p-4 rounded-lg border border-slate-200 shadow-xs">
        <div>
            <div class="flex items-center space-x-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('tender.index') }}" class="hover:text-blue-600 transition">Administrasi Tender</a>
                <span>/</span>
                <a href="{{ route('tender.rab.index') }}" class="hover:text-blue-600 transition">Estimasi RAB</a>
                <span>/</span>
                <span class="font-mono text-slate-800 font-semibold">{{ $tender->tender_number }}</span>
            </div>
            <h1 class="text-base font-bold text-slate-900 leading-tight">{{ $tender->name }}</h1>
            <div class="text-xs text-slate-500 mt-0.5 flex flex-wrap items-center gap-2">
                <span>Klien: <strong class="text-slate-700">{{ $tender->client->name ?? 'N/A' }}</strong></span>
                <span>•</span>
                <span>Deadline: <strong class="text-slate-700">{{ $tender->deadline ? \Carbon\Carbon::parse($tender->deadline)->format('d M Y') : '-' }}</strong></span>
                <span>•</span>
                <span>Metode: <strong class="text-slate-700">{{ $tender->metode_penanganan === 'vendor_relasi' ? 'Vendor Relasi (' . ($tender->nama_vendor_relasi ?: 'Mitra') . ')' : 'Internal PT SPU' }}</strong></span>
            </div>
        </div>

        <div class="flex items-center gap-2 w-full md:w-auto">
            <a href="{{ route('tender.index') }}" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-md transition flex items-center gap-1.5">
                <i class="fa-solid fa-folder-tree text-[11px]"></i> Administrasi
            </a>
            <a href="{{ route('tender.proyek.show', $tender) }}" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-md transition flex items-center gap-1.5">
                <i class="fa-solid fa-helmet-safety text-[11px]"></i> Lapangan Proyek
            </a>
        </div>
    </div>

    <!-- KPI Summary Bar (Perhitungan Otomatis RAB) -->
    <div class="grid grid-cols-2 md:grid-cols-6 gap-3">
        <!-- 1. Total HPP Modal -->
        <div class="bg-white p-3.5 rounded-lg border border-slate-200 shadow-xs">
            <div class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Total HPP Modal</div>
            <div class="text-lg font-bold text-slate-900 mt-1 font-numeric">Rp {{ number_format($tender->total_rab_cost, 0, ',', '.') }}</div>
            <div class="text-[10px] text-slate-400 mt-0.5">Biaya dasar operasional</div>
        </div>

        <!-- 2. Nilai Penawaran -->
        <div class="bg-white p-3.5 rounded-lg border border-slate-200 shadow-xs">
            <div class="text-[11px] font-semibold text-blue-600 uppercase tracking-wider">Nilai Penawaran (Bid)</div>
            <div class="text-lg font-bold text-blue-700 mt-1 font-numeric">Rp {{ number_format($tender->bid_value > 0 ? $tender->bid_value : $tender->total_rab_price, 0, ',', '.') }}</div>
            <div class="text-[10px] text-blue-500 mt-0.5">Penawaran ke klien</div>
        </div>

        <!-- 3. Estimasi Keuntungan -->
        <div class="bg-white p-3.5 rounded-lg border border-slate-200 shadow-xs">
            <div class="text-[11px] font-semibold text-emerald-600 uppercase tracking-wider">Estimasi Laba Kotor</div>
            @php $profit = $tender->estimated_profit; @endphp
            <div class="text-lg font-bold {{ $profit >= 0 ? 'text-emerald-700' : 'text-rose-700' }} mt-1 font-numeric">
                Rp {{ number_format($profit, 0, ',', '.') }}
            </div>
            <div class="text-[10px] text-slate-400 mt-0.5">Penawaran - Total HPP</div>
        </div>

        <!-- 4. Margin Keuntungan -->
        <div class="bg-white p-3.5 rounded-lg border border-slate-200 shadow-xs">
            <div class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Margin Keuntungan</div>
            @php $margin = $tender->profit_margin_percentage; @endphp
            <div class="text-lg font-bold {{ $margin >= 20 ? 'text-emerald-700' : ($margin > 0 ? 'text-amber-700' : 'text-rose-700') }} mt-1 font-numeric">
                {{ $margin }}%
            </div>
            <div class="text-[10px] text-slate-400 mt-0.5">Persentase margin laba</div>
        </div>

        <!-- 5. Kesiapan Material Dagang -->
        <div class="bg-white p-3.5 rounded-lg border border-slate-200 shadow-xs">
            <div class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Material di Gudang</div>
            @php $defCount = $tender->deficit_items_count; @endphp
            <div class="text-lg font-bold {{ $defCount > 0 ? 'text-rose-600' : 'text-emerald-700' }} mt-1 font-numeric">
                {{ $defCount > 0 ? $defCount . ' Item Kurang' : 'Stok Cukup' }}
            </div>
            <div class="text-[10px] text-slate-400 mt-0.5">Pemeriksaan modul Dagang</div>
        </div>

        <!-- 6. Status Persetujuan RAB -->
        <div class="bg-white p-3.5 rounded-lg border border-slate-200 shadow-xs">
            <div class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Status RAB</div>
            <div class="mt-1">
                @if(($tender->rab_status ?? 'draft') === 'draft')
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200">Draft</span>
                @elseif($tender->rab_status === 'diajukan')
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200 animate-pulse">Diajukan</span>
                @elseif($tender->rab_status === 'disetujui')
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">Disetujui</span>
                @elseif($tender->rab_status === 'perlu_revisi')
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-bold bg-amber-100 text-amber-900 border border-amber-300">Perlu Revisi</span>
                @else
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-bold bg-rose-50 text-rose-800 border border-rose-200">Ditolak</span>
                @endif
            </div>
            <div class="text-[10px] text-slate-400 mt-1">Persetujuan Manajemen</div>
        </div>
    </div>

    <!-- Rekomendasi Kelayakan Sistem & Integrasi Proyek (Requirement E) -->
    @php
        $rec = $tender->execution_recommendation;
        $deficitItems = $tender->rabItems->filter(fn($i) => $i->has_deficit);
    @endphp
    <div class="rounded-lg p-4 border {{ $rec['type'] === 'internal' ? 'bg-emerald-50/50 border-emerald-200 text-emerald-950' : 'bg-amber-50/60 border-amber-200 text-amber-950' }}">
        <div class="flex items-start gap-3">
            <div class="w-8 h-8 rounded-md flex items-center justify-center shrink-0 {{ $rec['type'] === 'internal' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                <i class="fa-solid {{ $rec['type'] === 'internal' ? 'fa-circle-check' : 'fa-triangle-exclamation' }} text-sm"></i>
            </div>
            <div class="space-y-1.5 flex-1 text-xs">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <span class="font-bold text-sm tracking-tight">{{ $rec['title'] }}</span>
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ $rec['badge_class'] }}">
                        Analisis Otomatis Sistem
                    </span>
                </div>
                <p class="leading-relaxed">{{ $rec['description'] }}</p>

                <!-- Rincian Defisit Jika Ada -->
                @if($deficitItems->isNotEmpty())
                    <div class="mt-2 bg-white/80 p-3 rounded-md border border-amber-200 text-xs space-y-1.5">
                        <span class="font-semibold text-amber-900 block">Daftar Material dengan Kekurangan Stok di Modul Dagang:</span>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                            @foreach($deficitItems as $def)
                                <div class="flex items-center justify-between bg-amber-50 p-2 rounded border border-amber-200 text-[11px]">
                                    <div class="truncate">
                                        <span class="font-medium text-slate-800">{{ $def->item_name }}</span>
                                        <span class="text-slate-500 font-mono">({{ $def->product->sku ?? '-' }})</span>
                                    </div>
                                    <div class="text-right shrink-0 font-numeric">
                                        <span class="text-slate-500">Tersedia: {{ (float) ($def->product->stock ?? 0) }} {{ $def->unit }}</span>
                                        <span class="font-bold text-rose-700 ml-1.5">Kurang: {{ (float) $def->deficit }} {{ $def->unit }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Action & Approval Bar Manajemen -->
    <div class="bg-white rounded-lg p-3.5 border border-slate-200 shadow-xs flex flex-col md:flex-row items-start md:items-center justify-between gap-3">
        <div class="flex items-center gap-2 text-xs">
            <span class="text-slate-500 font-medium">Status Pengajuan RAB:</span>
            <span class="font-semibold text-slate-800 uppercase tracking-wide">{{ $tender->rab_status ?: 'draft' }}</span>
            @if($tender->rab_notes)
                <span class="text-slate-400">|</span>
                <span class="text-slate-600 italic">"{{ $tender->rab_notes }}"</span>
            @endif
        </div>

        <div class="flex flex-wrap items-center gap-2 w-full md:w-auto">
            <!-- Admin: Ajukan RAB ke Manajemen -->
            @if(auth()->user()->role?->name === 'admin' && in_array($tender->rab_status, ['draft', 'perlu_revisi', null], true))
                <form action="{{ route('tender.rab.submit', $tender) }}" method="POST" onsubmit="return confirm('Ajukan estimasi RAB ini kepada Manajemen untuk persetujuan?');">
                    @csrf
                    <button type="submit" class="px-3.5 py-1.5 bg-amber-500 hover:bg-amber-600 text-white font-semibold text-xs rounded-md shadow-xs transition flex items-center gap-1.5">
                        <i class="fa-solid fa-paper-plane text-[10px]"></i> Ajukan RAB ke Manajemen
                    </button>
                </form>
            @endif

            <!-- Manajemen: Setujui / Minta Revisi / Tolak RAB -->
            @if(auth()->user()->role?->name === 'manager' && in_array($tender->rab_status, ['diajukan', 'pending'], true))
                <form action="{{ route('tender.rab.approve', $tender) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-md shadow-xs transition flex items-center gap-1">
                        <i class="fa-solid fa-check text-[10px]"></i> Setujui RAB
                    </button>
                </form>
                <button type="button" @click="reviseModal = true" class="px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs rounded-md shadow-xs transition flex items-center gap-1">
                    <i class="fa-solid fa-rotate-left text-[10px]"></i> Minta Revisi
                </button>
                <form action="{{ route('tender.rab.reject', $tender) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menolak RAB ini?');">
                    @csrf
                    <button type="submit" class="px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs rounded-md shadow-xs transition flex items-center gap-1">
                        <i class="fa-solid fa-xmark text-[10px]"></i> Tolak RAB
                    </button>
                </form>
            @endif

            <!-- Tombol Tambah Item Komponen Biaya -->
            @if(auth()->user()->role?->name !== 'owner')
                <button @click="addItemModal = true" class="px-3.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs rounded-md shadow-xs transition flex items-center gap-1.5">
                    <i class="fa-solid fa-plus text-[10px]"></i> Tambah Komponen Biaya
                </button>
            @endif
        </div>
    </div>

    <!-- Filter Tab Kategori RAB -->
    <div class="flex items-center gap-1 border-b border-slate-200 text-xs font-medium">
        <button type="button" @click="activeTab = 'all'" class="px-3 py-2 border-b-2 transition" :class="activeTab === 'all' ? 'border-blue-600 text-blue-600 font-bold' : 'border-transparent text-slate-500 hover:text-slate-800'">
            Semua Komponen ({{ $tender->rabItems->count() }})
        </button>
        <button type="button" @click="activeTab = 'barang'" class="px-3 py-2 border-b-2 transition" :class="activeTab === 'barang' ? 'border-blue-600 text-blue-600 font-bold' : 'border-transparent text-slate-500 hover:text-slate-800'">
            1. Barang & Material ({{ $tender->rabItems->where('category', 'barang')->count() }})
        </button>
        <button type="button" @click="activeTab = 'tenaga_kerja'" class="px-3 py-2 border-b-2 transition" :class="activeTab === 'tenaga_kerja' ? 'border-blue-600 text-blue-600 font-bold' : 'border-transparent text-slate-500 hover:text-slate-800'">
            2. Tenaga Kerja / SDM ({{ $tender->rabItems->where('category', 'tenaga_kerja')->count() }})
        </button>
        <button type="button" @click="activeTab = 'transportasi'" class="px-3 py-2 border-b-2 transition" :class="activeTab === 'transportasi' ? 'border-blue-600 text-blue-600 font-bold' : 'border-transparent text-slate-500 hover:text-slate-800'">
            3. Transport & Logistik ({{ $tender->rabItems->where('category', 'transportasi')->count() }})
        </button>
        <button type="button" @click="activeTab = 'operasional'" class="px-3 py-2 border-b-2 transition" :class="activeTab === 'operasional' ? 'border-blue-600 text-blue-600 font-bold' : 'border-transparent text-slate-500 hover:text-slate-800'">
            4. Operasional ({{ $tender->rabItems->where('category', 'operasional')->count() }})
        </button>
        <button type="button" @click="activeTab = 'vendor'" class="px-3 py-2 border-b-2 transition" :class="activeTab === 'vendor' ? 'border-blue-600 text-blue-600 font-bold' : 'border-transparent text-slate-500 hover:text-slate-800'">
            5. Biaya Vendor ({{ $tender->rabItems->where('category', 'vendor')->count() }})
        </button>
    </div>

    <!-- Data Table Rincian Item RAB -->
    <div class="bg-white rounded-lg border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-600 font-semibold border-b border-slate-200 uppercase tracking-wider">
                        <th class="p-3">Kategori</th>
                        <th class="p-3">Deskripsi / Nama Item</th>
                        <th class="p-3">Integrasi Gudang / SDM</th>
                        <th class="p-3 text-right">Vol & Satuan</th>
                        <th class="p-3 text-right">HPP Modal (Satuan)</th>
                        <th class="p-3 text-right">Subtotal HPP</th>
                        <th class="p-3 text-right">Penawaran (Satuan)</th>
                        <th class="p-3 text-right">Subtotal Penawaran</th>
                        <th class="p-3 text-right">Estimasi Margin</th>
                        <th class="p-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($tender->rabItems as $item)
                        @php
                            $isDeficit = $item->has_deficit;
                            $itemMargin = $item->subtotal_price - $item->subtotal_cost;
                        @endphp
                        <tr class="hover:bg-slate-50/70 transition" x-show="activeTab === 'all' || activeTab === '{{ $item->category }}'">
                            <!-- Kategori Badge -->
                            <td class="p-3 whitespace-nowrap">
                                @if($item->category === 'barang')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-semibold bg-purple-50 text-purple-700 border border-purple-200">
                                        <i class="fa-solid fa-boxes-stacked text-[9px]"></i> Barang / Material
                                    </span>
                                @elseif($item->category === 'tenaga_kerja')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <i class="fa-solid fa-user-gear text-[9px]"></i> Tenaga Kerja
                                    </span>
                                @elseif($item->category === 'transportasi')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-semibold bg-sky-50 text-sky-700 border border-sky-200">
                                        <i class="fa-solid fa-truck text-[9px]"></i> Transportasi
                                    </span>
                                @elseif($item->category === 'operasional')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                        <i class="fa-solid fa-screwdriver-wrench text-[9px]"></i> Operasional
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                        <i class="fa-solid fa-handshake text-[9px]"></i> Vendor
                                    </span>
                                @endif
                            </td>

                            <!-- Deskripsi / Nama Item -->
                            <td class="p-3">
                                <div class="font-semibold text-slate-900 leading-snug">{{ $item->item_name }}</div>
                                @if($item->notes)
                                    <div class="text-[10px] text-slate-500 mt-0.5 italic">{{ $item->notes }}</div>
                                @endif
                            </td>

                            <!-- Integrasi Gudang Dagang / SDM Jasa -->
                            <td class="p-3 whitespace-nowrap">
                                @if($item->category === 'barang')
                                    @if($item->product)
                                        <div class="text-[11px]">
                                            <span class="text-slate-500">Stok Gudang:</span>
                                            <strong class="text-slate-800 font-numeric">{{ (float) $item->product->stock }} {{ $item->unit }}</strong>
                                        </div>
                                        @if($isDeficit)
                                            <span class="inline-flex items-center gap-1 px-1.5 py-0.2 rounded text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200 mt-0.5">
                                                <i class="fa-solid fa-circle-exclamation text-[9px]"></i> Defisit: {{ (float) $item->deficit }} {{ $item->unit }}
                                            </span>
                                        @else
                                            <span class="text-[10px] text-emerald-600 font-medium flex items-center gap-1 mt-0.5">
                                                <i class="fa-solid fa-check text-[9px]"></i> Cukup di gudang
                                            </span>
                                        @endif
                                    @else
                                        <span class="text-slate-400 text-[11px]">- Non master produk -</span>
                                    @endif
                                @elseif($item->category === 'tenaga_kerja')
                                    <span class="text-slate-600 text-[11px]">Modul Jasa (Teknisi)</span>
                                @else
                                    <span class="text-slate-400 text-[11px]">-</span>
                                @endif
                            </td>

                            <!-- Vol & Satuan -->
                            <td class="p-3 text-right font-numeric font-medium text-slate-800">
                                {{ (float) $item->quantity }} {{ $item->unit }}
                            </td>

                            <!-- HPP Modal Satuan -->
                            <td class="p-3 text-right font-numeric text-slate-600">
                                Rp {{ number_format($item->unit_cost, 0, ',', '.') }}
                            </td>

                            <!-- Subtotal HPP Modal -->
                            <td class="p-3 text-right font-numeric font-bold text-slate-900">
                                Rp {{ number_format($item->subtotal_cost, 0, ',', '.') }}
                            </td>

                            <!-- Penawaran Satuan -->
                            <td class="p-3 text-right font-numeric text-slate-600">
                                Rp {{ number_format($item->unit_price, 0, ',', '.') }}
                            </td>

                            <!-- Subtotal Penawaran -->
                            <td class="p-3 text-right font-numeric font-bold text-blue-700">
                                Rp {{ number_format($item->subtotal_price, 0, ',', '.') }}
                            </td>

                            <!-- Estimasi Margin -->
                            <td class="p-3 text-right font-numeric font-semibold {{ $itemMargin >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                                Rp {{ number_format($itemMargin, 0, ',', '.') }}
                            </td>

                            <!-- Aksi -->
                            <td class="p-3 text-center whitespace-nowrap">
                                @if(auth()->user()->role?->name !== 'owner')
                                    <div class="inline-flex items-center gap-1">
                                        <button type="button" @click="openEditItem({{ $item }})" class="w-7 h-7 flex items-center justify-center bg-slate-100 hover:bg-slate-200 text-slate-700 rounded transition" title="Edit Item">
                                            <i class="fa-solid fa-pen text-[10px]"></i>
                                        </button>
                                        <form action="{{ route('tender.rab.items.destroy', [$tender, $item]) }}" method="POST" onsubmit="return confirm('Hapus item biaya ini dari RAB?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-7 h-7 flex items-center justify-center bg-rose-50 hover:bg-rose-100 text-rose-700 rounded transition" title="Hapus Item">
                                                <i class="fa-solid fa-trash text-[10px]"></i>
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <span class="text-slate-400 text-xs">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="p-8 text-center text-slate-400 text-sm">
                                <div class="flex flex-col items-center justify-center space-y-2">
                                    <i class="fa-solid fa-calculator text-2xl text-slate-300"></i>
                                    <span>Belum ada komponen biaya RAB. Klik tombol "Tambah Komponen Biaya" untuk memulai.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <!-- Footer Total -->
                @if($tender->rabItems->isNotEmpty())
                    <tfoot class="bg-slate-50 font-semibold border-t-2 border-slate-200 text-slate-900 font-numeric">
                        <tr>
                            <td colspan="5" class="p-3 text-right uppercase text-[11px] tracking-wider text-slate-500 font-sans">Total Akumulasi RAB:</td>
                            <td class="p-3 text-right text-sm font-bold text-slate-900">Rp {{ number_format($tender->total_rab_cost, 0, ',', '.') }}</td>
                            <td></td>
                            <td class="p-3 text-right text-sm font-bold text-blue-700">Rp {{ number_format($tender->total_rab_price, 0, ',', '.') }}</td>
                            <td class="p-3 text-right text-sm font-bold {{ $tender->estimated_profit >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                                Rp {{ number_format($tender->estimated_profit, 0, ',', '.') }}
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>

    <!-- Modal Tambah Komponen Biaya RAB -->
    <div x-show="addItemModal" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4" x-cloak>
        <div class="bg-white rounded-lg border border-slate-200 max-w-lg w-full p-5 shadow-xl space-y-4 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center border-b border-slate-200 pb-3">
                <div>
                    <h3 class="font-bold text-sm text-slate-900">Tambah Komponen Biaya RAB</h3>
                    <p class="text-xs text-slate-500">Pilih kategori dan input estimasi modal serta penawaran</p>
                </div>
                <button @click="addItemModal = false" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-sm"></i></button>
            </div>

            <form action="{{ route('tender.rab.items.store', $tender) }}" method="POST" class="space-y-3.5">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Kategori Biaya <span class="text-rose-500">*</span></label>
                    <select name="category" x-model="formItem.category" required class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs font-medium outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                        <option value="barang">1. Barang / Material (Modul Dagang / Gudang)</option>
                        <option value="tenaga_kerja">2. Tenaga Kerja / SDM Teknisi (Modul Jasa)</option>
                        <option value="transportasi">3. Transportasi & Logistik Lapangan</option>
                        <option value="operasional">4. Operasional & Sewa Peralatan</option>
                        <option value="vendor">5. Biaya Vendor / Mitra Subkontraktor</option>
                    </select>
                </div>

                <!-- Jika Kategori Barang, dropdown master produk Modul Dagang -->
                <div x-show="formItem.category === 'barang'" class="space-y-1 bg-purple-50/50 p-3 rounded-md border border-purple-200">
                    <label class="block text-xs font-semibold text-purple-900 uppercase tracking-wider">Pilih Barang dari Modul Dagang (Gudang)</label>
                    <select name="product_id" x-model="formItem.product_id" @change="onProductChange($event.target.value)" class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                        <option value="">-- Pilih Barang Master / Manual --</option>
                        @foreach($products as $prod)
                            <option value="{{ $prod->id }}">
                                {{ $prod->sku }} - {{ $prod->name }} (Stok: {{ (float) $prod->stock }} {{ $prod->unit }} | HPP: Rp {{ number_format($prod->purchase_price, 0, ',', '.') }})
                            </option>
                        @endforeach
                    </select>
                    <p class="text-[10px] text-purple-700">Memilih produk akan otomatis memeriksa ketersediaan stok gudang dan mengisi harga modal HPP.</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Nama / Uraian Komponen Biaya <span class="text-rose-500">*</span></label>
                    <input type="text" name="item_name" x-model="formItem.item_name" required placeholder="Contoh: Router Wireless AC1200 / Teknisi Instalasi Kabel..." class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Volume / Kuantitas <span class="text-rose-500">*</span></label>
                        <input type="number" name="quantity" x-model="formItem.quantity" step="0.01" min="0.01" required class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500 font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Satuan</label>
                        <input type="text" name="unit" x-model="formItem.unit" required placeholder="Unit, Roll, Orang/Hari..." class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">HPP Biaya Modal Satuan (Rp) <span class="text-rose-500">*</span></label>
                        <input type="number" name="unit_cost" x-model="formItem.unit_cost" min="0" required class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500 font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Harga Penawaran Satuan (Rp) <span class="text-rose-500">*</span></label>
                        <input type="number" name="unit_price" x-model="formItem.unit_price" min="0" required class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500 font-mono">
                    </div>
                </div>

                <!-- Preview Kalkulasi Realtime -->
                <div class="bg-slate-50 p-2.5 rounded-md border border-slate-200 grid grid-cols-3 gap-2 text-xs font-numeric">
                    <div>
                        <span class="text-[10px] text-slate-500 block">Subtotal Modal:</span>
                        <span class="font-bold text-slate-900" x-text="'Rp ' + ((formItem.quantity * formItem.unit_cost) || 0).toLocaleString('id-ID')"></span>
                    </div>
                    <div>
                        <span class="text-[10px] text-slate-500 block">Subtotal Penawaran:</span>
                        <span class="font-bold text-blue-700" x-text="'Rp ' + ((formItem.quantity * formItem.unit_price) || 0).toLocaleString('id-ID')"></span>
                    </div>
                    <div>
                        <span class="text-[10px] text-slate-500 block">Estimasi Margin:</span>
                        <span class="font-bold text-emerald-700" x-text="'Rp ' + (((formItem.quantity * formItem.unit_price) - (formItem.quantity * formItem.unit_cost)) || 0).toLocaleString('id-ID')"></span>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Catatan Tambahan</label>
                    <textarea name="notes" x-model="formItem.notes" rows="2" placeholder="Spesifikasi teknis, merk, atau ketentuan instalasi..." class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-200">
                    <button type="button" @click="addItemModal = false" class="px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium text-xs rounded-md transition">Batal</button>
                    <button type="submit" class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs rounded-md transition shadow-xs">Simpan ke RAB</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Komponen Biaya RAB -->
    <div x-show="editItemModal" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4" x-cloak>
        <div class="bg-white rounded-lg border border-slate-200 max-w-lg w-full p-5 shadow-xl space-y-4 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center border-b border-slate-200 pb-3">
                <div>
                    <h3 class="font-bold text-sm text-slate-900">Edit Komponen Biaya RAB</h3>
                    <p class="text-xs text-slate-500" x-text="selectedItem ? selectedItem.item_name : ''"></p>
                </div>
                <button @click="editItemModal = false" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-sm"></i></button>
            </div>

            <form x-bind:action="selectedItem ? '/tender/' + {{ $tender->id }} + '/rab/items/' + selectedItem.id : '#'" method="POST" class="space-y-3.5">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Kategori Biaya</label>
                    <select name="category" x-model="formItem.category" required class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs font-medium outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                        <option value="barang">1. Barang / Material</option>
                        <option value="tenaga_kerja">2. Tenaga Kerja / SDM Teknisi</option>
                        <option value="transportasi">3. Transportasi & Logistik</option>
                        <option value="operasional">4. Operasional & Peralatan</option>
                        <option value="vendor">5. Biaya Vendor / Mitra</option>
                    </select>
                </div>

                <div x-show="formItem.category === 'barang'" class="space-y-1">
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Produk Terhubung (Modul Dagang)</label>
                    <select name="product_id" x-model="formItem.product_id" @change="onProductChange($event.target.value)" class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                        <option value="">-- Pilih Barang Master / Manual --</option>
                        @foreach($products as $prod)
                            <option value="{{ $prod->id }}">
                                {{ $prod->sku }} - {{ $prod->name }} (Stok: {{ (float) $prod->stock }} {{ $prod->unit }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Nama / Uraian Item</label>
                    <input type="text" name="item_name" x-model="formItem.item_name" required class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Volume / Kuantitas</label>
                        <input type="number" name="quantity" x-model="formItem.quantity" step="0.01" min="0.01" required class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500 font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Satuan</label>
                        <input type="text" name="unit" x-model="formItem.unit" required class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">HPP Biaya Modal Satuan (Rp)</label>
                        <input type="number" name="unit_cost" x-model="formItem.unit_cost" min="0" required class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500 font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Harga Penawaran Satuan (Rp)</label>
                        <input type="number" name="unit_price" x-model="formItem.unit_price" min="0" required class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500 font-mono">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Catatan</label>
                    <textarea name="notes" x-model="formItem.notes" rows="2" class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-200">
                    <button type="button" @click="editItemModal = false" class="px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium text-xs rounded-md transition">Batal</button>
                    <button type="submit" class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs rounded-md transition shadow-xs">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Minta Revisi RAB (Manajemen) -->
    <div x-show="reviseModal" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4" x-cloak>
        <div class="bg-white rounded-lg border border-slate-200 max-w-md w-full p-5 shadow-xl space-y-4">
            <div class="flex justify-between items-center border-b border-slate-200 pb-3">
                <h3 class="font-bold text-sm text-slate-900">Minta Revisi Estimasi RAB</h3>
                <button @click="reviseModal = false" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-sm"></i></button>
            </div>

            <form action="{{ route('tender.rab.revise', $tender) }}" method="POST" class="space-y-3.5">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Catatan Revisi Manajemen <span class="text-rose-500">*</span></label>
                    <textarea name="rab_notes" required rows="4" placeholder="Uraikan item yang perlu disesuaikan (misal: margin terlalu rendah, biaya transport terlalu tinggi, dsb)..." class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-200">
                    <button type="button" @click="reviseModal = false" class="px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium text-xs rounded-md transition">Batal</button>
                    <button type="submit" class="px-4 py-1.5 bg-amber-600 hover:bg-amber-700 text-white font-semibold text-xs rounded-md transition shadow-xs flex items-center gap-1">
                        <i class="fa-solid fa-rotate-left text-[10px]"></i> Kirim Revisi RAB
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
