<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Menampilkan halaman login
     */
    public function showLogin()
    {
        /**
         * Cek user apakah sudah login
         */
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user()->role);
        }
        return view('auth.login');
    }
    
    /**
     * Proses Login
     */
    public function login(Request $request)
    {
        /**
         * Validasi Input Login
         */
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->has('remember');
        $attemptCredentials = array_merge($credentials, ['is_deleted' => 0]);

        if (Auth::attempt($attemptCredentials, $remember)) {
            $request->session()->regenerate();
            $user = Auth::user();
            return $this->redirectBasedOnRole($user->role);
        }

        // Cek apakah akun terdaftar namun berstatus di Recycle Bin (is_deleted = 1)
        $deletedUser = \App\Models\User::onlyDeleted()->where('email', $credentials['email'])->first();
        if ($deletedUser) {
            return back()->withErrors([
                'email' => 'Akun pengguna ini sedang berada di Pusat Data Terhapus (Recycle Bin). Silakan hubungi Administrator atau Master Admin untuk memulihkan akun.',
            ])->onlyInput('email');
        }

        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan tidak sesuai.',
        ])->onlyInput('email');
    }
    
    /**
     * Proses Logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil logout.');
    }
    
    /**
     * Redirect berdasarkan role user
     */
    protected function redirectBasedOnRole(string $role)
    {
        return match ($role) {
            'master_admin' => redirect()->route('masteradmin.dashboard'),
            'admin' => redirect()->route('admin.dashboard'),
            'sub_admin' => redirect()->route('subadmin.dashboard'),
            default => redirect('/'),
        };
    }
}
