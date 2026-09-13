# DesignSystem.md
# SPU Business Management System — UI/UX Design System

> **Dokumen siap pakai untuk UI/UX Designer dan Frontend Developer**
>
> Produk: **SignalNiagaJasaTender**  
> Perusahaan: **PT Signal Panca Utama**  
> Platform: **Responsive Web Application**  
> Frontend target: **Blade + Tailwind CSS**  
> Backend: **Laravel 10**

---

# 1. Overview & Tipografi

## 1.1 Overview Brand

SPU Business Management System adalah aplikasi internal untuk mengelola tiga domain utama bisnis:

```text
TENDER → JASA → PERDAGANGAN
```

UI harus memberikan kesan:

- Profesional
- Modern
- Bersih
- Terpercaya
- Efisien
- Data-oriented
- Premium tetapi tidak berlebihan

Konsep visual utama:

> **Clean Corporate + Modern SaaS Dashboard**

Tampilan tidak menggunakan gaya dashboard yang terlalu ramai. Fokus utama adalah **hierarki informasi, keterbacaan angka, status bisnis, dan kecepatan pengguna menemukan data**.

### Prinsip visual

1. Background dominan putih/off-white.
2. Warna biru digunakan sebagai identitas utama dan action.
3. Card menggunakan border tipis dan shadow sangat halus.
4. Rounded corner digunakan secara moderat.
5. Hindari gradient berlebihan.
6. Gunakan whitespace sebagai elemen desain.
7. KPI penting harus mudah dipindai dalam 2–3 detik.
8. Status bisnis menggunakan warna semantik yang konsisten.
9. Data tabel harus padat tetapi tetap nyaman dibaca.
10. Animasi hanya digunakan untuk feedback dan transisi.

---

# 1.2 Font Family

## Font Utama — Outfit

**Outfit** digunakan untuk:

- Heading
- Dashboard title
- Navigation
- Button
- KPI
- Form
- Table
- Body
- Label

Fallback:

```css
font-family: 'Outfit', ui-sans-serif, system-ui, sans-serif;
```

## Font Pendukung — TT Commons Pro

**TT Commons Pro** digunakan secara terbatas untuk:

- Subheading
- Supporting statement
- Hero tagline
- Intro section
- Marketing-style copy

Fallback:

```css
font-family: 'TT Commons Pro', 'Outfit', sans-serif;
```

> Jika lisensi TT Commons Pro tidak tersedia pada environment development, gunakan Outfit sebagai fallback tanpa mengganti struktur typography.

---

# 1.3 Hierarki Tipografi

| Token | Size | Weight | Line Height | Letter Spacing | Penggunaan |
|---|---:|---:|---:|---:|---|
| `display-xl` | 48px | 700 | 1.05 | -0.04em | Hero / angka utama |
| `display-lg` | 40px | 700 | 1.1 | -0.035em | Page hero |
| `heading-xl` | 32px | 700 | 1.15 | -0.025em | Page title |
| `heading-lg` | 24px | 700 | 1.2 | -0.02em | Section title |
| `heading-md` | 20px | 650 | 1.3 | -0.015em | Card title |
| `heading-sm` | 16px | 650 | 1.35 | -0.01em | Small heading |
| `body-lg` | 18px | 400 | 1.6 | 0 | Intro text |
| `body-md` | 15px | 400 | 1.55 | 0 | Default body |
| `body-sm` | 14px | 400 | 1.5 | 0 | Secondary text |
| `caption` | 12px | 500 | 1.4 | 0.01em | Metadata |
| `button-md` | 14px | 600 | 1 | 0 | Button |
| `button-sm` | 13px | 600 | 1 | 0 | Small button |
| `kpi-xl` | 32px | 700 | 1.1 | -0.03em | KPI dashboard |
| `table-md` | 14px | 450 | 1.45 | 0 | Data table |

---

# 1.4 Prinsip Tipografi

## Do

- Gunakan `Outfit` sebagai font utama.
- Gunakan heading dengan weight 650–700.
- Gunakan tracking negatif pada heading besar.
- Gunakan sentence case untuk judul UI.
- Gunakan angka KPI dengan ukuran besar dan weight kuat.
- Pastikan line-height cukup untuk teks panjang.
- Gunakan warna teks berdasarkan hierarchy, bukan semuanya hitam.

