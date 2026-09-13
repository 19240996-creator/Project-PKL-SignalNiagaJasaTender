@extends('layouts.app')

@section('title', 'Laporan Bisnis')
@section('header-title', 'Laporan Eksekutif & Operational SignalNiagaJasaTender')

@section('content')
<div class="space-y-6">

    <!-- Filter Bar & Print Header -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex flex-col md:flex-row justify-between items-center gap-4 print:hidden">
        <form method="GET" action="{{ route('laporan.index') }}" class="flex flex-wrap items-center gap-3 w-full md:w-auto">
            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Jenis Laporan</label>
                <select name="type" onchange="this.form.submit()" class="px-3 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                    <option value="tender" {{ $type === 'tender' ? 'selected' : '' }}>Laporan Tender</option>
                    <option value="contract" {{ $type === 'contract' ? 'selected' : '' }}>Laporan Kontrak Jasa</option>
                    <option value="procurement" {{ $type === 'procurement' ? 'selected' : '' }}>Laporan Pengadaan Barang</option>
                    <option value="sales" {{ $type === 'sales' ? 'selected' : '' }}>Laporan Penjualan Perdagangan</option>
                    <option value="stock" {{ $type === 'stock' ? 'selected' : '' }}>Laporan Persediaan Stok</option>
                    <option value="invoice" {{ $type === 'invoice' ? 'selected' : '' }}>Laporan Invoice & Piutang</option>
                    <option value="payment" {{ $type === 'payment' ? 'selected' : '' }}>Laporan Pembayaran Diterima</option>
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="px-3 py-2 rounded-xl border border-slate-200 text-sm outline-none">
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="px-3 py-2 rounded-xl border border-slate-200 text-sm outline-none">
            </div>

            <div class="self-end">
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-xl">
                    <i class="fa-solid fa-filter"></i> Tampilkan
                </button>
            </div>
        </form>

        <button onclick="window.print()" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white font-semibold text-sm rounded-xl transition flex items-center gap-2">
            <i class="fa-solid fa-print"></i> Cetak Laporan
        </button>
    </div>

    <!-- Report Document Printable Sheet -->
    <div class="bg-white rounded-2xl p-6 md:p-8 border border-slate-200 shadow-sm space-y-6">

        <div class="border-b border-slate-200 pb-4 flex justify-between items-center">
            <div>
                <h2 class="text-xl font-bold text-slate-900 uppercase tracking-tight">LAPORAN {{ strtoupper($type) }}</h2>
                <p class="text-xs text-slate-500">PT Signal Panca Utama &bull; Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
            </div>
            <div class="text-right text-xs text-slate-400">
                Dicetak pada: {{ date('d M Y H:i') }}
            </div>
        </div>

        <!-- Render Report Tables according to $type -->
        <div class="overflow-x-auto">
            @if($type === 'tender')
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-100 text-slate-700 font-semibold border-b">
                            <th class="p-3">No. Tender</th>
                            <th class="p-3">Nama Tender</th>
                            <th class="p-3">Klien</th>
                            <th class="p-3">Tanggal</th>
                            <th class="p-3 text-right">Nilai Estimasi</th>
                            <th class="p-3 text-right">Nilai Penawaran</th>
                            <th class="p-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($data as $t)
                            <tr>
                                <td class="p-3 font-mono font-bold text-blue-600">{{ $t->tender_number }}</td>
                                <td class="p-3 font-medium">{{ $t->name }}</td>
                                <td class="p-3">{{ $t->client->name ?? '-' }}</td>
                                <td class="p-3">{{ \Carbon\Carbon::parse($t->found_date)->format('d/m/Y') }}</td>
                                <td class="p-3 text-right">Rp {{ number_format($t->estimated_value, 0, ',', '.') }}</td>
                                <td class="p-3 text-right font-bold">Rp {{ number_format($t->bid_value, 0, ',', '.') }}</td>
                                <td class="p-3"><span class="px-2 py-0.5 rounded font-semibold bg-slate-100 text-slate-700">{{ $t->status }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="p-6 text-center text-slate-400">Tidak ada data tender pada periode ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            @elseif($type === 'contract')
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-100 text-slate-700 font-semibold border-b">
                            <th class="p-3">No. Kontrak</th>
                            <th class="p-3">Klien</th>
                            <th class="p-3">Tanggal Mulai</th>
                            <th class="p-3">Tanggal Berakhir</th>
                            <th class="p-3 text-right">Nilai Kontrak</th>
                            <th class="p-3 text-right">Fee (%)</th>
                            <th class="p-3 text-right">Nilai Fee</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($data as $c)
                            <tr>
                                <td class="p-3 font-mono font-bold text-blue-600">{{ $c->contract_number }}</td>
                                <td class="p-3 font-medium">{{ $c->client->name ?? '-' }}</td>
                                <td class="p-3">{{ \Carbon\Carbon::parse($c->start_date)->format('d/m/Y') }}</td>
                                <td class="p-3">{{ \Carbon\Carbon::parse($c->end_date)->format('d/m/Y') }}</td>
                                <td class="p-3 text-right font-bold">Rp {{ number_format($c->contract_value, 0, ',', '.') }}</td>
                                <td class="p-3 text-right">{{ number_format($c->fee_percentage, 1) }}%</td>
                                <td class="p-3 text-right font-bold text-emerald-600">Rp {{ number_format($c->fee_amount, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="p-6 text-center text-slate-400">Tidak ada data kontrak pada periode ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            @elseif($type === 'procurement')
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-100 text-slate-700 font-semibold border-b">
                            <th class="p-3">No. Pengadaan</th>
                            <th class="p-3">Supplier</th>
                            <th class="p-3">Tanggal</th>
                            <th class="p-3 text-right">Total Biaya</th>
                            <th class="p-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($data as $prc)
                            <tr>
                                <td class="p-3 font-mono font-bold text-blue-600">{{ $prc->procurement_number }}</td>
                                <td class="p-3 font-medium">{{ $prc->supplier->name ?? '-' }}</td>
                                <td class="p-3">{{ \Carbon\Carbon::parse($prc->procurement_date)->format('d/m/Y') }}</td>
                                <td class="p-3 text-right font-bold">Rp {{ number_format($prc->total_amount, 0, ',', '.') }}</td>
                                <td class="p-3"><span class="px-2 py-0.5 rounded font-semibold bg-emerald-100 text-emerald-800">{{ $prc->status }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="p-6 text-center text-slate-400">Tidak ada data pengadaan pada periode ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            @elseif($type === 'sales')
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-100 text-slate-700 font-semibold border-b">
                            <th class="p-3">No. Penjualan</th>
                            <th class="p-3">Pelanggan</th>
                            <th class="p-3">Tanggal Penjualan</th>
                            <th class="p-3 text-right">Total Penjualan</th>
                            <th class="p-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($data as $s)
                            <tr>
                                <td class="p-3 font-mono font-bold text-blue-600">{{ $s->sale_number }}</td>
                                <td class="p-3 font-medium">{{ $s->customer_name }}</td>
                                <td class="p-3">{{ \Carbon\Carbon::parse($s->sale_date)->format('d/m/Y') }}</td>
                                <td class="p-3 text-right font-bold text-emerald-600">Rp {{ number_format($s->total_amount, 0, ',', '.') }}</td>
                                <td class="p-3"><span class="px-2 py-0.5 rounded font-semibold bg-emerald-100 text-emerald-800">{{ $s->status }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="p-6 text-center text-slate-400">Tidak ada data penjualan pada periode ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            @elseif($type === 'stock')
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-100 text-slate-700 font-semibold border-b">
                            <th class="p-3">SKU</th>
                            <th class="p-3">Nama Produk</th>
                            <th class="p-3">Kategori</th>
                            <th class="p-3 text-right">Harga Beli</th>
                            <th class="p-3 text-right">Harga Jual</th>
                            <th class="p-3 text-right">Stok Terkini</th>
                            <th class="p-3">Status stok</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($data as $p)
                            <tr>
                                <td class="p-3 font-mono font-bold text-blue-600">{{ $p->sku }}</td>
                                <td class="p-3 font-medium">{{ $p->name }}</td>
                                <td class="p-3">{{ $p->category ?? 'Umum' }}</td>
                                <td class="p-3 text-right">Rp {{ number_format($p->purchase_price, 0, ',', '.') }}</td>
                                <td class="p-3 text-right font-bold">Rp {{ number_format($p->selling_price, 0, ',', '.') }}</td>
                                <td class="p-3 text-right font-bold">{{ number_format($p->stock, 0) }} {{ $p->unit }}</td>
                                <td class="p-3">
                                    @if($p->isLowStock())
                                        <span class="px-2 py-0.5 rounded font-bold bg-rose-100 text-rose-700">Stok Menipis</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded font-semibold bg-emerald-100 text-emerald-800">Aman</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="p-6 text-center text-slate-400">Tidak ada data produk.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            @elseif($type === 'invoice')
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-100 text-slate-700 font-semibold border-b">
                            <th class="p-3">No. Invoice</th>
                            <th class="p-3">Tanggal Tagihan</th>
                            <th class="p-3">Jatuh Tempo</th>
                            <th class="p-3 text-right">Total Tagihan</th>
                            <th class="p-3 text-right">Dibayar</th>
                            <th class="p-3 text-right">Sisa Piutang</th>
                            <th class="p-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($data as $inv)
                            <tr>
                                <td class="p-3 font-mono font-bold text-blue-600">{{ $inv->invoice_number }}</td>
                                <td class="p-3">{{ \Carbon\Carbon::parse($inv->invoice_date)->format('d/m/Y') }}</td>
                                <td class="p-3 text-rose-600 font-semibold">{{ \Carbon\Carbon::parse($inv->due_date)->format('d/m/Y') }}</td>
                                <td class="p-3 text-right font-bold">Rp {{ number_format($inv->total_amount, 0, ',', '.') }}</td>
                                <td class="p-3 text-right text-emerald-600 font-semibold">Rp {{ number_format($inv->paid_amount, 0, ',', '.') }}</td>
                                <td class="p-3 text-right font-bold text-rose-600">Rp {{ number_format($inv->remaining_balance, 0, ',', '.') }}</td>
                                <td class="p-3"><span class="px-2 py-0.5 rounded font-semibold bg-slate-100 text-slate-700">{{ $inv->status }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="p-6 text-center text-slate-400">Tidak ada data invoice pada periode ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            @elseif($type === 'payment')
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-100 text-slate-700 font-semibold border-b">
                            <th class="p-3">Tanggal Bayar</th>
                            <th class="p-3">No. Invoice</th>
                            <th class="p-3">Metode Bayar</th>
                            <th class="p-3">No. Referensi</th>
                            <th class="p-3 text-right">Jumlah Dibayar</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($data as $pm)
                            <tr>
                                <td class="p-3 font-medium">{{ \Carbon\Carbon::parse($pm->payment_date)->format('d/m/Y') }}</td>
                                <td class="p-3 font-mono font-bold text-blue-600">{{ $pm->invoice->invoice_number ?? '-' }}</td>
                                <td class="p-3 font-semibold">{{ $pm->payment_method }}</td>
                                <td class="p-3 font-mono text-slate-500">{{ $pm->reference_number ?? '-' }}</td>
                                <td class="p-3 text-right font-bold text-emerald-600">Rp {{ number_format($pm->amount, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="p-6 text-center text-slate-400">Tidak ada data pembayaran pada periode ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            @endif
        </div>

    </div>

</div>
@endsection
