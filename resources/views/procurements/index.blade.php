@extends('layouts.app')

@section('title', 'Pengadaan Barang')
@section('header-title', 'Manajemen Procurement & Pembelian Supplier')

@section('content')
<div class="space-y-6" x-data="{ createModal: false, supplierModal: false }">

    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex flex-col md:flex-row justify-between items-center gap-4">
        <h3 class="font-bold text-slate-800 text-base">Riwayat Transaksi Pengadaan</h3>

        <button @click="createModal = true" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-xl shadow transition flex items-center gap-2">
            <i class="fa-solid fa-plus"></i> Buat Pengadaan Barang Baru
        </button>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[900px] text-left text-sm">
                <thead class="bg-blue-600 text-white">
                    <tr class="border-b border-blue-700 font-semibold uppercase tracking-wider text-xs text-white">
                        <th class="w-[17%] px-5 py-3.5 font-semibold text-white whitespace-nowrap text-left">No. Pengadaan</th>
                        <th class="w-[25%] px-5 py-3.5 font-semibold text-white text-left">Supplier & Tender</th>
                        <th class="w-[15%] px-5 py-3.5 font-semibold text-white whitespace-nowrap text-left">Tanggal Pengadaan</th>
                        <th class="w-[18%] px-5 py-3.5 font-semibold text-white text-left">Item Barang</th>
                        <th class="w-[13%] px-5 py-3.5 font-semibold text-white whitespace-nowrap text-left">Total Biaya</th>
                        <th class="w-[12%] px-5 py-3.5 font-semibold text-white whitespace-nowrap text-left">Status Stok</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($procurements as $prc)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-5 py-3.5 align-middle font-mono font-bold text-blue-600 text-xs sm:text-sm whitespace-nowrap">
                                {{ $prc->procurement_number }}
                            </td>
                            <td class="px-5 py-3.5 align-middle">
                                <div class="font-bold text-slate-900 text-xs sm:text-sm leading-snug">{{ $prc->supplier->name ?? 'Supplier N/A' }}</div>
                                <div class="text-xs text-slate-500 mt-0.5">
                                    {{ $prc->tender ? 'Tender: ' . $prc->tender->tender_number : 'Pengadaan Rutin Stok' }}
                                </div>
                            </td>
                            <td class="px-5 py-3.5 align-middle text-slate-600 text-xs sm:text-sm whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5">
                                    <i class="fa-regular fa-calendar text-slate-400 text-xs"></i>
                                    <span>{{ \Carbon\Carbon::parse($prc->procurement_date)->format('d M Y') }}</span>
                                </span>
                            </td>
                            <td class="px-5 py-3.5 align-middle">
                                <div class="space-y-1">
                                    @forelse($prc->items as $item)
                                        <div class="text-xs text-slate-800 flex items-center gap-1.5">
                                            <span class="font-medium text-slate-900">{{ $item->product->name ?? 'Produk' }}</span>
                                            <span class="text-slate-500 font-mono text-[11px]">({{ number_format($item->quantity, 0, ',', '.') }} {{ $item->product->unit ?? 'unit' }})</span>
                                        </div>
                                    @empty
                                        <span class="text-xs text-slate-400">Tidak ada item</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="px-5 py-3.5 align-middle whitespace-nowrap">
                                <div class="font-mono font-bold text-slate-900 text-xs sm:text-sm">
                                    Rp {{ number_format($prc->total_amount, 0, ',', '.') }}
                                </div>
                            </td>
                            <td class="px-5 py-3.5 align-middle whitespace-nowrap">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold {{ $prc->status === 'Received' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $prc->status === 'Received' ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>
                                    {{ $prc->status === 'Received' ? 'Diterima (+Stok)' : $prc->status }}
                                </span>
                                @if($prc->status !== 'Received')
                                    <form action="{{ route('procurements.receive', $prc) }}" method="POST" class="mt-1.5">
                                        @csrf
                                        <button type="submit" class="text-xs font-semibold text-blue-600 hover:text-blue-800 transition">Konfirmasi Diterima</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-slate-400">Belum ada data pengadaan barang.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100">
            {{ $procurements->links() }}
        </div>
    </div>

    <!-- Modal Create Procurement -->
    <div x-show="createModal" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4" x-cloak>
        <div class="bg-white rounded-2xl max-w-2xl w-full p-6 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center border-b pb-3">
                <h3 class="font-bold text-lg text-slate-800">Buat Transaksi Pengadaan Barang</h3>
                <button @click="createModal = false" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>

            <form action="{{ route('procurements.store') }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-xs font-semibold text-slate-700 uppercase">Supplier</label>
                            <button type="button" @click="supplierModal = true" class="text-xs font-semibold text-blue-600 hover:text-blue-800">
                                <i class="fa-solid fa-plus mr-1"></i> Tambah supplier
                            </button>
                        </div>
                        <select name="supplier_id" required class="w-full px-3 py-2 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Pilih supplier --</option>
                            @foreach($suppliers as $sup)
                                <option value="{{ $sup->id }}">{{ $sup->code }} - {{ $sup->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Tender Terkait (Opsional)</label>
                        <select name="tender_id" class="w-full px-3 py-2 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Tidak Terikat Tender --</option>
                            @foreach($tenders as $tdr)
                                <option value="{{ $tdr->id }}">{{ $tdr->tender_number }} - {{ $tdr->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Tanggal Pengadaan</label>
                        <input type="date" name="procurement_date" required value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Status Pengadaan</label>
                        <select name="status" required class="w-full px-3 py-2 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="Received">Diterima (Langsung Tambah Stok)</option>
                            <option value="Processing">Dalam Proses Pembelian</option>
                        </select>
                    </div>
                </div>

                <div class="border-t pt-3">
                    <label class="block text-xs font-bold text-slate-800 uppercase mb-2">Item Barang yang Dibeli</label>
                    <div id="procurement-items" class="space-y-3">
                        <div class="procurement-item-row grid grid-cols-1 sm:grid-cols-12 gap-2 items-center bg-slate-50 p-3 rounded-xl border border-slate-200 text-xs">
                            <div class="sm:col-span-6">
                                <label class="block font-semibold text-slate-600 mb-1">Produk</label>
                                <select name="items[0][product_id]" required class="w-full p-2 border rounded-lg bg-white">
                                    @foreach($products as $p)
                                        <option value="{{ $p->id }}">{{ $p->sku }} - {{ $p->name }} (Hrg: Rp {{ number_format($p->purchase_price,0) }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="sm:col-span-3">
                                <label class="block font-semibold text-slate-600 mb-1">Jumlah Qty</label>
                                <input type="number" step="0.01" name="items[0][quantity]" required min="0.01" placeholder="Qty" class="w-full p-2 border rounded-lg">
                            </div>
                            <div class="sm:col-span-3">
                                <label class="block font-semibold text-slate-600 mb-1">Harga Satuan (Rp)</label>
                                <input type="number" name="items[0][price]" required min="0" placeholder="Harga beli" class="w-full p-2 border rounded-lg">
                            </div>
                        </div>
                    </div>
                    <button type="button" onclick="addProcurementItem()" class="mt-3 text-xs font-semibold text-blue-600 hover:text-blue-800"><i class="fa-solid fa-plus"></i> Tambah item barang</button>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t">
                    <button type="button" @click="createModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm rounded-xl">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-xl shadow">Proses Pengadaan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Create Supplier -->
    <div x-show="supplierModal" class="fixed inset-0 z-[60] bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4" x-cloak>
        <div @click.outside="supplierModal = false" class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4">
            <div class="flex justify-between items-center border-b pb-3">
                <div>
                    <h3 class="font-bold text-lg text-slate-800">Tambah Supplier</h3>
                    <p class="text-xs text-slate-500 mt-1">Supplier baru akan langsung tersedia di form Purchasing.</p>
                </div>
                <button type="button" @click="supplierModal = false" class="text-slate-400 hover:text-slate-600" aria-label="Tutup">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form id="create-supplier-form" action="{{ route('suppliers.store') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="return_to" value="purchasing">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Kode supplier</label>
                        <input type="text" name="code" required value="SUP-{{ rand(1000, 9999) }}" class="w-full px-3 py-2 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Nama supplier</label>
                        <input type="text" name="name" required placeholder="PT Nama Supplier" class="w-full px-3 py-2 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Telepon</label>
                        <input type="text" name="phone" class="w-full px-3 py-2 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Email</label>
                        <input type="email" name="email" class="w-full px-3 py-2 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Alamat</label>
                    <textarea name="address" rows="2" class="w-full px-3 py-2 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
                <input type="hidden" name="status" value="active">
                <div class="flex justify-end gap-2 pt-3 border-t">
                    <button type="button" @click="supplierModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm rounded-xl">Batal</button>
                    <button type="submit" form="create-supplier-form" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-xl shadow">Simpan supplier</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
    function addProcurementItem() {
        const container = document.getElementById('procurement-items');
        const index = container.querySelectorAll('.procurement-item-row').length;
        const row = container.querySelector('.procurement-item-row').cloneNode(true);
        row.innerHTML = row.innerHTML.replaceAll('[0]', '[' + index + ']');
        row.querySelectorAll('input').forEach((input) => input.value = '');
        const remove = document.createElement('button');
        remove.type = 'button';
        remove.className = 'sm:col-span-12 text-right text-xs font-semibold text-rose-600 hover:text-rose-800';
        remove.innerHTML = '<i class="fa-solid fa-trash"></i> Hapus item';
        remove.onclick = () => row.remove();
        row.appendChild(remove);
        container.appendChild(row);
    }
</script>
@endsection
