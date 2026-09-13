# PRD.md --- SignalNiagaJasaTender

> **Prompt siap pakai untuk AI/Developer**
>
> Bertindaklah sebagai **Senior Product Manager, Business Analyst,
> System Analyst, dan Laravel 10 Developer** yang berpengalaman. Gunakan
> dokumen PRD berikut sebagai spesifikasi utama untuk merancang dan
> mengembangkan sistem.

------------------------------------------------------------------------

# PRODUCT REQUIREMENT DOCUMENT (PRD)

## 1. Informasi Produk

-   **Nama Produk:** SignalNiagaJasaTender
-   **Perusahaan:** PT Signal Panca Utama
-   **Jenis Platform:** Responsive Web Application
-   **Framework Backend:** Laravel 10
-   **Bahasa Pemrograman:** PHP 8.1+
-   **Database:** MySQL 8
-   **Frontend:** Blade + Bootstrap 5/Tailwind CSS + Alpine.js
-   **Chart:** Chart.js
-   **Target:** Sistem manajemen bisnis internal perusahaan

## 2. Tujuan Utama

Membangun sistem informasi berbasis web yang mengintegrasikan tiga
domain utama bisnis PT Signal Panca Utama:

> **TENDER → JASA → PERDAGANGAN**

Sistem harus memusatkan data dan proses bisnis perusahaan sehingga
management dapat memantau tender, kontrak, pekerjaan jasa, pengadaan,
persediaan, penjualan, invoice, pembayaran, serta laporan bisnis dalam
satu sistem.

------------------------------------------------------------------------

# 3. Scope Bisnis PT Signal Panca Utama

Perusahaan memiliki ruang lingkup bisnis:

  -----------------------------------------------------------------------
  KBLI                                Ruang Lingkup
  ----------------------------------- -----------------------------------
  46100                               Perdagangan Besar Atas Dasar Balas
                                      Jasa (Fee) atau Kontrak

  46422                               Perdagangan Besar Barang Percetakan
                                      dan Penerbitan Dalam Berbagai
                                      Bentuk

  46499                               Perdagangan Besar Berbagai Barang
                                      dan Perlengkapan Rumah Tangga
                                      Lainnya YTDL

  46511                               Perdagangan Besar Komputer dan
                                      Perlengkapan Komputer

  46900                               Perdagangan Besar Berbagai Macam
                                      Barang
  -----------------------------------------------------------------------

------------------------------------------------------------------------

# 4. Gambaran Umum Produk

## 4.1 Latar Belakang

PT Signal Panca Utama memiliki aktivitas bisnis yang meliputi tender,
jasa, dan perdagangan barang. Proses bisnis tersebut membutuhkan
pengelolaan data yang terintegrasi.

Sistem harus mampu menghubungkan proses dari pencarian tender sampai
tender selesai, kemudian menghubungkannya dengan kontrak, pelaksanaan
jasa, pengadaan barang, penjualan, invoice, pembayaran, dan reporting.

Tujuan akhirnya adalah menciptakan satu sumber data bisnis yang mudah
dipantau oleh management dan mudah digunakan oleh setiap bagian
perusahaan.

## 4.2 Pernyataan Masalah

Sistem dibuat untuk mengatasi:

1.  Data tender belum terpusat dan sulit dipantau.
2.  Deadline tender berisiko terlewat.
3.  Dokumen tender tersebar.
4.  Data klien, kontrak, pekerjaan, fee, tagihan, dan pembayaran belum
    terintegrasi.
5.  Data supplier dan pengadaan belum terhubung dengan persediaan.
6.  Stok barang sulit ditelusuri berdasarkan transaksi.
7.  Penawaran, pesanan, penjualan, invoice, dan pembayaran perlu
    dikelola dalam satu alur.
8.  Management membutuhkan dashboard untuk melihat performa bisnis
    secara cepat.
9.  Riwayat transaksi dan aktivitas perlu dapat ditelusuri.

------------------------------------------------------------------------

# 5. Konsep Utama Sistem

Sistem dibagi menjadi tiga domain utama:

## 5.1 Manajemen Tender

-   Tender ditemukan
-   Evaluasi
-   Persiapan dokumen
-   Penawaran
-   Evaluasi
-   Menang/Kalah
-   Kontrak
-   Pelaksanaan
-   Selesai

### Tender Pipeline

``` text
Tender Ditemukan
       ↓
Evaluasi
       ↓
Persiapan Dokumen
       ↓
Penawaran
       ↓
Evaluasi
       ↓
Menang / Kalah
       ↓
Kontrak
       ↓
Pelaksanaan
       ↓
Selesai
```

------------------------------------------------------------------------

## 5.2 Manajemen Jasa

Fungsi utama:

-   Pengelolaan klien
-   Penawaran jasa
-   Kontrak kerja
-   Nilai kontrak
-   Fee/komisi
-   Periode kontrak
-   Status pekerjaan
-   Tagihan jasa
-   Pembayaran
-   Riwayat pekerjaan

### Business Flow Jasa

``` text
Klien
  ↓
Penawaran
  ↓
Kontrak
  ↓
Pelaksanaan
  ↓
Fee / Tagihan
  ↓
Pembayaran
  ↓
Riwayat Pekerjaan
```

------------------------------------------------------------------------

## 5.3 Manajemen Perdagangan

Fungsi utama:

