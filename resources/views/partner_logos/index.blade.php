@extends('layouts.app')

@section('title', 'Kelola Logo Klien')
@section('header-title', 'Logo Klien Landing Page')

@section('content')
<div class="space-y-5" x-data="{ createModal: false, editModal: false, editData: {} }">

    @if(session('success'))
        <div class="p-3.5 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-medium rounded-lg flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-circle-check text-emerald-600"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800"><i class="fa-solid fa-xmark"></i></button>
        </div>
    @endif

    <div class="bg-white rounded-lg p-4 border border-slate-200 shadow-xs flex flex-col md:flex-row justify-between items-center gap-3">
        <div>
            <h2 class="text-base font-bold text-slate-900 tracking-tight">Logo Klien & Mitra</h2>
            <p class="text-xs text-slate-500">Kelola daftar logo klien yang ditampilkan pada carousel landing page depan.</p>
        </div>

        <button @click="createModal = true" class="px-3.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-medium text-xs rounded-md shadow-xs transition flex items-center gap-1.5 shrink-0">
            <i class="fa-solid fa-plus text-[11px]"></i> Tambah Logo Klien
        </button>
    </div>

    <div class="bg-white rounded-lg border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-200 uppercase tracking-wider text-[11px]">
                        <th class="p-3.5 w-16 text-center">Urutan</th>
                        <th class="p-3.5">Preview Logo</th>
                        <th class="p-3.5">Nama Klien / Perusahaan</th>
                        <th class="p-3.5">Kategori</th>
                        <th class="p-3.5">Tipe Media</th>
                        <th class="p-3.5 text-center whitespace-nowrap">Status Tampil</th>
                        <th class="p-3.5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($partnerLogos as $pl)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="p-3.5 font-mono text-center text-slate-600 font-medium">{{ $pl->sort_order }}</td>
                            <td class="p-3.5">
                                <div class="px-3 py-1.5 bg-slate-50 rounded-md border border-slate-200 inline-flex items-center gap-2 max-w-[200px]">
                                    @if($pl->logo_path)
                                        <img src="{{ asset('storage/' . $pl->logo_path) }}" alt="{{ $pl->name }}" class="h-6 max-w-[100px] object-contain">
                                    @else
                                        <span class="font-bold text-xs {{ $pl->badge_color }}">{{ $pl->badge_text ?? $pl->name }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="p-3.5 font-semibold text-slate-800">{{ $pl->name }}</td>
                            <td class="p-3.5 text-slate-600">{{ $pl->category ?? '-' }}</td>
                            <td class="p-3.5">
                                @if($pl->logo_path)
                                    <span class="px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 text-slate-700 border border-slate-200 inline-flex items-center gap-1">
                                        <i class="fa-regular fa-image text-[10px]"></i> Gambar
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 text-slate-700 border border-slate-200 inline-flex items-center gap-1">
                                        <i class="fa-solid fa-font text-[10px]"></i> Badge
                                    </span>
                                @endif
                            </td>
                            <td class="p-3.5 text-center whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-xs font-medium border {{ $pl->is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-slate-100 text-slate-600 border border-slate-200' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $pl->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                    {{ $pl->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="p-3.5 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button @click="editData = { 
                                        id: {{ $pl->id }}, 
                                        name: '{{ addslashes($pl->name) }}', 
                                        category: '{{ addslashes($pl->category ?? '') }}', 
                                        badge_text: '{{ addslashes($pl->badge_text ?? '') }}', 
                                        badge_color: '{{ $pl->badge_color ?? 'text-blue-600' }}', 
                                        sort_order: {{ $pl->sort_order }}, 
                                        is_active: {{ $pl->is_active ? 1 : 0 }},
                                        logo_url: '{{ $pl->logo_path ? asset('storage/' . $pl->logo_path) : '' }}'
                                    }; editModal = true;" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium text-xs rounded-md transition inline-flex items-center gap-1">
                                        <i class="fa-solid fa-pen-to-square text-[10px]"></i> Edit
                                    </button>
                                    
                                    <form action="{{ route('partner-logos.destroy', $pl->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus logo ini?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-2.5 py-1 bg-rose-50 hover:bg-rose-100 text-rose-700 font-medium text-xs rounded-md border border-rose-200 transition inline-flex items-center gap-1" title="Hapus Logo">
                                            <i class="fa-solid fa-trash text-[10px]"></i> Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-slate-400 text-xs">Belum ada logo klien ditambahkan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3.5 border-t border-slate-100">
            {{ $partnerLogos->links() }}
        </div>
    </div>

    <!-- Modal Create Partner Logo -->
    <div x-show="createModal" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4" x-cloak>
        <div class="bg-white rounded-lg border border-slate-200 max-w-lg w-full p-5 shadow-xl space-y-4 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center border-b border-slate-200 pb-3">
                <h3 class="font-bold text-sm text-slate-900">Tambah Logo Klien Baru</h3>
                <button @click="createModal = false" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-sm"></i></button>
            </div>

            <form action="{{ route('partner-logos.store') }}" method="POST" enctype="multipart/form-data" class="space-y-3.5">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Nama Klien / Perusahaan <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" required placeholder="PT Pelanggan Utama..." class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Kategori Klien</label>
                        <input type="text" name="category" placeholder="BUMN / Instansi / Swasta" class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Urutan Tampil</label>
                        <input type="number" name="sort_order" value="1" class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div class="p-4 bg-slate-50 rounded-lg border border-dashed border-slate-300 space-y-2 text-center">
                    <label class="block text-xs font-semibold text-slate-800 uppercase tracking-wider">File Gambar Logo Klien</label>
                    <p class="text-[11px] text-slate-500">Format: PNG, JPG, JPEG, SVG, WEBP (Maksimal 4MB).</p>
                    <input type="file" name="logo" accept="image/*" class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-medium file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 cursor-pointer">
                </div>

                <div class="p-3 bg-slate-50 rounded-md border border-slate-200 space-y-2">
                    <span class="text-xs font-semibold text-slate-800 block">Atau Opsi Teks Badge (Jika Tidak Ada Gambar):</span>
                    
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-medium text-slate-600 mb-1">Teks Singkatan</label>
                            <input type="text" name="badge_text" placeholder="Contoh: SPU" class="w-full px-3 py-1.5 border border-slate-200 bg-white rounded-md text-xs outline-none">
                        </div>
                        <div>
                            <label class="block text-[11px] font-medium text-slate-600 mb-1">Warna Teks Badge</label>
                            <select name="badge_color" class="w-full px-3 py-1.5 border border-slate-200 bg-white rounded-md text-xs outline-none">
                                <option value="text-slate-800">Slate (Gelap)</option>
                                <option value="text-blue-600">Biru (Blue)</option>
                                <option value="text-emerald-600">Hijau (Emerald)</option>
                                <option value="text-amber-600">Amber (Oranye)</option>
                                <option value="text-rose-600">Merah (Rose)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-200">
                    <button type="button" @click="createModal = false" class="px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium text-xs rounded-md transition">Batal</button>
                    <button type="submit" class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-medium text-xs rounded-md transition shadow-xs">Simpan Logo</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Partner Logo -->
    <div x-show="editModal" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4" x-cloak>
        <div class="bg-white rounded-lg border border-slate-200 max-w-lg w-full p-5 shadow-xl space-y-4 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center border-b border-slate-200 pb-3">
                <h3 class="font-bold text-sm text-slate-900">Edit Logo Klien</h3>
                <button @click="editModal = false" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-sm"></i></button>
            </div>

            <form :action="'/partner-logos/' + editData.id" method="POST" enctype="multipart/form-data" class="space-y-3.5">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Nama Klien / Perusahaan <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" x-model="editData.name" required class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Kategori Klien</label>
                        <input type="text" name="category" x-model="editData.category" class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Urutan Tampil</label>
                        <input type="number" name="sort_order" x-model="editData.sort_order" class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <!-- Preview Gambar Saat Ini -->
                <template x-if="editData.logo_url">
                    <div class="p-3 bg-slate-50 border border-slate-200 rounded-md flex items-center gap-3">
                        <span class="text-xs font-medium text-slate-600">Logo Saat Ini:</span>
                        <img :src="editData.logo_url" alt="Current Logo" class="h-6 max-w-[100px] object-contain bg-white p-1 rounded border">
                    </div>
                </template>

                <div class="p-4 bg-slate-50 rounded-lg border border-dashed border-slate-300 space-y-2 text-center">
                    <label class="block text-xs font-semibold text-slate-800 uppercase tracking-wider">Ganti File Gambar Logo</label>
                    <p class="text-[11px] text-slate-500">Kosongkan jika tidak ingin mengubah file logo gambar saat ini.</p>
                    <input type="file" name="logo" accept="image/*" class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-medium file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 cursor-pointer">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Status Tampil</label>
                        <select name="is_active" x-model="editData.is_active" class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none bg-white">
                            <option value="1">Aktif (Tampil)</option>
                            <option value="0">Nonaktif</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Warna Teks Badge</label>
                        <select name="badge_color" x-model="editData.badge_color" class="w-full px-3 py-1.5 border border-slate-200 rounded-md text-xs outline-none bg-white">
                            <option value="text-slate-800">Slate (Gelap)</option>
                            <option value="text-blue-600">Biru</option>
                            <option value="text-emerald-600">Hijau</option>
                            <option value="text-amber-600">Amber</option>
                            <option value="text-rose-600">Rose</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-200">
                    <button type="button" @click="editModal = false" class="px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium text-xs rounded-md transition">Batal</button>
                    <button type="submit" class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-medium text-xs rounded-md transition shadow-xs">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
