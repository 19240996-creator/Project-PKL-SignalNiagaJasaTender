<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Hak Akses — SignalNiagaJasaTender</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>body { font-family: 'Outfit', sans-serif; }</style>
</head>
<body class="bg-slate-900 min-h-screen flex items-center justify-center p-4" x-data="{ email: '', password: '', showPassword: false }">
    <div class="max-w-5xl w-full grid md:grid-cols-12 bg-white rounded-2xl shadow-2xl overflow-hidden">
        
        <!-- Left Side: Branding -->
        <div class="md:col-span-5 bg-gradient-to-br from-blue-900 to-indigo-950 p-8 text-white flex flex-col justify-between">
            <div>
                <a href="{{ route('home') }}" class="flex items-center space-x-3 mb-8 group" title="Kembali ke Beranda">
                    <div class="w-10 h-10 rounded-xl bg-blue-600 group-hover:bg-blue-500 flex items-center justify-center font-bold text-white text-xl shadow-lg transition-transform group-hover:scale-105">
                        S
                    </div>
                    <div>
                        <span class="font-bold text-xl tracking-tight block group-hover:text-blue-200 transition">SignalNiaga</span>
                        <span class="text-xs text-blue-400 font-medium tracking-wide uppercase">PT Signal Panca Utama</span>
                    </div>
                </a>

                <h2 class="text-2xl font-bold leading-tight mb-4">Sistem Autentikasi & Hak Akses Per Role</h2>
                <p class="text-slate-300 text-xs leading-relaxed mb-6">
                    Setiap peran memiliki kredensial email & password sendiri. <b class="text-blue-400">Super Admin</b> memiliki wewenang akses penuh ke seluruh modul sistem.
                </p>

                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-semibold border border-white/20 transition backdrop-blur-sm">
                    <i class="fa-solid fa-house text-blue-400"></i> Kembali ke Beranda Website
                </a>
            </div>

            <div class="pt-6 border-t border-slate-800 text-[11px] text-slate-400">
                &copy; {{ date('Y') }} PT Signal Panca Utama. Multi-Role RBAC System.
            </div>
        </div>

        <!-- Right Side: Login Form & Account List -->
        <div class="md:col-span-7 p-6 md:p-8 flex flex-col justify-between max-h-[90vh] overflow-y-auto">
            <div>
                <div class="flex items-center justify-between mb-4 pb-2 border-b border-slate-100">
                    <a href="{{ route('home') }}" class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-slate-100 hover:bg-blue-50 text-slate-600 hover:text-blue-600 text-xs font-bold transition">
                        <i class="fa-solid fa-arrow-left"></i> Kembali ke Halaman Utama
                    </a>
                    <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Portal Autentikasi</span>
                </div>

                <h3 class="text-2xl font-bold text-slate-800 mb-1">Masuk dengan Email & Password</h3>
                <p class="text-slate-500 text-xs mb-6">Masukkan email dan password akun sesuai hak akses Anda.</p>

                @if($errors->any())
                    <div class="mb-4 p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-medium">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form action="{{ route('login') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Alamat Email</label>
                        <input type="email" name="email" required x-model="email" placeholder="superadmin@signalpanca.co.id" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Password</label>
                        <div class="relative">
                            <input :type="showPassword ? 'text' : 'password'" name="password" required x-model="password" placeholder="••••••••" class="w-full px-4 py-2.5 pr-10 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                            <button type="button" @click="showPassword = !showPassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 focus:outline-none text-sm p-1" title="Tampilkan/Sembunyikan Password">
                                <i class="fa-solid" :class="showPassword ? 'fa-eye-slash text-blue-600' : 'fa-eye'"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl shadow-md transition-colors text-sm flex items-center justify-center gap-2">
                        <i class="fa-solid fa-right-to-bracket"></i> Login Masuk Sistem
                    </button>
                </form>

                <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-slate-200"></div></div>
                    <div class="relative flex justify-center text-[11px] uppercase"><span class="bg-white px-2 text-slate-400 font-bold">Daftar Akun Kredensial Per Role</span></div>
                </div>

                <!-- Account Credentials Cards -->
                <div class="space-y-2 text-xs">
                    @foreach($accounts as $acc)
                        <div class="p-3 rounded-xl border border-slate-200 bg-slate-50 flex items-center justify-between hover:border-blue-300 transition">
                            <div class="space-y-0.5">
                                <div class="flex items-center gap-2">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $acc['badge'] }}">
                                        {{ $acc['label'] }}
                                    </span>
                                    @if($acc['role'] === 'super_admin')
                                        <span class="text-[10px] font-bold text-purple-700">(Akses Penuh)</span>
                                    @endif
                                </div>
                                <div class="font-mono text-slate-700 text-xs font-bold">{{ $acc['email'] }}</div>
                                <div class="text-[11px] text-slate-400">Password: <span class="font-mono font-bold text-slate-600">password</span></div>
                            </div>
                            <button type="button" @click="email = '{{ $acc['email'] }}'; password = 'password'" class="px-3 py-1.5 bg-white border border-slate-300 hover:bg-blue-50 hover:text-blue-600 text-slate-700 font-semibold rounded-lg shadow-sm text-xs transition">
                                Pilih Akun
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</body>
</html>
