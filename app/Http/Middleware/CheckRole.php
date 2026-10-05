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
     * Hanya 3 role: owner, manager, admin
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $userRole = Auth::user()->role->name ?? '';

        $allowed = [];
        foreach ($roles as $r) {
            foreach (explode(',', $r) as $sub) {
                if (filled($sub)) {
                    $allowed[] = trim($sub);
                }
            }
        }

        // Check if user's role is in the allowed list
        if (in_array($userRole, $allowed, true)) {
            return $next($request);
        }

        // Otherwise block access
        if ($request->wantsJson()) {
            return response()->json(['message' => 'Anda tidak memiliki hak akses untuk membuka modul ini.'], 403);
        }

        return redirect()->route('dashboard')->with('error', 'Akses ditolak! Peran Anda (' . ucwords($userRole) . ') tidak memiliki wewenang membuka modul tersebut.');
    }
}
