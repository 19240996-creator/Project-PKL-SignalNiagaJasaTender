@extends('layouts.app')
@section('title', 'Quotation & Order')
@section('header-title', 'Quotation & Customer Order')
@section('content')
<div class="space-y-6" x-data="commercialForms()">
    <div class="grid md:grid-cols-3 gap-4">
        <button @click="modal = 'service'" class="p-5 bg-white border rounded-2xl text-left hover:border-blue-500"><i class="fa-solid fa-briefcase text-blue-600"></i><div class="mt-2 font-bold">Quotation Jasa</div><div class="text-xs text-slate-500">Penawaran pekerjaan jasa kepada klien</div></button>
        <button @click="modal = 'sales'" class="p-5 bg-white border rounded-2xl text-left hover:border-blue-500"><i class="fa-solid fa-tags text-emerald-600"></i><div class="mt-2 font-bold">Sales Quotation</div><div class="text-xs text-slate-500">Penawaran produk kepada pelanggan</div></button>
        <button @click="modal = 'order'" class="p-5 bg-white border rounded-2xl text-left hover:border-blue-500"><i class="fa-solid fa-cart-plus text-amber-600"></i><div class="mt-2 font-bold">Customer Order</div><div class="text-xs text-slate-500">Pesanan pelanggan sebelum penjualan</div></button>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        <section class="bg-white border rounded-2xl overflow-hidden"><h2 class="p-4 font-bold border-b">Quotation Jasa</h2>@forelse($serviceQuotations as $q)<div class="p-4 border-b text-sm"><b>{{ $q->quotation_number }}</b><div>{{ $q->client->name }}</div><span class="text-xs text-blue-600">{{ $q->status }} · Rp {{ number_format($q->total_amount, 0, ',', '.') }}</span>@if($q->status === 'Approved')<form method="POST" action="{{ route('commercial.service.convert', $q) }}" class="mt-2 grid grid-cols-2 gap-2">@csrf<input name="contract_number" required value="CTR-QTN-{{ $q->id }}" class="rounded-lg border p-1.5 text-xs"><input type="date" name="start_date" required value="{{ date('Y-m-d') }}" class="rounded-lg border p-1.5 text-xs"><input type="date" name="end_date" required value="{{ date('Y-m-d', strtotime('+1 year')) }}" class="rounded-lg border p-1.5 text-xs"><input type="number" name="fee_percentage" min="0" max="100" step="0.01" placeholder="Fee %" class="rounded-lg border p-1.5 text-xs"><button class="col-span-2 rounded-lg bg-blue-600 px-2 py-1.5 text-xs font-semibold text-white">Konversi ke Kontrak</button></form>@endif</div>@empty<div class="p-5 text-sm text-slate-400">Belum ada data.</div>@endforelse</section>
        <section class="bg-white border rounded-2xl overflow-hidden"><h2 class="p-4 font-bold border-b">Sales Quotation</h2>@forelse($salesQuotations as $q)<div class="p-4 border-b text-sm"><b>{{ $q->quotation_number }}</b><div>{{ $q->customer_name }}</div><span class="text-xs text-emerald-600">{{ $q->status }} · Rp {{ number_format($q->total_amount, 0, ',', '.') }}</span></div>@empty<div class="p-5 text-sm text-slate-400">Belum ada data.</div>@endforelse</section>
        <section class="bg-white border rounded-2xl overflow-hidden"><h2 class="p-4 font-bold border-b">Customer Order</h2>@forelse($orders as $order)<div class="p-4 border-b text-sm"><b>{{ $order->order_number }}</b><div>{{ $order->customer_name }}</div><span class="text-xs text-amber-600">{{ $order->status }} · Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>@if($order->status === 'Confirmed')<form method="POST" action="{{ route('commercial.order.convert', $order) }}" class="mt-2">@csrf<button class="text-xs text-blue-600 font-semibold hover:underline">Konversi ke Penjualan</button></form>@endif</div>@empty<div class="p-5 text-sm text-slate-400">Belum ada data.</div>@endforelse</section>
    </div>

    <div x-show="modal" x-cloak class="fixed inset-0 z-50 bg-slate-900/50 flex items-center justify-center p-4"><div @click.outside="modal = null" class="bg-white rounded-2xl p-6 w-full max-w-xl max-h-[90vh] overflow-y-auto"><div class="flex justify-between mb-5"><h2 class="font-bold" x-text="modal === 'service' ? 'Quotation Jasa' : modal === 'sales' ? 'Sales Quotation' : 'Customer Order'"></h2><button type="button" @click="modal = null"><i class="fa-solid fa-xmark"></i></button></div>
        <form method="POST" :action="modal === 'service' ? '{{ route('commercial.service.store') }}' : modal === 'sales' ? '{{ route('commercial.sales.store') }}' : '{{ route('commercial.order.store') }}'" class="space-y-3">@csrf
            <template x-if="modal === 'service'"><select name="client_id" required class="w-full p-2.5 border rounded-xl"><option value="">Pilih klien</option>@foreach($clients as $client)<option value="{{ $client->id }}">{{ $client->name }}</option>@endforeach</select></template>
            <template x-if="modal !== 'service'"><input name="customer_name" required placeholder="Nama pelanggan" class="w-full p-2.5 border rounded-xl"></template>
            <template x-if="modal === 'order'"><select name="status" required class="w-full p-2.5 border rounded-xl"><option value="Draft">Draft</option><option value="Confirmed">Confirmed</option></select></template>
            <template x-if="modal !== 'order'"><input type="hidden" name="status" value="Draft"></template>
            <template x-if="modal === 'service'"><input name="quotation_number" required value="QTN-SVC-{{ date('YmdHis') }}" class="w-full p-2.5 border rounded-xl"></template>
            <template x-if="modal === 'sales'"><input name="quotation_number" required value="QTN-SLS-{{ date('YmdHis') }}" class="w-full p-2.5 border rounded-xl"></template>
            <template x-if="modal === 'order'"><input name="order_number" required value="ORD-{{ date('YmdHis') }}" class="w-full p-2.5 border rounded-xl"></template>
            <input type="date" name="quotation_date" x-show="modal !== 'order'" value="{{ date('Y-m-d') }}" class="w-full p-2.5 border rounded-xl"><input type="date" name="order_date" x-show="modal === 'order'" value="{{ date('Y-m-d') }}" class="w-full p-2.5 border rounded-xl">
            <template x-if="modal === 'service'"><input name="items[0][description]" required placeholder="Deskripsi jasa" class="w-full p-2.5 border rounded-xl"></template>
            <template x-if="modal !== 'service'"><select name="items[0][product_id]" required class="w-full p-2.5 border rounded-xl"><option value="">Pilih produk</option>@foreach($products as $product)<option value="{{ $product->id }}">{{ $product->sku }} - {{ $product->name }}</option>@endforeach</select></template>
            <div class="grid grid-cols-2 gap-3"><input type="number" step="0.01" min="0.01" name="items[0][quantity]" required placeholder="Quantity" class="p-2.5 border rounded-xl"><input type="number" step="0.01" min="0" name="items[0][price]" required placeholder="Harga" class="p-2.5 border rounded-xl"></div>
            <template x-if="modal === 'order'"><input name="sales_quotation_id" type="number" placeholder="ID sales quotation (opsional)" class="w-full p-2.5 border rounded-xl"></template>
            <button class="w-full py-2.5 bg-blue-600 text-white rounded-xl font-semibold">Simpan</button>
        </form>
    </div></div>
</div>
@endsection
@section('scripts')<script>function commercialForms(){return {modal:null};}</script>@endsection
