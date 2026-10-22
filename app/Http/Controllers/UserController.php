<?php

namespace App\Http\Controllers;

use App\Models\AccessAuditLog;
use App\Models\Unit;
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
        $users = User::active()->with(['unitModel', 'accessLogs.actor'])->orderByRaw("
            CASE 
                WHEN role = 'master_admin' THEN 1 
                WHEN role = 'admin' THEN 2 
                ELSE 3 
            END ASC
        ")->orderBy('id', 'asc')->get();

        // Ambil daftar seluruh unit dari tabel units untuk dropdown penugasan
        $units = Unit::active()->orderBy('nama')->get(['id', 'nama', 'tipe', 'kepala', 'nip']);
        $availablePermissions = User::AVAILABLE_PERMISSIONS;
        $rolePresets = User::ROLE_PRESETS;

        return view('pages.master.users.index', compact('users', 'units', 'currentUser', 'availablePermissions', 'rolePresets'));
    }

    /**
     * Simpan Pengguna Baru ke Database.
     */
    public function store(Request $request)
    {
        $currentUser = Auth::user();

        // Validasi Otorisasi
        if ($currentUser->role === 'admin') {
            if (!$currentUser->canDo('users', 'create')) {
                if ($request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Anda tidak memiliki hak akses untuk mendaftarkan akun pengguna baru.'
                    ], 403);
                }
                return redirect()->back()->with('error', 'Anda tidak memiliki hak akses untuk mendaftarkan akun pengguna baru.');
            }
            if ($request->role !== 'sub_admin') {
                if ($request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Admin Operasional hanya diizinkan mendaftarkan akun Sub Admin.'
                    ], 403);
                }
                return redirect()->back()->with('error', 'Admin Operasional hanya diizinkan mendaftarkan akun Sub Admin.');
            }
        }

        // Cek jika email sudah terdaftar di Recycle Bin
        if (User::onlyDeleted()->where('email', $request->email)->exists()) {
            $deletedMsg = "Email \"{$request->email}\" sudah terdaftar di Pusat Data Terhapus (Recycle Bin). Silakan pulihkan akun tersebut dari menu Recycle Bin atau gunakan email lain.";
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $deletedMsg], 422);
            }
            return redirect()->back()->withInput()->with('error', $deletedMsg);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'role' => ['required', Rule::in(['master_admin', 'admin', 'sub_admin'])],
            'penugasan' => 'nullable|string|max:500',
            'permissions' => 'nullable',
            'password' => 'nullable|string|min:4',
            'unit_id' => 'nullable|exists:units,id',
        ]);

        $permissions = null;
        if ($validated['role'] === 'admin') {
            $rawPermissions = $request->input('permissions');
            $permissions = User::normalizePermissions($rawPermissions);
            // Jika kosong, berikan preset data_entry
            if (empty($permissions)) {
                $permissions = User::getPresetPermissions('data_entry');
            }
        }

        $unitId = null;
        if ($validated['role'] === 'sub_admin' && !empty($request->unit_id)) {
            $unitId = (int) $request->unit_id;
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($request->filled('password') ? $request->password : 'rsud123'),
            'role' => $validated['role'],
            'unit_id' => $unitId,
            'penugasan' => $validated['penugasan'] ?? 'Pengguna Sistem SIMAT',
            'permissions' => $permissions,
            'status' => 'Aktif',
            'deskripsi' => $validated['penugasan'] ?? '',
        ]);

        // Catat Audit Trail Pendaftaran Akun Pengguna
        $user->load('unitModel');
        $unitSummary = ($user->role === 'sub_admin' && $user->unitModel) ? " (Unit: {$user->unitModel->nama})" : '';
        AccessAuditLog::create([
            'user_id' => $user->id,
            'actor_id' => $currentUser->id,
            'action_type' => 'CREATE_USER',
            'summary' => "Pendaftaran akun baru {$user->name} dengan role {$user->role}{$unitSummary}",
            'old_permissions' => null,
            'new_permissions' => $permissions,
            'old_role' => null,
            'new_role' => $user->role,
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 500),
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Akun {$user->name} ({$user->role}) berhasil didaftarkan!",
                'user' => $user->load(['unitModel', 'accessLogs.actor'])
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
            if ($user->id !== $currentUser->id && !$currentUser->canDo('users', 'edit')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki hak akses untuk mengubah akun pengguna lain.'
                ], 403);
            }
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
            'role' => ['required', Rule::in(['master_admin', 'admin', 'sub_admin'])],
            'penugasan' => 'nullable|string|max:500',
            'permissions' => 'nullable',
            'password' => 'nullable|string|min:4',
            'unit_id' => 'nullable|exists:units,id',
        ]);

        // Simpan snapshot nilai lama sebelum pembaruan untuk Audit Trail
        $oldRole = $user->role;
        $oldPermissions = $user->permissions;
        $oldUnitId = $user->unit_id;

        // Cegah Admin mengubah role menjadi master_admin
        if ($currentUser->role === 'admin') {
            $validated['role'] = $user->role;
        }

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'penugasan' => $validated['penugasan'] ?? $user->penugasan,
            'status' => 'Aktif',
        ];

        // Pengaturan penugasan unit
        if ($validated['role'] === 'sub_admin') {
            $updateData['unit_id'] = !empty($request->unit_id) ? (int) $request->unit_id : null;
        } else {
            $updateData['unit_id'] = null;
        }

        // Hanya Master Admin yang berwenang mengubah daftar permissions akun Admin
        if ($currentUser->role === 'master_admin') {
            if ($validated['role'] === 'admin') {
                $updateData['permissions'] = User::normalizePermissions($request->input('permissions'));
            } else {
                $updateData['permissions'] = null;
            }
        }

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        // Deteksi Perubahan Signifikan & Catat ke Audit Trail
        $changes = [];
        $actionType = 'UPDATE_PROFILE';

        if ($oldRole !== $user->role) {
            $changes[] = "Role diubah dari '{$oldRole}' menjadi '{$user->role}'";
            $actionType = 'ROLE_CHANGE';
        }

        // Periksa apakah hak akses (permissions) berubah
        $oldPermsJson = json_encode($oldPermissions ?? []);
        $newPermsJson = json_encode($user->permissions ?? []);
        if ($oldPermsJson !== $newPermsJson) {
            $changes[] = "Matriks wewenang hak akses modul diperbarui";
            $actionType = ($actionType === 'ROLE_CHANGE') ? 'ROLE_AND_PERMISSION' : 'PERMISSION_UPDATE';
        }

        if ($request->filled('password')) {
            $changes[] = "Password akun diperbarui";
            if ($actionType === 'UPDATE_PROFILE') {
                $actionType = 'PASSWORD_CHANGE';
            }
        }

        if ($oldUnitId !== $user->unit_id) {
            $user->load('unitModel');
            $unitName = $user->unitModel?->nama ?? 'Tanpa Unit';
            $changes[] = "Penugasan unit diubah ke {$unitName}";
            if ($actionType === 'UPDATE_PROFILE') {
                $actionType = 'UNIT_ASSIGNMENT';
            }
        }

        $summary = !empty($changes)
            ? implode('; ', $changes)
            : "Pembaruan informasi profil akun ({$user->name})";

        AccessAuditLog::create([
            'user_id' => $user->id,
            'actor_id' => $currentUser->id,
            'action_type' => $actionType,
            'summary' => $summary,
            'old_permissions' => $oldPermissions,
            'new_permissions' => $user->permissions,
            'old_role' => $oldRole,
            'new_role' => $user->role,
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 500),
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Data pengguna {$user->name} berhasil diperbarui!",
                'user' => $user->load(['unitModel', 'accessLogs.actor'])
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
        if ($user->id === $currentUser->id) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif digunakan.'
                ], 400);
            }
            return redirect()->back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        // Admin dilarang menghapus sesama admin atau master admin & harus memiliki izin users.delete
        if ($currentUser->role === 'admin') {
            if (!$currentUser->canDo('users', 'delete')) {
                if ($request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Anda tidak memiliki hak akses untuk menghapus akun pengguna.'
                    ], 403);
                }
                return redirect()->back()->with('error', 'Anda tidak memiliki hak akses untuk menghapus akun pengguna.');
            }
            if (in_array($user->role, ['admin', 'master_admin'])) {
                if ($request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Admin Operasional hanya diizinkan menghapus akun Sub Admin.'
                    ], 403);
                }
                return redirect()->back()->with('error', 'Anda tidak memiliki hak akses menghapus akun tersebut.');
            }
        }

        // Catat Audit Trail Penghapusan Akun Pengguna
        AccessAuditLog::create([
            'user_id' => $user->id,
            'actor_id' => $currentUser->id,
            'action_type' => 'DELETE_USER',
            'summary' => "Penghapusan akun {$user->name} ({$user->role}) ke Pusat Data Terhapus (Recycle Bin)",
            'old_permissions' => $user->permissions,
            'new_permissions' => null,
            'old_role' => $user->role,
            'new_role' => null,
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 500),
        ]);

        $userName = $user->name;
        $user->softDelete();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Akun {$userName} berhasil dipindahkan ke Pusat Data Terhapus (Recycle Bin)."
            ]);
        }

        return redirect()->route('master.users')->with('success', "Akun {$userName} berhasil dipindahkan ke Pusat Data Terhapus (Recycle Bin).");
    }

    /**
     * Reset Password Akun Pengguna ke default 'rsud123'.
     */
    public function resetPassword(Request $request, $id)
    {
        $currentUser = Auth::user();
        $user = User::findOrFail($id);

        if ($currentUser->role === 'admin' && $user->role === 'master_admin') {
            return response()->json([
                'success' => false,
                'message' => 'Admin tidak dapat mereset password Master Admin.'
            ], 403);
        }

        $user->update([
            'password' => Hash::make('rsud123')
        ]);

        // Catat Audit Trail Reset Password
        AccessAuditLog::create([
            'user_id' => $user->id,
            'actor_id' => $currentUser->id,
            'action_type' => 'RESET_PASSWORD',
            'summary' => "Reset password akun {$user->name} ke default ('rsud123') oleh {$currentUser->name}",
            'old_permissions' => $user->permissions,
            'new_permissions' => $user->permissions,
            'old_role' => $user->role,
            'new_role' => $user->role,
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 500),
        ]);

        return response()->json([
            'success' => true,
            'message' => "Password akun {$user->name} berhasil direset ke default ('rsud123').",
            'user' => $user->load(['unitModel', 'accessLogs.actor'])
        ]);
    }
}

