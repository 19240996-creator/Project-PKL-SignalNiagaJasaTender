<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SignalNiagaJasaTender') - PT Signal Panca Utama</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-icon.png') }}">
    <!-- Google Fonts: Plus Jakarta Sans & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400;1,600&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', '"Inter"', 'system-ui', '-apple-system', 'BlinkMacSystemFont', '"Segoe UI"', 'Roboto', 'sans-serif'],
                        number: ['"Plus Jakarta Sans"', '"Inter"', 'system-ui', '-apple-system', 'BlinkMacSystemFont', '"Segoe UI"', 'Roboto', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#EFF6FF',
                            100: '#DBEAFE',
                            500: '#3B82F6',
                            600: '#2563EB',
                            700: '#1D4ED8',
                        },
                        navy: {
                            800: '#0C1733',
                            900: '#091126',
                            950: '#060B1A',
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
            font-family: 'Plus Jakarta Sans', 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #F8FAFC;
            color: #0F172A;
            letter-spacing: -0.015em;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        .font-number, .font-numeric {
            font-family: 'Plus Jakarta Sans', 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
            font-feature-settings: "tnum" 1, "cv05" 1;
            font-variant-numeric: tabular-nums;
        }
        .sidebar-item-active {
            background-color: #2563EB !important;
            color: #FFFFFF !important;
            font-weight: 600 !important;
            border-radius: 0.75rem !important;
            box-shadow: 0 4px 12px -2px rgba(37, 99, 235, 0.45) !important;
        }
        .sidebar-item-active i {
            color: #FFFFFF !important;
        }
        .sidebar-subitem-active {
            background-color: #2563EB !important;
            color: #FFFFFF !important;
            font-weight: 600 !important;
            border-radius: 0.625rem !important;
            box-shadow: 0 2px 8px -1px rgba(37, 99, 235, 0.35) !important;
        }
        .sidebar-subitem-active i {
            color: #FFFFFF !important;
        }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-[#F8FAFC] antialiased min-h-screen flex flex-col text-slate-800" 
      x-data="{ 
          sidebarOpen: false,
          logoutModal: false,
          deleteModal: {
              open: false,
              title: 'Konfirmasi Hapus Data',
              message: 'Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.',
              form: null
          },
          openDeleteModal(form, message, title) {
              this.deleteModal.form = form;
              this.deleteModal.message = message || 'Apakah Anda yakin ingin menghapus data ini? Tindakan ini bersifat permanen dan data yang dihapus tidak dapat dipulihkan.';
              this.deleteModal.title = title || 'Konfirmasi Hapus Data';
              this.deleteModal.open = true;
          },
          executeDelete() {
              if (this.deleteModal.form) {
                  this.deleteModal.form.dataset.confirmed = 'true';
                  this.deleteModal.form.submit();
              }
              this.deleteModal.open = false;
          }
      }"
      @open-delete.window="openDeleteModal($event.detail.form, $event.detail.message, $event.detail.title)">

    @php
        $user = Auth::user();
        $role = $user->role->name ?? '';
        $isOwner = ($role === 'owner');
        $isManager = ($role === 'manager');
        $isAdmin = ($role === 'admin');

        // Notifikasi pengajuan menunggu verifikasi
        $pendingTenders = $isManager ? \App\Models\Tender::where(function ($q) {
            $q->where('approval_status', 'pending')
              ->orWhere('submission_status', 'diajukan')
              ->orWhere('rab_status', 'diajukan');
        })->count() : 0;
        $pendingServices = $isManager ? \App\Models\ServiceJob::where('approval_status', 'pending')->count() : 0;
        $pendingSales = $isManager ? \App\Models\Sale::where('approval_status', 'pending')->count() : 0;
        $totalPending = $pendingTenders + $pendingServices + $pendingSales;
    @endphp

    <div class="flex h-screen overflow-hidden bg-[#F8FAFC]">
        <!-- Sidebar: Dark Blue Solid Elegance (Sesuai Referensi Gambar) -->
        <aside class="fixed inset-y-0 left-0 z-30 w-64 bg-[#091126] text-slate-300 border-r border-[#152345] transform transition-transform duration-200 ease-in-out md:translate-x-0 md:static flex flex-col justify-between shrink-0"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
            
            <div class="flex-1 flex flex-col overflow-y-auto">
                <!-- Brand Header -->
                <div class="h-20 flex items-center px-5 text-white justify-between shrink-0">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-600 flex items-center justify-center shadow-md shadow-blue-600/30 shrink-0">
                            <i class="fa-solid fa-layer-group text-lg text-white"></i>
                        </div>
                        <div class="truncate">
                            <span class="font-bold text-base tracking-tight text-white block leading-tight truncate">PT Signal</span>
                            <span class="text-[11px] text-blue-400 font-medium tracking-wide block truncate">Enterprise System</span>
                        </div>
                    </div>
                    <button @click="sidebarOpen = false" class="md:hidden text-slate-400 hover:text-white p-1">
                        <i class="fa-solid fa-xmark text-base"></i>
                    </button>
                </div>

                <!-- Navigation Links -->
                <nav class="px-3.5 space-y-1.5 flex-1 text-sm mt-1">
                    <!-- 1. Dashboard -->
                    <a href="{{ route('dashboard') }}" 
                       class="group flex items-center px-3.5 py-2.5 text-xs font-medium rounded-xl text-slate-300 hover:bg-white/10 hover:text-white transition-all {{ request()->routeIs('dashboard') ? 'sidebar-item-active' : '' }}">
                        <i class="fa-solid fa-house w-5 text-center text-slate-400 group-hover:text-white"></i>
                        <span class="ml-2.5">Dashboard</span>
                    </a>

                    @if($isOwner)
                        <div class="pt-5 pb-1.5 px-3 text-[10px] font-bold text-blue-400/80 uppercase tracking-wider">
                            Laporan Eksekutif
                        </div>

                        <!-- Laporan Tender -->
                        <a href="{{ route('laporan.index', ['domain' => 'tender']) }}" 
                           class="group flex items-center px-3.5 py-2.5 text-xs font-medium rounded-xl text-slate-300 hover:bg-white/10 hover:text-white transition-all {{ request()->routeIs('laporan.*') && request('domain') === 'tender' ? 'sidebar-item-active' : '' }}">
                            <i class="fa-solid fa-file-contract w-5 text-center text-slate-400 group-hover:text-white"></i>
                            <span class="ml-2.5 truncate">Laporan Tender</span>
                        </a>

                        <!-- Laporan Jasa -->
                        <a href="{{ route('laporan.index', ['domain' => 'jasa']) }}" 
                           class="group flex items-center px-3.5 py-2.5 text-xs font-medium rounded-xl text-slate-300 hover:bg-white/10 hover:text-white transition-all {{ request()->routeIs('laporan.*') && request('domain') === 'jasa' ? 'sidebar-item-active' : '' }}">
                            <i class="fa-solid fa-wrench w-5 text-center text-slate-400 group-hover:text-white"></i>
                            <span class="ml-2.5 truncate">Laporan Jasa</span>
                        </a>

                        <!-- Laporan Dagang -->
                        <a href="{{ route('laporan.index', ['domain' => 'barang']) }}" 
                           class="group flex items-center px-3.5 py-2.5 text-xs font-medium rounded-xl text-slate-300 hover:bg-white/10 hover:text-white transition-all {{ request()->routeIs('laporan.*') && request('domain') === 'barang' ? 'sidebar-item-active' : '' }}">
                            <i class="fa-solid fa-boxes-stacked w-5 text-center text-slate-400 group-hover:text-white"></i>
                            <span class="ml-2.5 truncate">Laporan Dagang</span>
                        </a>

                        <!-- Ringkasan 3 Bisnis -->
                        <a href="{{ route('laporan.index', ['domain' => 'semua']) }}" 
                           class="group flex items-center px-3.5 py-2.5 text-xs font-medium rounded-xl text-slate-300 hover:bg-white/10 hover:text-white transition-all {{ request()->routeIs('laporan.*') && request('domain', 'semua') === 'semua' ? 'sidebar-item-active' : '' }}">
                            <i class="fa-solid fa-chart-pie w-5 text-center text-slate-400 group-hover:text-white"></i>
                            <span class="ml-2.5 truncate">Ringkasan 3 Bisnis</span>
                        </a>

                        <div class="pt-5 pb-1.5 px-3 text-[10px] font-bold text-blue-400/80 uppercase tracking-wider">
                            Sistem & Pengguna
                        </div>

                        <a href="{{ route('users.index') }}" 
                           class="group flex items-center px-3.5 py-2.5 text-xs font-medium rounded-xl text-slate-300 hover:bg-white/10 hover:text-white transition-all {{ request()->routeIs('users.*') ? 'sidebar-item-active' : '' }}">
                            <i class="fa-solid fa-users w-5 text-center text-slate-400 group-hover:text-white"></i>
                            <span class="ml-2.5">Pengguna & Akses</span>
                        </a>

                        <a href="{{ route('activity-logs.index') }}" 
                           class="group flex items-center px-3.5 py-2.5 text-xs font-medium rounded-xl text-slate-300 hover:bg-white/10 hover:text-white transition-all {{ request()->routeIs('activity-logs.*') ? 'sidebar-item-active' : '' }}">
                            <i class="fa-solid fa-clock-rotate-left w-5 text-center text-slate-400 group-hover:text-white"></i>
                            <span class="ml-2.5">Log Aktivitas</span>
                        </a>

                    @else
                        <!-- Sub-header Operasional Bisnis -->
                        <div class="pt-4 pb-1.5 px-3 text-[10px] font-bold text-blue-400/80 uppercase tracking-wider">
                            Operasional Bisnis
                        </div>

                        <!-- 2. NESTED MENU: TENDER (Persis konsep Projects pada gambar referensi) -->
                        <div x-data="{ tenderOpen: {{ request()->routeIs('tender.*') ? 'true' : 'false' }} }" class="space-y-1">
                            <button type="button"
                                    @click="tenderOpen = !tenderOpen"
                                    class="w-full group flex items-center justify-between px-3.5 py-2.5 text-xs font-medium rounded-xl transition-all {{ request()->routeIs('tender.*') ? 'text-white bg-white/10' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                                <div class="flex items-center space-x-2.5 truncate">
                                    <i class="fa-regular fa-folder w-5 text-center {{ request()->routeIs('tender.*') ? 'text-blue-400' : 'text-slate-400 group-hover:text-white' }}"></i>
                                    <span class="ml-1 truncate font-medium">Tender</span>
                                </div>
                                <div class="flex items-center space-x-2 shrink-0">
                                    @if($pendingTenders > 0)
                                        <span class="px-1.5 py-0.5 text-[10px] font-bold bg-blue-600 text-white rounded-full">{{ $pendingTenders }}</span>
                                    @endif
                                    <i class="fa-solid text-[10px] text-slate-400 transition-transform duration-200"
                                       :class="tenderOpen ? 'fa-chevron-up text-blue-400' : 'fa-chevron-down'"></i>
                                </div>
                            </button>

                            <!-- Nested Submenu Items (Indented & Clean Highlight Pill Sesuai Gambar) -->
                            <div x-show="tenderOpen"
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0 -translate-y-1"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 class="pl-4 pr-1 py-1 space-y-1"
                                 x-cloak>
                                
                                <!-- Submenu 1: Administrasi Tender -->
                                @php
                                    $isAdmActive = (request()->routeIs('tender.index') || request()->routeIs('tender.administrasi')) && !request()->routeIs('tender.rab.*') && !request()->routeIs('tender.proyek.*');
                                @endphp
                                <a href="{{ route('tender.index') }}"
                                   class="group flex items-center justify-between px-3 py-2 text-xs font-medium rounded-lg transition-all {{ $isAdmActive ? 'sidebar-subitem-active' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
                                    <div class="flex items-center space-x-2.5 truncate">
                                        <i class="fa-regular fa-folder-open w-4 text-center {{ $isAdmActive ? 'text-white' : 'text-slate-400 group-hover:text-white' }}"></i>
                                        <span class="truncate">Administrasi Tender</span>
                                    </div>
                                    @if($pendingTenders > 0)
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span>
                                    @endif
                                </a>

                                <!-- Submenu 2: Estimasi / RAB -->
                                @php
                                    $isRabActive = request()->routeIs('tender.rab.*');
                                @endphp
                                <a href="{{ route('tender.rab.index') }}"
                                   class="group flex items-center justify-between px-3 py-2 text-xs font-medium rounded-lg transition-all {{ $isRabActive ? 'sidebar-subitem-active' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
                                    <div class="flex items-center space-x-2.5 truncate">
                                        <i class="fa-solid fa-calculator w-4 text-center {{ $isRabActive ? 'text-white' : 'text-slate-400 group-hover:text-white' }}"></i>
                                        <span class="truncate">Estimasi / RAB</span>
                                    </div>
                                </a>

                                <!-- Submenu 3: Lapangan / Proyek -->
                                @php
                                    $isProyekActive = request()->routeIs('tender.proyek.*');
                                @endphp
                                <a href="{{ route('tender.proyek.index') }}"
                                   class="group flex items-center justify-between px-3 py-2 text-xs font-medium rounded-lg transition-all {{ $isProyekActive ? 'sidebar-subitem-active' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
                                    <div class="flex items-center space-x-2.5 truncate">
                                        <i class="fa-solid fa-helmet-safety w-4 text-center {{ $isProyekActive ? 'text-white' : 'text-slate-400 group-hover:text-white' }}"></i>
                                        <span class="truncate">Lapangan / Proyek</span>
                                    </div>
                                </a>
                            </div>
                        </div>

                        <!-- 3. Domain Jasa -->
                        @php $isJasaActive = request()->routeIs('jasa.*'); @endphp
                        <div x-data="{ jasaOpen: {{ $isJasaActive ? 'true' : 'false' }} }" class="space-y-1">
                            <button type="button" @click="jasaOpen = !jasaOpen"
                                    :aria-expanded="jasaOpen.toString()"
                                    class="w-full group flex items-center justify-between px-3.5 py-2.5 text-xs font-medium rounded-xl transition-all {{ $isJasaActive ? 'text-white bg-white/10' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                                <div class="flex items-center space-x-2.5 truncate">
                                    <i class="fa-solid fa-screwdriver-wrench w-5 text-center {{ $isJasaActive ? 'text-blue-400' : 'text-slate-400 group-hover:text-white' }}"></i>
                                    <span class="ml-1 truncate">Jasa</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    @if($pendingServices > 0)<span class="px-1.5 py-0.5 text-[10px] font-bold bg-blue-600 text-white rounded-full">{{ $pendingServices }}</span>@endif
                                    <i class="fa-solid text-[10px] text-slate-400" :class="jasaOpen ? 'fa-chevron-up text-blue-400' : 'fa-chevron-down'"></i>
                                </div>
                            </button>
                            <div x-show="jasaOpen" x-transition class="pl-4 pr-1 py-1 space-y-1" x-cloak>
                                <a href="{{ route('jasa.teknisi.index') }}" class="group flex items-center px-3 py-2 text-xs font-medium rounded-lg transition-all {{ request()->routeIs('jasa.teknisi.*') ? 'sidebar-subitem-active' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}"><i class="fa-solid fa-user-gear w-4 text-center mr-2.5"></i><span>Teknisi</span></a>
                                <a href="{{ route('jasa.instalasi.index') }}" class="group flex items-center px-3 py-2 text-xs font-medium rounded-lg transition-all {{ request()->routeIs('jasa.instalasi.*') ? 'sidebar-subitem-active' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}"><i class="fa-solid fa-satellite-dish w-4 text-center mr-2.5"></i><span>Instalasi</span></a>
                                <a href="{{ route('jasa.maintenance.index') }}" class="group flex items-center px-3 py-2 text-xs font-medium rounded-lg transition-all {{ request()->routeIs('jasa.maintenance.*') ? 'sidebar-subitem-active' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}"><i class="fa-solid fa-screwdriver-wrench w-4 text-center mr-2.5"></i><span>Perbaikan / Maintenance</span></a>
                            </div>
                        </div>

                        <!-- 4. Domain Dagang -->
                        @php
                            $isDagangActive = request()->routeIs('purchasing.*')
                                || request()->routeIs('procurements.*')
                                || request()->routeIs('gudang.*')
                                || request()->routeIs('products.*')
                                || request()->routeIs('distribusi.*')
                                || request()->routeIs('sales.*')
                                || request()->routeIs('perdagangan.*')
                                || request()->routeIs('suppliers.*');
                        @endphp
                        <div x-data="{ dagangOpen: {{ $isDagangActive ? 'true' : 'false' }} }" class="space-y-1">
                            <button type="button"
                                    @click="dagangOpen = !dagangOpen"
                                    class="w-full group flex items-center justify-between px-3.5 py-2.5 text-xs font-medium rounded-xl transition-all {{ $isDagangActive ? 'text-white bg-white/10' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}"
                                    :aria-expanded="dagangOpen.toString()">
                                <div class="flex items-center space-x-2.5 truncate">
                                    <i class="fa-solid fa-boxes-stacked w-5 text-center {{ $isDagangActive ? 'text-blue-400' : 'text-slate-400 group-hover:text-white' }}"></i>
                                    <span class="ml-1 truncate">Dagang</span>
                                </div>
                                <i class="fa-solid text-[10px] text-slate-400 transition-transform duration-200"
                                   :class="dagangOpen ? 'fa-chevron-up text-blue-400' : 'fa-chevron-down'"></i>
                            </button>

                            <div x-show="dagangOpen"
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0 -translate-y-1"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 class="pl-4 pr-1 py-1 space-y-1"
                                 x-cloak>
                                <a href="{{ route('purchasing.index') }}"
                                   class="group flex items-center justify-between px-3 py-2 text-xs font-medium rounded-lg transition-all {{ request()->routeIs('purchasing.*') || request()->routeIs('procurements.*') || request()->routeIs('suppliers.*') ? 'sidebar-subitem-active' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
                                    <span class="flex items-center gap-2.5 truncate">
                                        <i class="fa-solid fa-cart-shopping w-4 text-center"></i>
                                        <span class="truncate">Purchasing</span>
                                    </span>
                                </a>
                                <a href="{{ route('gudang.index') }}"
                                   class="group flex items-center px-3 py-2 text-xs font-medium rounded-lg transition-all {{ request()->routeIs('gudang.*') || request()->routeIs('products.*') ? 'sidebar-subitem-active' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
                                    <i class="fa-solid fa-warehouse w-4 text-center mr-2.5"></i>
                                    <span class="truncate">Gudang</span>
                                </a>
                                <a href="{{ route('distribusi.index') }}"
                                   class="group flex items-center px-3 py-2 text-xs font-medium rounded-lg transition-all {{ request()->routeIs('distribusi.*') || request()->routeIs('sales.*') || request()->routeIs('perdagangan.*') ? 'sidebar-subitem-active' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
                                    <i class="fa-solid fa-truck-fast w-4 text-center mr-2.5"></i>
                                    <span class="truncate">Distribusi</span>
                                </a>
                            </div>
                        </div>

                        <!-- 5. Laporan Bisnis -->
                        <a href="{{ route('laporan.index') }}" 
                           class="group flex items-center px-3.5 py-2.5 text-xs font-medium rounded-xl text-slate-300 hover:bg-white/10 hover:text-white transition-all {{ request()->routeIs('laporan.*') ? 'sidebar-item-active' : '' }}">
                            <i class="fa-solid fa-chart-line w-5 text-center text-slate-400 group-hover:text-white"></i>
                            <span class="ml-2.5">Laporan Bisnis</span>
                        </a>

                    @endif

                    <!-- Logout Button (Sesuai Referensi Gambar) -->
                    <form id="logoutForm" action="{{ route('logout') }}" method="POST" class="pt-3">
                        @csrf
                        <button type="button" 
                                @click="logoutModal = true"
                                class="w-full group flex items-center px-3.5 py-2.5 text-xs font-medium rounded-xl text-slate-400 hover:bg-rose-500/10 hover:text-rose-300 transition-all text-left cursor-pointer">
                            <i class="fa-solid fa-arrow-right-from-bracket w-5 text-center text-slate-400 group-hover:text-rose-400"></i>
                            <span class="ml-2.5">Logout</span>
                        </button>
                    </form>
                </nav>
            </div>

            <!-- Bottom User Profile Card (Sesuai Persis Kartu Alex Carter di Kiri Bawah) -->
            <div class="p-3.5 m-3 rounded-2xl bg-[#111C3D] border border-[#1E2D58] flex items-center justify-between shadow-xs">
                <div class="flex items-center space-x-2.5 overflow-hidden">
                    <div class="w-9 h-9 rounded-full bg-blue-600/30 border border-blue-400/40 text-blue-300 font-bold flex items-center justify-center text-xs shrink-0 uppercase">
                        {{ strtoupper(substr($user->name ?? 'SPU', 0, 2)) }}
                    </div>
                    <div class="truncate">
                        <div class="text-xs font-bold text-white truncate leading-tight">{{ $user->name ?? 'Pengguna' }}</div>
                        <div class="text-[10px] text-slate-400 font-medium truncate mt-0.5">{{ ucfirst($role) }}</div>
                    </div>
                </div>

                <button type="button" 
                        @click="logoutModal = true"
                        class="shrink-0 text-slate-400 hover:text-rose-400 cursor-pointer p-1.5 rounded-lg hover:bg-white/5 transition"
                        title="Keluar dari sistem">
                    <i class="fa-solid fa-arrow-right-from-bracket text-xs"></i>
                </button>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col overflow-y-auto">
            <!-- Header Nav Bar (Clean White Sesuai Referensi Gambar) -->
            <header class="bg-white border-b border-slate-200/80 h-16 flex items-center px-6 justify-between sticky top-0 z-20 shadow-xs">
                <div class="flex items-center space-x-3">
                    <button @click="sidebarOpen = !sidebarOpen" class="md:hidden text-slate-600 hover:text-blue-600 mr-1">
                        <i class="fa-solid fa-bars text-lg"></i>
                    </button>
                    <div class="flex items-center space-x-2">
                        <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                        <h1 class="text-sm font-bold text-slate-900 tracking-tight">
                            @yield('header-title', 'PT Signal Panca Utama')
                        </h1>
                    </div>
                </div>

                <!-- Right Header Actions (Bell & User Profile) -->
                <div class="flex items-center space-x-4">
                    <!-- Notification Bell with Ping Dot -->
                    <div class="relative">
                        <button type="button" class="w-9 h-9 rounded-full bg-slate-50 hover:bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-600 transition">
                            <i class="fa-regular fa-bell text-sm"></i>
                        </button>
                        @if($isManager && $totalPending > 0)
                            <span class="absolute top-1 right-1 w-2.5 h-2.5 bg-rose-500 rounded-full border-2 border-white ring-1 ring-rose-300"></span>
                        @endif
                    </div>

                    <!-- User Profile Dropdown Pill (Sesuai Gambar Kanan Atas) -->
                    <div class="relative" x-data="{ profileMenu: false }">
                        <button type="button" 
                                @click="profileMenu = !profileMenu" 
                                class="flex items-center space-x-2.5 pl-2 border-l border-slate-200 text-left outline-none cursor-pointer">
                            <div class="w-8 h-8 rounded-full bg-blue-600 text-white font-bold flex items-center justify-center text-xs uppercase shadow-xs">
                                {{ strtoupper(substr($user->name ?? 'U', 0, 2)) }}
                            </div>
                            <div class="hidden sm:block text-left">
                                <div class="text-xs font-bold text-slate-900 leading-tight">{{ $user->name ?? 'Pengguna' }}</div>
                                <div class="text-[10px] text-slate-500 leading-tight">{{ ucfirst($role) }}</div>
                            </div>
                            <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 ml-1 hidden sm:block"></i>
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-show="profileMenu" 
                             @click.away="profileMenu = false" 
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-slate-200 py-1.5 z-30 text-xs" 
                             x-cloak>
                            <div class="px-3.5 py-2 border-b border-slate-100">
                                <div class="font-bold text-slate-900 truncate">{{ $user->name ?? 'Pengguna' }}</div>
                                <div class="text-[10px] text-slate-500 truncate mt-0.5">{{ $user->email ?? '' }}</div>
                            </div>
                            <button type="button" 
                                    @click="profileMenu = false; logoutModal = true"
                                    class="w-full text-left px-3.5 py-2 text-rose-600 hover:bg-rose-50 flex items-center gap-2 font-semibold transition cursor-pointer">
                                <i class="fa-solid fa-arrow-right-from-bracket text-xs"></i>
                                <span>Keluar (Logout)</span>
                            </button>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Container -->
            <main class="flex-1 p-5 md:p-8 max-w-7xl w-full mx-auto">
                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="mb-5 p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between text-xs shadow-xs">
                        <div class="flex items-center space-x-2.5">
                            <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
                            <span class="font-medium">{{ session('success') }}</span>
                        </div>
                        <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700"><i class="fa-solid fa-xmark"></i></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-5 p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 flex items-center justify-between text-xs shadow-xs">
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

    <!-- MODAL KONFIRMASI LOGOUT -->
    <div x-show="logoutModal" 
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4" 
         style="display: none;"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @keydown.escape.window="logoutModal = false"
         x-cloak>
        <div class="bg-white rounded-2xl shadow-xl max-w-sm w-full border border-slate-200 overflow-hidden transform transition-all p-5" 
             @click.away="logoutModal = false">
            <div class="flex items-center gap-3.5 mb-3.5">
                <div class="w-11 h-11 rounded-xl bg-rose-50 border border-rose-200 text-rose-600 flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-arrow-right-from-bracket text-lg"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900 leading-tight">Konfirmasi Keluar Sistem</h3>
                    <span class="text-[11px] text-slate-500 block mt-0.5">Sesi login aktif akan diakhiri</span>
                </div>
            </div>
            <p class="text-xs text-slate-600 leading-relaxed">
                Apakah Anda yakin ingin keluar dari sistem? Anda harus memasukkan kredensial akun kembali untuk dapat mengakses aplikasi PT Signal Panca Utama.
            </p>
            <div class="mt-5 pt-3.5 border-t border-slate-100 flex items-center justify-end gap-2.5">
                <button type="button" 
                        @click="logoutModal = false" 
                        class="px-3.5 py-2 border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-semibold rounded-xl transition cursor-pointer">
                    Batal
                </button>
                <button type="button" 
                        onclick="document.getElementById('logoutForm').submit()" 
                        class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold rounded-xl shadow-sm transition flex items-center gap-1.5 cursor-pointer">
                    <i class="fa-solid fa-arrow-right-from-bracket text-[11px]"></i>
                    <span>Ya, Keluar</span>
                </button>
            </div>
        </div>
    </div>

    <!-- MODAL KONFIRMASI HAPUS DATA -->
    <div x-show="deleteModal.open" 
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4" 
         style="display: none;"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @keydown.escape.window="deleteModal.open = false"
         x-cloak>
        <div class="bg-white rounded-2xl shadow-xl max-w-sm w-full border border-slate-200 overflow-hidden transform transition-all p-5" 
             @click.away="deleteModal.open = false">
            <div class="flex items-center gap-3.5 mb-3.5">
                <div class="w-11 h-11 rounded-xl bg-rose-50 border border-rose-200 text-rose-600 flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-triangle-exclamation text-lg"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900 leading-tight" x-text="deleteModal.title"></h3>
                    <span class="text-[11px] text-rose-600 font-semibold block mt-0.5">Tindakan tidak dapat dibatalkan</span>
                </div>
            </div>
            <p class="text-xs text-slate-600 leading-relaxed" x-text="deleteModal.message"></p>
            <div class="mt-5 pt-3.5 border-t border-slate-100 flex items-center justify-end gap-2.5">
                <button type="button" 
                        @click="deleteModal.open = false" 
                        class="px-3.5 py-2 border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-semibold rounded-xl transition cursor-pointer">
                    Batal
                </button>
                <button type="button" 
                        @click="executeDelete()" 
                        class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold rounded-xl shadow-sm transition flex items-center gap-1.5 cursor-pointer">
                    <i class="fa-solid fa-trash-can text-[11px]"></i>
                    <span>Ya, Hapus Data</span>
                </button>
            </div>
        </div>
    </div>

    <!-- SCRIPT INTERSEPSI FORM HAPUS GLOBAL -->
    <script>
        document.addEventListener('submit', function (e) {
            const form = e.target;
            if (!form || form.tagName !== 'FORM') return;
            
            // Jika sudah terkonfirmasi oleh modal atau form meminta bypass
            if (form.dataset.confirmed === 'true' || form.dataset.bypassConfirm === 'true') {
                return;
            }

            // Periksa apakah form ini adalah aksi HTTP DELETE
            const methodInput = form.querySelector('input[name="_method"]');
            const isDeleteMethod = methodInput && methodInput.value.toUpperCase() === 'DELETE';
            const actionUrl = form.action || '';
            const isSpecificDeleteAction = actionUrl.endsWith('/destroy') || actionUrl.endsWith('/delete') || actionUrl.includes('/destroy/');
            const hasDeleteClass = form.classList.contains('form-delete') || form.classList.contains('delete-form');

            // Jangan intersepsi form pengajuan izin hapus atau persetujuan izin hapus
            if (actionUrl.includes('-deletion')) {
                return;
            }

            if (isDeleteMethod || isSpecificDeleteAction || hasDeleteClass) {
                e.preventDefault();
                e.stopImmediatePropagation();

                // Ambil pesan konfirmasi jika disediakan
                let customMessage = form.getAttribute('data-confirm') || form.getAttribute('data-message');
                if (!customMessage && form.getAttribute('onsubmit')) {
                    const match = form.getAttribute('onsubmit').match(/confirm\(['"](.+?)['"]\)/);
                    if (match && match[1]) {
                        customMessage = match[1];
                    }
                }

                if (!customMessage) {
                    customMessage = 'Apakah Anda yakin ingin menghapus data ini? Tindakan ini bersifat permanen dan data yang telah dihapus tidak dapat dipulihkan kembali.';
                }

                window.dispatchEvent(new CustomEvent('open-delete', {
                    detail: {
                        form: form,
                        message: customMessage,
                        title: 'Konfirmasi Hapus Data'
                    }
                }));
            }
        }, true);
    </script>

    @yield('scripts')
</body>
</html>