-   Produk/barang
-   Supplier
-   Pengadaan
-   Stok
-   Penawaran barang
-   Pesanan pelanggan
-   Penjualan
-   Invoice
-   Pembayaran
-   Riwayat transaksi

### Business Flow Perdagangan

``` text
Supplier
   ↓
Pengadaan
   ↓
Persediaan
   ↓
Penawaran
   ↓
Pesanan
   ↓
Penjualan
   ↓
Invoice
   ↓
Pembayaran
```

------------------------------------------------------------------------

# 6. Integrasi Tiga Domain

Tiga domain utama harus saling terhubung.

``` text
                    SPU BUSINESS MANAGEMENT
                             │
              ┌──────────────┼──────────────┐
              │              │              │
              ▼              ▼              ▼
           TENDER           JASA        PERDAGANGAN
              │              │              │
              │              │              │
              └───────┬──────┴──────┬───────┘
                      │              │
                      ▼              ▼
                   KONTRAK       PENGADAAN
                      │              │
                      ▼              ▼
                 PELAKSANAAN       STOK
                      │              │
                      ▼              ▼
                   TAGIHAN         PENJUALAN
                      │              │
                      └──────┬───────┘
                             ▼
                          INVOICE
                             │
                             ▼
                         PEMBAYARAN
                             │
                             ▼
                          REPORTING
                             │
                             ▼
                          DASHBOARD
```

## Prinsip integrasi

### Tender → Jasa

Tender yang dimenangkan dapat dikonversi menjadi kontrak.

``` text
Tender Menang
     ↓
Generate Kontrak
     ↓
Data Klien
     ↓
Nilai Kontrak
     ↓
Periode Kontrak
     ↓
Pekerjaan Jasa
```

### Tender → Perdagangan

Tender dapat memiliki kebutuhan barang.

``` text
Tender
 ├── Laptop 10 Unit
 ├── Printer 5 Unit
 └── ATK 20 Paket
          ↓
       Pengadaan
          ↓
       Supplier
          ↓
       Persediaan
```

Sistem harus dapat menjawab:

> Barang apa yang paling banyak digunakan dalam tender?

------------------------------------------------------------------------

# 7. Target Pengguna / User Roles

## 7.1 Super Admin

Akses penuh terhadap seluruh sistem.

## 7.2 Management

Fokus pada dashboard, KPI, monitoring dan laporan.

## 7.3 Tender Officer

Mengelola proses tender dari awal sampai selesai.

## 7.4 Service Officer

Mengelola klien, penawaran jasa, kontrak, pekerjaan dan tagihan jasa.

## 7.5 Purchasing

Mengelola supplier dan pengadaan barang.

## 7.6 Warehouse

Mengelola persediaan dan pergerakan stok.

## 7.7 Sales

Mengelola penawaran barang, pesanan pelanggan dan penjualan.

## 7.8 Finance

Mengelola invoice, tagihan, pembayaran dan piutang.

------------------------------------------------------------------------

# 8. User Stories

## Management

-   Sebagai Management, saya ingin melihat jumlah tender aktif sehingga
    dapat mengetahui kondisi pipeline bisnis.
-   Sebagai Management, saya ingin melihat jumlah tender menang sehingga
    dapat mengevaluasi performa tender.
-   Sebagai Management, saya ingin melihat nilai tender sehingga dapat
    mengetahui potensi bisnis.
-   Sebagai Management, saya ingin melihat win rate sehingga dapat
    mengevaluasi efektivitas tender.
-   Sebagai Management, saya ingin melihat kontrak aktif sehingga dapat
    memantau pekerjaan berjalan.
-   Sebagai Management, saya ingin melihat pendapatan jasa sehingga
    dapat mengetahui performa bisnis jasa.
-   Sebagai Management, saya ingin melihat omzet perdagangan sehingga
    dapat mengetahui performa perdagangan.
-   Sebagai Management, saya ingin melihat supplier yang paling sering
    digunakan sehingga dapat mengevaluasi pengadaan.

## Tender Officer

-   Sebagai Tender Officer, saya ingin mencatat tender baru sehingga
    tender dapat dipantau.
-   Sebagai Tender Officer, saya ingin mengubah status tender sehingga
    pipeline selalu up-to-date.
-   Sebagai Tender Officer, saya ingin mencatat deadline sehingga tidak
    melewatkan batas waktu.
-   Sebagai Tender Officer, saya ingin menyimpan dokumen tender sehingga
    dokumen terorganisir.
-   Sebagai Tender Officer, saya ingin mencatat nilai tender sehingga
    potensi bisnis dapat dihitung.
-   Sebagai Tender Officer, saya ingin mencatat hasil tender sehingga
    dapat diketahui menang atau kalah.
-   Sebagai Tender Officer, saya ingin mengubah tender menang menjadi
    kontrak sehingga proses bisnis dapat dilanjutkan.

## Service Officer

-   Sebagai Service Officer, saya ingin mengelola data klien sehingga
    informasi klien terpusat.
-   Sebagai Service Officer, saya ingin membuat penawaran jasa sehingga
    dapat memberikan proposal kepada klien.
-   Sebagai Service Officer, saya ingin membuat kontrak sehingga
    pekerjaan memiliki dasar administrasi.
-   Sebagai Service Officer, saya ingin mencatat nilai kontrak dan fee
    sehingga nilai bisnis dapat dipantau.
