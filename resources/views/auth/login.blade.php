<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk | SignalNiagaJasaTender</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        :root { --primary: #2563EB; --primary-deep: #1D4ED8; --ink: #0F172A; --body: #334155; --mute: #64748B; --canvas-soft: #F8FAFC; --hairline: #E2E8F0; }
        body { font-family: 'Manrope', sans-serif; background: var(--canvas-soft); }
        .login-grid { background-color: var(--ink); background-image: linear-gradient(rgba(255,255,255,.055) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.055) 1px, transparent 1px); background-size: 34px 34px; }
        .login-grid::after { position: absolute; right: -8rem; bottom: -10rem; width: 28rem; height: 28rem; content: ''; border: 1px solid rgba(147,197,253,.2); border-radius: 50%; box-shadow: 0 0 0 2rem rgba(147,197,253,.04), 0 0 0 5rem rgba(147,197,253,.025); }
        .form-shell { box-shadow: 0 24px 80px rgba(15, 23, 42, .08); }
        .login-feature { display: flex; align-items: center; gap: .7rem; padding: .75rem .85rem; border: 1px solid rgba(255,255,255,.12); border-radius: .7rem; background: rgba(255,255,255,.06); color: rgba(219,234,254,.78); font-size: .7rem; font-weight: 600; }
        .login-feature i { display: grid; width: 1.8rem; height: 1.8rem; place-items: center; color: #bfdbfe; border-radius: .5rem; background: rgba(37,99,235,.3); }
        .login-form-card { border: 1px solid var(--hairline); background: rgba(255,255,255,.92); box-shadow: 0 24px 70px rgba(15,23,42,.09); }
        .login-submit { background: var(--primary); transition: background-color 150ms ease, transform 150ms ease, box-shadow 150ms ease; }
        .login-submit:hover { background: var(--primary-deep); transform: translateY(-1px); box-shadow: 0 12px 24px rgba(37,99,235,.2); }
        .login-submit:active { transform: scale(.98); }
        .login-submit:focus-visible { outline: 3px solid rgba(37,99,235,.25); outline-offset: 3px; }
        .login-input { border-color: var(--hairline); transition: border-color 150ms ease, box-shadow 150ms ease; }
        .login-input:focus { border-color: var(--primary); box-shadow: 0 0 0 4px rgba(37,99,235,.1); }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="min-h-screen bg-[#f4f8ff] text-slate-900" x-data="loginForm()">
    <main class="min-h-screen lg:grid lg:grid-cols-[minmax(420px,0.9fr)_minmax(500px,1.1fr)]">
        <section class="login-grid relative hidden overflow-hidden px-12 py-12 text-white lg:flex lg:flex-col lg:justify-between xl:px-20">
            <a href="{{ route('home') }}" class="relative z-10 inline-flex w-fit items-center gap-3" title="Kembali ke beranda">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#3b8cff] text-lg font-extrabold shadow-lg shadow-blue-950/30">S</span>
                <span><span class="block text-lg font-extrabold tracking-tight">SignalNiaga</span><span class="block text-[10px] font-semibold uppercase tracking-[0.18em] text-blue-200">PT Signal Panca Utama</span></span>
            </a>

            <div class="relative z-10 max-w-md py-12">
                <p class="mb-5 text-xs font-bold uppercase tracking-[0.22em] text-blue-200">Business management system</p>
                <h1 class="text-4xl font-extrabold leading-tight tracking-[-0.03em] xl:text-5xl">Satu ruang kerja untuk bisnis yang terus bergerak.</h1>
                <p class="mt-6 max-w-sm text-sm leading-7 text-blue-100/75">Kelola tender, pekerjaan jasa, perdagangan, dan arus transaksi perusahaan dalam satu tempat yang terhubung.</p>
                <div class="mt-8 grid max-w-sm gap-2">
                    <div class="login-feature"><i class="fa-solid fa-layer-group"></i><span>Semua alur kerja terhubung dalam satu workspace</span></div>
                    <div class="login-feature"><i class="fa-solid fa-chart-simple"></i><span>Informasi operasional lebih mudah dipantau</span></div>
                    <div class="login-feature"><i class="fa-solid fa-shield-halved"></i><span>Akses internal dengan kontrol berbasis peran</span></div>
                </div>
                <div class="mt-9 flex items-center gap-3 text-xs font-semibold text-blue-100/80"><span class="h-2 w-2 rounded-full bg-[#8fc1ff] shadow-[0_0_0_4px_rgba(143,193,255,.15)]"></span>Ruang kerja internal PT Signal Panca Utama</div>
            </div>

            <div class="relative z-10 flex items-center justify-between border-t border-white/15 pt-5 text-[11px] text-blue-100/60"><span>© {{ date('Y') }} PT Signal Panca Utama</span><span>Internal workspace</span></div>
        </section>

        <section class="flex min-h-screen items-center justify-center px-5 py-8 sm:px-8 lg:px-12 xl:px-20">
            <div class="form-shell login-form-card w-full max-w-[460px] rounded-2xl bg-white p-6 sm:p-9 lg:p-10">
                <div class="mb-10 flex items-center justify-between lg:hidden">
                    <a href="{{ route('home') }}" class="flex items-center gap-3"><span class="flex h-9 w-9 items-center justify-center rounded-lg bg-[#155eef] font-extrabold text-white shadow-md shadow-blue-600/20">S</span><span class="text-base font-extrabold tracking-tight text-[#0d2b5c]">SignalNiaga</span></a>
                    <span class="text-[10px] font-bold uppercase tracking-[0.16em] text-blue-400">Internal</span>
                </div>

                <div class="mb-9">
                    <div class="mb-5 flex items-center gap-2 text-[11px] font-bold uppercase tracking-[0.16em] text-blue-500"><span class="flex h-6 w-6 items-center justify-center rounded-md bg-blue-50 text-blue-600"><i class="fa-solid fa-shield-halved"></i></span>Secure internal access</div>
                    <p class="mb-3 text-xs font-bold uppercase tracking-[0.2em] text-[#155eef]">Selamat datang kembali</p>
                    <h2 class="text-3xl font-extrabold tracking-[-0.03em] text-[#0d2b5c]">Masuk ke ruang kerja</h2>
                    <p class="mt-3 text-sm leading-6 text-slate-500">Gunakan akun perusahaan Anda untuk melanjutkan ke dashboard.</p>
                </div>

                @if(session('status'))
                    <div class="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('status') }}</div>
                @endif
                @if($errors->any())
                    <div class="mb-5 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700"><p class="font-semibold">Login belum berhasil.</p><p class="mt-1 text-xs">{{ $errors->first() }}</p></div>
                @endif

                <form action="{{ route('login') }}" method="POST" class="space-y-5" @submit="submitForm">
                    @csrf
                    <div>
                        <label for="email" class="mb-2 block text-sm font-semibold text-slate-700">Alamat email</label>
                        <div class="relative"><i class="fa-regular fa-envelope pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 transition" :class="emailFocused ? 'text-blue-600' : 'text-slate-400'"></i><input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="email" required autofocus placeholder="nama@perusahaan.co.id" @focus="emailFocused = true" @blur="emailFocused = false" @input="validateEmail" class="login-input h-12 w-full rounded-lg border bg-white pl-11 pr-11 text-sm outline-none transition placeholder:text-slate-400" :class="emailError ? 'border-rose-300 focus:border-rose-500 focus:ring-4 focus:ring-rose-500/10' : 'border-slate-300 focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10'"><span x-show="emailValid" x-cloak class="absolute right-4 top-1/2 -translate-y-1/2 text-emerald-500"><i class="fa-solid fa-circle-check"></i></span></div>
                        <p x-show="emailError" x-cloak class="mt-1.5 text-xs text-rose-600">Masukkan alamat email yang valid.</p>
                    </div>
                    <div><div class="mb-2 flex items-center justify-between"><label for="password" class="block text-sm font-semibold text-slate-700">Password</label><a href="{{ route('password.request') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-800">Lupa password?</a></div><div class="relative"><i class="fa-solid fa-lock pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 transition" :class="passwordFocused ? 'text-blue-600' : 'text-slate-400'"></i><input id="password" :type="showPassword ? 'text' : 'password'" name="password" autocomplete="current-password" required placeholder="Masukkan password" @focus="passwordFocused = true" @blur="passwordFocused = false" @input="passwordTouched = true" class="login-input h-12 w-full rounded-lg border bg-white pl-11 pr-12 text-sm outline-none transition placeholder:text-slate-400 focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10"><button type="button" @click="showPassword = !showPassword" class="absolute right-3 top-1/2 flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-md text-slate-400 hover:bg-slate-100 hover:text-slate-700" :aria-label="showPassword ? 'Sembunyikan password' : 'Tampilkan password'"><i class="fa-solid" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i></button></div><p x-show="passwordTouched && !passwordFilled" x-cloak class="mt-1.5 text-xs text-rose-600">Password wajib diisi.</p></div>
                    <label class="flex items-center gap-2 text-xs text-slate-500"><input type="checkbox" name="remember" value="1" class="h-4 w-4 rounded border-blue-200 text-blue-600 focus:ring-blue-600">Ingat saya di perangkat ini</label>
                    <button type="submit" :disabled="loading" class="login-submit flex h-12 w-full items-center justify-center gap-2 rounded-lg px-4 text-sm font-bold text-white shadow-lg shadow-blue-600/20 focus:outline-none focus:ring-4 focus:ring-blue-600/20 disabled:cursor-wait disabled:opacity-70"><i class="fa-solid" :class="loading ? 'fa-spinner fa-spin' : 'fa-arrow-right-to-bracket'"></i><span x-text="loading ? 'Memverifikasi...' : 'Masuk ke dashboard'"></span></button>
                </form>

                <div class="mt-10 border-t border-blue-100 pt-5 text-center text-xs leading-5 text-slate-400">Akses sistem dikelola oleh administrator perusahaan.<br><a href="{{ route('home') }}" class="font-semibold text-[#155eef] hover:text-[#0d2b5c]">Kembali ke halaman utama</a></div>
            </div>
        </section>
    </main>
</body>
<script>
    function loginForm() {
        return {
            showPassword: false,
            emailFocused: false,
            passwordFocused: false,
            emailValid: false,
            emailError: false,
            passwordTouched: false,
            loading: false,
            get passwordFilled() { return document.getElementById('password')?.value.length > 0; },
            validateEmail(event) {
                const value = event.target.value.trim();
                this.emailValid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
                this.emailError = value.length > 0 && !this.emailValid;
            },
            submitForm(event) {
                const email = document.getElementById('email')?.value.trim();
                const password = document.getElementById('password')?.value;
                this.emailValid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
                this.emailError = !this.emailValid;
                this.passwordTouched = true;
                if (!this.emailValid || !password) {
                    event.preventDefault();
                    return;
                }
                this.loading = true;
            }
        };
    }
</script>
</html>