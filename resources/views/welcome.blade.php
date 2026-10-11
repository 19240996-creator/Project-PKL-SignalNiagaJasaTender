<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PT Signal Panca Utama: Tender, Jasa & Perdagangan</title>
    <meta name="description" content="PT Signal Panca Utama: Perusahaan perdagangan, jasa, dan pengadaan tender terpercaya di Karawang, Jawa Barat. Mitra bisnis BUMN dan instansi pemerintah.">
    <link rel="icon" type="image/png" href="{{ asset('images/logo-icon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite('resources/js/app.js')
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        html {
            scroll-behavior: smooth;
        }

        section[id], footer[id] {
            scroll-margin-top: 80px;
        }

        :root {
            --navy: #0F172A;
            --navy-light: #1E293B;
            --blue: #2563EB;
            --blue-dark: #1D4ED8;
            --blue-muted: #0EA5E9;
            --blue-soft: #EFF6FF;
            --blue-pale: #DBEAFE;
            --teal: #16A34A;
            --amber: #D97706;
            --ink: #0F172A;
            --body: #334155;
            --mute: #64748B;
            --subtle: #94A3B8;
            --hairline: #E2E8F0;
            --hairline-strong: #CBD5E1;
            --surface: #F8FAFC;
            --surface-soft: #F1F5F9;
            --canvas: #FFFFFF;
            --radius: 14px;
            --radius-lg: 18px;
            --shadow-sm: 0 1px 2px rgba(15,23,42,.05);
            --shadow-md: 0 2px 6px rgba(15,23,42,.06), 0 1px 2px rgba(15,23,42,.04);
            --shadow-lg: 0 8px 20px rgba(15,23,42,.08), 0 2px 6px rgba(15,23,42,.05);
        }

        body {
            font-family: 'Outfit', ui-sans-serif, system-ui, sans-serif;
            background: var(--canvas);
            color: var(--ink);
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        ::selection { background: var(--blue); color: #fff; }

        img { display: block; max-width: 100%; }

        a { color: inherit; text-decoration: none; }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 24px;
        }

        /* ─── HEADER ─────────────────────────────────────── */
        .site-header {
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 100;
            background: rgba(255,255,255,.92);
            backdrop-filter: blur(16px) saturate(1.6);
            -webkit-backdrop-filter: blur(16px) saturate(1.6);
            border-bottom: 1px solid var(--hairline);
            transition: box-shadow .3s ease;
        }
        .site-header.scrolled { box-shadow: 0 1px 8px rgba(0,0,0,.08); }
        .header-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 72px;
        }
        .brand { display: flex; align-items: center; gap: 12px; }
        .brand img { height: 38px; width: auto; }
        .brand-text { line-height: 1.2; }
        .brand-name { font-weight: 800; font-size: 17.5px; color: var(--ink); letter-spacing: -.02em; }
        .brand-sub { font-size: 11px; font-weight: 600; color: var(--blue); letter-spacing: .02em; }

        /* Capsule Pill Navbar */
        .nav-links {
            display: flex;
            align-items: center;
            gap: 4px;
            background: #E8EEF5;
            padding: 5px 6px;
            border-radius: 999px;
            border: 1px solid #CBD5E1;
            box-shadow: inset 0 1px 2px rgba(15, 23, 42, 0.05);
        }
        .nav-links a {
            font-size: 13.5px;
            font-weight: 500;
            color: #334155;
            padding: 7px 18px;
            border-radius: 999px;
            transition: all .2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            line-height: 1.2;
        }
        .nav-links a:hover {
            color: #0F172A;
            background: rgba(255, 255, 255, 0.45);
        }
        .nav-links a.active {
            background: #FFFFFF;
            color: #1D4ED8;
            font-weight: 700;
            border: 1px solid rgba(203, 213, 225, 0.8);
            box-shadow: 0 2px 6px -1px rgba(15, 23, 42, 0.12), 0 1px 2px rgba(15, 23, 42, 0.06);
        }
        .header-actions { display: flex; align-items: center; }
        .btn-portal {
            padding: 8px 20px;
            font-size: 13.5px;
            border-radius: 999px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-family: inherit;
            font-weight: 600;
            font-size: 14px;
            border: none;
            cursor: pointer;
            border-radius: 10px;
            padding: 10px 22px;
            transition: color 150ms ease, background-color 150ms ease, border-color 150ms ease, box-shadow 150ms ease, transform 150ms ease;
            text-decoration: none;
        }
        .btn-ghost {
            background: transparent;
            color: var(--mute);
            border: 1px solid var(--hairline);
        }
        .btn-ghost:hover { background: var(--surface); color: var(--ink); border-color: #d1d5db; }
        .btn-primary {
            background: var(--blue);
            color: #fff;
            box-shadow: 0 2px 8px rgba(37,99,235,.25);
        }
        .btn-primary:hover { background: var(--blue-dark); transform: translateY(-1px); box-shadow: 0 4px 14px rgba(37,99,235,.3); }
        .btn-primary:active { transform: translateY(0); }
        .btn-lg { padding: 14px 32px; font-size: 15px; border-radius: 999px; }
        .btn-white {
            background: #fff;
            color: var(--ink);
            box-shadow: var(--shadow-sm);
        }
        .btn-white:hover { background: var(--surface); transform: translateY(-1px); }

        .mobile-toggle {
            display: none;
            background: none; border: none; cursor: pointer;
            font-size: 22px; color: var(--ink); padding: 8px;
        }
        .mobile-menu {
            display: none;
            position: absolute;
            top: 72px; left: 0; right: 0;
            background: #fff;
            border-bottom: 1px solid var(--hairline);
            padding: 16px 24px;
            box-shadow: var(--shadow-md);
        }
        .mobile-menu.active { display: block; }
        .mobile-menu a {
            display: block;
            padding: 12px 0;
            font-size: 15px;
            font-weight: 500;
            color: var(--body);
            border-bottom: 1px solid var(--hairline);
        }
        .mobile-menu a:last-child { border: none; }
        .mobile-menu .btn { width: 100%; margin-top: 12px; }

        @media (max-width: 900px) {
            .nav-links, .header-actions { display: none; }
            .mobile-toggle { display: block; }
        }

        /* ─── HERO ─────────────────────────────────────── */
        .hero {
            position: relative;
            min-height: 92vh;
            display: flex;
            align-items: center;
            overflow: hidden;
            padding-top: 72px;
        }
        .hero-bg {
            position: absolute;
            inset: 0;
        }
        .hero-bg img {
            width: 100%; height: 100%;
            object-fit: cover;
            object-position: center 30%;
        }
        .hero-bg::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(15,23,42,.88) 0%, rgba(30,41,59,.75) 40%, rgba(37,99,235,.45) 100%);
        }
        .hero-content {
            position: relative;
            z-index: 2;
            max-width: 680px;
            padding: 80px 0;
        }
        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 16px;
            border-radius: 999px;
            background: rgba(255,255,255,.12);
            border: 1px solid rgba(255,255,255,.18);
            backdrop-filter: blur(8px);
            color: rgba(255,255,255,.9);
            font-size: 12px;
            font-weight: 600;
            letter-spacing: .03em;
            margin-bottom: 28px;
        }
        .hero-badge i { color: #60A5FA; font-size: 10px; }
        .hero h1 {
            font-size: clamp(2.6rem, 5vw, 3.5rem);
            font-weight: 700;
            color: #fff;
            line-height: 1.1;
            letter-spacing: -.04em;
            margin-bottom: 20px;
        }
        .hero h1 em {
            font-style: normal;
            color: #60A5FA;
        }
        .hero-desc {
            font-size: 17px;
            line-height: 1.7;
            color: rgba(255,255,255,.75);
            max-width: 540px;
            margin-bottom: 36px;
        }
        .hero-actions { display: flex; flex-wrap: wrap; gap: 14px; margin-bottom: 40px; }
        .hero-stats {
            display: flex;
            flex-wrap: wrap;
            gap: 36px;
            padding-top: 32px;
            border-top: 1px solid rgba(255,255,255,.12);
        }
        .hero-stat { text-align: left; }
        .hero-stat-value {
            font-size: 28px;
            font-weight: 700;
            color: #fff;
            letter-spacing: -.03em;
        }
        .hero-stat-label {
            font-size: 12px;
            font-weight: 500;
            color: rgba(255,255,255,.5);
            margin-top: 2px;
        }

        @media (max-width: 640px) {
            .hero { min-height: 85vh; }
            .hero-content { padding: 48px 0; }
            .hero-stats { gap: 24px; }
        }

        /* ─── SECTION SHARED ─────────────────────────────── */
        .section { padding: 96px 0; }
        .section-alt { background: var(--surface); }
        .section-dark { background: var(--navy); color: #fff; }
        .section-label {
            font-size: 12px;
            font-weight: 700;
            letter-spacing: .1em;
            text-transform: uppercase;
            color: var(--blue);
            margin-bottom: 12px;
        }
        .section-dark .section-label { color: #60A5FA; }
        .section-title {
            font-size: clamp(1.75rem, 3vw, 2.5rem);
            font-weight: 700;
            letter-spacing: -.025em;
            line-height: 1.15;
            margin-bottom: 12px;
        }
        .section-subtitle {
            font-size: 16px;
            color: var(--mute);
            max-width: 560px;
            line-height: 1.6;
        }
        .section-dark .section-subtitle { color: rgba(255,255,255,.55); }
        .section-header { margin-bottom: 56px; }
        .section-header-center { text-align: center; }
        .section-header-center .section-subtitle { margin: 0 auto; }


        /* ─── CLIENT MARQUEE ─────────────────────────────── */
        .clients-section {
            padding: 52px 0;
            background: #f8fafc;
            border-top: 1px solid var(--hairline);
            border-bottom: 1px solid var(--hairline);
            overflow: hidden;
        }
        .clients-label {
            text-align: center;
            font-size: 12.5px;
            font-weight: 700;
            color: var(--mute);
            letter-spacing: .06em;
            text-transform: uppercase;
            margin-bottom: 24px;
        }
        .marquee-track {
            position: relative;
            width: 100%;
            overflow: hidden;
        }
        .marquee-track::before,
        .marquee-track::after {
            content: '';
            position: absolute;
            top: 0; bottom: 0;
            width: 80px;
            z-index: 2;
            pointer-events: none;
        }
        .marquee-track::before { left: 0; background: linear-gradient(90deg, #f8fafc, transparent); }
        .marquee-track::after { right: 0; background: linear-gradient(270deg, #f8fafc, transparent); }
        .marquee-strip {
            display: flex;
            width: max-content;
            animation: scroll 35s linear infinite;
            gap: 20px;
            padding: 4px 0;
        }
        .marquee-strip:hover { animation-play-state: paused; }
        @keyframes scroll {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }
        .client-card {
            flex-shrink: 0;
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px 24px;
            background: #fff;
            border: 1px solid var(--hairline);
            border-radius: var(--radius);
            min-width: 230px;
            transition: border-color .2s, box-shadow .2s, transform .2s;
        }
        .client-card:hover {
            border-color: #DBEAFE;
            box-shadow: var(--shadow-md);
            transform: translateY(-2px);
        }
        .client-badge {
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px; height: 40px;
            border-radius: 10px;
            font-weight: 900;
            font-size: 13px;
            letter-spacing: .04em;
            color: #fff;
        }
        .client-card img.client-logo {
            height: 36px;
            width: auto;
            max-width: 120px;
            object-fit: contain;
            flex-shrink: 0;
        }
        .client-name {
            font-size: 13px;
            font-weight: 700;
            color: var(--ink);
            line-height: 1.3;
        }

        /* ─── ZIGZAG FEATURE BLOCKS ─────────────────────── */
        .feature-block {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 64px;
            align-items: center;
            padding: 80px 0;
        }
        .feature-block + .feature-block {
            border-top: 1px solid var(--hairline);
        }
        .feature-block:nth-child(even) .feature-image { order: -1; }
        .feature-image {
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-lg);
            position: relative;
        }
        .feature-image img {
            width: 100%;
            height: 360px;
            object-fit: cover;
            display: block;
        }
        .feature-image-badge {
            position: absolute;
            top: 16px; left: 16px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 8px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .03em;
            backdrop-filter: blur(8px);
        }
        .feature-text .section-label { margin-bottom: 8px; }
        .feature-text h3 {
            font-size: 24px;
            font-weight: 700;
            letter-spacing: -.02em;
            line-height: 1.2;
            margin-bottom: 16px;
        }
        .feature-text p {
            font-size: 15px;
            color: var(--body);
            line-height: 1.7;
            margin-bottom: 24px;
        }
        .feature-list { list-style: none; padding: 0; }
        .feature-list li {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 10px 0;
            font-size: 14px;
            color: var(--body);
            font-weight: 500;
        }
        .feature-list li i {
            flex-shrink: 0;
            margin-top: 3px;
            font-size: 14px;
        }
        .check-blue { color: var(--blue); }
        .check-teal { color: var(--teal); }
        .check-amber { color: var(--amber); }

        /* ─── BUSINESS PILLARS (FLIP CARDS) ─────────────── */
        .pillar-section-bg {
            background: #ffffff;
            padding: 88px 0 96px;
            border-bottom: 1px solid var(--hairline);
        }
        .pillar-intro {
            text-align: center;
            max-width: 640px;
            margin: 0 auto 48px;
        }
        .pillar-intro .section-label {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--blue);
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 8px;
        }
        .pillar-intro .section-title {
            color: var(--ink);
            font-size: clamp(2rem, 3.2vw, 2.75rem);
            font-weight: 800;
            letter-spacing: -.03em;
            margin-bottom: 12px;
        }
        .pillar-intro .section-subtitle {
            color: var(--mute);
            font-size: 14.5px;
            line-height: 1.65;
        }
        .pillar-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
        }
        .flip-card {
            background-color: transparent;
            perspective: 1200px;
            height: 490px;
            cursor: pointer;
            outline: none;
            user-select: none;
        }
        .flip-card:focus-visible .flip-card-front,
        .flip-card:focus-visible .flip-card-back {
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.45);
        }
        .flip-card-inner {
            position: relative;
            width: 100%;
            height: 100%;
            text-align: left;
            transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
            transform-style: preserve-3d;
        }
        .flip-card.is-flipped .flip-card-inner {
            transform: rotateY(180deg);
        }
        .flip-card-front,
        .flip-card-back {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            -webkit-backface-visibility: hidden;
            backface-visibility: hidden;
            border-radius: var(--radius-lg);
            border: 1px solid var(--hairline);
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.05);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: box-shadow .2s ease, border-color .2s ease;
        }
        .flip-card:hover .flip-card-front,
        .flip-card:hover .flip-card-back {
            border-color: #93c5fd;
            box-shadow: 0 10px 24px -4px rgba(15, 23, 42, 0.08);
        }
        /* Front face */
        .flip-card-front {
            background: #ffffff;
        }
        .flip-card-image {
            height: 190px;
            width: 100%;
            overflow: hidden;
            position: relative;
            background: #e2e8f0;
        }
        .flip-card-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform .45s ease;
        }
        .flip-card:hover .flip-card-front img {
            transform: scale(1.04);
        }
        .flip-card-content {
            padding: 22px 22px 18px;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }
        .flip-card-kicker {
            font-size: 11px;
            font-weight: 800;
            color: var(--blue);
            letter-spacing: .08em;
            text-transform: uppercase;
            margin-bottom: 8px;
        }
        .flip-card-front h3 {
            font-size: 18px;
            font-weight: 700;
            color: var(--ink);
            line-height: 1.35;
            margin-bottom: 10px;
        }
        .flip-card-front p {
            font-size: 13px;
            color: var(--body);
            line-height: 1.6;
            margin-bottom: auto;
        }
        .flip-hint {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            margin-top: 16px;
            padding-top: 14px;
            border-top: 1px solid var(--hairline);
            font-size: 12px;
            font-weight: 600;
            color: var(--blue);
        }
        .flip-hint i {
            font-size: 11px;
        }
        /* Back face */
        .flip-card-back {
            background: #0f172a;
            color: #f8fafc;
            transform: rotateY(180deg);
            padding: 26px 22px 20px;
            justify-content: space-between;
        }
        .flip-card-back .flip-card-kicker {
            color: #60a5fa;
            margin-bottom: 6px;
        }
        .flip-card-back h3 {
            font-size: 17.5px;
            font-weight: 700;
            color: #ffffff;
            line-height: 1.35;
            margin-bottom: 10px;
        }
        .flip-card-back p {
            font-size: 12.5px;
            color: rgba(226, 232, 240, 0.78);
            line-height: 1.6;
            margin-bottom: 14px;
        }
        .flip-feature-list {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .flip-feature-list li {
            display: flex;
            align-items: flex-start;
            gap: 9px;
            font-size: 12px;
            color: rgba(255, 255, 255, 0.88);
            line-height: 1.45;
        }
        .flip-feature-list li i {
            color: #38bdf8;
            font-size: 11px;
            margin-top: 2px;
            flex-shrink: 0;
        }
        .flip-hint-back {
            border-top-color: rgba(255, 255, 255, 0.12);
            color: #93c5fd;
        }
        @media (max-width: 980px) {
            .pillar-grid {
                grid-template-columns: 1fr;
                max-width: 460px;
                margin: 0 auto;
                gap: 20px;
            }
            .flip-card {
                height: 480px;
            }
        }

        /* ─── KBLI QUALIFICATION SECTION ─────────────────── */
        #kbli {
            padding: 88px 0 96px;
            background: #f8fafc;
            border-bottom: 1px solid var(--hairline);
        }
        #kbli .section-header-center {
            text-align: center;
            max-width: 620px;
            margin: 0 auto 44px;
        }
        #kbli .section-label {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--blue);
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 8px;
        }
        #kbli .section-title {
            color: var(--ink);
            font-size: clamp(2rem, 3.2vw, 2.75rem);
            font-weight: 800;
            letter-spacing: -.03em;
            margin-bottom: 12px;
        }
        #kbli .section-subtitle {
            color: var(--mute);
            font-size: 14.5px;
            line-height: 1.65;
        }
        #kbli .kbli-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 16px;
        }
        #kbli .kbli-card {
            display: flex;
            flex-direction: column;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: var(--radius);
            padding: 22px 18px;
            transition: transform .2s ease, border-color .2s ease, box-shadow .2s ease;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
            position: relative;
        }
        #kbli .kbli-card:hover {
            transform: translateY(-3px);
            border-color: #93c5fd;
            box-shadow: 0 10px 24px -4px rgba(15, 23, 42, 0.08);
        }
        #kbli .kbli-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 14px;
        }
        #kbli .kbli-code {
            font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
            font-size: 11.5px;
            font-weight: 700;
            color: var(--blue);
            background: var(--blue-soft);
            border: 1px solid var(--blue-pale);
            border-radius: 6px;
            padding: 3px 7px;
            letter-spacing: .02em;
        }
        #kbli .kbli-icon {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            color: var(--mute);
            font-size: 13px;
            transition: all .2s ease;
        }
        #kbli .kbli-card:hover .kbli-icon {
            background: var(--blue-soft);
            border-color: var(--blue-pale);
            color: var(--blue);
        }
        #kbli .kbli-card h4 {
            font-size: 14.5px;
            font-weight: 700;
            color: var(--ink);
            margin-bottom: 8px;
            line-height: 1.35;
        }
        #kbli .kbli-card p {
            font-size: 12px;
            color: var(--body);
            line-height: 1.6;
            margin-top: auto;
        }
        @media (max-width: 1100px) {
            #kbli .kbli-grid { grid-template-columns: repeat(3, 1fr); gap: 14px; }
        }
        @media (max-width: 700px) {
            #kbli { padding: 64px 0 70px; }
            #kbli .kbli-grid { grid-template-columns: repeat(2, 1fr); gap: 12px; }
        }
        @media (max-width: 480px) {
            #kbli .kbli-grid { grid-template-columns: 1fr; }
        }

        /* ─── ALUR BISNIS (PROCESS FLOW) ─────────────────── */
        #alur {
            padding: 84px 0 92px;
            background: #ffffff;
            border-top: 1px solid var(--hairline);
        }
        #alur .section-header-center {
            text-align: center;
            max-width: 600px;
            margin: 0 auto 44px;
        }
        #alur .section-label {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--blue);
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 8px;
        }
        #alur .section-title {
            color: var(--ink);
            font-size: clamp(2rem, 3.2vw, 2.75rem);
            font-weight: 800;
            letter-spacing: -.03em;
            margin-bottom: 12px;
        }
        #alur .section-subtitle {
            color: var(--mute);
            font-size: 14.5px;
            line-height: 1.65;
        }
        #alur .process-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
        }
        #alur .process-step {
            display: flex;
            flex-direction: column;
            background: #ffffff;
            border: 1px solid var(--hairline);
            border-radius: var(--radius);
            padding: 24px 20px;
            transition: transform .2s ease, border-color .2s ease, box-shadow .2s ease;
            box-shadow: var(--shadow-sm);
        }
        #alur .process-step:hover {
            transform: translateY(-3px);
            border-color: #93C5FD;
            box-shadow: 0 10px 24px -4px rgba(15, 23, 42, 0.07);
        }
        #alur .step-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 18px;
        }
        #alur .step-num {
            font-size: 12px;
            font-weight: 700;
            color: var(--blue);
            background: var(--blue-soft);
            border: 1px solid var(--blue-pale);
            border-radius: 6px;
            padding: 3px 8px;
            letter-spacing: .04em;
        }
        #alur .step-icon {
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--surface);
            border: 1px solid var(--hairline);
            border-radius: 8px;
            color: var(--mute);
            font-size: 14px;
            transition: all .2s ease;
        }
        #alur .process-step:hover .step-icon {
            background: var(--blue-soft);
            border-color: var(--blue-pale);
            color: var(--blue);
        }
        #alur .process-step h4 {
            font-size: 15.5px;
            font-weight: 700;
            color: var(--ink);
            margin-bottom: 8px;
            line-height: 1.35;
        }
        #alur .process-step p {
            font-size: 12.5px;
            color: var(--body);
            line-height: 1.6;
            margin-top: auto;
        }
        @media (max-width: 900px) {
            #alur { padding: 68px 0 74px; }
            #alur .process-grid { grid-template-columns: repeat(2, 1fr); gap: 16px; }
        }
        @media (max-width: 540px) {
            #alur .process-grid { grid-template-columns: 1fr; gap: 14px; }
        }


        /* ─── FOOTER ──────────────────────────────────────── */
        .site-footer {
            background: #0c1929;
            color: rgba(255,255,255,.5);
            padding: 56px 0 0;
        }
        .footer-grid {
            display: grid;
            grid-template-columns: 1.4fr 0.9fr 0.9fr 1.8fr;
            gap: 36px;
        }
        .footer-brand { display: flex; align-items: center; gap: 10px; margin-bottom: 14px; }
        .footer-brand img { height: 34px; width: auto; border-radius: 6px; background: #fff; padding: 3px; }
        .footer-brand-text .brand-name { font-weight: 800; font-size: 17px; color: #fff; line-height: 1.2; }
        .footer-brand-text .brand-sub { font-size: 11px; color: var(--blue-muted); font-weight: 500; letter-spacing: .02em; }
        .footer-desc { font-size: 12.5px; line-height: 1.7; max-width: 320px; color: rgba(255,255,255,.42); }
        .footer-heading {
            font-size: 12px;
            font-weight: 700;
            color: var(--blue-muted);
            letter-spacing: .05em;
            text-transform: uppercase;
            margin-bottom: 18px;
        }
        .footer-links { list-style: none; padding: 0; }
        .footer-links li { margin-bottom: 10px; }
        .footer-links a {
            font-size: 13px;
            font-weight: 400;
            color: rgba(255,255,255,.55);
            transition: color .2s;
        }
        .footer-links a:hover { color: #fff; }
        .footer-contact-list {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 14px;
        }
        .footer-contact-list li {
            margin: 0;
            padding: 0;
        }
        .footer-contact-link {
            display: flex;
            align-items: center;
            gap: 12px;
            color: rgba(255,255,255,.55);
            text-decoration: none;
            transition: color .2s ease;
            font-size: 12.5px;
            line-height: 1.45;
        }
        .footer-contact-link i {
            flex-shrink: 0;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            border: 1px solid rgba(255,255,255,.14);
            color: var(--blue-muted);
            font-size: 12px;
            transition: all .2s ease;
            background: rgba(255,255,255,.03);
        }
        .footer-contact-link span {
            flex: 1;
        }
        .footer-contact-link:hover {
            color: #ffffff;
        }
        .footer-contact-link:hover i {
            border-color: #60a5fa;
            color: #60a5fa;
            background: rgba(96, 165, 250, 0.15);
        }
        .footer-bottom {
            margin-top: 40px;
            padding: 18px 0;
            border-top: 1px solid rgba(255,255,255,.07);
            text-align: center;
            font-size: 11.5px;
            color: rgba(255,255,255,.28);
            line-height: 1.7;
        }

        @media (max-width: 900px) {
            .footer-grid { grid-template-columns: 1fr 1fr; gap: 32px; }
        }
        @media (max-width: 540px) {
            .site-footer { padding: 40px 0 0; }
            .footer-grid { grid-template-columns: 1fr; gap: 28px; }
        }

        /* ─── PAGE LOAD ENTRANCE ANIMATIONS ───────────────── */
        @keyframes fadeDown {
            from { opacity: 0; transform: translateY(-20px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(36px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to   { opacity: 1; }
        }
        @keyframes scaleIn {
            from { opacity: 0; transform: scale(1.08); }
            to   { opacity: 1; transform: scale(1); }
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* Header entrance */
        .anim-header {
            animation: fadeDown .8s cubic-bezier(.22, 1, .36, 1) .1s both;
        }

        /* Hero background Ken Burns */
        .anim-hero-bg {
            animation: scaleIn 1.6s cubic-bezier(.22, 1, .36, 1) 0s both;
        }

        /* Hero staggered content */
        .anim-hero-1 { animation: fadeUp 1s cubic-bezier(.22, 1, .36, 1) .3s both; }
        .anim-hero-2 { animation: fadeUp 1s cubic-bezier(.22, 1, .36, 1) .5s both; }
        .anim-hero-3 { animation: fadeUp 1s cubic-bezier(.22, 1, .36, 1) .7s both; }
        .anim-hero-4 { animation: fadeUp 1s cubic-bezier(.22, 1, .36, 1) .9s both; }

        /* Client section entrance */
        .anim-clients {
            animation: slideUp .9s cubic-bezier(.22, 1, .36, 1) 1.1s both;
        }

        /* Reduce motion for accessibility */
        @media (prefers-reduced-motion: reduce) {
            .anim-header, .anim-hero-bg, .anim-hero-1, .anim-hero-2,
            .anim-hero-3, .anim-hero-4, .anim-clients {
                animation: none !important;
                opacity: 1 !important;
                transform: none !important;
            }
            .reveal {
                transition: none !important;
                opacity: 1 !important;
                transform: none !important;
            }
        }

        /* ─── SCROLL REVEAL ANIMATION ─────────────────────── */
        .reveal {
            opacity: 0;
            transform: translateY(40px);
            transition: opacity .9s cubic-bezier(.22, 1, .36, 1), transform .9s cubic-bezier(.22, 1, .36, 1);
            will-change: opacity, transform;
        }
        .reveal.visible {
            opacity: 1;
            transform: translateY(0);
        }
        .reveal-delay-1 { transition-delay: .12s; }
        .reveal-delay-2 { transition-delay: .24s; }
        .reveal-delay-3 { transition-delay: .36s; }
        .reveal-delay-4 { transition-delay: .48s; }

        /* Smooth transitions for interactive elements */
        .kbli-card, .client-card, .feature-image, .process-step {
            will-change: transform;
        }

        /* Parallax-like subtle float on images */
        .feature-image img {
            transition: transform .6s cubic-bezier(.22, 1, .36, 1);
        }
        .feature-block:hover .feature-image img {
            transform: scale(1.03);
        }

        /* ─── WHATSAPP FLOATING WIDGET ───────────────────── */
        .wa-floating-container {
            position: fixed;
            bottom: 24px;
            right: 24px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            font-family: 'Outfit', ui-sans-serif, system-ui, sans-serif;
        }

        .wa-trigger-btn {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: #25D366;
            color: #FFFFFF;
            border: none;
            box-shadow: 0 6px 22px rgba(37, 211, 102, 0.45);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            position: relative;
            transition: transform 0.22s cubic-bezier(0.2, 0.8, 0.2, 1), box-shadow 0.22s ease;
            outline: none;
        }
        .wa-trigger-btn:hover {
            transform: scale(1.08);
            box-shadow: 0 8px 26px rgba(37, 211, 102, 0.55);
        }
        .wa-trigger-btn:active {
            transform: scale(0.96);
        }
        .wa-trigger-btn:focus-visible {
            box-shadow: 0 0 0 3px #FFFFFF, 0 0 0 5px #25D366;
        }
        .wa-trigger-icon {
            font-size: 30px;
            line-height: 1;
        }
        .wa-badge {
            position: absolute;
            top: -2px;
            right: -2px;
            width: 22px;
            height: 22px;
            background: #EF4444;
            color: #FFFFFF;
            border-radius: 50%;
            font-size: 11px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #FFFFFF;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.25);
            animation: pulseBadge 2.4s infinite ease-in-out;
        }
        .wa-badge.is-hidden {
            display: none !important;
        }
        @keyframes pulseBadge {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.15); }
        }

        /* Popup Card */
        .wa-popup-card {
            position: absolute;
            bottom: 74px;
            right: 0;
            width: 370px;
            max-width: calc(100vw - 32px);
            background: #FFFFFF;
            border-radius: 18px;
            border: 1px solid #E2E8F0;
            box-shadow: 0 20px 48px -10px rgba(15, 23, 42, 0.22), 0 4px 16px rgba(15, 23, 42, 0.08);
            overflow: hidden;
            opacity: 0;
            visibility: hidden;
            transform: translateY(16px) scale(0.95);
            transform-origin: bottom right;
            transition: opacity 0.25s cubic-bezier(0.16, 1, 0.3, 1),
                        transform 0.25s cubic-bezier(0.16, 1, 0.3, 1),
                        visibility 0.25s;
            z-index: 10000;
        }
        .wa-popup-card.is-open {
            opacity: 1;
            visibility: visible;
            transform: translateY(0) scale(1);
        }

        /* Header */
        .wa-popup-header {
            background: linear-gradient(135deg, #075E54 0%, #0F766E 50%, #059669 100%);
            padding: 16px 18px;
            color: #FFFFFF;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .wa-agent-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .wa-avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: #FFFFFF;
            padding: 3px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
            flex-shrink: 0;
        }
        .wa-avatar img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        .wa-agent-text h4 {
            font-size: 14.5px;
            font-weight: 700;
            color: #FFFFFF;
            line-height: 1.2;
            margin: 0;
        }
        .wa-agent-text p {
            font-size: 11px;
            color: rgba(255, 255, 255, 0.9);
            margin-top: 3px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .wa-online-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #25D366;
            display: inline-block;
        }
        .wa-close-btn {
            background: none;
            border: none;
            color: rgba(255, 255, 255, 0.8);
            font-size: 17px;
            cursor: pointer;
            padding: 6px;
            line-height: 1;
            border-radius: 8px;
            transition: color 0.15s, background 0.15s;
        }
        .wa-close-btn:hover {
            color: #FFFFFF;
            background: rgba(255, 255, 255, 0.18);
        }

        /* Body */
        .wa-popup-body {
            padding: 16px;
            background: #FFFFFF;
            max-height: 440px;
            overflow-y: auto;
        }
        .wa-greeting {
            font-size: 12px;
            color: #334155;
            line-height: 1.5;
            margin-bottom: 12px;
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 10px;
            padding: 10px 12px;
        }
        .wa-options-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-bottom: 14px;
        }
        .wa-option-card {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 10px 12px;
            border-radius: 12px;
            border: 1px solid #E2E8F0;
            background: #FFFFFF;
            cursor: pointer;
            text-align: left;
            transition: all 0.18s ease;
            position: relative;
            width: 100%;
            box-sizing: border-box;
        }
        .wa-option-card:hover {
            border-color: #25D366;
            background: #F0FDF4;
            transform: translateY(-1px);
        }
        .wa-option-card.is-selected {
            border-color: #25D366;
            background: #ECFDF5;
            box-shadow: 0 2px 8px rgba(37, 211, 102, 0.16);
        }
        .wa-option-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            flex-shrink: 0;
            background: #F1F5F9;
            transition: background 0.15s, color 0.15s;
        }
        .wa-option-card.is-selected .wa-option-icon {
            background: #DCFCE7;
            color: #16A34A;
        }
        .wa-option-content {
            flex: 1;
            min-width: 0;
        }
        .wa-option-title {
            font-size: 12.5px;
            font-weight: 700;
            color: #0F172A;
            line-height: 1.3;
        }
        .wa-option-desc {
            font-size: 11px;
            color: #64748B;
            line-height: 1.35;
            margin-top: 2px;
        }
        .wa-option-actions {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-top: 2px;
            flex-shrink: 0;
        }
        .wa-option-check {
            font-size: 12px;
            color: #16A34A;
            opacity: 0;
            transform: scale(0.5);
            transition: all 0.18s ease;
        }
        .wa-option-card.is-selected .wa-option-check {
            opacity: 1;
            transform: scale(1);
        }
        .wa-quick-send-btn {
            background: none;
            border: none;
            color: #64748B;
            cursor: pointer;
            padding: 4px;
            border-radius: 6px;
            font-size: 12px;
            transition: color 0.15s, background 0.15s;
        }
        .wa-quick-send-btn:hover {
            color: #25D366;
            background: #E2E8F0;
        }

        .wa-custom-note-wrap {
            margin-bottom: 12px;
        }
        .wa-custom-note {
            width: 100%;
            padding: 8px 12px;
            font-size: 12px;
            font-family: inherit;
            border: 1px solid #CBD5E1;
            border-radius: 8px;
            outline: none;
            resize: none;
            color: #0F172A;
            background: #FFFFFF;
            transition: border-color 0.15s;
            box-sizing: border-box;
        }
        .wa-custom-note:focus {
            border-color: #25D366;
            box-shadow: 0 0 0 2px rgba(37, 211, 102, 0.2);
        }

        .wa-submit-btn {
            width: 100%;
            padding: 12px 18px;
            background: #25D366;
            color: #FFFFFF;
            border: none;
            border-radius: 12px;
            font-size: 13.5px;
            font-weight: 700;
            font-family: inherit;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            cursor: pointer;
            box-shadow: 0 3px 12px rgba(37, 211, 102, 0.38);
            transition: background 0.15s, transform 0.15s, box-shadow 0.15s;
            text-decoration: none;
        }
        .wa-submit-btn:hover {
            background: #20BD5A;
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(37, 211, 102, 0.48);
        }
        .wa-submit-btn:active {
            transform: translateY(0);
        }

        @media (max-width: 480px) {
            .wa-floating-container {
                bottom: 16px;
                right: 16px;
            }
            .wa-popup-card {
                width: calc(100vw - 32px);
                bottom: 68px;
                right: 0;
            }
            .wa-trigger-btn {
                width: 54px;
                height: 54px;
            }
            .wa-trigger-icon {
                font-size: 26px;
            }
        }
    </style>
</head>
<body>

    <!-- ═══ HEADER ═══ -->
    <header class="site-header anim-header" id="siteHeader">
        <div class="container header-inner">
            <a href="#" class="brand">
                <img src="{{ asset('images/logo-icon.png') }}" alt="PT Signal Panca Utama">
                <div class="brand-text">
                    <div class="brand-name">Signal Panca Utama</div>
                    <div class="brand-sub">Tender · Jasa · Perdagangan</div>
                </div>
            </a>

            <nav class="nav-links">
                <a href="#beranda" class="active">Beranda</a>
                <a href="#layanan">Layanan</a>
                <a href="#kbli">Kualifikasi</a>
                <a href="#alur">Alur Bisnis</a>
                <a href="#kontak">Kontak</a>
            </nav>

            <div class="header-actions">
                <a href="{{ route('login') }}" class="btn btn-primary btn-portal">
                    <i class="fa-solid fa-arrow-right-to-bracket"></i> Masuk Portal
                </a>
            </div>

            <button class="mobile-toggle" id="mobileToggle" aria-label="Menu">
                <i class="fa-solid fa-bars"></i>
            </button>
        </div>

        <div class="mobile-menu" id="mobileMenu">
            <a href="#beranda" class="mobile-link">Beranda</a>
            <a href="#layanan" class="mobile-link">Layanan</a>
            <a href="#kbli" class="mobile-link">Kualifikasi</a>
            <a href="#alur" class="mobile-link">Alur Bisnis</a>
            <a href="#kontak" class="mobile-link">Kontak</a>
            <a href="{{ route('login') }}" class="btn btn-primary btn-lg">Masuk ke Portal</a>
        </div>
    </header>

    <!-- ═══ HERO ═══ -->
    <section class="hero" id="beranda">
        <div class="hero-bg anim-hero-bg">
            <img src="{{ asset('images/hero-corporate.jpg') }}" alt="PT Signal Panca Utama Office">
        </div>
        <div class="container">
            <div class="hero-content">
                <h1 class="anim-hero-1">Mitra Terpercaya untuk <em>Tender, Jasa,</em> dan <em>Perdagangan</em></h1>
                <p class="hero-desc anim-hero-2">
                    Mengelola pengadaan tender instansi, pelaksanaan pekerjaan jasa, serta perdagangan barang sejak 2015. Sistem terintegrasi untuk transparansi dan efisiensi bisnis.
                </p>
                <div class="hero-actions anim-hero-3">
                    <a href="{{ route('login') }}" class="btn btn-primary btn-lg">
                        <i class="fa-solid fa-arrow-right-to-bracket"></i> Masuk Portal
                    </a>
                    <a href="#layanan" class="btn btn-white btn-lg">
                        Pelajari Layanan Kami
                    </a>
                </div>
                <div class="hero-stats anim-hero-4">
                    <div class="hero-stat">
                        <div class="hero-stat-value">50+</div>
                        <div class="hero-stat-label">Tender Dimenangkan</div>
                    </div>
                    <div class="hero-stat">
                        <div class="hero-stat-value">100+</div>
                        <div class="hero-stat-label">Mitra Kerjasama</div>
                    </div>
                    <div class="hero-stat">
                        <div class="hero-stat-value">Rp 25M+</div>
                        <div class="hero-stat-label">Nilai Kontrak Terkelola</div>
                    </div>
                    <div class="hero-stat">
                        <div class="hero-stat-value">99.8%</div>
                        <div class="hero-stat-label">Ketepatan Waktu</div>
                    </div>
                </article>
            </div>
        </div>
    </section>

    <!-- ═══ CLIENT MARQUEE ═══ -->
    <section class="clients-section anim-clients" id="klien">
        <div class="clients-label">Dipercaya oleh berbagai instansi & perusahaan</div>
        <div class="marquee-track">
            <div class="marquee-strip">
                @if(isset($partnerLogos) && count($partnerLogos) > 0)
                    {{-- Set 1 --}}
                    @foreach($partnerLogos as $logo)
                        <div class="client-card">
                            @if($logo->logo_path)
                                <img src="{{ asset('storage/' . $logo->logo_path) }}" alt="{{ $logo->name }}" class="client-logo">
                            @else
                                <div class="client-badge" style="background-color: {{ $logo->badge_color && str_starts_with($logo->badge_color, '#') ? $logo->badge_color : '#1a6de3' }}">
                                    {{ $logo->badge_text ?? strtoupper(substr($logo->name, 0, 3)) }}
                                </div>
                            @endif
                            <span class="client-name">{{ $logo->name }}</span>
                        </div>
                    @endforeach
                    {{-- Set 2 (duplicate for infinite loop) --}}
                    @foreach($partnerLogos as $logo)
                        <div class="client-card">
                            @if($logo->logo_path)
                                <img src="{{ asset('storage/' . $logo->logo_path) }}" alt="{{ $logo->name }}" class="client-logo">
                            @else
                                <div class="client-badge" style="background-color: {{ $logo->badge_color && str_starts_with($logo->badge_color, '#') ? $logo->badge_color : '#1a6de3' }}">
                                    {{ $logo->badge_text ?? strtoupper(substr($logo->name, 0, 3)) }}
                                </div>
                            @endif
                            <span class="client-name">{{ $logo->name }}</span>
                        </div>
                    @endforeach
                @else
                    <div class="client-card"><div class="client-badge" style="background:#dc2626">HK</div><span class="client-name">Hutama Karya</span></div>
                    <div class="client-card"><div class="client-badge" style="background:#f59e0b">BA</div><span class="client-name">Brantas Abipraya</span></div>
                    <div class="client-card"><div class="client-badge" style="background:#dc2626">HK</div><span class="client-name">Hutama Karya</span></div>
                    <div class="client-card"><div class="client-badge" style="background:#f59e0b">BA</div><span class="client-name">Brantas Abipraya</span></div>
                @endif
            </div>
        </div>
    </section>

    <!-- ═══ LAYANAN / DOMAIN BISNIS (Interactive Flip Cards) ═══ -->
    <section class="section pillar-section-bg" id="layanan">
        <div class="container">
            <div class="pillar-intro reveal">
                <h2 class="section-title">Layanan Utama Perusahaan</h2>
                <p class="section-subtitle">Tiga domain operasional terintegrasi untuk akurasi data tender, pelaksanaan jasa, dan rantai suplai barang.</p>
            </div>

            <div class="pillar-grid">
                {{-- Widget 1: Manajemen Tender --}}
                <div class="flip-card reveal" tabindex="0" role="button" aria-expanded="false" aria-label="Detail Manajemen Tender">
                    <div class="flip-card-inner">
                        <div class="flip-card-front">
                            <div class="flip-card-image">
                                <img src="{{ asset('images/hero-corporate.jpg') }}" alt="Manajemen Tender PT Signal Panca Utama">
                            </div>
                            <div class="flip-card-content">
                                <div class="flip-card-kicker">Domain Utama</div>
                                <h3>Manajemen Tender</h3>
                                <p>Pengelolaan siklus pengadaan tender LPSE & BUMN dari pemantauan jadwal, penyusunan dokumen, hingga konversi ke kontrak kerja.</p>
                                <div class="flip-hint">
                                    <i class="fa-solid fa-arrow-rotate-right"></i> Klik kartu untuk penjelasan
                                </div>
                            </div>
                        </div>
                        <div class="flip-card-back">
                            <div>
                                <div class="flip-card-kicker">Alur Lengkap</div>
                                <h3>Manajemen Tender</h3>
                                <p>Sistem terpadu memfasilitasi pelacakan peluang tender instansi pemerintah & BUMN secara transparan dengan audit berkas yang rapi.</p>
                                <ul class="flip-feature-list">
                                    <li><i class="fa-solid fa-check"></i> <span>Monitoring pengumuman LPSE & batas waktu penawaran</span></li>
                                    <li><i class="fa-solid fa-check"></i> <span>Manajemen berkas kualifikasi teknis & administrasi</span></li>
                                    <li><i class="fa-solid fa-check"></i> <span>Perhitungan HPS dan kalkulasi margin penawaran</span></li>
                                    <li><i class="fa-solid fa-check"></i> <span>Otomatisasi konversi status tender menang menjadi SPK & kontrak</span></li>
                                </ul>
                            </div>
                            <div class="flip-hint flip-hint-back">
                                <i class="fa-solid fa-arrow-rotate-left"></i> Klik untuk membalik kembali
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Widget 2: Manajemen Jasa --}}
                <div class="flip-card reveal reveal-delay-1" tabindex="0" role="button" aria-expanded="false" aria-label="Detail Manajemen Jasa">
                    <div class="flip-card-inner">
                        <div class="flip-card-front">
                            <div class="flip-card-image">
                                <img src="{{ asset('images/section-finance.jpg') }}" alt="Manajemen Jasa dan Keuangan">
                            </div>
                            <div class="flip-card-content">
                                <div class="flip-card-kicker">Jasa & Keuangan</div>
                                <h3>Manajemen Jasa & Kontrak</h3>
                                <p>Pencatatan klien, kontrak kerja, pemantauan progres pengerjaan jasa, fee komisi rekanan, dan penagihan invoice termin bertahap.</p>
                                <div class="flip-hint">
                                    <i class="fa-solid fa-arrow-rotate-right"></i> Klik kartu untuk penjelasan
                                </div>
                            </div>
                        </div>
                        <div class="flip-card-back">
                            <div>
                                <div class="flip-card-kicker">Alur Kerja</div>
                                <h3>Manajemen Jasa & Kontrak</h3>
                                <p>Memastikan setiap kesepakatan jasa tercatat legal, hak komisi mitra terkelola, dan pembayaran termin terpantau tanpa jeda.</p>
                                <ul class="flip-feature-list">
                                    <li><i class="fa-solid fa-check"></i> <span>Basis data rekanan klien & riwayat penugasan terpusat</span></li>
                                    <li><i class="fa-solid fa-check"></i> <span>Klausul kontrak jasa & skema komisi fee terintegrasi</span></li>
                                    <li><i class="fa-solid fa-check"></i> <span>Pelacakan milestone progres penyelesaian pekerjaan</span></li>
                                    <li><i class="fa-solid fa-check"></i> <span>Penerbitan invoice termin dengan riwayat pembayaran</span></li>
                                </ul>
                            </div>
                            <div class="flip-hint flip-hint-back">
                                <i class="fa-solid fa-arrow-rotate-left"></i> Klik untuk membalik kembali
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Widget 3: Perdagangan & Pengadaan --}}
                <div class="flip-card reveal reveal-delay-2" tabindex="0" role="button" aria-expanded="false" aria-label="Detail Perdagangan dan Pengadaan">
                    <div class="flip-card-inner">
                        <div class="flip-card-front">
                            <div class="flip-card-image">
                                <img src="{{ asset('images/section-warehouse.jpg') }}" alt="Gudang dan Pengadaan Barang">
                            </div>
                            <div class="flip-card-content">
                                <div class="flip-card-kicker">Logistik & Gudang</div>
                                <h3>Perdagangan & Pengadaan</h3>
                                <p>Pengelolaan stok persediaan gudang, penerimaan suplai distributor, mutasi barang, serta validasi kuantitas saat transaksi penjualan.</p>
                                <div class="flip-hint">
                                    <i class="fa-solid fa-arrow-rotate-right"></i> Klik kartu untuk penjelasan
                                </div>
                            </div>
                        </div>
                        <div class="flip-card-back">
                            <div>
                                <div class="flip-card-kicker">Alur Pasok</div>
                                <h3>Perdagangan & Pengadaan</h3>
                                <p>Menghubungkan rantai pasok supplier hingga barang diterima pelanggan dengan kontrol stok otomatis tanpa selisih kuantitas.</p>
                                <ul class="flip-feature-list">
                                    <li><i class="fa-solid fa-check"></i> <span>Penerimaan pengadaan supplier otomatis menambah stok</span></li>
                                    <li><i class="fa-solid fa-check"></i> <span>Validasi batas minimum stok saat transaksi penjualan</span></li>
                                    <li><i class="fa-solid fa-check"></i> <span>Rekapitulasi mutasi stok keluar-masuk secara real-time</span></li>
                                    <li><i class="fa-solid fa-check"></i> <span>Integrasi faktur tagihan dan laporan ketersediaan</span></li>
                                </ul>
                            </div>
                            <div class="flip-hint flip-hint-back">
                                <i class="fa-solid fa-arrow-rotate-left"></i> Klik untuk membalik kembali
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══ RUANG LINGKUP KBLI ═══ -->
    <section class="section" id="kbli">
        <div class="container">
            <div class="section-header section-header-center reveal">
                <h2 class="section-title">Ruang Lingkup KBLI</h2>
                <p class="section-subtitle">Klasifikasi Baku Lapangan Usaha Indonesia terdaftar resmi untuk legalitas & operasional PT Signal Panca Utama.</p>
            </div>

            <div class="kbli-grid">
                <div class="kbli-card reveal">
                    <div class="kbli-header">
                        <span class="kbli-code">KBLI 46100</span>
                        <div class="kbli-icon"><i class="fa-solid fa-handshake"></i></div>
                    </div>
                    <h4>Perdagangan Balas Jasa</h4>
                    <p>Perdagangan besar atas dasar balas jasa (fee) atau kontrak keagenan komersial.</p>
                </div>
                <div class="kbli-card reveal reveal-delay-1">
                    <div class="kbli-header">
                        <span class="kbli-code">KBLI 46422</span>
                        <div class="kbli-icon"><i class="fa-solid fa-print"></i></div>
                    </div>
                    <h4>Percetakan & Penerbitan</h4>
                    <p>Perdagangan besar barang percetakan, dokumen formal, dan materi publikasi.</p>
                </div>
                <div class="kbli-card reveal reveal-delay-2">
                    <div class="kbli-header">
                        <span class="kbli-code">KBLI 46499</span>
                        <div class="kbli-icon"><i class="fa-solid fa-building-columns"></i></div>
                    </div>
                    <h4>Perlengkapan Kantor</h4>
                    <p>Perdagangan besar perlengkapan kantor, perabot, dan perkakas operasional instansi.</p>
                </div>
                <div class="kbli-card reveal reveal-delay-3">
                    <div class="kbli-header">
                        <span class="kbli-code">KBLI 46511</span>
                        <div class="kbli-icon"><i class="fa-solid fa-laptop-code"></i></div>
                    </div>
                    <h4>Komputer & IT Equipment</h4>
                    <p>Perdagangan besar komputer, server, perangkat jaringan, dan infrastruktur IT.</p>
                </div>
                <div class="kbli-card reveal reveal-delay-4">
                    <div class="kbli-header">
                        <span class="kbli-code">KBLI 46900</span>
                        <div class="kbli-icon"><i class="fa-solid fa-boxes-stacked"></i></div>
                    </div>
                    <h4>Perdagangan Umum</h4>
                    <p>Perdagangan besar aneka ragam barang pengadaan umum dan distribusi komoditas.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══ ALUR BISNIS ═══ -->
    <section class="section" id="alur">
        <div class="container">
            <div class="section-header section-header-center reveal">
                <h2 class="section-title">Alur Bisnis End-to-End</h2>
                <p class="section-subtitle">Seluruh transaksi terhubung untuk transparansi data dan auditabilitas penuh.</p>
            </div>

            <div class="process-grid">
                <div class="process-step reveal">
                    <div class="step-header">
                        <span class="step-num">01</span>
                        <div class="step-icon"><i class="fa-solid fa-file-contract"></i></div>
                    </div>
                    <h4>Tender & Kontrak</h4>
                    <p>Tender LPSE/BUMN yang dimenangkan dikonversi langsung menjadi kontrak kerja resmi.</p>
                </div>
                <div class="process-step reveal reveal-delay-1">
                    <div class="step-header">
                        <span class="step-num">02</span>
                        <div class="step-icon"><i class="fa-solid fa-boxes-packing"></i></div>
                    </div>
                    <h4>Pengadaan Barang</h4>
                    <p>Barang diterima dari supplier langsung tercatat dan menambah persediaan gudang.</p>
                </div>
                <div class="process-step reveal reveal-delay-2">
                    <div class="step-header">
                        <span class="step-num">03</span>
                        <div class="step-icon"><i class="fa-solid fa-cart-flatbed"></i></div>
                    </div>
                    <h4>Penjualan & Stok</h4>
                    <p>Transaksi penjualan memvalidasi kuantitas stok secara otomatis tanpa selisih.</p>
                </div>
                <div class="process-step reveal reveal-delay-3">
                    <div class="step-header">
                        <span class="step-num">04</span>
                        <div class="step-icon"><i class="fa-solid fa-receipt"></i></div>
                    </div>
                    <h4>Invoice & Pelunasan</h4>
                    <p>Tagihan termin diterbitkan dan rekonsiliasi pembayaran tersinkronisasi dalam laporan.</p>
                </div>
            </div>
        </div>
    </section>


    <!-- ═══ FOOTER ═══ -->
    <footer class="site-footer" id="kontak">
        <div class="container">
            <div class="footer-grid">
                <div>
                    <div class="footer-brand">
                        <img src="{{ asset('images/logo-icon.png') }}" alt="PT Signal Panca Utama">
                        <div class="footer-brand-text">
                            <div class="brand-name">Signal Panca Utama</div>
                            <div class="brand-sub">Tender · Jasa · Perdagangan</div>
                        </div>
                    </div>
                    <p class="footer-desc">
                        Perusahaan perdagangan, jasa, dan pengadaan tender terpercaya di Karawang, Jawa Barat. Mitra bisnis BUMN dan instansi pemerintah.
                    </p>
                </div>

                <div>
                    <div class="footer-heading">Navigasi</div>
                    <ul class="footer-links">
                        <li><a href="#beranda">Beranda</a></li>
                        <li><a href="#layanan">Layanan</a></li>
                        <li><a href="#kbli">Kualifikasi KBLI</a></li>
                        <li><a href="#alur">Alur Bisnis</a></li>
                    </ul>
                </div>

                <div>
                    <div class="footer-heading">Layanan</div>
                    <ul class="footer-links">
                        <li><a href="{{ route('login') }}">Portal Sistem</a></li>
                    </ul>
                </div>

                <div>
                    <div class="footer-heading">Hubungi Kami</div>
                    <ul class="footer-contact-list">
                        <li>
                            <a href="https://maps.google.com/?q=Jl+Rubaya+Buher+SPU+Mansion+Kavling+Cahaya,+Karawang,+Jawa+Barat+41315" target="_blank" rel="noopener noreferrer" class="footer-contact-link" title="Buka lokasi di Google Maps">
                                <i class="fa-solid fa-location-dot"></i>
                                <span>Jl Rubaya Buher SPU Mansion Kavling Cahaya, Karawang, Jawa Barat 41315</span>
                            </a>
                        </li>
                        <li>
                            <a href="mailto:info@signalpanca.co.id" class="footer-contact-link" title="Kirim Email">
                                <i class="fa-solid fa-envelope"></i>
                                <span>info@signalpanca.co.id</span>
                            </a>
                        </li>
                        <li>
                            <a href="tel:02178901234" class="footer-contact-link" title="Hubungi Telepon">
                                <i class="fa-solid fa-phone"></i>
                                <span>(021) 7890-1234</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                Dikembangkan oleh Tim Internal IT<br>
                &copy; {{ date('Y') }} PT Signal Panca Utama. Seluruh hak cipta dilindungi.
            </div>
        </div>
    </footer>

    <script>
        // Header, sections & navigation elements
        const header = document.getElementById('siteHeader');
        const sections = document.querySelectorAll('section[id], footer[id]');
        const navLinks = document.querySelectorAll('.nav-links a');
        let ticking = false;
        let isManualScroll = false;
        let manualScrollTimer = null;

        // Smooth scroll for anchor links with instantaneous visual feedback
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                const targetId = this.getAttribute('href');
                if (!targetId) return;

                if (targetId === '#') {
                    e.preventDefault();
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                    navLinks.forEach(link => link.classList.toggle('active', link.getAttribute('href') === '#beranda'));
                    return;
                }

                const target = document.querySelector(targetId);
                if (!target) return;

                e.preventDefault();

                // 1. Instant feedback: highlight the active capsule immediately without delay
                navLinks.forEach(link => {
                    link.classList.toggle('active', link.getAttribute('href') === targetId);
                });

                // 2. Prevent scroll spy from intermediate flickering during smooth transit
                isManualScroll = true;
                clearTimeout(manualScrollTimer);
                manualScrollTimer = setTimeout(() => {
                    isManualScroll = false;
                }, 700);

                // 3. Smooth scroll with header offset
                const headerHeight = header ? header.offsetHeight : 72;
                const targetPosition = target.getBoundingClientRect().top + window.pageYOffset - (headerHeight + 6);

                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });
            });
        });

        // Header scroll shadow and active nav spy
        window.addEventListener('scroll', () => {
            if (!ticking) {
                requestAnimationFrame(() => {
                    if (header) {
                        header.classList.toggle('scrolled', window.scrollY > 10);
                    }

                    // Only spy when not in manual click transition
                    if (!isManualScroll) {
                        const isBottom = (window.innerHeight + window.scrollY) >= (document.documentElement.scrollHeight - 60);
                        if (isBottom) {
                            navLinks.forEach(link => {
                                link.classList.toggle('active', link.getAttribute('href') === '#kontak');
                            });
                        } else {
                            const scrollPos = window.scrollY + 140;
                            sections.forEach(section => {
                                const top = section.offsetTop;
                                const height = section.offsetHeight;
                                const id = section.getAttribute('id');
                                if (scrollPos >= top && scrollPos < top + height) {
                                    navLinks.forEach(link => {
                                        link.classList.toggle('active', link.getAttribute('href') === `#${id}`);
                                    });
                                }
                            });
                        }
                    }

                    ticking = false;
                });
                ticking = true;
            }
        }, { passive: true });

        // Mobile menu toggle
        const toggle = document.getElementById('mobileToggle');
        const menu = document.getElementById('mobileMenu');
        toggle.addEventListener('click', () => {
            menu.classList.toggle('active');
            const icon = toggle.querySelector('i');
            icon.classList.toggle('fa-bars');
            icon.classList.toggle('fa-xmark');
        });
        document.querySelectorAll('.mobile-link').forEach(link => {
            link.addEventListener('click', () => {
                menu.classList.remove('active');
                const icon = toggle.querySelector('i');
                icon.classList.add('fa-bars');
                icon.classList.remove('fa-xmark');
            });
        });

        // Scroll reveal with staggered children
        const reveals = document.querySelectorAll('.reveal');
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1, rootMargin: '0px 0px -60px 0px' });
        reveals.forEach(el => observer.observe(el));

        // Interactive 3D flip card toggle
        document.querySelectorAll('.flip-card').forEach(card => {
            card.addEventListener('click', function() {
                this.classList.toggle('is-flipped');
                const isFlipped = this.classList.contains('is-flipped');
                this.setAttribute('aria-expanded', isFlipped);
            });
            card.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    this.click();
                }
            });
        });
    </script>

    <!-- ═══ WHATSAPP FLOATING WIDGET ═══ -->
    <div class="wa-floating-container" id="waContainer" data-phone="6281278901234">
        <!-- Popup Widget Card -->
        <div class="wa-popup-card" id="waPopupCard" role="dialog" aria-modal="true" aria-labelledby="waAgentTitle">
            <!-- Header -->
            <div class="wa-popup-header">
                <div class="wa-agent-info">
                    <div class="wa-avatar">
                        <img src="{{ asset('images/logo-icon.png') }}" alt="PT Signal Panca Utama" width="36" height="36">
                    </div>
                    <div class="wa-agent-text">
                        <h4 id="waAgentTitle">SignalCare (CS PT Signal)</h4>
                        <p>
                            <span class="wa-online-dot"></span>
                            <span>Customer Support · Siap Membantu Anda</span>
                        </p>
                    </div>
                </div>
                <button type="button" class="wa-close-btn" id="waCloseBtn" aria-label="Tutup menu WhatsApp">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- Body -->
            <div class="wa-popup-body">
                <div class="wa-greeting">
                    Halo! Selamat datang di <strong>PT Signal Panca Utama</strong>. Silakan pilih topik pertanyaan Anda seputar Tender, Jasa, atau Perdagangan:
                </div>

                <!-- Pilihan Topik -->
                <div class="wa-options-list" role="radiogroup" aria-label="Topik Pertanyaan">
                    <!-- Option 1: Tender -->
                    <div class="wa-option-card is-selected" role="radio" aria-checked="true" tabindex="0"
                         data-message="Halo CS PT Signal Panca Utama, saya ingin berkonsultasi mengenai pengadaan proyek tender dan portofolio pelaksanaan proyek.">
                        <div class="wa-option-icon" style="color: #2563EB; background: #EFF6FF;">
                            <i class="fa-solid fa-file-signature"></i>
                        </div>
                        <div class="wa-option-content">
                            <div class="wa-option-title">Proyek & Dokumen Tender</div>
                            <div class="wa-option-desc">Kualifikasi tender LPSE, dokumen administrasi, dan RAB proyek</div>
                        </div>
                        <div class="wa-option-actions">
                            <i class="fa-solid fa-check wa-option-check"></i>
                            <button type="button" class="wa-quick-send-btn" title="Kirim langsung ke WhatsApp" aria-label="Kirim langsung topik tender ke WhatsApp">
                                <i class="fa-solid fa-arrow-up-right-from-square"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Option 2: Jasa -->
                    <div class="wa-option-card" role="radio" aria-checked="false" tabindex="0"
                         data-message="Halo CS PT Signal Panca Utama, saya ingin menanyakan penawaran layanan jasa teknik, jadwal penugasan teknisi, instalasi, dan pemeliharaan.">
                        <div class="wa-option-icon" style="color: #059669; background: #ECFDF5;">
                            <i class="fa-solid fa-screwdriver-wrench"></i>
                        </div>
                        <div class="wa-option-content">
                            <div class="wa-option-title">Layanan Jasa & Pemeliharaan</div>
                            <div class="wa-option-desc">Jasa teknik, penugasan teknisi, instalasi, dan jadwal maintenance</div>
                        </div>
                        <div class="wa-option-actions">
                            <i class="fa-solid fa-check wa-option-check"></i>
                            <button type="button" class="wa-quick-send-btn" title="Kirim langsung ke WhatsApp" aria-label="Kirim langsung topik jasa ke WhatsApp">
                                <i class="fa-solid fa-arrow-up-right-from-square"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Option 3: Dagang / Barang -->
                    <div class="wa-option-card" role="radio" aria-checked="false" tabindex="0"
                         data-message="Halo CS PT Signal Panca Utama, saya ingin menanyakan ketersediaan stok produk dan pemesanan barang dagang untuk kebutuhan operasional kami.">
                        <div class="wa-option-icon" style="color: #D97706; background: #FFFBEB;">
                            <i class="fa-solid fa-boxes-packing"></i>
                        </div>
                        <div class="wa-option-content">
                            <div class="wa-option-title">Pemesanan Barang Dagang</div>
                            <div class="wa-option-desc">Ketersediaan stok barang dagang, pengadaan material, dan distribusi</div>
                        </div>
                        <div class="wa-option-actions">
                            <i class="fa-solid fa-check wa-option-check"></i>
                            <button type="button" class="wa-quick-send-btn" title="Kirim langsung ke WhatsApp" aria-label="Kirim langsung topik barang dagang ke WhatsApp">
                                <i class="fa-solid fa-arrow-up-right-from-square"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Option 4: Kemitraan & Informasi Umum -->
                    <div class="wa-option-card" role="radio" aria-checked="false" tabindex="0"
                         data-message="Halo CS PT Signal Panca Utama, saya ingin berdiskusi mengenai peluang kemitraan vendor / supplier dan informasi umum perusahaan.">
                        <div class="wa-option-icon" style="color: #7C3AED; background: #F5F3FF;">
                            <i class="fa-solid fa-handshake-simple"></i>
                        </div>
                        <div class="wa-option-content">
                            <div class="wa-option-title">Kemitraan Vendor & Info Umum</div>
                            <div class="wa-option-desc">Kerja sama rekanan supplier, subkontraktor, dan profil perusahaan</div>
                        </div>
                        <div class="wa-option-actions">
                            <i class="fa-solid fa-check wa-option-check"></i>
                            <button type="button" class="wa-quick-send-btn" title="Kirim langsung ke WhatsApp" aria-label="Kirim langsung topik kemitraan ke WhatsApp">
                                <i class="fa-solid fa-arrow-up-right-from-square"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Input Catatan Tambahan (Opsional) -->
                <div class="wa-custom-note-wrap">
                    <textarea id="waCustomNote" rows="2" placeholder="Tuliskan detail pertanyaan atau catatan Anda (opsional)..." class="wa-custom-note"></textarea>
                </div>

                <!-- Tombol CTA Utama -->
                <button type="button" id="waSendBtn" class="wa-submit-btn">
                    <i class="fa-brands fa-whatsapp text-lg"></i>
                    <span>Mulai Chat WhatsApp</span>
                </button>
            </div>
        </div>

        <!-- Floating Trigger Button (WhatsApp Green Circle) -->
        <button type="button" class="wa-trigger-btn" id="waTriggerBtn" aria-label="Buka bantuan WhatsApp PT Signal Panca Utama" aria-expanded="false" title="Hubungi Kami via WhatsApp">
            <i class="fa-brands fa-whatsapp wa-trigger-icon"></i>
            <span class="wa-badge" id="waBadge">1</span>
        </button>
    </div>

    <!-- WhatsApp Floating Widget Script -->
    <script>
        (function() {
            function initWhatsAppWidget() {
                const container = document.getElementById('waContainer');
                if (!container || container.dataset.initialized) return;
                container.dataset.initialized = 'true';

                const triggerBtn = document.getElementById('waTriggerBtn');
                const popupCard = document.getElementById('waPopupCard');
                const closeBtn = document.getElementById('waCloseBtn');
                const badge = document.getElementById('waBadge');
                const optionCards = document.querySelectorAll('.wa-option-card');
                const customNote = document.getElementById('waCustomNote');
                const sendBtn = document.getElementById('waSendBtn');
                const phoneNumber = container.getAttribute('data-phone') || '6281278901234';

                let selectedMessage = '';
                const initialSelected = document.querySelector('.wa-option-card.is-selected');
                if (initialSelected) {
                    selectedMessage = initialSelected.getAttribute('data-message') || '';
                }

                function openWidget() {
                    popupCard.classList.add('is-open');
                    triggerBtn.setAttribute('aria-expanded', 'true');
                    if (badge) badge.classList.add('is-hidden');
                }

                function closeWidget() {
                    popupCard.classList.remove('is-open');
                    triggerBtn.setAttribute('aria-expanded', 'false');
                }

                function toggleWidget() {
                    if (popupCard.classList.contains('is-open')) {
                        closeWidget();
                    } else {
                        openWidget();
                    }
                }

                function sendToWhatsApp(message) {
                    const note = customNote ? customNote.value.trim() : '';
                    let finalMsg = message || selectedMessage;
                    if (note) {
                        finalMsg += ' (Catatan tambahan: ' + note + ')';
                    }
                    const url = 'https://wa.me/' + phoneNumber + '?text=' + encodeURIComponent(finalMsg);
                    window.open(url, '_blank', 'noopener,noreferrer');
                }

                if (triggerBtn) {
                    triggerBtn.addEventListener('click', toggleWidget);
                }

                if (closeBtn) {
                    closeBtn.addEventListener('click', closeWidget);
                }

                // Close on click outside
                document.addEventListener('click', function(e) {
                    if (!container.contains(e.target) && popupCard.classList.contains('is-open')) {
                        closeWidget();
                    }
                });

                // Close on Escape key
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape' && popupCard.classList.contains('is-open')) {
                        closeWidget();
                        triggerBtn.focus();
                    }
                });

                // Handle option selection
                optionCards.forEach(card => {
                    card.addEventListener('click', function(e) {
                        if (e.target.closest('.wa-quick-send-btn')) {
                            const msg = this.getAttribute('data-message') || '';
                            sendToWhatsApp(msg);
                            return;
                        }

                        optionCards.forEach(c => {
                            c.classList.remove('is-selected');
                            c.setAttribute('aria-checked', 'false');
                        });
                        this.classList.add('is-selected');
                        this.setAttribute('aria-checked', 'true');
                        selectedMessage = this.getAttribute('data-message') || '';
                    });

                    card.addEventListener('keydown', function(e) {
                        if (e.key === 'Enter' || e.key === ' ') {
                            e.preventDefault();
                            this.click();
                        }
                    });
                });

                // Handle main submit button
                if (sendBtn) {
                    sendBtn.addEventListener('click', function() {
                        sendToWhatsApp();
                    });
                }
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initWhatsAppWidget);
            } else {
                initWhatsAppWidget();
            }
        })();
    </script>
</body>
</html>