## Don't

- Jangan menggunakan lebih dari dua keluarga font.
- Jangan menggunakan ALL CAPS untuk heading utama.
- Jangan menggunakan font dekoratif untuk tabel.
- Jangan menggunakan weight 800–900 secara berlebihan.
- Jangan menggunakan heading terlalu rapat tanpa whitespace.
- Jangan membuat teks body terlalu kecil untuk mengejar kepadatan dashboard.

---

# 2. Sistem Warna

## 2.1 Brand & Aksen

| Token | Hex | Penggunaan |
|---|---|---|
| `primary` | `#2563EB` | CTA, active state, link |
| `primary-deep` | `#1D4ED8` | Hover / pressed |
| `primary-soft` | `#EFF6FF` | Background highlight |
| `primary-muted` | `#DBEAFE` | Badge / soft surface |
| `accent` | `#0EA5E9` | Secondary accent |
| `accent-soft` | `#E0F2FE` | Accent background |

## 2.2 Surface

| Token | Hex | Penggunaan |
|---|---|---|
| `canvas` | `#FFFFFF` | Background utama |
| `canvas-soft` | `#F8FAFC` | Background dashboard |
| `surface` | `#FFFFFF` | Card |
| `surface-soft` | `#F1F5F9` | Secondary card |
| `surface-hover` | `#F8FAFC` | Hover |
| `hairline` | `#E2E8F0` | Border/divider |
| `hairline-strong` | `#CBD5E1` | Border emphasis |

## 2.3 Teks

| Token | Hex | Penggunaan |
|---|---|---|
| `ink` | `#0F172A` | Heading / primary text |
| `body` | `#334155` | Body text |
| `mute` | `#64748B` | Secondary text |
| `subtle` | `#94A3B8` | Placeholder / metadata |
| `on-primary` | `#FFFFFF` | Text di atas primary |

## 2.4 Semantik

| Token | Hex | Penggunaan |
|---|---|---|
| `success` | `#16A34A` | Berhasil / selesai |
| `success-soft` | `#F0FDF4` | Background success |
| `warning` | `#D97706` | Perhatian / mendekati deadline |
| `warning-soft` | `#FFFBEB` | Background warning |
| `error` | `#DC2626` | Error / ditolak / gagal |
| `error-soft` | `#FEF2F2` | Background error |
| `info` | `#0284C7` | Informasi |
| `info-soft` | `#F0F9FF` | Background info |

---

# 2.5 Status Tender

| Status | Token | Makna |
|---|---|---|
| Ditemukan | `info` | Tender baru ditemukan |
| Evaluasi | `primary` | Sedang dievaluasi |
| Persiapan Dokumen | `primary` | Dokumen sedang disiapkan |
| Penawaran | `primary` | Penawaran sedang diproses |
| Evaluasi Akhir | `warning` | Menunggu hasil evaluasi |
| Menang | `success` | Tender berhasil |
| Kalah | `error` | Tender tidak berhasil |
| Kontrak | `success` | Berlanjut menjadi kontrak |
| Pelaksanaan | `primary` | Sedang dikerjakan |
| Selesai | `success` | Proses selesai |
| Dibatalkan | `error` | Dibatalkan |

---

# 2.6 Status Jasa

| Status | Token |
|---|---|
| Draft | `mute` |
| Aktif | `success` |
| Direncanakan | `info` |
| Berjalan | `primary` |
| Selesai | `success` |
| Terlambat | `warning` |
| Dibatalkan | `error` |

---

# 2.7 Status Perdagangan

| Status | Token |
|---|---|
| Draft | `mute` |
| Dipesan | `info` |
| Diterima | `success` |
| Diproses | `primary` |
| Selesai | `success` |
| Dibatalkan | `error` |

---

# 2.8 Status Pembayaran

| Status | Token |
|---|---|
| Belum Dibayar | `warning` |
| Sebagian | `info` |
| Lunas | `success` |
| Jatuh Tempo | `error` |
| Dibatalkan | `mute` |