-   Sebagai Service Officer, saya ingin mencatat periode kontrak
    sehingga pekerjaan dapat dimonitor.
-   Sebagai Service Officer, saya ingin mencatat status pekerjaan
    sehingga progress dapat dipantau.
-   Sebagai Service Officer, saya ingin membuat tagihan sehingga proses
    penagihan dapat dilakukan.

## Purchasing

-   Sebagai Purchasing, saya ingin mengelola supplier sehingga data
    pemasok terorganisir.
-   Sebagai Purchasing, saya ingin membuat pengadaan sehingga pembelian
    barang dapat dicatat.
-   Sebagai Purchasing, saya ingin menghubungkan pengadaan dengan tender
    sehingga kebutuhan barang dapat ditelusuri.

## Warehouse

-   Sebagai Warehouse, saya ingin mencatat barang masuk sehingga stok
    bertambah otomatis.
-   Sebagai Warehouse, saya ingin mencatat barang keluar sehingga stok
    berkurang otomatis.
-   Sebagai Warehouse, saya ingin melihat stok terkini sehingga
    mengetahui ketersediaan barang.
-   Sebagai Warehouse, saya ingin melihat riwayat stok sehingga
    perubahan persediaan dapat dilacak.

## Sales

-   Sebagai Sales, saya ingin membuat penawaran barang sehingga dapat
    menawarkan produk kepada pelanggan.
-   Sebagai Sales, saya ingin mencatat pesanan pelanggan sehingga
    transaksi dapat diproses.
-   Sebagai Sales, saya ingin mencatat penjualan sehingga omzet dapat
    dihitung.
-   Sebagai Sales, saya ingin membuat invoice sehingga pelanggan dapat
    ditagih.

## Finance

-   Sebagai Finance, saya ingin melihat seluruh invoice sehingga dapat
    memonitor tagihan.
-   Sebagai Finance, saya ingin mencatat pembayaran sehingga status
    tagihan dapat diperbarui.
-   Sebagai Finance, saya ingin melihat piutang sehingga mengetahui
    tagihan belum dibayar.
-   Sebagai Finance, saya ingin melihat riwayat pembayaran sehingga
    transaksi dapat diaudit.

------------------------------------------------------------------------

# 9. Scope of Work

## 9.1 In-Scope MVP

### Authentication

-   Login
-   Logout
-   User management
-   Role management
-   Permission management
-   Reset password
-   Active/inactive user

### Dashboard

-   Tender aktif
-   Tender menang
-   Tender kalah
-   Nilai tender
-   Win rate
-   Tender mendekati deadline
-   Kontrak aktif
-   Nilai kontrak
-   Pendapatan jasa
-   Omzet perdagangan
-   Invoice belum lunas
-   Total pembayaran
-   Produk terlaris
-   Supplier paling sering digunakan
-   Stok minimum

### Tender

-   Data tender
-   Pipeline tender
-   Status tender
-   Deadline
-   Instansi/perusahaan
-   PIC
-   Nilai estimasi
-   Nilai penawaran
-   Dokumen
-   Evaluasi
-   Hasil tender
-   Kebutuhan barang
-   Konversi tender menjadi kontrak

### Jasa

-   Client
-   Quotation
-   Contract
-   Service job
-   Fee/commission
-   Contract period
-   Billing
-   Payment
-   Service history

### Perdagangan

-   Product
-   Product category
-   Supplier
-   Procurement
-   Procurement item
-   Inventory
-   Stock movement
-   Sales quotation
-   Customer order
-   Sales
-   Sales item
-   Invoice
-   Payment
-   Transaction history

### Reporting

-   Laporan tender
-   Laporan kontrak
-   Laporan jasa
-   Laporan pengadaan
-   Laporan stok
-   Laporan penjualan
-   Laporan invoice
-   Laporan pembayaran

Output laporan:

-   PDF
-   Excel
-   Print

------------------------------------------------------------------------

# 10. Out-of-Scope MVP

Fitur berikut ditunda:

-   Mobile native Android/iOS
-   Marketplace integration
-   Payment gateway otomatis
-   Face recognition
-   Blockchain
-   AI forecasting
-   OCR otomatis
-   ERP eksternal
-   Integrasi pajak otomatis
-   WhatsApp API otomatis
-   Multi-company

------------------------------------------------------------------------

# 11. Functional Requirements

## FR-01 Authentication

Sistem harus:

-   Memiliki login.
-   Melakukan hashing password.
-   Menggunakan session management.
-   Menerapkan role-based access.
-   Menolak login user nonaktif.
-   Memvalidasi input login.

## FR-02 Dashboard

Dashboard harus menampilkan KPI:

``` text
TENDER
├── Tender Aktif
├── Tender Menang
├── Tender Kalah
├── Nilai Tender
├── Win Rate
└── Deadline Terdekat

JASA
├── Kontrak Aktif
├── Nilai Kontrak
├── Pekerjaan Berjalan
├── Tagihan
└── Pendapatan

PERDAGANGAN
├── Total Penjualan
├── Total Pengadaan
├── Total Stok
├── Piutang
└── Produk Terlaris
```

Filter:

-   Hari
-   Minggu
-   Bulan
-   Tahun
-   Custom date range

## FR-03 Tender Management

Data minimal:

-   Nomor tender
-   Nama tender
-   Instansi
-   PIC
-   Sumber tender
-   Tanggal ditemukan
-   Deadline
-   Nilai estimasi
-   Nilai penawaran
-   Status
-   Hasil
-   Catatan
-   Dokumen

