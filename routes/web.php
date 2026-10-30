<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\JenisAstapController;
use App\Http\Controllers\JenisPengadaanController;
use App\Http\Controllers\RekeningBelanjaController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\DistribusiController;
use App\Http\Controllers\MutasiController;
use App\Http\Controllers\RecycleBinController;
use App\Http\Controllers\AstapController;
use App\Http\Middleware\RoleMiddleware;

// Auth Routes (Guest)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Halaman Publik Scan QR Code Aset (Tanpa Perlu Login)
Route::get('/scan/{nibar}', function ($nibar) {
    $register = \App\Models\AstapRegister::where('nibar', $nibar)
        ->orWhere('no_register', $nibar)
        ->first();

    $astap = null;
    if ($register) {
        $astap = \App\Models\Astap::with('jenisAstap', 'jenisPengadaan', 'rekeningBelanja', 'registers')->find($register->astap_id);
    } else {
        $astap = \App\Models\Astap::with('jenisAstap', 'jenisPengadaan', 'rekeningBelanja', 'registers')
            ->where('kode_barang', $nibar)
            ->first();
    }

    return view('pages.public_scan', [
        'found' => ($astap !== null || $register !== null),
        'nibar' => $nibar,
        'register' => $register,
        'astap' => $astap
    ]);
})->name('scan.nibar');

// Halaman Publik Validasi Sertifikat TTE BSrE (Tanpa Perlu Login)
Route::get('/validasi-tte/{hash}', function ($hash) {
    $judul = 'Berita Acara Serah Terima Barang';
    $nomor = '000.2.3.2/224/430.10.7/2026';
    $nama = 'BUDI HARTONO, S.Sos';
    $nip = '19760229 200801 1 010';
    $jabatan = 'Pengurus Barang Aset Pada RSUD dr. H. Koesnandi Kabupaten Bondowoso';
    $tgl = date('d/m/Y H:i') . ' WIB';

    // 1. Cek tabel BAST Triwulan
    $tw = \App\Models\AstapBastTriwulan::where('qr_hash', $hash)->orWhere('nomor_surat', $hash)->first();
    if ($tw) {
        $judul = 'Berita Acara Serah Terima Barang (' . $tw->triwulan . ')';
        $nomor = $tw->nomor_surat;
        $nama = $tw->pihak2_nama ?: 'BUDI HARTONO, S.Sos';
        $nip = $tw->pihak2_nip ?: '19760229 200801 1 010';
        $jabatan = $tw->pihak2_jabatan ?: 'Pengurus Barang Aset Pada RSUD dr. H. Koesnandi';
        $tgl = $tw->tgl_signed ?: ($tw->tanggal_bast ? date('d/m/Y', strtotime($tw->tanggal_bast)) . ' WIB' : date('d/m/Y H:i') . ' WIB');
    }

    // 2. Cek tabel Distribusi
    $dst = \App\Models\Distribusi::where('kode', $hash)->orWhere('bast_nomor', $hash)->first();
    if ($dst) {
        $judul = 'Berita Acara Serah Terima Distribusi Aset';
        $nomor = $dst->bast_nomor ?: ($dst->kode . ' / BAST / 430.10.7 / 2026');
        $nama = 'BUDI HARTONO, S.Sos';
        $nip = '19760229 200801 1 010';
        $jabatan = 'Pengurus Barang Aset (Instalasi Perbekalan) RSUD dr. H. Koesnandi';
        $tgl = $dst->tgl_signed ?: ($dst->tanggal_distribusi ? date('d/m/Y', strtotime($dst->tanggal_distribusi)) . ' WIB' : date('d/m/Y H:i') . ' WIB');
    }

    // 3. Cek tabel Mutasi
    $mts = \App\Models\AstapMutasi::where('nomor_bamb', $hash)->where('is_deleted', 0)->first();
    if ($mts) {
        $judul = 'Berita Acara Mutasi Barang (BAMB)';
        $nomor = $mts->nomor_bamb;
        $nama = $mts->penanggung_jawab_asal ?: 'Kepala Ruangan Pengirim';
        $nip = '-';
        $jabatan = 'Penanggung Jawab Ruangan ' . ($mts->ruangan_asal ?? '');
        $tgl = $mts->tgl_persetujuan_admin ?: ($mts->tanggal_mutasi ? date('d/m/Y', strtotime($mts->tanggal_mutasi)) . ' WIB' : date('d/m/Y H:i') . ' WIB');
    }

    // 4. Cek tabel Mutasi Eksternal (Transfer OPD)
    $ext = \App\Models\MutasiEksternal::where(function($q) use ($hash) {
        $q->where('qr_hash', $hash)->orWhere('nomor_bamb', $hash);
    })->where('is_deleted', 0)->first();
    if ($ext) {
        $judul = $ext->tipe === 'keluar' ? 'Berita Acara Serah Terima (BAST) Mutasi Keluar OPD' : 'Berita Acara Serah Terima (BAST) Mutasi Masuk OPD';
        $nomor = $ext->nomor_bamb ?: ('BAST-EXT-' . $ext->id);
        if ($ext->tipe === 'keluar') {
            $nama = $ext->pj_asal_nama ?: 'dr. H. Yus Priyatna, Sp.P';
            $nip = $ext->pj_asal_nip ?: '196904121999031004';
            $jabatan = $ext->pj_asal_jabatan ?: 'Direktur RSUD Dr. H. Koesnandi';
        } else {
            $nama = $ext->pj_tujuan_nama ?: 'BUDI HARTONO, S.Sos';
            $nip = $ext->pj_tujuan_nip ?: '19760229 200801 1 010';
            $jabatan = $ext->pj_tujuan_jabatan ?: 'Pengurus Barang / PPK RSUD Dr. H. Koesnandi';
        }
        $tgl = $ext->tgl_signed ?: ($ext->tanggal_mutasi ? date('d/m/Y', strtotime($ext->tanggal_mutasi)) . ' WIB' : date('d/m/Y H:i') . ' WIB');
    }

    // 5. Cek tabel Kemitraan (Akun 1.5.2)
    $kmt = \App\Models\AstapKemitraan::where(function($q) use ($hash) {
        $q->where('nomor_pks', $hash)
          ->orWhere('id', $hash);
    })->where('is_deleted', 0)->first();
    if ($kmt) {
        $judul = 'Berita Acara Serah Terima (BAST) Pemanfaatan BMD Kemitraan';
        $nomor = $kmt->nomor_pks ?: ('BAST-KMT-' . $kmt->id);
        $nama = 'BUDI HARTONO, S.Sos';
        $nip = '19760229 200801 1 010';
        $jabatan = 'Pengurus Barang Pengguna RSUD Dr. H. Koesnandi';
        $tgl = $kmt->tanggal_pks ? date('d/m/Y', strtotime($kmt->tanggal_pks)) . ' WIB' : date('d/m/Y H:i') . ' WIB';
    } elseif (str_contains(strtoupper($hash), 'KMT') || str_contains(strtoupper($hash), 'KEMITRAAN')) {
        $judul = 'Berita Acara Serah Terima (BAST) Pemanfaatan BMD Kemitraan';
        $nomor = str_replace('-', '/', $hash);
        $nama = 'BUDI HARTONO, S.Sos';
        $nip = '19760229 200801 1 010';
        $jabatan = 'Pengurus Barang Pengguna RSUD Dr. H. Koesnandi';
        $tgl = date('d/m/Y H:i') . ' WIB';
    }

    // Fallback parser jika hash mengandung kata kunci PPK
    if (str_contains($hash, 'PPK')) {
        $nama = 'dr. YUS PRIYATNA ADRYANTO, Sp.P, FISR';
        $nip = '19771002 200604 1 006';
        $jabatan = 'Pejabat Pembuat Komitmen (PPK) RSUD dr. H. Koesnandi';
    }

    return view('pages.public_tte_verify', [
        'qrHash'       => $hash,
        'judulDokumen' => $judul,
        'nomorSurat'   => $nomor,
        'signerNama'   => $nama,
        'signerNip'    => $nip,
        'signerJabatan'=> $jabatan,
        'tglSigned'    => $tgl,
    ]);
})->where('hash', '.*')->name('tte.validate');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Rute Impersonasi Akun (Khusus Master Admin)
Route::post('/impersonate/{id}', [AuthController::class, 'impersonate'])->name('impersonate.start')->middleware(['auth', 'role:master_admin']);
Route::post('/impersonate-leave', [AuthController::class, 'leaveImpersonate'])->name('impersonate.leave')->middleware('auth');

// Redirect / or /dashboard to specific role dashboard
Route::middleware('auth')->get('/', function () {
    $role = Auth::user()->role;
    return match ($role) {
        'master_admin' => redirect()->route('masteradmin.dashboard'),
        'admin' => redirect()->route('admin.dashboard'),
        'sub_admin' => redirect()->route('subadmin.dashboard'),
        default => redirect()->route('login'),
    };
});

Route::middleware('auth')->get('/dashboard', function () {
    $role = Auth::user()->role;
    return match ($role) {
        'master_admin' => redirect()->route('masteradmin.dashboard'),
        'admin' => redirect()->route('admin.dashboard'),
        'sub_admin' => redirect()->route('subadmin.dashboard'),
        default => redirect()->route('login'),
    };
});

use App\Http\Controllers\DashboardController;

// Dashboard Master Admin
Route::middleware(['auth', RoleMiddleware::class . ':master_admin'])->group(function () {
    Route::get('/master-admin/dashboard', [DashboardController::class, 'masterAdmin'])->name('masteradmin.dashboard');
});

// Dashboard Admin
Route::middleware(['auth', RoleMiddleware::class . ':admin'])->group(function () {
    Route::get('/admin/dashboard', [DashboardController::class, 'admin'])->name('admin.dashboard');
});

// Dashboard Sub Admin
Route::middleware(['auth', RoleMiddleware::class . ':sub_admin'])->group(function () {
    Route::get('/sub-admin/dashboard', [DashboardController::class, 'subAdmin'])->name('subadmin.dashboard');

    // Halaman Ubah Email & Password Akun Sub Admin
    Route::get('/sub-admin/profile', function () {
        return view('pages.subadmin_profile');
    })->name('subadmin.profile');

    // Update Profil Akun Sub Admin (Email & Password - tersinkronisasi ke tabel units & users)
    Route::post('/sub-admin/profile/update', function (\Illuminate\Http\Request $request) {
        $user = \Illuminate\Support\Facades\Auth::user();
        $changeType = $request->input('change_type', 'both');
        
        $rules = [];
        $customMessages = [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah digunakan oleh akun lain.',
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password minimal terdiri dari 4 karakter.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ];

        if ($changeType === 'email' || $changeType === 'both') {
            $rules['email'] = [
                'required', 
                'email', 
                'max:255', 
                \Illuminate\Validation\Rule::unique('users', 'email')->ignore($user->id)
            ];
        }

        if ($changeType === 'password') {
            $rules['password'] = 'required|string|min:4|confirmed';
        } elseif ($changeType === 'both') {
            $rules['password'] = 'nullable|string|min:4|confirmed';
        }

        $validated = $request->validate($rules, $customMessages);

        // 1. Update Akun Pengguna di tabel `users`
        if (isset($validated['email'])) {
            $user->email = $validated['email'];
        }
        if (!empty($validated['password'])) {
            $user->password = \Illuminate\Support\Facades\Hash::make($validated['password']);
        }
        $user->save();

        // 2. Sinkronkan email ke tabel `units` pada ruangan milik Sub Admin ini jika email diubah
        if (isset($validated['email']) && $user->unit_id) {
            $unit = \App\Models\Unit::find($user->unit_id);
            if ($unit) {
                // Update langsung tanpa trigger loop event
                $unit->withoutEvents(function () use ($unit, $validated) {
                    $unit->update([
                        'email' => $validated['email']
                    ]);
                });
            }
        }

        $msg = match($changeType) {
            'email' => 'Alamat email berhasil diperbarui dan tersinkronisasi ke data unit ruangan!',
            'password' => 'Password akun ruangan berhasil diperbarui!',
            default => 'Perubahan kredensial akun ruangan berhasil disimpan!',
        };

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $msg,
                'email' => $user->email,
            ]);
        }

        return redirect()->back()->with('success', $msg);
    })->name('subadmin.profile.update');
});

