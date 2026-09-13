<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class ModuleController extends Controller
{
    public function tender(): View
    {
        return $this->module('Tender', 'Pipeline tender dan dokumen penawaran', [
            ['label' => 'Tender ditemukan', 'value' => '12', 'detail' => '4 mendekati deadline'],
            ['label' => 'Dalam evaluasi', 'value' => '8', 'detail' => 'Perlu tindak lanjut'],
            ['label' => 'Tender menang', 'value' => '4', 'detail' => 'Siap dikonversi ke kontrak'],
        ], ['Nomor tender', 'Nama tender', 'Klien', 'Deadline', 'Status']);
    }

    public function jasa(): View
    {
        return $this->module('Jasa', 'Kontrak, pekerjaan, fee, dan tagihan jasa', [
            ['label' => 'Kontrak aktif', 'value' => '7', 'detail' => 'Rp 2,5 M nilai kontrak'],
            ['label' => 'Pekerjaan berjalan', 'value' => '5', 'detail' => '2 selesai bulan ini'],
            ['label' => 'Pendapatan jasa', 'value' => 'Rp 850 Jt', 'detail' => 'Periode berjalan'],
        ], ['Nomor kontrak', 'Klien', 'Pekerjaan', 'Periode', 'Status']);
    }

    public function perdagangan(): View
    {
        return $this->module('Perdagangan', 'Pengadaan, persediaan, dan penjualan barang', [
            ['label' => 'Produk aktif', 'value' => '0', 'detail' => 'Belum ada data master'],
            ['label' => 'Pengadaan bulan ini', 'value' => '0', 'detail' => 'Belum ada transaksi'],
            ['label' => 'Omzet perdagangan', 'value' => 'Rp 0', 'detail' => 'Periode berjalan'],
        ], ['SKU', 'Produk', 'Supplier', 'Stok', 'Status']);
    }

    public function finance(): View
    {
        return $this->module('Finance', 'Invoice, piutang, dan pembayaran', [
            ['label' => 'Invoice belum lunas', 'value' => '18', 'detail' => 'Rp 850 Juta outstanding'],
            ['label' => 'Jatuh tempo minggu ini', 'value' => '4', 'detail' => 'Perlu ditindaklanjuti'],
            ['label' => 'Pembayaran bulan ini', 'value' => 'Rp 0', 'detail' => 'Belum ada data transaksi'],
        ], ['Nomor invoice', 'Sumber transaksi', 'Jatuh tempo', 'Nilai', 'Status']);
    }

    public function laporan(): View
    {
        return $this->module('Laporan', 'Ringkasan laporan bisnis perusahaan', [
            ['label' => 'Laporan tender', 'value' => '0', 'detail' => 'Siap dibuat dari data transaksi'],
            ['label' => 'Laporan operasional', 'value' => '0', 'detail' => 'Siap dibuat dari data transaksi'],
            ['label' => 'Laporan keuangan', 'value' => '0', 'detail' => 'Siap dibuat dari data transaksi'],
        ], ['Jenis laporan', 'Periode', 'Dibuat oleh', 'Tanggal', 'Aksi']);
    }

    private function module(string $title, string $description, array $metrics, array $columns): View
    {
        return view('modules.index', compact('title', 'description', 'metrics', 'columns'));
    }
}