## FR-04 Contract Management

Kontrak harus dapat menyimpan:

-   Nomor kontrak
-   Tender
-   Klien
-   Tanggal mulai
-   Tanggal berakhir
-   Nilai kontrak
-   Persentase fee
-   Nilai fee
-   Status

## FR-05 Service Management

Sistem harus dapat:

-   Membuat pekerjaan jasa.
-   Menghubungkan pekerjaan dengan kontrak.
-   Mengatur status pekerjaan.
-   Membuat tagihan.
-   Melihat riwayat pekerjaan.

## FR-06 Procurement

Sistem harus dapat:

-   Memilih supplier.
-   Memilih produk.
-   Menentukan quantity.
-   Menentukan harga.
-   Menghitung subtotal.
-   Menghitung total.
-   Menghubungkan pengadaan dengan tender.
-   Mengubah stok setelah barang diterima.

## FR-07 Inventory

Jenis stock movement:

``` text
IN
OUT
ADJUSTMENT
```

Contoh:

``` text
Pengadaan 10
    ↓
Stock +10

Penjualan 3
    ↓
Stock -3

Stock akhir = 7
```

Stok tidak boleh negatif.

## FR-08 Sales

Alur:

``` text
Quotation
   ↓
Customer Order
   ↓
Sales
   ↓
Invoice
   ↓
Payment
```

## FR-09 Invoice & Payment

Invoice memiliki:

-   Nomor invoice
-   Tanggal invoice
-   Jatuh tempo
-   Referensi transaksi
-   Subtotal
-   Pajak
-   Total
-   Paid amount
-   Status

Status invoice:

``` text
Draft
Issued
Partial
Paid
Overdue
Cancelled
```

## FR-10 Reporting

Laporan harus dapat:

-   Difilter.
-   Dicari.
-   Dipaginasi.
-   Diekspor PDF.
-   Diekspor Excel.
-   Dicetak.

------------------------------------------------------------------------

# 12. Non-Functional Requirements

## Performance

-   Target response halaman utama sekitar 2--3 detik dalam kondisi
    server normal.
-   Gunakan pagination.
-   Gunakan database indexing.
-   Optimalkan query.
-   Hindari N+1 query dengan Eager Loading.

## Security

Wajib menggunakan:

-   HTTPS
-   CSRF protection
-   XSS protection
-   SQL injection prevention
-   Laravel password hashing
-   Authorization
-   Rate limiting
-   Validasi request
-   File upload validation
-   Audit log

## Data Integrity

Gunakan database transaction untuk:

-   Pengadaan + update stok.
-   Penjualan + update stok.
-   Pembayaran + update invoice.
-   Konversi tender → kontrak.

Contoh:

``` php
DB::transaction(function () {
    // create transaction
    // create detail
    // update stock
});
```

## Responsive

Sistem harus optimal pada:

-   Desktop
-   Laptop
-   Tablet
-   Smartphone

------------------------------------------------------------------------

# 13. Business Rules

## Tender

1.  Nomor tender harus unik.
2.  Tender memiliki satu status aktif.
3.  Deadline harus dapat dicatat.
4.  Tender tidak dapat memiliki hasil menang dan kalah sekaligus.
5.  Tender menang dapat dikonversi menjadi kontrak.
6.  Tender selesai tidak boleh diedit oleh user biasa.
7.  Dokumen tender mengikuti permission user.

## Win Rate

``` text
Win Rate =
Jumlah Tender Menang
-------------------- × 100%
Jumlah Tender Selesai
```

## Jasa

1.  Kontrak harus memiliki client.
2.  Kontrak mempunyai tanggal mulai dan selesai.
3.  Nilai kontrak \>= 0.
4.  Tagihan terhubung dengan kontrak/pekerjaan.
5.  Total pembayaran tidak boleh melebihi total invoice, kecuali sistem
    memang menyediakan overpayment.
6.  Kontrak yang melewati tanggal berakhir dapat berubah status menjadi
    Expired.

## Perdagangan

1.  SKU produk harus unik.
2.  Stok tidak boleh negatif.
3.  Pengadaan yang dikonfirmasi menambah stok.
4.  Penjualan yang dikonfirmasi mengurangi stok.
5.  Nomor invoice harus unik.
6.  Supplier harus terdaftar sebelum pengadaan.
7.  Produk tidak boleh dijual melebihi stok tersedia.

## User

1.  Setiap user memiliki role.
2.  User nonaktif tidak dapat login.
3.  User hanya dapat mengakses modul sesuai permission.
4.  Aktivitas penting dicatat dalam audit log.

------------------------------------------------------------------------

# 14. User Flow

## Login

``` mermaid
flowchart TD
    A[Login] --> B{Valid?}
    B -- Tidak --> C[Error]
    C --> A
    B -- Ya --> D[Dashboard]
```

## Tender

``` mermaid
flowchart TD
    A[Tender Ditemukan]
    --> B[Input Tender]
    --> C[Evaluasi]
    --> D[Persiapan Dokumen]
    --> E[Penawaran]
    --> F[Evaluasi]
    --> G{Hasil}
    G -->|Kalah| H[Status Kalah]
    G -->|Menang| I[Generate Kontrak]
    I --> J[Pelaksanaan]
    --> K[Selesai]
```

