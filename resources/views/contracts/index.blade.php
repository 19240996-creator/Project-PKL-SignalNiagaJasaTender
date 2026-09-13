@extends('layouts.app')

@section('title', 'Kontrak Jasa')
@section('header-title', 'Manajemen Kontrak & Komisi Fee Jasa')

@section('content')
<div class="space-y-6" x-data="{ createModal: false }">

    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex flex-col md:flex-row justify-between items-center gap-4">
        <form method="GET" action="{{ route('contracts.index') }}" class="flex items-center gap-3 w-full md:w-auto">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nomor kontrak / klien..." class="px-4 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500 outline-none w-64">
            <button type="submit" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm rounded-xl">Filter</button>
        </form>

        <button @click="createModal = true" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-xl shadow transition flex items-center gap-2">
            <i class="fa-solid fa-plus"></i> Buat Kontrak Manual
        </button>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-200">
                        <th class="p-4">No. Kontrak</th>
                        <th class="p-4">Klien & Tender asal</th>
                        <th class="p-4">Periode Kontrak</th>
                        <th class="p-4">Nilai Kontrak</th>
                        <th class="p-4">Komisi Fee</th>
                        <th class="p-4">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($contracts as $ctr)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="p-4 font-mono font-bold text-blue-600">{{ $ctr->contract_number }}</td>
                            <td class="p-4">
                                <div class="font-bold text-slate-800">{{ $ctr->client->name }}</div>
                                <div class="text-xs text-slate-500">
                                    {{ $ctr->tender ? 'Tender: ' . $ctr->tender->tender_number : 'Kontrak Langsung' }}
                                </div>
                            </td>
                            <td class="p-4 text-xs text-slate-600">
                                <div>{{ \Carbon\Carbon::parse($ctr->start_date)->format('d M Y') }} s/d</div>
                                <div class="font-semibold text-slate-800">{{ \Carbon\Carbon::parse($ctr->end_date)->format('d M Y') }}</div>
                            </td>
                            <td class="p-4 font-bold text-slate-800">
                                Rp {{ number_format($ctr->contract_value, 0, ',', '.') }}
                            </td>
                            <td class="p-4">
                                <div class="font-bold text-emerald-600">Rp {{ number_format($ctr->fee_amount, 0, ',', '.') }}</div>
                                <div class="text-[11px] text-slate-400">({{ number_format($ctr->fee_percentage, 1) }}%)</div>
                            </td>
                            <td class="p-4">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $ctr->status === 'Aktif' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                                    {{ $ctr->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-400">Belum ada data kontrak.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100">
            {{ $contracts->links() }}
        </div>
    </div>

    <!-- Modal Create Contract -->
    <div x-show="createModal" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4" x-cloak>
        <div class="bg-white rounded-2xl max-w-xl w-full p-6 shadow-2xl space-y-4">
            <div class="flex justify-between items-center border-b pb-3">
                <h3 class="font-bold text-lg text-slate-800">Buat Kontrak Jasa Baru</h3>
                <button @click="createModal = false" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>

            <form action="{{ route('contracts.store') }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Nomor Kontrak</label>
                        <input type="text" name="contract_number" required value="CTR-{{ date('Ymd') }}-{{ rand(100,999) }}" class="w-full px-3 py-2 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Pilih Klien</label>
                        <select name="client_id" required class="w-full px-3 py-2 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500">
                            @foreach($clients as $c)
                                <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->company_name ?? '-' }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Tanggal Mulai</label>
                        <input type="date" name="start_date" required value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Tanggal Berakhir</label>
                        <input type="date" name="end_date" required value="{{ date('Y-m-d', strtotime('+1 year')) }}" class="w-full px-3 py-2 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Nilai Kontrak (Rp)</label>
                        <input type="number" name="contract_value" required placeholder="100000000" class="w-full px-3 py-2 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Komisi Fee (%)</label>
                        <input type="number" step="0.1" name="fee_percentage" required value="5" class="w-full px-3 py-2 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <input type="hidden" name="status" value="Aktif">

                <div class="flex justify-end gap-2 pt-3 border-t">
                    <button type="button" @click="createModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm rounded-xl">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-xl shadow">Simpan Kontrak</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