---

# 2.9 Brand Gradient

Gradient harus digunakan secara terbatas untuk hero atau highlight tertentu.

```css
--gradient-brand:
    linear-gradient(135deg, #2563EB 0%, #0EA5E9 100%);

--gradient-soft:
    linear-gradient(135deg, #EFF6FF 0%, #F0F9FF 100%);

--gradient-radial:
    radial-gradient(circle at top right, #DBEAFE 0%, #FFFFFF 55%);
```

Jangan menggunakan gradient pada seluruh card dashboard.

---

# 3. Layout, Elevasi, & Bentuk

# 3.1 Spacing

Base unit: **4px**

| Token | Value |
|---|---:|
| `xxs` | 4px |
| `xs` | 8px |
| `sm` | 12px |
| `md` | 16px |
| `lg` | 24px |
| `xl` | 32px |
| `2xl` | 40px |
| `3xl` | 48px |
| `4xl` | 64px |
| `section` | 80px |
| `section-lg` | 96px |
| `section-xl` | 120px |

Default component spacing:

```text
Label → Input          8px
Input → Input          16px
Card internal padding  20–24px
Section → Section      32–48px
Page padding           24px mobile
Page padding           32px desktop
```

---

# 3.2 Grid & Container

| Breakpoint | Width | Layout |
|---|---:|---|
| Mobile | < 640px | 1 column |
| Tablet | 640–1023px | 1–2 column |
| Desktop | 1024–1279px | Sidebar + content |
| Wide | ≥ 1280px | Sidebar + max-width content |

Container utama:

```css
max-width: 1440px;
margin-inline: auto;
padding-inline: 24px;
```

Dashboard:

```text
Sidebar: 248px
Content gap: 24px
Header height: 72px
```

---

# 3.3 Elevation

Gunakan stacked shadows yang halus.

| Level | Shadow | Penggunaan |
|---|---|---|
| `shadow-0` | none | Flat |
| `shadow-1` | 0 1px 2px rgba(15,23,42,.05) | Card |
| `shadow-2` | 0 2px 6px rgba(15,23,42,.06), 0 1px 2px rgba(15,23,42,.04) | Elevated card |
| `shadow-3` | 0 8px 20px rgba(15,23,42,.08), 0 2px 6px rgba(15,23,42,.05) | Dropdown |
| `shadow-4` | 0 16px 32px rgba(15,23,42,.10), 0 4px 10px rgba(15,23,42,.06) | Popover |
| `shadow-5` | 0 24px 48px rgba(15,23,42,.14), 0 8px 16px rgba(15,23,42,.08) | Modal |

Tambahkan:

```css
box-shadow:
    var(--shadow),
    inset 0 0 0 1px rgba(226, 232, 240, 0.8);
```

---

# 3.4 Border Radius

| Token | Value | Penggunaan |
|---|---:|---|
| `none` | 0px | Table tertentu |
| `sm` | 6px | Input kecil |
| `md` | 10px | Input / Button |
| `lg` | 14px | Card |
| `xl` | 18px | Feature card |
| `pill` | 999px | Badge / CTA |
| `full` | 9999px | Avatar / circular |

Default:

```text
Card: lg
Input: md
Button: md / pill
Badge: pill
Modal: xl
```

---

# 4. Komponen UI Standar

# 4.1 Buttons

## Primary Button

```text
Height: 40px
Padding: 0 16px
Radius: 10px
Font: 14px / 600
Background: primary
Text: on-primary
```

States:

```text
Default → #2563EB
Hover   → #1D4ED8
Active  → #1E40AF
Focus   → 2px primary-soft ring
Disabled → opacity 50%
```

## Primary Marketing Pill

```text
Height: 44px
Padding: 0 20px
Radius: pill
Font: 14px / 600
```

## Secondary Button

```text
Background: white
Border: hairline
Text: ink
Hover: canvas-soft
```

## Small Navigation Button

```text
Height: 36px
Padding: 0 12px
Radius: 8px
Font: 13px / 600
```

## Danger Button

```text
Background: error
Text: white
Hover: #B91C1C
```

## Tab / Ghost Chips