## Jasa

``` mermaid
flowchart TD
    A[Klien]
    --> B[Penawaran]
    --> C{Disetujui?}
    C -->|Tidak| D[Arsip]
    C -->|Ya| E[Kontrak]
    --> F[Pelaksanaan]
    --> G[Tagihan]
    --> H[Pembayaran]
    --> I[Selesai]
```

## Perdagangan

``` mermaid
flowchart TD
    A[Supplier]
    --> B[Pengadaan]
    --> C[Barang Masuk]
    --> D[Stok]
    D --> E[Penawaran]
    --> F[Pesanan]
    --> G[Penjualan]
    --> H[Invoice]
    --> I[Pembayaran]
```

------------------------------------------------------------------------

# 15. Acceptance Criteria

## AC-01 Login

**Given** user memiliki akun aktif.

**When** user memasukkan username dan password benar.

**Then** user diarahkan ke dashboard sesuai role.

## AC-02 Login Gagal

**Given** password salah.

**When** user menekan Login.

**Then** sistem menolak akses dan menampilkan error.

## AC-03 Membuat Tender

**Given** user memiliki permission tender.

**When** user mengisi data valid dan menekan Simpan.

**Then** tender tersimpan dan muncul dalam daftar tender.

## AC-04 Deadline

**Given** tender mempunyai deadline.

**When** tanggal saat ini mendekati deadline sesuai konfigurasi
reminder.

**Then** tender muncul sebagai tender mendekati deadline.

## AC-05 Tender Menang

**Given** tender berada pada tahap evaluasi.

**When** user mengubah hasil menjadi Menang.

**Then** status tender menjadi Menang dan tombol Buat Kontrak tersedia.

## AC-06 Generate Contract

**Given** tender berstatus Menang.

**When** user memilih Buat Kontrak.

**Then** sistem membuat draft kontrak berdasarkan data tender.

## AC-07 Procurement

**Given** supplier dan produk tersedia.

**When** pengadaan dikonfirmasi sebagai diterima.

**Then** stok produk bertambah sesuai quantity.

## AC-08 Sales

**Given** stok produk 10.

**When** penjualan 3 unit dikonfirmasi.

**Then** stok menjadi 7.

## AC-09 Insufficient Stock

**Given** stok produk 2.

**When** user menjual 5 unit.

**Then** sistem menolak transaksi.

## AC-10 Payment

**Given** invoice Rp10.000.000.

**When** Finance mencatat pembayaran Rp10.000.000.

**Then** invoice menjadi Lunas.

------------------------------------------------------------------------

# 16. Database Design

Database menggunakan MySQL 8.

## Tabel Utama

``` text
users
roles
permissions

clients
service_quotations
contracts
service_jobs
service_bills
payments

tenders
tender_statuses
tender_documents
tender_items

products
product_categories
suppliers
procurements
procurement_items
stock_movements

sales_quotations
customer_orders
sales
sales_items

invoices
invoice_items

audit_logs
```

------------------------------------------------------------------------

# 17. Detail Struktur Database

## users

  Field        Type           Keterangan
  ------------ -------------- ------------
  id           BIGINT         PK
  name         VARCHAR(150)   Nama
  email        VARCHAR(150)   Email
  password     VARCHAR        Hash
  role_id      BIGINT         FK
  is_active    BOOLEAN        Status
  created_at   TIMESTAMP      Created
  updated_at   TIMESTAMP      Updated

## clients

  Field        Type
  ------------ -----------
  id           BIGINT
  code         VARCHAR
  name         VARCHAR
  company      VARCHAR
  phone        VARCHAR
  email        VARCHAR
  address      TEXT
  status       ENUM
  created_at   TIMESTAMP
  updated_at   TIMESTAMP

Relasi:

``` text
Client 1 ──── * Tender
Client 1 ──── * Quotation
Client 1 ──── * Contract
```

## tenders

  Field             Type
  ----------------- -----------
  id                BIGINT
  tender_number     VARCHAR
  name              VARCHAR
  client_id         BIGINT
  status            VARCHAR
  found_date        DATE
  deadline          DATE
  estimated_value   DECIMAL
  bid_value         DECIMAL
  result            ENUM
  notes             TEXT
  created_at        TIMESTAMP
  updated_at        TIMESTAMP

## contracts

  Field             Type
  ----------------- -------------
  id                BIGINT
  contract_number   VARCHAR
  tender_id         BIGINT NULL
  client_id         BIGINT
  start_date        DATE
  end_date          DATE
  contract_value    DECIMAL
  fee_percentage    DECIMAL
  fee_amount        DECIMAL
  status            VARCHAR

## products

  Field            Type
  ---------------- ---------
  id               BIGINT
  sku              VARCHAR
  name             VARCHAR
  category_id      BIGINT
  unit             VARCHAR
  purchase_price   DECIMAL
  selling_price    DECIMAL
  minimum_stock    INTEGER
  current_stock    INTEGER
  is_active        BOOLEAN

## suppliers

  Field     Type
  --------- ---------
  id        BIGINT
  code      VARCHAR
  name      VARCHAR
  phone     VARCHAR
  email     VARCHAR
  address   TEXT
  status    VARCHAR

## procurements

  Field                Type
  -------------------- -------------
  id                   BIGINT
  procurement_number   VARCHAR
  supplier_id          BIGINT
  tender_id            BIGINT NULL
  procurement_date     DATE
  total_amount         DECIMAL
  status               VARCHAR

