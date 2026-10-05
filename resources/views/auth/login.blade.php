<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk | SignalNiagaJasaTender</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-icon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', '"Inter"', 'system-ui', '-apple-system', 'BlinkMacSystemFont', '"Segoe UI"', 'Roboto', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        :root { --blue: #1a6de3; --blue-deep: #1457b8; --blue-light: #5cb5f5; --blue-muted: #3b82c4; --ink: #142642; --muted: #91a1b8; --line: #dce5ef; }
        body { font-family: 'Plus Jakarta Sans', 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #edf6ff; }
        .auth-card { box-shadow: 0 22px 55px rgba(76, 127, 177, .18); }
        .blue-panel { background: linear-gradient(145deg, var(--blue-light) 0%, var(--blue-muted) 100%); }
        .login-panel { border-radius: 0 43% 43% 0 / 0 28% 28% 0; }
        .register-panel { border-radius: 43% 0 0 43% / 28% 0 0 28%; }
        .auth-pane { transition: transform .55s cubic-bezier(.22, 1, .36, 1), border-radius .55s cubic-bezier(.22, 1, .36, 1); }
        .auth-form-pane { transition: transform .55s cubic-bezier(.22, 1, .36, 1); }
        .field { background: #f1f6fb; border: 1px solid transparent; }
        .field:focus { border-color: var(--blue); box-shadow: 0 0 0 4px rgba(26, 109, 227, .16); outline: 0; }
        .primary-button { background: var(--blue); transition: transform .15s ease, background-color .15s ease, box-shadow .15s ease; }
        .primary-button:hover { background: var(--blue-deep); box-shadow: 0 10px 20px rgba(26, 109, 227, .24); transform: translateY(-1px); }
        .outline-button { border: 1px solid rgba(255,255,255,.9); }
        .social-button { border: 1px solid var(--line); box-shadow: 0 2px 5px rgba(44, 73, 107, .08); }
        .social-button:disabled { cursor: not-allowed; opacity: .72; }
        .auth-form-pane.is-compact .register-form > :not([hidden]) ~ :not([hidden]) { margin-top: .5rem; }
        .auth-form-pane.is-compact .register-form .field { height: 2.5rem; }
        .auth-form-pane.is-compact .register-form .primary-button { height: 2.5rem; }
        .auth-form-pane.is-compact .register-form .bg-blue-50 { padding-top: .5rem; padding-bottom: .5rem; }
        .auth-form-pane.is-compact .mb-4 { margin-bottom: .5rem !important; }
        .auth-form-pane.is-compact .mb-5 { margin-bottom: .5rem !important; }
        .auth-form-pane.is-compact .mt-7 { margin-top: .75rem !important; }
        [x-cloak] { display: none !important; }
    </style>
</head>
@php($hasAuthMessage = session('status') || $errors->any())
<body class="min-h-screen px-4 py-4 text-slate-900 sm:px-8 sm:py-6 lg:flex lg:items-center lg:justify-center" x-data="loginForm('{{ old('_form', 'login') }}')">
    <main class="auth-card relative mx-auto min-h-[640px] w-full max-w-[980px] rounded-[24px] bg-white">
        <section class="auth-pane absolute inset-y-0 left-1/2 z-10 hidden w-1/2 overflow-hidden text-white lg:flex lg:items-center lg:justify-center" :class="mode === 'login' ? 'blue-panel login-panel translate-x-0' : 'blue-panel register-panel -translate-x-full'">
            <div class="relative z-10 max-w-md px-10 text-center xl:px-14">
                <div class="mx-auto mb-6 flex h-12 w-12 items-center justify-center rounded-xl bg-white p-2 shadow-lg shadow-blue-700/20">
                    <img src="{{ asset('images/logo-icon.png') }}" alt="PT Signal Panca Utama" class="h-full w-full object-contain">
                </div>
                <h1 class="text-3xl font-extrabold tracking-[-0.04em] xl:text-4xl" x-text="mode === 'login' ? 'Halo, Teman!' : 'Selamat Datang!'">Halo, Teman!</h1>
                <p class="mx-auto mt-4 max-w-sm text-sm leading-6 text-white/90" x-text="mode === 'login' ? 'Senang sekali bisa melihat kamu di sini. Yuk, mulai langkah baru bersama kami!' : 'Daftar sekarang dan nikmati semua fitur yang kami sediakan.'">Senang sekali bisa melihat kamu di sini. Yuk, mulai langkah baru bersama kami!</p>
                <button type="button" @click="toggleMode" class="outline-button mt-7 min-w-[200px] rounded-xl px-6 py-2.5 text-sm font-bold text-white transition hover:bg-white/10 active:scale-[.98]" x-text="mode === 'login' ? 'Daftar Sekarang' : 'Masuk'">Daftar Sekarang</button>
            </div>
        </section>

        <section class="auth-form-pane flex min-h-[640px] items-center justify-center px-6 py-8 sm:px-12 lg:absolute lg:inset-y-0 lg:left-0 lg:z-20 lg:w-1/2 lg:px-12 lg:py-6 xl:px-14 {{ $hasAuthMessage ? 'is-compact lg:items-start' : '' }}" :class="mode === 'login' ? 'translate-x-0' : 'lg:translate-x-full'">
            <div class="w-full max-w-[400px] {{ $hasAuthMessage ? 'lg:pt-1' : '' }}">
                <div class="mb-4 flex items-center gap-3">
                    <a href="{{ route('home') }}" class="flex items-center gap-2.5" title="Kembali ke beranda">
                        <span class="flex h-9 w-9 items-center justify-center rounded-lg border border-blue-100 bg-white p-1 shadow-sm"><img src="{{ asset('images/logo-icon.png') }}" alt="PT Signal Panca Utama" class="h-full w-full object-contain"></span>
                        <span class="text-sm font-extrabold tracking-tight text-[var(--ink)]">SignalNiaga</span>
                    </a>
                </div>

                <div class="mb-4">
                    <h2 class="text-3xl font-extrabold tracking-[-0.05em] text-[var(--ink)]" x-text="mode === 'login' ? 'Masuk' : 'Buat Akun'">Masuk</h2>
                    <p class="mt-2 text-sm text-slate-400" x-text="mode === 'login' ? 'Masuk dengan email dan kata sandi' : 'Daftar dengan email dan kata sandi'">Masuk dengan email dan kata sandi</p>
                </div>

                @if(session('status'))
                    <div class="auth-notice mb-3 rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-2.5 text-xs leading-5 text-emerald-700">{{ session('status') }}</div>
                @endif
                @if($errors->any())
                    <div class="auth-notice mb-3 rounded-xl border border-rose-200 bg-rose-50 px-3 py-2.5 text-xs leading-5 text-rose-700"><p class="font-semibold">Belum berhasil.</p><p class="mt-0.5 text-[11px] leading-4">{{ $errors->first() }}</p></div>
                @endif

                <div class="mb-4 flex gap-3" x-show="mode === 'login' || mode === 'register'">
                    <a href="{{ route('oauth.redirect', ['provider' => 'google']) }}" class="social-button flex h-10 flex-1 items-center justify-center gap-2 rounded-xl bg-white text-xs font-bold text-slate-700 transition hover:bg-slate-50"><span class="text-base font-extrabold text-[#4285f4]">G</span><span x-text="mode === 'login' ? 'Masuk dengan Google' : 'Google'">Masuk dengan Google</span></a>
                    <a href="{{ route('oauth.redirect', ['provider' => 'linkedin']) }}" class="social-button flex h-10 flex-1 items-center justify-center gap-2 rounded-xl bg-white text-xs font-bold text-slate-700 transition hover:bg-slate-50"><span class="flex h-5 w-5 items-center justify-center rounded bg-[#0a66c2] text-xs font-extrabold text-white">in</span><span x-text="mode === 'login' ? 'Masuk dengan LinkedIn' : 'LinkedIn'">Masuk dengan LinkedIn</span></a>
                </div>
                <div class="mb-5 flex items-center gap-4 text-xs font-semibold text-slate-400"><span class="h-px flex-1 bg-slate-200"></span><span>atau</span><span class="h-px flex-1 bg-slate-200"></span></div>

                <form x-show="mode === 'login'" x-cloak action="{{ route('login') }}" method="POST" class="space-y-3" @submit="submitForm">
                    @csrf
                    <input type="hidden" name="_form" value="login">
                    <div class="relative"><label class="sr-only" for="email">Email</label><i class="fa-regular fa-envelope pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i><input id="email" type="email" name="email" x-model="loginEmail" autocomplete="email" required autofocus placeholder="Email" class="field h-12 w-full rounded-xl pl-12 pr-4 text-sm text-slate-700 placeholder:text-slate-400"></div>
                    <div class="relative"><label class="sr-only" for="password">Kata sandi</label><i class="fa-solid fa-lock pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i><input id="password" :type="showPassword ? 'text' : 'password'" name="password" x-model="loginPassword" autocomplete="current-password" required placeholder="Kata sandi" class="field h-12 w-full rounded-xl pl-12 pr-12 text-sm text-slate-700 placeholder:text-slate-400"><button type="button" @click="showPassword = !showPassword" class="absolute right-3 top-1/2 flex h-9 w-9 -translate-y-1/2 items-center justify-center rounded-lg text-slate-400 hover:bg-white hover:text-slate-700" :aria-label="showPassword ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'"><i class="fa-solid" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i></button></div>
                    <div class="flex items-center justify-between pt-1"><label class="flex items-center gap-2 text-xs text-slate-500"><input type="checkbox" name="remember" value="1" class="h-4 w-4 rounded border-slate-300 text-blue-500 focus:ring-blue-400"> Ingat saya</label><a href="{{ route('password.request') }}" class="text-xs font-semibold text-blue-500 hover:text-blue-700">Lupa kata sandi?</a></div>
                    <button type="submit" :disabled="loading" class="primary-button flex h-12 w-full items-center justify-center gap-2 rounded-xl text-sm font-bold text-white disabled:cursor-wait disabled:opacity-70"><i class="fa-solid" :class="loading ? 'fa-spinner fa-spin' : 'fa-arrow-right-to-bracket'"></i><span x-text="loading ? 'Memverifikasi...' : 'Masuk'">Masuk</span></button>
                </form>

                <form x-show="mode === 'register'" x-cloak action="{{ route('register') }}" method="POST" class="register-form space-y-3" @submit="registerLoading = true">
                    @csrf
                    <input type="hidden" name="_form" value="register">
                    <div class="relative"><label class="sr-only" for="register-name">Nama lengkap</label><i class="fa-regular fa-user pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i><input id="register-name" type="text" name="name" value="{{ old('name') }}" autocomplete="name" required placeholder="Nama lengkap" class="field h-12 w-full rounded-xl pl-12 pr-4 text-sm text-slate-700 placeholder:text-slate-400"></div>
                    <div class="relative"><label class="sr-only" for="register-email">Email</label><i class="fa-regular fa-envelope pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i><input id="register-email" type="email" name="email" value="{{ old('email') }}" autocomplete="email" required placeholder="Email" class="field h-12 w-full rounded-xl pl-12 pr-4 text-sm text-slate-700 placeholder:text-slate-400"></div>
                    <div class="relative"><label class="sr-only" for="register-password">Kata sandi</label><i class="fa-solid fa-lock pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i><input id="register-password" :type="registerPasswordVisible ? 'text' : 'password'" name="password" autocomplete="new-password" required minlength="8" placeholder="Kata sandi" class="field h-12 w-full rounded-xl pl-12 pr-12 text-sm text-slate-700 placeholder:text-slate-400"><button type="button" @click="registerPasswordVisible = !registerPasswordVisible" class="absolute right-3 top-1/2 flex h-9 w-9 -translate-y-1/2 items-center justify-center rounded-lg text-slate-400 hover:bg-white hover:text-slate-700" :aria-label="registerPasswordVisible ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'"><i class="fa-solid" :class="registerPasswordVisible ? 'fa-eye-slash' : 'fa-eye'"></i></button></div>
                    <div class="relative"><label class="sr-only" for="register-password-confirmation">Konfirmasi kata sandi</label><i class="fa-solid fa-lock pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i><input id="register-password-confirmation" type="password" name="password_confirmation" autocomplete="new-password" required minlength="8" placeholder="Konfirmasi kata sandi" class="field h-12 w-full rounded-xl pl-12 pr-4 text-sm text-slate-700 placeholder:text-slate-400"></div>
                    <p class="rounded-xl bg-blue-50 px-4 py-3 text-xs leading-5 text-blue-700"><i class="fa-solid fa-circle-info mr-1"></i> Akun akan berstatus <strong>Nonaktif</strong> sampai disetujui Owner.</p>
                    <button type="submit" :disabled="registerLoading" class="primary-button flex h-12 w-full items-center justify-center gap-2 rounded-xl text-sm font-bold text-white disabled:cursor-wait disabled:opacity-70"><i class="fa-solid" :class="registerLoading ? 'fa-spinner fa-spin' : 'fa-user-plus'"></i><span x-text="registerLoading ? 'Mendaftarkan...' : 'Daftar'">Daftar</span></button>
                </form>

                <div class="mt-7 text-center text-xs text-slate-400 lg:hidden"><button type="button" @click="toggleMode" class="font-bold text-blue-500" x-text="mode === 'login' ? 'Daftar sekarang' : 'Masuk di sini'">Daftar sekarang</button></div>
                <p class="mt-7 text-center text-[11px] text-slate-400"><a href="{{ route('home') }}" class="font-semibold text-blue-500 hover:text-blue-700">Kembali ke halaman utama</a></p>
            </div>
        </section>
    </main>

    <script>
        function loginForm(initialMode) {
            return {
                mode: initialMode === 'register' ? 'register' : 'login',
                showPassword: false,
                registerPasswordVisible: false,
                loading: false,
                registerLoading: false,
                loginEmail: '{{ old('email') }}',
                loginPassword: '',
                toggleMode() {
                    this.mode = this.mode === 'login' ? 'register' : 'login';
                    this.showPassword = false;
                    this.registerPasswordVisible = false;
                },
                submitForm() {
                    this.loading = true;
                }
            };
        }
    </script>
</body>
</html>
