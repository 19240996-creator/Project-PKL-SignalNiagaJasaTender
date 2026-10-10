@extends('layouts.app')

@section('title', 'Log Aktivitas')
@section('header-title', 'Log Aktivitas Sistem')

@section('content')
<div class="space-y-5" x-data="activityLog(@js($logs), @js($pagination), @js($checkedAt))" x-init="startPolling()" x-cloak>
    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <div>
            <h2 class="text-base font-bold text-slate-900 tracking-tight">Audit Log Aktivitas Sistem</h2>
            <div class="mt-1 flex items-center gap-2 text-xs text-slate-500">
                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500" :class="loading ? 'animate-pulse' : ''"></span>
                <span x-text="loading ? 'Memperbarui data...' : 'Terhubung · diperbarui ' + lastChecked"></span>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <select x-model="filter" @change="goToPage(1)" class="rounded-md border border-slate-200 bg-white px-2.5 py-1.5 text-xs text-slate-700 outline-none focus:ring-2 focus:ring-blue-500">
                <option value="all">Semua Aktivitas</option>
                <option value="create">Penambahan</option>
                <option value="update">Perubahan</option>
                <option value="delete">Penghapusan</option>
            </select>
            <button type="button" @click="refresh()" class="rounded-md bg-blue-600 px-3.5 py-1.5 text-xs font-medium text-white transition hover:bg-blue-700 shadow-xs flex items-center gap-1.5">
                <i class="fa-solid fa-rotate-right text-[11px]" :class="loading ? 'animate-spin' : ''"></i> Perbarui
            </button>
        </div>
    </div>

    <div class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[850px] text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50 text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                        <th class="p-3.5">Waktu</th>
                        <th class="p-3.5">Pengguna</th>
                        <th class="p-3.5">Aktivitas</th>
                        <th class="p-3.5">Modul</th>
                        <th class="p-3.5">Detail</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <template x-for="log in logs" :key="log.id">
                        <tr class="transition hover:bg-slate-50/70">
                            <td class="whitespace-nowrap p-3.5 align-top">
                                <div class="font-medium text-slate-900 font-mono" x-text="log.time"></div>
                                <div class="text-[11px] text-slate-400" x-text="log.relative_time"></div>
                            </td>
                            <td class="p-3.5 align-top">
                                <div class="font-semibold text-slate-800" x-text="log.user"></div>
                                <div class="text-[11px] uppercase text-slate-400" x-text="formatRole(log.role)"></div>
                            </td>
                            <td class="p-3.5 align-top">
                                <span class="inline-flex items-center gap-1.5 rounded px-2 py-0.5 text-xs font-medium border" :class="actionClass(log.action)">
                                    <span class="w-1.5 h-1.5 rounded-full" :class="actionDot(log.action)"></span>
                                    <span x-text="actionLabel(log.action)"></span>
                                </span>
                            </td>
                            <td class="p-3.5 align-top">
                                <div class="font-mono text-xs text-slate-700" x-text="log.resource"></div>
                                <div class="text-[11px] text-slate-400" x-text="log.record_id ? 'ID: ' + log.record_id : '-'"></div>
                            </td>
                            <td class="max-w-sm p-3.5 align-top">
                                <template x-if="log.action === 'delete' && log.old_values">
                                    <div>
                                        <div class="font-medium text-slate-800" x-text="log.old_values.name || log.old_values.sku || 'Data dihapus'"></div>
                                        <div class="truncate text-[11px] text-slate-500" x-text="summary(log.old_values)"></div>
                                    </div>
                                </template>
                                <template x-if="log.action === 'update' && log.old_values && log.new_values">
                                    <div class="space-y-1">
                                        <template x-for="(val, key) in log.new_values" :key="key">
                                            <div x-show="log.old_values[key] !== undefined && String(log.old_values[key]) !== String(val)" class="text-xs">
                                                <span class="font-medium text-slate-600" x-text="formatKey(key) + ':'"></span>
                                                <span class="text-rose-500 line-through mr-1" x-text="formatValue(key, log.old_values[key])"></span>
                                                <i class="fa-solid fa-arrow-right text-[10px] text-slate-400 mx-0.5"></i>
                                                <span class="text-emerald-600 font-semibold" x-text="formatValue(key, val)"></span>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                                <template x-if="log.action !== 'delete' && !(log.action === 'update' && log.old_values) && log.new_values">
                                    <div class="truncate text-xs text-slate-600" x-text="summary(log.new_values)"></div>
                                </template>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="logs.length === 0">
                        <td colspan="5" class="p-8 text-center text-slate-400 text-xs">Belum ada aktivitas yang sesuai.</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="flex flex-col gap-2 border-t border-slate-100 bg-slate-50 px-3.5 py-2.5 text-xs text-slate-500 sm:flex-row sm:items-center sm:justify-between">
            <span x-text="`Menampilkan ${logs.length ? ((pagination.current_page - 1) * pagination.per_page) + 1 : 0}-${Math.min(pagination.current_page * pagination.per_page, pagination.total)} dari ${pagination.total} aktivitas. Data diperiksa otomatis setiap 5 detik.`"></span>
            <div class="flex items-center gap-1" x-show="pagination.last_page > 1">
                <button type="button" @click="goToPage(pagination.current_page - 1)" :disabled="pagination.current_page === 1" class="rounded border border-slate-200 bg-white px-2 py-1 hover:bg-slate-100 disabled:cursor-not-allowed disabled:opacity-40" aria-label="Halaman sebelumnya">&laquo;</button>
                <span class="px-2" x-text="`${pagination.current_page} / ${pagination.last_page}`"></span>
                <button type="button" @click="goToPage(pagination.current_page + 1)" :disabled="pagination.current_page === pagination.last_page" class="rounded border border-slate-200 bg-white px-2 py-1 hover:bg-slate-100 disabled:cursor-not-allowed disabled:opacity-40" aria-label="Halaman berikutnya">&raquo;</button>
            </div>
        </div>
    </div>
