@extends('layouts.app')

@section('title', $distributionMode ? 'Distribusi Barang' : 'Penjualan Barang')
@section('header-title', $distributionMode ? 'Distribusi Barang ke Proyek & Pelanggan' : 'Manajemen Penjualan Barang & Verifikasi')

@section('content')
<div class="space-y-5" x-data="{
    createModal: false,
    approveModal: false,
    rejectModal: false,
    deleteModal: false,
    selectedSale: null,
    approveActionUrl: '',
    rejectActionUrl: '',
    deleteActionUrl: '',
    deletionReason: '',
    openApproveModal(id, number, name, amount) {
        if (typeof id === 'object' && id !== null) {
            this.selectedSale = id;
            this.approveActionUrl = '/sales/' + id.id + '/approve';
        } else {
            this.selectedSale = { id: id, sale_number: number, nama_barang: name, total_amount: amount };
            this.approveActionUrl = '/sales/' + id + '/approve';
        }
        this.approveModal = true;
    },
    openRejectModal(id, number, name, amount) {
        if (typeof id === 'object' && id !== null) {
            this.selectedSale = id;
            this.rejectActionUrl = '/sales/' + (id.id || id) + '/reject';
        } else {
            this.selectedSale = { id: id, sale_number: number, nama_barang: name, total_amount: amount };
            this.rejectActionUrl = '/sales/' + id + '/reject';
        }
        this.rejectModal = true;
    },
    openDeleteModal(id, number, name, amount) {
        if (typeof id === 'object' && id !== null) {
            this.selectedSale = id;
            this.deleteActionUrl = '/sales/' + id.id;
        } else {
            this.selectedSale = { id: id, sale_number: number, nama_barang: name, total_amount: amount };
            this.deleteActionUrl = '/sales/' + id;
        }
        this.deletionReason = '';
        this.deleteModal = true;
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
                <span class="text-xs font-semibold text-slate-900 block leading-tight">Pemantauan Eksekutif: Penjualan Barang</span>
                <span class="text-xs text-slate-500">Sebagai Owner, Anda memiliki akses pemantauan penjualan barang dan laporan transaksi tanpa mengubah operasional.</span>
            </div>
            <a href="{{ route('laporan.index', ['domain' => 'barang']) }}" class="px-3 py-1.5 bg-purple-600 hover:bg-purple-700 text-white rounded-md text-xs font-medium transition inline-flex items-center gap-1.5 self-start sm:self-auto shadow-xs">
                <i class="fa-solid fa-chart-pie"></i> Buka Laporan Dagang
            </a>
        </div>
    @elseif(auth()->user()->isManager() && ($pendingCount ?? 0) > 0)
        <div class="border-l-4 border-amber-500 bg-white border border-slate-200 rounded-lg p-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-xs">
            <div>
                <span class="text-xs font-semibold text-slate-900 block leading-tight">Verifikasi Persetujuan Penjualan</span>
                <span class="text-xs text-slate-500">Terdapat <span class="font-semibold text-slate-800">{{ $pendingCount }} transaksi penjualan barang</span> yang menunggu persetujuan (Approval) Manager.</span>
            </div>
            <a href="{{ route('sales.index', ['approval_status' => 'pending']) }}" class="px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white rounded-md text-xs font-medium transition inline-flex items-center gap-1.5 self-start sm:self-auto">
                Tampilkan Hanya Pending
            </a>
        </div>
    @endif

    @if($distributionMode)
        <div class="rounded-lg border border-blue-100 bg-blue-50/70 px-4 py-3 text-xs text-blue-800 flex items-start gap-2">
            <i class="fa-solid fa-truck-fast mt-0.5 text-blue-600"></i>
            <span>Gunakan transaksi ini untuk mencatat daftar barang, tujuan pengiriman, catatan pengiriman, dan status penerimaan. Setiap barang keluar tetap tercatat sebagai mutasi stok.</span>
        </div>
    @endif

    <!-- Toolbar & Filter Bar -->
    <div class="bg-white rounded-lg p-4 border border-slate-200 shadow-xs flex flex-col md:flex-row justify-between items-center gap-3">
        <form method="GET" action="{{ route('sales.index') }}" class="flex flex-wrap items-center gap-2.5 w-full md:w-auto">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari transaksi / pelanggan / barang..." class="px-3 py-1.5 rounded-md border border-slate-200 text-xs focus:ring-2 focus:ring-blue-500 outline-none w-64">
            
            <select name="approval_status" onchange="this.form.submit()" class="px-2.5 py-1.5 rounded-md border border-slate-200 text-xs font-medium text-slate-700 focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                <option value="">Semua Status Persetujuan</option>
                <option value="pending" {{ request('approval_status') == 'pending' ? 'selected' : '' }}>Menunggu Persetujuan</option>
                <option value="approved" {{ request('approval_status') == 'approved' ? 'selected' : '' }}>Disetujui (Approved)</option>
                <option value="rejected" {{ request('approval_status') == 'rejected' ? 'selected' : '' }}>Ditolak (Rejected)</option>
            </select>

            <button type="submit" class="px-3 py-1.5 bg-purple-50 hover:bg-purple-100 text-purple-700 border border-purple-200 font-semibold text-xs rounded-md transition">
                <i class="fa-solid fa-filter mr-1"></i> Filter
            </button>
            @if(request()->hasAny(['search', 'approval_status']))
                <a href="{{ route('sales.index') }}" class="px-2.5 py-1.5 text-slate-500 hover:text-slate-800 text-xs font-medium rounded-md">Reset</a>
            @endif
        </form>

        @if(!auth()->user()->isOwner())
            <button @click="createModal = true" class="px-3.5 py-1.5 bg-purple-600 hover:bg-purple-700 text-white font-medium text-xs rounded-md shadow-xs transition flex items-center gap-1.5">
                <i class="fa-solid fa-plus text-[11px]"></i> Transaksi Penjualan Barang
            </button>
        @endif
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-lg border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-200 uppercase tracking-wider text-[11px]">
                        <th class="p-3.5 whitespace-nowrap">No. Transaksi</th>
                        <th class="p-3.5">Pelanggan</th>
                        <th class="p-3.5 whitespace-nowrap">Tanggal</th>
                        <th class="p-3.5">Rincian Barang</th>
                        <th class="p-3.5 whitespace-nowrap">Total Penjualan</th>
                        <th class="p-3.5 whitespace-nowrap">Pengiriman</th>
                        <th class="p-3.5 text-center whitespace-nowrap">Status Persetujuan</th>
                        <th class="p-3.5 text-center whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($sales as $s)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="p-3.5 font-mono font-medium text-slate-900 whitespace-nowrap">
                                {{ $s->sale_number }}
                            </td>
                            <td class="p-3.5">
                                <div class="font-semibold text-slate-800">{{ $s->customer_name }}</div>
                                @if($s->customer_phone)
                                    <div class="text-[11px] text-slate-500">{{ $s->customer_phone }}</div>
                                @endif
                                @if($s->tender)
                                    <div class="mt-1">
                                        <a href="{{ route('tender.show', $s->tender_id) }}" class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-semibold bg-blue-50 text-blue-700 border border-blue-200 hover:bg-blue-100" title="Material Proyek Tender: {{ $s->tender->name }}">
                                            <i class="fa-solid fa-folder text-[9px]"></i> Proyek: {{ $s->tender->tender_number }}
                                        </a>
                                    </div>
                                @endif
                            </td>
                            <td class="p-3.5 text-slate-600 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($s->sale_date)->format('d M Y') }}
                            </td>
                            <td class="p-3.5 text-slate-700 max-w-xs">
                                @if($s->nama_barang)
                                    <div class="font-semibold text-slate-800">{{ $s->nama_barang }}</div>
                                    <div class="text-slate-500 text-[11px]">
                                        {{ number_format($s->kuantitas ?? 1, 0, ',', '.') }} unit @ Rp {{ number_format($s->harga_satuan ?? 0, 0, ',', '.') }}
                                    </div>
                                @elseif($s->items && $s->items->count() > 0)
                                    <ul class="space-y-0.5">
                                        @foreach($s->items as $item)
                                            <li>• {{ $item->product->name ?? 'Produk' }} ({{ number_format($item->quantity, 0) }} {{ $item->product->unit ?? 'unit' }})</li>
                                        @endforeach
                                    </ul>
                                @else
                                    <span class="text-slate-400 italic">Barang Dagang</span>
                                @endif
                            </td>
                            <td class="p-3.5 font-mono font-semibold text-slate-900 whitespace-nowrap">
                                Rp {{ number_format($s->total_amount, 0, ',', '.') }}
                            </td>
                            <td class="p-3.5 text-slate-600 max-w-xs">
                                @if($s->catatan_pengiriman)
                                    <div class="truncate" title="{{ $s->catatan_pengiriman }}">
                                        {{ $s->catatan_pengiriman }}
                                    </div>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="p-3.5 text-center whitespace-nowrap">
                                @if($s->approval_status === 'approved')
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Disetujui
                                    </span>
                                    @if($s->approver)
                                        <div class="text-[10px] text-slate-400 mt-0.5">oleh {{ $s->approver->name }}</div>
                                    @endif
                                @elseif($s->approval_status === 'rejected')
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-xs font-medium bg-rose-50 text-rose-700 border border-rose-200" title="{{ $s->approval_notes }}">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Ditolak
                                    </span>
                                    @if($s->approval_notes)
                                        <div class="text-[10px] text-rose-500 mt-0.5 max-w-[120px] truncate mx-auto" title="{{ $s->approval_notes }}">{{ $s->approval_notes }}</div>
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
                                    @if(auth()->user()->isManager() && $s->approval_status === 'pending')
                                        @php
                                            $sId = $s->id;
                                            $sNumber = $s->sale_number;
                                            $sItem = addslashes($s->nama_barang ?: ($s->items->first()?->product?->name ?? 'Barang Dagang'));
                                            $sTotal = 'Rp ' . number_format($s->total_amount, 0, ',', '.');
                                        @endphp
                                        <!-- Quick Approve Button -->
                                        <button type="button" @click="openApproveModal({{ $sId }}, '{{ $sNumber }}', '{{ $sItem }}', '{{ $sTotal }}')" class="px-2 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded text-xs font-medium transition inline-flex items-center gap-1 shadow-xs" title="Setujui">
                                            <i class="fa-solid fa-check text-[10px]"></i> Setujui
                                        </button>

                                        <!-- Quick Reject Button -->
                                        <button type="button" @click="openRejectModal({{ $sId }}, '{{ $sNumber }}', '{{ $sItem }}', '{{ $sTotal }}')" class="px-2 py-1 bg-rose-600 hover:bg-rose-700 text-white rounded text-xs font-medium transition inline-flex items-center gap-1 shadow-xs" title="Tolak">
                                            <i class="fa-solid fa-xmark text-[10px]"></i> Tolak
                                        </button>
                                    @endif

                                    @if($s->invoices && $s->invoices->count() > 0)
                                        @php
                                            $firstInv = $s->invoices->first();
                                            $isPaid = $firstInv->status === 'Paid';
                                        @endphp
                                        <a href="{{ route('invoices.show', $firstInv->id) }}" class="px-2.5 py-1 rounded text-xs font-medium inline-flex items-center gap-1 transition {{ $isPaid ? 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }}" title="{{ $isPaid ? 'Invoice Lunas' : 'Lihat Invoice & Catat Pembayaran' }}">
                                            <i class="fa-solid {{ $isPaid ? 'fa-circle-check text-emerald-600' : 'fa-file-invoice text-slate-500' }} text-[10px]"></i>
                                            <span>{{ $isPaid ? 'Lunas' : 'Invoice' }}</span>
                                        </a>
                                    @endif

                                    @if(auth()->user()->isManager() || auth()->user()->isOwner())
                                        @php
                                            $delId = $s->id;
                                            $delNumber = $s->sale_number;
                                            $delItem = addslashes($s->nama_barang ?: ($s->items->first()?->product?->name ?? 'Barang Dagang'));
                                            $delTotal = 'Rp ' . number_format($s->total_amount, 0, ',', '.');
                                        @endphp
                                        <button type="button" @click="openDeleteModal({{ $delId }}, '{{ $delNumber }}', '{{ $delItem }}', '{{ $delTotal }}')" class="px-2 py-1 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded text-xs font-medium transition inline-flex items-center gap-1 border border-rose-200" title="Hapus Transaksi (Manager / Owner)">
                                            <i class="fa-solid fa-trash-can text-[10px]"></i> Hapus
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-slate-400">
                                <p class="text-xs">Belum ada transaksi penjualan barang yang sesuai filter.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3.5 border-t border-slate-100">
            {{ $sales->links() }}
        </div>
    </div>

    <!-- Modal Create Sale (PRD Standard) -->
    <div x-show="createModal" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4" x-cloak>
        <div class="bg-white rounded-lg border border-slate-200 max-w-2xl w-full p-5 shadow-xl space-y-4 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center border-b border-slate-200 pb-3">
                <div>
                    <h3 class="font-bold text-sm text-slate-900">Input Transaksi Penjualan Barang</h3>
                    <p class="text-xs text-slate-500">Pencatatan penjualan barang & verifikasi manager</p>
                </div>
                <button @click="createModal = false" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-sm"></i></button>
            </div>

            <form action="{{ route('sales.store') }}" method="POST" class="space-y-3.5"
                  x-data="{
                      customerName: '',
                      customerPhone: '',
                      namaBarang: '',
                      kuantitas: 1,
                      hargaSatuan: 0,
                      selectedProductId: '',
                      selectedTenderId: '',
                      tendersMap: {
                          @foreach($tenders as $td)
                              '{{ $td->id }}': {
                                  client: '{{ addslashes($td->client?->name ?? '') }}',
                                  phone: '{{ addslashes($td->client?->phone ?? '') }}'
                              },
                          @endforeach
                      },
                      onTenderChange() {
                          if (this.selectedTenderId && this.tendersMap[this.selectedTenderId]) {
                              const t = this.tendersMap[this.selectedTenderId];
                              if (t.client) {
                                  this.customerName = t.client;
                              }
                              if (t.phone) {
                                  this.customerPhone = t.phone;
                              }
                          }
                      },
                      products: {
                          @foreach($products as $p)
                              '{{ $p->id }}': {
                                  name: '{{ addslashes($p->name) }}',
                                  price: {{ (float) $p->selling_price }},
                                  stock: {{ (float) $p->stock }}
                              },
                          @endforeach
                      },
                      onProductChange() {
                          if (this.selectedProductId && this.products[this.selectedProductId]) {
                              const p = this.products[this.selectedProductId];
                              this.namaBarang = p.name;
                              this.hargaSatuan = p.price;
                          }
                      },
                      calcTotal() {
                          return (parseFloat(this.kuantitas || 0) * parseFloat(this.hargaSatuan || 0)).toLocaleString('id-ID');
                      }
                  }">
                @csrf

                <!-- Tujuan Distribusi: Penjualan Mandiri vs Proyek Tender -->
                <div class="p-3 bg-blue-50/70 border border-blue-200 rounded-lg space-y-1.5">
                    <label class="block text-xs font-semibold text-blue-900 uppercase tracking-wider">
                        Tujuan Distribusi Barang <span class="text-xs text-blue-700 font-normal lowercase">(hubungkan dengan Proyek Tender jika ada)</span>
                    </label>
                    <select name="tender_id" x-model="selectedTenderId" @change="onTenderChange()" class="w-full px-3 py-1.5 border border-blue-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500 bg-white text-slate-800">
                        <option value="">Penjualan Langsung / Mandiri (Bukan untuk Tender)</option>
                        @foreach($tenders as $td)
                            <option value="{{ $td->id }}">
                                Proyek Tender: {{ $td->tender_number }} - {{ $td->name }} (Klien: {{ $td->client?->name ?? '-' }})
                            </option>
                        @endforeach
                    </select>
                    <p class="text-[11px] text-blue-700/80">
                        Pilih proyek tender jika barang ini didistribusikan sebagai material proyek. Nama pelanggan akan otomatis terisi dengan instansi klien tender.
                    </p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Tanggal Transaksi <span class="text-rose-500">*</span></label>
                    <input type="date" name="sale_date" required value="{{ date('Y-m-d') }}" class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Nama Pelanggan / Perusahaan <span class="text-rose-500">*</span></label>
                        <input type="text" name="customer_name" x-model="customerName" required placeholder="PT Maju Bersama..." class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Nomor Telepon Pelanggan</label>
                        <input type="text" name="customer_phone" x-model="customerPhone" placeholder="081234567890" class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <!-- PRD Section: Rincian Barang -->
                <div class="p-3.5 bg-slate-50 border border-slate-200 rounded-lg space-y-3">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-semibold text-slate-800 uppercase tracking-wider">Rincian Barang yang Dijual</label>
                        <span class="text-[11px] text-slate-500">Pilih dari katalog atau ketik manual</span>
                    </div>

                    @if($products->count() > 0)
                        <div>
                            <label class="block text-[11px] font-medium text-slate-600 mb-1">Pilih dari Katalog Produk (Stok Siap Kirim)</label>
                            <select name="product_id" x-model="selectedProductId" @change="onProductChange()" class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                                <option value="">-- Pilih Produk dari Katalog --</option>
                                @foreach($products as $p)
                                    <option value="{{ $p->id }}">{{ $p->sku }} - {{ $p->name }} (Stok: {{ number_format($p->stock, 0) }} {{ $p->unit }} | Hrg: Rp {{ number_format($p->selling_price, 0) }})</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Nama Barang <span class="text-rose-500">*</span></label>
                        <input type="text" name="nama_barang" x-model="namaBarang" required placeholder="Contoh: Genset Silent 50kVA / Kabel Fiber Optic" class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Kuantitas (Qty) <span class="text-rose-500">*</span></label>
                            <input type="number" step="1" min="1" name="kuantitas" x-model="kuantitas" required class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500 font-mono">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Harga Satuan (Rp) <span class="text-rose-500">*</span></label>
                            <input type="number" step="1000" min="0" name="harga_satuan" x-model="hargaSatuan" required placeholder="Harga per unit" class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500 font-mono">
                        </div>
                    </div>

                    <div class="flex justify-between items-center pt-2 border-t border-slate-200 text-xs">
                        <span class="font-medium text-slate-600">Total Nominal Penjualan:</span>
                        <span class="font-mono font-semibold text-slate-900 text-sm">Rp <span x-text="calcTotal()">0</span></span>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Catatan Pengiriman / Ekspedisi</label>
                    <textarea name="catatan_pengiriman" rows="2" placeholder="Contoh: Pengiriman via armada gudang, resi no: EXP-9901..." class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>

                @if(auth()->user()->isAdmin())
                    <div class="rounded-md bg-amber-50 border border-amber-200 p-2.5 text-xs text-amber-800 flex items-start gap-2">
                        <i class="fa-solid fa-circle-info text-amber-600 mt-0.5"></i>
                        <span>Input Admin berstatus <strong>Pending</strong> dan memerlukan persetujuan Manager sebelum pemotongan stok dan invoice difinalkan.</span>
                    </div>
                @endif

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-200">
                    <button type="button" @click="createModal = false" class="px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium text-xs rounded-md transition">Batal</button>
                    <button type="submit" class="px-4 py-1.5 bg-purple-600 hover:bg-purple-700 text-white font-medium text-xs rounded-md transition shadow-xs">Simpan & Ajukan Transaksi</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Persetujuan Penjualan Barang (Manager / Owner) -->
    <div x-show="approveModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs" x-cloak>
        <div class="w-full max-w-md overflow-hidden rounded-lg bg-white border border-slate-200 shadow-xl p-5 space-y-4" @click.away="approveModal = false">
            <div class="flex items-start justify-between border-b border-slate-200 pb-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-600 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-check text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 leading-tight">Persetujuan Penjualan Barang</h3>
                        <p class="text-[11px] text-emerald-700 font-medium mt-0.5">Wewenang Manajemen</p>
                    </div>
                </div>
                <button type="button" @click="approveModal = false" class="text-slate-400 hover:text-slate-600 transition">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <!-- Detail Transaksi -->
            <div class="bg-slate-50 rounded-lg p-3 border border-slate-200 text-xs space-y-1.5">
                <div class="flex items-center justify-between">
                    <span class="text-slate-500">No. Transaksi:</span>
                    <span class="font-mono font-bold text-slate-900" x-text="selectedSale ? selectedSale.sale_number : ''"></span>
                </div>
                <div class="flex items-start justify-between gap-2">
                    <span class="text-slate-500 shrink-0">Barang / Item:</span>
                    <span class="font-semibold text-slate-800 text-right" x-text="selectedSale ? selectedSale.nama_barang : ''"></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-500">Total Penjualan:</span>
                    <span class="font-mono font-bold text-emerald-700" x-text="selectedSale ? selectedSale.total_amount : ''"></span>
                </div>
            </div>

            <p class="text-xs text-slate-600 leading-relaxed">
                Apakah Anda yakin ingin menyetujui transaksi penjualan ini? Stok gudang akan otomatis dialokasikan dan status menjadi <strong>Disetujui</strong>.
            </p>

            <form :action="approveActionUrl" method="POST" class="space-y-3.5">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                        Catatan Persetujuan (Opsional)
                    </label>
                    <textarea name="notes" rows="2" placeholder="Tuliskan catatan persetujuan jika ada..." class="w-full px-3 py-2 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-200">
                    <button type="button" @click="approveModal = false" class="px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium text-xs rounded-md transition">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs rounded-md transition flex items-center gap-1.5 shadow-xs">
                        <i class="fa-solid fa-check text-[11px]"></i> Ya, Setujui Transaksi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Rejection Notes -->
    <div x-show="rejectModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs" x-cloak>
        <div class="w-full max-w-md overflow-hidden rounded-lg bg-white border border-slate-200 shadow-xl p-5 space-y-4" @click.away="rejectModal = false">
            <div class="flex items-start justify-between border-b border-slate-200 pb-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-lg bg-rose-50 border border-rose-200 text-rose-600 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-ban text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 leading-tight">Penolakan Penjualan Barang</h3>
                        <p class="text-[11px] text-rose-600 font-medium mt-0.5">Wewenang Manajemen</p>
                    </div>
                </div>
                <button type="button" @click="rejectModal = false" class="text-slate-400 hover:text-slate-600 transition">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <!-- Detail Transaksi -->
            <div class="bg-slate-50 rounded-lg p-3 border border-slate-200 text-xs space-y-1.5" x-show="selectedSale">
                <div class="flex items-center justify-between">
                    <span class="text-slate-500">No. Transaksi:</span>
                    <span class="font-mono font-bold text-slate-900" x-text="selectedSale ? selectedSale.sale_number : ''"></span>
                </div>
                <div class="flex items-start justify-between gap-2">
                    <span class="text-slate-500 shrink-0">Barang / Item:</span>
                    <span class="font-semibold text-slate-800 text-right" x-text="selectedSale ? selectedSale.nama_barang : ''"></span>
                </div>
            </div>

            <form :action="rejectActionUrl" method="POST" class="space-y-3.5">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                        Alasan Penolakan <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="notes" rows="3" required placeholder="Contoh: Stok barang tidak mencukupi atau harga satuan di bawah batas minimum..." class="w-full px-3 py-2 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-rose-500"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-200">
                    <button type="button" @click="rejectModal = false" class="px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium text-xs rounded-md transition">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-1.5 bg-rose-600 hover:bg-rose-700 text-white font-semibold text-xs rounded-md transition flex items-center gap-1.5 shadow-xs">
                        <i class="fa-solid fa-ban text-[11px]"></i> Ya, Tolak Transaksi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Verifikasi Hapus Transaksi (Manager & Owner) -->
    <div x-show="deleteModal" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs" 
         @keydown.escape.window="deleteModal = false">
        <div class="w-full max-w-md overflow-hidden rounded-lg bg-white border border-slate-200 shadow-xl p-5 space-y-4"
             @click.away="deleteModal = false">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-rose-50 border border-rose-200 text-rose-600 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-triangle-exclamation text-xs"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Verifikasi Hapus Transaksi</h3>
                        <p class="text-[11px] text-slate-500">Tercatat di Log Aktivitas Owner</p>
                    </div>
                </div>
                <button type="button" @click="deleteModal = false" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <!-- Detail Ringkasan Transaksi -->
            <div class="bg-slate-50 border border-slate-200 rounded-lg p-3 text-xs space-y-1.5" x-show="selectedSale">
                <div class="flex justify-between">
                    <span class="text-slate-500">No. Transaksi:</span>
                    <span class="font-mono font-bold text-slate-800" x-text="selectedSale ? selectedSale.sale_number : ''"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Barang / Item:</span>
                    <span class="font-semibold text-slate-800" x-text="selectedSale ? selectedSale.nama_barang : ''"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Total Transaksi:</span>
                    <span class="font-bold text-slate-800" x-text="selectedSale ? selectedSale.total_amount : ''"></span>
                </div>
            </div>

            <!-- Peringatan Audit SOP -->
            <div class="bg-rose-50/70 border border-rose-200 rounded-lg p-3 text-xs text-rose-900 leading-relaxed">
                <div class="flex items-start gap-2">
                    <i class="fa-solid fa-circle-info text-rose-600 text-xs mt-0.5 shrink-0"></i>
                    <div class="text-[11px]">
                        <span class="font-bold">Apakah Anda yakin ingin menghapus transaksi ini?</span>
                        <p class="mt-0.5 text-rose-800/90">
                            Stok barang terkait akan dipulihkan otomatis. Penghapusan oleh Manager dicatat di <strong>Log Aktivitas Owner</strong> bersama catatan alasan Anda.
                        </p>
                    </div>
                </div>
            </div>

            <form :action="deleteActionUrl" method="POST" class="space-y-3.5">
                @csrf
                @method('DELETE')
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                        Catatan Alasan Penghapusan <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="deletion_reason" 
                              x-model="deletionReason" 
                              required 
                              minlength="5" 
                              maxlength="1000" 
                              rows="3" 
                              placeholder="Tuliskan catatan alasan jelas kenapa transaksi ini dihapus..." 
                              class="w-full px-3 py-2 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-rose-500 text-slate-800"></textarea>
                    <p class="text-[11px] text-slate-400 mt-1">Minimal 5 karakter. Wajib diisi sebagai rekaman audit manajerial.</p>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-200">
                    <button type="button" @click="deleteModal = false" class="px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium text-xs rounded-md transition">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-1.5 bg-rose-600 hover:bg-rose-700 text-white font-semibold text-xs rounded-md transition flex items-center gap-1.5 shadow-xs">
                        <i class="fa-solid fa-trash-can text-[11px]"></i> Ya, Hapus Transaksi
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
