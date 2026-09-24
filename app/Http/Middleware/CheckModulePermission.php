<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckModulePermission
{
    /**
     * Handle an incoming request and verify module permissions.
     */
    public function handle(Request $request, Closure $next, string $module): Response
    {
        if (!Auth::check()) {
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Sesi login telah berakhir. Silakan login kembali.'], 401);
            }
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $user = Auth::user();

        // 1. Master Admin selalu memiliki wewenang penuh
        if ($user->role === 'master_admin') {
            return $next($request);
        }

        $moduleList = array_map('trim', explode(',', $module));

        // 2. Admin Operasional: periksa izin modul
        if ($user->role === 'admin') {
            foreach ($moduleList as $mod) {
                if ($user->canAccess($mod)) {
                    return $next($request);
                }
            }

            $moduleLabels = [
                'astap'       => 'Data ASTAP & Kode 108',
                'distribusi'  => 'Distribusi ASTAP',
                'bast'        => 'Berita Acara (BAST)',
                'mutasi'      => 'Mutasi Aset',
                'unit'        => 'Unit & Paviliun',
                'master_data' => 'Master Data SIPD',
                'users'       => 'Manajemen Pengguna',
            ];
            $modName = $moduleLabels[$moduleList[0]] ?? strtoupper($moduleList[0]);

            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => "Akses Ditolak: Anda tidak memiliki wewenang untuk modul {$modName}."
                ], 403);
            }

            abort(403, "Akses Ditolak: Akun Anda tidak memiliki wewenang untuk mengakses modul {$modName}. Silakan hubungi Master Admin untuk mendapatkan hak akses.");
        }

        // 3. Sub Admin: hanya modul tertentu yang diizinkan untuk ruangan (misal: kir, pengajuan distribusi, mutasi ruangan)
        $isRestricted = true;
        foreach ($moduleList as $mod) {
            if (!in_array($mod, ['unit', 'master_data', 'users', 'bast'])) {
                $isRestricted = false;
                break;
            }
        }
        if ($isRestricted) {
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses Ditolak: Modul ini hanya untuk staf Admin Pusat.'
                ], 403);
            }
            abort(403, 'Akses Ditolak: Halaman ini hanya untuk wewenang Admin Operasional Pusat.');
        }

        return $next($request);
    }
}
