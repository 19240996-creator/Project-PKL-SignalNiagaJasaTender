@extends('layouts.app')

@section('title', 'Master Produk & Stok')
@section('header-title', 'Persediaan Stok Barang & Katalog Produk')

@section('content')
<div class="space-y-6" x-data="{ createModal: false, adjustModal: false, selectedProduct: null }">

    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex flex-col md:flex-row justify-between items-center gap-4">
        <form method="GET" action="{{ route('products.index') }}" class="flex items-center gap-3 w-full md:w-auto">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari SKU, nama produk, kategori..." class="px-4 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500 outline-none w-64">
            <button type="submit" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm rounded-xl">Cari</button>
        </form>

        <button @click="createModal = true" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-xl shadow transition flex items-center gap-2">
            <i class="fa-solid fa-plus"></i> Tambah Produk Baru
        </button>
    </div>

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
                        <th class="p-4 text-center">Aksi Stok</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($products as $p)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="p-4 font-mono font-bold text-blue-600">{{ $p->sku }}</td>
                            <td class="p-4">
                                <div class="font-bold text-slate-800">{{ $p->name }}</div>
                                <div class="text-xs text-slate-500"><i class="fa-solid fa-tag"></i> {{ $p->category ?? 'Umum' }}</div>
                            </td>
                            <td class="p-4 text-slate-700">Rp {{ number_format($p->purchase_price, 0, ',', '.') }}</td>
                            <td class="p-4 font-bold text-emerald-600">Rp {{ number_format($p->selling_price, 0, ',', '.') }}</td>
                            <td class="p-4">
                                <div class="font-bold text-base {{ $p->isLowStock() ? 'text-rose-600' : 'text-slate-800' }}">
                                    {{ number_format($p->stock, 0) }} {{ $p->unit }}
                                </div>
                                <div class="mt-0.5">
                                    @if($p->isLowStock())
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-700 border border-rose-200">Stok Menipis</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">Aman (Min {{ number_format($p->minimum_stock, 0) }})</span>
                                    @endif
                                </div>
                            </td>
                            <td class="p-4 text-center">
                                <button @click="selectedProduct = {{ $p }}; adjustModal = true" class="px-3 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-lg transition" title="Penyesuaian Stok">
                                    <i class="fa-solid fa-boxes-packing"></i> Adjust Stok
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-400">Belum ada data produk.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100">
            {{ $products->links() }}
        </div>
    </div>

    <!-- Modal Create Product -->
    <div x-show="createModal" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4" x-cloak>
        <div class="bg-white rounded-2xl max-w-xl w-full p-6 shadow-2xl space-y-4">
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

    <!-- Modal Adjust Stock -->
    <div x-show="adjustModal" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4" x-cloak>
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
            <div class="flex justify-between items-center border-b pb-3">
                <h3 class="font-bold text-lg text-slate-800">Adjust / Penyesuaian Stok</h3>
                <button @click="adjustModal = false" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>

            <form x-bind:action="'/products/' + (selectedProduct ? selectedProduct.id : 0) + '/adjust'" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Jenis Pergerakan</label>
                    <select name="movement_type" required class="w-full px-3 py-2 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="IN">Stok Masuk (+)</option>
                        <option value="OUT">Stok Keluar (-)</option>
                        <option value="ADJUSTMENT">Koreksi / Stock Opname</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Jumlah Kuantitas</label>
                    <input type="number" step="0.01" name="quantity" required placeholder="5" class="w-full px-3 py-2 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Alasan Penyesuaian / Catatan</label>
                    <textarea name="notes" required rows="2" class="w-full px-3 py-2 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500" placeholder="Hasil stock opname bulanan..."></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t">
                    <button type="button" @click="adjustModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm rounded-xl">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-xl shadow">Update Stok</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