```text
Height: 36px
Padding: 0 12px
Background: transparent
Active background: primary-soft
Active text: primary
Radius: pill
```

---

# 4.2 Cards

## Marketing Card

```text
Padding: 24px
Radius: 18px
Border: hairline
Background: surface
Shadow: shadow-1
```

## Dashboard KPI Card

```text
Padding: 20px
Radius: 14px
Border: hairline
Min-height: 128px
```

Anatomi:

```text
Label
KPI Number
Trend / Status
Supporting information
```

## List Item Card

```text
Padding: 16px
Radius: 12px
Border: hairline
Hover: surface-hover
```

## Soft Card

```text
Background: primary-soft
Border: transparent
```

---

# 4.3 Form Input

## Text Input

```text
Height: 40px
Padding: 0 12px
Radius: 10px
Border: 1px solid hairline
Font: 14px
```

Focus:

```text
border: primary
box-shadow: 0 0 0 3px primary-soft
```

Error:

```text
border: error
background: error-soft
```

## Label

```text
Font: 13px
Weight: 600
Color: ink
Margin-bottom: 8px
```

## Placeholder

```text
Color: subtle
```

## Error Message

```text
Font: 12px
Color: error
Margin-top: 6px
```

## Textarea

```text
Min-height: 100px
Padding: 12px
Radius: 10px
Resize: vertical
```

## Select

Gunakan tampilan visual yang sama dengan input.

---

# 4.4 Navigation

## Sticky Navbar

```text
Height: 64–72px
Position: sticky
Top: 0
Background: rgba(255,255,255,.92)
Backdrop-filter: blur(12px)
Border-bottom: hairline
Z-index: 40
```

## Sidebar

```text
Width: 248px
Background: #FFFFFF
Border-right: hairline
Position: fixed
```

Struktur:

```text
Logo
│
├── Dashboard
│
├── Tender
│
├── Jasa
│
├── Perdagangan
│
├── Inventory
│
├── Invoice
├── Pembayaran
│
├── Laporan
│
└── Pengaturan
```

## Menu Item

Default:

```text
Height: 40px
Padding: 0 12px
Radius: 8px
Color: body
```

Hover:

```text
Background: canvas-soft
Color: ink
```

Active:

```text
Background: primary-soft
Color: primary
Font-weight: 600
```

---

# 4.5 Footer

Footer dashboard bersifat minimal:

```text
© PT Signal Panca Utama
SPU Business Management System
```

Tidak perlu footer besar pada halaman internal.

---

# 4.6 Data Table

## Header

```text
Height: 44px
Padding: 0 16px
Background: canvas-soft
Font: 12px / 600
Color: mute
```

## Cell

```text
Padding: 14px 16px
Font: 14px
```

## Row

```text
Border-bottom: hairline
Hover: canvas-soft
```

### Alignment

```text
Text      → left
Date      → left
Status    → center
Quantity  → right
Currency  → right
Action    → right
```

### Table UX

- Gunakan pagination.
- Gunakan search.
- Gunakan filter status.
- Gunakan sort untuk field yang relevan.
- Jangan menampilkan terlalu banyak kolom sekaligus.
- Pada mobile, gunakan horizontal scroll atau ubah menjadi list/card.

---

# 4.7 Modal

Anatomi:

```text
Overlay
└── Modal
    ├── Header
    │   ├── Title
    │   └── Close
    ├── Body
    └── Footer
        ├── Cancel
        └── Primary Action
```

Spesifikasi:

```text
Max-width: 560px
Radius: 18px
Padding: 24px
Shadow: shadow-5
```

Overlay:

```text
background: rgba(15,23,42,.45)
backdrop-filter: blur(4px)
```

---

# 5. Komponen Khusus Produk

# 5.1 Tender Pipeline Stepper

Komponen paling penting pada domain Tender.

```text
Ditemukan
   ↓
Evaluasi
   ↓
Persiapan Dokumen
   ↓
Penawaran
   ↓
Evaluasi Akhir
   ↓
Menang / Kalah
   ↓
Kontrak
   ↓
Pelaksanaan
   ↓
Selesai
```

Visual:

