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
    @vite('resources/js/app.js')
    <style>
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
            --navy: var(--color-ink);
            --blue: var(--color-primary);
            --blue-soft: var(--color-primary-soft);
            --teal: var(--color-success);
            --amber: var(--color-warning);
            --ink: var(--color-ink);
            --muted: var(--color-mute);
            --surface: var(--color-canvas-soft);
        }

        body { font-family: 'Outfit', sans-serif; background-color: var(--surface); color: var(--ink); }
        .bg-white { background-color: #FFFFFF !important; }
        .bg-slate-50 { background-color: var(--surface) !important; }
        .bg-blue-600 { background-color: var(--blue) !important; }
        .hover\:bg-blue-700:hover { background-color: var(--color-primary-deep) !important; }
        .bg-blue-100, .bg-blue-50 { background-color: var(--blue-soft) !important; }
        .text-slate-900, .text-slate-800 { color: var(--ink) !important; }
        .text-slate-700, .text-slate-600, .text-slate-500 { color: var(--muted) !important; }
        .text-blue-600, .text-blue-700, .text-blue-800 { color: var(--blue) !important; }
        .text-emerald-600 { color: var(--teal) !important; }
        .text-purple-600 { color: var(--teal) !important; }
        .text-amber-600 { color: var(--amber) !important; }
        .border-blue-200 { border-color: #DBEAFE !important; }
        .border-slate-200 { border-color: var(--color-hairline) !important; }
        .shadow-blue-600\/20, .shadow-blue-600\/25 { --tw-shadow-color: rgba(23, 105, 224, 0.22) !important; }
        .bg-slate-900 { background-color: var(--navy) !important; }
        .landing-action,
        .landing-menu-button {
            transition: background-color 150ms ease, color 150ms ease, border-color 150ms ease, box-shadow 150ms ease, transform 150ms ease, filter 150ms ease;
        }
        .landing-action:hover {
            transform: translateY(-2px);
        }
        .landing-action:active,
        .landing-menu-button:active {
            transform: translateY(0) scale(0.97);
            filter: brightness(0.9);
        }
        .landing-action:focus-visible,
        .landing-menu-button:focus-visible {
            outline: 3px solid rgba(23, 105, 224, 0.35);
            outline-offset: 3px;
        }
        .landing-menu-button { display: none; }
        .hero-modern {
            background: #f8fafc !important;
        }
        .hero-layout {
            display: grid;
            grid-template-columns: minmax(0, 1.05fr) minmax(360px, .95fr);
            align-items: center;
            gap: clamp(2rem, 6vw, 6rem);
            min-height: 650px;
            text-align: left;
        }
        .hero-copy { max-width: 650px; }
        .hero-copy h1 { max-width: 620px; margin-left: 0; margin-right: 0; font-size: clamp(2.8rem, 5.3vw, 5.25rem); line-height: .98; letter-spacing: -0.04em; }
        .hero-copy h1 span { display: block; margin-top: .8rem; color: var(--blue); }
        .hero-copy > p { max-width: 560px; margin-left: 0; margin-right: 0; color: #526579; font-size: 1.08rem; }
        .hero-actions { justify-content: flex-start; }
        .hero-visual {
            position: relative;
            min-height: 420px;
            padding: 1.25rem;
            border: 1px solid #dbe5ef;
            border-radius: 1.75rem;
            background: #eaf2ff;
            box-shadow: 0 28px 70px rgba(16, 42, 67, .12);
        }
        .hero-visual::before { position: absolute; inset: 2rem -1.5rem -1.5rem 2rem; z-index: 0; content: ''; border-radius: 1.75rem; background: #dcefe9; }
        .workflow-window { position: relative; z-index: 1; overflow: hidden; border: 1px solid #d9e3ed; border-radius: 1.1rem; background: #fff; box-shadow: 0 18px 40px rgba(16, 42, 67, .1); }
        .workflow-topbar { display: flex; align-items: center; justify-content: space-between; padding: .9rem 1rem; border-bottom: 1px solid #e7edf3; }
        .workflow-dots { display: flex; gap: .35rem; }
        .workflow-dots i { width: .5rem; height: .5rem; border-radius: 50%; background: #cbd6e1; }
        .workflow-dots i:first-child { background: var(--blue); }
        .workflow-topbar small { color: #728399; font-size: .68rem; font-weight: 700; }
        .workflow-content { display: grid; grid-template-columns: 1fr 1.25fr; min-height: 330px; }
        .workflow-sidebar { padding: 1.2rem .9rem; background: #f5f8fb; border-right: 1px solid #e7edf3; }
        .workflow-sidebar strong { display: block; margin-bottom: 1rem; color: var(--ink); font-size: .76rem; }
        .workflow-nav { display: grid; gap: .35rem; }
        .workflow-nav span { padding: .6rem .65rem; color: #718197; border-radius: .55rem; font-size: .65rem; font-weight: 600; }
        .workflow-nav span:first-child { color: var(--blue); background: #e4efff; }
        .workflow-main { padding: 1.25rem; }
        .workflow-main h3 { margin: 0; color: var(--ink); font-size: 1.1rem; font-weight: 800; }
        .workflow-main > p { margin: .3rem 0 1.1rem; color: #8290a0; font-size: .68rem; }
        .workflow-card { display: grid; grid-template-columns: auto 1fr auto; gap: .7rem; align-items: center; padding: .8rem; margin-bottom: .6rem; border: 1px solid #e6edf4; border-radius: .75rem; }
        .workflow-icon { display: grid; width: 2rem; height: 2rem; place-items: center; color: var(--blue); border-radius: .55rem; background: #e9f1ff; font-size: .75rem; }
        .workflow-card strong, .workflow-card small { display: block; }
        .workflow-card strong { color: var(--ink); font-size: .68rem; }
        .workflow-card small { margin-top: .2rem; color: #8794a4; font-size: .58rem; }
        .workflow-badge { padding: .3rem .45rem; color: var(--teal); border-radius: 999px; background: #e4f5ef; font-size: .55rem; font-weight: 800; }
        .hero-note { display: flex; align-items: center; gap: .6rem; margin-top: 1.2rem; color: #738297; font-size: .72rem; font-weight: 600; }
        .hero-note i { color: var(--teal); }
        #beranda .grid.max-w-4xl {
            position: relative;
            overflow: hidden;
            border-color: var(--color-hairline);
            box-shadow: 0 18px 45px rgba(15, 23, 42, .08);
        }
        #beranda .grid.max-w-4xl::before {
            position: absolute;
            inset: 0 0 auto;
            height: 3px;
            content: '';
            background: linear-gradient(90deg, var(--color-primary), #38BDF8, var(--color-success), var(--color-warning));
        }
        #beranda .grid.max-w-4xl > div { position: relative; min-height: 5.5rem; display: grid; align-content: center; }
        #beranda .grid.max-w-4xl > div + div { border-left: 1px solid var(--color-hairline); }
        #klien-slider { background: var(--color-canvas) !important; }
        #klien-slider .animate-marquee > div > div {
            border-color: var(--color-hairline);
            box-shadow: 0 8px 22px rgba(15, 23, 42, .045);
            transition: border-color 180ms ease, box-shadow 180ms ease, transform 180ms ease;
        }
        #klien-slider .animate-marquee > div > div:hover { border-color: #BFDBFE; box-shadow: 0 14px 30px rgba(37, 99, 235, .1); transform: translateY(-3px); }
        #kbli, #alur { background: var(--color-canvas-soft) !important; }
        #kbli .grid > div {
            position: relative;
            overflow: hidden;
            border-color: var(--color-hairline);
            border-radius: 14px;
            box-shadow: 0 6px 18px rgba(15, 23, 42, .035);
            transition: border-color 180ms ease, box-shadow 180ms ease, transform 180ms ease;
        }
        #kbli .grid > div::after { position: absolute; inset: auto 0 0; height: 3px; content: ''; background: var(--color-primary); transform: scaleX(0); transform-origin: left; transition: transform 180ms ease; }
        #kbli .grid > div:hover { border-color: #BFDBFE; box-shadow: 0 16px 30px rgba(15, 23, 42, .08); transform: translateY(-4px); }
        #kbli .grid > div:hover::after { transform: scaleX(1); }
        #domain { background: var(--color-canvas) !important; }
        #domain .grid > div {
            border-color: var(--color-hairline);
            border-radius: 14px;
            box-shadow: 0 8px 22px rgba(15, 23, 42, .045);
            transition: border-color 180ms ease, box-shadow 180ms ease, transform 180ms ease;
        }
        #domain .grid > div:hover { border-color: #BFDBFE; box-shadow: 0 18px 35px rgba(15, 23, 42, .09); transform: translateY(-5px); }
        #domain .grid > div:nth-child(2):hover { border-color: #93C5FD; }
        #domain .grid > div:nth-child(3):hover { border-color: #86EFAC; }
        #domain .grid > div:nth-child(4):hover { border-color: #C4B5FD; }
        #alur .grid > div {
            border-color: var(--color-hairline);
            border-radius: 14px;
            box-shadow: 0 6px 18px rgba(15, 23, 42, .035);
            transition: border-color 180ms ease, box-shadow 180ms ease, transform 180ms ease;
        }
        #alur .grid > div:hover { border-color: #BFDBFE; box-shadow: 0 14px 28px rgba(15, 23, 42, .08); transform: translateY(-3px); }
        @media (max-width: 640px) { #beranda .grid.max-w-4xl > div + div { border-left: 0; border-top: 1px solid var(--color-hairline); } }
        @media (max-width: 900px) { .hero-layout { grid-template-columns: 1fr; min-height: auto; padding-top: 2rem; } .hero-copy { max-width: 700px; } .hero-visual { max-width: 650px; width: 100%; margin: 0 auto; } }
        @media (max-width: 640px) { .landing-menu-button { display: block; } .hero-copy h1 { font-size: clamp(2.6rem, 13vw, 4rem); } .hero-copy > p { font-size: .98rem; } .hero-visual { min-height: 350px; padding: .7rem; border-radius: 1.2rem; } .hero-visual::before { inset: 1rem -.5rem -.5rem 1rem; border-radius: 1.2rem; } .workflow-content { grid-template-columns: 1fr; } .workflow-sidebar { display: none; } .workflow-main { padding: 1rem; } }
        
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
<body class="bg-white antialiased selection:bg-blue-600 selection:text-white" x-data="{ mobileMenu: false }" data-landing-page>

    <!-- Header Navigation -->
    <header class="fixed top-0 inset-x-0 z-50 bg-white/95 backdrop-blur-md border-b border-slate-200 transition-all shadow-sm" data-landing-header>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 min-h-20 py-3 flex flex-wrap items-center justify-between gap-x-6 gap-y-3">
            
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
            <nav class="order-last basis-full md:order-none md:basis-auto flex items-center gap-6 overflow-x-auto whitespace-nowrap text-sm font-semibold text-slate-600 pb-1 md:pb-0">
                <a href="#beranda" class="hover:text-blue-600 transition-colors">Beranda</a>
                <a href="#klien-slider" class="hover:text-blue-600 transition-colors">Klien Terpercaya</a>
                <a href="#kbli" class="hover:text-blue-600 transition-colors">Ruang Lingkup KBLI</a>
                <a href="#domain" class="hover:text-blue-600 transition-colors">Domain Bisnis</a>
                <a href="#alur" class="hover:text-blue-600 transition-colors">Alur Integrasi</a>
                <a href="#kontak" class="hover:text-blue-600 transition-colors">Kontak</a>
            </nav>

            <!-- Action Buttons -->
            <div class="hidden md:flex items-center space-x-3">
                <a href="{{ route('login') }}" class="landing-action px-4 py-2 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-50 font-semibold text-sm transition-all">
                    Masuk
                </a>
                <a href="{{ route('login') }}" class="landing-action px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm transition-all shadow-md shadow-blue-600/20 flex items-center gap-2">
                    <i class="fa-solid fa-right-to-bracket"></i> Portal Login
                </a>
            </div>

            <!-- Mobile Hamburger Button -->
            <button @click="mobileMenu = !mobileMenu" class="landing-menu-button text-slate-700 hover:text-blue-600 p-2">
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
                <a href="{{ route('login') }}" class="landing-action w-full py-2.5 text-center bg-blue-600 text-white font-semibold rounded-xl block text-sm shadow">Masuk Portal Login</a>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section id="beranda" class="hero-modern relative pt-32 pb-20 md:pt-40 md:pb-24 overflow-hidden">
        <div class="hero-layout max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="hero-copy">
                <h1 class="font-extrabold tracking-tight text-slate-900" data-hero-item>
                    Satu ruang kerja untuk bisnis yang terus bergerak
                    <span>Tender, jasa, dan perdagangan.</span>
                </h1>

            <!-- Subtitle -->
            <p class="text-base sm:text-lg text-slate-600 max-w-2xl mx-auto mb-10 leading-relaxed font-normal" data-hero-item>
                Platform manajemen internal perusahaan untuk menghubungkan alur pengadaan tender instansi, pelaksanaan pekerjaan jasa, stok persediaan barang, hingga invoice dan laporan bisnis.
            </p>

            <!-- Action Buttons -->
            <div class="hero-actions flex flex-col sm:flex-row items-center justify-center gap-4" data-hero-item>
                <a href="{{ route('login') }}" class="landing-action w-full sm:w-auto px-8 py-3.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm transition-all shadow-lg shadow-blue-600/25 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-right-to-bracket"></i> Masuk ke Portal Login
                </a>
                <a href="#domain" class="landing-action w-full sm:w-auto px-8 py-3.5 rounded-xl bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 font-semibold text-sm transition-all shadow-sm flex items-center justify-center gap-2">
                    <i class="fa-solid fa-layer-group"></i> Jelajahi Modul Sistem
                </a>
            </div>

                <div class="hero-note"><i class="fa-solid fa-circle-check"></i><span>Data lebih rapi. Keputusan lebih cepat. Tim lebih selaras.</span></div>
            </div>

            <div class="hero-visual" data-hero-item aria-label="Preview alur kerja SignalNiaga">
                <div class="workflow-window">
                    <div class="workflow-topbar"><div class="workflow-dots"><i></i><i></i><i></i></div><small>SignalNiaga Workspace</small></div>
                    <div class="workflow-content">
                        <aside class="workflow-sidebar"><strong>Workspace</strong><div class="workflow-nav"><span>Ringkasan</span><span>Pipeline Tender</span><span>Kontrak & Jasa</span><span>Persediaan</span><span>Keuangan</span></div></aside>
                        <div class="workflow-main">
                            <h3>Ringkasan operasional</h3>
                            <p>Senin, 26 September 2026</p>
                            <div class="workflow-card"><div class="workflow-icon"><i class="fa-solid fa-file-signature"></i></div><div><strong>Pipeline tender</strong><small>12 tender membutuhkan perhatian</small></div><span class="workflow-badge">Aktif</span></div>
                            <div class="workflow-card"><div class="workflow-icon"><i class="fa-solid fa-briefcase"></i></div><div><strong>Pekerjaan jasa</strong><small>8 kontrak sedang berjalan</small></div><span class="workflow-badge">On track</span></div>
                            <div class="workflow-card"><div class="workflow-icon"><i class="fa-solid fa-chart-line"></i></div><div><strong>Arus keuangan</strong><small>Invoice dan pembayaran terpantau</small></div><span class="workflow-badge">Terpantau</span></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Bar -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 max-w-4xl mx-auto mt-16 p-6 rounded-2xl bg-white border border-slate-200 shadow-xl shadow-slate-200/50 col-span-full">
                <div class="p-3 border-r border-slate-100 last:border-r-0" data-stat-card>
                    <div class="text-3xl font-extrabold text-blue-600">50+</div>
                    <div class="text-xs font-semibold text-slate-500 mt-1">Tender Dimenangkan</div>
                </div>
                <div class="p-3 border-r border-slate-100 last:border-r-0" data-stat-card>
                    <div class="text-3xl font-extrabold text-emerald-600">100+</div>
                    <div class="text-xs font-semibold text-slate-500 mt-1">Mitra Kerjasama</div>
                </div>
                <div class="p-3 border-r border-slate-100 last:border-r-0" data-stat-card>
                    <div class="text-3xl font-extrabold text-purple-600">Rp 25M+</div>
                    <div class="text-xs font-semibold text-slate-500 mt-1">Nilai Kontrak Terkelola</div>
                </div>
                <div class="p-3" data-stat-card>
                    <div class="text-3xl font-extrabold text-amber-600">99.8%</div>
                    <div class="text-xs font-semibold text-slate-500 mt-1">Ketepatan Waktu</div>
                </div>
            </div>

        </div>
    </section>

    <!-- Client Logo Marquee Slider Section (Persis Gaya pengadaan.com) -->
    <section id="klien-slider" class="py-16 bg-white border-y border-slate-200 overflow-hidden" data-reveal-section>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center mb-10" data-reveal-item>
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
    <section id="kbli" class="py-20 bg-slate-50 border-b border-slate-200" data-reveal-section>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-14" data-reveal-item>
                <span class="text-xs font-bold uppercase tracking-widest text-blue-600 block mb-2">Kualifikasi Resmi</span>
                <h2 class="text-3xl font-bold text-slate-900">Ruang Lingkup KBLI PT Signal Panca Utama</h2>
                <p class="text-sm text-slate-600 mt-2">Disesuaikan dengan Klasifikasi Baku Lapangan Usaha Indonesia resmi perusahaan.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-5" data-reveal-item>
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
    <section id="domain" class="py-24 bg-white" data-reveal-section>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-16" data-reveal-item>
                <span class="text-xs font-bold uppercase tracking-widest text-blue-600 block mb-2">Tiga Pillar Bisnis</span>
                <h2 class="text-3xl font-bold text-slate-900">Pengelolaan Bisnis Terpusat</h2>
                <p class="text-sm text-slate-600 mt-2">Menghubungkan seluruh proses dari pencarian tender hingga pelunasan pembayaran.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8" data-reveal-item>
                
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
    <section id="alur" class="py-20 bg-slate-50 border-t border-slate-200" data-reveal-section>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-14" data-reveal-item>
                <span class="text-xs font-bold uppercase tracking-widest text-blue-600 block mb-2">Proses Bisnis</span>
                <h2 class="text-3xl font-bold text-slate-900">Alur Bisnis Terintegrasi End-to-End</h2>
                <p class="text-sm text-slate-600 mt-2">Seluruh transaksi terhubung untuk transparansi & auditabilitas data.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 text-center" data-reveal-item>
                
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
                <a href="{{ route('login') }}" class="landing-action px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl inline-block shadow-md">
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
