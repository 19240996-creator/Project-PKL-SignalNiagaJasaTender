@extends('layouts.app')

@section('title', 'Log Aktivitas')
@section('header-title', 'Log Aktivitas Sistem')

@section('content')
<div class="space-y-6" x-data="activityLog(@js($logs), @js($checkedAt))" x-init="startPolling()" x-cloak>
    <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div>
            <p class="text-sm text-slate-500">Pantau aktivitas pengguna dan perubahan data secara berkala.</p>
            <div class="mt-2 flex items-center gap-2 text-xs text-slate-400">
                <span class="h-2 w-2 rounded-full bg-emerald-500" :class="loading ? 'animate-pulse' : ''"></span>
                <span x-text="loading ? 'Memperbarui data...' : 'Terhubung · diperbarui ' + lastChecked"></span>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <select x-model="filter" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-600 outline-none focus:ring-2 focus:ring-blue-500">
                <option value="all">Semua aktivitas</option>
                <option value="create">Penambahan</option>
                <option value="update">Perubahan</option>
                <option value="delete">Penghapusan</option>
            </select>
            <button type="button" @click="refresh()" class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-700">
                <i class="fa-solid fa-rotate-right mr-1" :class="loading ? 'animate-spin' : ''"></i> Perbarui
            </button>
        </div>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[850px] text-left text-sm">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <th class="p-4">Waktu</th>
                        <th class="p-4">Pengguna</th>
                        <th class="p-4">Aktivitas</th>
                        <th class="p-4">Modul</th>
                        <th class="p-4">Detail</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <template x-for="log in filteredLogs" :key="log.id">
                        <tr class="transition hover:bg-slate-50">
                            <td class="whitespace-nowrap p-4 align-top">
                                <div class="font-semibold text-slate-700" x-text="log.time"></div>
                                <div class="mt-1 text-xs text-slate-400" x-text="log.relative_time"></div>
                            </td>
                            <td class="p-4 align-top">
                                <div class="font-semibold text-slate-800" x-text="log.user"></div>
                                <div class="mt-1 text-xs uppercase text-slate-400" x-text="formatRole(log.role)"></div>
                            </td>
                            <td class="p-4 align-top">
                                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold" :class="actionClass(log.action)">
                                    <i class="fa-solid" :class="actionIcon(log.action)"></i>
                                    <span x-text="actionLabel(log.action)"></span>
                                </span>
                            </td>
                            <td class="p-4 align-top">
                                <div class="font-mono text-xs text-slate-600" x-text="log.resource"></div>
                                <div class="mt-1 text-xs text-slate-400" x-text="log.record_id ? 'ID data: ' + log.record_id : 'Tanpa ID data'"></div>
                            </td>
                            <td class="max-w-sm p-4 align-top">
                                <template x-if="log.action === 'delete' && log.old_values">
                                    <div>
                                        <div class="font-medium text-slate-700" x-text="log.old_values.name || log.old_values.sku || 'Data dihapus'"></div>
                                        <div class="mt-1 truncate text-xs text-slate-400" x-text="summary(log.old_values)"></div>
                                    </div>
                                </template>
                                <template x-if="log.action !== 'delete' && log.new_values">
                                    <div class="truncate text-xs text-slate-500" x-text="summary(log.new_values)"></div>
                                </template>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="filteredLogs.length === 0">
                        <td colspan="5" class="p-12 text-center text-slate-400">Belum ada aktivitas yang sesuai.</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-100 bg-slate-50 px-4 py-3 text-xs text-slate-400">
            Menampilkan maksimal 50 aktivitas terbaru. Data baru diperiksa otomatis setiap 5 detik.
        </div>
    </div>
</div>

<script>
    function activityLog(initialLogs, initialCheckedAt) {
        return {
            logs: initialLogs,
            filter: 'all',
            loading: false,
            lastChecked: initialCheckedAt,
            timer: null,
            get filteredLogs() {
                return this.filter === 'all' ? this.logs : this.logs.filter((log) => log.action === this.filter);
            },
            startPolling() {
                this.timer = setInterval(() => this.refresh(), 5000);
            },
            async refresh() {
                if (this.loading) return;
                this.loading = true;
                try {
                    const response = await fetch('{{ route('activity-logs.data') }}', {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    });
                    if (!response.ok) throw new Error('Gagal mengambil log aktivitas.');
                    const data = await response.json();
                    this.logs = data.logs;
                    this.lastChecked = data.checked_at;
                } finally {
                    this.loading = false;
                }
            },
            actionLabel(action) {
                return { create: 'Penambahan', update: 'Perubahan', delete: 'Penghapusan' }[action] || action;
            },
            actionIcon(action) {
                return { create: 'fa-plus', update: 'fa-pen', delete: 'fa-trash-can' }[action] || 'fa-circle-info';
            },
            actionClass(action) {
                return {
                    create: 'bg-emerald-50 text-emerald-700',
                    update: 'bg-blue-50 text-blue-700',
                    delete: 'bg-rose-50 text-rose-700',
                }[action] || 'bg-slate-100 text-slate-600';
            },
            formatRole(role) {
                return role.replaceAll('_', ' ');
            },
            summary(values) {
                return Object.entries(values).filter(([key]) => !['created_at', 'updated_at'].includes(key)).map(([key, value]) => `${key}: ${value ?? '-'}`).join(' · ');
            },
        };
    }
</script>
@endsection
