@extends('layouts.app')

@section('title', 'Invoice & Tagihan')
@section('header-title', 'Manajemen Invoice & Piutang Perusahaan')

@section('content')
<div class="space-y-6" x-data="{ 
    payModal: false, 
    deleteModal: false, 
    selectedInvoice: null, 
    deletionReason: '', 
    openDeleteModal(inv) { 
        this.selectedInvoice = inv; 
        this.deletionReason = ''; 
        this.deleteModal = true; 
    } 
}">

    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex flex-col md:flex-row justify-between items-center gap-4">
        <form method="GET" action="{{ route('invoices.index') }}" class="flex items-center gap-3 w-full md:w-auto">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nomor invoice..." class="px-4 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500 outline-none w-64">
            
            <select name="status" onchange="this.form.submit()" class="px-4 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                <option value="">Semua Status Invoice</option>
                <option value="Issued" {{ request('status') == 'Issued' ? 'selected' : '' }}>Issued (Belum Bayar)</option>
                <option value="Partial" {{ request('status') == 'Partial' ? 'selected' : '' }}>Partial (Bayar Sebagian)</option>
                <option value="Paid" {{ request('status') == 'Paid' ? 'selected' : '' }}>Paid (Lunas)</option>
                <option value="Overdue" {{ request('status') == 'Overdue' ? 'selected' : '' }}>Overdue (Jatuh Tempo)</option>
            </select>

            <button type="submit" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm rounded-xl">Filter</button>
        </form>

        <a href="{{ route('payments.index') }}" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-xl shadow transition flex items-center gap-2">
            <i class="fa-solid fa-money-bill-transfer"></i> Kelola Pembayaran
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-200 text-xs">
                        <th class="p-4 whitespace-nowrap">No. Invoice</th>
                        <th class="p-4">Sumber Transaksi</th>
                        <th class="p-4 whitespace-nowrap">Tanggal & Due Date</th>
                        <th class="p-4 whitespace-nowrap">Total Tagihan</th>
                        <th class="p-4 whitespace-nowrap">Telah Dibayar</th>
                        <th class="p-4 whitespace-nowrap">Sisa Piutang</th>
                        <th class="p-4 text-center whitespace-nowrap">Status</th>
                        <th class="p-4 text-center whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($invoices as $inv)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="p-4 font-mono font-bold text-blue-600 whitespace-nowrap">
                                <a href="{{ route('invoices.show', $inv->id) }}" class="hover:underline">{{ $inv->invoice_number }}</a>
                            </td>
                            <td class="p-4 text-xs">
                                @if($inv->serviceJob)
                                    <div class="font-bold text-slate-800">Tagihan Jasa: {{ $inv->serviceJob->name }}</div>
                                    <div class="text-slate-500">Klien: {{ $inv->serviceJob->contract->client->name ?? '-' }}</div>
                                @elseif($inv->sale)
                                    <div class="font-bold text-slate-800">Penjualan: {{ $inv->sale->sale_number }}</div>
                                    <div class="text-slate-500">Pelanggan: {{ $inv->sale->customer_name }}</div>
                                @else
                                    <div class="text-slate-400">Tagihan Umum</div>
                                @endif
                            </td>
                            <td class="p-4 text-xs text-slate-600 whitespace-nowrap">
                                <div>Tgl: {{ \Carbon\Carbon::parse($inv->invoice_date)->format('d M Y') }}</div>
                                <div class="font-semibold text-rose-600">Due: {{ \Carbon\Carbon::parse($inv->due_date)->format('d M Y') }}</div>
                            </td>
                            <td class="p-4 font-bold text-slate-800 whitespace-nowrap">Rp {{ number_format($inv->total_amount, 0, ',', '.') }}</td>
                            <td class="p-4 text-emerald-600 font-semibold whitespace-nowrap">Rp {{ number_format($inv->paid_amount, 0, ',', '.') }}</td>
                            <td class="p-4 font-bold text-rose-600 whitespace-nowrap">
                                Rp {{ number_format($inv->remaining_balance, 0, ',', '.') }}
                            </td>
                            <td class="p-4 text-center whitespace-nowrap">
                                @php
                                    $statusConfig = match($inv->status) {
                                        'Paid' => ['bg' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'dot' => 'bg-emerald-500', 'label' => 'Lunas'],
                                        'Partial' => ['bg' => 'bg-amber-50 text-amber-700 border-amber-200', 'dot' => 'bg-amber-500', 'label' => 'Sebagian'],
                                        'Issued' => ['bg' => 'bg-blue-50 text-blue-700 border-blue-200', 'dot' => 'bg-blue-500 animate-pulse', 'label' => 'Belum Lunas'],
                                        'Overdue' => ['bg' => 'bg-rose-50 text-rose-700 border-rose-200 font-bold', 'dot' => 'bg-rose-500 animate-pulse', 'label' => 'Jatuh Tempo'],
                                        default => ['bg' => 'bg-slate-50 text-slate-700 border-slate-200', 'dot' => 'bg-slate-400', 'label' => $inv->status]
                                    };
                                @endphp
                                <span class="inline-flex items-center justify-center gap-1.5 w-28 py-1 rounded-full text-xs font-semibold border {{ $statusConfig['bg'] }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $statusConfig['dot'] }}"></span>
                                    {{ $statusConfig['label'] }}
                                </span>
                            </td>
                            <td class="p-4 text-center whitespace-nowrap">
                                <div class="inline-flex items-center justify-center gap-1.5">
                                    @if($inv->status !== 'Paid')
                                        <button @click="selectedInvoice = {{ $inv }}; payModal = true" class="px-2.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs rounded-lg shadow-xs transition inline-flex items-center justify-center gap-1.5" title="Catat Pembayaran">
                                            <i class="fa-solid fa-hand-holding-dollar text-[11px]"></i> Bayar
                                        </button>
                                    @endif
                                    <a href="{{ route('invoices.show', $inv->id) }}" class="px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-lg border border-slate-200 transition inline-flex items-center gap-1.5" title="Lihat Detail Invoice">
                                        <i class="fa-solid fa-file-invoice text-[11px]"></i> Detail
                                    </a>
                                    @if(auth()->user()->isManager() || auth()->user()->isOwner())
                                        <button type="button" @click="openDeleteModal({{ $inv }})" class="px-2.5 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 font-semibold text-xs rounded-lg border border-rose-200 transition inline-flex items-center gap-1" title="Hapus Invoice (Manager / Owner)">
                                            <i class="fa-solid fa-trash-can text-[11px]"></i> Hapus
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-slate-400">Belum ada invoice/tagihan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100">
            {{ $invoices->links() }}
        </div>
    </div>

    <!-- Modal Record Payment -->
    <div x-show="payModal" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4" x-cloak>
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
            <div class="flex justify-between items-center border-b pb-3">
                <h3 class="font-bold text-lg text-slate-800">Catat Pembayaran Tagihan</h3>
                <button @click="payModal = false" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>

            <form action="{{ route('payments.store') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="invoice_id" x-bind:value="selectedInvoice ? selectedInvoice.id : ''">

                <div class="bg-slate-50 p-3 rounded-xl border border-slate-200 text-xs space-y-1">
                    <div class="text-slate-500">Invoice: <span class="font-bold text-slate-800" x-text="selectedInvoice ? selectedInvoice.invoice_number : ''"></span></div>
                    <div class="text-slate-500">Total Tagihan: <span class="font-bold text-slate-800" x-text="selectedInvoice ? 'Rp ' + Number(selectedInvoice.total_amount).toLocaleString('id-ID') : ''"></span></div>
                    <div class="text-rose-600 font-bold">Sisa Piutang: <span x-text="selectedInvoice ? 'Rp ' + (selectedInvoice.total_amount - selectedInvoice.paid_amount).toLocaleString('id-ID') : ''"></span></div>
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

    <!-- Modal Verifikasi Hapus Invoice (Manager & Owner) -->
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
                    <span class="font-mono font-bold text-slate-800" x-text="selectedInvoice ? selectedInvoice.invoice_number : ''"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Total Tagihan:</span>
                    <span class="font-bold text-slate-800" x-text="selectedInvoice ? 'Rp ' + Number(selectedInvoice.total_amount).toLocaleString('id-ID') : ''"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Status Pembayaran:</span>
                    <span class="font-semibold text-slate-700" x-text="selectedInvoice ? selectedInvoice.status : ''"></span>
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

            <form x-bind:action="selectedInvoice ? ('/invoices/' + selectedInvoice.id) : '#'" method="POST" class="space-y-3.5">
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
