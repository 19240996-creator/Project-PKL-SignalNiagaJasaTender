@extends('layouts.app')
@section('title', 'Permission Role')
@section('header-title', 'Permission Role')
@section('content')
<div class="space-y-5"><p class="text-sm text-slate-500">Atur akses granular untuk setiap role. Super Admin selalu memiliki seluruh akses.</p>@foreach($roles as $role)<form method="POST" action="{{ route('permissions.update', $role) }}" class="bg-white border rounded-2xl p-5 shadow-sm">@csrf @method('PUT')<div class="flex items-center justify-between mb-4"><div><h2 class="font-bold">{{ ucwords(str_replace('_', ' ', $role->name)) }}</h2><p class="text-xs text-slate-500">{{ $role->description }}</p></div><button class="px-3 py-2 rounded-xl bg-blue-600 text-white text-xs font-semibold">Simpan Akses</button></div><div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-3">@foreach($permissions as $permission)<label class="flex items-center gap-2 p-3 rounded-xl bg-slate-50 border text-sm"><input type="checkbox" name="permissions[]" value="{{ $permission->id }}" {{ $role->permissions->contains($permission->id) ? 'checked' : '' }} class="rounded text-blue-600"><span>{{ $permission->name }}</span></label>@endforeach</div></form>@endforeach</div>
@endsection
