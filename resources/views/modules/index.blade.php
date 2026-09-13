<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} | SignalNiagaJasaTender</title>
    @vite(['resources/css/app.css'])
</head>
<body>
    <main class="app-shell">
        <aside class="sidebar">
            <div class="brand-mark">
                <span class="brand-icon">SPU</span>
                <div><strong>Signal Panca Utama</strong><small>Business Management</small></div>
            </div>
            <nav class="nav-list" aria-label="Navigasi utama">
                <a class="nav-link" href="/">Overview</a>
                <a class="nav-link {{ $title === 'Tender' ? 'active' : '' }}" href="{{ route('tender.index') }}">Tender</a>
                <a class="nav-link {{ $title === 'Jasa' ? 'active' : '' }}" href="{{ route('jasa.index') }}">Jasa</a>
                <a class="nav-link {{ $title === 'Perdagangan' ? 'active' : '' }}" href="{{ route('perdagangan.index') }}">Perdagangan</a>
                <a class="nav-link {{ $title === 'Finance' ? 'active' : '' }}" href="{{ route('finance.index') }}">Finance</a>
                <a class="nav-link {{ $title === 'Laporan' ? 'active' : '' }}" href="{{ route('laporan.index') }}">Laporan</a>
            </nav>
            <div class="sidebar-footer"><span class="status-dot"></span>Sistem internal aktif</div>
        </aside>

        <section class="content">
            <header class="topbar">
                <div><p class="eyebrow">PT Signal Panca Utama / Modul bisnis</p><h1>{{ $title }}</h1></div>
                <a class="back-link" href="/">← Kembali ke overview</a>
            </header>
            <section class="module-intro">
                <p class="eyebrow">Workspace {{ strtoupper($title) }}</p>
                <h2>{{ $description }}</h2>
                <p class="module-note">Gunakan modul ini sebagai pusat pencatatan dan pemantauan data {{ strtolower($title) }} sesuai alur kerja perusahaan.</p>
            </section>
            <section class="metric-grid" aria-label="Ringkasan {{ $title }}">
                @foreach ($metrics as $metric)
                    <article class="metric-card"><span>{{ $metric['label'] }}</span><strong>{{ $metric['value'] }}</strong><small>{{ $metric['detail'] }}</small></article>
                @endforeach
            </section>
            <section class="panel module-table-panel">
                <div class="panel-heading"><div><p class="eyebrow">Data {{ $title }}</p><h3>Daftar terbaru</h3></div><button class="action-button" type="button" disabled>Tambah data</button></div>
                <div class="empty-state">
                    <div class="empty-icon">+</div>
                    <h3>Belum ada data transaksi</h3>
                    <p>Struktur database sudah tersedia. Form input dan proses transaksi akan menggunakan tabel sesuai DatabaseStructure_SignalNiagaJasaTender.md.</p>
                </div>
                <div class="table-header">
                    @foreach ($columns as $column)<span>{{ $column }}</span>@endforeach
                </div>
            </section>
        </section>
    </main>
</body>
</html>
