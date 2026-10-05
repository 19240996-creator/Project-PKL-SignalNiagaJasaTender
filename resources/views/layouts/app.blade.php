<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SignalNiagaJasaTender') — PT Signal Panca Utama</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-icon.png') }}">
    <!-- Google Fonts: Plus Jakarta Sans & JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400;1,600&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'system-ui', '-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'Roboto', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'monospace'],
                    },
                    colors: {
                        navy: {
                            800: '#0F172A',
                            900: '#020617',
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
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            background-color: #F8FAFC;
            color: #0F172A;
            letter-spacing: -0.015em;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        .font-mono {
            font-family: 'JetBrains Mono', monospace;
            letter-spacing: -0.025em;
        }
        .sidebar-item-active {
            background-color: rgba(255, 255, 255, 0.08) !important;
            color: #FFFFFF !important;
            font-weight: 600;
            border-left: 3px solid #3B82F6;
        }
        .sidebar-item-active i {
            color: #60A5FA !important;
        }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-[#F8FAFC] antialiased min-h-screen flex flex-col text-slate-800" x-data="{ sidebarOpen: false }">

    @php
        $role = Auth::user()->role->name ?? '';
        $isOwner = ($role === 'owner');
        $isManager = ($role === 'manager');
        $isAdmin = ($role === 'admin');

        // Hak akses modul
        $canTender = true;
        $canService = true;
        $canGoods = true;
        $canFinance = true;
        $canReport = true;

        // Notifikasi pengajuan menunggu verifikasi
        $pendingTenders = $isManager ? \App\Models\Tender::where('approval_status', 'pending')->count() : 0;
        $pendingServices = $isManager ? \App\Models\ServiceJob::where('approval_status', 'pending')->count() : 0;
        $pendingSales = $isManager ? \App\Models\Sale::where('approval_status', 'pending')->count() : 0;
        $totalPending = $pendingTenders + $pendingServices + $pendingSales;
    @endphp

    <div class="flex h-screen overflow-hidden bg-[#F8FAFC]">
        <!-- Sidebar Corporate Charcoal Navy -->
        <aside class="fixed inset-y-0 left-0 z-30 w-64 bg-[#0F172A] text-slate-300 border-r border-slate-800 transform transition-transform duration-200 ease-in-out md:translate-x-0 md:static flex flex-col justify-between"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
            <div>
                <!-- Brand Header -->
                <div class="h-16 flex items-center px-5 border-b border-slate-800 bg-[#090E17] text-white justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 rounded-md bg-white p-1 flex items-center justify-center shadow-xs shrink-0">
                            <img src="{{ asset('images/logo-icon.png') }}" alt="SPU" class="h-full w-full object-contain">
                        </div>
                        <div class="truncate">
                            <span class="font-bold text-sm tracking-tight text-white block leading-tight truncate">Signal Panca Utama</span>
                            <span class="text-[10px] text-blue-300 font-medium tracking-wide block truncate">Enterprise Portal</span>
                        </div>
                    </div>
                    <button @click="sidebarOpen = false" class="md:hidden text-slate-400 hover:text-white">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>

                <!-- Navigation Links -->
                <nav class="mt-4 px-3 space-y-1 overflow-y-auto max-h-[calc(100vh-140px)] text-sm">
                    <!-- Dashboard -->
                    <a href="{{ route('dashboard') }}" class="group flex items-center px-3 py-2 text-xs font-medium rounded-md text-slate-300 hover:bg-white/10 hover:text-white transition-colors {{ request()->routeIs('dashboard') ? 'sidebar-item-active' : '' }}">
                        <i class="fa-solid fa-gauge w-5 text-center text-blue-400 group-hover:text-white"></i>
                        <span>Dashboard</span>
                    </a>

                    @if($isOwner)
                        <div class="pt-4 pb-1.5 px-3 text-[10px] font-bold text-blue-300/70 uppercase tracking-wider">
                            Laporan Bisnis
                        </div>

                        <!-- 1. Laporan Tender (Biru) -->
                        <a href="{{ route('laporan.index', ['domain' => 'tender']) }}" class="group flex items-center px-3 py-2 text-xs font-medium rounded-md text-slate-300 hover:bg-white/10 hover:text-white transition-colors {{ request()->routeIs('laporan.*') && request('domain') === 'tender' ? 'sidebar-item-active' : '' }}">
                            <i class="fa-solid fa-file-contract w-5 text-center text-blue-400 group-hover:text-white"></i>
                            <span class="truncate">Laporan Tender</span>
                        </a>

                        <!-- 2. Laporan Jasa (Hijau) -->
                        <a href="{{ route('laporan.index', ['domain' => 'jasa']) }}" class="group flex items-center px-3 py-2 text-xs font-medium rounded-md text-slate-300 hover:bg-white/10 hover:text-white transition-colors {{ request()->routeIs('laporan.*') && request('domain') === 'jasa' ? 'sidebar-item-active' : '' }}">
                            <i class="fa-solid fa-wrench w-5 text-center text-emerald-400 group-hover:text-white"></i>
                            <span class="truncate">Laporan Jasa</span>
                        </a>

                        <!-- 3. Laporan Dagang (Ungu) -->
                        <a href="{{ route('laporan.index', ['domain' => 'barang']) }}" class="group flex items-center px-3 py-2 text-xs font-medium rounded-md text-slate-300 hover:bg-white/10 hover:text-white transition-colors {{ request()->routeIs('laporan.*') && request('domain') === 'barang' ? 'sidebar-item-active' : '' }}">
                            <i class="fa-solid fa-boxes-stacked w-5 text-center text-purple-400 group-hover:text-white"></i>
                            <span class="truncate">Laporan Dagang</span>
                        </a>

                        <!-- 4. Ringkasan 3 Bisnis -->
                        <a href="{{ route('laporan.index', ['domain' => 'semua']) }}" class="group flex items-center px-3 py-2 text-xs font-medium rounded-md text-slate-300 hover:bg-white/10 hover:text-white transition-colors {{ request()->routeIs('laporan.*') && request('domain', 'semua') === 'semua' ? 'sidebar-item-active' : '' }}">
                            <i class="fa-solid fa-chart-pie w-5 text-center text-amber-400 group-hover:text-white"></i>
                            <span class="truncate">Ringkasan 3 Bisnis</span>
                        </a>

                        <div class="pt-4 pb-1.5 px-3 text-[10px] font-bold text-blue-300/70 uppercase tracking-wider">
                            Sistem & Akses
                        </div>
                        <a href="{{ route('users.index') }}" class="group flex items-center px-3 py-2 text-xs font-medium rounded-md text-slate-300 hover:bg-white/10 hover:text-white transition-colors {{ request()->routeIs('users.*') ? 'sidebar-item-active' : '' }}">
                            <i class="fa-solid fa-users-gear w-5 text-center text-blue-400 group-hover:text-white"></i>
                            <span>Pengguna & Hak Akses</span>
                        </a>
                        <a href="{{ route('permissions.index') }}" class="group flex items-center px-3 py-2 text-xs font-medium rounded-md text-slate-300 hover:bg-white/10 hover:text-white transition-colors {{ request()->routeIs('permissions.*') ? 'sidebar-item-active' : '' }}">
                            <i class="fa-solid fa-key w-5 text-center text-blue-400 group-hover:text-white"></i>
                            <span>Permission Role</span>
                        </a>
                        <a href="{{ route('partner-logos.index') }}" class="group flex items-center px-3 py-2 text-xs font-medium rounded-md text-slate-300 hover:bg-white/10 hover:text-white transition-colors {{ request()->routeIs('partner-logos.*') ? 'sidebar-item-active' : '' }}">
                            <i class="fa-solid fa-images w-5 text-center text-blue-400 group-hover:text-white"></i>
                            <span>Kelola Logo Klien</span>
                        </a>
                        <a href="{{ route('activity-logs.index') }}" class="group flex items-center px-3 py-2 text-xs font-medium rounded-md text-slate-300 hover:bg-white/10 hover:text-white transition-colors {{ request()->routeIs('activity-logs.*') ? 'sidebar-item-active' : '' }}">
                            <i class="fa-solid fa-clock-rotate-left w-5 text-center text-blue-400 group-hover:text-white"></i>
                            <span>Log Aktivitas</span>
                        </a>
                    @else
                        <div class="pt-4 pb-1.5 px-3 text-[10px] font-bold text-blue-300/70 uppercase tracking-wider">
                            Operasional Bisnis
                        </div>

                        <!-- 1. Domain Tender (Biru) -->
                        <a href="{{ route('tender.index') }}" class="group flex items-center px-3 py-2 text-xs font-medium rounded-md text-slate-300 hover:bg-white/10 hover:text-white transition-colors {{ request()->routeIs('tender.*') ? 'sidebar-item-active' : '' }}">
                            <i class="fa-solid fa-file-contract w-5 text-center text-blue-400 group-hover:text-white"></i>
                            <span class="truncate">Tender</span>
                            @if($pendingTenders > 0)
                                <span class="ml-auto px-1.5 py-0.5 text-[10px] font-bold bg-amber-400 text-slate-900 rounded">{{ $pendingTenders }}</span>
                            @endif
                        </a>

                        <!-- 2. Domain Jasa (Hijau) -->
                        <a href="{{ route('jasa.index') }}" class="group flex items-center px-3 py-2 text-xs font-medium rounded-md text-slate-300 hover:bg-white/10 hover:text-white transition-colors {{ request()->routeIs('jasa.*') ? 'sidebar-item-active' : '' }}">
                            <i class="fa-solid fa-wrench w-5 text-center text-emerald-400 group-hover:text-white"></i>
                            <span class="truncate">Jasa</span>
                            @if($pendingServices > 0)
                                <span class="ml-auto px-1.5 py-0.5 text-[10px] font-bold bg-amber-400 text-slate-900 rounded">{{ $pendingServices }}</span>
                            @endif
                        </a>

                        <!-- 3. Domain Dagang (Ungu) -->
                        <a href="{{ route('sales.index') }}" class="group flex items-center px-3 py-2 text-xs font-medium rounded-md text-slate-300 hover:bg-white/10 hover:text-white transition-colors {{ request()->routeIs('sales.*') || request()->routeIs('perdagangan.*') ? 'sidebar-item-active' : '' }}">
                            <i class="fa-solid fa-boxes-stacked w-5 text-center text-purple-400 group-hover:text-white"></i>
                            <span class="truncate">Dagang (Barang)</span>
                            @if($pendingSales > 0)
                                <span class="ml-auto px-1.5 py-0.5 text-[10px] font-bold bg-amber-400 text-slate-900 rounded">{{ $pendingSales }}</span>
                            @endif
                        </a>

                        <div class="pt-4 pb-1.5 px-3 text-[10px] font-bold text-blue-300/70 uppercase tracking-wider">
                            Laporan
                        </div>
                        <a href="{{ route('laporan.index') }}" class="group flex items-center px-3 py-2 text-xs font-medium rounded-md text-slate-300 hover:bg-white/10 hover:text-white transition-colors {{ request()->routeIs('laporan.*') ? 'sidebar-item-active' : '' }}">
                            <i class="fa-solid fa-chart-pie w-5 text-center text-amber-400 group-hover:text-white"></i>
                            <span>Laporan {{ $isAdmin ? 'Saya' : 'Bisnis' }}</span>
                        </a>
                    @endif
                </nav>
            </div>

            <!-- Footer User Profile -->
            <div class="p-3 border-t border-slate-800 bg-[#090E17] flex items-center justify-between">
                <div class="flex items-center space-x-2.5 overflow-hidden">
                    <div class="w-8 h-8 rounded-md bg-slate-800 border border-slate-700 text-slate-200 font-bold flex items-center justify-center text-xs shrink-0">
                        {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 2)) }}
                    </div>
                    <div class="truncate">
                        <div class="text-xs font-semibold text-white truncate leading-tight">{{ Auth::user()->name ?? 'Pengguna' }}</div>
                        <div class="text-[10px] text-slate-400 font-medium uppercase tracking-wider">{{ $role }}</div>
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-slate-400 hover:text-rose-400 p-1.5 rounded transition-colors" title="Keluar">
                        <i class="fa-solid fa-arrow-right-from-bracket text-xs"></i>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col overflow-y-auto">
            <!-- Header Nav Crisp White with Blue Elements -->
            <header class="bg-white border-b border-slate-200/90 h-14 flex items-center px-6 justify-between sticky top-0 z-20 shadow-xs">
                <div class="flex items-center space-x-3">
                    <button @click="sidebarOpen = !sidebarOpen" class="md:hidden text-slate-600 hover:text-blue-600">
                        <i class="fa-solid fa-bars text-base"></i>
                    </button>
                    <div class="flex items-center space-x-2">
                        <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                        <h1 class="text-sm font-bold text-slate-900 tracking-tight">
                            @yield('header-title', 'PT Signal Panca Utama')
                        </h1>
                    </div>
                </div>

                <div class="flex items-center space-x-2.5">
                    @if($isManager && $totalPending > 0)
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-semibold bg-amber-50 text-amber-900 border border-amber-300 hover:bg-amber-100 transition shadow-xs">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                            <span>{{ $totalPending }} Menunggu Persetujuan</span>
                        </a>
                    @endif

                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                        <i class="fa-solid fa-shield-halved text-[10px] text-slate-500"></i>
                        {{ ucfirst($role) }}
                    </span>
                </div>
            </header>

            <!-- Main Container -->
            <main class="flex-1 p-5 md:p-6 max-w-7xl w-full mx-auto">
                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="mb-5 p-3.5 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between text-xs shadow-xs">
                        <div class="flex items-center space-x-2.5">
                            <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
                            <span class="font-medium">{{ session('success') }}</span>
                        </div>
                        <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700"><i class="fa-solid fa-xmark"></i></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-5 p-3.5 rounded-lg bg-rose-50 border border-rose-200 text-rose-800 flex items-center justify-between text-xs shadow-xs">
                        <div class="flex items-center space-x-2.5">
                            <i class="fa-solid fa-circle-exclamation text-rose-600 text-sm"></i>
                            <span class="font-medium">{{ session('error') }}</span>
                        </div>
                        <button onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700"><i class="fa-solid fa-xmark"></i></button>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    @yield('scripts')
</body>
</html>
