# Product Requirements Document (PRD)
## Sistem Manajemen Operasional & Pelaporan PT Signal Panca Utama

---

## 1. Ikhtisar Produk (Product Overview)
Sistem ini adalah aplikasi web internal berbasis peran (*role-based*) yang dirancang untuk mengelola alur pencatatan, persetujuan (*approval*), dan pelaporan dari tiga domain bisnis utama di **PT Signal Panca Utama**: **Tender**, **Jasa**, dan **Barang**.

---

## 2. Hak Akses Pengguna & Workflow (User Roles & Workflows)

Sistem memiliki 3 tingkat hak akses (*Roles*):

### A. Admin
*   **Fungsi:** Menginput data transaksi atau operasional baru ke dalam sistem.
*   **Batasan:** 
    *   Data yang diinput berstatus **Pending (Menunggu Persetujuan)** secara default dan belum aktif sepenuhnya dalam laporan final.
    *   Hanya dapat melihat laporan/riwayat dari data yang ia input sendiri.

### B. Manager (Manager QC / Penanggung Jawab)
*   **Fungsi:** 
    *   Meninjau data yang diinput oleh Admin.
    *   Memberikan keputusan akhir: **Setujui (Approve)** atau **Tolak (Reject)**.
    *   Mengeksekusi atau memutuskan status operasional.
*   **Akses Laporan:** Memiliki akses ke **Dashboard Laporan Komprehensif** untuk seluruh domain (Tender, Jasa, Barang) dengan fitur *filter*.

### C. Owner
*   **Fungsi:** Melakukan pengawasan, pemantauan, dan evaluasi bisnis (*View-Only*).
*   **Akses Laporan:** Memiliki akses penuh ke **Dashboard Laporan Komprehensif** untuk semua domain dengan fitur *filter* lanjutan tanpa hak input atau *approval*.

---

## 3. Struktur Domain Bisnis

Sistem dibagi menjadi 3 modul/domain utama:

### A. Domain Tender
Digunakan untuk mencatat proyek atau pengadaan yang didapatkan melalui proses lelang/tender. 
*   **Aturan Khusus Penanganan:** Setiap data tender memiliki opsi metode pengerjaan:
    1.  **Dikerjakan Sendiri (Internal):** Dikerjakan langsung oleh tim PT Signal Panca Utama.
    2.  **Dilimpahkan ke Vendor Relasi (Sub-Contract):** Jika tender dimenangkan tetapi dialihkan ke vendor relasi (mitra eksternal) karena keterbatasan kapasitas/strategi.
*   **Field Tambahan untuk Tender:**
    *   `metode_penanganan` (Enum: `internal`, `vendor_relasi`)
    *   `nama_vendor_relasi` (String, wajib diisi jika metode penanganan adalah `vendor_relasi`)
    *   `nilai_kontrak` (Decimal)
    *   `deadline` (Date)

### B. Domain Jasa
Digunakan untuk mencatat layanan teknis/operasional kepada klien.
*   **Contoh Cakupan:** Pemasangan Wi-Fi, perbaikan/pemeliharaan komputer, dll.
*   **Field Utama:** Jenis layanan, nama klien, deskripsi pekerjaan, biaya, tanggal pengerjaan.

### C. Domain Barang
Digunakan untuk mencatat transaksi penjualan, pengadaan, atau produksi produk fisik.
*   **Contoh Cakupan:** Jasa percetakan, perlengkapan dan aksesoris komputer, dll.
*   **Field Utama:** Nama barang/produk, kuantitas, harga satuan, total harga, catatan pengiriman.

---

## 4. Sistem Pelaporan & Filter (Reporting & Filtering)

*   **Untuk Admin:** Laporan terbatas pada data yang diinput sendiri beserta status persetujuannya (`Pending`, `Approved`, `Rejected`).
*   **Untuk Manager & Owner:** Halaman laporan terpusat yang mencakup seluruh domain dengan filter:
    *   **Filter Berdasarkan Domain:** Semua / Tender / Jasa / Barang
    *   **Filter Berdasarkan Status:** Pending / Approved / Rejected
    *   **Filter Berdasarkan Rentang Tanggal (Periode):** Harian, Mingguan, Bulanan, Tahunan
    *   **Filter Berdasarkan Vendor Relasi:** Khusus untuk memantau performa tender yang dilimpahkan.

---

## 5. Spesifikasi Teknis & Database (Panduan untuk AI / Developer)

### Skema Tabel Database (Rekomendasi Relasional)

1.  **Tabel `users`**
    *   `id` (PK, INT/UUID)
    *   `name` (VARCHAR)
    *   `email` (VARCHAR, Unique)
    *   `password` (VARCHAR)
    *   `role` (ENUM: `admin`, `manager`, `owner`)

2.  **Tabel `tenders`**
    *   `id` (PK)
    *   `input_by` (FK -> `users.id`)
    *   `nama_tender` (VARCHAR)
    *   `metode_penanganan` (ENUM: `internal`, `vendor_relasi`)
    *   `nama_vendor_relasi` (VARCHAR, Nullable)
    *   `nilai_kontrak` (DECIMAL)
    *   `status` (ENUM: `pending`, `approved`, `rejected`), default: `pending`
    *   `created_at`, `updated_at`

3.  **Tabel `services` (Domain Jasa)**
    *   `id` (PK)
    *   `input_by` (FK -> `users.id`)
    *   `nama_layanan` (VARCHAR)
    *   `klien` (VARCHAR)
    *   `biaya` (DECIMAL)
    *   `status` (ENUM: `pending`, `approved`, `rejected`), default: `pending`
    *   `created_at`, `updated_at`

4.  **Tabel `goods` (Domain Barang)**
    *   `id` (PK)
    *   `input_by` (FK -> `users.id`)
    *   `nama_barang` (VARCHAR)
    *   `kuantitas` (INT)
    *   `total_harga` (DECIMAL)
    *   `status` (ENUM: `pending`, `approved`, `rejected`), default: `pending`
    *   `created_at`, `updated_at`

---

## 6. Fitur Keamanan & Non-Fungsional
*   **Autentikasi & Otorisasi:** Proteksi rute (*route guards*) berdasarkan *role* (Admin tidak boleh mengakses halaman *approval* manager/owner, Manager/Owner tidak boleh menginput data operasional jika tidak diperlukan).
*   **Notifikasi Sederhana:** Memberikan indikator/badge jumlah data *pending* pada akun Manager untuk mempercepat proses *approval*.