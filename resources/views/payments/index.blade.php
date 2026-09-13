@extends('layouts.app')

@section('title', 'Riwayat Pembayaran')
@section('header-title', 'Pencatatan & Audit Pembayaran Invoice')

@section('content')
<div class="space-y-6" x-data="{ payModal: false }">

    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex flex-col md:flex-row justify-between items-center gap-4">
        <h3 class="font-bold text-slate-800 text-base">Riwayat Transaksi Pembayaran</h3>

        <button @click="payModal = true" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm rounded-xl shadow transition flex items-center gap-2">
            <i class="fa-solid fa-plus"></i> Catat Pembayaran Baru
        </button>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-200">
                        <th class="p-4">Tanggal Pembayaran</th>
                        <th class="p-4">No. Invoice Tagihan</th>
                        <th class="p-4">Metode Bayar</th>
                        <th class="p-4">No. Referensi</th>
                        <th class="p-4">Jumlah Pembayaran</th>
                        <th class="p-4">Dicatat Oleh</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($payments as $pm)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="p-4 text-slate-700 font-medium">{{ \Carbon\Carbon::parse($pm->payment_date)->format('d M Y') }}</td>
                            <td class="p-4 font-mono font-bold text-blue-600">
                                <a href="{{ route('invoices.show', $pm->invoice_id) }}" class="hover:underline">{{ $pm->invoice->invoice_number }}</a>
                            </td>
                            <td class="p-4 text-xs font-semibold text-slate-700">{{ $pm->payment_method }}</td>
                            <td class="p-4 font-mono text-xs text-slate-500">{{ $pm->reference_number ?? '-' }}</td>
                            <td class="p-4 font-bold text-emerald-600">Rp {{ number_format($pm->amount, 0, ',', '.') }}</td>
                            <td class="p-4 text-xs text-slate-500">{{ $pm->creator->name ?? 'Finance' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-400">Belum ada riwayat pembayaran.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100">
            {{ $payments->links() }}
        </div>
    </div>

    <!-- Modal Create Payment -->
    <div x-show="payModal" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4" x-cloak>
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
            <div class="flex justify-between items-center border-b pb-3">
                <h3 class="font-bold text-lg text-slate-800">Catat Pembayaran Masuk</h3>
                <button @click="payModal = false" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>

            <form action="{{ route('payments.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Pilih Invoice Belum Lunas</label>
                    <select name="invoice_id" required class="w-full px-3 py-2 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500">
                        @foreach($unpaidInvoices as $inv)
                            <option value="{{ $inv->id }}">
                                {{ $inv->invoice_number }} (Sisa: Rp {{ number_format($inv->remaining_balance, 0) }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Tanggal Bayar</label>
                    <input type="date" name="payment_date" required value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Jumlah Pembayaran (Rp)</label>
                    <input type="number" name="amount" required placeholder="10000000" class="w-full px-3 py-2 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Metode Pembayaran</label>
                    <select name="payment_method" required class="w-full px-3 py-2 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="Transfer Bank BCA">Transfer Bank BCA</option>
                        <option value="Transfer Bank Mandiri">Transfer Bank Mandiri</option>
                        <option value="Cek / Giro">Cek / Giro</option>
                        <option value="Tunai / Kas">Tunai / Kas</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Nomor Referensi Transaksi</label>
                    <input type="text" name="reference_number" placeholder="TRX-BCA-987123..." class="w-full px-3 py-2 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t">
                    <button type="button" @click="payModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm rounded-xl">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm rounded-xl shadow">Simpan Pembayaran</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
