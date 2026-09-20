<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Baru - SignalNiagaJasaTender</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-900 flex items-center justify-center p-4">
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
                <input type="password" name="password" required class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2.5">
            </label>
            <label class="block text-sm font-semibold text-slate-700">Konfirmasi Password
                <input type="password" name="password_confirmation" required class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2.5">
            </label>
            <button class="w-full rounded-xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white hover:bg-blue-700">Simpan Password</button>
        </form>
    </main>
</body>
</html>