</div>

<script>
    function activityLog(initialLogs, initialPagination, initialCheckedAt) {
        return {
            logs: initialLogs,
            pagination: initialPagination,
            filter: 'all',
            loading: false,
            lastChecked: initialCheckedAt,
            timer: null,
            startPolling() {
                this.timer = setInterval(() => this.refresh(), 5000);
            },
            goToPage(page) {
                if (page < 1 || page > this.pagination.last_page) return;
                this.refresh(page);
            },
            async refresh(page = this.pagination.current_page) {
                if (this.loading) return;
                this.loading = true;
                try {
                    const params = new URLSearchParams({ page, action: this.filter });
                    const response = await fetch('{{ route('activity-logs.data') }}?' + params, {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    });
                    if (!response.ok) throw new Error('Gagal mengambil log aktivitas.');
                    const data = await response.json();
                    this.logs = data.logs;
                    this.pagination = data.pagination;
                    this.lastChecked = data.checked_at;
                } finally {
                    this.loading = false;
                }
            },
            actionLabel(action) {
                return { create: 'Penambahan', update: 'Perubahan', delete: 'Penghapusan' }[action] || action;
            },
            actionDot(action) {
                return {
                    create: 'bg-emerald-500',
                    update: 'bg-blue-500',
                    delete: 'bg-rose-500',
                }[action] || 'bg-slate-400';
            },
            actionClass(action) {
                return {
                    create: 'bg-emerald-50 text-emerald-700 border-emerald-200',
                    update: 'bg-blue-50 text-blue-700 border-blue-200',
                    delete: 'bg-rose-50 text-rose-700 border-rose-200',
                }[action] || 'bg-slate-100 text-slate-600 border-slate-200';
            },
            formatRole(role) {
                return role.replaceAll('_', ' ');
            },
            formatKey(key) {
                const map = {
                    name: 'Nama Produk',
                    purchase_price: 'Harga Beli',
                    selling_price: 'Harga Jual',
                };
                return map[key] || key.replaceAll('_', ' ');
            },
            formatValue(key, val) {
                if (val === null || val === undefined) return '-';
                if (key.includes('price') || key.includes('amount') || key.includes('nominal')) {
                    const num = Number(val);
                    if (!isNaN(num)) {
                        return 'Rp ' + num.toLocaleString('id-ID');
                    }
                }
                return val;
            },
            summary(values) {
                return Object.entries(values).filter(([key]) => !['created_at', 'updated_at'].includes(key)).map(([key, value]) => `${key}: ${value ?? '-'}`).join(' · ');
            },
        };
    }
</script>
@endsection
