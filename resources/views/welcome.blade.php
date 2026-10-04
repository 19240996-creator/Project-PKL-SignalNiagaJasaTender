<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PT Signal Panca Utama — Tender, Jasa & Perdagangan</title>
    <meta name="description" content="PT Signal Panca Utama — Perusahaan perdagangan, jasa, dan pengadaan tender terpercaya di Karawang, Jawa Barat. Mitra bisnis BUMN dan instansi pemerintah.">
    <link rel="icon" type="image/png" href="{{ asset('images/logo-icon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite('resources/js/app.js')
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        html {
            scroll-behavior: smooth;
        }

        :root {
            --navy: #0c1d36;
            --navy-light: #162d50;
            --blue: #1a6de3;
            --blue-dark: #1457b8;
            --blue-muted: #3b82c4;
            --teal: #0f9b6e;
            --amber: #d4880f;
            --ink: #111827;
            --body: #4b5563;
            --mute: #6b7280;
            --subtle: #9ca3af;
            --hairline: #e5e7eb;
            --surface: #f9fafb;
            --canvas: #ffffff;
            --radius: 12px;
            --radius-lg: 20px;
            --shadow-sm: 0 1px 3px rgba(0,0,0,.06), 0 1px 2px rgba(0,0,0,.04);
            --shadow-md: 0 4px 12px rgba(0,0,0,.07);
            --shadow-lg: 0 12px 40px rgba(0,0,0,.1);
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
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
        .brand-text { line-height: 1.15; }
        .brand-name { font-weight: 800; font-size: 18px; color: var(--ink); letter-spacing: -.02em; }
        .brand-sub { font-size: 10px; font-weight: 700; color: var(--blue); letter-spacing: .06em; text-transform: uppercase; }
        .nav-links { display: flex; align-items: center; gap: 32px; }
        .nav-links a {
            font-size: 14px;
            font-weight: 500;
            color: var(--mute);
            transition: color .2s;
            position: relative;
        }
        .nav-links a:hover { color: var(--ink); }
        .nav-links a::after {
            content: '';
            position: absolute;
            bottom: -4px; left: 0;
            width: 0; height: 2px;
            background: var(--blue);
            transition: width .2s ease;
        }
        .nav-links a:hover::after { width: 100%; }
        .header-actions { display: flex; align-items: center; gap: 12px; }

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
            transition: all .2s ease;
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
            box-shadow: 0 2px 8px rgba(26,109,227,.25);
        }
        .btn-primary:hover { background: var(--blue-dark); transform: translateY(-1px); box-shadow: 0 4px 14px rgba(26,109,227,.3); }
        .btn-primary:active { transform: translateY(0); }
        .btn-lg { padding: 14px 32px; font-size: 15px; border-radius: 12px; }
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
            background: linear-gradient(135deg, rgba(12,29,54,.88) 0%, rgba(22,45,80,.75) 40%, rgba(26,109,227,.45) 100%);
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
        .hero-badge i { color: #5cb5f5; font-size: 10px; }
        .hero h1 {
            font-size: clamp(2.6rem, 5vw, 4rem);
            font-weight: 800;
            color: #fff;
            line-height: 1.08;
            letter-spacing: -.035em;
            margin-bottom: 20px;
        }
        .hero h1 em {
            font-style: normal;
            color: #5cb5f5;
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
            font-weight: 800;
            color: #fff;
            letter-spacing: -.02em;
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
        .section { padding: 100px 0; }
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
        .section-dark .section-label { color: #5cb5f5; }
        .section-title {
            font-size: clamp(1.75rem, 3vw, 2.5rem);
            font-weight: 800;
            letter-spacing: -.03em;
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
            padding: 56px 0;
            border-bottom: 1px solid var(--hairline);
            overflow: hidden;
        }
        .clients-label {
            text-align: center;
            font-size: 13px;
            font-weight: 600;
            color: var(--subtle);
            letter-spacing: .04em;
            text-transform: uppercase;
            margin-bottom: 28px;
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
        .marquee-track::before { left: 0; background: linear-gradient(90deg, #fff, transparent); }
        .marquee-track::after { right: 0; background: linear-gradient(270deg, #fff, transparent); }
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
            border-color: #bfdbfe;
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
            font-size: 28px;
            font-weight: 800;
            letter-spacing: -.025em;
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

        /* ─── BUSINESS PILLARS ─────────────────────────── */
        .pillar-intro {
            display: grid;
            grid-template-columns: minmax(0, 1.2fr) minmax(260px, .8fr);
            gap: 48px;
            align-items: end;
            margin-bottom: 44px;
        }
        .pillar-intro .section-header { margin-bottom: 0; }
        .pillar-note {
            padding: 18px 0 2px 24px;
            border-left: 2px solid var(--blue);
            color: var(--body);
            font-size: 14px;
            line-height: 1.7;
        }
        .pillar-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.15fr) minmax(300px, .85fr);
            gap: 20px;
            align-items: stretch;
        }
        .pillar-card {
            position: relative;
            display: flex;
            flex-direction: column;
            min-width: 0;
            overflow: hidden;
            background: var(--canvas);
            border: 1px solid var(--hairline);
            border-radius: var(--radius-lg);
            transition: border-color .25s ease, box-shadow .25s ease, transform .25s ease;
        }
        .pillar-card:hover {
            border-color: #b8d5f7;
            box-shadow: var(--shadow-lg);
            transform: translateY(-3px);
        }
        .pillar-card-primary { grid-row: span 2; }
        .pillar-card-image {
            position: relative;
            overflow: hidden;
            background: #dbeafe;
        }
        .pillar-card-primary .pillar-card-image { min-height: 286px; }
        .pillar-card-secondary .pillar-card-image { height: 148px; }
        .pillar-card-secondary:nth-child(2) .pillar-card-image {
            height: 180px;
        }
        .pillar-card-secondary:nth-child(2) .pillar-card-image img {
            object-position: 68% 38%;
        }
        .pillar-card-image::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, transparent 52%, rgba(12,29,54,.42));
            pointer-events: none;
        }
        .pillar-card-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform .45s cubic-bezier(.22, 1, .36, 1);
        }
        .pillar-card:hover .pillar-card-image img { transform: scale(1.04); }
        .pillar-card-index {
            position: absolute;
            top: 18px;
            left: 18px;
            z-index: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 42px;
            height: 28px;
            border: 1px solid rgba(255,255,255,.55);
            border-radius: 7px;
            background: rgba(12,29,54,.58);
            color: #fff;
            font-family: 'SF Mono', 'Fira Code', monospace;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .04em;
            backdrop-filter: blur(8px);
        }
        .pillar-card-body { padding: 26px 28px 28px; }
        .pillar-card-secondary .pillar-card-body { padding: 20px 22px 22px; }
        .pillar-card-kicker {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 10px;
            color: var(--blue);
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .09em;
            text-transform: uppercase;
        }
        .pillar-card-kicker::before {
            content: '';
            width: 20px;
            height: 2px;
            background: currentColor;
        }
        .pillar-card-secondary:nth-child(3) .pillar-card-kicker { color: var(--teal); }
        .pillar-card h3 {
            margin-bottom: 10px;
            color: var(--ink);
            font-size: 24px;
            font-weight: 800;
            letter-spacing: -.03em;
            line-height: 1.2;
        }
        .pillar-card-secondary h3 { font-size: 20px; }
        .pillar-card p {
            color: var(--body);
            font-size: 14px;
            line-height: 1.7;
        }
        .pillar-features {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px 18px;
            margin-top: 22px;
            padding-top: 20px;
            border-top: 1px solid var(--hairline);
            list-style: none;
        }
        .pillar-features li {
            display: flex;
            gap: 8px;
            color: var(--body);
            font-size: 12px;
            font-weight: 600;
            line-height: 1.45;
        }
        .pillar-features li i { margin-top: 3px; color: var(--blue); font-size: 10px; }
        .pillar-card-secondary .pillar-features {
            display: block;
            margin-top: 16px;
            padding-top: 14px;
        }
        .pillar-card-secondary .pillar-features li + li { margin-top: 8px; }
        .pillar-card-secondary .pillar-features li i { color: var(--teal); }

        @media (max-width: 800px) {
            .pillar-intro { grid-template-columns: 1fr; gap: 24px; margin-bottom: 32px; }
            .pillar-note { padding: 0 0 0 16px; }
            .pillar-grid { grid-template-columns: 1fr; }
            .pillar-card-primary { grid-row: auto; }
            .pillar-card-primary .pillar-card-image { min-height: 230px; }
        }
        @media (max-width: 500px) {
            .pillar-card-body, .pillar-card-secondary .pillar-card-body { padding: 20px; }
            .pillar-features { grid-template-columns: 1fr; }
        }

        @media (max-width: 800px) {
            .feature-block {
                grid-template-columns: 1fr;
                gap: 32px;
                padding: 48px 0;
            }
            .feature-block:nth-child(even) .feature-image { order: 0; }
            .feature-image img { height: 240px; }
        }

        /* ─── KBLI CARDS ─────────────────────────────────── */
        .kbli-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 16px;
        }
        .kbli-card {
            padding: 24px 20px;
            background: var(--canvas);
            border: 1px solid var(--hairline);
            border-radius: var(--radius);
            transition: all .25s ease;
            position: relative;
            overflow: hidden;
        }
        .kbli-card::after {
            content: '';
            position: absolute;
            bottom: 0; left: 0; right: 0;
            height: 3px;
            background: var(--blue);
            transform: scaleX(0);
            transform-origin: left;
            transition: transform .25s ease;
        }
        .kbli-card:hover { border-color: #93c5fd; box-shadow: var(--shadow-md); transform: translateY(-3px); }
        .kbli-card:hover::after { transform: scaleX(1); }
        .kbli-code {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 6px;
            background: #eff6ff;
            color: var(--blue);
            font-size: 11px;
            font-weight: 800;
            font-family: 'SF Mono', 'Fira Code', monospace;
            letter-spacing: .04em;
            margin-bottom: 12px;
        }
        .kbli-card h4 {
            font-size: 14px;
            font-weight: 700;
            color: var(--ink);
            margin-bottom: 6px;
            line-height: 1.3;
        }
        .kbli-card p {
            font-size: 12px;
            color: var(--mute);
            line-height: 1.5;
        }
        @media (max-width: 1000px) { .kbli-grid { grid-template-columns: repeat(3, 1fr); } }
        @media (max-width: 640px) { .kbli-grid { grid-template-columns: repeat(2, 1fr); } }

        /* ─── PROCESS FLOW ────────────────────────────────── */
        .process-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 0;
            position: relative;
        }
        .process-grid::before {
            content: '';
            position: absolute;
            top: 28px;
            left: 14%;
            right: 14%;
            height: 2px;
            background: repeating-linear-gradient(90deg, rgba(255,255,255,.2) 0 8px, transparent 8px 16px);
            z-index: 1;
        }
        .process-step {
            text-align: center;
            padding: 0 20px;
            position: relative;
            z-index: 2;
        }
        .process-num {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 56px; height: 56px;
            margin: 0 auto 20px;
            border-radius: 16px;
            font-weight: 800;
            font-size: 20px;
        }
        .process-step:nth-child(1) .process-num { background: rgba(26,109,227,.2); color: #5cb5f5; }
        .process-step:nth-child(2) .process-num { background: rgba(99,102,241,.2); color: #a5b4fc; }
        .process-step:nth-child(3) .process-num { background: rgba(139,92,246,.2); color: #c4b5fd; }
        .process-step:nth-child(4) .process-num { background: rgba(15,155,110,.2); color: #6ee7b7; }
        .process-step h4 {
            font-size: 16px;
            font-weight: 700;
            color: #fff;
            margin-bottom: 8px;
        }
        .process-step p {
            font-size: 13px;
            color: rgba(255,255,255,.5);
            line-height: 1.6;
        }
        @media (max-width: 800px) {
            .process-grid { grid-template-columns: repeat(2, 1fr); gap: 40px; }
            .process-grid::before { display: none; }
        }
        @media (max-width: 500px) {
            .process-grid { grid-template-columns: 1fr; }
        }

        /* ─── CTA SECTION ─────────────────────────────────── */
        .cta-section {
            padding: 80px 0;
            background: linear-gradient(135deg, var(--navy) 0%, var(--navy-light) 100%);
        }
        .cta-inner {
            text-align: center;
            max-width: 620px;
            margin: 0 auto;
        }
        .cta-inner h2 {
            font-size: clamp(1.8rem, 3vw, 2.4rem);
            font-weight: 800;
            color: #fff;
            letter-spacing: -.03em;
            margin-bottom: 16px;
        }
        .cta-inner p {
            font-size: 16px;
            color: rgba(255,255,255,.55);
            margin-bottom: 32px;
            line-height: 1.6;
        }
        .cta-actions { display: flex; justify-content: center; gap: 14px; flex-wrap: wrap; }

        /* ─── FOOTER ──────────────────────────────────────── */
        .site-footer {
            background: var(--navy);
            color: rgba(255,255,255,.5);
            padding: 64px 0 0;
            border-top: 1px solid rgba(255,255,255,.06);
        }
        .footer-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr;
            gap: 48px;
        }
        .footer-brand { display: flex; align-items: center; gap: 12px; margin-bottom: 16px; }
        .footer-brand img { height: 36px; width: auto; border-radius: 8px; background: #fff; padding: 3px; }
        .footer-brand span { font-weight: 800; font-size: 18px; color: #fff; }
        .footer-desc { font-size: 13px; line-height: 1.7; margin-bottom: 20px; max-width: 380px; }
        .footer-contact { font-size: 12px; line-height: 1.8; }
        .footer-contact i { color: var(--blue-muted); margin-right: 6px; width: 14px; text-align: center; }
        .footer-heading {
            font-size: 13px;
            font-weight: 700;
            color: #fff;
            letter-spacing: .04em;
            text-transform: uppercase;
            margin-bottom: 20px;
        }
        .footer-links { list-style: none; padding: 0; }
        .footer-links li { margin-bottom: 10px; }
        .footer-links a {
            font-size: 13px;
            font-weight: 500;
            color: rgba(255,255,255,.45);
            transition: color .2s;
        }
        .footer-links a:hover { color: #5cb5f5; }
        .footer-bottom {
            margin-top: 48px;
            padding: 20px 0;
            border-top: 1px solid rgba(255,255,255,.08);
            text-align: center;
            font-size: 12px;
            color: rgba(255,255,255,.3);
        }

        @media (max-width: 800px) {
            .footer-grid { grid-template-columns: 1fr; gap: 32px; }
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
                <a href="#beranda">Beranda</a>
                <a href="#layanan">Layanan</a>
                <a href="#kbli">Kualifikasi</a>
                <a href="#alur">Alur Bisnis</a>
                <a href="#kontak">Kontak</a>
            </nav>

            <div class="header-actions">
                <a href="{{ route('login') }}" class="btn btn-ghost">Masuk</a>
                <a href="{{ route('login') }}" class="btn btn-primary">
                    <i class="fa-solid fa-arrow-right-to-bracket"></i> Portal Sistem
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

    <!-- ═══ LAYANAN / DOMAIN BISNIS (Zigzag with Photos) ═══ -->
    <section class="section" id="layanan">
        <div class="container">
            <div class="pillar-intro reveal">
                <div class="section-header">
                    <div class="section-label">Tiga Pilar Bisnis</div>
                    <h2 class="section-title">Satu alur kerja untuk tiga area utama</h2>
                    <p class="section-subtitle">Dari peluang tender hingga barang diterima pelanggan, setiap domain dikelola dengan data yang saling terhubung.</p>
                </div>
                <p class="pillar-note">Struktur ini membantu tim menjaga keputusan, dokumen, dan transaksi tetap berada dalam satu sumber data yang dapat ditelusuri.</p>
            </div>

            <div class="pillar-grid">
                <article class="pillar-card pillar-card-primary reveal">
                    <div class="pillar-card-image">
                        <span class="pillar-card-index">01</span>
                        <img src="{{ asset('images/hero-corporate.jpg') }}" alt="Manajemen Tender PT Signal Panca Utama">
                    </div>
                    <div class="pillar-card-body">
                        <div class="pillar-card-kicker">Domain utama</div>
                        <h3>Manajemen Tender</h3>
                        <p>Pantau peluang LPSE dan BUMN, siapkan dokumen penawaran, lalu kelola pipeline sampai tender beralih menjadi kontrak kerja.</p>
                        <ul class="pillar-features">
                            <li><i class="fa-solid fa-check"></i><span>Pipeline tender aktif</span></li>
                            <li><i class="fa-solid fa-check"></i><span>Peringatan deadline</span></li>
                            <li><i class="fa-solid fa-check"></i><span>Konversi ke kontrak</span></li>
                            <li><i class="fa-solid fa-check"></i><span>Arsip dokumen terpusat</span></li>
                        </ul>
                    </div>
                </article>

                <article class="pillar-card pillar-card-secondary reveal reveal-delay-1">
                    <div class="pillar-card-image">
                        <span class="pillar-card-index">02</span>
                        <img src="{{ asset('images/section-finance.jpg') }}" alt="Manajemen Jasa dan Keuangan">
                    </div>
                    <div class="pillar-card-body">
                        <div class="pillar-card-kicker">Domain pendukung</div>
                        <h3>Manajemen Jasa</h3>
                        <p>Kelola klien, kontrak, progres pekerjaan, fee komisi, dan tagihan termin dalam satu alur.</p>
                        <ul class="pillar-features">
                            <li><i class="fa-solid fa-check"></i><span>Kontrak dan fee komisi</span></li>
                            <li><i class="fa-solid fa-check"></i><span>Invoice dan riwayat pembayaran</span></li>
                        </ul>
                    </div>
                </article>

                <article class="pillar-card pillar-card-secondary reveal reveal-delay-2">
                    <div class="pillar-card-image">
                        <span class="pillar-card-index">03</span>
                        <img src="{{ asset('images/section-warehouse.jpg') }}" alt="Gudang dan Pengadaan Barang">
                    </div>
                    <div class="pillar-card-body">
                        <div class="pillar-card-kicker">Domain pendukung</div>
                        <h3>Perdagangan & Pengadaan</h3>
                        <p>Hubungkan supplier, stok gudang, transaksi penjualan, dan invoice dengan kontrol yang jelas.</p>
                        <ul class="pillar-features">
                            <li><i class="fa-solid fa-check"></i><span>Validasi stok saat penjualan</span></li>
                            <li><i class="fa-solid fa-check"></i><span>Rekap mutasi barang</span></li>
                        </ul>
                    </div>
                </article>
            </div>
        </div>
    </section>

    <!-- ═══ RUANG LINGKUP KBLI ═══ -->
    <section class="section section-alt" id="kbli">
        <div class="container">
            <div class="section-header section-header-center reveal">
                <div class="section-label">Kualifikasi Resmi</div>
                <h2 class="section-title">Ruang Lingkup KBLI</h2>
                <p class="section-subtitle">Klasifikasi Baku Lapangan Usaha Indonesia resmi yang dimiliki PT Signal Panca Utama.</p>
            </div>

            <div class="kbli-grid">
                <div class="kbli-card reveal">
                    <span class="kbli-code">46100</span>
                    <h4>Perdagangan Balas Jasa</h4>
                    <p>Perdagangan besar atas dasar balas jasa (fee) atau kontrak.</p>
                </div>
                <div class="kbli-card reveal reveal-delay-1">
                    <span class="kbli-code">46422</span>
                    <h4>Percetakan & Penerbitan</h4>
                    <p>Perdagangan besar barang percetakan dalam berbagai bentuk.</p>
                </div>
                <div class="kbli-card reveal reveal-delay-2">
                    <span class="kbli-code">46499</span>
                    <h4>Perlengkapan Rumah Tangga</h4>
                    <p>Perdagangan besar perlengkapan kantor dan rumah tangga.</p>
                </div>
                <div class="kbli-card reveal reveal-delay-3">
                    <span class="kbli-code">46511</span>
                    <h4>Komputer & IT Equipment</h4>
                    <p>Perdagangan besar komputer, server, dan perangkat IT.</p>
                </div>
                <div class="kbli-card reveal reveal-delay-4">
                    <span class="kbli-code">46900</span>
                    <h4>Berbagai Macam Barang</h4>
                    <p>Perdagangan besar berbagai macam barang pengadaan umum.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══ ALUR BISNIS ═══ -->
    <section class="section section-dark" id="alur">
        <div class="container">
            <div class="section-header section-header-center reveal">
                <div class="section-label">Proses Terintegrasi</div>
                <h2 class="section-title">Alur Bisnis End-to-End</h2>
                <p class="section-subtitle">Seluruh transaksi terhubung untuk transparansi data dan auditabilitas penuh.</p>
            </div>

            <div class="process-grid">
                <div class="process-step reveal">
                    <div class="process-num">1</div>
                    <h4>Tender Menang</h4>
                    <p>Tender dimenangkan dan dikonversi otomatis menjadi kontrak kerja jasa.</p>
                </div>
                <div class="process-step reveal reveal-delay-1">
                    <div class="process-num">2</div>
                    <h4>Pengadaan Barang</h4>
                    <p>Barang diterima dari supplier langsung menambah stok persediaan gudang.</p>
                </div>
                <div class="process-step reveal reveal-delay-2">
                    <div class="process-num">3</div>
                    <h4>Penjualan & Stok</h4>
                    <p>Transaksi penjualan memvalidasi dan mengurangi stok secara akurat.</p>
                </div>
                <div class="process-step reveal reveal-delay-3">
                    <div class="process-num">4</div>
                    <h4>Invoice & Pelunasan</h4>
                    <p>Tagihan diterbitkan dan pembayaran terupdate otomatis dalam laporan.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══ CTA ═══ -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-inner reveal">
                <h2>Siap mengelola bisnis lebih efisien?</h2>
                <p>Masuk ke portal manajemen internal untuk memantau tender, kontrak, persediaan, dan keuangan dalam satu platform.</p>
                <div class="cta-actions">
                    <a href="{{ route('login') }}" class="btn btn-primary btn-lg">
                        <i class="fa-solid fa-arrow-right-to-bracket"></i> Masuk ke Portal
                    </a>
                    <a href="#layanan" class="btn btn-ghost btn-lg" style="border-color: rgba(255,255,255,.2); color: rgba(255,255,255,.7);">
                        <i class="fa-solid fa-layer-group"></i> Lihat Layanan
                    </a>
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
                        <span>PT Signal Panca Utama</span>
                    </div>
                    <p class="footer-desc">
                        Sistem Manajemen Bisnis Internal terintegrasi untuk Tender, Jasa, dan Perdagangan Barang. Memusatkan data perusahaan untuk transparansi dan performa bisnis.
                    </p>
                    <div class="footer-contact">
                        <div><i class="fa-solid fa-location-dot"></i> Jl Rubaya Buher SPU Mansion Kavling Cahaya, Karangpawitan, Kec. Karawang Bar., Karawang, Jawa Barat 41315</div>
                        <div><i class="fa-solid fa-phone"></i> (021) 7890-1234</div>
                        <div><i class="fa-solid fa-envelope"></i> info@signalpanca.co.id</div>
                    </div>
                </div>

                <div>
                    <div class="footer-heading">Navigasi</div>
                    <ul class="footer-links">
                        <li><a href="#beranda">Beranda</a></li>
                        <li><a href="#layanan">Layanan</a></li>
                        <li><a href="#kbli">Kualifikasi KBLI</a></li>
                        <li><a href="#alur">Alur Bisnis</a></li>
                        <li><a href="#kontak">Kontak</a></li>
                    </ul>
                </div>

                <div>
                    <div class="footer-heading">Akses Sistem</div>
                    <p style="font-size:13px;margin-bottom:16px;">Portal manajemen internal perusahaan untuk staf dan management.</p>
                    <a href="{{ route('login') }}" class="btn btn-primary" style="width:100%;">
                        Masuk Portal <i class="fa-solid fa-arrow-right" style="font-size:12px;"></i>
                    </a>
                </div>
            </div>

            <div class="footer-bottom">
                &copy; {{ date('Y') }} PT Signal Panca Utama. Seluruh hak cipta dilindungi.
            </div>
        </div>
    </footer>

    <script>
        // Smooth scroll for anchor links with custom easing
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const targetId = this.getAttribute('href');
                if (targetId === '#') return;
                const target = document.querySelector(targetId);
                if (!target) return;

                const headerHeight = document.getElementById('siteHeader').offsetHeight;
                const targetPosition = target.getBoundingClientRect().top + window.pageYOffset - headerHeight;
                const startPosition = window.pageYOffset;
                const distance = targetPosition - startPosition;
                const duration = Math.min(1200, Math.max(600, Math.abs(distance) * 0.5));
                let startTime = null;

                // Cubic bezier easing (ease-out-quart)
                function easeOutQuart(t) {
                    return 1 - Math.pow(1 - t, 4);
                }

                function animate(currentTime) {
                    if (!startTime) startTime = currentTime;
                    const elapsed = currentTime - startTime;
                    const progress = Math.min(elapsed / duration, 1);
                    const eased = easeOutQuart(progress);
                    window.scrollTo(0, startPosition + distance * eased);
                    if (progress < 1) requestAnimationFrame(animate);
                }
                requestAnimationFrame(animate);
            });
        });

        // Header scroll shadow
        const header = document.getElementById('siteHeader');
        let ticking = false;
        window.addEventListener('scroll', () => {
            if (!ticking) {
                requestAnimationFrame(() => {
                    header.classList.toggle('scrolled', window.scrollY > 10);
                    ticking = false;
                });
                ticking = true;
            }
        });

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
    </script>

</body>
</html>