## procurement_items

  Field            Type
  ---------------- ---------
  id               BIGINT
  procurement_id   BIGINT
  product_id       BIGINT
  quantity         DECIMAL
  price            DECIMAL
  subtotal         DECIMAL

## sales

  Field          Type
  -------------- ---------
  id             BIGINT
  sales_number   VARCHAR
  customer_id    BIGINT
  sales_date     DATE
  total_amount   DECIMAL
  status         VARCHAR

## sales_items

  Field        Type
  ------------ ---------
  id           BIGINT
  sales_id     BIGINT
  product_id   BIGINT
  quantity     DECIMAL
  price        DECIMAL
  subtotal     DECIMAL

## invoices

  Field            Type
  ---------------- ---------
  id               BIGINT
  invoice_number   VARCHAR
  reference_type   VARCHAR
  reference_id     BIGINT
  invoice_date     DATE
  due_date         DATE
  subtotal         DECIMAL
  tax              DECIMAL
  total            DECIMAL
  paid_amount      DECIMAL
  status           VARCHAR

## payments

  Field              Type
  ------------------ ---------
  id                 BIGINT
  invoice_id         BIGINT
  payment_date       DATE
  amount             DECIMAL
  payment_method     VARCHAR
  reference_number   VARCHAR
  notes              TEXT

## stock_movements

  Field            Type
  ---------------- -----------
  id               BIGINT
  product_id       BIGINT
  type             ENUM
  quantity         DECIMAL
  reference_type   VARCHAR
  reference_id     BIGINT
  notes            TEXT
  created_at       TIMESTAMP

------------------------------------------------------------------------

# 18. Database Relationship

``` mermaid
erDiagram
    USERS }o--|| ROLES : has

    CLIENTS ||--o{ TENDERS : participates
    CLIENTS ||--o{ CONTRACTS : owns

    TENDERS ||--o| CONTRACTS : generates
    TENDERS ||--o{ TENDER_DOCUMENTS : contains
    TENDERS ||--o{ TENDER_ITEMS : requires

    CONTRACTS ||--o{ SERVICE_JOBS : contains
    CONTRACTS ||--o{ SERVICE_BILLS : generates

    SUPPLIERS ||--o{ PROCUREMENTS : supplies
    PROCUREMENTS ||--o{ PROCUREMENT_ITEMS : contains

    PRODUCTS ||--o{ PROCUREMENT_ITEMS : included
    PRODUCTS ||--o{ SALES_ITEMS : sold
    PRODUCTS ||--o{ STOCK_MOVEMENTS : has

    SALES ||--o{ SALES_ITEMS : contains
    SALES ||--o{ INVOICES : generates

    SERVICE_BILLS ||--o{ INVOICES : generates
    INVOICES ||--o{ PAYMENTS : receives
```

------------------------------------------------------------------------

# 19. API Specification

Gunakan REST API Laravel.

## Dashboard

``` http
GET /api/dashboard
```

Response:

``` json
{
    "tenders_active": 12,
    "tenders_won": 8,
    "tender_value": 2500000000,
    "win_rate": 66.67,
    "active_contracts": 7,
    "service_revenue": 850000000,
    "trade_revenue": 1200000000
}
```

## Tender

``` http
GET /api/tenders
POST /api/tenders
GET /api/tenders/{id}
PUT /api/tenders/{id}
DELETE /api/tenders/{id}
```

Request:

``` json
{
    "tender_number": "TDR-001",
    "name": "Pengadaan Komputer",
    "client_id": 1,
    "deadline": "2026-09-20",
    "estimated_value": 500000000,
    "status": "evaluation"
}
```

## Contract

``` http
GET /api/contracts
POST /api/contracts
GET /api/contracts/{id}
PUT /api/contracts/{id}
```

## Products

``` http
GET /api/products
POST /api/products
GET /api/products/{id}
PUT /api/products/{id}
```

## Sales

``` http
GET /api/sales
POST /api/sales
GET /api/sales/{id}
```

------------------------------------------------------------------------

# 20. Tech Stack

## Backend

-   Laravel 10
-   PHP 8.1+
-   Eloquent ORM
-   Laravel Validation
-   Laravel Authentication
-   Laravel Authorization
-   Laravel Scheduler
-   Laravel Storage

## Frontend

Rekomendasi MVP:

-   Laravel Blade
-   Bootstrap 5 atau Tailwind CSS
-   Alpine.js
-   Chart.js
-   DataTables

Gunakan Blade + Alpine.js terlebih dahulu agar sistem sederhana, mudah
dirawat dan mudah dideploy. SPA terpisah belum diperlukan pada MVP.

## Database

-   MySQL 8

## Server

``` text
Ubuntu
   ↓
Nginx
   ↓
PHP-FPM
   ↓
Laravel 10
   ↓
MySQL 8
```

------------------------------------------------------------------------

# 21. Struktur Folder Laravel

