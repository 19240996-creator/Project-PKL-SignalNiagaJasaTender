<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Baru - SignalNiagaJasaTender</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="min-h-screen bg-slate-900 flex items-center justify-center p-4" x-data="{ showPassword: false, showConfirmation: false, password: '', confirmation: '' }">
    <main class="w-full max-w-md bg-white rounded-2xl p-8 shadow-2xl">
        <h1 class="text-2xl font-bold text-slate-800">Buat Password Baru</h1>
        @if($errors->any())
            <div class="mt-5 rounded-xl bg-rose-50 border border-rose-200 p-3 text-sm text-rose-700">{{ $errors->first() }}</div>
        @endif
        <form action="{{ route('password.update') }}" method="POST" class="mt-6 space-y-4">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <label class="block text-sm font-semibold text-slate-700">Email
                <input type="email" name="email" value="{{ old('email', $email) }}" required class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2.5">
            </label>
            <label class="block text-sm font-semibold text-slate-700">Password Baru
                <div class="relative mt-1">
                    <input :type="showPassword ? 'text' : 'password'" name="password" x-model="password" required minlength="8" autocomplete="new-password" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 pr-11 focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600/20">
                    <button type="button" @click="showPassword = !showPassword" class="absolute right-2 top-1/2 flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-lg text-slate-400 hover:bg-blue-50 hover:text-blue-600" :aria-label="showPassword ? 'Sembunyikan password baru' : 'Tampilkan password baru'">
                        <i class="fa-solid" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                    </button>
                </div>
                <span class="mt-1 block text-xs font-normal text-slate-400">Minimal 8 karakter.</span>
            </label>
            <label class="block text-sm font-semibold text-slate-700">Konfirmasi Password
                <div class="relative mt-1">
                    <input :type="showConfirmation ? 'text' : 'password'" name="password_confirmation" x-model="confirmation" required autocomplete="new-password" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 pr-11 focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600/20">
                    <button type="button" @click="showConfirmation = !showConfirmation" class="absolute right-2 top-1/2 flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-lg text-slate-400 hover:bg-blue-50 hover:text-blue-600" :aria-label="showConfirmation ? 'Sembunyikan konfirmasi password' : 'Tampilkan konfirmasi password'">
                        <i class="fa-solid" :class="showConfirmation ? 'fa-eye-slash' : 'fa-eye'"></i>
                    </button>
                </div>
                <span x-show="confirmation && password === confirmation" x-cloak class="mt-1 block text-xs font-normal text-emerald-600">Password cocok.</span>
                <span x-show="confirmation && password !== confirmation" x-cloak class="mt-1 block text-xs font-normal text-rose-600">Password belum cocok.</span>
            </label>
            <div class="grid grid-cols-2 gap-3">
                <a href="{{ route('login') }}" class="flex items-center justify-center rounded-xl bg-rose-600 px-4 py-3 text-sm font-semibold text-white hover:bg-rose-700">Batal</a>
                <button type="submit" class="rounded-xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white hover:bg-blue-700">Simpan Password</button>
            </div>
        </form>
    </main>
</body>
</html>