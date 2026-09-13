<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        if (!$request->user()) return redirect()->route('login');
        if ($request->user()->role?->name === 'super_admin' || collect($permissions)->contains(fn ($permission) => $request->user()->hasPermission($permission))) return $next($request);
        if ($request->wantsJson()) return response()->json(['message' => 'Permission ditolak.'], 403);
        return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki permission untuk aksi ini.');
    }
}
