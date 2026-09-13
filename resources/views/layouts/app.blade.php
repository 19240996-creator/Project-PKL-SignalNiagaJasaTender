<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SignalNiagaJasaTender') — PT Signal Panca Utama</title>
    <!-- Google Fonts: Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
                            deep: '#1D4ED8',
                            soft: '#EFF6FF',
                            muted: '#DBEAFE',
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
        body { font-family: 'Outfit', sans-serif; background-color: #F8FAFC; color: #1E293B; }
        .sidebar-item-active { background-color: #EFF6FF; color: #2563EB; font-weight: 600; border-right: 3px solid #2563EB; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-slate-50 antialiased min-h-screen flex flex-col" x-data="{ sidebarOpen: false }">

    @php
        $role = Auth::user()->role->name ?? '';
        $isSuperAdmin = ($role === 'super_admin');
    @endphp

    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside class="fixed inset-y-0 left-0 z-30 w-64 bg-white border-r border-slate-200 transform transition-transform duration-200 ease-in-out md:translate-x-0 md:static flex flex-col justify-between"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
            <div>
                <!-- Brand Header -->
                <div class="h-16 flex items-center px-6 border-b border-slate-100 bg-slate-900 text-white justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center font-bold text-white shadow">
                            S
                        </div>
                        <div>
                            <span class="font-bold text-base tracking-tight text-white block leading-none">SignalNiaga</span>
                            <span class="text-[10px] text-blue-400 font-medium tracking-wide uppercase">Jasa & Tender</span>
                        </div>
                    </div>
                    <button @click="sidebarOpen = false" class="md:hidden text-slate-400 hover:text-white">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <!-- Navigation Links -->
                <nav class="mt-4 px-3 space-y-1 overflow-y-auto max-h-[calc(100vh-140px)]">
                    <!-- Dashboard -->
                    <a href="{{ route('dashboard') }}" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition-colors {{ request()->routeIs('dashboard') ? 'sidebar-item-active' : '' }}">
                        <i class="fa-solid fa-chart-line w-6 text-center text-slate-400 {{ request()->routeIs('dashboard') ? 'text-blue-600' : '' }}"></i>
                        <span>Dashboard</span>
                    </a>

                    <!-- Modul Tender -->
                    @if($isSuperAdmin || $role === 'tender_officer')
                        <div class="pt-3 pb-1 px-3 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Modul Tender</div>
                        <a href="{{ route('tender.index') }}" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition-colors {{ request()->routeIs('tender.*') ? 'sidebar-item-active' : '' }}">
                            <i class="fa-solid fa-file-contract w-6 text-center text-slate-400 {{ request()->routeIs('tender.*') ? 'text-blue-600' : '' }}"></i>
                            <span>Tender Pipeline</span>
                        </a>
                    @endif

                    <!-- Modul Jasa & Kontrak -->
                    @if($isSuperAdmin || $role === 'service_officer')
                        <div class="pt-3 pb-1 px-3 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Modul Jasa</div>
                        <a href="{{ route('jasa.index') }}" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition-colors {{ request()->routeIs('jasa.*') ? 'sidebar-item-active' : '' }}">
                            <i class="fa-solid fa-briefcase w-6 text-center text-slate-400 {{ request()->routeIs('jasa.*') ? 'text-blue-600' : '' }}"></i>
                            <span>Pekerjaan Jasa</span>
                        </a>

                        <a href="{{ route('contracts.index') }}" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition-colors {{ request()->routeIs('contracts.*') ? 'sidebar-item-active' : '' }}">
                            <i class="fa-solid fa-file-signature w-6 text-center text-slate-400 {{ request()->routeIs('contracts.*') ? 'text-blue-600' : '' }}"></i>
                            <span>Kontrak Jasa</span>
                        </a>

                        <a href="{{ route('clients.index') }}" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition-colors {{ request()->routeIs('clients.*') ? 'sidebar-item-active' : '' }}">
                            <i class="fa-solid fa-building-user w-6 text-center text-slate-400 {{ request()->routeIs('clients.*') ? 'text-blue-600' : '' }}"></i>
                            <span>Data Klien</span>
                        </a>
                    @endif

                    <!-- Modul Perdagangan & Warehouse -->
                    @if($isSuperAdmin || $role === 'warehouse')
                        <div class="pt-3 pb-1 px-3 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Gudang & Stok</div>
                        <a href="{{ route('products.index') }}" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition-colors {{ request()->routeIs('products.*') ? 'sidebar-item-active' : '' }}">
                            <i class="fa-solid fa-boxes-stacked w-6 text-center text-slate-400 {{ request()->routeIs('products.*') ? 'text-blue-600' : '' }}"></i>
                            <span>Produk & Stok</span>
                        </a>
                    @endif

                    <!-- Modul Purchasing & Supplier -->
                    @if($isSuperAdmin || $role === 'purchasing')
                        <div class="pt-3 pb-1 px-3 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Pengadaan</div>
                        <a href="{{ route('suppliers.index') }}" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition-colors {{ request()->routeIs('suppliers.*') ? 'sidebar-item-active' : '' }}">
                            <i class="fa-solid fa-warehouse w-6 text-center text-slate-400 {{ request()->routeIs('suppliers.*') ? 'text-blue-600' : '' }}"></i>
                            <span>Data Supplier</span>
                        </a>

                        <a href="{{ route('procurements.index') }}" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition-colors {{ request()->routeIs('procurements.*') ? 'sidebar-item-active' : '' }}">
                            <i class="fa-solid fa-truck-ramp-box w-6 text-center text-slate-400 {{ request()->routeIs('procurements.*') ? 'text-blue-600' : '' }}"></i>
                            <span>Pengadaan Barang</span>
                        </a>
                    @endif

                    <!-- Modul Sales -->
                    @if($isSuperAdmin || $role === 'sales')
                        <div class="pt-3 pb-1 px-3 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Penjualan</div>
                        <a href="{{ route('sales.index') }}" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition-colors {{ request()->routeIs('sales.*') || request()->routeIs('perdagangan.*') ? 'sidebar-item-active' : '' }}">
                            <i class="fa-solid fa-cart-shopping w-6 text-center text-slate-400 {{ request()->routeIs('sales.*') ? 'text-blue-600' : '' }}"></i>
                            <span>Transaksi Penjualan</span>
                        </a>
                    @endif

                    <!-- Modul Finance -->
                    @if($isSuperAdmin || $role === 'finance')
                        <div class="pt-3 pb-1 px-3 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Keuangan</div>
                        <a href="{{ route('finance.index') }}" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition-colors {{ request()->routeIs('finance.*') || request()->routeIs('invoices.*') ? 'sidebar-item-active' : '' }}">
                            <i class="fa-solid fa-file-invoice-dollar w-6 text-center text-slate-400 {{ request()->routeIs('finance.*') ? 'text-blue-600' : '' }}"></i>
                            <span>Invoice & Tagihan</span>
                        </a>

                        <a href="{{ route('payments.index') }}" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition-colors {{ request()->routeIs('payments.*') ? 'sidebar-item-active' : '' }}">
                            <i class="fa-solid fa-money-bill-transfer w-6 text-center text-slate-400 {{ request()->routeIs('payments.*') ? 'text-blue-600' : '' }}"></i>
                            <span>Pembayaran</span>
                        </a>
                    @endif

                    <!-- Modul Laporan -->
                    @if($isSuperAdmin || $role === 'management')
                        <div class="pt-3 pb-1 px-3 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Laporan</div>
                        <a href="{{ route('laporan.index') }}" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition-colors {{ request()->routeIs('laporan.*') ? 'sidebar-item-active' : '' }}">
                            <i class="fa-solid fa-print w-6 text-center text-slate-400 {{ request()->routeIs('laporan.*') ? 'text-blue-600' : '' }}"></i>
                            <span>Laporan Bisnis</span>
                        </a>
                    @endif

                    <!-- Modul Khusus Super Admin (Posisi Paling Bawah) -->
                    @if($isSuperAdmin)
                        <div class="pt-3 pb-1 px-3 text-[11px] font-semibold text-purple-600 uppercase tracking-wider">Pengaturan Admin</div>
                        <a href="{{ route('users.index') }}" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg text-slate-600 hover:bg-purple-50 hover:text-purple-700 transition-colors {{ request()->routeIs('users.*') ? 'sidebar-item-active' : '' }}">
                            <i class="fa-solid fa-users-gear w-6 text-center text-purple-600"></i>
                            <span>Manajemen User</span>
                        </a>
                        <a href="{{ route('partner-logos.index') }}" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg text-slate-600 hover:bg-purple-50 hover:text-purple-700 transition-colors {{ request()->routeIs('partner-logos.*') ? 'sidebar-item-active' : '' }}">
                            <i class="fa-solid fa-images w-6 text-center text-purple-600"></i>
                            <span>Kelola Logo Klien</span>
                        </a>
                    @endif
                </nav>
            </div>

            <!-- Footer User Profile -->
            <div class="p-4 border-t border-slate-100 bg-slate-50 flex items-center justify-between">
                <div class="flex items-center space-x-3 overflow-hidden">
                    <div class="w-9 h-9 rounded-full bg-blue-100 text-blue-700 font-bold flex items-center justify-center text-sm shrink-0">
                        {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 2)) }}
                    </div>
                    <div class="truncate">
                        <div class="text-sm font-medium text-slate-800 truncate">{{ Auth::user()->name ?? 'Pengguna' }}</div>
                        <div class="text-[11px] text-blue-600 font-medium tracking-wide uppercase">{{ ucwords(str_replace('_', ' ', $role)) }}</div>
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-slate-400 hover:text-red-600 p-2 rounded-lg transition-colors" title="Keluar">
                        <i class="fa-solid fa-right-from-bracket"></i>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col overflow-y-auto">
            <!-- Header Nav -->
            <header class="bg-white border-b border-slate-200 h-16 flex items-center px-6 justify-between sticky top-0 z-20 shadow-sm">
                <div class="flex items-center space-x-4">
                    <button @click="sidebarOpen = !sidebarOpen" class="md:hidden text-slate-500 hover:text-slate-700">
                        <i class="fa-solid fa-bars text-lg"></i>
                    </button>
                    <h1 class="text-lg font-bold text-slate-800 tracking-tight">
                        @yield('header-title', 'PT Signal Panca Utama')
                    </h1>
                </div>

                <div class="flex items-center space-x-4">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium {{ $isSuperAdmin ? 'bg-purple-100 text-purple-800 border-purple-300' : 'bg-blue-50 text-blue-700 border-blue-200' }} border">
                        <span class="w-2 h-2 rounded-full {{ $isSuperAdmin ? 'bg-purple-600' : 'bg-blue-600' }} animate-pulse"></span>
                        Hak Akses: {{ ucwords(str_replace('_', ' ', $role)) }} {{ $isSuperAdmin ? '(Akses Penuh)' : '' }}
                    </span>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-xs font-semibold text-rose-600 hover:underline flex items-center gap-1">
                            <i class="fa-solid fa-right-from-bracket"></i> Logout
                        </button>
                    </form>
                </div>
            </header>

            <!-- Main Container -->
            <main class="flex-1 p-6 md:p-8 max-w-7xl w-full mx-auto">
                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between shadow-sm">
                        <div class="flex items-center space-x-3">
                            <i class="fa-solid fa-circle-check text-emerald-600 text-lg"></i>
                            <span class="text-sm font-medium">{{ session('success') }}</span>
                        </div>
                        <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700"><i class="fa-solid fa-xmark"></i></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 flex items-center justify-between shadow-sm">
                        <div class="flex items-center space-x-3">
                            <i class="fa-solid fa-circle-exclamation text-rose-600 text-lg"></i>
                            <span class="text-sm font-medium">{{ session('error') }}</span>
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
