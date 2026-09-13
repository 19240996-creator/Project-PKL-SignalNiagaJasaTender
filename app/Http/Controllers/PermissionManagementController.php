<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PermissionManagementController extends Controller
{
    public function index(): View
    {
        return view('permissions.index', ['roles' => Role::with('permissions')->orderBy('name')->get(), 'permissions' => Permission::orderBy('name')->get()]);
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $validated = $request->validate(['permissions' => ['nullable', 'array'], 'permissions.*' => ['integer', 'exists:permissions,id']]);
        $role->permissions()->sync($validated['permissions'] ?? []);
        return back()->with('success', 'Permission role berhasil diperbarui.');
    }
}
