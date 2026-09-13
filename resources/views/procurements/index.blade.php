@extends('layouts.app')

@section('title', 'Pengadaan Barang')
@section('header-title', 'Manajemen Procurement & Pembelian Supplier')

@section('content')
<div class="space-y-6" x-data="{ createModal: false }">

    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex flex-col md:flex-row justify-between items-center gap-4">
        <h3 class="font-bold text-slate-800 text-base">Riwayat Transaksi Pengadaan</h3>

        <button @click="createModal = true" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-xl shadow transition flex items-center gap-2">
            <i class="fa-solid fa-plus"></i> Buat Pengadaan Barang Baru
        </button>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-200">
                        <th class="p-4">No. Pengadaan</th>
                        <th class="p-4">Supplier & Tender</th>
                        <th class="p-4">Tanggal Pengadaan</th>
                        <th class="p-4">Item Barang</th>
                        <th class="p-4">Total Biaya</th>
                        <th class="p-4">Status Stok</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($procurements as $prc)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="p-4 font-mono font-bold text-blue-600">{{ $prc->procurement_number }}</td>
                            <td class="p-4">
                                <div class="font-bold text-slate-800">{{ $prc->supplier->name }}</div>
                                <div class="text-xs text-slate-500">
                                    {{ $prc->tender ? 'Tender: ' . $prc->tender->tender_number : 'Pengadaan Rutin Stok' }}
                                </div>
                            </td>
                            <td class="p-4 text-slate-600">{{ \Carbon\Carbon::parse($prc->procurement_date)->format('d M Y') }}</td>
                            <td class="p-4 text-xs text-slate-700">
                                <ul class="list-disc list-inside space-y-0.5">
                                    @foreach($prc->items as $item)
                                        <li>{{ $item->product->name ?? 'Produk' }} ({{ number_format($item->quantity,0) }} {{ $item->product->unit ?? 'unit' }})</li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="p-4 font-bold text-slate-800">Rp {{ number_format($prc->total_amount, 0, ',', '.') }}</td>
                            <td class="p-4">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $prc->status === 'Received' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                    {{ $prc->status === 'Received' ? 'Diterima (+Stok)' : $prc->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-400">Belum ada data pengadaan barang.</td>
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
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Supplier</label>
                        <select name="supplier_id" required class="w-full px-3 py-2 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500">
                            @foreach($suppliers as $sup)
                                <option value="{{ $sup->id }}">{{ $sup->name }}</option>
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

                <div class="grid grid-cols-2 gap-4">
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
                    <div class="space-y-3">
                        <div class="grid grid-cols-12 gap-2 items-center bg-slate-50 p-3 rounded-xl border border-slate-200 text-xs">
                            <div class="col-span-6">
                                <label class="block font-semibold text-slate-600 mb-1">Produk</label>
                                <select name="items[0][product_id]" required class="w-full p-2 border rounded-lg bg-white">
                                    @foreach($products as $p)
                                        <option value="{{ $p->id }}">{{ $p->sku }} - {{ $p->name }} (Hrg: Rp {{ number_format($p->purchase_price,0) }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-span-3">
                                <label class="block font-semibold text-slate-600 mb-1">Jumlah Qty</label>
                                <input type="number" step="0.01" name="items[0][quantity]" required value="5" class="w-full p-2 border rounded-lg">
                            </div>
                            <div class="col-span-3">
                                <label class="block font-semibold text-slate-600 mb-1">Harga Satuan (Rp)</label>
                                <input type="number" name="items[0][price]" required value="5000000" class="w-full p-2 border rounded-lg">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t">
                    <button type="button" @click="createModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm rounded-xl">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-xl shadow">Proses Pengadaan</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