- Completed: success
- Current: primary
- Upcoming: hairline/mute
- Failed: error

Setiap step menampilkan:

```text
Nomor
Label
Tanggal (opsional)
```

---

# 5.2 Tender Deadline Alert

Card khusus untuk tender mendekati deadline.

Level:

```text
> 7 hari      → info
3–7 hari      → warning
≤ 2 hari      → error
Lewat deadline → error
```

Anatomi:

```text
[Icon] Tender Deadline
Nama Tender
Deadline: 12 Sep 2026
Sisa: 2 hari
[Detail]
```

---

# 5.3 KPI Business Overview

Dashboard utama menggunakan KPI cards:

```text
┌──────────────┐ ┌──────────────┐ ┌──────────────┐
│ Tender Aktif │ │ Tender Menang│ │ Nilai Tender │
│      12      │ │      8       │ │ Rp 4,2 M     │
└──────────────┘ └──────────────┘ └──────────────┘

┌──────────────┐ ┌──────────────┐ ┌──────────────┐
│ Win Rate     │ │ Kontrak Aktif│ │ Omzet Dagang │
│    66,7%     │ │ Rp 2,8 M     │ │ Rp 1,4 M     │
└──────────────┘ └──────────────┘ └──────────────┘
```

Tambahkan:

- Trend jika data periode tersedia.
- Period selector.
- Tooltip untuk definisi KPI.

---

# 5.4 Business Flow Overview

Komponen visual untuk menunjukkan hubungan tiga domain:

```text
┌───────────┐
│  TENDER   │
└─────┬─────┘
      │
      ▼
┌───────────┐
│   JASA    │
└─────┬─────┘
      │
      ▼
┌───────────┐
│PERDAGANGAN│
└───────────┘
```

Digunakan pada dashboard atau halaman overview, bukan pada setiap halaman.

---

# 5.5 Inventory Stock Indicator

Menampilkan kondisi stok:

```text
Stok Aman      → success
Mendekati Min  → warning
Stok Habis     → error
```

Contoh:

```text
Kertas A4
██████████████░░ 82%
Stok: 820
Minimum: 200
```

---

# 6. Layout Halaman Utama

# 6.1 Dashboard

Struktur:

```text
Sidebar
│
└── Main
    ├── Topbar
    ├── Page Header
    │   ├── "Dashboard"
    │   └── Period Filter
    │
    ├── KPI Grid
    │
    ├── Tender Overview
    │   ├── Pipeline
    │   └── Deadline Alert
    │
    ├── Business Performance
    │   ├── Revenue Jasa
    │   └── Omzet Perdagangan
    │
    └── Operational Insight
        ├── Top Products
        └── Top Suppliers
```

---

# 6.2 Halaman Tender

```text
Page Header
├── Tender
├── Search
└── + Tambah Tender

Filter
├── Status
├── Deadline
└── Client

Table
├── Nomor
├── Nama Tender
├── Client
├── Nilai
├── Deadline
├── Status
└── Action
```

---

# 6.3 Detail Tender

```text
Header
├── Tender Number
├── Tender Name
└── Status Badge

Tender Summary
├── Client
├── Estimated Value
├── Bid Value
└── Deadline

Pipeline Stepper

Documents

Procurement Related

Contract

Activity / Audit
```

---

# 6.4 Halaman Jasa

```text
Page Header
    ↓
Contract Summary
    ↓
Service Job List
    ↓
Billing
    ↓
Payment
```

Flow utama:

```text
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
```

---

# 6.5 Halaman Perdagangan

Flow:

