<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Cek Role Middleware
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        /**
         * Cek User apakah sudah login atau belum
         */
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        /**
         * Ambil data user yang login
         */
        $user = Auth::user();

        /**
         * Cek Role User apakah sesuai
         */
        if (!in_array($user->role, $roles)) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki wewenang untuk mengakses halaman ini.');
        }

        /**
         * Jika user login dan role sesuai, lanjutkan akses
         */
        return $next($request);
    }
}
