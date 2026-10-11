@extends('layouts.app')

@section('title', 'Detail Invoice: ' . $invoice->invoice_number)
@section('header-title', 'Rincian Invoice & Bukti Tagihan')

@section('content')
<div class="space-y-6 max-w-4xl mx-auto" x-data="{ payModal: false, deleteModal: false, deletionReason: '' }">

    <div class="flex flex-col sm:flex-row justify-between items-center gap-3 bg-white p-5 rounded-2xl border border-slate-200 shadow-sm print:hidden">
        <a href="{{ route('invoices.index') }}" class="text-xs font-semibold text-slate-600 hover:text-blue-600 flex items-center gap-2">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar Invoice
        </a>
        <div class="flex items-center gap-2.5">
            <button onclick="window.print()" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl transition flex items-center gap-1.5 border border-slate-200">
                <i class="fa-solid fa-print"></i> Cetak Invoice
            </button>
            @if($invoice->status !== 'Paid' && $invoice->remaining_balance > 0)
                <button type="button" @click="payModal = true" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs rounded-xl shadow-xs transition flex items-center gap-1.5">
                    <i class="fa-solid fa-hand-holding-dollar text-sm"></i> Catat Pembayaran / Pelunasan
                </button>
            @else
                <span class="px-3.5 py-2 bg-emerald-50 text-emerald-700 font-semibold text-xs rounded-xl border border-emerald-200 flex items-center gap-1.5">
                    <i class="fa-solid fa-circle-check text-emerald-600"></i> Tagihan Lunas (Paid)
                </span>
            @endif
            @if(auth()->user()->isManager() || auth()->user()->isOwner())
                <button type="button" @click="deleteModal = true" class="px-3.5 py-2 bg-rose-50 hover:bg-rose-100 text-rose-600 font-semibold text-xs rounded-xl border border-rose-200 transition flex items-center gap-1.5" title="Hapus Invoice (Manager / Owner)">
                    <i class="fa-solid fa-trash-can text-xs"></i> Hapus Invoice
                </button>
            @endif
        </div>
    </div>

    <!-- Printable Invoice Sheet -->
    <div class="bg-white rounded-2xl p-8 border border-slate-200 shadow-lg space-y-8 print:shadow-none print:border-none">
        
        <!-- Header -->
        <div class="flex justify-between items-start border-b border-slate-200 pb-6">
            <div>
                <div class="flex items-center space-x-3 mb-2">
                    <img src="{{ asset('images/logo-icon.png') }}" alt="PT Signal Panca Utama" class="h-9 w-auto object-contain">
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
            <div class="flex items-center justify-between mb-3">
                <h4 class="font-bold text-slate-800 text-xs uppercase tracking-wider">Riwayat Pembayaran Diterima</h4>
                @if($invoice->status !== 'Paid' && $invoice->remaining_balance > 0)
                    <button type="button" @click="payModal = true" class="text-xs font-semibold text-emerald-700 hover:text-emerald-800 flex items-center gap-1 print:hidden">
                        <i class="fa-solid fa-plus text-[10px]"></i> Tambah Pembayaran
                    </button>
                @endif
            </div>
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

    <!-- Modal Catat Pembayaran Invoice -->
    <div x-show="payModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs" x-cloak>
        <div class="w-full max-w-md overflow-hidden rounded-lg bg-white border border-slate-200 shadow-xl p-5 space-y-4" @click.away="payModal = false">
            <div class="flex items-start justify-between border-b border-slate-200 pb-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-600 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-money-bill-transfer text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 leading-tight">Catat Pembayaran Invoice</h3>
                        <p class="text-[11px] text-emerald-700 font-medium mt-0.5">Penerimaan & Pelunasan Piutang</p>
                    </div>
                </div>
                <button type="button" @click="payModal = false" class="text-slate-400 hover:text-slate-600 transition">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <!-- Detail Invoice -->
            <div class="bg-slate-50 rounded-lg p-3 border border-slate-200 text-xs space-y-1.5">
                <div class="flex items-center justify-between">
                    <span class="text-slate-500">No. Invoice:</span>
                    <span class="font-mono font-bold text-slate-900">{{ $invoice->invoice_number }}</span>
                </div>
                <div class="flex items-start justify-between gap-2">
                    <span class="text-slate-500 shrink-0">Ditagihkan ke:</span>
                    <span class="font-semibold text-slate-800 text-right">
                        @if($invoice->serviceJob)
                            {{ $invoice->serviceJob->contract->client->name ?? 'Klien Jasa' }}
                        @elseif($invoice->sale)
                            {{ $invoice->sale->customer_name }}
                        @else
                            Klien Umum
                        @endif
                    </span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-500">Total Tagihan:</span>
                    <span class="font-mono font-semibold text-slate-900">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between pt-1 border-t border-slate-200">
                    <span class="text-slate-600 font-semibold">Sisa Piutang:</span>
                    <span class="font-mono font-bold text-rose-600">Rp {{ number_format($invoice->remaining_balance, 0, ',', '.') }}</span>
                </div>
            </div>

            <form action="{{ route('payments.store') }}" method="POST" class="space-y-3.5" x-data="{
                payAmount: {{ (float) $invoice->remaining_balance }},
                maxAmount: {{ (float) $invoice->remaining_balance }},
                setFullPay() {
                    this.payAmount = this.maxAmount;
                }
            }">
                @csrf
                <input type="hidden" name="invoice_id" value="{{ $invoice->id }}">

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                        Tanggal Pembayaran <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" name="payment_date" required value="{{ date('Y-m-d') }}" class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-emerald-500">
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">
                            Jumlah Pembayaran (Rp) <span class="text-rose-500">*</span>
                        </label>
                        <button type="button" @click="setFullPay()" class="text-[11px] font-semibold text-emerald-700 hover:text-emerald-800 underline">
                            Lunasi Penuh
                        </button>
                    </div>
                    <input type="number" name="amount" x-model="payAmount" required min="1" max="{{ (float) $invoice->remaining_balance }}" step="1" class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-emerald-500 font-mono font-bold">
                    <p class="text-[10px] text-slate-400 mt-0.5">Maksimal Rp {{ number_format($invoice->remaining_balance, 0, ',', '.') }}</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                        Metode Pembayaran <span class="text-rose-500">*</span>
                    </label>
                    <select name="payment_method" required class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-emerald-500 bg-white">
                        <option value="Transfer Bank BCA">Transfer Bank BCA</option>
                        <option value="Transfer Bank Mandiri">Transfer Bank Mandiri</option>
                        <option value="Transfer Bank BRI">Transfer Bank BRI</option>
                        <option value="Transfer Bank BNI">Transfer Bank BNI</option>
                        <option value="Tunai / Kas Langsung">Tunai / Kas Langsung</option>
                        <option value="Cek / Bilyet Giro">Cek / Bilyet Giro</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                        Nomor Referensi Transaksi
                    </label>
                    <input type="text" name="reference_number" placeholder="Contoh: TRX-BCA-20261011-001..." class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                        Catatan Pembayaran (Opsional)
                    </label>
                    <textarea name="notes" rows="2" placeholder="Catatan tambahan pembayaran jika ada..." class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-200">
                    <button type="button" @click="payModal = false" class="px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium text-xs rounded-md transition">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs rounded-md transition flex items-center gap-1.5 shadow-xs">
                        <i class="fa-solid fa-check text-[11px]"></i> Simpan Pembayaran
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Verifikasi Hapus Invoice (Manager & Owner) -->
    <div x-show="deleteModal" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs print:hidden" 
         @keydown.escape.window="deleteModal = false">
        <div class="w-full max-w-md overflow-hidden rounded-lg bg-white border border-slate-200 shadow-xl p-5 space-y-4"
             @click.away="deleteModal = false">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-rose-50 border border-rose-200 text-rose-600 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-triangle-exclamation text-xs"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Verifikasi Hapus Invoice</h3>
                        <p class="text-[11px] text-slate-500">Tercatat di Log Aktivitas Owner</p>
                    </div>
                </div>
                <button type="button" @click="deleteModal = false" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <!-- Detail Ringkasan Invoice -->
            <div class="bg-slate-50 border border-slate-200 rounded-lg p-3 text-xs space-y-1.5">
                <div class="flex justify-between">
                    <span class="text-slate-500">Nomor Invoice:</span>
                    <span class="font-mono font-bold text-slate-800">{{ $invoice->invoice_number }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Total Tagihan:</span>
                    <span class="font-bold text-slate-800">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Status Pembayaran:</span>
                    <span class="font-semibold text-slate-700">{{ $invoice->status }}</span>
                </div>
            </div>

            <!-- Peringatan Audit SOP -->
            <div class="bg-rose-50/70 border border-rose-200 rounded-lg p-3 text-xs text-rose-900 leading-relaxed">
                <div class="flex items-start gap-2">
                    <i class="fa-solid fa-circle-info text-rose-600 text-xs mt-0.5 shrink-0"></i>
                    <div class="text-[11px]">
                        <span class="font-bold">Apakah Anda yakin ingin menghapus invoice ini?</span>
                        <p class="mt-0.5 text-rose-800/90">
                            Penghapusan oleh Manager akan dicatat secara permanen di <strong>Log Aktivitas Owner</strong> bersama identitas dan catatan alasan penghapusan.
                        </p>
                    </div>
                </div>
            </div>

            <form action="{{ route('invoices.destroy', $invoice->id) }}" method="POST" class="space-y-3.5">
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
                              placeholder="Tuliskan catatan alasan jelas kenapa invoice ini dihapus..." 
                              class="w-full px-3 py-2 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-rose-500 text-slate-800"></textarea>
                    <p class="text-[11px] text-slate-400 mt-1">Minimal 5 karakter. Wajib diisi sebagai rekaman audit.</p>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-200">
                    <button type="button" @click="deleteModal = false" class="px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium text-xs rounded-md transition">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-1.5 bg-rose-600 hover:bg-rose-700 text-white font-semibold text-xs rounded-md transition flex items-center gap-1.5 shadow-xs">
                        <i class="fa-solid fa-trash-can text-[11px]"></i> Ya, Hapus Invoice
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
