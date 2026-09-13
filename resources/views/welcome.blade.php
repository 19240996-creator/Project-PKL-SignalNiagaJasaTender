<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SignalNiagaJasaTender — PT Signal Panca Utama</title>
    <!-- Google Fonts: Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Outfit', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            DEFAULT: '#2563EB',
                            hover: '#1D4ED8',
                            soft: '#EFF6FF',
                        }
                    }
                }
            }
        }
    </script>
    <!-- FontAwesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Alpine.js CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body { font-family: 'Outfit', sans-serif; background-color: #FFFFFF; color: #0F172A; }
        
        @keyframes marquee {
            0% { transform: translateX(0%); }
            100% { transform: translateX(-50%); }
        }
        .animate-marquee {
            display: flex;
            width: max-content;
            animation: marquee 30s linear infinite;
        }
        .animate-marquee:hover {
            animation-play-state: paused;
        }
    </style>
</head>
<body class="bg-white antialiased selection:bg-blue-600 selection:text-white" x-data="{ mobileMenu: false }">

    <!-- Header Navigation -->
    <header class="fixed top-0 inset-x-0 z-50 bg-white/95 backdrop-blur-md border-b border-slate-200 transition-all shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            
            <!-- Brand Logo -->
            <a href="#" class="flex items-center space-x-3 group">
                <div class="w-10 h-10 rounded-xl bg-blue-600 flex items-center justify-center font-bold text-white text-xl shadow-md shadow-blue-600/20 group-hover:scale-105 transition-transform">
                    S
                </div>
                <div>
                    <span class="font-bold text-xl tracking-tight text-slate-900 block leading-none">SignalNiaga</span>
                    <span class="text-[10px] text-blue-600 font-bold tracking-wider uppercase">PT Signal Panca Utama</span>
                </div>
            </a>

            <!-- Desktop Nav Links -->
            <nav class="hidden md:flex items-center space-x-8 text-sm font-semibold text-slate-600">
                <a href="#beranda" class="hover:text-blue-600 transition-colors">Beranda</a>
                <a href="#klien-slider" class="hover:text-blue-600 transition-colors">Klien Terpercaya</a>
                <a href="#kbli" class="hover:text-blue-600 transition-colors">Ruang Lingkup KBLI</a>
                <a href="#domain" class="hover:text-blue-600 transition-colors">Domain Bisnis</a>
                <a href="#alur" class="hover:text-blue-600 transition-colors">Alur Integrasi</a>
                <a href="#kontak" class="hover:text-blue-600 transition-colors">Kontak</a>
            </nav>

            <!-- Action Buttons -->
            <div class="hidden md:flex items-center space-x-3">
                <a href="{{ route('login') }}" class="px-4 py-2 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-50 font-semibold text-sm transition-all">
                    Masuk
                </a>
                <a href="{{ route('login') }}" class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm transition-all shadow-md shadow-blue-600/20 flex items-center gap-2">
                    <i class="fa-solid fa-right-to-bracket"></i> Portal Login
                </a>
            </div>

            <!-- Mobile Hamburger Button -->
            <button @click="mobileMenu = !mobileMenu" class="md:hidden text-slate-700 hover:text-blue-600 p-2">
                <i class="fa-solid text-xl" :class="mobileMenu ? 'fa-xmark' : 'fa-bars'"></i>
            </button>
        </div>

        <!-- Mobile Menu Dropdown -->
        <div x-show="mobileMenu" class="md:hidden bg-white border-b border-slate-200 px-6 py-4 space-y-3" x-cloak>
            <a href="#beranda" @click="mobileMenu = false" class="block text-sm font-semibold text-slate-700 hover:text-blue-600">Beranda</a>
            <a href="#klien-slider" @click="mobileMenu = false" class="block text-sm font-semibold text-slate-700 hover:text-blue-600">Klien Terpercaya</a>
            <a href="#kbli" @click="mobileMenu = false" class="block text-sm font-semibold text-slate-700 hover:text-blue-600">Ruang Lingkup KBLI</a>
            <a href="#domain" @click="mobileMenu = false" class="block text-sm font-semibold text-slate-700 hover:text-blue-600">Domain Bisnis</a>
            <a href="#alur" @click="mobileMenu = false" class="block text-sm font-semibold text-slate-700 hover:text-blue-600">Alur Integrasi</a>
            <div class="pt-3 border-t border-slate-100 flex flex-col gap-2">
                <a href="{{ route('login') }}" class="w-full py-2.5 text-center bg-blue-600 text-white font-semibold rounded-xl block text-sm shadow">Masuk Portal Login</a>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section id="beranda" class="relative pt-32 pb-20 md:pt-40 md:pb-24 bg-gradient-to-b from-blue-50/60 via-slate-50 to-white overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
            
            <!-- Badge -->
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-blue-100/80 border border-blue-200 text-xs font-semibold text-blue-800 mb-6 shadow-sm">
                <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                <span>PT Signal Panca Utama — System Information Enterprise</span>
            </div>

            <!-- Main Title -->
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-slate-900 leading-tight max-w-4xl mx-auto mb-6">
                Portal Pengadaan Bisnis Terintegrasi<br>
                <span class="text-blue-600">Tender &bull; Jasa &bull; Perdagangan Barang</span>
            </h1>

            <!-- Subtitle -->
            <p class="text-base sm:text-lg text-slate-600 max-w-2xl mx-auto mb-10 leading-relaxed font-normal">
                Platform manajemen internal perusahaan untuk menghubungkan alur pengadaan tender instansi, pelaksanaan pekerjaan jasa, stok persediaan barang, hingga invoice dan laporan bisnis.
            </p>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('login') }}" class="w-full sm:w-auto px-8 py-3.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm transition-all shadow-lg shadow-blue-600/25 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-right-to-bracket"></i> Masuk ke Portal Login
                </a>
                <a href="#domain" class="w-full sm:w-auto px-8 py-3.5 rounded-xl bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 font-semibold text-sm transition-all shadow-sm flex items-center justify-center gap-2">
                    <i class="fa-solid fa-layer-group"></i> Jelajahi Modul Sistem
                </a>
            </div>

            <!-- Stats Bar -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 max-w-4xl mx-auto mt-16 p-6 rounded-2xl bg-white border border-slate-200 shadow-xl shadow-slate-200/50">
                <div class="p-3 border-r border-slate-100 last:border-r-0">
                    <div class="text-3xl font-extrabold text-blue-600">50+</div>
                    <div class="text-xs font-semibold text-slate-500 mt-1">Tender Dimenangkan</div>
                </div>
                <div class="p-3 border-r border-slate-100 last:border-r-0">
                    <div class="text-3xl font-extrabold text-emerald-600">100+</div>
                    <div class="text-xs font-semibold text-slate-500 mt-1">Mitra Kerjasama</div>
                </div>
                <div class="p-3 border-r border-slate-100 last:border-r-0">
                    <div class="text-3xl font-extrabold text-purple-600">Rp 25M+</div>
                    <div class="text-xs font-semibold text-slate-500 mt-1">Nilai Kontrak Terkelola</div>
                </div>
                <div class="p-3">
                    <div class="text-3xl font-extrabold text-amber-600">99.8%</div>
                    <div class="text-xs font-semibold text-slate-500 mt-1">Ketepatan Waktu</div>
                </div>
            </div>

        </div>
    </section>

    <!-- Client Logo Marquee Slider Section (Persis Gaya pengadaan.com) -->
    <section id="klien-slider" class="py-16 bg-white border-y border-slate-200 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center mb-10">
            <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                PT Signal Panca Utama telah dipercaya oleh berbagai klien dari
            </h2>
        </div>

        <!-- Infinite Auto-Sliding Marquee Track -->
        <div class="relative w-full overflow-hidden flex">
            <!-- Left & Right Fade Shadows -->
            <div class="absolute left-0 top-0 bottom-0 w-24 bg-gradient-to-r from-white to-transparent z-10 pointer-events-none"></div>
            <div class="absolute right-0 top-0 bottom-0 w-24 bg-gradient-to-l from-white to-transparent z-10 pointer-events-none"></div>

            <div class="flex space-x-6 animate-marquee whitespace-nowrap py-4">
                @if(isset($partnerLogos) && count($partnerLogos) > 0)
                    <!-- Set 1 of Client Logos -->
                    <div class="flex items-center space-x-6 shrink-0">
                        @foreach($partnerLogos as $logo)
                            <div class="px-7 py-5 bg-white rounded-2xl border border-slate-200/90 shadow-sm hover:shadow-lg transition-all flex items-center gap-4 min-w-[240px] md:min-w-[270px] h-20 justify-center">
                                @if($logo->logo_path)
                                    <img src="{{ asset('storage/' . $logo->logo_path) }}" alt="{{ $logo->name }}" class="h-11 md:h-12 max-h-12 w-auto max-w-[160px] object-contain shrink-0">
                                @else
                                    @if(str_starts_with($logo->badge_color ?? '', '#'))
                                        <div class="px-3.5 py-1.5 rounded-xl text-white font-black text-sm md:text-base tracking-wider shadow-xs shrink-0" style="background-color: {{ $logo->badge_color }}">
                                            {{ $logo->badge_text ?? strtoupper(substr($logo->name, 0, 4)) }}
                                        </div>
                                    @elseif(str_starts_with($logo->badge_color ?? '', 'text-'))
                                        <div class="px-3.5 py-1.5 rounded-xl bg-slate-100 font-black text-sm md:text-base tracking-wider shadow-xs shrink-0 {{ $logo->badge_color }}">
                                            {{ $logo->badge_text ?? strtoupper(substr($logo->name, 0, 4)) }}
                                        </div>
                                    @else
                                        <div class="px-3.5 py-1.5 rounded-xl bg-blue-600 text-white font-black text-sm md:text-base tracking-wider shadow-xs shrink-0">
                                            {{ $logo->badge_text ?? strtoupper(substr($logo->name, 0, 4)) }}
                                        </div>
                                    @endif
                                @endif
                                <span class="text-sm font-bold text-slate-800 tracking-tight leading-snug">{{ $logo->name }}</span>
                            </div>
                        @endforeach
                    </div>

                    <!-- Set 2 of Client Logos (Duplicate for Seamless Infinite Marquee Loop) -->
                    <div class="flex items-center space-x-6 shrink-0">
                        @foreach($partnerLogos as $logo)
                            <div class="px-7 py-5 bg-white rounded-2xl border border-slate-200/90 shadow-sm hover:shadow-lg transition-all flex items-center gap-4 min-w-[240px] md:min-w-[270px] h-20 justify-center">
                                @if($logo->logo_path)
                                    <img src="{{ asset('storage/' . $logo->logo_path) }}" alt="{{ $logo->name }}" class="h-11 md:h-12 max-h-12 w-auto max-w-[160px] object-contain shrink-0">
                                @else
                                    @if(str_starts_with($logo->badge_color ?? '', '#'))
                                        <div class="px-3.5 py-1.5 rounded-xl text-white font-black text-sm md:text-base tracking-wider shadow-xs shrink-0" style="background-color: {{ $logo->badge_color }}">
                                            {{ $logo->badge_text ?? strtoupper(substr($logo->name, 0, 4)) }}
                                        </div>
                                    @elseif(str_starts_with($logo->badge_color ?? '', 'text-'))
                                        <div class="px-3.5 py-1.5 rounded-xl bg-slate-100 font-black text-sm md:text-base tracking-wider shadow-xs shrink-0 {{ $logo->badge_color }}">
                                            {{ $logo->badge_text ?? strtoupper(substr($logo->name, 0, 4)) }}
                                        </div>
                                    @else
                                        <div class="px-3.5 py-1.5 rounded-xl bg-blue-600 text-white font-black text-sm md:text-base tracking-wider shadow-xs shrink-0">
                                            {{ $logo->badge_text ?? strtoupper(substr($logo->name, 0, 4)) }}
                                        </div>
                                    @endif
                                @endif
                                <span class="text-sm font-bold text-slate-800 tracking-tight leading-snug">{{ $logo->name }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <!-- Fallback Set 1 -->
                    <div class="flex items-center space-x-6 shrink-0">
                        <div class="px-7 py-5 bg-white rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4 min-w-[240px] h-20 justify-center">
                            <span class="font-black text-red-600 text-3xl tracking-tighter">HK</span>
                            <span class="text-sm font-bold text-slate-800">Hutama Karya</span>
                        </div>
                        <div class="px-7 py-5 bg-white rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4 min-w-[240px] h-20 justify-center">
                            <div class="w-9 h-9 rounded-full bg-amber-500 text-white font-black text-sm flex items-center justify-center">A</div>
                            <span class="text-sm font-bold text-slate-800">Brantas Abipraya</span>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <!-- Ruang Lingkup KBLI Section -->
    <section id="kbli" class="py-20 bg-slate-50 border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-14">
                <span class="text-xs font-bold uppercase tracking-widest text-blue-600 block mb-2">Kualifikasi Resmi</span>
                <h2 class="text-3xl font-bold text-slate-900">Ruang Lingkup KBLI PT Signal Panca Utama</h2>
                <p class="text-sm text-slate-600 mt-2">Disesuaikan dengan Klasifikasi Baku Lapangan Usaha Indonesia resmi perusahaan.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-5">
                <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-sm hover:border-blue-500 transition group">
                    <span class="px-2.5 py-1 rounded-md bg-blue-50 text-blue-700 font-mono font-bold text-xs">KBLI 46100</span>
                    <h3 class="font-bold text-slate-900 text-sm mt-3 mb-1 group-hover:text-blue-600 transition">Perdagangan Balas Jasa (Fee)</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">Perdagangan besar atas dasar balas jasa atau kontrak kerja.</p>
                </div>

                <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-sm hover:border-blue-500 transition group">
                    <span class="px-2.5 py-1 rounded-md bg-blue-50 text-blue-700 font-mono font-bold text-xs">KBLI 46422</span>
                    <h3 class="font-bold text-slate-900 text-sm mt-3 mb-1 group-hover:text-blue-600 transition">Percetakan & Penerbitan</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">Perdagangan besar barang percetakan berbagai bentuk.</p>
                </div>

                <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-sm hover:border-blue-500 transition group">
                    <span class="px-2.5 py-1 rounded-md bg-blue-50 text-blue-700 font-mono font-bold text-xs">KBLI 46499</span>
                    <h3 class="font-bold text-slate-900 text-sm mt-3 mb-1 group-hover:text-blue-600 transition">Perlengkapan Rumah Tangga</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">Perdagangan besar perlengkapan & perabotan kantor/rumah.</p>
                </div>

                <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-sm hover:border-blue-500 transition group">
                    <span class="px-2.5 py-1 rounded-md bg-blue-50 text-blue-700 font-mono font-bold text-xs">KBLI 46511</span>
                    <h3 class="font-bold text-slate-900 text-sm mt-3 mb-1 group-hover:text-blue-600 transition">Komputer & IT Equipment</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">Perdagangan besar komputer, laptop, server, & perangkat IT.</p>
                </div>

                <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-sm hover:border-blue-500 transition group">
                    <span class="px-2.5 py-1 rounded-md bg-blue-50 text-blue-700 font-mono font-bold text-xs">KBLI 46900</span>
                    <h3 class="font-bold text-slate-900 text-sm mt-3 mb-1 group-hover:text-blue-600 transition">Perdagangan Macam Barang</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">Perdagangan besar berbagai macam barang pengadaan umum.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Tiga Domain Utama Section -->
    <section id="domain" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <span class="text-xs font-bold uppercase tracking-widest text-blue-600 block mb-2">Tiga Pillar Bisnis</span>
                <h2 class="text-3xl font-bold text-slate-900">Pengelolaan Bisnis Terpusat</h2>
                <p class="text-sm text-slate-600 mt-2">Menghubungkan seluruh proses dari pencarian tender hingga pelunasan pembayaran.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                
                <!-- Domain 1: Tender -->
                <div class="p-8 rounded-3xl bg-slate-50 border border-slate-200 hover:border-blue-500 transition-all flex flex-col justify-between shadow-sm hover:shadow-md">
                    <div>
                        <div class="w-14 h-14 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-2xl mb-6">
                            <i class="fa-solid fa-trophy"></i>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-3">1. Manajemen Tender</h3>
                        <p class="text-xs text-slate-600 leading-relaxed mb-6">
                            Pencarian tender instansi LPSE/BUMN, penyiapan dokumen penawaran, tracking pipeline (Ditemukan &rarr; Evaluasi &rarr; Menang/Kalah), dan pemicu kontrak.
                        </p>
                        <ul class="space-y-2.5 text-xs text-slate-700 font-medium">
                            <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-blue-600"></i> Monitoring pipeline tender aktif</li>
                            <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-blue-600"></i> Peringatan reminder deadline</li>
                            <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-blue-600"></i> Konversi otomatis tender menang ke kontrak</li>
                        </ul>
                    </div>
                </div>

                <!-- Domain 2: Jasa -->
                <div class="p-8 rounded-3xl bg-slate-50 border border-slate-200 hover:border-emerald-500 transition-all flex flex-col justify-between shadow-sm hover:shadow-md">
                    <div>
                        <div class="w-14 h-14 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold text-2xl mb-6">
                            <i class="fa-solid fa-briefcase"></i>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-3">2. Manajemen Jasa</h3>
                        <p class="text-xs text-slate-600 leading-relaxed mb-6">
                            Database Klien, pembuatan penawaran, kontrak kerja jasa, pencatatan persentase komisi fee %, tracking progress pekerjaan (%), dan penerbitan tagihan termin.
                        </p>
                        <ul class="space-y-2.5 text-xs text-slate-700 font-medium">
                            <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-emerald-600"></i> Pengelolaan kontrak & fee komisi</li>
                            <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-emerald-600"></i> Tracking progress (%) pelaksanaan job</li>
                            <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-emerald-600"></i> Penerbitan invoice tagihan jasa</li>
                        </ul>
                    </div>
                </div>

                <!-- Domain 3: Perdagangan -->
                <div class="p-8 rounded-3xl bg-slate-50 border border-slate-200 hover:border-purple-500 transition-all flex flex-col justify-between shadow-sm hover:shadow-md">
                    <div>
                        <div class="w-14 h-14 rounded-2xl bg-purple-100 text-purple-600 flex items-center justify-center font-bold text-2xl mb-6">
                            <i class="fa-solid fa-cart-shopping"></i>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-3">3. Perdagangan & Pengadaan</h3>
                        <p class="text-xs text-slate-600 leading-relaxed mb-6">
                            Katalog produk & SKU, supplier mitra, pengadaan stok barang (`StockMovement IN`), transaksi penjualan (`StockMovement OUT`), dan otomatisasi invoice.
                        </p>
                        <ul class="space-y-2.5 text-xs text-slate-700 font-medium">
                            <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-purple-600"></i> Pengadaan barang otomatis +Stok</li>
                            <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-purple-600"></i> Validasi kecukupan stok saat penjualan</li>
                            <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-purple-600"></i> Indikator stok minimum warning</li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Alur Integrasi Flowchart Section -->
    <section id="alur" class="py-20 bg-slate-50 border-t border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-14">
                <span class="text-xs font-bold uppercase tracking-widest text-blue-600 block mb-2">Proses Bisnis</span>
                <h2 class="text-3xl font-bold text-slate-900">Alur Bisnis Terintegrasi End-to-End</h2>
                <p class="text-sm text-slate-600 mt-2">Seluruh transaksi terhubung untuk transparansi & auditabilitas data.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 text-center">
                
                <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm relative">
                    <div class="w-9 h-9 rounded-full bg-blue-600 text-white font-bold text-sm flex items-center justify-center mx-auto mb-4 shadow-md shadow-blue-600/30">1</div>
                    <h4 class="font-bold text-slate-900 text-base mb-1">Tender Menang</h4>
                    <p class="text-xs text-slate-500 leading-relaxed">Tender dimenangkan dan dikonversi otomatis menjadi Kontrak Jasa.</p>
                </div>

                <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm relative">
                    <div class="w-9 h-9 rounded-full bg-indigo-600 text-white font-bold text-sm flex items-center justify-center mx-auto mb-4 shadow-md shadow-indigo-600/30">2</div>
                    <h4 class="font-bold text-slate-900 text-base mb-1">Pengadaan Barang</h4>
                    <p class="text-xs text-slate-500 leading-relaxed">Barang diterima dari supplier langsung menambah stok persediaan.</p>
                </div>

                <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm relative">
                    <div class="w-9 h-9 rounded-full bg-purple-600 text-white font-bold text-sm flex items-center justify-center mx-auto mb-4 shadow-md shadow-purple-600/30">3</div>
                    <h4 class="font-bold text-slate-900 text-base mb-1">Penjualan & Stok</h4>
                    <p class="text-xs text-slate-500 leading-relaxed">Transaksi penjualan memvalidasi & mengurangi stok secara akurat.</p>
                </div>

                <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm relative">
                    <div class="w-9 h-9 rounded-full bg-emerald-600 text-white font-bold text-sm flex items-center justify-center mx-auto mb-4 shadow-md shadow-emerald-600/30">4</div>
                    <h4 class="font-bold text-slate-900 text-base mb-1">Invoice & Pelunasan</h4>
                    <p class="text-xs text-slate-500 leading-relaxed">Tagihan diterbitkan & pembayaran terupdate otomatis dalam laporan.</p>
                </div>

            </div>
        </div>
    </section>

    <!-- Footer Section -->
    <footer id="kontak" class="bg-slate-900 text-slate-400 text-xs py-14 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-4 gap-8">
            <div class="md:col-span-2 space-y-4">
                <div class="flex items-center space-x-3">
                    <div class="w-9 h-9 rounded-xl bg-blue-600 flex items-center justify-center font-bold text-white text-base">S</div>
                    <span class="font-bold text-xl text-white">PT Signal Panca Utama</span>
                </div>
                <p class="max-w-md leading-relaxed text-slate-400">
                    Sistem Manajemen Bisnis Internal terintegrasi untuk Tender, Jasa, dan Perdagangan Barang. Memusatkan data perusahaan untuk transparansi & performa bisnis.
                </p>
                <div class="text-slate-400 space-y-1 font-medium">
                    <div><i class="fa-solid fa-location-dot text-blue-500"></i> Jl Rubaya Buher SPU Mansion Kavling CahayaKarangpawitan, Kec. Karawang Bar., Karawang, Jawa Barat 41315</div>
                    <div><i class="fa-solid fa-phone text-blue-500"></i> (021) 7890-1234 | Email: info@signalpanca.co.id</div>
                </div>
            </div>

            <div>
                <h4 class="font-bold text-white text-sm mb-3">Navigasi Portal</h4>
                <ul class="space-y-2 text-slate-400 font-medium">
                    <li><a href="#beranda" class="hover:text-blue-400">Beranda</a></li>
                    <li><a href="#klien-slider" class="hover:text-blue-400">Klien Terpercaya</a></li>
                    <li><a href="#kbli" class="hover:text-blue-400">Ruang Lingkup KBLI</a></li>
                    <li><a href="#domain" class="hover:text-blue-400">Domain Bisnis</a></li>
                </ul>
            </div>

            <div>
                <h4 class="font-bold text-white text-sm mb-3">Akses Sistem</h4>
                <p class="mb-3 text-slate-400">Masuk ke dalam portal manajemen internal perusahaan.</p>
                <a href="{{ route('login') }}" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl inline-block shadow-md">
                    Masuk Portal Login &rarr;
                </a>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 border-t border-slate-800 mt-10 pt-6 text-center text-slate-500">
            &copy; {{ date('Y') }} PT Signal Panca Utama. All Rights Reserved. Enterprise SaaS Web Application.
        </div>
    </footer>

</body>
</html>
