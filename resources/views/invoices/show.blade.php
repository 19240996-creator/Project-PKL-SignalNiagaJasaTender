@extends('layouts.app')

@section('title', 'Detail Invoice — ' . $invoice->invoice_number)
@section('header-title', 'Rincian Invoice & Bukti Tagihan')

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">

    <div class="flex justify-between items-center bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
        <a href="{{ route('invoices.index') }}" class="text-sm font-semibold text-slate-600 hover:text-blue-600 flex items-center gap-2">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar Invoice
        </a>
        <button onclick="window.print()" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white font-semibold text-sm rounded-xl transition flex items-center gap-2">
            <i class="fa-solid fa-print"></i> Cetak Invoice
        </button>
    </div>

    <!-- Printable Invoice Sheet -->
    <div class="bg-white rounded-2xl p-8 border border-slate-200 shadow-lg space-y-8 print:shadow-none print:border-none">
        
        <!-- Header -->
        <div class="flex justify-between items-start border-b border-slate-200 pb-6">
            <div>
                <div class="flex items-center space-x-3 mb-2">
                    <div class="w-9 h-9 rounded-xl bg-blue-600 flex items-center justify-center font-bold text-white text-lg">
                        S
                    </div>
                    <div>
                        <span class="font-bold text-xl text-slate-900 block">PT Signal Panca Utama</span>
                        <span class="text-xs text-slate-500 font-medium">Tender &bull; Jasa &bull; Perdagangan</span>
                    </div>
                </div>
                <p class="text-xs text-slate-500 max-w-xs mt-1">
                    Jl Rubaya Buher SPU Mansion Kavling CahayaKarangpawitan, Kec. Karawang Bar., Karawang, Jawa Barat 41315<br>
                    Telp: (021) 7890-1234 | Email: finance@signalpanca.co.id
                </p>
            </div>

            <div class="text-right">
                <h2 class="text-2xl font-bold text-blue-600 tracking-tight">INVOICE</h2>
                <div class="font-mono font-bold text-slate-800 text-base mt-1">{{ $invoice->invoice_number }}</div>
                <div class="mt-2">
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200 uppercase">
                        Status: {{ $invoice->status }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Dates & Client Info -->
        <div class="grid grid-cols-2 gap-6 bg-slate-50 p-4 rounded-xl border border-slate-100 text-xs">
            <div>
                <span class="text-slate-400 font-semibold uppercase block mb-1">Ditagihkan Kepada:</span>
                @if($invoice->serviceJob)
                    <div class="font-bold text-slate-800 text-sm">{{ $invoice->serviceJob->contract->client->name }}</div>
                    <div class="text-slate-600">{{ $invoice->serviceJob->contract->client->company_name ?? '' }}</div>
                    <div class="text-slate-500 mt-1">{{ $invoice->serviceJob->contract->client->address ?? '-' }}</div>
                @elseif($invoice->sale)
                    <div class="font-bold text-slate-800 text-sm">{{ $invoice->sale->customer_name }}</div>
                    <div class="text-slate-600">Telp: {{ $invoice->sale->customer_phone ?? '-' }}</div>
                @else
                    <div class="font-bold text-slate-800 text-sm">Klien Umum</div>
                @endif
            </div>

            <div class="text-right space-y-1">
                <div><span class="text-slate-400 font-semibold">Tanggal Tanggal:</span> <span class="font-bold text-slate-700">{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d M Y') }}</span></div>
                <div><span class="text-slate-400 font-semibold">Jatuh Tempo:</span> <span class="font-bold text-rose-600">{{ \Carbon\Carbon::parse($invoice->due_date)->format('d M Y') }}</span></div>
            </div>
        </div>

        <!-- Line Items -->
        <div>
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-100 text-slate-700 font-semibold border-b border-slate-200">
                        <th class="p-3">Deskripsi Layanan / Barang</th>
                        <th class="p-3 text-center">Qty</th>
                        <th class="p-3 text-right">Harga Satuan</th>
                        <th class="p-3 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @if($invoice->serviceJob)
                        <tr>
                            <td class="p-3 font-medium text-slate-800">
                                Pekerjaan Jasa: {{ $invoice->serviceJob->name }} ({{ $invoice->serviceJob->job_number }})
                            </td>
                            <td class="p-3 text-center">1 Job</td>
                            <td class="p-3 text-right">Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</td>
                            <td class="p-3 text-right font-bold">Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @elseif($invoice->sale && $invoice->sale->items->count() > 0)
                        @foreach($invoice->sale->items as $item)
                            <tr>
                                <td class="p-3 font-medium text-slate-800">
                                    {{ $item->product->name ?? 'Produk' }} (SKU: {{ $item->product->sku ?? '-' }})
                                </td>
                                <td class="p-3 text-center">{{ number_format($item->quantity, 0) }} {{ $item->product->unit ?? 'unit' }}</td>
                                <td class="p-3 text-right">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                <td class="p-3 text-right font-bold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td class="p-3 font-medium text-slate-800">Layanan Bisnis SPU</td>
                            <td class="p-3 text-center">1</td>
                            <td class="p-3 text-right">Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</td>
                            <td class="p-3 text-right font-bold">Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Total Calculation -->
        <div class="flex justify-end pt-4 border-t border-slate-200">
            <div class="w-64 space-y-2 text-xs">
                <div class="flex justify-between text-slate-600">
                    <span>Subtotal:</span>
                    <span class="font-semibold">Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-slate-600">
                    <span>PPN (11%):</span>
                    <span class="font-semibold">Rp {{ number_format($invoice->tax_amount, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-base font-bold text-slate-900 border-t border-slate-200 pt-2">
                    <span>Total Tagihan:</span>
                    <span class="text-blue-600">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-slate-600 border-t border-slate-100 pt-2">
                    <span>Telah Dibayar:</span>
                    <span class="font-bold text-emerald-600">Rp {{ number_format($invoice->paid_amount, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-xs font-bold text-rose-600 bg-rose-50 p-2 rounded-lg">
                    <span>Sisa Piutang:</span>
                    <span>Rp {{ number_format($invoice->remaining_balance, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Payment History Table -->
        <div class="pt-6 border-t border-slate-200">
            <h4 class="font-bold text-slate-800 text-xs uppercase mb-3">Riwayat Pembayaran Diterima</h4>
            @if($invoice->payments->count() > 0)
                <table class="w-full text-left text-xs bg-slate-50 rounded-xl overflow-hidden">
                    <thead>
                        <tr class="bg-slate-100 text-slate-600 font-semibold border-b">
                            <th class="p-2.5">Tanggal Bayar</th>
                            <th class="p-2.5">Metode</th>
                            <th class="p-2.5">No. Referensi</th>
                            <th class="p-2.5 text-right">Jumlah Dibayar</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @foreach($invoice->payments as $pm)
                            <tr>
                                <td class="p-2.5 font-medium">{{ \Carbon\Carbon::parse($pm->payment_date)->format('d M Y') }}</td>
                                <td class="p-2.5">{{ $pm->payment_method }}</td>
                                <td class="p-2.5 font-mono text-slate-500">{{ $pm->reference_number ?? '-' }}</td>
                                <td class="p-2.5 text-right font-bold text-emerald-600">Rp {{ number_format($pm->amount, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="p-3 text-center text-slate-400 text-xs bg-slate-50 rounded-xl">Belum ada pembayaran dicatat untuk invoice ini.</div>
            @endif
        </div>

    </div>

</div>
@endsection