``` text
spu-business-system/
│
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/
│   │   │   ├── DashboardController.php
│   │   │   ├── TenderController.php
│   │   │   ├── ClientController.php
│   │   │   ├── ContractController.php
│   │   │   ├── ServiceJobController.php
│   │   │   ├── ProductController.php
│   │   │   ├── SupplierController.php
│   │   │   ├── ProcurementController.php
│   │   │   ├── SalesController.php
│   │   │   ├── InvoiceController.php
│   │   │   ├── PaymentController.php
│   │   │   └── ReportController.php
│   │   │
│   │   ├── Requests/
│   │   └── Middleware/
│   │
│   ├── Models/
│   └── Services/
│       ├── TenderService.php
│       ├── ContractService.php
│       ├── ProcurementService.php
│       ├── InventoryService.php
│       ├── SalesService.php
│       └── PaymentService.php
│
├── database/
│   ├── migrations/
│   ├── seeders/
│   └── factories/
│
├── resources/
│   ├── views/
│   │   ├── layouts/
│   │   ├── dashboard/
│   │   ├── tenders/
│   │   ├── clients/
│   │   ├── contracts/
│   │   ├── services/
│   │   ├── products/
│   │   ├── suppliers/
│   │   ├── procurements/
│   │   ├── sales/
│   │   ├── invoices/
│   │   ├── payments/
│   │   └── reports/
│   │
│   ├── css/
│   └── js/
│
├── routes/
│   ├── web.php
│   └── api.php
│
├── storage/
├── tests/
│   ├── Feature/
│   └── Unit/
│
├── .env
├── composer.json
└── artisan
```

------------------------------------------------------------------------

# 22. Dashboard Management

Dashboard harus dapat menjawab pertanyaan berikut:

1.  Berapa tender yang sedang berjalan?
2.  Berapa tender yang dimenangkan?
3.  Berapa nilai tender?
4.  Berapa win rate tender?
5.  Tender mana yang mendekati deadline?
6.  Berapa nilai kontrak aktif?
7.  Berapa omzet perdagangan?
8.  Berapa pendapatan dari jasa?
9.  Barang apa yang paling banyak digunakan dalam tender?
10. Supplier mana yang paling sering digunakan?
11. Berapa invoice belum lunas?
12. Berapa total pembayaran?
13. Produk mana yang stoknya menipis?

## Contoh KPI

``` text
Tender Aktif
12

Tender Menang
8

Win Rate
66,67%

Nilai Tender
Rp2.500.000.000

Kontrak Aktif
7

Pendapatan Jasa
Rp850.000.000

Omzet Perdagangan
Rp1.200.000.000
```

Dashboard sebaiknya menggunakan:

-   KPI Cards
-   Line Chart
-   Bar Chart
-   Donut/Pie Chart
-   Recent Transactions
-   Deadline List
-   Low Stock Alert
-   Outstanding Invoice

------------------------------------------------------------------------

# 23. Risiko dan Mitigasi

  Risiko               Dampak   Mitigasi
  -------------------- -------- ------------------------
  Kebocoran data       Tinggi   RBAC, HTTPS, hashing
  Password dicuri      Tinggi   Password hashing
  Double transaction   Tinggi   DB transaction
  Stok tidak sesuai    Tinggi   Stock movement + audit
  Deadline terlewat    Tinggi   Reminder
  Data terhapus        Tinggi   Backup
  Server down          Sedang   Monitoring
  Dokumen hilang       Sedang   Storage + backup
  Kesalahan input      Sedang   Validation
  Akses salah          Tinggi   Permission
  Brute force          Sedang   Rate limiting
  Database rusak       Tinggi   Backup berkala

------------------------------------------------------------------------

# 24. Asumsi

1.  Sistem digunakan sebagai sistem internal perusahaan.
2.  Perusahaan memiliki administrator.
3.  User mempunyai koneksi internet.
4.  User menggunakan browser modern.
5.  Management menentukan role dan permission.
6.  Data perusahaan akan dimasukkan secara bertahap.
7.  Sistem menggunakan MySQL.
8.  Sistem tidak membutuhkan aplikasi mobile pada MVP.

------------------------------------------------------------------------

# 25. Batasan MVP

Target awal:

-   10--50 user aktif.
-   Dapat dikembangkan hingga ratusan user dengan konfigurasi server
    yang sesuai.
-   Mendukung ribuan sampai puluhan ribu transaksi dengan indexing,
    pagination dan query optimization.
-   Membutuhkan koneksi internet.
-   Belum multi-company.

------------------------------------------------------------------------

# 26. Future Enhancement

## Phase 2

### Financial Management

-   Cash flow
-   Pengeluaran
-   Pendapatan
-   Profit & Loss
-   Piutang
-   Hutang
-   Rekonsiliasi pembayaran

### Advanced Tender

-   Kalender deadline
-   Reminder otomatis
-   Template dokumen
-   Win rate berdasarkan klien
-   Win rate berdasarkan jenis tender
-   Ranking tender berdasarkan nilai
-   Analisis performa tender

### Advanced Inventory

-   Barcode
-   QR Code
-   Stock opname
-   Multi warehouse
-   Minimum stock notification
-   Purchase recommendation

## Phase 3

### Integrasi

-   WhatsApp API
-   Email notification
-   Payment gateway
-   Marketplace
-   Accounting software
-   Cloud storage

### Business Intelligence

``` text
Business Intelligence
        │
        ├── Tender Analytics
        ├── Service Analytics
        ├── Sales Analytics
        ├── Inventory Analytics
        └── Supplier Analytics
```

------------------------------------------------------------------------

# 27. Prinsip Pengembangan

Developer wajib mengikuti prinsip berikut:

## 27.1 Clean Code

Kode harus:

