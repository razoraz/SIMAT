<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Tampilkan halaman Manajemen Pengguna (Master Users).
     */
    public function index(Request $request)
    {
        $currentUser = Auth::user();
        $users = User::orderByRaw("
            CASE 
                WHEN role = 'master_admin' THEN 1 
                WHEN role = 'admin' THEN 2 
                ELSE 3 
            END ASC
        ")->orderBy('id', 'asc')->get();

        // Ambil daftar seluruh 55 unit dari database atau array default
        $units = [];
        try {
            $mysqli = @new \mysqli("127.0.0.1", "root", "", "siprs_unit_pejabat");
            if (!$mysqli->connect_error) {
                $res = $mysqli->query("SELECT nama_unit FROM unit_penanggung_jawab ORDER BY id ASC");
                while ($row = $res->fetch_assoc()) {
                    $units[] = $row['nama_unit'];
                }
                $mysqli->close();
            }
        } catch (\Throwable $e) {}

        if (empty($units)) {
            $units = User::whereNotNull('unit')->pluck('unit')->unique()->values()->toArray();
        }

        return view('pages.master_users', compact('users', 'units', 'currentUser'));
    }

    /**
     * Simpan Pengguna / Sub-Admin Baru ke Database.
     */
    public function store(Request $request)
    {
        $currentUser = Auth::user();

        // Validasi Otorisasi
        if ($currentUser->role === 'admin' && $request->role !== 'sub_admin') {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Admin Operasional hanya diizinkan mendaftarkan akun Sub Admin (Kepala Unit/Ruangan).'
                ], 403);
            }
            return redirect()->back()->with('error', 'Admin Operasional hanya diizinkan mendaftarkan akun Sub Admin.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'nip' => 'nullable|string|max:50',
            'role' => ['required', Rule::in(['master_admin', 'admin', 'sub_admin'])],
            'unit' => 'nullable|string|max:150',
            'penugasan' => 'nullable|string|max:500',
            'status' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:4',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'nip' => $validated['nip'] ?? '-',
            'password' => Hash::make($request->filled('password') ? $request->password : 'rsud123'),
            'role' => $validated['role'],
            'unit' => $validated['unit'] ?? 'Semua Unit Paviliun',
            'penugasan' => $validated['penugasan'] ?? "Sub Admin & Penanggung Jawab {$validated['unit']}",
            'status' => $validated['status'] ?? 'Aktif',
            'deskripsi' => "Sub Admin {$validated['unit']}",
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Akun {$user->name} ({$user->role}) berhasil didaftarkan!",
                'user' => $user
            ]);
        }

        return redirect()->route('master.users')->with('success', "Akun {$user->name} berhasil didaftarkan.");
    }

    /**
     * Perbarui Data Pengguna.
     */
    public function update(Request $request, $id)
    {
        $currentUser = Auth::user();
        $user = User::findOrFail($id);

        // Validasi Otorisasi Edit
        if ($currentUser->role === 'admin') {
            if ($user->role === 'master_admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki hak akses untuk mengubah akun Master Admin.'
                ], 403);
            }
            if ($user->role === 'admin' && $user->id !== $currentUser->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda hanya dapat mengubah profil akun Admin Anda sendiri.'
                ], 403);
            }
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'nip' => 'nullable|string|max:50',
            'role' => ['required', Rule::in(['master_admin', 'admin', 'sub_admin'])],
            'unit' => 'nullable|string|max:150',
            'penugasan' => 'nullable|string|max:500',
            'status' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:4',
        ]);

        // Cegah Admin mengubah role menjadi master_admin
        if ($currentUser->role === 'admin') {
            $validated['role'] = $user->role; // pertahankan role asli
        }

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'nip' => $validated['nip'] ?? $user->nip,
            'role' => $validated['role'],
            'unit' => $validated['unit'] ?? $user->unit,
            'penugasan' => $validated['penugasan'] ?? $user->penugasan,
            'status' => $validated['status'] ?? $user->status,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Data pengguna {$user->name} berhasil diperbarui!",
                'user' => $user
            ]);
        }

        return redirect()->route('master.users')->with('success', "Data pengguna {$user->name} berhasil diperbarui.");
    }

    /**
     * Hapus Pengguna dari Database.
     */
    public function destroy(Request $request, $id)
    {
        $currentUser = Auth::user();
        $user = User::findOrFail($id);

        // Tidak boleh menghapus akun diri sendiri
        if ($user->id === $currentUser->id || $user->email === $currentUser->email) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif digunakan.'
            ], 403);
        }

        // Tidak boleh menghapus Master Admin
        if ($user->role === 'master_admin') {
            return response()->json([
                'success' => false,
                'message' => 'Akun Master Admin diproteksi dan tidak dapat dihapus.'
            ], 403);
        }

        // Admin tidak boleh menghapus sesama Admin
        if ($currentUser->role === 'admin' && $user->role === 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Admin Operasional tidak diizinkan menghapus akun Admin lainnya.'
            ], 403);
        }

        $nama = $user->name;
        $user->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Akun {$nama} berhasil dihapus dari sistem."
            ]);
        }

        return redirect()->route('master.users')->with('success', "Akun {$nama} berhasil dihapus.");
    }

    /**
     * Reset Password Pengguna ke Default ('rsud123').
     */
    public function resetPassword(Request $request, $id)
    {
        $currentUser = Auth::user();
        $user = User::findOrFail($id);

        if ($currentUser->role === 'admin' && $user->role === 'master_admin') {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak untuk mereset akun Master Admin.'
            ], 403);
        }

        $user->update([
            'password' => Hash::make('rsud123')
        ]);

        return response()->json([
            'success' => true,
            'message' => "Password akun {$user->name} berhasil direset menjadi 'rsud123'."
        ]);
    }
}
