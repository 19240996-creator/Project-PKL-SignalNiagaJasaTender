@extends('layouts.app')

@section('title', 'Manajemen User')
@section('header-title', 'Manajemen Pengguna & Hak Akses')

@section('content')
<div class="space-y-5" x-data="userForm()" x-on:keydown.escape.window="open = false">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
            <h2 class="text-base font-bold text-slate-900 tracking-tight">Daftar Pengguna Sistem</h2>
            <p class="text-xs text-slate-500">Kelola akun, hak akses role (Admin, Manager, Owner), dan status aktif pengguna.</p>
        </div>
        <button type="button" @click="editUser = null; showPassword = false; showConfirmation = false; open = true" class="inline-flex items-center justify-center gap-1.5 px-3.5 py-1.5 rounded-md bg-blue-600 text-white text-xs font-medium hover:bg-blue-700 transition shadow-xs">
            <i class="fa-solid fa-user-plus text-[11px]"></i> Tambah Pengguna
        </button>
    </div>

    <form method="GET" class="bg-white p-3.5 rounded-lg border border-slate-200 shadow-xs flex items-center justify-between gap-3">
        <input name="search" value="{{ request('search') }}" placeholder="Cari nama atau email pengguna..." class="w-full md:w-80 px-3 py-1.5 rounded-md border border-slate-200 text-xs focus:ring-2 focus:ring-blue-500 outline-none">
        @if(request('search'))
            <a href="{{ route('users.index') }}" class="text-xs text-slate-500 hover:text-slate-800">Reset</a>
        @endif
    </form>

    <div class="bg-white rounded-lg border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-200 uppercase tracking-wider text-[11px]">
                    <tr>
                        <th class="p-3.5">Pengguna</th>
                        <th class="p-3.5">Role Akses</th>
                        <th class="p-3.5 text-center whitespace-nowrap w-32">Status</th>
                        <th class="p-3.5 text-center whitespace-nowrap w-44">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $user)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="p-3.5">
                                <div class="font-semibold text-slate-800">{{ $user->name }}</div>
                                <div class="text-[11px] text-slate-500">{{ $user->email }}</div>
                            </td>
                            <td class="p-3.5">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                    {{ ucwords(str_replace('_', ' ', $user->role->name ?? '-')) }}
                                </span>
                            </td>
                            <td class="p-3.5 text-center whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-xs font-medium border {{ $user->is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-rose-50 text-rose-700 border-rose-200' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $user->is_active ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                                    {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="p-3.5 text-center whitespace-nowrap">
                                <div class="inline-flex items-center justify-center gap-1.5">
                                    <button type="button" @click="editUser = {{ $user->toJson() }}; showPassword = false; showConfirmation = false; open = true" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium text-xs rounded-md transition inline-flex items-center gap-1" title="Edit Akun">
                                        <i class="fa-solid fa-pen-to-square text-[10px]"></i> Edit
                                    </button>
                                    @if($user->id !== auth()->id())
                                        <button type="button" @click="deleteUser = {{ $user->toJson() }}; typedEmail = ''; deleteOpen = true" class="px-2.5 py-1 bg-rose-50 hover:bg-rose-100 text-rose-700 font-medium text-xs rounded-md border border-rose-200 transition inline-flex items-center gap-1" title="Hapus Akun">
                                            <i class="fa-solid fa-trash text-[10px]"></i> Hapus
                                        </button>
                                    @else
                                        <span class="px-2.5 py-1 bg-slate-50 text-slate-400 font-medium text-xs rounded-md border border-slate-200 inline-flex items-center gap-1 cursor-not-allowed">
                                            <i class="fa-solid fa-lock text-[10px]"></i> Anda
                                        </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="p-8 text-center text-slate-400 text-xs">Belum ada pengguna terdaftar.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3.5 border-t border-slate-100">{{ $users->links() }}</div>
    </div>

    <!-- Modal Form User -->
    <div x-show="open" x-cloak style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs">
        <div @click.outside="open = false" class="bg-white rounded-lg border border-slate-200 shadow-xl w-full max-w-lg p-5 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-200 pb-3">
                <h3 class="text-sm font-bold text-slate-900">
                    <span x-text="editUser ? 'Edit Data Pengguna' : 'Tambah Pengguna Baru'"></span>
                </h3>
                <button type="button" @click="open = false" class="text-slate-400 hover:text-slate-600 cursor-pointer" aria-label="Tutup modal">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <form :action="editUser ? '/users/' + editUser.id : '{{ route('users.store') }}'" method="POST" class="space-y-3.5">
                @csrf
                <template x-if="editUser"><input type="hidden" name="_method" value="PUT"></template>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Nama Lengkap <span class="text-rose-500">*</span></label>
                    <input name="name" :value="editUser?.name ?? ''" required placeholder="Nama lengkap staf" class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Alamat Email <span class="text-rose-500">*</span></label>
                    <input type="email" name="email" :value="editUser?.email ?? ''" required placeholder="email@perusahaan.com" class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Role Pengguna <span class="text-rose-500">*</span></label>
                    <select name="role_id" required class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                        <option value="">Pilih Role Akses</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" x-bind:selected="editUser?.role_id == {{ $role->id }}">{{ ucwords(str_replace('_', ' ', $role->name)) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="relative">
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Password <span x-show="!editUser" class="text-rose-500">*</span></label>
                    <input :type="showPassword ? 'text' : 'password'" name="password" :required="!editUser" placeholder="Minimal 8 karakter" autocomplete="new-password" class="w-full px-3 py-1.5 pr-10 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500">
                    <button type="button" @click="showPassword = !showPassword" class="absolute right-2 top-7 text-slate-400 hover:text-slate-600" :aria-label="showPassword ? 'Sembunyikan' : 'Tampilkan'">
                        <i class="fa-solid text-xs" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                    </button>
                </div>

                <div class="relative">
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Konfirmasi Password</label>
                    <input :type="showConfirmation ? 'text' : 'password'" name="password_confirmation" :required="!editUser" placeholder="Ketik ulang password" autocomplete="new-password" class="w-full px-3 py-1.5 pr-10 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500">
                    <button type="button" @click="showConfirmation = !showConfirmation" class="absolute right-2 top-7 text-slate-400 hover:text-slate-600" :aria-label="showConfirmation ? 'Sembunyikan' : 'Tampilkan'">
                        <i class="fa-solid text-xs" :class="showConfirmation ? 'fa-eye-slash' : 'fa-eye'"></i>
                    </button>
                </div>

                <label class="flex items-center gap-2 text-xs font-medium text-slate-700 pt-1">
                    <input type="checkbox" name="is_active" value="1" :checked="editUser ? editUser.is_active : true" class="rounded text-slate-900 border-slate-300">
                    <span>Status Akun Aktif</span>
                </label>

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-200">
                    <button type="button" @click="open = false" class="px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium text-xs rounded-md transition">Batal</button>
                    <button type="submit" class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-medium text-xs rounded-md transition shadow-xs">Simpan Pengguna</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Delete User -->
    <div x-show="deleteOpen" x-cloak style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs">
        <div @click.outside="deleteOpen = false" class="bg-white rounded-lg border border-slate-200 shadow-xl w-full max-w-md p-5 space-y-4">
            <div class="flex items-start justify-between border-b border-slate-200 pb-3">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Hapus Akun Pengguna</h3>
                    <p class="text-xs text-slate-500">Tindakan ini permanen dan tidak dapat dibatalkan</p>
                </div>
                <button type="button" @click="deleteOpen = false" class="text-slate-400 hover:text-slate-600" aria-label="Tutup"><i class="fa-solid fa-xmark text-sm"></i></button>
            </div>

            <p class="text-xs text-slate-600">Akun <strong class="text-slate-900" x-text="deleteUser?.name"></strong> akan dihapus permanen. Ketik ulang email akun untuk melanjutkan konfirmasi:</p>
            <p class="rounded-md bg-slate-50 border border-slate-200 px-3 py-1.5 text-xs font-mono text-slate-800" x-text="deleteUser?.email"></p>

            <form :action="deleteUser ? '/users/' + deleteUser.id : '#'" method="POST" class="space-y-3.5" @submit="confirmDelete">
                @csrf
                <input type="hidden" name="_method" value="DELETE">
                <input type="email" name="email_confirmation" x-model="typedEmail" required autocomplete="off" placeholder="Ketik ulang email akun..." class="w-full rounded-md border border-slate-300 px-3 py-1.5 text-xs outline-none focus:ring-2 focus:ring-rose-500">
                <p x-show="typedEmail && !emailMatches" x-cloak class="text-[11px] text-rose-600">Email belum sesuai dengan akun yang akan dihapus.</p>
                
                <div class="flex justify-end gap-2 pt-2 border-t border-slate-200">
                    <button type="button" @click="deleteOpen = false" class="rounded-md border border-slate-200 px-3.5 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-50 transition">Batal</button>
                    <button type="submit" :disabled="!emailMatches" class="rounded-md bg-rose-600 px-4 py-1.5 text-xs font-medium text-white hover:bg-rose-700 disabled:cursor-not-allowed disabled:opacity-50 transition">Hapus Permanen</button>
                </div>
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