```text
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

Tampilan harus memprioritaskan:

- Stok.
- Barang masuk.
- Barang keluar.
- Penjualan.
- Invoice.
- Pembayaran.

---

# 7. Responsive Rules

## Mobile

- Sidebar berubah menjadi drawer.
- KPI menjadi 1 kolom.
- Table menggunakan horizontal scroll/card.
- Filter dapat dibuka dalam bottom sheet/dropdown.
- Primary action tetap mudah dijangkau.
- Modal menjadi hampir full-width.
- Padding halaman 16px.

## Tablet

- Sidebar dapat collapse.
- KPI 2 kolom.
- Table tetap digunakan jika ruang cukup.
- Form dapat menggunakan 2 kolom.

## Desktop

- Sidebar fixed.
- KPI 4–6 cards per row tergantung ukuran.
- Content max-width 1440px.
- Detail page dapat menggunakan two-column layout.

---

# 8. Tailwind CSS Implementation

## 8.1 `tailwind.config.js`

```javascript
/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],

    theme: {
        extend: {
            colors: {
                primary: {
                    DEFAULT: "#2563EB",
                    deep: "#1D4ED8",
                    soft: "#EFF6FF",
                    muted: "#DBEAFE",
                },

                accent: {
                    DEFAULT: "#0EA5E9",
                    soft: "#E0F2FE",
                },

                canvas: {
                    DEFAULT: "#FFFFFF",
                    soft: "#F8FAFC",
                },

                surface: {
                    DEFAULT: "#FFFFFF",
                    soft: "#F1F5F9",
                    hover: "#F8FAFC",
                },

                hairline: {
                    DEFAULT: "#E2E8F0",
                    strong: "#CBD5E1",
                },

                ink: "#0F172A",
                body: "#334155",
                mute: "#64748B",
                subtle: "#94A3B8",

                success: {
                    DEFAULT: "#16A34A",
                    soft: "#F0FDF4",
                },

                warning: {
                    DEFAULT: "#D97706",
                    soft: "#FFFBEB",
                },

                error: {
                    DEFAULT: "#DC2626",
                    soft: "#FEF2F2",
                },

                info: {
                    DEFAULT: "#0284C7",
                    soft: "#F0F9FF",
                },
            },

            fontFamily: {
                sans: [
                    "Outfit",
                    "ui-sans-serif",
                    "system-ui",
                    "sans-serif",
                ],

                commons: [
                    "TT Commons Pro",
                    "Outfit",
                    "sans-serif",
                ],
            },

            borderRadius: {
                sm: "6px",
                md: "10px",
                lg: "14px",
                xl: "18px",
                pill: "999px",
            },

            boxShadow: {
                0: "none",

                1: "0 1px 2px rgba(15,23,42,.05)",

                2: [
                    "0 2px 6px rgba(15,23,42,.06)",
                    "0 1px 2px rgba(15,23,42,.04)",
                ].join(", "),

                3: [
                    "0 8px 20px rgba(15,23,42,.08)",
                    "0 2px 6px rgba(15,23,42,.05)",
                ].join(", "),

                4: [
                    "0 16px 32px rgba(15,23,42,.10)",
                    "0 4px 10px rgba(15,23,42,.06)",
                ].join(", "),

                5: [
                    "0 24px 48px rgba(15,23,42,.14)",
                    "0 8px 16px rgba(15,23,42,.08)",
                ].join(", "),
            },

            maxWidth: {
                "8xl": "1440px",
            },
        },
    },

    plugins: [],
};
```

---

# 9. Tailwind Utility Examples

## 9.1 Primary Button

```html
<button
    type="button"
    class="inline-flex h-10 items-center justify-center
           rounded-md bg-primary px-4
           text-sm font-semibold text-white
           transition-colors
           hover:bg-primary-deep
           focus:outline-none
           focus:ring-4 focus:ring-primary-soft
           disabled:cursor-not-allowed
           disabled:opacity-50"
>
    Tambah Tender
</button>
```

---

# 9.2 Status Badge

```html
<span
    class="inline-flex items-center rounded-pill
           bg-success-soft px-2.5 py-1
           text-xs font-semibold text-success"
>
    Menang
</span>
```

Warning:

```html
<span
    class="inline-flex items-center rounded-pill
           bg-warning-soft px-2.5 py-1
           text-xs font-semibold text-warning"
>
    Mendekati Deadline
</span>
```

---

# 9.3 KPI Card

```html
<div
    class="rounded-lg border border-hairline
           bg-surface p-5 shadow-1"
>
    <p class="text-sm font-medium text-mute">
        Tender Aktif
    </p>

    <p class="mt-2 text-3xl font-bold
              tracking-[-0.03em] text-ink">
        12
    </p>

    <p class="mt-2 text-xs text-success">
        +14.2% dari periode sebelumnya
    </p>
