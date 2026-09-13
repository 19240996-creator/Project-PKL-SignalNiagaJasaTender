@extends('layouts.app')

@section('title', 'Kelola Logo Klien (Super Admin)')
@section('header-title', 'Kustomisasi Logo Klien Landing Page')

@section('content')
<div class="space-y-6" x-data="{ createModal: false, editModal: false, editData: {} }">

    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 font-semibold text-sm rounded-2xl flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-circle-check text-emerald-600 text-lg"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800"><i class="fa-solid fa-xmark"></i></button>
        </div>
    @endif

    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex flex-col md:flex-row justify-between items-center gap-4">
        <div>
            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-purple-100 text-purple-800 border border-purple-200">Khusus Super Admin</span>
            <h3 class="font-bold text-slate-900 text-base mt-1">Daftar Logo Klien Slider Berjalan (Marquee)</h3>
            <p class="text-xs text-slate-500">Anda dapat mengupload gambar logo resmi klien (PNG/JPG/SVG/WEBP) atau menggunakan badge teks buatan.</p>
        </div>

        <button @click="createModal = true" class="px-5 py-2.5 bg-purple-600 hover:bg-purple-700 text-white font-semibold text-sm rounded-xl shadow transition flex items-center gap-2 shrink-0">
            <i class="fa-solid fa-cloud-arrow-up text-base"></i> Upload / Tambah Logo Klien
        </button>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-200">
                        <th class="p-4">Urutan</th>
                        <th class="p-4">Gambar Logo / Preview Badge</th>
                        <th class="p-4">Nama Klien / Perusahaan</th>
                        <th class="p-4">Kategori</th>
                        <th class="p-4">Tipe Media</th>
                        <th class="p-4">Status Tampil</th>
                        <th class="p-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($partnerLogos as $pl)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="p-4 font-bold text-slate-600">{{ $pl->sort_order }}</td>
                            <td class="p-4">
                                <div class="px-4 py-2 bg-slate-50 rounded-xl border border-slate-200 inline-flex items-center gap-2 max-w-[220px]">
                                    @if($pl->logo_path)
                                        <img src="{{ asset('storage/' . $pl->logo_path) }}" alt="{{ $pl->name }}" class="h-8 max-w-[120px] object-contain">
                                    @else
                                        <span class="font-black text-sm {{ $pl->badge_color }}">{{ $pl->badge_text ?? $pl->name }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="p-4 font-bold text-slate-800">{{ $pl->name }}</td>
                            <td class="p-4 text-xs text-slate-600 font-medium">{{ $pl->category ?? '-' }}</td>
                            <td class="p-4">
                                @if($pl->logo_path)
                                    <span class="px-2.5 py-1 rounded-md text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200 inline-flex items-center gap-1">
                                        <i class="fa-solid fa-image"></i> Gambar Logo File
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-md text-[11px] font-bold bg-slate-100 text-slate-600 border border-slate-200 inline-flex items-center gap-1">
                                        <i class="fa-solid fa-font"></i> Badge Teks
                                    </span>
                                @endif
                            </td>
                            <td class="p-4">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $pl->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                                    {{ $pl->is_active ? 'Aktif (Tampil)' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="p-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button @click="editData = { 
                                        id: {{ $pl->id }}, 
                                        name: '{{ addslashes($pl->name) }}', 
                                        category: '{{ addslashes($pl->category ?? '') }}', 
                                        badge_text: '{{ addslashes($pl->badge_text ?? '') }}', 
                                        badge_color: '{{ $pl->badge_color ?? 'text-blue-600' }}', 
                                        sort_order: {{ $pl->sort_order }}, 
                                        is_active: {{ $pl->is_active ? 1 : 0 }},
                                        logo_url: '{{ $pl->logo_path ? asset('storage/' . $pl->logo_path) : '' }}'
                                    }; editModal = true;" class="px-3 py-1 bg-amber-100 hover:bg-amber-200 text-amber-800 font-semibold text-xs rounded-lg transition flex items-center gap-1">
                                        <i class="fa-solid fa-pen-to-square"></i> Edit / Kirim Gambar
                                    </button>
                                    
                                    <form action="{{ route('partner-logos.destroy', $pl->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus logo ini?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-1 bg-rose-100 hover:bg-rose-200 text-rose-700 font-semibold text-xs rounded-lg transition" title="Hapus Logo">
                                            <i class="fa-solid fa-trash"></i> Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-slate-400">Belum ada logo klien ditambahkan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100">
            {{ $partnerLogos->links() }}
        </div>
    </div>

    <!-- Modal Create Partner Logo -->
    <div x-show="createModal" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4" x-cloak>
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center border-b pb-3">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center"><i class="fa-solid fa-cloud-arrow-up"></i></div>
                    <h3 class="font-bold text-lg text-slate-800">Tambah / Upload Logo Klien Baru</h3>
                </div>
                <button @click="createModal = false" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>

            <form action="{{ route('partner-logos.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Nama Klien / Perusahaan <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" required placeholder="PT Hutama Karya (Persero)..." class="w-full px-3 py-2 border border-slate-300 rounded-xl text-sm outline-none focus:ring-2 focus:ring-purple-500">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Kategori Klien</label>
                        <input type="text" name="category" placeholder="BUMN / Instansi / Swasta" class="w-full px-3 py-2 border border-slate-300 rounded-xl text-sm outline-none focus:ring-2 focus:ring-purple-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Urutan Tampil (Sort Order)</label>
                        <input type="number" name="sort_order" value="1" class="w-full px-3 py-2 border border-slate-300 rounded-xl text-sm outline-none focus:ring-2 focus:ring-purple-500">
                    </div>
                </div>

                <!-- Prominent File Upload Section -->
                <div class="p-4 bg-purple-50/60 rounded-2xl border-2 border-dashed border-purple-200 space-y-2 text-center">
                    <div class="w-10 h-10 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center mx-auto mb-1">
                        <i class="fa-solid fa-image text-xl"></i>
                    </div>
                    <label class="block text-xs font-bold text-purple-900 uppercase">Upload File Gambar Logo Klien</label>
                    <p class="text-[11px] text-slate-500">Format yang didukung: PNG, JPG, JPEG, SVG, WEBP (Maksimal 4MB).</p>
                    <input type="file" name="logo" accept="image/*" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-purple-600 file:text-white hover:file:bg-purple-700 cursor-pointer">
                </div>

                <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 space-y-3">
                    <span class="text-xs font-bold text-slate-800 block">Atau Opsi Teks Badge (Fallback Jika Tidak Mengupload Gambar):</span>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-700 uppercase mb-1">Teks Singkatan</label>
                            <input type="text" name="badge_text" placeholder="HK / PP / NINDYA" class="w-full px-3 py-2 border bg-white rounded-xl text-sm outline-none">
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-700 uppercase mb-1">Warna Teks Badge</label>
                            <select name="badge_color" class="w-full px-3 py-2 border bg-white rounded-xl text-sm outline-none">
                                <option value="text-red-600">Merah (red)</option>
                                <option value="text-blue-600">Biru (blue)</option>
                                <option value="text-emerald-600">Hijau (emerald)</option>
                                <option value="text-amber-600">Kuning/Oranye (amber)</option>
                                <option value="text-purple-600">Ungu (purple)</option>
                                <option value="text-rose-700">Merah Marun (rose)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t">
                    <button type="button" @click="createModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm rounded-xl">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-purple-600 hover:bg-purple-700 text-white font-semibold text-sm rounded-xl shadow flex items-center gap-2">
                        <i class="fa-solid fa-check"></i> Simpan Logo Klien
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Partner Logo & Upload Gambar -->
    <div x-show="editModal" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4" x-cloak>
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center border-b pb-3">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center"><i class="fa-solid fa-pen-to-square"></i></div>
                    <h3 class="font-bold text-lg text-slate-800">Edit / Upload Gambar Logo Klien</h3>
                </div>
                <button @click="editModal = false" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>

            <form :action="'/partner-logos/' + editData.id" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Nama Klien / Perusahaan <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" x-model="editData.name" required class="w-full px-3 py-2 border border-slate-300 rounded-xl text-sm outline-none focus:ring-2 focus:ring-purple-500">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Kategori Klien</label>
                        <input type="text" name="category" x-model="editData.category" class="w-full px-3 py-2 border border-slate-300 rounded-xl text-sm outline-none focus:ring-2 focus:ring-purple-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Urutan Tampil (Sort Order)</label>
                        <input type="number" name="sort_order" x-model="editData.sort_order" class="w-full px-3 py-2 border border-slate-300 rounded-xl text-sm outline-none focus:ring-2 focus:ring-purple-500">
                    </div>
                </div>

                <!-- Preview Gambar Saat Ini -->
                <template x-if="editData.logo_url">
                    <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl flex items-center gap-3">
                        <span class="text-xs font-semibold text-slate-600">Gambar Logo Saat Ini:</span>
                        <img :src="editData.logo_url" alt="Current Logo" class="h-8 max-w-[120px] object-contain bg-white p-1 rounded border">
                    </div>
                </template>

                <!-- File Upload Section in Edit Modal -->
                <div class="p-4 bg-purple-50/60 rounded-2xl border-2 border-dashed border-purple-200 space-y-2 text-center">
                    <div class="w-10 h-10 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center mx-auto mb-1">
                        <i class="fa-solid fa-cloud-arrow-up text-xl"></i>
                    </div>
                    <label class="block text-xs font-bold text-purple-900 uppercase">Kirim / Ganti File Gambar Logo</label>
                    <p class="text-[11px] text-slate-500">Pilih file baru jika ingin mengganti logo gambar saat ini.</p>
                    <input type="file" name="logo" accept="image/*" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-purple-600 file:text-white hover:file:bg-purple-700 cursor-pointer">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Status Tampil</label>
                        <select name="is_active" x-model="editData.is_active" class="w-full px-3 py-2 border border-slate-300 rounded-xl text-sm outline-none">
                            <option value="1">Aktif (Tampil)</option>
                            <option value="0">Nonaktif</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Warna Teks (Jika No Gambar)</label>
                        <select name="badge_color" x-model="editData.badge_color" class="w-full px-3 py-2 border border-slate-300 rounded-xl text-sm outline-none">
                            <option value="text-red-600">Merah</option>
                            <option value="text-blue-600">Biru</option>
                            <option value="text-emerald-600">Hijau</option>
                            <option value="text-amber-600">Amber</option>
                            <option value="text-purple-600">Ungu</option>
                            <option value="text-rose-700">Rose</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t">
                    <button type="button" @click="editModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm rounded-xl">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-purple-600 hover:bg-purple-700 text-white font-semibold text-sm rounded-xl shadow flex items-center gap-2">
                        <i class="fa-solid fa-check"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@section('scripts')
<style>[x-cloak] { display: none !important; }</style>
@endsection
@endsection