// Frontend Menu & Form Pages (Auth Protected)
Route::middleware('auth')->group(function () {
    
    // 1. Data ASTAP Pages
    Route::get('/astap', [AstapController::class, 'index'])->name('astap.index')->middleware('module:astap');



    // 2. Distribusi Pages & Forms
    Route::middleware('module:distribusi')->group(function () {
        Route::get('/distribusi', [DistribusiController::class, 'index'])->name('distribusi.index');
        Route::get('/distribusi/create', [DistribusiController::class, 'create'])->name('distribusi.create');
        Route::get('/distribusi/next-bast', [DistribusiController::class, 'getNextBast'])->name('distribusi.next-bast');
        Route::get('/distribusi/{id}/edit', [DistribusiController::class, 'edit'])->name('distribusi.edit');
        Route::post('/distribusi/save', [DistribusiController::class, 'saveDistribusi'])->name('distribusi.save');
        Route::post('/distribusi', [DistribusiController::class, 'saveDistribusi'])->name('distribusi.store');
        Route::put('/distribusi/{id}', [DistribusiController::class, 'saveDistribusi'])->name('distribusi.update');
        Route::delete('/distribusi/{id}', [DistribusiController::class, 'destroy'])->name('distribusi.destroy')->middleware('module:distribusi.delete');
        Route::post('/distribusi/{id}/restore', [DistribusiController::class, 'restore'])->name('distribusi.restore');
    });

    // API: Update Status Distribusi (misal: Sub Admin menandai Barang Diterima / Admin menolak)
    Route::patch('/distribusi/{id}/status', [DistribusiController::class, 'updateStatus'])->name('distribusi.status.update');

    // API: Toggle Status TTD BSrE Distribusi (simpan ke database agar persist setelah reload)
    Route::patch('/distribusi/{id}/sign', [DistribusiController::class, 'sign'])->name('distribusi.sign');

    // API: Update kondisi per Register NIBAR (dari halaman distribusi — semua role terautentikasi)
    Route::patch('/distribusi/register-kondisi/{id}', [DistribusiController::class, 'updateRegisterKondisi'])->name('distribusi.register_kondisi.update');

    // API: Ambil kondisi terkini satu astap_register dari DB (untuk refresh realtime)
    Route::get('/distribusi/register-kondisi/{id}', [DistribusiController::class, 'showRegisterKondisi'])->name('distribusi.register_kondisi.show');

    // API: Ambil riwayat komprehensif satu unit register NIBAR (Mutasi Internal, Mutasi Eksternal, Reklasifikasi)
    Route::get('/astap/register-mutasi/{id}', [AstapController::class, 'getRegisterMutasi'])->name('astap.register_mutasi');

    // 4. Mutasi Aset Pages & Forms
    Route::middleware('module:mutasi')->group(function () {
        Route::get('/mutasi-aset',                 [MutasiController::class, 'index'])->name('mutasi.index');
        Route::get('/mutasi-aset/eksternal',        [\App\Http\Controllers\MutasiEksternalController::class, 'index'])->name('mutasi.eksternal')->middleware(['role:master_admin,admin', 'module:mutasi.eksternal']);
        Route::get('/mutasi-aset/create',          [MutasiController::class, 'create'])->name('mutasi.create');
        Route::post('/mutasi-aset',                [MutasiController::class, 'store'])->name('mutasi.store');
        Route::get('/mutasi-aset/{id}/edit',       [MutasiController::class, 'edit'])->name('mutasi.edit');
        Route::put('/mutasi-aset/{id}',            [MutasiController::class, 'update'])->name('mutasi.update');
        Route::delete('/mutasi-aset/{id}',         [MutasiController::class, 'destroy'])->name('mutasi.destroy')->middleware('module:mutasi.delete');
        Route::post('/mutasi-aset/{id}/restore',    [MutasiController::class, 'restore'])->name('mutasi.restore');
        Route::post('/mutasi-aset/{id}/approve-pengirim', [MutasiController::class, 'approvePengirim'])->name('mutasi.approve.pengirim');
        Route::post('/mutasi-aset/{id}/approve-penerima', [MutasiController::class, 'approvePenerima'])->name('mutasi.approve.penerima');
        Route::post('/mutasi-aset/{id}/approve-admin',    [MutasiController::class, 'approveAdmin'])->name('mutasi.approve.admin')->middleware('module:mutasi.approve');
        Route::post('/mutasi-aset/{id}/reject',           [MutasiController::class, 'reject'])->name('mutasi.reject');
        Route::post('/mutasi-aset/{id}/cancel-reject',    [MutasiController::class, 'cancelReject'])->name('mutasi.cancel.reject');
        Route::get('/mutasi-aset/register/{id}',          [MutasiController::class, 'getRegisterData'])->name('mutasi.register.data');
    });

    // 4b. Pusat Pemulihan Data (Recycle Bin Center Terpadu SIMAT)
    Route::get('/recycle-bin',                                     [RecycleBinController::class, 'index'])->name('recycle_bin.index')->middleware('module:recycle_bin.view');
    Route::post('/recycle-bin/{module}/{id}/restore',              [RecycleBinController::class, 'restore'])->name('recycle_bin.restore')->middleware('module:recycle_bin.restore');
    Route::post('/recycle-bin/{module}/bulk-restore',              [RecycleBinController::class, 'bulkRestore'])->name('recycle_bin.bulk_restore')->middleware('module:recycle_bin.restore');
    Route::post('/recycle-bin/{module}/bulk-force-delete',         [RecycleBinController::class, 'bulkForceDelete'])->name('recycle_bin.bulk_force_delete')->middleware('module:recycle_bin.force_delete');
    Route::delete('/recycle-bin/{module}/{id}/force-delete',       [RecycleBinController::class, 'forceDelete'])->name('recycle_bin.force_delete')->middleware('module:recycle_bin.force_delete');

    // 4c. Rekonsiliasi Belanja Modal (RMB) - Format Resmi BPKAD
    Route::get('/audit-pemulihan/rmb', [\App\Http\Controllers\RmbController::class, 'index'])->name('rmb.index');

    // 4d. Audit & Ledger Koreksi Nilai BMD (Biasa, LKD, Manset)
    Route::get('/audit-pemulihan/koreksi', [\App\Http\Controllers\AuditKoreksiController::class, 'index'])->name('audit_koreksi.index');
    Route::get('/audit-pemulihan/koreksi/export', [\App\Http\Controllers\AuditKoreksiController::class, 'export'])->name('audit_koreksi.export');
    Route::get('/audit-pemulihan/koreksi/{id}', [\App\Http\Controllers\AuditKoreksiController::class, 'show'])->name('audit_koreksi.show');

    // 5. Unit & Paviliun Index
    Route::get('/unit-paviliun', [UnitController::class, 'index'])->name('unit.index')->middleware('module:unit');

    // 5b. Halaman Khusus Lembar Kartu Inventaris Ruangan (KIR)
    Route::get('/lembar-kir-ruangan', [UnitController::class, 'kir'])->name('kir.index');
    Route::patch('/lembar-kir-ruangan/kondisi/{id}', [UnitController::class, 'updateKondisi'])->name('kir.update_kondisi');

    // 6. Pemeliharaan Index (Dinonaktifkan sementara karena masih dalam pengembangan)
    // Route::get('/pemeliharaan', function () {
    //     return view('pages.pemeliharaan');
    // })->name('pemeliharaan.index');

    // 7. API Notifikasi Sistem
    Route::post('/api/notifications/mark-all-read', function () {
        \App\Services\NotificationService::markAllAsReadForUser(auth()->user());
        return response()->json(['success' => true, 'message' => 'Semua notifikasi telah ditandai sebagai dibaca.']);
    })->name('notifications.mark_all_read');

    Route::get('/api/notifications/list', function () {
        $res = \App\Services\NotificationService::getForUser(auth()->user());
        return response()->json([
            'success'       => true,
            'unread_count'  => $res['unread_count'],
            'notifications' => $res['notifications'],
        ]);
    })->name('notifications.list');

    // 8. API Rekanan / Mitra Kemitraan (Autofill & Filter)
    Route::get('/api/kemitraan/mitras', function () {
        return response()->json([
            'success' => true,
            'data'    => \App\Models\AstapKemitraan::getDistinctMitras(),
        ]);
    })->name('api.kemitraan.mitras');

    // 9. Manajemen Halaman & API Tutup Buku BMD (Penutupan Rekon BPKAD)
    Route::get('/tutup-buku', [\App\Http\Controllers\TutupBukuController::class, 'index'])->name('tutup_buku.index');
    Route::get('/api/tutup-buku/status', [\App\Http\Controllers\TutupBukuController::class, 'status'])->name('tutup_buku.status');
    Route::post('/api/tutup-buku/lock', [\App\Http\Controllers\TutupBukuController::class, 'lock'])->name('tutup_buku.lock');
    Route::post('/api/tutup-buku/unlock', [\App\Http\Controllers\TutupBukuController::class, 'unlock'])->name('tutup_buku.unlock');

    // Rute Khusus Master Admin & Admin Operasional (Sub Admin Dibatasi)
    Route::middleware([RoleMiddleware::class . ':master_admin,admin'])->group(function () {
        // Berita Acara (BAST)
        Route::middleware('module:bast')->group(function () {
            Route::get('/berita-acara', [\App\Http\Controllers\BeritaAcaraController::class, 'index'])->name('bast.index');
            Route::post('/berita-acara/triwulan/{key}', [\App\Http\Controllers\BeritaAcaraController::class, 'saveTriwulan'])->name('bast.save_triwulan');
            Route::post('/berita-acara/triwulan/{key}/sign', [\App\Http\Controllers\BeritaAcaraController::class, 'signTriwulan'])->name('bast.sign_triwulan')->middleware('module:bast.tte');
            Route::post('/berita-acara/mutasi-eksternal/{id}/sign', [\App\Http\Controllers\BeritaAcaraController::class, 'signMutasiEksternal'])->name('bast.sign_mutasi_eksternal')->middleware('module:bast.tte');
        });

        // Form Tambah, Edit, Simpan, Update & Hapus ASTAP
        Route::middleware('module:astap')->group(function () {
            // Helper pengambilan data unik Penyedia & PPK dari riwayat data ASTAP
            $getDistinctPenyedias = fn() => AstapController::getDistinctPenyedias();
            $getDistinctPejabats = fn() => AstapController::getDistinctPejabats();

            $getDistinctMitras = function() {
                return \App\Models\AstapKemitraan::whereNotNull('mitra_nama')
                    ->where('mitra_nama', '!=', '')
                    ->where('is_deleted', 0)
                    ->orderBy('id', 'desc')
                    ->get(['id', 'mitra_nama', 'mitra_pimpinan', 'mitra_alamat', 'created_at'])
                    ->groupBy(fn($item) => strtolower(trim($item->mitra_nama)))
                    ->map(function($group) {
                        $latest = $group->first();
                        $companyName = trim($latest->mitra_nama);

                        // Ambil seluruh riwayat pejabat/pimpinan unik, paling baru berada di indeks 0
                        $historyPejabat = $group
                            ->map(fn($it) => trim($it->mitra_pimpinan ?? ''))
                            ->filter()
                            ->unique()
                            ->values()
                            ->all();

                        // Ambil seluruh riwayat alamat domisili kantor unik, paling baru berada di indeks 0
                        $historyAlamat = $group
                            ->map(fn($it) => trim($it->mitra_alamat ?? ''))
                            ->filter()
                            ->unique()
                            ->values()
                            ->all();

                        return [
                            'nama'             => $companyName,
                            'pimpinan'         => $historyPejabat[0] ?? '',
                            'alamat'           => $historyAlamat[0] ?? '',
                            'history_pimpinan' => $historyPejabat,
                            'history_alamat'   => $historyAlamat,
                        ];
                    })
                    ->values();
            };

            // ─── Pilih Jenis Input (Belanja Modal / Hibah) ───────────────
            Route::get('/astap/pilih-jenis', function () {
                return view('pages.astap.pilih_jenis');
            })->name('astap.pilih_jenis');

            // ─── Form Hibah (Create, Store, Edit, Update) ───────────────────
            Route::get('/astap/create-hibah', [\App\Http\Controllers\HibahController::class, 'create'])->name('astap.create_hibah');
            Route::post('/astap/store-hibah', [\App\Http\Controllers\HibahController::class, 'store'])->name('astap.store_hibah');
            Route::get('/astap/{id}/edit-hibah', [\App\Http\Controllers\HibahController::class, 'edit'])->name('astap.edit_hibah');
            Route::match(['put', 'post'], '/astap/update-hibah/{id}', [\App\Http\Controllers\HibahController::class, 'update'])->name('astap.update_hibah');


            // ─── Belanja Barang (Akun 5.1.02 / Perbekalan Ruangan) ────────
            Route::get('/master-data/belanja-barang', [\App\Http\Controllers\BelanjaBarangController::class, 'index'])->name('master.belanja_barang');
            Route::delete('/master-data/belanja-barang/{id}', [\App\Http\Controllers\BelanjaBarangController::class, 'destroy'])->name('master.belanja_barang.destroy');
            Route::get('/astap/create-belanja-barang', [\App\Http\Controllers\BelanjaBarangController::class, 'create'])->name('astap.create_belanja_barang');
            Route::post('/astap/store-belanja-barang', [\App\Http\Controllers\BelanjaBarangController::class, 'store'])->name('astap.store_belanja_barang');

            // Backward compatibility aliases
            Route::post('/astap/store-belanja-barang-alias', [\App\Http\Controllers\BelanjaBarangController::class, 'store'])->name('belanja_barang.store');
            Route::get('/astap/create-rekening', fn() => redirect()->route('astap.create_belanja_barang'))->name('astap.create_rekening');
            Route::post('/astap/store-rekening', [\App\Http\Controllers\BelanjaBarangController::class, 'store'])->name('astap.store_rekening');

            // ─── Mutasi Eksternal (Transfer Antar-OPD / Masuk & Keluar) ───────────────
            Route::get('/astap/create-mutasi-eksternal', [\App\Http\Controllers\MutasiEksternalController::class, 'create'])->name('astap.create_mutasi_eksternal');
            Route::get('/astap/create-mutasi-masuk', function () {
                return redirect()->route('astap.create_mutasi_eksternal', request()->all());
            })->name('astap.create_mutasi_masuk');

            Route::get('/astap/{id}/edit-mutasi-eksternal', [\App\Http\Controllers\MutasiEksternalController::class, 'edit'])->name('astap.edit_mutasi_eksternal');
            Route::get('/astap/{id}/edit-mutasi-masuk', function ($id) {
                return redirect()->route('astap.edit_mutasi_eksternal', array_merge(['id' => $id], request()->all()));
            })->name('astap.edit_mutasi_masuk');

            Route::post('/astap/store-mutasi-eksternal', [\App\Http\Controllers\MutasiEksternalController::class, 'store'])->name('astap.store_mutasi_eksternal');

            Route::match(['put', 'post'], '/astap/update-mutasi-eksternal/{id}', [\App\Http\Controllers\MutasiEksternalController::class, 'update'])->name('astap.update_mutasi_eksternal');

            Route::delete('/mutasi-eksternal/{id}', [\App\Http\Controllers\MutasiEksternalController::class, 'destroy'])->name('mutasi.eksternal.destroy');
            Route::post('/mutasi-eksternal/{id}/batalkan', [\App\Http\Controllers\MutasiEksternalController::class, 'batalkan'])->name('mutasi.eksternal.batalkan');
            Route::post('/mutasi-eksternal/{id}/sahkan', [\App\Http\Controllers\MutasiEksternalController::class, 'sahkan'])->name('mutasi.eksternal.sahkan');
            Route::get('/mutasi-eksternal/{id}', [\App\Http\Controllers\MutasiEksternalController::class, 'show'])->name('mutasi.eksternal.show');
            Route::get('/mutasi-eksternal/{id}/cetak', [\App\Http\Controllers\MutasiEksternalController::class, 'cetak'])->name('mutasi.eksternal.cetak');


            // ─── Form Kemitraan Pihak Ketiga / KSO (Create & Store) ────────
            $getObjekAsetKemitraans = function() {
                // 1. Ambil data Kemitraan yang terdaftar sebagai Objek Pemanfaatan BMD (Tabel 1 Master Kemitraan)
                $kemitraans = \App\Models\AstapKemitraan::with([
                    'astap.jenisAstap',
                    'astap.registers.unit',
                    'astap.unit',
                    'astap.reklas',
                    'objekAstap.reklas',
                    'objekRegister.astap.reklas'
                ])
                ->where('is_deleted', 0)
                ->whereHas('astap', fn($q) => $q->where('is_deleted', 0))
                ->orderBy('tanggal_pks', 'desc')
                ->orderBy('id', 'desc')
                ->get();

                // Filter aset yang dimanfaatkan (bukan ditambahkan) persis seperti logika Tabel 1
                $dimanfaatkan = $kemitraans->filter(function($row) {
                    $tipe = $row->tipe_kemitraan;
                    if ($tipe === 'ditambahkan') return false;
                    if ($tipe === 'dimanfaatkan') return true;
                    $astap = $row->astap;
                    if ($astap && ($astap->is_reklas || ($astap->reklas && $astap->reklas->isNotEmpty()))) return true;
                    $namaBarang = strtolower($astap?->nama_barang ?? '');
                    if (str_starts_with($namaBarang, 'sewa ') || str_contains($namaBarang, 'pemanfaatan')) return true;
                    return false;
                })->values();

                // 2. Ambil Reklasifikasi ke Akun Kemitraan (1.5.2) yang belum dibuatkan PKS
                $existingAstapIds = $kemitraans->flatMap(fn($r) => array_filter([$r->astap_id, $r->objek_astap_id]))->unique()->toArray();
                $reklasPending = \App\Models\AstapReklas::where('is_deleted', 0)
                    ->where(function ($rq) {
                        $rq->where('tujuan_kib', 'KEMITRAAN')
                           ->orWhere('tujuan_kode', 'like', '1.5.2%');
                    })
                    ->whereNotIn('astap_id', $existingAstapIds)
                    ->whereHas('astap', fn($q) => $q->where('is_deleted', 0))
                    ->with(['astap.registers.unit', 'astap.jenisAstap', 'astap.unit', 'astap.reklas'])
                    ->orderBy('tanggal_reklas', 'desc')
                    ->get()
                    ->unique('astap_id');

                // Helper deduplikasi kata berulang (misal "Sewa Aset Tetap lainnya, Sewa Aset Tetap lainnya")
                $cleanDuplicateWords = function($t) {
                    if (empty($t)) return '';
                    if (str_contains($t, ',')) {
                        $parts = array_filter(array_map('trim', explode(',', $t)));
                        return implode(', ', array_unique($parts));
                    }
                    return trim($t);
                };

                $isJenisKemitraanText = function($t) {
                    if (empty($t)) return true;
                    $l = strtolower(trim($t));
                    return str_starts_with($l, 'kerja sama pemanfaatan')
                        || str_starts_with($l, 'bangun guna serah')
                        || str_starts_with($l, 'bangun serah guna')
                        || str_starts_with($l, 'ksp ')
                        || str_starts_with($l, 'kso ')
                        || str_starts_with($l, 'sewa ')
                        || $l === 'sewa tanah'
                        || $l === 'sewa peralatan dan mesin'
                        || $l === 'sewa gedung dan bangunan'
                        || $l === 'sewa jalan, irigasi dan jaringan'
                        || $l === 'sewa aset tetap lainnya'
                        || $l === 'kerja sama pemanfaatan tanah'
                        || $l === 'kerja sama pemanfaatan peralatan dan mesin'
                        || $l === 'kerja sama pemanfaatan gedung dan bangunan'
                        || $l === 'kerja sama pemanfaatan jalan, irigasi dan jaringan'
                        || $l === 'kerja sama pemanfaatan aset tetap lainnya';
                };

                $isInvalidDummyName = function($str) {
                    if (empty($str)) return true;
                    $cleaned = trim(str_replace([' ', '-', '_', '.', '—'], '', (string)$str));
                    return empty($cleaned);
                };

                $resolveNama = function($astap, $row, $spec, $reklasHistory, $kib = 'KIB A') use ($isJenisKemitraanText, $cleanDuplicateWords, $isInvalidDummyName) {
                    $specNama = $spec['tanah_items'][0]['tanah_nama_barang'] 
                        ?? ($spec['mesin_items'][0]['mesin_nama_barang'] 
                        ?? ($spec['gedung_items'][0]['gedung_nama_barang'] 
                        ?? ($spec['jaringan_items'][0]['jaringan_nama_barang'] 
                        ?? ($spec['lainnya_items'][0]['lainnya_nama_barang'] 
                        ?? ($spec['tanah_nama_barang'] ?? ($spec['gedung_nama_bangunan'] ?? null))))));

                    if (!empty($specNama) && !$isInvalidDummyName($specNama) && !$isJenisKemitraanText($specNama)) {
                        return $cleanDuplicateWords($specNama);
                    }

                    if (!empty($astap?->nama_barang) && !$isInvalidDummyName($astap->nama_barang) && !$isJenisKemitraanText($astap->nama_barang)) {
                        return $cleanDuplicateWords($astap->nama_barang);
                    }

                    if ($reklasHistory && !empty($reklasHistory->asal_nama) && !$isInvalidDummyName($reklasHistory->asal_nama) && !$isJenisKemitraanText($reklasHistory->asal_nama)) {
                        return $cleanDuplicateWords($reklasHistory->asal_nama);
                    }

                    $merkType = trim(($spec['merk'] ?? '') . ' ' . ($spec['type'] ?? ''));
                    if (!empty($merkType) && strlen($merkType) > 2 && !$isInvalidDummyName($merkType)) {
                        return $cleanDuplicateWords($merkType);
                    }

                    $rawNama = $astap?->nama_barang ?: 'Objek BMD';
                    if ($isInvalidDummyName($rawNama)) {
                        $rawNama = $reklasHistory?->tujuan_nama 
                            ?: ($astap?->jenisAstap?->uraian_sub_sub_rincian 
                            ?: ($astap?->jenisAstap?->uraian_sub_rincian 
                            ?: ($astap?->jenisAstap?->nama_jenis ?: 'Sewa ' . ($kib === 'KIB A' ? 'Tanah' : ($kib === 'KIB C' ? 'Gedung' : ($kib === 'KIB B' ? 'Peralatan' : 'BMD'))))));
                    }
                    if ($isInvalidDummyName($rawNama)) {
                        $rawNama = 'Sewa ' . ($kib === 'KIB A' ? 'Tanah' : ($kib === 'KIB C' ? 'Gedung' : 'Objek BMD'));
                    }
                    return $cleanDuplicateWords($rawNama);
                };

                $resolveKib = function($astap, $spec, $reklasHistory) {
                    if (!empty($reklasHistory?->asal_kib)) return strtoupper($reklasHistory->asal_kib);
                    if (!empty($astap?->category) && in_array(strtoupper($astap->category), ['KIB A', 'KIB B', 'KIB C', 'KIB D', 'KIB E', 'EXTRACOM'])) {
                        return strtoupper($astap->category);
                    }
                    $kode = $astap?->kode_108 ?: ($astap?->jenisAstap?->sub_sub_rincian_objek ?: ($astap?->jenisAstap?->jenis ?: ''));
                    if (str_contains($kode, '.01.01.001') || str_ends_with($kode, '.001') || str_starts_with($kode, '1.3.1')) return 'KIB A';
                    if (str_contains($kode, '.01.01.002') || str_ends_with($kode, '.002') || str_starts_with($kode, '1.3.2')) return 'KIB B';
                    if (str_contains($kode, '.01.01.003') || str_ends_with($kode, '.003') || str_starts_with($kode, '1.3.3')) return 'KIB C';
                    if (str_contains($kode, '.01.01.004') || str_ends_with($kode, '.004') || str_starts_with($kode, '1.3.4')) return 'KIB D';
                    if (str_contains($kode, '.01.01.005') || str_ends_with($kode, '.005') || str_starts_with($kode, '1.3.5')) return 'KIB E';

                    $nb = strtolower($astap?->nama_barang ?? '');
                    if (str_contains($nb, 'tanah') || str_contains($nb, 'lahan')) return 'KIB A';
                    if (str_contains($nb, 'gedung') || str_contains($nb, 'bangunan') || str_contains($nb, 'ruang')) return 'KIB C';
                    if (str_contains($nb, 'mesin') || str_contains($nb, 'alat') || str_contains($nb, 'alkes')) return 'KIB B';
                    if (str_contains($nb, 'jalan') || str_contains($nb, 'jaringan') || str_contains($nb, 'irigasi')) return 'KIB D';

                    return 'KIB A';
                };

                $extractSpecFields = function($astap, $spec, $reklasHistory) use ($resolveKib) {
                    $isExtracom = (bool) (
                        $astap?->is_extracomtable 
                        || ($astap?->category && strtoupper($astap->category) === 'EXTRACOM')
                        || ($reklasHistory?->asal_kib && strtoupper($reklasHistory->asal_kib) === 'EXTRACOM')
                        || ($spec['is_extracomtable'] ?? false)
                        || (isset($spec['mesin_items']) && is_array($spec['mesin_items']) && collect($spec['mesin_items'])->contains(fn($m) => !empty($m['is_extracom'])))
                        || (isset($spec['lainnya_items']) && is_array($spec['lainnya_items']) && collect($spec['lainnya_items'])->contains(fn($l) => !empty($l['is_extracom'])))
                    );

                    $kibAsli = $resolveKib($astap, $spec, $reklasHistory);
                    $kib = $isExtracom ? 'EXTRACOM' : $kibAsli;

                    $luas = $spec['luas_m2'] ?? ($spec['tanah_luas_m2'] ?? ($spec['gedung_luas_lantai'] ?? ($spec['jaringan_luas'] ?? null)));
                    $sertifikat = $spec['sertifikat_no'] ?? ($spec['tanah_sertifikat_no'] ?? ($spec['gedung_dokumen_no'] ?? null));

                    $merk = $spec['merk'] ?? ($spec['mesin_merk'] ?? ($spec['mesin_items'][0]['mesin_merk'] ?? null));
                    $type = $spec['type'] ?? ($spec['mesin_type'] ?? ($spec['mesin_items'][0]['mesin_type'] ?? null));
                    $merkType = trim(($merk ?: '') . ' ' . ($type ?: ''));
                    if (empty($merkType)) {
                        $merkType = $spec['spesifikasi'] ?? ($spec['lainnya_spesifikasi'] ?? ($spec['lainnya_items'][0]['lainnya_spesifikasi'] ?? null));
                    }

                    $ukuran = $spec['ukuran'] ?? ($spec['mesin_ukuran'] ?? ($spec['mesin_items'][0]['mesin_ukuran'] ?? ($spec['lainnya_ukuran'] ?? null)));
                    $noPabrik = $spec['no_pabrik'] ?? ($spec['mesin_no_pabrik'] ?? ($spec['mesin_items'][0]['mesin_no_pabrik'] ?? ($spec['mesin_no_polisi'] ?? null)));
                    $konstruksi = $spec['konstruksi_bertingkat'] ?? ($spec['gedung_konstruksi_bertingkat'] ?? ($spec['jaringan_konstruksi'] ?? null));
                    $panjang = $spec['panjang_m'] ?? ($spec['jaringan_panjang'] ?? null);
                    $judul = $spec['judul'] ?? ($spec['lainnya_judul'] ?? ($spec['lainnya_items'][0]['lainnya_judul'] ?? null));
                    $bahan = $spec['bahan'] ?? ($spec['mesin_bahan'] ?? ($spec['lainnya_bahan'] ?? null));

                    return [
                        'kib'             => $kib,
                        'kib_asli'        => $kibAsli,
                        'is_extracomtable'=> $isExtracom,
                        'luas'            => $luas,
                        'sertifikat'      => $sertifikat,
                        'merk_type'       => $merkType ?: null,
                        'ukuran'          => $ukuran ?: null,
                        'no_pabrik'       => $noPabrik ?: null,
                        'konstruksi'      => $konstruksi ?: null,
                        'panjang'         => $panjang ?: null,
                        'judul'           => $judul ?: null,
                        'bahan'           => $bahan ?: null,
                        'spesifikasi_raw' => $spec,
                    ];
                };

                $result = collect();

                // Masukkan data Kemitraan Dimanfaatkan (Per Aset/Kontrak Induk)
                foreach ($dimanfaatkan as $kem) {
                    $astap = $kem->astap;
                    if (!$astap) continue;
                    $spec = is_array($astap->spesifikasi_json) ? $astap->spesifikasi_json : (json_decode($astap->spesifikasi_json, true) ?: []);
                    $firstReg = $astap->registers?->first();
                    $reklasHistory = $astap->reklas?->sortByDesc('id')->first();
                    $specData = $extractSpecFields($astap, $spec, $reklasHistory);
                    $namaBarang = $resolveNama($astap, $kem, $spec, $reklasHistory, $specData['kib']);

                    $tglPks = $kem->tanggal_pks ? $kem->tanggal_pks->format('Y-m-d') : ($astap->bast_dokumen_tanggal ? date('Y-m-d', strtotime($astap->bast_dokumen_tanggal)) : ($spec['tanggal_pks'] ?? null));
                    $tglMulai = $kem->tanggal_mulai ? $kem->tanggal_mulai->format('Y-m-d') : ($spec['tanggal_mulai'] ?? null);
                    $tglSelesai = $kem->tanggal_selesai ? $kem->tanggal_selesai->format('Y-m-d') : ($spec['tanggal_selesai'] ?? null);

                    $result->push([
                        'id'              => $kem->id,
                        'kemitraan_id'    => $kem->id,
                        'astap_id'        => $astap->id,
                        'register_id'     => $firstReg?->id ?: null,
                        'nibar'           => null, // Sesuai permintaan user: tidak menampilkan NIBAR register
                        'nama_barang'     => $namaBarang,
                        'kib'             => $specData['kib'],
                        'kib_asli'        => $specData['kib_asli'],
                        'is_extracomtable'=> $specData['is_extracomtable'],
                        'kode_108'        => $astap->jenisAstap?->sub_sub_rincian_objek ?: ($astap->jenisAstap?->sub_rincian_objek ?: ($astap->jenisAstap?->jenis ?: '1.5.2')),
                        'volume'          => $kem->jumlah_volume ?: ($astap->jumlah_volume ?: 1),
                        'satuan'          => $kem->satuan ?: ($astap->satuan ?: 'Unit'),
                        'nomor_pks'       => $kem->nomor_pks ?: ($astap->bast_dokumen_nomor ?: '-'),
                        'mitra_nama'      => $kem->mitra_nama ?: '-',
                        'alamat_barang'   => $astap->alamat_barang ?: 'RSUD Dr. H. Koesnandi',
                        'unit_id'         => $astap->unit_id,
                        'unit_nama'       => $astap->unit?->nama ?: 'RSUD Dr. H. Koesnandi',
                        'tahun_perolehan' => $kem->tahun ?: $astap->tahun_perolehan,
                        'total_realisasi' => (float) ($kem->nilai_aset ?: $astap->total_realisasi),
                        'kondisi'         => $firstReg?->kondisi ?: 'Baik',
                        'luas'            => $specData['luas'],
                        'sertifikat'      => $specData['sertifikat'],
                        'merk_type'       => $specData['merk_type'],
                        'ukuran'          => $specData['ukuran'],
                        'no_pabrik'       => $specData['no_pabrik'],
                        'konstruksi'      => $specData['konstruksi'],
                        'panjang'         => $specData['panjang'],
                        'judul'           => $specData['judul'],
                        'bahan'           => $specData['bahan'],
                        'spesifikasi_raw' => $specData['spesifikasi_raw'],
                        'skema_kemitraan' => $kem->skema_kemitraan ?: ($spec['skema_kemitraan'] ?? 'Sewa'),
                        'kemitraan_data'  => [
                            'mitra_nama'      => $kem->mitra_nama,
                            'mitra_pimpinan'  => $kem->mitra_pimpinan,
                            'mitra_alamat'    => $kem->mitra_alamat,
                            'nomor_pks'       => $kem->nomor_pks,
                            'tanggal_pks'     => $tglPks,
                            'tanggal_mulai'   => $tglMulai,
                            'tanggal_selesai' => $tglSelesai,
                            'skema_kemitraan' => $kem->skema_kemitraan ?: ($spec['skema_kemitraan'] ?? 'Sewa'),
                            'keterangan'      => $kem->keterangan ?: ($astap->keterangan_tambahan ?: null),
                        ],
                        'pks_aktif'       => ($kem->status_konsesi === 'Aktif') ? [
                            'nomor_pks'       => $kem->nomor_pks,
                            'mitra_nama'      => $kem->mitra_nama,
                            'tanggal_selesai' => $kem->tanggal_selesai ? \Carbon\Carbon::parse($kem->tanggal_selesai)->format('d/m/Y') : null,
                        ] : null,
                    ]);
                }

                // Masukkan data Reklasifikasi yang belum ber-PKS
                foreach ($reklasPending as $reklas) {
                    $astap = $reklas->astap;
                    if (!$astap) continue;
                    $spec = is_array($astap->spesifikasi_json) ? $astap->spesifikasi_json : (json_decode($astap->spesifikasi_json, true) ?: []);
                    $firstReg = $astap->registers?->first();
                    $specData = $extractSpecFields($astap, $spec, $reklas);
                    $namaBarang = $resolveNama($astap, null, $spec, $reklas, $specData['kib']);

                    $result->push([
                        'id'              => null,
                        'kemitraan_id'    => null,
                        'astap_id'        => $astap->id,
                        'register_id'     => $firstReg?->id ?: null,
                        'nibar'           => null,
                        'nama_barang'     => $namaBarang,
                        'kib'             => $specData['kib'],
                        'kib_asli'        => $specData['kib_asli'],
                        'is_extracomtable'=> $specData['is_extracomtable'],
                        'kode_108'        => $reklas->tujuan_kode ?: ($astap->jenisAstap?->sub_sub_rincian_objek ?: '1.5.2'),
                        'volume'          => $astap->jumlah_volume ?: 1,
                        'satuan'          => $astap->satuan ?: 'Unit',
                        'nomor_pks'       => null,
                        'mitra_nama'      => null,
                        'alamat_barang'   => $astap->alamat_barang ?: 'RSUD Dr. H. Koesnandi',
                        'unit_id'         => $astap->unit_id,
                        'unit_nama'       => $astap->unit?->nama ?: 'RSUD Dr. H. Koesnandi',
                        'tahun_perolehan' => $astap->tahun_perolehan,
                        'total_realisasi' => (float) $astap->total_realisasi,
                        'kondisi'         => $firstReg?->kondisi ?: 'Baik',
                        'luas'            => $specData['luas'],
                        'sertifikat'      => $specData['sertifikat'],
                        'merk_type'       => $specData['merk_type'],
                        'ukuran'          => $specData['ukuran'],
                        'no_pabrik'       => $specData['no_pabrik'],
                        'konstruksi'      => $specData['konstruksi'],
                        'panjang'         => $specData['panjang'],
                        'judul'           => $specData['judul'],
                        'bahan'           => $specData['bahan'],
                        'spesifikasi_raw' => $specData['spesifikasi_raw'],
                        'kemitraan_data'  => null,
                        'pks_aktif'       => null,
                    ]);
                }

                return $result->values();
            };

            Route::get('/astap/create-kemitraan', function (\Illuminate\Http\Request $request) use ($getDistinctPenyedias, $getDistinctPejabats, $getDistinctMitras, $getObjekAsetKemitraans) {
                $dbMaster108 = \App\Models\JenisAstap::getNested108();
                $dbUnits = \App\Models\Unit::orderBy('nama')->get();
                $dbPenyedias = $getDistinctPenyedias();
                $dbPejabats = $getDistinctPejabats();
                $dbMitraKemitraans = $getDistinctMitras();
                $dbObjekAsetKemitraan = $getObjekAsetKemitraans();
                $tipe = $request->query('tipe', 'dimanfaatkan');

                // PPK: khusus riwayat Pejabat Pembuat Komitmen kemitraan untuk autofill NIP
                $dbPpkKemitraans = \App\Models\Astap::where('sumber_dana', 'kemitraan')
                    ->whereNotNull('ppk_nama')
                    ->where('ppk_nama', '!=', '')
                    ->where('is_deleted', 0)
                    ->orderBy('id', 'desc')
                    ->get(['ppk_nama', 'ppk_nip'])
                    ->filter(fn($it) => strlen(trim($it->ppk_nama)) >= 4 && !in_array(strtolower(trim($it->ppk_nama)), ['dsc', 'gf', 'test', 'tester']))
                    ->groupBy(function($item) {
                        return strtolower(trim($item->ppk_nama));
                    })
                    ->map(function($group) {
                        $latest = $group->first();
                        $withNip = $group->first(function($it) {
                            return !empty(trim($it->ppk_nip ?? ''));
                        });
                        return [
                            'nama' => trim($latest->ppk_nama),
                            'nip'  => trim($withNip ? $withNip->ppk_nip : ($latest->ppk_nip ?? '')),
                        ];
                    })
                    ->values();

                // Pastikan PPK default (BUDI HARTONO, S.Sos) selalu tersedia jika belum ada di database
                if ($dbPpkKemitraans->isEmpty() || !$dbPpkKemitraans->contains(fn($p) => stripos($p['nama'], 'BUDI HARTONO') !== false)) {
                    $dbPpkKemitraans->prepend([
                        'nama' => 'BUDI HARTONO, S.Sos',
                        'nip'  => '19760229 200801 1 010'
                    ]);
                }

                return view('pages.kemitraan.form', compact(
                    'dbMaster108', 'dbUnits', 'dbPenyedias', 'dbPejabats',
                    'dbMitraKemitraans', 'dbPpkKemitraans', 'dbObjekAsetKemitraan', 'tipe'
                ));
            })->name('astap.create_kemitraan');

            Route::post('/astap/store-kemitraan', function (\Illuminate\Http\Request $request) {
                // Normalisasi input JSON string jika dikirim via multipart FormData
                foreach (['mesin_items', 'tanah_items', 'gedung_items', 'jaringan_items', 'lainnya_items', 'spesifikasi_json'] as $jsonField) {
                    if ($request->has($jsonField) && is_string($request->input($jsonField))) {
                        $decoded = json_decode($request->input($jsonField), true);
                        if (is_array($decoded)) {
                            $request->merge([$jsonField => $decoded]);
                        }
                    }
                }

                // Sanitasi input tanggal: tangani ISO string (misal 2026-09-29T17:00:00.000000Z) dari datepicker
                $cleanDateInput = function($val) {
                    if (empty($val)) return null;
                    $val = trim((string)$val);
                    if (strpos($val, 'T') !== false) {
                        $val = explode('T', $val)[0];
                    }
                    return $val;
                };

                $dateMerges = [];
                foreach (['tanggal_pks', 'tanggal_mulai', 'tanggal_selesai'] as $df) {
                    if ($request->has($df) && !empty($request->input($df))) {
                        $dateMerges[$df] = $cleanDateInput($request->input($df));
                    }
                }
                if (!empty($dateMerges)) {
                    $request->merge($dateMerges);
                }

                if (!$request->filled('satuan')) {
                    $request->merge(['satuan' => 'Unit']);
                }

                $data = $request->validate([
                    'nama_barang'          => 'required|string|max:500',
                    'jenis_astap_id'       => 'required|integer|exists:jenis_astaps,id',
                    'tahun_perolehan'      => 'required|integer|min:1990|max:2100',
                    'jumlah_volume'        => 'required|integer|min:1',
                    'satuan'               => 'required|string|max:100',
                    'total_realisasi'      => 'required|numeric|min:0',
                    'triwulan'             => 'required|string|in:TW I,TW II,TW III,TW IV',
                    // BUG-01 FIX: validasi skema_kemitraan agar tidak bisa diinjeksi nilai bebas
                    'skema_kemitraan'      => 'nullable|string|in:Sewa,KSO,KSP,BGS,BSG,BGS/BSG,KSPI,KSO/KSP',
                    'mitra_nama'           => 'required|string|max:500',
                    'mitra_pimpinan'       => 'nullable|string|max:255',
                    'mitra_alamat'         => 'nullable|string|max:1000',
                    'ppk_nama'             => 'nullable|string|max:255',
                    'ppk_nip'              => 'nullable|string|max:100',
                    'nomor_pks'            => 'required|string|max:255',
                    // Format tanggal PKS: toleran DD/MM/YYYY atau YYYY-MM-DD
                    'tanggal_pks'          => ['required', 'string', 'regex:/^(\d{1,2}[\/\-]\d{1,2}[\/\-]\d{4}|\d{4}[\/\-]\d{1,2}[\/\-]\d{1,2})/'],
                    // tanggal_mulai & selesai boleh kosong — diparse manual di bawah
                    'tanggal_mulai'        => 'nullable|string|max:50',
                    'tanggal_selesai'      => 'nullable|string|max:50',
                    'kemitraan_keterangan' => 'nullable|string|max:2000',
                    'unit_id'              => 'nullable|integer|exists:units,id',
                    'alamat_barang'        => 'nullable|string|max:1000',
                    'kondisi'              => 'nullable|string|in:Baik,Kurang Baik,Rusak Berat',
                    'dokumen_file'         => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240',
                    'objek_astap_id'       => 'nullable|integer|exists:astaps,id',
                    'objek_register_id'    => 'nullable|integer|exists:astap_registers,id',
                    'objek_nibar'          => 'nullable|string|max:50',
                ]);

                // Helper parser tanggal toleran DD/MM/YYYY dan YYYY-MM-DD
                $parseDateHelper = function($val) {
                    if (empty($val)) return null;
                    $val = trim($val);
                    if (preg_match('/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})$/', $val, $m)) {
                        return strtotime(sprintf('%04d-%02d-%02d 00:00:00', $m[3], $m[2], $m[1]));
                    }
                    if (preg_match('/^(\d{4})[\/\-](\d{1,2})[\/\-](\d{1,2})$/', $val, $m)) {
                        return strtotime(sprintf('%04d-%02d-%02d 00:00:00', $m[1], $m[2], $m[3]));
                    }
                    $t = strtotime(str_replace('/', '-', $val));
                    return $t ? strtotime(date('Y-m-d 00:00:00', $t)) : null;
                };

                $todayTimestamp = strtotime(date('Y-m-d 23:59:59'));
                $tPks = $parseDateHelper($data['tanggal_pks']);

                // Validasi Tanggal PKS: tidak boleh lebih dari tanggal hari ini
                if ($tPks && $tPks > $todayTimestamp) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Tanggal penandatanganan PKS tidak boleh melebihi tanggal hari ini.',
                        'errors'  => [
                            'tanggal_pks' => ['Tanggal penandatanganan PKS tidak boleh melebihi tanggal hari ini.']
                        ]
                    ], 422);
                }

                // Validasi Tanggal Mulai & Selesai Kerjasama
                // Fleksibel untuk kontrak di masa depan maupun berlaku surut administratif
                if (!empty($data['tanggal_mulai'])) {
                    $tMulai = $parseDateHelper($data['tanggal_mulai']);
                }

                // Validasi Tanggal Berakhir: tidak boleh lebih awal dari tanggal mulai
                if (!empty($data['tanggal_mulai']) && !empty($data['tanggal_selesai'])) {
                    $tMulai = $parseDateHelper($data['tanggal_mulai']);
                    $tSelesai = $parseDateHelper($data['tanggal_selesai']);
                    if ($tMulai && $tSelesai && $tSelesai < $tMulai) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Tanggal berakhir kerjasama tidak boleh di bawah (lebih awal dari) tanggal mulai kerjasama.',
                            'errors'  => [
                                'tanggal_selesai' => ['Tanggal berakhir kerjasama tidak boleh di bawah (lebih awal dari) tanggal mulai kerjasama.']
                            ]
                        ], 422);
                    }
                }

                // Validasi Overlap PKS Aktif pada Objek Aset RSUD Terkait (Bug B8 Prevention)
                if (!empty($data['objek_register_id'])) {
                    $tMulaiVal = !empty($data['tanggal_mulai']) ? $parseDateHelper($data['tanggal_mulai']) : $tPks;
                    $tSelesaiVal = !empty($data['tanggal_selesai']) ? $parseDateHelper($data['tanggal_selesai']) : null;

                    $activeOverlap = \App\Models\AstapKemitraan::where('objek_register_id', $data['objek_register_id'])
                        ->where('status_konsesi', 'Aktif')
                        ->where('is_deleted', 0)
                        ->where('nomor_pks', '!=', trim($data['nomor_pks']))
                        ->first();

                    if ($activeOverlap) {
                        $exMulai = $activeOverlap->tanggal_mulai ? strtotime($activeOverlap->tanggal_mulai->format('Y-m-d 00:00:00')) : ($activeOverlap->tanggal_pks ? strtotime($activeOverlap->tanggal_pks->format('Y-m-d 00:00:00')) : null);
                        $exSelesai = $activeOverlap->tanggal_selesai ? strtotime($activeOverlap->tanggal_selesai->format('Y-m-d 23:59:59')) : null;

                        $isOverlap = false;
                        if (!$exSelesai || !$tSelesaiVal) {
                            $isOverlap = true;
                        } else {
                            if ($tMulaiVal <= $exSelesai && $tSelesaiVal >= $exMulai) {
                                $isOverlap = true;
                            }
                        }

                        if ($isOverlap) {
                            return response()->json([
                                'success' => false,
                                'message' => 'Aset objek RSUD ini (NIBAR: ' . ($data['objek_nibar'] ?? $activeOverlap->objek_nibar) . ') sedang terikat perjanjian kerja sama aktif lain: ' . $activeOverlap->nomor_pks . ' (' . $activeOverlap->mitra_nama . ').',
                                'errors'  => [
                                    'objek_register_id' => ['Aset ini masih dalam masa konsesi aktif pada PKS ' . $activeOverlap->nomor_pks]
                                ]
                            ], 422);
                        }
                    }
                }

                $totalRealisasi = (float) $data['total_realisasi'];
                $totalVolume    = max(1, (int) $data['jumlah_volume']);
                $hargaSatuan    = $totalRealisasi / $totalVolume;
                $tahun          = (int) $data['tahun_perolehan'];
                $kondisiItem    = (!empty($data['kondisi']) ? $data['kondisi'] : 'Baik');

                $isExtracom = $request->boolean('is_extracomtable');
                if (!$isExtracom && $request->has('mesin_items')) {
                    foreach ((array)$request->input('mesin_items') as $m) {
                        if (!empty($m['is_extracom'])) { $isExtracom = true; break; }
                    }
                }
                if (!$isExtracom && $request->has('lainnya_items')) {
                    foreach ((array)$request->input('lainnya_items') as $l) {
                        if (!empty($l['is_extracom'])) { $isExtracom = true; break; }
                    }
                }

                // Handle Berkas Dokumen BAST / PKS Kerja Sama (Bisa diunggah baru atau otomatis mewarisi dokumen BAST awal)
                $dokumenPath = null;
                if ($request->hasFile('dokumen_file')) {
                    $file = $request->file('dokumen_file');
                    $filename = 'BAST_KEMITRAAN_' . time() . '_' . \Illuminate\Support\Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
                    $dokumenPath = $file->storeAs('dokumen_kemitraan', $filename, 'public');
                } else {
                    // Otomatis mewarisi dokumen BAST dari objek tanah BMD yang disewa / dikerjasamakan
                    if (!empty($data['objek_astap_id'])) {
                        $parentAstap = \App\Models\Astap::with('kemitraan')->find($data['objek_astap_id']);
                        $dokumenPath = $parentAstap?->kemitraan?->dokumen_path ?: ($parentAstap?->spesifikasi_json['dokumen_path'] ?? null);
                    }
                    if (!$dokumenPath && !empty($data['nomor_pks'])) {
                        $existingKemitraan = \App\Models\AstapKemitraan::where('nomor_pks', $data['nomor_pks'])
                            ->whereNotNull('dokumen_path')
                            ->where('dokumen_path', '!=', '')
                            ->first();
                        $dokumenPath = $existingKemitraan?->dokumen_path;
                    }
                }

                $resolvedObjekNibar = $data['objek_nibar'] ?? null;
                if (empty($resolvedObjekNibar) && !empty($data['objek_register_id'])) {
                    $resolvedObjekNibar = \App\Models\AstapRegister::where('id', $data['objek_register_id'])->value('nibar');
                }

                $astapPayload = [
                    'nama_barang'               => $data['nama_barang'],
                    'jenis_astap_id'            => $data['jenis_astap_id'],
                    'tahun_perolehan'           => $tahun,
                    'jumlah_volume'             => $totalVolume,
                    'satuan'                    => $data['satuan'],
                    'harga_satuan'              => $hargaSatuan,
                    'jumlah_anggaran'           => 0,
                    'jumlah_realisasi'          => $totalRealisasi,
                    'total_realisasi'           => $totalRealisasi,
                    'biaya_administrasi_proyek' => 0,
                    'triwulan'                  => $data['triwulan'],
                    'sumber_dana'               => 'kemitraan',
                    'bast_dokumen_nomor'        => $data['nomor_pks'],
                    'bast_dokumen_tanggal'      => \App\Models\Astap::parseDateInput($data['tanggal_pks']) ?? $data['tanggal_pks'],
                    'keterangan_tambahan'       => $data['kemitraan_keterangan'] ?? null,
                    'unit_id'                   => $data['unit_id'] ?? null,
                    'alamat_barang'             => (!empty($data['alamat_barang']) ? $data['alamat_barang'] : 'RSUD Dr. H. Koesnandi'),
                    'user_id'                   => auth()->id(),
                    'is_extracomtable'          => $isExtracom,
                    'is_reklas'                 => false,
                    'is_deleted'                => 0,
                    'spesifikasi_json'          => [
                        'sumber_dana'        => 'kemitraan',
                        'is_extracomtable'   => $isExtracom,
                        'mitra_nama'         => $data['mitra_nama'],
                        'mitra_pimpinan'     => $data['mitra_pimpinan'] ?? null,
                        'mitra_alamat'       => $data['mitra_alamat'] ?? null,
                        'ppk_nama'           => $data['ppk_nama'] ?? null,
                        'ppk_nip'            => $data['ppk_nip'] ?? null,
                        'nomor_pks'          => $data['nomor_pks'],
                        'tanggal_pks'        => $data['tanggal_pks'],
                        'tanggal_mulai'      => $data['tanggal_mulai'] ?? null,
                        'tanggal_selesai'    => $data['tanggal_selesai'] ?? null,
                        'kondisi'            => $kondisiItem,
                        'keterangan'         => $data['kemitraan_keterangan'] ?? null,
                        'dokumen_path'       => $dokumenPath,
                        'objek_astap_id'     => $data['objek_astap_id'] ?? null,
                        'objek_register_id'  => $data['objek_register_id'] ?? null,
                        'objek_nibar'        => $resolvedObjekNibar,
                    ],
                ];

                if ($request->has('spesifikasi_json') && is_array($request->input('spesifikasi_json'))) {
                    $astapPayload['spesifikasi_json'] = array_merge($astapPayload['spesifikasi_json'], $request->input('spesifikasi_json'));
                }

                $ja = \App\Models\JenisAstap::find($data['jenis_astap_id']);
                $kode108Raw = $ja ? ($ja->sub_sub_rincian_objek ?: ($ja->sub_rincian_objek ?: $ja->jenis)) : '1.5.2.00.00.00';
                $kode108Clean = str_replace('.', '', $kode108Raw);

                $kibCategory = null;
                if (!empty($astapPayload['spesifikasi_json']['kategori_kib'])) {
                    $catStr = strtoupper($astapPayload['spesifikasi_json']['kategori_kib']);
                    if (str_contains($catStr, 'KIB A') || str_contains($catStr, 'TANAH')) $kibCategory = 'KIB A';
                    elseif (str_contains($catStr, 'KIB B') || str_contains($catStr, 'MESIN') || str_contains($catStr, 'PERALATAN')) $kibCategory = 'KIB B';
                    elseif (str_contains($catStr, 'KIB C') || str_contains($catStr, 'GEDUNG') || str_contains($catStr, 'BANGUNAN')) $kibCategory = 'KIB C';
                    elseif (str_contains($catStr, 'KIB D') || str_contains($catStr, 'JARINGAN') || str_contains($catStr, 'JALAN') || str_contains($catStr, 'IRIGASI')) $kibCategory = 'KIB D';
                    elseif (str_contains($catStr, 'KIB E') || str_contains($catStr, 'LAINNYA') || str_contains($catStr, 'BUKU')) $kibCategory = 'KIB E';
                }

                if (!$kibCategory && !empty($kode108Raw)) {
                    if (str_ends_with($kode108Raw, '.005') || str_starts_with($kode108Raw, '1.3.5')) $kibCategory = 'KIB E';
                    elseif (str_ends_with($kode108Raw, '.002') || str_starts_with($kode108Raw, '1.3.2')) $kibCategory = 'KIB B';
                    elseif (str_ends_with($kode108Raw, '.003') || str_starts_with($kode108Raw, '1.3.3')) $kibCategory = 'KIB C';
                    elseif (str_ends_with($kode108Raw, '.004') || str_starts_with($kode108Raw, '1.3.4')) $kibCategory = 'KIB D';
                    elseif (str_ends_with($kode108Raw, '.001') || str_starts_with($kode108Raw, '1.3.1')) $kibCategory = 'KIB A';
                }
                if (!$kibCategory) $kibCategory = 'KIB B';

                $repeaterKeys = ['tanah_items', 'mesin_items', 'gedung_items', 'jaringan_items', 'lainnya_items', 'atb_items', 'kdp_items', 'merk', 'type', 'ukuran', 'bahan', 'no_pabrik', 'no_rangka', 'no_mesin', 'no_polisi', 'sertifikat_nomor'];
                foreach ($repeaterKeys as $rk) {
                    if ($request->has($rk) && !is_null($request->input($rk))) {
                        $astapPayload['spesifikasi_json'][$rk] = $request->input($rk);
                    }
                }

                // Bersihkan repeater array KIB yang tidak aktif agar tidak mencemari spesifikasi_json
                $inactiveKeys = [
                    'KIB A' => ['mesin_items', 'gedung_items', 'jaringan_items', 'lainnya_items'],
                    'KIB B' => ['tanah_items', 'gedung_items', 'jaringan_items', 'lainnya_items'],
                    'KIB C' => ['tanah_items', 'mesin_items', 'jaringan_items', 'lainnya_items'],
                    'KIB D' => ['tanah_items', 'mesin_items', 'gedung_items', 'lainnya_items'],
                    'KIB E' => ['tanah_items', 'mesin_items', 'gedung_items', 'jaringan_items'],
                ];
                foreach ($inactiveKeys[$kibCategory] ?? [] as $ik) {
                    unset($astapPayload['spesifikasi_json'][$ik]);
                }

                $mesinItems = ($kibCategory === 'KIB B') ? ($request->input('mesin_items') ?? ($astapPayload['spesifikasi_json']['mesin_items'] ?? null)) : null;
                if ($kibCategory === 'KIB B' && is_array($mesinItems) && count($mesinItems) > 0) {
                    $astapPayload['spesifikasi_json']['mesin_items'] = $mesinItems;
                    $firstM = $mesinItems[0];
                    if (empty($astapPayload['spesifikasi_json']['merk'])) $astapPayload['spesifikasi_json']['merk'] = $firstM['mesin_merk'] ?? '';
                    if (empty($astapPayload['spesifikasi_json']['type'])) $astapPayload['spesifikasi_json']['type'] = $firstM['mesin_type'] ?? '';
                    if (empty($astapPayload['spesifikasi_json']['no_pabrik'])) $astapPayload['spesifikasi_json']['no_pabrik'] = $firstM['mesin_no_pabrik'] ?? '';
                    if (empty($astapPayload['spesifikasi_json']['bahan'])) $astapPayload['spesifikasi_json']['bahan'] = $firstM['mesin_bahan'] ?? '';
                    if (empty($astapPayload['spesifikasi_json']['ukuran'])) $astapPayload['spesifikasi_json']['ukuran'] = $firstM['mesin_ukuran'] ?? '';
                    if (empty($astapPayload['spesifikasi_json']['tahun_pembuatan'])) $astapPayload['spesifikasi_json']['tahun_pembuatan'] = $firstM['mesin_tahun_pembuatan'] ?? null;
                    if (empty($astapPayload['spesifikasi_json']['kondisi'])) $astapPayload['spesifikasi_json']['kondisi'] = $firstM['mesin_kondisi'] ?? 'Baik';
                    if (empty($astapPayload['spesifikasi_json']['no_rangka'])) $astapPayload['spesifikasi_json']['no_rangka'] = $firstM['mesin_no_rangka'] ?? '';
                    if (empty($astapPayload['spesifikasi_json']['no_mesin'])) $astapPayload['spesifikasi_json']['no_mesin'] = $firstM['mesin_no_mesin'] ?? '';
                    if (empty($astapPayload['spesifikasi_json']['no_bpkb'])) $astapPayload['spesifikasi_json']['no_bpkb'] = $firstM['mesin_no_bpkb'] ?? '';
                    if (!empty($firstM['mesin_kode_barang'])) $astapPayload['spesifikasi_json']['mesin_kode_barang'] = $firstM['mesin_kode_barang'];
                }

                if ($kibCategory === 'KIB A' && $request->has('tanah_items') && is_array($request->input('tanah_items')) && count($request->input('tanah_items')) > 0) {
                    $tItems = $request->input('tanah_items');
                    $firstT = $tItems[0];
                    $totalLuas = array_sum(array_map(fn($it) => (float)($it['tanah_luas_m2'] ?? 0), $tItems));
                    $allSertifikat = array_filter(array_map(fn($it) => $it['tanah_sertifikat_no'] ?? null, $tItems));

                    $astapPayload['spesifikasi_json']['tanah_items'] = $tItems;
                    $astapPayload['spesifikasi_json']['luas_m2'] = $totalLuas;
                    $astapPayload['spesifikasi_json']['hak_tanah'] = $firstT['tanah_hak'] ?? 'Hak Pakai';
                    $astapPayload['spesifikasi_json']['sertifikat_no'] = count($allSertifikat) > 0 ? implode(', ', $allSertifikat) : ($firstT['tanah_sertifikat_no'] ?? null);
                    $astapPayload['spesifikasi_json']['sertifikat_tgl'] = $firstT['tanah_sertifikat_tgl'] ?? null;
                    $astapPayload['spesifikasi_json']['penggunaan'] = $firstT['tanah_penggunaan'] ?? null;
                    $astapPayload['spesifikasi_json']['tanah_jumlah_bidang'] = count($tItems);
                    if (!empty($firstT['tanah_kode_barang'])) $astapPayload['spesifikasi_json']['tanah_kode_barang'] = $firstT['tanah_kode_barang'];
                }

                if ($kibCategory === 'KIB C' && $request->has('gedung_items') && is_array($request->input('gedung_items')) && count($request->input('gedung_items')) > 0) {
                    $gItems = $request->input('gedung_items');
                    foreach ($gItems as &$gi) {
                        $gi['gedung_is_baru'] = $gi['gedung_is_baru'] ?? 'Lama';
                        $gi['is_baru'] = $gi['is_baru'] ?? $gi['gedung_is_baru'];
                    }
                    unset($gi);
                    $firstG = $gItems[0];
                    $astapPayload['spesifikasi_json']['gedung_items'] = $gItems;
                    $astapPayload['spesifikasi_json']['gedung_is_baru'] = $firstG['gedung_is_baru'] ?? 'Lama';
                    $astapPayload['spesifikasi_json']['is_baru'] = $astapPayload['spesifikasi_json']['gedung_is_baru'];
                    if (!empty($firstG['gedung_kapitalisasi_tahun_induk'])) {
                        $astapPayload['spesifikasi_json']['gedung_kapitalisasi_tahun_induk'] = $firstG['gedung_kapitalisasi_tahun_induk'];
                        $astapPayload['spesifikasi_json']['kapitalisasi_tahun_induk'] = $firstG['gedung_kapitalisasi_tahun_induk'];
                    }
                    if (!empty($firstG['gedung_kode_barang'])) $astapPayload['spesifikasi_json']['gedung_kode_barang'] = $firstG['gedung_kode_barang'];
                }

                if ($kibCategory === 'KIB D' && $request->has('jaringan_items') && is_array($request->input('jaringan_items')) && count($request->input('jaringan_items')) > 0) {
                    $jItems = $request->input('jaringan_items');
                    foreach ($jItems as &$ji) {
                        $ji['jaringan_is_baru'] = $ji['jaringan_is_baru'] ?? 'Lama';
                        $ji['is_baru'] = $ji['is_baru'] ?? $ji['jaringan_is_baru'];
                    }
                    unset($ji);
                    $firstJ = $jItems[0];
                    $astapPayload['spesifikasi_json']['jaringan_items'] = $jItems;
                    $astapPayload['spesifikasi_json']['jaringan_is_baru'] = $firstJ['jaringan_is_baru'] ?? 'Lama';
                    $astapPayload['spesifikasi_json']['is_baru'] = $astapPayload['spesifikasi_json']['jaringan_is_baru'];
                    if (!empty($firstJ['jaringan_kapitalisasi_tahun_induk'])) {
                        $astapPayload['spesifikasi_json']['jaringan_kapitalisasi_tahun_induk'] = $firstJ['jaringan_kapitalisasi_tahun_induk'];
                        $astapPayload['spesifikasi_json']['kapitalisasi_tahun_induk'] = $firstJ['jaringan_kapitalisasi_tahun_induk'];
                    }
                    if (!empty($firstJ['jaringan_kode_barang'])) $astapPayload['spesifikasi_json']['jaringan_kode_barang'] = $firstJ['jaringan_kode_barang'];
                }

                if ($kibCategory === 'KIB E' && $request->has('lainnya_items') && is_array($request->input('lainnya_items')) && count($request->input('lainnya_items')) > 0) {
                    $lItems = $request->input('lainnya_items');
                    $firstL = $lItems[0];
                    $astapPayload['spesifikasi_json']['lainnya_items'] = $lItems;
                    if (!empty($firstL['lainnya_kode_barang'])) $astapPayload['spesifikasi_json']['lainnya_kode_barang'] = $firstL['lainnya_kode_barang'];
                }

                if ($request->filled('ppk_nama')) {
                    $astapPayload['ppk_nama'] = $request->input('ppk_nama');
                    $astapPayload['spesifikasi_json']['ppk_nama'] = $request->input('ppk_nama');
                }
                if ($request->filled('ppk_nip')) {
                    $astapPayload['ppk_nip'] = $request->input('ppk_nip');
                    $astapPayload['spesifikasi_json']['ppk_nip'] = $request->input('ppk_nip');
                }

                // Sinkronisasi Alamat Fisik Barang (Khusus Tanah, Gedung, Jaringan)
                $firstT = ($request->has('tanah_items') && is_array($request->input('tanah_items')) && count($request->input('tanah_items')) > 0) ? $request->input('tanah_items')[0] : [];
                $firstG = ($request->has('gedung_items') && is_array($request->input('gedung_items')) && count($request->input('gedung_items')) > 0) ? $request->input('gedung_items')[0] : [];
                $firstJ = ($request->has('jaringan_items') && is_array($request->input('jaringan_items')) && count($request->input('jaringan_items')) > 0) ? $request->input('jaringan_items')[0] : [];

                $isTanahGedungJaringan = count($firstT) > 0 || count($firstG) > 0 || count($firstJ) > 0;

                $resolvedAlamat = null;
                if ($isTanahGedungJaringan) {
                    if (!empty($firstT['tanah_alamat'])) {
                        $resolvedAlamat = $firstT['tanah_alamat'];
                    } elseif ($request->filled('tanah_alamat')) {
                        $resolvedAlamat = $request->input('tanah_alamat');
                    } elseif (!empty($firstG['gedung_alamat'])) {
                        $resolvedAlamat = $firstG['gedung_alamat'];
                    } elseif ($request->filled('gedung_alamat')) {
                        $resolvedAlamat = $request->input('gedung_alamat');
                    } elseif (!empty($firstJ['jaringan_alamat'])) {
                        $resolvedAlamat = $firstJ['jaringan_alamat'];
                    } elseif ($request->filled('jaringan_alamat')) {
                        $resolvedAlamat = $request->input('jaringan_alamat');
                    } elseif (!empty($data['alamat_barang'])) {
                        $resolvedAlamat = $data['alamat_barang'];
                    }
                    $resolvedAlamat = $resolvedAlamat ?: 'RSUD Dr. H. Koesnandi';
                } else {
                    // KIB B (Mesin) dan KIB E (Lainnya) berorientasi pada Ruangan Penempatan, bukan alamat fisik lahan
                    $resolvedAlamat = null;
                }

                $astapPayload['alamat_barang'] = $resolvedAlamat;

                $item = \Illuminate\Support\Facades\DB::transaction(function () use ($astapPayload, $data, $totalVolume, $totalRealisasi, $tahun, $kondisiItem, $mesinItems, $dokumenPath, $request, $kibCategory, $kode108Clean, $resolvedObjekNibar) {
                    $item = \App\Models\Astap::create($astapPayload);

                    // Buat AstapRegister untuk setiap unit barang kemitraan
                    // Pastikan nomor register NIBAR melanjutkan urutan jika tahun dan jenis barang sama (global tahunan)
                    $maxRegInt = \App\Models\AstapRegister::where('tahun_perolehan', $tahun)
                        ->whereHas('astap', function ($sq) use ($data) {
                            $sq->where('jenis_astap_id', $data['jenis_astap_id']);
                        })
                        ->max('no_register_int') ?? 0;

                    $runningRegNum = (int) $maxRegInt;

                    // Periksa juga nomor register dari NIBAR tersimpan dengan prefix (tahun + kode 108) yang sama
                    $nibarPrefix = "1201351102000000280000{$tahun}{$kode108Clean}";
                    $latestNibar = \App\Models\AstapRegister::where('nibar', 'like', "{$nibarPrefix}%")
                        ->orderBy('nibar', 'desc')
                        ->value('nibar');

                    if ($latestNibar && strlen($latestNibar) >= 7) {
                        $lastNum = (int) substr($latestNibar, -7);
                        if ($lastNum > $runningRegNum) {
                            $runningRegNum = $lastNum;
                        }
                    }

                    $unitModel = !empty($data['unit_id']) ? \App\Models\Unit::find($data['unit_id']) : null;
                    
                    // Helper untuk membersihkan string alamat/placeholder agar tidak tercampur ke nama ruangan penempatan
                    $cleanRuang = function ($val) {
                        if (empty($val)) return null;
                        $s = trim($val);
                        if (stripos($s, 'Piere Tendean') !== false ||
                            stripos($s, 'RSUD Dr. H. Koesnandi Bondowoso, Jl') !== false ||
                            in_array($s, ['Belum Ditempatkan / Di Gudang', 'Gudang Aset', '-'])) {
                            return null;
                        }
                        return $s;
                    };

                    $createdRegsCount = 0;

                    // 1. KIB C GEDUNG & BANGUNAN:
                    if ($kibCategory === 'KIB C' && isset($astapPayload['spesifikasi_json']['gedung_items']) && is_array($astapPayload['spesifikasi_json']['gedung_items']) && count($astapPayload['spesifikasi_json']['gedung_items']) > 0) {
                        foreach ($astapPayload['spesifikasi_json']['gedung_items'] as $gItem) {
                            $itemQty = max(1, (int)($gItem['gedung_jumlah_bangunan'] ?? 1));
                            $rawKondisi = strtoupper(trim((string)($gItem['gedung_kondisi'] ?? $kondisiItem)));
                            $kondisiStr = ($rawKondisi === 'KB' || $rawKondisi === 'KURANG BAIK' || $rawKondisi === 'RR' || $rawKondisi === 'RUSAK RINGAN') ? 'Kurang Baik' : (($rawKondisi === 'RB' || $rawKondisi === 'RUSAK BERAT' || $rawKondisi === 'RUSAK') ? 'Rusak Berat' : 'Baik');

                            for ($q = 0; $q < $itemQty; $q++) {
                                $runningRegNum++;
                                $noRegStr = str_pad($runningRegNum, 7, '0', STR_PAD_LEFT);
                                $nibar = "1201351102000000280000{$tahun}{$kode108Clean}{$noRegStr}";

                                while (\App\Models\AstapRegister::where('nibar', $nibar)->exists()) {
                                    $runningRegNum++;
                                    $noRegStr = str_pad($runningRegNum, 7, '0', STR_PAD_LEFT);
                                    $nibar = "1201351102000000280000{$tahun}{$kode108Clean}{$noRegStr}";
                                }

                                $qrPath = "/scan/{$nibar}";
                                \App\Models\AstapRegister::create([
                                    'astap_id'        => $item->id,
                                    'unit_id'         => null,
                                    'tahun_perolehan' => $tahun,
                                    'no_register_int' => $runningRegNum,
                                    'no_register'     => $nibar,
                                    'nibar'           => $nibar,
                                    'qr_code_path'    => $qrPath,
                                    'ruang_pemegang'  => null,
                                    'kondisi'         => $kondisiStr,
                                    'status'          => 'Tersedia',
                                    'is_deleted'      => 0,
                                ]);
                                $createdRegsCount++;
                            }
                        }
                    // 2. KIB A TANAH:
                    } elseif ($kibCategory === 'KIB A' && isset($astapPayload['spesifikasi_json']['tanah_items']) && is_array($astapPayload['spesifikasi_json']['tanah_items']) && count($astapPayload['spesifikasi_json']['tanah_items']) > 0) {
                        foreach ($astapPayload['spesifikasi_json']['tanah_items'] as $tItem) {
                            $itemQty = max(1, (int)($tItem['tanah_jumlah_barang'] ?? 1));
                            $rawKondisi = strtoupper(trim((string)($tItem['tanah_kondisi'] ?? $kondisiItem)));
                            $kondisiStr = ($rawKondisi === 'KB' || $rawKondisi === 'KURANG BAIK' || $rawKondisi === 'RR' || $rawKondisi === 'RUSAK RINGAN') ? 'Kurang Baik' : (($rawKondisi === 'RB' || $rawKondisi === 'RUSAK BERAT' || $rawKondisi === 'RUSAK') ? 'Rusak Berat' : 'Baik');

                            for ($q = 0; $q < $itemQty; $q++) {
                                $runningRegNum++;
                                $noRegStr = str_pad($runningRegNum, 7, '0', STR_PAD_LEFT);
                                $nibar = "1201351102000000280000{$tahun}{$kode108Clean}{$noRegStr}";

                                while (\App\Models\AstapRegister::where('nibar', $nibar)->exists()) {
                                    $runningRegNum++;
                                    $noRegStr = str_pad($runningRegNum, 7, '0', STR_PAD_LEFT);
                                    $nibar = "1201351102000000280000{$tahun}{$kode108Clean}{$noRegStr}";
                                }

                                $qrPath = "/scan/{$nibar}";
                                \App\Models\AstapRegister::create([
                                    'astap_id'        => $item->id,
                                    'unit_id'         => null,
                                    'tahun_perolehan' => $tahun,
                                    'no_register_int' => $runningRegNum,
                                    'no_register'     => $nibar,
                                    'nibar'           => $nibar,
                                    'qr_code_path'    => $qrPath,
                                    'ruang_pemegang'  => null,
                                    'kondisi'         => $kondisiStr,
                                    'status'          => 'Tersedia',
                                    'is_deleted'      => 0,
                                ]);
                                $createdRegsCount++;
                            }
                        }
                    // 3. KIB D JALAN, IRIGASI & JARINGAN:
                    } elseif ($kibCategory === 'KIB D' && isset($astapPayload['spesifikasi_json']['jaringan_items']) && is_array($astapPayload['spesifikasi_json']['jaringan_items']) && count($astapPayload['spesifikasi_json']['jaringan_items']) > 0) {
                        foreach ($astapPayload['spesifikasi_json']['jaringan_items'] as $jItem) {
                            $itemQty = max(1, (int)($jItem['jaringan_jumlah'] ?? 1));
                            $rawKondisi = strtoupper(trim((string)($jItem['jaringan_kondisi'] ?? $kondisiItem)));
                            $kondisiStr = ($rawKondisi === 'KB' || $rawKondisi === 'KURANG BAIK' || $rawKondisi === 'RR' || $rawKondisi === 'RUSAK RINGAN') ? 'Kurang Baik' : (($rawKondisi === 'RB' || $rawKondisi === 'RUSAK BERAT' || $rawKondisi === 'RUSAK') ? 'Rusak Berat' : 'Baik');

                            for ($q = 0; $q < $itemQty; $q++) {
                                $runningRegNum++;
                                $noRegStr = str_pad($runningRegNum, 7, '0', STR_PAD_LEFT);
                                $nibar = "1201351102000000280000{$tahun}{$kode108Clean}{$noRegStr}";

                                while (\App\Models\AstapRegister::where('nibar', $nibar)->exists()) {
                                    $runningRegNum++;
                                    $noRegStr = str_pad($runningRegNum, 7, '0', STR_PAD_LEFT);
                                    $nibar = "1201351102000000280000{$tahun}{$kode108Clean}{$noRegStr}";
                                }

                                $qrPath = "/scan/{$nibar}";
                                \App\Models\AstapRegister::create([
                                    'astap_id'        => $item->id,
                                    'unit_id'         => null,
                                    'tahun_perolehan' => $tahun,
                                    'no_register_int' => $runningRegNum,
                                    'no_register'     => $nibar,
                                    'nibar'           => $nibar,
                                    'qr_code_path'    => $qrPath,
                                    'ruang_pemegang'  => null,
                                    'kondisi'         => $kondisiStr,
                                    'status'          => 'Tersedia',
                                    'is_deleted'      => 0,
                                ]);
                                $createdRegsCount++;
                            }
                        }
                    // 4. KIB E ASET TETAP LAINNYA:
                    } elseif ($kibCategory === 'KIB E' && isset($astapPayload['spesifikasi_json']['lainnya_items']) && is_array($astapPayload['spesifikasi_json']['lainnya_items']) && count($astapPayload['spesifikasi_json']['lainnya_items']) > 0) {
                        foreach ($astapPayload['spesifikasi_json']['lainnya_items'] as $lItem) {
                            $itemQty = max(1, (int)($lItem['lainnya_jumlah'] ?? 1));
                            $rawKondisi = strtoupper(trim((string)($lItem['lainnya_kondisi'] ?? $kondisiItem)));
                            $kondisiStr = ($rawKondisi === 'KB' || $rawKondisi === 'KURANG BAIK' || $rawKondisi === 'RR' || $rawKondisi === 'RUSAK RINGAN') ? 'Kurang Baik' : (($rawKondisi === 'RB' || $rawKondisi === 'RUSAK BERAT' || $rawKondisi === 'RUSAK') ? 'Rusak Berat' : 'Baik');

                            for ($q = 0; $q < $itemQty; $q++) {
                                $runningRegNum++;
                                $noRegStr = str_pad($runningRegNum, 7, '0', STR_PAD_LEFT);
                                $nibar = "1201351102000000280000{$tahun}{$kode108Clean}{$noRegStr}";

                                while (\App\Models\AstapRegister::where('nibar', $nibar)->exists()) {
                                    $runningRegNum++;
                                    $noRegStr = str_pad($runningRegNum, 7, '0', STR_PAD_LEFT);
                                    $nibar = "1201351102000000280000{$tahun}{$kode108Clean}{$noRegStr}";
                                }

                                $qrPath = "/scan/{$nibar}";
                                \App\Models\AstapRegister::create([
                                    'astap_id'        => $item->id,
                                    'unit_id'         => null,
                                    'tahun_perolehan' => $tahun,
                                    'no_register_int' => $runningRegNum,
                                    'no_register'     => $nibar,
                                    'nibar'           => $nibar,
                                    'qr_code_path'    => $qrPath,
                                    'ruang_pemegang'  => null,
                                    'kondisi'         => $kondisiStr,
                                    'status'          => 'Tersedia',
                                    'is_deleted'      => 0,
                                ]);
                                $createdRegsCount++;
                            }
                        }
                    // 5. KIB B PERALATAN & MESIN:
                    } elseif ($kibCategory === 'KIB B' && isset($astapPayload['spesifikasi_json']['mesin_items']) && is_array($astapPayload['spesifikasi_json']['mesin_items']) && count($astapPayload['spesifikasi_json']['mesin_items']) > 0) {
                        foreach ($astapPayload['spesifikasi_json']['mesin_items'] as $mItem) {
                            $itemQty = max(1, (int)($mItem['mesin_jumlah_barang'] ?? 1));
                            $itemNama = !empty($mItem['mesin_nama_barang']) ? trim($mItem['mesin_nama_barang']) : ($item->nama_barang ?? 'Peralatan & Mesin');
                            $rawKondisi = strtoupper(trim((string)($mItem['mesin_kondisi'] ?? $kondisiItem)));
                            $kondisiStr = ($rawKondisi === 'KB' || $rawKondisi === 'KURANG BAIK' || $rawKondisi === 'RR' || $rawKondisi === 'RUSAK RINGAN') ? 'Kurang Baik' : (($rawKondisi === 'RB' || $rawKondisi === 'RUSAK BERAT' || $rawKondisi === 'RUSAK') ? 'Rusak Berat' : 'Baik');

                            for ($q = 0; $q < $itemQty; $q++) {
                                $runningRegNum++;
                                $noRegStr = str_pad($runningRegNum, 7, '0', STR_PAD_LEFT);
                                $nibar = "1201351102000000280000{$tahun}{$kode108Clean}{$noRegStr}";

                                while (\App\Models\AstapRegister::where('nibar', $nibar)->exists()) {
                                    $runningRegNum++;
                                    $noRegStr = str_pad($runningRegNum, 7, '0', STR_PAD_LEFT);
                                    $nibar = "1201351102000000280000{$tahun}{$kode108Clean}{$noRegStr}";
                                }

                                $qrPath = "/scan/{$nibar}";
                                \App\Models\AstapRegister::create([
                                    'astap_id'        => $item->id,
                                    'nama_barang'     => $itemNama,
                                    'unit_id'         => null,
                                    'tahun_perolehan' => $tahun,
                                    'no_register_int' => $runningRegNum,
                                    'no_register'     => $nibar,
                                    'nibar'           => $nibar,
                                    'qr_code_path'    => $qrPath,
                                    'ruang_pemegang'  => null,
                                    'kondisi'         => $kondisiStr,
                                    'status'          => 'Tersedia',
                                    'is_deleted'      => 0,
                                ]);
                                $createdRegsCount++;
                            }
                        }
                    }

                    // 6. Lengkapi register jika total register yang dibuat belum mencapai totalVolume
                    $itemRuang = $cleanRuang($data['ruang_pemegang'] ?? null) ?: ($unitModel ? $unitModel->nama : null);
                    while ($createdRegsCount < $totalVolume) {
                        $runningRegNum++;
                        $noRegStr = str_pad($runningRegNum, 7, '0', STR_PAD_LEFT);
                        $nibar = "1201351102000000280000{$tahun}{$kode108Clean}{$noRegStr}";

                        while (\App\Models\AstapRegister::where('nibar', $nibar)->exists()) {
                            $runningRegNum++;
                            $noRegStr = str_pad($runningRegNum, 7, '0', STR_PAD_LEFT);
                            $nibar = "1201351102000000280000{$tahun}{$kode108Clean}{$noRegStr}";
                        }

                        $qrPath = "/scan/{$nibar}";
                        \App\Models\AstapRegister::create([
                            'astap_id'        => $item->id,
                            'nama_barang'     => $item->nama_barang,
                            'unit_id'         => $itemRuang ? ($data['unit_id'] ?? null) : null,
                            'tahun_perolehan' => $tahun,
                            'no_register_int' => $runningRegNum,
                            'no_register'     => $nibar,
                            'nibar'           => $nibar,
                            'qr_code_path'    => $qrPath,
                            'ruang_pemegang'  => $itemRuang,
                            'kondisi'         => $kondisiItem,
                            'status'          => 'Tersedia',
                            'is_deleted'      => 0,
                        ]);
                        $createdRegsCount++;
                    }

                    // Catat ke tabel master astap_kemitraans
                    \App\Models\AstapKemitraan::create([
                        'astap_id'          => $item->id,
                        'objek_astap_id'    => $data['objek_astap_id'] ?? null,
                        'objek_register_id' => $data['objek_register_id'] ?? null,
                        'objek_nibar'       => $resolvedObjekNibar,
                        'mitra_nama'        => $data['mitra_nama'],
                        'mitra_pimpinan'    => $data['mitra_pimpinan'] ?? null,
                        'mitra_alamat'      => $data['mitra_alamat'] ?? null,
                        'nomor_pks'        => $data['nomor_pks'],
                        'tanggal_pks'      => $data['tanggal_pks'],
                        // BUG-05 FIX: normalisasi skema BGS/BSG → BGS agar konsisten dengan filter master
                        'skema_kemitraan'  => (function($s) {
                            $s = trim($s ?? 'Sewa');
                            // Normalisasi: simpan 'BGS/BSG' tetapi filter master sudah diupdate
                            $allowed = ['Sewa', 'KSO', 'KSP', 'BGS', 'BSG', 'BGS/BSG', 'KSPI', 'KSO/KSP'];
                            return in_array($s, $allowed) ? $s : 'Sewa';
                        })($data['skema_kemitraan'] ?? $request->input('skema_kemitraan', 'Sewa')),
                        'tanggal_mulai'    => $data['tanggal_mulai'] ?? null,
                        'tanggal_selesai'  => $data['tanggal_selesai'] ?? null,
                        'status_konsesi'   => 'Aktif',
                        'jumlah_volume'    => $totalVolume,
                        'satuan'           => $data['satuan'],
                        'nilai_aset'       => $totalRealisasi,
                        'tahun'            => $tahun,
                        'triwulan'         => $data['triwulan'],
                        'keterangan'       => $data['kemitraan_keterangan'] ?? null,
                        'tipe_kemitraan'   => $request->input('tipe_kemitraan', $data['tipe_kemitraan'] ?? 'dimanfaatkan'),
                        'dokumen_path'     => $dokumenPath,
                        'user_id'          => auth()->id(),
                    ]);

                    return $item;
                });

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Data Aset Kemitraan "' . $item->nama_barang . '" berhasil disimpan ke database SIMAT-RK!',
                        'redirect' => route('master.kemitraan')
                    ]);
                }

                return redirect()->route('master.kemitraan')
                    ->with('success', 'Data Aset Kemitraan "' . $item->nama_barang . '" berhasil ditambahkan.');
            })->name('astap.store_kemitraan');

            // ─── API Upload / Ubah & Hapus Berkas BAST Kemitraan ─────────────────
            Route::post('/astap/kemitraan/{id}/upload-dokumen', function(\Illuminate\Http\Request $request, $id) {
                $request->validate([
                    'dokumen_file' => 'required|file|mimes:pdf,jpg,jpeg,png,webp,doc,docx|max:10240'
                ]);

                $astap = \App\Models\Astap::with('kemitraan')->find($id);
                $kemitraan = null;
                if ($astap) {
                    $kemitraan = $astap->kemitraan ?: \App\Models\AstapKemitraan::where('astap_id', $astap->id)->first();
                } else {
                    $kemitraan = \App\Models\AstapKemitraan::find($id);
                    if ($kemitraan && $kemitraan->astap_id) {
                        $astap = \App\Models\Astap::with('kemitraan')->find($kemitraan->astap_id);
                    }
                }

                if (!$astap && !$kemitraan) {
                    return response()->json(['success' => false, 'message' => 'Aset atau data kemitraan tidak ditemukan.'], 404);
                }

                // Hapus berkas lama jika ada
                $oldPath = $kemitraan?->dokumen_path ?: ($astap?->spesifikasi_json['dokumen_path'] ?? null);
                if ($oldPath && \Illuminate\Support\Facades\Storage::disk('public')->exists($oldPath)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($oldPath);
                }

                $file = $request->file('dokumen_file');
                $filename = 'BAST_KEMITRAAN_' . time() . '_' . \Illuminate\Support\Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
                $newPath = $file->storeAs('dokumen_kemitraan', $filename, 'public');

                if ($kemitraan) {
                    $kemitraan->update(['dokumen_path' => $newPath]);
                }

                if ($astap) {
                    $spec = is_array($astap->spesifikasi_json) ? $astap->spesifikasi_json : (json_decode($astap->spesifikasi_json ?? '[]', true) ?: []);
                    $spec['dokumen_path'] = $newPath;
                    $astap->update(['spesifikasi_json' => $spec]);
                }

                return response()->json([
                    'success'      => true,
                    'message'      => 'Berkas dokumen BAST berhasil diunggah/diperbarui.',
                    'dokumen_path' => $newPath,
                    'file_url'     => asset('storage/' . $newPath),
                    'file_name'    => $filename
                ]);
            })->name('astap.kemitraan.upload_dokumen');

            Route::delete('/astap/kemitraan/{id}/delete-dokumen', function($id) {
                $astap = \App\Models\Astap::with('kemitraan')->find($id);
                $kemitraan = null;
                if ($astap) {
                    $kemitraan = $astap->kemitraan ?: \App\Models\AstapKemitraan::where('astap_id', $astap->id)->first();
                } else {
                    $kemitraan = \App\Models\AstapKemitraan::find($id);
                    if ($kemitraan && $kemitraan->astap_id) {
                        $astap = \App\Models\Astap::with('kemitraan')->find($kemitraan->astap_id);
                    }
                }

                if (!$astap && !$kemitraan) {
                    return response()->json(['success' => false, 'message' => 'Aset atau data kemitraan tidak ditemukan.'], 404);
                }

                $oldPath = $kemitraan?->dokumen_path ?: ($astap?->spesifikasi_json['dokumen_path'] ?? null);
                if ($oldPath && \Illuminate\Support\Facades\Storage::disk('public')->exists($oldPath)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($oldPath);
                }

                if ($kemitraan) {
                    $kemitraan->update(['dokumen_path' => null]);
                }

                if ($astap) {
                    $spec = is_array($astap->spesifikasi_json) ? $astap->spesifikasi_json : (json_decode($astap->spesifikasi_json ?? '[]', true) ?: []);
                    unset($spec['dokumen_path']);
                    $astap->update(['spesifikasi_json' => $spec]);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Berkas dokumen BAST berhasil dihapus dari sistem.'
                ]);
            })->name('astap.kemitraan.delete_dokumen');


            // ─── Form Kemitraan Pihak Ketiga (Edit & Update) ─────────────────
            Route::get('/astap/{id}/edit-kemitraan', function (\Illuminate\Http\Request $request, $id) use ($getDistinctPenyedias, $getDistinctPejabats, $getDistinctMitras, $getObjekAsetKemitraans) {
                $astap = \App\Models\Astap::with(['registers.unit', 'jenisAstap', 'kemitraan', 'reklas'])->findOrFail($id);
                $dbMaster108 = \App\Models\JenisAstap::getNested108();
                $dbUnits = \App\Models\Unit::orderBy('nama')->get();
                $dbPenyedias = $getDistinctPenyedias();
                $dbPejabats = $getDistinctPejabats();
                $dbMitraKemitraans = $getDistinctMitras();
                $dbObjekAsetKemitraan = $getObjekAsetKemitraans();

                $dbPpkKemitraans = \App\Models\Astap::where('sumber_dana', 'kemitraan')
                    ->whereNotNull('ppk_nama')
                    ->where('ppk_nama', '!=', '')
                    ->where('is_deleted', 0)
                    ->orderBy('id', 'desc')
                    ->get(['ppk_nama', 'ppk_nip'])
                    ->filter(fn($it) => strlen(trim($it->ppk_nama)) >= 4 && !in_array(strtolower(trim($it->ppk_nama)), ['dsc', 'gf', 'test', 'tester']))
                    ->groupBy(fn($item) => strtolower(trim($item->ppk_nama)))
                    ->map(function($group) {
                        $latest = $group->first();
                        $withNip = $group->first(fn($it) => !empty(trim($it->ppk_nip ?? '')));
                        return [
                            'nama' => trim($latest->ppk_nama),
                            'nip'  => trim($withNip ? $withNip->ppk_nip : ($latest->ppk_nip ?? '')),
                        ];
                    })
                    ->values();

                if ($dbPpkKemitraans->isEmpty() || !$dbPpkKemitraans->contains(fn($p) => stripos($p['nama'], 'BUDI HARTONO') !== false)) {
                    $dbPpkKemitraans->prepend([
                        'nama' => 'BUDI HARTONO, S.Sos',
                        'nip'  => '19760229 200801 1 010'
                    ]);
                }

                $kemitraanRow = $astap->kemitraan ?? \App\Models\AstapKemitraan::where(function ($q) use ($astap) {
                    $q->where('astap_id', $astap->id)->orWhere('objek_astap_id', $astap->id);
                })->where('is_deleted', 0)->first();

                if ($kemitraanRow && (!$astap->relationLoaded('kemitraan') || !$astap->kemitraan)) {
                    $astap->setRelation('kemitraan', $kemitraanRow);
                }

                $statusKonsesi = $kemitraanRow?->status_konsesi;
                if (in_array($statusKonsesi, ['Dikembalikan ke Rekanan', 'Dibatalkan'])) {
                    $targetRoute = request('from') === 'astap' ? 'astap.index' : 'master.kemitraan';
                    return redirect()->route($targetRoute)
                        ->with('error', "Aset kemitraan '{$astap->nama_barang}' berstatus {$statusKonsesi} dan terkunci dari perubahan.");
                }

                $spec = is_array($astap?->spesifikasi_json) ? $astap->spesifikasi_json : (json_decode($astap?->spesifikasi_json ?? '[]', true) ?: []);
                $queryTipe = $request->query('tipe');
                if ($queryTipe && in_array($queryTipe, ['ditambahkan', 'dimanfaatkan'])) {
                    $tipe = $queryTipe;
                } elseif (!empty($kemitraanRow?->tipe_kemitraan) && in_array($kemitraanRow->tipe_kemitraan, ['ditambahkan', 'dimanfaatkan'])) {
                    $tipe = $kemitraanRow->tipe_kemitraan;
                } elseif (!empty($spec['tipe_kemitraan']) && in_array($spec['tipe_kemitraan'], ['ditambahkan', 'dimanfaatkan'])) {
                    $tipe = $spec['tipe_kemitraan'];
                } elseif (!empty($kemitraanRow?->objek_astap_id)) {
                    $tipe = 'ditambahkan';
                } elseif ($astap?->is_reklas || !empty($spec['split_parent_id'])) {
                    $tipe = 'dimanfaatkan';
                } else {
                    $nb = strtolower($astap?->nama_barang ?? '');
                    if (str_starts_with($nb, 'sewa ') || str_contains($nb, 'pemanfaatan')) {
                        $tipe = 'dimanfaatkan';
                    } else {
                        $tipe = 'ditambahkan';
                    }
                }

                return view('pages.kemitraan.form', compact(
                    'astap', 'dbMaster108', 'dbUnits', 'dbPenyedias', 'dbPejabats',
                    'dbMitraKemitraans', 'dbPpkKemitraans', 'dbObjekAsetKemitraan', 'tipe'
                ));
            })->name('astap.edit_kemitraan');

            Route::match(['put', 'post'], '/astap/update-kemitraan/{id}', function (\Illuminate\Http\Request $request, $id) {
                // Normalisasi input JSON string jika dikirim via multipart FormData
                foreach (['mesin_items', 'tanah_items', 'gedung_items', 'jaringan_items', 'lainnya_items', 'spesifikasi_json'] as $jsonField) {
                    if ($request->has($jsonField) && is_string($request->input($jsonField))) {
                        $decoded = json_decode($request->input($jsonField), true);
                        if (is_array($decoded)) {
                            $request->merge([$jsonField => $decoded]);
                        }
                    }
                }

                $astap = \App\Models\Astap::with(['registers', 'kemitraan', 'reklas'])->findOrFail($id);
                if (in_array($astap->kemitraan?->status_konsesi, ['Dikembalikan ke Rekanan', 'Dibatalkan'])) {
                    $statusTxt = $astap->kemitraan->status_konsesi;
                    if ($request->ajax() || $request->wantsJson()) {
                        return response()->json([
                            'success' => false,
                            'message' => "Aset kemitraan '{$astap->nama_barang}' berstatus {$statusTxt} dan terkunci dari perubahan.",
                        ], 422);
                    }
                    return redirect()->route('master.kemitraan')
                        ->with('error', "Aset kemitraan '{$astap->nama_barang}' berstatus {$statusTxt} dan terkunci dari perubahan.");
                }

                // Sanitasi input tanggal: tangani ISO string (misal 2026-09-29T17:00:00.000000Z) dari datepicker
                $cleanDateInput = function($val) {
                    if (empty($val)) return null;
                    $val = trim((string)$val);
                    if (strpos($val, 'T') !== false) {
                        $val = explode('T', $val)[0];
                    }
                    return $val;
                };

                $dateMerges = [];
                foreach (['tanggal_pks', 'tanggal_mulai', 'tanggal_selesai'] as $df) {
                    if ($request->has($df) && !empty($request->input($df))) {
                        $dateMerges[$df] = $cleanDateInput($request->input($df));
                    }
                }
                if (!empty($dateMerges)) {
                    $request->merge($dateMerges);
                }

                if (!$request->filled('satuan')) {
                    $request->merge(['satuan' => 'Unit']);
                }

                $data = $request->validate([
                    'nama_barang'          => 'required|string|max:500',
                    'jenis_astap_id'       => 'required|integer|exists:jenis_astaps,id',
                    'tahun_perolehan'      => 'required|integer|min:1990|max:2100',
                    'jumlah_volume'        => 'required|integer|min:1',
                    'satuan'               => 'required|string|max:100',
                    'total_realisasi'      => 'required|numeric|min:0',
                    'triwulan'             => 'required|string|in:TW I,TW II,TW III,TW IV',
                    'skema_kemitraan'      => 'nullable|string|in:Sewa,KSO,KSP,BGS,BSG,BGS/BSG,KSPI,KSO/KSP',
                    'mitra_nama'           => 'required|string|max:500',
                    'mitra_pimpinan'       => 'nullable|string|max:255',
                    'mitra_alamat'         => 'nullable|string|max:1000',
                    'ppk_nama'             => 'nullable|string|max:255',
                    'ppk_nip'              => 'nullable|string|max:100',
                    'nomor_pks'            => 'required|string|max:255',
                    'tanggal_pks'          => ['required', 'string', 'regex:/^(\d{1,2}[\/\-]\d{1,2}[\/\-]\d{4}|\d{4}[\/\-]\d{1,2}[\/\-]\d{1,2})/'],
                    'tanggal_mulai'        => 'nullable|string|max:50',
                    'tanggal_selesai'      => 'nullable|string|max:50',
                    'kemitraan_keterangan' => 'nullable|string|max:2000',
                    'unit_id'              => 'nullable|integer|exists:units,id',
                    'alamat_barang'        => 'nullable|string|max:1000',
                    'kondisi'              => 'nullable|string|in:Baik,Kurang Baik,Rusak Berat',
                    'dokumen_file'         => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240',
                    'objek_astap_id'       => 'nullable|integer|exists:astaps,id',
                    'objek_register_id'    => 'nullable|integer|exists:astap_registers,id',
                    'objek_nibar'          => 'nullable|string|max:50',
                ]);

                $parseDateHelper = function($val) {
                    if (empty($val)) return null;
                    $val = trim($val);
                    if (preg_match('/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})$/', $val, $m)) {
                        return strtotime(sprintf('%04d-%02d-%02d 00:00:00', $m[3], $m[2], $m[1]));
                    }
                    if (preg_match('/^(\d{4})[\/\-](\d{1,2})[\/\-](\d{1,2})$/', $val, $m)) {
                        return strtotime(sprintf('%04d-%02d-%02d 00:00:00', $m[1], $m[2], $m[3]));
                    }
                    $t = strtotime(str_replace('/', '-', $val));
                    return $t ? strtotime(date('Y-m-d 00:00:00', $t)) : null;
                };

                $todayTimestamp = strtotime(date('Y-m-d 23:59:59'));
                $tPks = $parseDateHelper($data['tanggal_pks']);

                if ($tPks && $tPks > $todayTimestamp) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Tanggal penandatanganan PKS tidak boleh melebihi tanggal hari ini.',
                        'errors'  => [
                            'tanggal_pks' => ['Tanggal penandatanganan PKS tidak boleh melebihi tanggal hari ini.']
                        ]
                    ], 422);
                }

                // Validasi Tanggal Mulai & Selesai Kerjasama
                // Fleksibel untuk kontrak di masa depan maupun berlaku surut administratif
                if (!empty($data['tanggal_mulai'])) {
                    $tMulai = $parseDateHelper($data['tanggal_mulai']);
                }

                if (!empty($data['tanggal_mulai']) && !empty($data['tanggal_selesai'])) {
                    $tMulai = $parseDateHelper($data['tanggal_mulai']);
                    $tSelesai = $parseDateHelper($data['tanggal_selesai']);
                    if ($tMulai && $tSelesai && $tSelesai < $tMulai) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Tanggal berakhir kerjasama tidak boleh di bawah (lebih awal dari) tanggal mulai kerjasama.',
                            'errors'  => [
                                'tanggal_selesai' => ['Tanggal berakhir kerjasama tidak boleh di bawah (lebih awal dari) tanggal mulai kerjasama.']
                            ]
                        ], 422);
                    }
                }

                // Validasi Overlap PKS Aktif pada Objek Aset RSUD Terkait (Bug B8 Prevention)
                if (!empty($data['objek_register_id'])) {
                    $tMulaiVal = !empty($data['tanggal_mulai']) ? $parseDateHelper($data['tanggal_mulai']) : $tPks;
                    $tSelesaiVal = !empty($data['tanggal_selesai']) ? $parseDateHelper($data['tanggal_selesai']) : null;

                    $activeOverlap = \App\Models\AstapKemitraan::where('objek_register_id', $data['objek_register_id'])
                        ->where('status_konsesi', 'Aktif')
                        ->where('is_deleted', 0)
                        ->where('astap_id', '!=', $astap->id)
                        ->where('nomor_pks', '!=', trim($data['nomor_pks']))
                        ->first();

                    if ($activeOverlap) {
                        $exMulai = $activeOverlap->tanggal_mulai ? strtotime($activeOverlap->tanggal_mulai->format('Y-m-d 00:00:00')) : ($activeOverlap->tanggal_pks ? strtotime($activeOverlap->tanggal_pks->format('Y-m-d 00:00:00')) : null);
                        $exSelesai = $activeOverlap->tanggal_selesai ? strtotime($activeOverlap->tanggal_selesai->format('Y-m-d 23:59:59')) : null;

                        $isOverlap = false;
                        if (!$exSelesai || !$tSelesaiVal) {
                            $isOverlap = true;
                        } else {
                            if ($tMulaiVal <= $exSelesai && $tSelesaiVal >= $exMulai) {
                                $isOverlap = true;
                            }
                        }

                        if ($isOverlap) {
                            return response()->json([
                                'success' => false,
                                'message' => 'Aset objek RSUD ini (NIBAR: ' . ($data['objek_nibar'] ?? $activeOverlap->objek_nibar) . ') sedang terikat perjanjian kerja sama aktif lain: ' . $activeOverlap->nomor_pks . ' (' . $activeOverlap->mitra_nama . ').',
                                'errors'  => [
                                    'objek_register_id' => ['Aset ini masih dalam masa konsesi aktif pada PKS ' . $activeOverlap->nomor_pks]
                                ]
                            ], 422);
                        }
                    }
                }

                $totalRealisasi = (float) $data['total_realisasi'];
                $totalVolume    = max(1, (int) $data['jumlah_volume']);
                $hargaSatuan    = $totalRealisasi / $totalVolume;
                $tahun          = (int) $data['tahun_perolehan'];
                $kondisiItem    = (!empty($data['kondisi']) ? $data['kondisi'] : 'Baik');

                $isExtracom = $request->boolean('is_extracomtable');
                if (!$isExtracom && $request->has('mesin_items')) {
                    foreach ((array)$request->input('mesin_items') as $m) {
                        if (!empty($m['is_extracom'])) { $isExtracom = true; break; }
                    }
                }
                if (!$isExtracom && $request->has('lainnya_items')) {
                    foreach ((array)$request->input('lainnya_items') as $l) {
                        if (!empty($l['is_extracom'])) { $isExtracom = true; break; }
                    }
                }

                $specJson = is_array($astap->spesifikasi_json) ? $astap->spesifikasi_json : [];
                $specJson['sumber_dana']      = 'kemitraan';
                $specJson['is_extracomtable'] = $isExtracom;
                $specJson['mitra_nama']       = $data['mitra_nama'];
                $specJson['mitra_pimpinan']   = $data['mitra_pimpinan'] ?? null;
                $specJson['mitra_alamat']     = $data['mitra_alamat'] ?? null;
                $specJson['ppk_nama']         = $data['ppk_nama'] ?? null;
                $specJson['ppk_nip']          = $data['ppk_nip'] ?? null;
                $specJson['nomor_pks']        = $data['nomor_pks'];
                $specJson['tanggal_pks']      = $data['tanggal_pks'];
                $specJson['tanggal_mulai']    = $data['tanggal_mulai'] ?? null;
                $specJson['tanggal_selesai']  = $data['tanggal_selesai'] ?? null;
                $specJson['kondisi']          = $kondisiItem;
                $specJson['keterangan']       = $data['kemitraan_keterangan'] ?? null;

                if ($request->has('spesifikasi_json') && is_array($request->input('spesifikasi_json'))) {
                    $specJson = array_merge($specJson, $request->input('spesifikasi_json'));
                }

                $ja = \App\Models\JenisAstap::find($data['jenis_astap_id']);
                $kode108Raw = $ja ? ($ja->sub_sub_rincian_objek ?: ($ja->sub_rincian_objek ?: $ja->jenis)) : '1.5.2.00.00.00';
                $kode108Clean = str_replace('.', '', $kode108Raw);

                $kibCategory = null;
                if (!empty($specJson['kategori_kib'])) {
                    $catStr = strtoupper($specJson['kategori_kib']);
                    if (str_contains($catStr, 'KIB A') || str_contains($catStr, 'TANAH')) $kibCategory = 'KIB A';
                    elseif (str_contains($catStr, 'KIB B') || str_contains($catStr, 'MESIN') || str_contains($catStr, 'PERALATAN')) $kibCategory = 'KIB B';
                    elseif (str_contains($catStr, 'KIB C') || str_contains($catStr, 'GEDUNG') || str_contains($catStr, 'BANGUNAN')) $kibCategory = 'KIB C';
                    elseif (str_contains($catStr, 'KIB D') || str_contains($catStr, 'JARINGAN') || str_contains($catStr, 'JALAN') || str_contains($catStr, 'IRIGASI')) $kibCategory = 'KIB D';
                    elseif (str_contains($catStr, 'KIB E') || str_contains($catStr, 'LAINNYA') || str_contains($catStr, 'BUKU')) $kibCategory = 'KIB E';
                }

                if (!$kibCategory && !empty($kode108Raw)) {
                    if (str_ends_with($kode108Raw, '.005') || str_starts_with($kode108Raw, '1.3.5')) $kibCategory = 'KIB E';
                    elseif (str_ends_with($kode108Raw, '.002') || str_starts_with($kode108Raw, '1.3.2')) $kibCategory = 'KIB B';
                    elseif (str_ends_with($kode108Raw, '.003') || str_starts_with($kode108Raw, '1.3.3')) $kibCategory = 'KIB C';
                    elseif (str_ends_with($kode108Raw, '.004') || str_starts_with($kode108Raw, '1.3.4')) $kibCategory = 'KIB D';
                    elseif (str_ends_with($kode108Raw, '.001') || str_starts_with($kode108Raw, '1.3.1')) $kibCategory = 'KIB A';
                }
                if (!$kibCategory) $kibCategory = 'KIB B';

                $repeaterKeys = ['tanah_items', 'mesin_items', 'gedung_items', 'jaringan_items', 'lainnya_items', 'atb_items', 'kdp_items', 'merk', 'type', 'ukuran', 'bahan', 'no_pabrik', 'no_rangka', 'no_mesin', 'no_polisi', 'sertifikat_nomor'];
                foreach ($repeaterKeys as $rk) {
                    if ($request->has($rk) && !is_null($request->input($rk))) {
                        $specJson[$rk] = $request->input($rk);
                    }
                }

                // Bersihkan repeater array KIB yang tidak aktif agar tidak mencemari spesifikasi_json
                $inactiveKeys = [
                    'KIB A' => ['mesin_items', 'gedung_items', 'jaringan_items', 'lainnya_items'],
                    'KIB B' => ['tanah_items', 'gedung_items', 'jaringan_items', 'lainnya_items'],
                    'KIB C' => ['tanah_items', 'mesin_items', 'jaringan_items', 'lainnya_items'],
                    'KIB D' => ['tanah_items', 'mesin_items', 'gedung_items', 'lainnya_items'],
                    'KIB E' => ['tanah_items', 'mesin_items', 'gedung_items', 'jaringan_items'],
                ];
                foreach ($inactiveKeys[$kibCategory] ?? [] as $ik) {
                    unset($specJson[$ik]);
                }

                $mesinItems = ($kibCategory === 'KIB B') ? ($request->input('mesin_items') ?? ($specJson['mesin_items'] ?? null)) : null;
                if ($kibCategory === 'KIB B' && is_array($mesinItems) && count($mesinItems) > 0) {
                    foreach ($mesinItems as &$mi) {
                        $uk = $mi['mesin_ukuran'] ?? ($mi['mesin_ukuran_cc'] ?? ($mi['ukuran'] ?? ($mi['ukuran_cc'] ?? '')));
                        $mi['mesin_ukuran'] = $uk;
                        $mi['mesin_ukuran_cc'] = $uk;
                    }
                    unset($mi);
                    $specJson['mesin_items'] = $mesinItems;
                    $firstM = $mesinItems[0];
                    if (empty($specJson['merk'])) $specJson['merk'] = $firstM['mesin_merk'] ?? '';
                    if (empty($specJson['type'])) $specJson['type'] = $firstM['mesin_type'] ?? '';
                    if (empty($specJson['no_pabrik'])) $specJson['no_pabrik'] = $firstM['mesin_no_pabrik'] ?? '';
                    if (empty($specJson['bahan'])) $specJson['bahan'] = $firstM['mesin_bahan'] ?? '';
                    $ukuranM = $firstM['mesin_ukuran'] ?? ($firstM['mesin_ukuran_cc'] ?? ($firstM['ukuran'] ?? ($specJson['ukuran'] ?? '')));
                    $specJson['ukuran'] = $ukuranM;
                    $specJson['mesin_ukuran'] = $ukuranM;
                    $specJson['mesin_ukuran_cc'] = $ukuranM;
                    if (empty($specJson['tahun_pembuatan'])) $specJson['tahun_pembuatan'] = $firstM['mesin_tahun_pembuatan'] ?? null;
                    if (empty($specJson['kondisi'])) $specJson['kondisi'] = $firstM['mesin_kondisi'] ?? 'Baik';
                    if (empty($specJson['no_rangka'])) $specJson['no_rangka'] = $firstM['mesin_no_rangka'] ?? '';
                    if (empty($specJson['no_mesin'])) $specJson['no_mesin'] = $firstM['mesin_no_mesin'] ?? '';
                    if (empty($specJson['no_bpkb'])) $specJson['no_bpkb'] = $firstM['mesin_no_bpkb'] ?? '';
                    if (!empty($firstM['mesin_kode_barang'])) $specJson['mesin_kode_barang'] = $firstM['mesin_kode_barang'];
                }

                if ($kibCategory === 'KIB A' && $request->has('tanah_items') && is_array($request->input('tanah_items')) && count($request->input('tanah_items')) > 0) {
                    $tItems = $request->input('tanah_items');
                    $firstT = $tItems[0];
                    $totalLuas = array_sum(array_map(fn($it) => (float)($it['tanah_luas_m2'] ?? 0), $tItems));
                    $allSertifikat = array_filter(array_map(fn($it) => $it['tanah_sertifikat_no'] ?? null, $tItems));

                    $specJson['tanah_items'] = $tItems;
                    $specJson['luas_m2'] = $totalLuas;
                    $specJson['hak_tanah'] = $firstT['tanah_hak'] ?? 'Hak Pakai';
                    $specJson['sertifikat_no'] = count($allSertifikat) > 0 ? implode(', ', $allSertifikat) : ($firstT['tanah_sertifikat_no'] ?? null);
                    $specJson['sertifikat_tgl'] = $firstT['tanah_sertifikat_tgl'] ?? null;
                    $specJson['penggunaan'] = $firstT['tanah_penggunaan'] ?? null;
                    $specJson['tanah_jumlah_bidang'] = count($tItems);
                    if (!empty($firstT['tanah_kode_barang'])) $specJson['tanah_kode_barang'] = $firstT['tanah_kode_barang'];
                }

                if ($kibCategory === 'KIB C' && $request->has('gedung_items') && is_array($request->input('gedung_items')) && count($request->input('gedung_items')) > 0) {
                    $gItems = $request->input('gedung_items');
                    foreach ($gItems as &$gi) {
                        $gi['gedung_is_baru'] = $gi['gedung_is_baru'] ?? 'Lama';
                        $gi['is_baru'] = $gi['is_baru'] ?? $gi['gedung_is_baru'];
                        $luas = $gi['gedung_luas_lantai_m2'] ?? ($gi['gedung_luas_lantai'] ?? ($gi['luas_lantai_m2'] ?? 0));
                        $gi['gedung_luas_lantai'] = $luas;
                        $gi['gedung_luas_lantai_m2'] = $luas;
                        $dokNo = $gi['gedung_dokumen_nomor'] ?? ($gi['gedung_dokumen_no'] ?? ($gi['dokumen_nomor'] ?? ''));
                        $gi['gedung_dokumen_no'] = $dokNo;
                        $gi['gedung_dokumen_nomor'] = $dokNo;
                    }
                    unset($gi);
                    $firstG = $gItems[0];
                    $specJson['gedung_items'] = $gItems;
                    $specJson['gedung_is_baru'] = $firstG['gedung_is_baru'] ?? 'Lama';
                    $specJson['is_baru'] = $specJson['gedung_is_baru'];
                    $specJson['luas_lantai_m2'] = $firstG['gedung_luas_lantai_m2'] ?? 0;
                    $specJson['gedung_luas_lantai'] = $specJson['luas_lantai_m2'];
                    $specJson['dokumen_nomor'] = $firstG['gedung_dokumen_nomor'] ?? null;
                    $specJson['dokumen_no'] = $specJson['dokumen_nomor'];
                    if (!empty($firstG['gedung_kapitalisasi_tahun_induk'])) {
                        $specJson['gedung_kapitalisasi_tahun_induk'] = $firstG['gedung_kapitalisasi_tahun_induk'];
                        $specJson['kapitalisasi_tahun_induk'] = $firstG['gedung_kapitalisasi_tahun_induk'];
                    }
                    if (!empty($firstG['gedung_kode_barang'])) $specJson['gedung_kode_barang'] = $firstG['gedung_kode_barang'];
                }

                if ($kibCategory === 'KIB D' && $request->has('jaringan_items') && is_array($request->input('jaringan_items')) && count($request->input('jaringan_items')) > 0) {
                    $jItems = $request->input('jaringan_items');
                    foreach ($jItems as &$ji) {
                        $ji['jaringan_is_baru'] = $ji['jaringan_is_baru'] ?? 'Lama';
                        $ji['is_baru'] = $ji['is_baru'] ?? $ji['jaringan_is_baru'];
                        $panjang = $ji['jaringan_panjang'] ?? ($ji['jaringan_panjang_km'] ?? null);
                        $ji['jaringan_panjang'] = $panjang;
                        $ji['jaringan_panjang_km'] = $panjang;
                        $lebar = $ji['jaringan_lebar'] ?? ($ji['jaringan_lebar_m'] ?? null);
                        $ji['jaringan_lebar'] = $lebar;
                        $ji['jaringan_lebar_m'] = $lebar;
                        $luas = $ji['jaringan_luas'] ?? ($ji['jaringan_luas_m2'] ?? 0);
                        $ji['jaringan_luas'] = $luas;
                        $ji['jaringan_luas_m2'] = $luas;
                    }
                    unset($ji);
                    $firstJ = $jItems[0];
                    $specJson['jaringan_items'] = $jItems;
                    $specJson['jaringan_is_baru'] = $firstJ['jaringan_is_baru'] ?? 'Lama';
                    $specJson['is_baru'] = $specJson['jaringan_is_baru'];
                    $specJson['panjang_km'] = $firstJ['jaringan_panjang'] ?? null;
                    $specJson['jaringan_panjang'] = $specJson['panjang_km'];
                    $specJson['lebar_m'] = $firstJ['jaringan_lebar'] ?? null;
                    $specJson['jaringan_lebar'] = $specJson['lebar_m'];
                    $specJson['luas_m2'] = $firstJ['jaringan_luas'] ?? 0;
                    $specJson['jaringan_luas'] = $specJson['luas_m2'];
                    if (!empty($firstJ['jaringan_kapitalisasi_tahun_induk'])) {
                        $specJson['jaringan_kapitalisasi_tahun_induk'] = $firstJ['jaringan_kapitalisasi_tahun_induk'];
                        $specJson['kapitalisasi_tahun_induk'] = $firstJ['jaringan_kapitalisasi_tahun_induk'];
                    }
                    if (!empty($firstJ['jaringan_kode_barang'])) $specJson['jaringan_kode_barang'] = $firstJ['jaringan_kode_barang'];
                }

                if ($kibCategory === 'KIB E' && $request->has('lainnya_items') && is_array($request->input('lainnya_items')) && count($request->input('lainnya_items')) > 0) {
                    $lItems = $request->input('lainnya_items');
                    foreach ($lItems as &$li) {
                        $judul = $li['lainnya_judul'] ?? ($li['lainnya_judul_pencipta'] ?? ($li['lainnya_nama_barang'] ?? ''));
                        $li['lainnya_judul'] = $judul;
                        $li['lainnya_judul_pencipta'] = $judul;
                    }
                    unset($li);
                    $firstL = $lItems[0];
                    $specJson['lainnya_items'] = $lItems;
                    $specJson['judul'] = $firstL['lainnya_judul'] ?? null;
                    $specJson['pencipta'] = $firstL['lainnya_pencipta'] ?? null;
                    $specJson['spesifikasi'] = $firstL['lainnya_spesifikasi'] ?? null;
                    $specJson['bahan'] = $firstL['lainnya_bahan'] ?? null;
                    $specJson['ukuran'] = $firstL['lainnya_ukuran'] ?? null;
                    if (!empty($firstL['lainnya_kode_barang'])) $specJson['lainnya_kode_barang'] = $firstL['lainnya_kode_barang'];
                }

                // Sinkronisasi Alamat Fisik Barang (Khusus Tanah, Gedung, Jaringan)
                $firstT = ($request->has('tanah_items') && is_array($request->input('tanah_items')) && count($request->input('tanah_items')) > 0) ? $request->input('tanah_items')[0] : [];
                $firstG = ($request->has('gedung_items') && is_array($request->input('gedung_items')) && count($request->input('gedung_items')) > 0) ? $request->input('gedung_items')[0] : [];
                $firstJ = ($request->has('jaringan_items') && is_array($request->input('jaringan_items')) && count($request->input('jaringan_items')) > 0) ? $request->input('jaringan_items')[0] : [];

                $isTanahGedungJaringan = count($firstT) > 0 || count($firstG) > 0 || count($firstJ) > 0
                    || str_starts_with($astap->kode_108 ?? '', '1.5.2.01')
                    || str_starts_with($astap->kode_108 ?? '', '1.5.2.03')
                    || str_starts_with($astap->kode_108 ?? '', '1.5.2.04');

                $resolvedAlamat = null;
                if ($isTanahGedungJaringan) {
                    if (!empty($firstT['tanah_alamat'])) {
                        $resolvedAlamat = $firstT['tanah_alamat'];
                    } elseif ($request->filled('tanah_alamat')) {
                        $resolvedAlamat = $request->input('tanah_alamat');
                    } elseif (!empty($firstG['gedung_alamat'])) {
                        $resolvedAlamat = $firstG['gedung_alamat'];
                    } elseif ($request->filled('gedung_alamat')) {
                        $resolvedAlamat = $request->input('gedung_alamat');
                    } elseif (!empty($firstJ['jaringan_alamat'])) {
                        $resolvedAlamat = $firstJ['jaringan_alamat'];
                    } elseif ($request->filled('jaringan_alamat')) {
                        $resolvedAlamat = $request->input('jaringan_alamat');
                    } elseif (!empty($data['alamat_barang'])) {
                        $resolvedAlamat = $data['alamat_barang'];
                    }
                    $resolvedAlamat = $resolvedAlamat ?: ($astap->alamat_barang ?: 'RSUD Dr. H. Koesnandi');
                } else {
                    $resolvedAlamat = null;
                }

                // Handle Upload Berkas Dokumen BAST / PKS Kerja Sama (jika ada file baru)
                $dokumenPath = $astap->kemitraan?->dokumen_path ?? ($astap->spesifikasi_json['dokumen_path'] ?? null);
                if ($request->hasFile('dokumen_file')) {
                    $file = $request->file('dokumen_file');
                    $filename = 'BAST_KEMITRAAN_' . time() . '_' . \Illuminate\Support\Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
                    $dokumenPath = $file->storeAs('dokumen_kemitraan', $filename, 'public');
                }
                $resolvedObjekNibar = $data['objek_nibar'] ?? null;
                if (empty($resolvedObjekNibar) && !empty($data['objek_register_id'])) {
                    $resolvedObjekNibar = \App\Models\AstapRegister::where('id', $data['objek_register_id'])->value('nibar');
                }

                $specJson['dokumen_path'] = $dokumenPath;
                $specJson['objek_astap_id'] = $data['objek_astap_id'] ?? null;
                $specJson['objek_register_id'] = $data['objek_register_id'] ?? null;
                $specJson['objek_nibar'] = $resolvedObjekNibar;

                $linkedAsetMitrasCount = 0;
                \Illuminate\Support\Facades\DB::transaction(function () use ($astap, $data, $specJson, $resolvedAlamat, $totalVolume, $totalRealisasi, $hargaSatuan, $tahun, $kondisiItem, $isExtracom, $dokumenPath, $request, $kibCategory, &$linkedAsetMitrasCount, $resolvedObjekNibar) {
                    $astap->update([
                        'nama_barang'          => $data['nama_barang'],
                        'jenis_astap_id'       => $data['jenis_astap_id'],
                        'tahun_perolehan'      => $tahun,
                        'jumlah_volume'        => $totalVolume,
                        'satuan'               => $data['satuan'],
                        'harga_satuan'         => $hargaSatuan,
                        'jumlah_realisasi'     => $totalRealisasi,
                        'total_realisasi'      => $totalRealisasi,
                        'triwulan'             => $data['triwulan'],
                        'bast_dokumen_nomor'   => $data['nomor_pks'],
                        'bast_dokumen_tanggal' => \App\Models\Astap::parseDateInput($data['tanggal_pks']) ?? $data['tanggal_pks'],
                        'keterangan_tambahan'  => $data['kemitraan_keterangan'] ?? null,
                        'unit_id'              => $data['unit_id'] ?? null,
                        'alamat_barang'        => $resolvedAlamat,
                        'is_extracomtable'     => $isExtracom,
                        'ppk_nama'             => $data['ppk_nama'] ?? null,
                        'ppk_nip'              => $data['ppk_nip'] ?? null,
                        'spesifikasi_json'     => $specJson,
                    ]);

                    // Sinkronisasi record AstapKemitraan
                    $kemitraan = \App\Models\AstapKemitraan::firstOrNew(['astap_id' => $astap->id]);
                    $kemitraan->fill([
                        'objek_astap_id'    => $data['objek_astap_id'] ?? null,
                        'objek_register_id' => $data['objek_register_id'] ?? null,
                        'objek_nibar'       => $resolvedObjekNibar,
                        'mitra_nama'       => $data['mitra_nama'],
                        'mitra_pimpinan'   => $data['mitra_pimpinan'] ?? null,
                        'mitra_alamat'     => $data['mitra_alamat'] ?? null,
                        'nomor_pks'        => $data['nomor_pks'],
                        'tanggal_pks'      => $data['tanggal_pks'],
                        'skema_kemitraan'  => (function($s) {
                            $s = trim($s ?? 'Sewa');
                            $allowed = ['Sewa', 'KSO', 'KSP', 'BGS', 'BSG', 'BGS/BSG', 'KSPI', 'KSO/KSP'];
                            return in_array($s, $allowed) ? $s : 'Sewa';
                        })($data['skema_kemitraan'] ?? $request->input('skema_kemitraan', 'Sewa')),
                        'tanggal_mulai'    => $data['tanggal_mulai'] ?? null,
                        'tanggal_selesai'  => $data['tanggal_selesai'] ?? null,
                        'status_konsesi'   => $kemitraan->status_konsesi ?: 'Aktif',
                        'jumlah_volume'    => $totalVolume,
                        'satuan'           => $data['satuan'],
                        'nilai_aset'       => $totalRealisasi,
                        'tahun'            => $tahun,
                        'triwulan'         => $data['triwulan'],
                        'keterangan'       => $data['kemitraan_keterangan'] ?? null,
                        'tipe_kemitraan'   => $request->input('tipe_kemitraan', $data['tipe_kemitraan'] ?? ($kemitraan->tipe_kemitraan ?: 'dimanfaatkan')),
                        'dokumen_path'     => $dokumenPath,
                        'user_id'          => auth()->id(),
                    ]);
                    $kemitraan->save();

                    // =========================================================================
                    // OPSI A: CASCADE UPDATE OTOMATIS KE ASET YANG DITAMBAHKAN MITRA
                    // Jika data kontrak Objek Dimanfaatkan (Tabel 1) ini diubah, otomatis perbarui
                    // seluruh aset mitra yang tertaut padanya di Tabel 2 (nomor PKS, tanggal, mitra, masa konsesi)
                    // =========================================================================
                    if ($kemitraan->tipe_kemitraan === 'dimanfaatkan') {
                        $linkedAsetMitras = \App\Models\AstapKemitraan::where('objek_astap_id', $astap->id)
                            ->where('is_deleted', 0)
                            ->get();

                        if ($linkedAsetMitras->isNotEmpty()) {
                            $linkedAsetMitrasCount = $linkedAsetMitras->count();
                            foreach ($linkedAsetMitras as $childKemitraan) {
                                // 1. Update data kontrak di tabel astap_kemitraans anak
                                $childKemitraan->update([
                                    'nomor_pks'       => $data['nomor_pks'],
                                    'tanggal_pks'     => $data['tanggal_pks'],
                                    'mitra_nama'      => $data['mitra_nama'],
                                    'mitra_pimpinan'  => $data['mitra_pimpinan'] ?? null,
                                    'mitra_alamat'    => $data['mitra_alamat'] ?? null,
                                    'tanggal_mulai'   => $data['tanggal_mulai'] ?? null,
                                    'tanggal_selesai' => $data['tanggal_selesai'] ?? null,
                                    'skema_kemitraan' => $kemitraan->skema_kemitraan,
                                    'status_konsesi'  => $kemitraan->status_konsesi,
                                    'tahun'           => $tahun,
                                    'triwulan'        => $data['triwulan'],
                                ]);

                                // 2. Update dokumen & spesifikasi_json di tabel astaps milik anak
                                if ($childKemitraan->astap) {
                                    $childAstap = $childKemitraan->astap;
                                    $childSpec = $childAstap->spesifikasi_json ?? [];
                                    if (is_string($childSpec)) {
                                        $childSpec = json_decode($childSpec, true) ?: [];
                                    }

                                    $childSpec['nomor_pks']         = $data['nomor_pks'];
                                    $childSpec['tanggal_pks']       = $data['tanggal_pks'];
                                    $childSpec['mitra_nama']        = $data['mitra_nama'];
                                    $childSpec['mitra_pimpinan']    = $data['mitra_pimpinan'] ?? null;
                                    $childSpec['mitra_alamat']      = $data['mitra_alamat'] ?? null;
                                    $childSpec['tanggal_mulai']     = $data['tanggal_mulai'] ?? null;
                                    $childSpec['tanggal_selesai']   = $data['tanggal_selesai'] ?? null;
                                    $childSpec['skema_kemitraan']   = $kemitraan->skema_kemitraan;
                                    $childSpec['objek_nama_barang'] = $astap->nama_barang;

                                    $childAstap->update([
                                        'bast_dokumen_nomor'   => $data['nomor_pks'],
                                        'bast_dokumen_tanggal' => \App\Models\Astap::parseDateInput($data['tanggal_pks']) ?? $data['tanggal_pks'],
                                        'spesifikasi_json'     => $childSpec,
                                    ]);
                                }
                            }
                        }
                    }

                    // Sinkronisasi penempatan ruangan pada tabel AstapRegister yang sudah ada
                    $cleanRuang = function ($val) {
                        if (empty($val)) return null;
                        $s = trim($val);
                        if (stripos($s, 'Piere Tendean') !== false ||
                            stripos($s, 'RSUD Dr. H. Koesnandi Bondowoso, Jl') !== false ||
                            in_array($s, ['Belum Ditempatkan / Di Gudang', 'Gudang Aset', '-'])) {
                            return null;
                        }
                        return $s;
                    };

                    $isTanah = (isset($specJson['tanah_items']) && count($specJson['tanah_items']) > 0) || str_starts_with($astap->kode_108 ?? '', '1.5.2.01') || stripos($astap->nama_barang, 'tanah') !== false;
                    $isGedung = (isset($specJson['gedung_items']) && count($specJson['gedung_items']) > 0) || str_starts_with($astap->kode_108 ?? '', '1.5.2.03') || stripos($astap->nama_barang, 'gedung') !== false;
                    $isJaringan = (isset($specJson['jaringan_items']) && count($specJson['jaringan_items']) > 0) || str_starts_with($astap->kode_108 ?? '', '1.5.2.04') || stripos($astap->nama_barang, 'jaringan') !== false;

                    $normalizeKondisi = function($raw) use ($kondisiItem) {
                        $rawK = strtoupper(trim((string)($raw ?: $kondisiItem)));
                        return ($rawK === 'KB' || $rawK === 'KURANG BAIK' || $rawK === 'RR' || $rawK === 'RUSAK RINGAN') 
                            ? 'Kurang Baik' 
                            : (($rawK === 'RB' || $rawK === 'RUSAK BERAT' || $rawK === 'RUSAK') ? 'Rusak Berat' : 'Baik');
                    };

                    $unitSpecs = [];
                    if ($kibCategory === 'KIB C' && isset($specJson['gedung_items']) && is_array($specJson['gedung_items']) && count($specJson['gedung_items']) > 0) {
                        foreach ($specJson['gedung_items'] as $gItem) {
                            $qty = max(1, (int)($gItem['gedung_jumlah_bangunan'] ?? 1));
                            $kStr = $normalizeKondisi($gItem['gedung_kondisi'] ?? null);
                            for ($q = 0; $q < $qty; $q++) {
                                $unitSpecs[] = ['kondisi' => $kStr, 'ruang' => null];
                            }
                        }
                    } elseif ($kibCategory === 'KIB A' && isset($specJson['tanah_items']) && is_array($specJson['tanah_items']) && count($specJson['tanah_items']) > 0) {
                        foreach ($specJson['tanah_items'] as $tItem) {
                            $qty = max(1, (int)($tItem['tanah_jumlah_barang'] ?? 1));
                            $kStr = $normalizeKondisi($tItem['tanah_kondisi'] ?? null);
                            for ($q = 0; $q < $qty; $q++) {
                                $unitSpecs[] = ['kondisi' => $kStr, 'ruang' => null];
                            }
                        }
                    } elseif ($kibCategory === 'KIB D' && isset($specJson['jaringan_items']) && is_array($specJson['jaringan_items']) && count($specJson['jaringan_items']) > 0) {
                        foreach ($specJson['jaringan_items'] as $jItem) {
                            $qty = max(1, (int)($jItem['jaringan_jumlah'] ?? 1));
                            $kStr = $normalizeKondisi($jItem['jaringan_kondisi'] ?? null);
                            for ($q = 0; $q < $qty; $q++) {
                                $unitSpecs[] = ['kondisi' => $kStr, 'ruang' => null];
                            }
                        }
                    } elseif ($kibCategory === 'KIB E' && isset($specJson['lainnya_items']) && is_array($specJson['lainnya_items']) && count($specJson['lainnya_items']) > 0) {
                        foreach ($specJson['lainnya_items'] as $lItem) {
                            $qty = max(1, (int)($lItem['lainnya_jumlah'] ?? 1));
                            $kStr = $normalizeKondisi($lItem['lainnya_kondisi'] ?? null);
                            for ($q = 0; $q < $qty; $q++) {
                                $unitSpecs[] = ['kondisi' => $kStr, 'ruang' => null];
                            }
                        }
                    } elseif (isset($specJson['mesin_items']) && is_array($specJson['mesin_items']) && count($specJson['mesin_items']) > 0) {
                        foreach ($specJson['mesin_items'] as $mItem) {
                            $qty = max(1, (int)($mItem['mesin_jumlah_barang'] ?? 1));
                            $kStr = $normalizeKondisi($mItem['mesin_kondisi'] ?? null);
                            for ($q = 0; $q < $qty; $q++) {
                                $unitSpecs[] = ['kondisi' => $kStr, 'ruang' => null];
                            }
                        }
                    }

                    // Tentukan kondisi agregat spesifikasi_json
                    if (!empty($unitSpecs)) {
                        $allConds = array_column($unitSpecs, 'kondisi');
                        if (in_array('Rusak Berat', $allConds)) {
                            $overallKondisi = 'Rusak Berat';
                        } elseif (in_array('Kurang Baik', $allConds)) {
                            $overallKondisi = 'Kurang Baik';
                        } else {
                            $overallKondisi = 'Baik';
                        }
                    } else {
                        $overallKondisi = $normalizeKondisi($kondisiItem);
                    }
                    $specJson['kondisi'] = $overallKondisi;
                    $astap->update(['spesifikasi_json' => $specJson]);

                    $registers = \App\Models\AstapRegister::where('astap_id', $astap->id)->where('is_deleted', 0)->orderBy('id')->get();
                    foreach ($registers as $regIdx => $reg) {
                        $targetKondisi = isset($unitSpecs[$regIdx]) ? $unitSpecs[$regIdx]['kondisi'] : $overallKondisi;

                        $isDistributed = \App\Models\DistribusiItemRegister::where('astap_register_id', $reg->id)->exists();
                        if ($isDistributed) {
                            $reg->update([
                                'tahun_perolehan' => $tahun,
                                'kondisi'         => $targetKondisi,
                            ]);
                        } else {
                            $reg->update([
                                'unit_id'         => null,
                                'ruang_pemegang'  => null,
                                'tahun_perolehan' => $tahun,
                                'kondisi'         => $targetKondisi,
                                'status'          => 'Tersedia',
                            ]);
                        }
                    }

                    // Sinkronisasi Penambahan / Pengurangan Register jika Volume Berubah saat Edit
                    $targetVolume = max(1, (int) $data['jumlah_volume']);
                    $currentRegCount = $registers->count();

                    if ($targetVolume > $currentRegCount) {
                        $ja = \App\Models\JenisAstap::find($astap->jenis_astap_id);
                        $kode108Raw = $ja ? ($ja->sub_sub_rincian_objek ?: ($ja->sub_rincian_objek ?: $ja->jenis)) : '1.5.2.00.00.00';
                        $kode108Clean = str_replace('.', '', $kode108Raw);

                        $runningRegNum = (int) (\App\Models\AstapRegister::where('tahun_perolehan', $tahun)
                            ->whereHas('astap', function ($sq) use ($astap) {
                                $sq->where('jenis_astap_id', $astap->jenis_astap_id);
                            })
                            ->max('no_register_int') ?? 0);

                        $nibarPrefix = "1201351102000000280000{$tahun}{$kode108Clean}";
                        $latestNibar = \App\Models\AstapRegister::where('nibar', 'like', "{$nibarPrefix}%")
                            ->orderBy('nibar', 'desc')
                            ->value('nibar');

                        if ($latestNibar && strlen($latestNibar) >= 7) {
                            $lastNum = (int) substr($latestNibar, -7);
                            if ($lastNum > $runningRegNum) {
                                $runningRegNum = $lastNum;
                            }
                        }

                        for ($i = $currentRegCount; $i < $targetVolume; $i++) {
                            $runningRegNum++;
                            $noRegStr = str_pad($runningRegNum, 7, '0', STR_PAD_LEFT);
                            $nibar = "1201351102000000280000{$tahun}{$kode108Clean}{$noRegStr}";

                            while (\App\Models\AstapRegister::where('nibar', $nibar)->exists()) {
                                $runningRegNum++;
                                $noRegStr = str_pad($runningRegNum, 7, '0', STR_PAD_LEFT);
                                $nibar = "1201351102000000280000{$tahun}{$kode108Clean}{$noRegStr}";
                            }

                            $newKondisi = isset($unitSpecs[$i]) ? $unitSpecs[$i]['kondisi'] : $overallKondisi;
                            \App\Models\AstapRegister::create([
                                'astap_id'        => $astap->id,
                                'unit_id'         => null,
                                'tahun_perolehan' => $tahun,
                                'no_register_int' => $runningRegNum,
                                'no_register'     => $nibar,
                                'nibar'           => $nibar,
                                'qr_code_path'    => "/scan/{$nibar}",
                                'ruang_pemegang'  => null,
                                'kondisi'         => $newKondisi,
                                'status'          => 'Tersedia',
                                'is_deleted'      => 0,
                            ]);
                        }
                    } elseif ($targetVolume < $currentRegCount) {
                        $excessRegisters = \App\Models\AstapRegister::where('astap_id', $astap->id)
                            ->where('is_deleted', 0)
                            ->orderBy('id', 'desc')
                            ->take($currentRegCount - $targetVolume)
                            ->get();

                        foreach ($excessRegisters as $exReg) {
                            $hasDist = \App\Models\DistribusiItemRegister::where('astap_register_id', $exReg->id)->exists();
                            $hasMutasi = \App\Models\AstapMutasiRegister::where('astap_register_id', $exReg->id)->exists();
                            if (!$hasDist && !$hasMutasi) {
                                $exReg->softDelete('Penyesuaian volume aset kemitraan');
                            }
                        }
                    }
                });

                $isFromAstap = $request->input('from') === 'astap' || $request->query('from') === 'astap';
                $isMilikRsud = $isFromAstap
                    || ($astap?->is_reklas)
                    || ($astap?->kemitraan?->status_konsesi === 'Selesai / Reklasifikasi')
                    || ($astap?->kemitraan?->status_konsesi === 'Selesai')
                    || ($astap?->reklas && $astap->reklas->isNotEmpty());

                $targetRoute = ($isMilikRsud || $isFromAstap) ? route('astap.index') : route('master.kemitraan');

                $succMessage = $isMilikRsud 
                    ? 'Perubahan Data Aset Milik RSUD (Eks-Kemitraan) "' . $astap->nama_barang . '" berhasil disimpan!' 
                    : 'Perubahan Aset Kemitraan "' . $astap->nama_barang . '" berhasil disimpan!';
                if ($linkedAsetMitrasCount > 0) {
                    $succMessage = 'Perubahan Objek Kemitraan "' . $astap->nama_barang . '" beserta ' . $linkedAsetMitrasCount . ' aset mitra terkait berhasil diperbarui otomatis!';
                }

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => $succMessage,
                        'redirect' => $targetRoute
                    ]);
                }

                return redirect()->to($targetRoute)
                    ->with('success', $succMessage);
            })->name('astap.update_kemitraan');

        // Belanja Modal ASTAP CRUD & Register Management (Controller-based)
        Route::get('/astap/create', [AstapController::class, 'create'])->name('astap.create');
        Route::get('/astap/{id}/edit', [AstapController::class, 'edit'])->name('astap.edit');
        Route::post('/astap', [AstapController::class, 'store'])->name('astap.store');
        Route::post('/astap/check-subrincian-anggaran', [AstapController::class, 'checkSubRincianAnggaran'])->name('astap.checkSubRincianAnggaran');
        Route::post('/astap/check-duplicate', [AstapController::class, 'checkDuplicate'])->name('astap.checkDuplicate');
        Route::put('/astap/{id}', [AstapController::class, 'update'])->name('astap.update');
        Route::delete('/astap/{id}', [AstapController::class, 'destroy'])->name('astap.destroy');
        Route::post('/astap/{id}/restore', [AstapController::class, 'restore'])->name('astap.restore');
        Route::put('/astap-register/{id}', [AstapController::class, 'updateRegister'])->name('astap_register.update');
        Route::delete('/astap-register/{id}', [AstapController::class, 'destroyRegister'])->name('astap_register.destroy');
        Route::post('/astap/resequence-nibar', [AstapController::class, 'resequenceNibar'])->name('astap.resequence_nibar');
    });

    // Form Tambah, Simpan, Edit, Update & Hapus Unit / Paviliun
    Route::middleware('module:unit')->group(function () {
        Route::get('/unit-paviliun/create', [UnitController::class, 'create'])->name('unit.create');
        Route::post('/unit-paviliun', [UnitController::class, 'store'])->name('unit.store');
        Route::get('/unit-paviliun/{id}/edit', [UnitController::class, 'edit'])->name('unit.edit')->middleware('module:unit.edit');
        Route::put('/unit-paviliun/{id}', [UnitController::class, 'update'])->name('unit.update')->middleware('module:unit.edit');
        Route::delete('/unit-paviliun/{id}', [UnitController::class, 'destroy'])->name('unit.destroy')->middleware('module:unit.delete');
        Route::post('/unit-paviliun/{id}/restore', [UnitController::class, 'restore'])->name('unit.restore');
    });

    // Master Data Users CRUD Routes (Hanya Master Admin & Admin yang memiliki izin users)
    Route::middleware('module:users')->group(function () {
        Route::get('/master-data/users', [UserController::class, 'index'])->name('master.users');
        Route::post('/master-data/users', [UserController::class, 'store'])->name('master.users.store');
        Route::put('/master-data/users/{id}', [UserController::class, 'update'])->name('master.users.update');
        Route::delete('/master-data/users/{id}', [UserController::class, 'destroy'])->name('master.users.destroy')->middleware('module:users.delete');
        Route::post('/master-data/users/{id}/reset-password', [UserController::class, 'resetPassword'])->name('master.users.reset_password');
    });

    // Master Data Jenis ASTAP (Kode 108)
    Route::middleware('module:master_data.jenis_astap,astap')->group(function () {
        Route::get('/master-data/jenis-astap', [JenisAstapController::class, 'index'])->name('master.jenis_astap');
        Route::get('/master-data/jenis-astap/search-subsub', [JenisAstapController::class, 'searchSubSub'])->name('master.jenis_astap.search_subsub');
        Route::post('/master-data/jenis-astap', [JenisAstapController::class, 'store'])->name('master.jenis_astap.store');
        Route::post('/master-data/jenis-astap/import', [JenisAstapController::class, 'import'])->name('master.jenis_astap.import');
        Route::get('/master-data/jenis-astap/download-template', [JenisAstapController::class, 'downloadTemplate'])->name('master.jenis_astap.template');
        Route::put('/master-data/jenis-astap/{id}', [JenisAstapController::class, 'update'])->name('master.jenis_astap.update');
        Route::delete('/master-data/jenis-astap/{id}', [JenisAstapController::class, 'destroy'])->name('master.jenis_astap.destroy');
    });

    // Master Data SIPD (Jenis Pengadaan & Rekening Belanja)
    Route::middleware('module:master_data')->group(function () {
        Route::get('/master-data/jenis-pengadaan', [JenisPengadaanController::class, 'index'])->name('master.jenis_pengadaan');
        Route::post('/master-data/jenis-pengadaan', [JenisPengadaanController::class, 'store'])->name('master.jenis_pengadaan.store');
        Route::put('/master-data/jenis-pengadaan/{id}', [JenisPengadaanController::class, 'update'])->name('master.jenis_pengadaan.update');
        Route::delete('/master-data/jenis-pengadaan/{id}', [JenisPengadaanController::class, 'destroy'])->name('master.jenis_pengadaan.destroy');

        Route::get('/master-data/rekening-belanja', [RekeningBelanjaController::class, 'index'])->name('master.rekening_belanja');
        Route::post('/master-data/rekening-belanja', [RekeningBelanjaController::class, 'store'])->name('master.rekening_belanja.store');
        Route::put('/master-data/rekening-belanja/{id}', [RekeningBelanjaController::class, 'update'])->name('master.rekening_belanja.update');
        Route::delete('/master-data/rekening-belanja/{id}', [RekeningBelanjaController::class, 'destroy'])->name('master.rekening_belanja.destroy');

    });

    // Master Reklasifikasi Aset (PMDN 108)
    Route::middleware('module:astap.reklas')->group(function () {
        Route::get('/master-data/reklasifikasi', [\App\Http\Controllers\ReklasifikasiController::class, 'index'])->name('master.reklasifikasi');
        Route::post('/master-data/reklasifikasi', [\App\Http\Controllers\ReklasifikasiController::class, 'store'])->name('master.reklasifikasi.store');
        Route::delete('/master-data/reklasifikasi/{id}', [\App\Http\Controllers\ReklasifikasiController::class, 'destroy'])->name('master.reklasifikasi.destroy');
    });

    // Master Hibah Aset (Hibah Masuk & Hibah Keluar)
    Route::middleware('module:astap.hibah')->group(function () {
        Route::get('/master-data/hibah', [\App\Http\Controllers\HibahController::class, 'index'])->name('master.hibah');
        Route::get('/master-data/hibah/{id}/cetak', [\App\Http\Controllers\HibahController::class, 'cetak'])->name('master.hibah.cetak');
        Route::post('/master-data/hibah/keluar', [\App\Http\Controllers\HibahController::class, 'storeHibahKeluar'])->name('master.hibah.keluar');
        Route::delete('/master-data/hibah/{id}', [\App\Http\Controllers\HibahController::class, 'destroy'])->name('master.hibah.destroy');
    });

    // Master Kemitraan Aset (KSO, BGS, Sewa Akun 1.5.2)
    Route::middleware('module:astap.kemitraan')->group(function () {
        Route::get('/master-data/kemitraan', [\App\Http\Controllers\KemitraanController::class, 'index'])->name('master.kemitraan');
        Route::put('/master-data/kemitraan/{id}/status', [\App\Http\Controllers\KemitraanController::class, 'updateStatus'])->name('master.kemitraan.status');
        Route::post('/master-data/kemitraan/{id}/extend', [\App\Http\Controllers\KemitraanController::class, 'extendKonsesi'])->name('master.kemitraan.extend');
        Route::post('/master-data/kemitraan/extend-mitra-batch', [\App\Http\Controllers\KemitraanController::class, 'extendMitraBatch'])->name('master.kemitraan.extend_mitra_batch');
        Route::patch('/master-data/kemitraan/{id}', [\App\Http\Controllers\KemitraanController::class, 'update'])->name('master.kemitraan.update');
        Route::post('/master-data/kemitraan/{id}/kembalikan-ke-mitra', [\App\Http\Controllers\KemitraanController::class, 'kembalikanKeMitra'])->name('master.kemitraan.kembalikan');
        Route::post('/master-data/kemitraan/{id}/batal-kembalikan', [\App\Http\Controllers\KemitraanController::class, 'batalKembalikanKeMitra'])->name('master.kemitraan.batal_kembalikan');
        Route::post('/master-data/kemitraan/{id}/batalkan', [\App\Http\Controllers\KemitraanController::class, 'batalkanKemitraan'])->name('master.kemitraan.batalkan');
        Route::post('/master-data/kemitraan/{id}/batal-pembatalan', [\App\Http\Controllers\KemitraanController::class, 'batalPembatalanKemitraan'])->name('master.kemitraan.batal_pembatalan');
        Route::post('/master-data/kemitraan/{id}/serah-terima-rsud', [\App\Http\Controllers\KemitraanController::class, 'serahTerimaRsud'])->name('master.kemitraan.serah_terima');
        Route::delete('/master-data/kemitraan/{id}', [\App\Http\Controllers\KemitraanController::class, 'destroy'])->name('master.kemitraan.destroy');
    });

});
});



