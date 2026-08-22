<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Middleware ini mengecek apakah user yang login punya roles
     * yang diizinkan untuk mengakses halaman tertentu.
     *
     * Cara pakai di route:
     *   middleware('role:admin')         → hanya admin
     *   middleware('role:admin,staff')   → admin DAN staff boleh akses
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Cek apakah user sudah login
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // Cek apakah roles user ada di daftar roles yang diizinkan
        if (!in_array(auth()->user()->roles, $roles)) {
            // Kalau roles-nya gak cocok, tampilkan 403 Forbidden
            abort(403, 'Unauthorized. Kamu tidak punya akses ke halaman ini.');
        }

        return $next($request);
    }
}