-   Mudah dibaca.
-   Modular.
-   Tidak duplikasi.
-   Menggunakan naming convention yang jelas.
-   Menggunakan Service Class untuk business logic kompleks.

## 27.2 MVC Laravel

Gunakan pola:

``` text
Route
  ↓
Controller
  ↓
Request Validation
  ↓
Service
  ↓
Model
  ↓
Database
```

Jangan menempatkan business logic kompleks di Controller.

## 27.3 Database Integrity

Gunakan:

-   Foreign Key
-   Unique Index
-   Database Transaction
-   Soft Delete jika diperlukan
-   Index pada kolom pencarian/filter

## 27.4 Auditability

Aktivitas penting seperti:

-   Create
-   Update
-   Delete
-   Status change
-   Payment
-   Stock adjustment

harus dapat ditelusuri melalui audit log.

------------------------------------------------------------------------

# 28. Urutan Pengembangan

## Phase 1 --- Foundation

``` text
Laravel Setup
↓
Authentication
↓
Role & Permission
↓
Layout
↓
Master Data
```

## Phase 2 --- Tender

``` text
Tender
↓
Pipeline
↓
Dokumen
↓
Evaluasi
↓
Hasil
↓
Contract
```

## Phase 3 --- Jasa

``` text
Client
↓
Quotation
↓
Contract
↓
Service Job
↓
Billing
↓
Payment
```

## Phase 4 --- Perdagangan

``` text
Product
↓
Supplier
↓
Procurement
↓
Inventory
↓
Sales
↓
Invoice
↓
Payment
```

## Phase 5 --- Reporting

``` text
Reports
↓
Dashboard
↓
KPI
↓
Charts
↓
Export PDF/Excel
```

------------------------------------------------------------------------

# 29. Instruksi Implementasi untuk AI / Developer

Gunakan PRD ini sebagai **single source of truth** selama proses
development.

Saat mulai membuat aplikasi:

1.  Gunakan **Laravel 10**.
2.  Gunakan struktur MVC Laravel yang rapi.
3.  Buat migration terlebih dahulu.
4.  Buat model dan relationship.
5.  Buat seeder untuk data awal.
6.  Buat authentication.
7.  Buat role dan permission.
8.  Buat master data.
9.  Implementasikan modul Tender.
10. Implementasikan modul Jasa.
11. Implementasikan modul Perdagangan.
12. Implementasikan Invoice dan Payment.
13. Implementasikan Dashboard.
14. Implementasikan Reporting.
15. Buat Feature Test dan Unit Test.
16. Pastikan validasi dan authorization diterapkan.
17. Jangan membuat fitur di luar scope MVP tanpa alasan yang jelas.
18. Jangan membuat struktur database yang redundan.
19. Gunakan database transaction pada proses yang mempengaruhi stok dan
    keuangan.
20. Pastikan seluruh halaman responsive dan mudah digunakan.

## Prioritas UX

Utamakan:

-   Navigasi sederhana.
-   Form tidak terlalu panjang.
-   Dropdown untuk master data.
-   Search dan filter.
-   Pagination.
-   Confirmation sebelum delete.
-   Status menggunakan badge.
-   KPI mudah dibaca.
-   Dashboard tidak terlalu penuh.
-   Mobile-friendly.
-   Feedback sukses/gagal setelah setiap aksi.

------------------------------------------------------------------------

# 30. Output yang Diharapkan dari Developer/AI

Jika diminta mulai mengembangkan sistem berdasarkan PRD ini, kerjakan
secara bertahap dan berikan:

1.  Struktur database lengkap.
2.  Migration Laravel.
3.  Model dan relationship.
4.  Seeder.
5.  Routes.
6.  Controller.
7.  Form Request Validation.
8.  Service Class.
9.  Blade View.
10. JavaScript/Alpine.js.
11. API endpoint.
12. Authorization.
13. Dashboard.
14. Reporting.
15. Testing.
16. Panduan instalasi.
17. Panduan deployment.

**Jangan langsung membuat seluruh sistem dalam satu file.** Pecah
berdasarkan modul dan tanggung jawab masing-masing.

------------------------------------------------------------------------

# 31. Kesimpulan Produk

SPU Business Management System adalah sistem manajemen bisnis
terintegrasi yang menghubungkan:

``` text
                 TENDER
                   ↓
            TENDER MENANG
                   ↓
                KONTRAK
             ↙           ↘
           JASA        PERDAGANGAN
            ↓               ↓
       PELAKSANAAN       PENGADAAN
            ↓               ↓
         TAGIHAN          STOK
            ↓               ↓
        PEMBAYARAN      PENJUALAN
            ↘               ↙
              INVOICE
                 ↓
              PAYMENT
                 ↓
              REPORT
                 ↓
             DASHBOARD
```

Sistem harus menghasilkan satu ekosistem data yang memungkinkan
management mengetahui **kondisi tender, nilai kontrak, performa jasa,
omzet perdagangan, persediaan, supplier, invoice, pembayaran, dan
performa bisnis secara keseluruhan.**


**Catatan Tambahan untuk AI:**
Pastikan seluruh alur yang dirancang mengutamakan kemudahan pengguna (*user-friendly*), alur kerja yang logis, dan penulisan kode/struktur data yang rapi agar mudah langsung diimplementasikan oleh developer. Berikan jawaban dalam bahasa Indonesia yang profesional namun mudah dipahami.