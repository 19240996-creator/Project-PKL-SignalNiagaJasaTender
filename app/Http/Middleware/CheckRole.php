<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $userRole = Auth::user()->role->name ?? '';

        // Super Admin has full access to everything
        if ($userRole === 'super_admin') {
            return $next($request);
        }

        // Check if user's role is allowed
        if (in_array($userRole, $roles)) {
            return $next($request);
        }

        // Otherwise block access
        if ($request->wantsJson()) {
            return response()->json(['message' => 'Anda tidak memiliki hak akses untuk membuka modul ini.'], 403);
        }

        return redirect()->route('dashboard')->with('error', 'Akses ditolak! Peran Anda (' . ucwords(str_replace('_', ' ', $userRole)) . ') tidak memiliki wewenang membuka modul tersebut.');
    }
}
