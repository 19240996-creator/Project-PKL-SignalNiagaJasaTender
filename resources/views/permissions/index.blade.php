@extends('layouts.app')

@section('title', 'Permission Role')
@section('header-title', 'Hak Akses Role (Permissions)')

@section('content')
<div class="space-y-5">
    <div>
        <h2 class="text-base font-bold text-slate-900 tracking-tight">Pengaturan Akses Granular Role</h2>
        <p class="text-xs text-slate-500">Tentukan izin akses per modul untuk masing-masing peran pengguna sistem (Admin, Manager, Owner).</p>
    </div>

    @foreach($roles as $role)
        <form method="POST" action="{{ route('permissions.update', $role) }}" class="bg-white border border-slate-200 rounded-lg p-5 shadow-xs">
            @csrf
            @method('PUT')
            
            <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3.5 border-b border-slate-100 gap-3">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">{{ ucwords(str_replace('_', ' ', $role->name)) }}</h3>
                    <p class="text-xs text-slate-500">{{ $role->description ?? 'Konfigurasi hak akses operasional untuk role ini.' }}</p>
                </div>
                <button type="submit" class="px-3.5 py-1.5 rounded-md bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium transition self-start sm:self-auto shadow-xs">
                    Simpan Akses Role
                </button>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2.5 pt-4">
                @foreach($permissions as $permission)
                    <label class="flex items-center gap-2.5 p-2.5 rounded-md bg-slate-50 border border-slate-200 text-xs cursor-pointer hover:bg-slate-100/70 transition">
                        <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" {{ $role->permissions->contains($permission->id) ? 'checked' : '' }} class="rounded text-blue-600 border-slate-300 focus:ring-blue-500">
                        <span class="font-medium text-slate-700">{{ $permission->name }}</span>
                    </label>
                @endforeach
            </div>
        </form>
    @endforeach
</div>
@endsection