</div>
```

---

# 9.4 Input

```html
<div>
    <label
        for="tender_name"
        class="mb-2 block text-[13px]
               font-semibold text-ink"
    >
        Nama Tender
    </label>

    <input
        id="tender_name"
        name="tender_name"
        type="text"
        placeholder="Masukkan nama tender"
        class="h-10 w-full rounded-md
               border border-hairline
               bg-white px-3
               text-sm text-ink
               placeholder:text-subtle
               transition
               focus:border-primary
               focus:outline-none
               focus:ring-4 focus:ring-primary-soft"
    >
</div>
```

---

# 9.5 Hero Headline

```html
<section class="relative overflow-hidden
                bg-white py-20">
    <div class="mx-auto max-w-8xl px-6">

        <p class="font-commons text-lg text-primary">
            SPU Business Management System
        </p>

        <h1
            class="mt-3 max-w-4xl
                   text-4xl font-bold
                   tracking-[-0.04em]
                   text-ink md:text-6xl"
        >
            Kelola Tender, Jasa, dan Perdagangan
            dalam satu sistem.
        </h1>

        <p class="mt-6 max-w-2xl
                  text-base leading-7
                  text-body md:text-lg">
            Satu pusat informasi untuk memantau proses bisnis,
            kontrak, transaksi, stok, invoice, dan pembayaran.
        </p>

    </div>
</section>
```

---

# 10. Iconography

Gunakan icon library yang konsisten, direkomendasikan:

**Lucide Icons**

Karakteristik:

- Outline.
- Stroke konsisten.
- Sederhana.
- Mudah dipahami.

Ukuran:

```text
12px → metadata
16px → button/menu
18px → navigation
20px → card
24px → section
```

Jangan mencampur banyak icon style.

---

# 11. Motion & Interaction

Gunakan animasi ringan:

```css
transition:
    color 150ms ease,
    background-color 150ms ease,
    border-color 150ms ease,
    box-shadow 150ms ease,
    transform 150ms ease;
```

Aturan:

- Button hover: 150ms.
- Dropdown: 150ms.
- Modal: 180–220ms.
- Page transition: maksimal 250ms.
- Hindari animasi dekoratif yang tidak memberikan informasi.

Untuk loading gunakan skeleton, bukan spinner pada seluruh halaman jika data membutuhkan waktu.

---

# 12. Accessibility

Minimal target:

```text
WCAG 2.1 AA
```

Wajib:

- Kontras teks memadai.
- Semua input memiliki label.
- Button memiliki accessible name.
- Focus state terlihat jelas.
- Jangan menjadikan warna sebagai satu-satunya indikator status.
- Gunakan icon + text untuk status penting.
- Navigasi dapat digunakan dengan keyboard.
- Modal memiliki focus management.
- Error form harus dapat dibaca screen reader.

Contoh:

```text
❌ Merah saja = gagal

✅ [Icon] Ditolak
```

---

# 13. Do's & Don'ts

## DO

1. Gunakan white space yang cukup.
2. Gunakan `Outfit` secara konsisten.
3. Gunakan blue sebagai primary action.
4. Gunakan status color sesuai semantic token.
5. Gunakan card dengan border tipis dan shadow halus.
6. Buat KPI mudah dipindai.
7. Gunakan hierarchy ukuran dan weight yang jelas.
8. Gunakan responsive layout sejak awal.
9. Gunakan loading, empty state, dan error state.
10. Gunakan icon yang konsisten.

## DON'T

1. Jangan menggunakan banyak warna brand sekaligus.
2. Jangan menggunakan gradient pada semua card.
3. Jangan membuat shadow terlalu gelap.
4. Jangan menggunakan rounded corner ekstrem pada semua elemen.
5. Jangan membuat dashboard penuh grafik tanpa prioritas informasi.
6. Jangan menggunakan font berbeda-beda.
7. Jangan menggunakan warna merah untuk informasi biasa.
8. Jangan menyembunyikan status penting hanya di tooltip.
9. Jangan membuat tabel terlalu padat di mobile.
10. Jangan menggunakan animasi yang mengganggu pekerjaan pengguna.

---

# 14. Empty, Loading, Error & Success State

## Empty State

```text
[Icon]

