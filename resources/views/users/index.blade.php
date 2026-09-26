@extends('layouts.app')

@section('title', 'Manajemen User')
@section('header-title', 'Manajemen User')

@section('content')
<div class="space-y-6" x-data="userForm()" x-on:keydown.escape.window="open = false">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <p class="text-sm text-slate-500">Kelola akun, role, status aktif, dan reset password pengguna.</p>
        </div>
        <button type="button" @click="editUser = null; showPassword = false; showConfirmation = false; open = true" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700">
            <i class="fa-solid fa-user-plus"></i> Tambah User
        </button>
    </div>

    <form method="GET" class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
        <input name="search" value="{{ request('search') }}" placeholder="Cari nama atau email..." class="w-full md:w-80 px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
    </form>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-slate-500">
                    <tr><th class="p-4">User</th><th class="p-4">Role</th><th class="p-4">Status</th><th class="p-4">Aksi</th></tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $user)
                        <tr>
                            <td class="p-4"><div class="font-semibold text-slate-800">{{ $user->name }}</div><div class="text-xs text-slate-500">{{ $user->email }}</div></td>
                            <td class="p-4 text-slate-600">{{ ucwords(str_replace('_', ' ', $user->role->name ?? '-')) }}</td>
                            <td class="p-4"><span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $user->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">{{ $user->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                            <td class="p-4"><div class="flex items-center gap-4"><button type="button" @click="editUser = {{ $user->toJson() }}; showPassword = false; showConfirmation = false; open = true" class="text-blue-600 hover:underline font-semibold">Edit</button><button type="button" @click="deleteUser = {{ $user->toJson() }}; typedEmail = ''; deleteOpen = true" class="text-rose-600 hover:underline font-semibold" :disabled="{{ $user->id === auth()->id() ? 'true' : 'false' }}" {{ $user->id === auth()->id() ? 'title="Akun yang sedang digunakan tidak dapat dihapus"' : '' }}>Hapus</button></div></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="p-8 text-center text-slate-500">Belum ada user.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4">{{ $users->links() }}</div>
    </div>
    <div x-show="open" x-cloak style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50">
        <div @click.outside="open = false" class="bg-white rounded-2xl shadow-xl w-full max-w-lg p-6">
            <div class="flex items-center justify-between mb-5"><h2 class="text-lg font-bold"> <span x-text="editUser ? 'Edit User' : 'Tambah User'"></span></h2><button type="button" @click.prevent.stop="open = false" onclick="this.closest('[x-show]').style.display = 'none'" class="text-slate-400 hover:text-slate-700 cursor-pointer" aria-label="Tutup modal"><i class="fa-solid fa-xmark"></i></button></div>
        <form :action="editUser ? '/users/' + editUser.id : '{{ route('users.store') }}'" method="POST" class="space-y-4">
            @csrf
            <template x-if="editUser"><input type="hidden" name="_method" value="PUT"></template>
            <input name="name" :value="editUser?.name ?? ''" required placeholder="Nama lengkap" class="w-full px-3 py-2.5 border rounded-xl text-sm">
            <input type="email" name="email" :value="editUser?.email ?? ''" required placeholder="email@perusahaan.com" class="w-full px-3 py-2.5 border rounded-xl text-sm">
            <select name="role_id" required class="w-full px-3 py-2.5 border rounded-xl text-sm"><option value="">Pilih role</option>@foreach($roles as $role)<option value="{{ $role->id }}" x-bind:selected="editUser?.role_id == {{ $role->id }}">{{ ucwords(str_replace('_', ' ', $role->name)) }}</option>@endforeach</select>
            <div class="relative">
                <input :type="showPassword ? 'text' : 'password'" name="password" :required="!editUser" placeholder="Password (minimal 8 karakter)" autocomplete="new-password" class="w-full px-3 py-2.5 pr-11 border rounded-xl text-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-500/20 outline-none">
                <button type="button" @click="showPassword = !showPassword" class="absolute right-2 top-1/2 flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-lg text-slate-400 hover:bg-blue-50 hover:text-blue-600" :aria-label="showPassword ? 'Sembunyikan password' : 'Tampilkan password'"><i class="fa-solid" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i></button>
            </div>
            <div class="relative">
                <input :type="showConfirmation ? 'text' : 'password'" name="password_confirmation" :required="!editUser" placeholder="Konfirmasi password" autocomplete="new-password" class="w-full px-3 py-2.5 pr-11 border rounded-xl text-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-500/20 outline-none">
                <button type="button" @click="showConfirmation = !showConfirmation" class="absolute right-2 top-1/2 flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-lg text-slate-400 hover:bg-blue-50 hover:text-blue-600" :aria-label="showConfirmation ? 'Sembunyikan konfirmasi password' : 'Tampilkan konfirmasi password'"><i class="fa-solid" :class="showConfirmation ? 'fa-eye-slash' : 'fa-eye'"></i></button>
            </div>
            <label class="flex items-center gap-2 text-sm text-slate-600"><input type="checkbox" name="is_active" value="1" :checked="editUser ? editUser.is_active : true" class="rounded text-blue-600"> User aktif</label>
            <button class="w-full py-2.5 rounded-xl bg-blue-600 text-white font-semibold hover:bg-blue-700">Simpan</button>
        </form>
        </div>
    </div>
    <div x-show="deleteOpen" x-cloak style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60">
        <div @click.outside="deleteOpen = false" class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
            <div class="flex items-start justify-between gap-4">
                <div><div class="mb-2 flex h-10 w-10 items-center justify-center rounded-xl bg-rose-50 text-rose-600"><i class="fa-solid fa-trash-can"></i></div><h2 class="text-lg font-bold text-slate-900">Hapus akun user?</h2></div>
                <button type="button" @click="deleteOpen = false" class="text-slate-400 hover:text-slate-700" aria-label="Tutup konfirmasi hapus"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <p class="mt-4 text-sm leading-6 text-slate-600">Akun <strong class="text-slate-900" x-text="deleteUser?.name"></strong> akan dihapus permanen. Ketik ulang email akun untuk melanjutkan:</p>
            <p class="mt-3 rounded-lg bg-slate-50 px-3 py-2 text-sm font-semibold text-slate-700" x-text="deleteUser?.email"></p>
            <form :action="deleteUser ? '/users/' + deleteUser.id : '#'" method="POST" class="mt-4 space-y-4" @submit="confirmDelete">
                @csrf
                <input type="hidden" name="_method" value="DELETE">
                <input type="email" name="email_confirmation" x-model="typedEmail" required autocomplete="off" placeholder="Ketik ulang email akun" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20">
                <p x-show="typedEmail && !emailMatches" x-cloak class="text-xs text-rose-600">Email belum sesuai dengan akun yang akan dihapus.</p>
                <div class="flex justify-end gap-3"><button type="button" @click="deleteOpen = false" class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50">Batal</button><button type="submit" :disabled="!emailMatches" class="rounded-xl bg-rose-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-rose-700 disabled:cursor-not-allowed disabled:opacity-50">Hapus permanen</button></div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function userForm() { return { open: false, editUser: null, showPassword: false, showConfirmation: false, deleteOpen: false, deleteUser: null, typedEmail: '', get emailMatches() { return Boolean(this.deleteUser && this.typedEmail.trim().toLowerCase() === this.deleteUser.email.toLowerCase()); }, confirmDelete(event) { if (!this.emailMatches) event.preventDefault(); } }; }
</script>
@endsection
