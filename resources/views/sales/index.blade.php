@extends('layouts.app')

@section('title', 'Penjualan Barang')
@section('header-title', 'Manajemen Penjualan & Otomatisasi Invoice')

@section('content')
<div class="space-y-6" x-data="{ createModal: false }">

    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex flex-col md:flex-row justify-between items-center gap-4">
        <h3 class="font-bold text-slate-800 text-base">Riwayat Penjualan Perdagangan</h3>

        <button @click="createModal = true" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-xl shadow transition flex items-center gap-2">
            <i class="fa-solid fa-plus"></i> Transaksi Penjualan Baru
        </button>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-200">
                        <th class="p-4">No. Penjualan</th>
                        <th class="p-4">Pelanggan & Telepon</th>
                        <th class="p-4">Tanggal Transaksi</th>
                        <th class="p-4">Rincian Barang</th>
                        <th class="p-4">Total Penjualan</th>
                        <th class="p-4">Status & Invoice</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($sales as $s)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="p-4 font-mono font-bold text-blue-600">{{ $s->sale_number }}</td>
                            <td class="p-4">
                                <div class="font-bold text-slate-800">{{ $s->customer_name }}</div>
                                <div class="text-xs text-slate-500"><i class="fa-solid fa-phone"></i> {{ $s->customer_phone ?? '-' }}</div>
                            </td>
                            <td class="p-4 text-slate-600">{{ \Carbon\Carbon::parse($s->sale_date)->format('d M Y') }}</td>
                            <td class="p-4 text-xs text-slate-700">
                                <ul class="list-disc list-inside space-y-0.5">
                                    @foreach($s->items as $item)
                                        <li>{{ $item->product->name ?? 'Produk' }} ({{ number_format($item->quantity,0) }} {{ $item->product->unit ?? 'unit' }})</li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="p-4 font-bold text-emerald-600">Rp {{ number_format($s->total_amount, 0, ',', '.') }}</td>
                            <td class="p-4">
                                <div class="space-y-1">
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 block w-fit">
                                        {{ $s->status }}
                                    </span>
                                    @if($s->invoices->count() > 0)
                                        <a href="{{ route('invoices.show', $s->invoices->first()->id) }}" class="text-[11px] font-semibold text-blue-600 hover:underline block">
                                            <i class="fa-solid fa-file-invoice"></i> {{ $s->invoices->first()->invoice_number }}
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-400">Belum ada transaksi penjualan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100">
            {{ $sales->links() }}
        </div>
    </div>

    <!-- Modal Create Sale -->
    <div x-show="createModal" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4" x-cloak>
        <div class="bg-white rounded-2xl max-w-2xl w-full p-6 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center border-b pb-3">
                <h3 class="font-bold text-lg text-slate-800">Input Penjualan Barang Baru</h3>
                <button @click="createModal = false" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>

            <form action="{{ route('sales.store') }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Nama Pelanggan / Perusahaan</label>
                        <input type="text" name="customer_name" required placeholder="PT Mega Utama..." class="w-full px-3 py-2 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Nomor Telepon Pelanggan</label>
                        <input type="text" name="customer_phone" placeholder="081234567890" class="w-full px-3 py-2 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Tanggal Penjualan</label>
                        <input type="date" name="sale_date" required value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500">
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

                <div class="border-t pt-3">
                    <label class="block text-xs font-bold text-slate-800 uppercase mb-2">Item Barang yang Dijual</label>
                    <div id="sale-items" class="space-y-3">
                        <div class="sale-item-row grid grid-cols-1 sm:grid-cols-12 gap-2 items-center bg-slate-50 p-3 rounded-xl border border-slate-200 text-xs">
                            <div class="sm:col-span-6">
                                <label class="block font-semibold text-slate-600 mb-1">Produk (Stok Tersedia)</label>
                                <select name="items[0][product_id]" required class="w-full p-2 border rounded-lg bg-white">
                                    @foreach($products as $p)
                                        <option value="{{ $p->id }}">{{ $p->sku }} - {{ $p->name }} (Stok: {{ number_format($p->stock,0) }} {{ $p->unit }}, Hrg: Rp {{ number_format($p->selling_price,0) }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="sm:col-span-3">
                                <label class="block font-semibold text-slate-600 mb-1">Qty Jual</label>
                                <input type="number" step="0.01" name="items[0][quantity]" required min="0.01" placeholder="Qty" class="w-full p-2 border rounded-lg">
                            </div>
                            <div class="sm:col-span-3">
                                <label class="block font-semibold text-slate-600 mb-1">Harga Jual (Rp)</label>
                                <input type="number" name="items[0][price]" required min="0" placeholder="Harga jual" class="w-full p-2 border rounded-lg">
                            </div>
                        </div>
                    </div>
                    <button type="button" onclick="addSaleItem()" class="mt-3 text-xs font-semibold text-blue-600 hover:text-blue-800"><i class="fa-solid fa-plus"></i> Tambah item barang</button>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t">
                    <button type="button" @click="createModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm rounded-xl">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-xl shadow">Proses Penjualan & Terbitkan Invoice</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
    function addSaleItem() {
        const container = document.getElementById('sale-items');
        const index = container.querySelectorAll('.sale-item-row').length;
        const row = container.querySelector('.sale-item-row').cloneNode(true);
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