Belum ada data tender

Belum ada tender yang terdaftar pada periode ini.

[Tambah Tender]
```

## Loading

Gunakan skeleton:

```text
████████████
████████
████████████████
```

Jangan membuat halaman kosong saat data sedang dimuat.

## Error

```text
Terjadi kesalahan

Data tidak dapat dimuat.
Silakan coba kembali.

[Coba Lagi]
```

## Success Toast

```text
✓ Tender berhasil ditambahkan
```

Toast:

```text
Position: top-right
Radius: 12px
Shadow: shadow-3
Duration: 3–5 detik
```

---

# 15. Design Token CSS

Jika token perlu digunakan di luar Tailwind:

```css
:root {
    --color-primary: #2563EB;
    --color-primary-deep: #1D4ED8;
    --color-primary-soft: #EFF6FF;

    --color-canvas: #FFFFFF;
    --color-canvas-soft: #F8FAFC;

    --color-surface: #FFFFFF;
    --color-hairline: #E2E8F0;

    --color-ink: #0F172A;
    --color-body: #334155;
    --color-mute: #64748B;
    --color-subtle: #94A3B8;

    --color-success: #16A34A;
    --color-warning: #D97706;
    --color-error: #DC2626;
    --color-info: #0284C7;

    --radius-sm: 6px;
    --radius-md: 10px;
    --radius-lg: 14px;
    --radius-xl: 18px;
    --radius-pill: 999px;
}
```

---

# 16. Struktur Komponen Frontend

Direkomendasikan menggunakan struktur:

```text
resources/
├── views/
│   ├── layouts/
│   │   ├── app.blade.php
│   │   ├── guest.blade.php
│   │   └── components/
│   │
│   ├── components/
│   │   ├── ui/
│   │   │   ├── button.blade.php
│   │   │   ├── badge.blade.php
│   │   │   ├── input.blade.php
│   │   │   ├── select.blade.php
│   │   │   ├── modal.blade.php
│   │   │   ├── card.blade.php
│   │   │   └── table.blade.php
│   │   │
│   │   ├── dashboard/
│   │   │   ├── kpi-card.blade.php
│   │   │   ├── deadline-alert.blade.php
│   │   │   └── business-flow.blade.php
│   │   │
│   │   └── tender/
│   │       ├── pipeline.blade.php
│   │       └── tender-status.blade.php
│   │
│   ├── dashboard/
│   ├── tenders/
│   ├── contracts/
│   ├── services/
│   ├── procurement/
│   ├── inventory/
│   ├── sales/
│   ├── invoices/
│   ├── payments/
│   ├── reports/
│   └── settings/
│
├── css/
│   └── app.css
│
└── js/
    └── app.js
```

---

# 17. Final UI/UX Principle

Seluruh pengembangan frontend harus mengikuti prinsip:

```text
CLEAR
  ↓
CONSISTENT
  ↓
FAST
  ↓
DATA-FIRST
  ↓
PROFESSIONAL
```

Tujuan desain bukan sekadar membuat sistem terlihat modern, tetapi membuat pengguna PT Signal Panca Utama dapat:

- Menemukan data dengan cepat.
- Memahami status bisnis tanpa membaca banyak teks.
- Memproses transaksi dengan langkah minimal.
- Melihat risiko deadline.
- Memantau kontrak dan pekerjaan.
- Memantau stok.
- Mengetahui invoice dan pembayaran.
- Memahami performa bisnis melalui dashboard.

> **Design rule utama:** jika sebuah elemen visual tidak membantu pengguna memahami data, mengambil keputusan, atau menyelesaikan pekerjaan, elemen tersebut sebaiknya dihilangkan.


**Catatan Tambahan untuk AI:**
Pastikan arsitektur token menggunakan penamaan yang modern, bersih, dan mengikuti standar desain sistem profesional skala industri (seperti milik Vercel atau Stripe). Tuliskan respons dalam bahasa Indonesia yang teknis, rapi, dan mudah dibaca oleh developer frontend.
