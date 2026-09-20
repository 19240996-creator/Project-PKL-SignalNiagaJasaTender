<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - SignalNiagaJasaTender</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-900 flex items-center justify-center p-4">
    <main class="w-full max-w-md bg-white rounded-2xl p-8 shadow-2xl">
        <h1 class="text-2xl font-bold text-slate-800">Reset Password</h1>
        <p class="mt-2 text-sm text-slate-500">Masukkan email akun untuk menerima tautan reset password.</p>

        @if(session('status'))
            <div class="mt-5 rounded-xl bg-emerald-50 border border-emerald-200 p-3 text-sm text-emerald-700">{{ session('status') }}</div>
        @endif
        @if($errors->any())
            <div class="mt-5 rounded-xl bg-rose-50 border border-rose-200 p-3 text-sm text-rose-700">{{ $errors->first() }}</div>
        @endif

        <form action="{{ route('password.email') }}" method="POST" class="mt-6 space-y-4">
            @csrf
            <label class="block text-sm font-semibold text-slate-700">Email
                <input type="email" name="email" value="{{ old('email') }}" required class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2.5">
            </label>
            <button class="w-full rounded-xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white hover:bg-blue-700">Kirim Tautan Reset</button>
        </form>
        <a href="{{ route('login') }}" class="mt-5 block text-center text-sm font-semibold text-blue-600">Kembali ke login</a>
    </main>
</body>
</html>