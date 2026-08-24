<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\JenisAstapController;
use App\Http\Controllers\JenisPengadaanController;
use App\Http\Controllers\RekeningBelanjaController;
use App\Http\Controllers\UnitController;
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

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

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

// Dashboard Master Admin
Route::middleware(['auth', RoleMiddleware::class . ':master_admin'])->group(function () {
    Route::get('/master-admin/dashboard', function () {
        return view('dashboards.master_admin');
    })->name('masteradmin.dashboard');
});

// Dashboard Admin
Route::middleware(['auth', RoleMiddleware::class . ':admin'])->group(function () {
    Route::get('/admin/dashboard', function () {
        return view('dashboards.admin');
    })->name('admin.dashboard');
});

// Dashboard Sub Admin
Route::middleware(['auth', RoleMiddleware::class . ':sub_admin'])->group(function () {
    Route::get('/sub-admin/dashboard', function () {
        $user = Auth::user();
        $unit = null;
        if ($user->unit_id) {
            $unit = \App\Models\Unit::find($user->unit_id);
        }
        
        // Fallback jika akun subadmin universal / belum memiliki unit_id
        if (!$unit) {
            $unit = \App\Models\Unit::where('nama', 'like', '%IGD%')->first() 
                ?? \App\Models\Unit::where('nama', 'like', '%Melati%')->first()
                ?? \App\Models\Unit::first();
        }

        return view('dashboards.sub_admin', compact('unit', 'user'));
    })->name('subadmin.dashboard');
});

// Frontend Menu & Form Pages (Auth Protected)
Route::middleware('auth')->group(function () {
    
    // 1. Data ASTAP Pages
    Route::get('/astap', function () {
        $astaps = \App\Models\Astap::with(['registers', 'jenisAstap', 'rekeningBelanja', 'jenisPengadaan', 'unit'])
            ->orderBy('id', 'desc')
            ->get()
            ->map(function($a) {
                $spec = is_array($a->spesifikasi_json) ? $a->spesifikasi_json : (json_decode($a->spesifikasi_json, true) ?? []);
                $firstReg = $a->registers ? $a->registers->first() : null;
                return [
                    'id' => $a->id,
                    'category' => $a->category,
                    'kode_barang' => $a->kode_108,
                    'nama_barang' => $a->nama_barang,
                    'tahun_perolehan' => (string) $a->tahun_perolehan,
                    'volume_satuan' => $a->jumlah_volume . ' ' . ($a->satuan ?: 'Unit'),
                    'jenis_aset_nama' => $a->jenisAstap ? $a->jenisAstap->nama_jenis : ($a->category === 'ATB' ? 'ASET TIDAK BERWUJUD' : ($a->category === 'EXTRACOM' ? 'EKSTRAKOMTABEL (< RP 300.000)' : 'PERALATAN DAN MESIN')),
                    'merk' => $spec['merk'] ?? ($spec['buku_judul'] ?? ($spec['judul_lisensi'] ?? ($spec['konstruksi'] ?? '-'))),
                    'type' => $spec['type'] ?? ($spec['hak_tanah'] ?? ($spec['bertingkat'] ?? '-')),
                    'ukuran' => $spec['ukuran'] ?? (isset($spec['luas_m2']) ? $spec['luas_m2'] . ' m²' : ($spec['buku_spesifikasi'] ?? '-')),
                    'no_pabrik' => $spec['no_pabrik'] ?? ($spec['sertifikat_no'] ?? '-'),
                    'bahan' => $spec['bahan'] ?? '-',
                    'program_nama' => $a->jenisPengadaan ? $a->jenisPengadaan->program_nama : 'Program Penunjang Urusan Pemerintah Daerah',
                    'kegiatan_nama' => $a->jenisPengadaan ? $a->jenisPengadaan->kegiatan_nama : 'Peningkatan Pelayanan BLUD',
                    'sub_kegiatan_nama' => $a->jenisPengadaan ? $a->jenisPengadaan->sub_kegiatan_nama : 'Pelayanan dan Penunjang Pelayanan BLUD',
                    'rekening_nama' => $a->rekeningBelanja ? $a->rekeningBelanja->nama_belanja : 'Belanja Modal Aset Tetap',
                    'spk_nomor' => $a->spk_nomor,
                    'spk_tanggal' => $a->spk_tanggal ? $a->spk_tanggal->format('Y-m-d') : null,
                    'surat_pesanan_nomor' => $a->surat_pesanan_nomor,
                    'kwitansi_nomor' => $a->kwitansi_nomor,
                    'faktur_nomor' => $a->faktur_nomor,
                    'jumlah_realisasi' => 'Rp ' . number_format($a->total_realisasi, 0, ',', '.'),
                    'total_realisasi_num' => (float) $a->total_realisasi,
                    'kondisi' => $firstReg ? $firstReg->kondisi : 'Baik',
                    'asal_usul' => 'BLUD RSUD',
                    'keterangan' => $a->keterangan_tambahan,
                    'registers' => $a->registers ? $a->registers->map(function($r) {
                        return [
                            'id' => $r->id,
                            'no_register' => $r->no_register,
                            'nibar' => $r->nibar,
                            'ruang_pemegang' => $r->ruang_pemegang,
                            'kondisi' => $r->kondisi,
                            'status_mutasi' => $r->status_mutasi
                        ];
                    })->values() : []
                ];
            });
        return view('pages.data_astap', compact('astaps'));
    })->name('astap.index');

    // 2. Distribusi Pages & Forms
    Route::get('/distribusi', function () {
        $units = \App\Models\Unit::orderBy('id', 'asc')->get()->map(function($u, $idx) {
            return [
                'id' => $u->id,
                'kode' => $u->kode_unit ?: ('UNIT-' . str_pad($u->id, 3, '0', STR_PAD_LEFT)),
                'nama' => $u->nama,
                'tipe' => $u->tipe ?: 'Rawat Inap & Paviliun',
                'kepala' => $u->kepala,
                'nip' => $u->nip ?: '-',
                'jabatan' => 'Kepala / Penanggung Jawab ' . $u->nama
            ];
        });
        return view('pages.distribusi', compact('units'));
    })->name('distribusi.index');

    Route::get('/distribusi/create', function () {
        $units = \App\Models\Unit::orderBy('id', 'asc')->get()->map(function($u) {
            return [
                'id' => $u->id,
                'nama' => $u->nama,
                'tipe' => $u->tipe,
                'kepala' => $u->kepala,
                'nip' => $u->nip ?: '-',
                'jabatan' => 'Kepala / Penanggung Jawab ' . $u->nama
            ];
        });

        $jenisAstapList = \App\Models\JenisAstap::whereNotNull('nama_jenis')
            ->where('nama_jenis', '!=', '')
            ->where('nama_jenis', '!=', '-')
            ->select('jenis', 'nama_jenis')
            ->distinct()
            ->orderBy('jenis')
            ->get()
            ->filter(fn($j) => !empty(trim($j->nama_jenis ?? '')))
            ->map(function($j) {
                return [
                    'kode' => $j->jenis,
                    'nama' => trim($j->nama_jenis),
                ];
            })
            ->unique('nama')
            ->values();

        if ($jenisAstapList->isEmpty()) {
            $jenisAstapList = collect([
                ['kode' => '1.3.1', 'nama' => 'TANAH'],
                ['kode' => '1.3.2', 'nama' => 'PERALATAN DAN MESIN'],
                ['kode' => '1.3.3', 'nama' => 'GEDUNG DAN BANGUNAN'],
                ['kode' => '1.3.4', 'nama' => 'JALAN, IRIGASI DAN JARINGAN'],
                ['kode' => '1.3.5', 'nama' => 'ASET TETAP LAINNYA'],
                ['kode' => '1.3.6', 'nama' => 'KONSTRUKSI DALAM PENGERJAAN'],
                ['kode' => '1.5.3', 'nama' => 'ASET TIDAK BERWUJUD'],
            ]);
        }

        $astapList = \App\Models\Astap::with('jenisAstap')
            ->orderBy('nama_barang', 'asc')
            ->get()
            ->map(function($a) {
                $spec = is_array($a->spesifikasi_json) ? $a->spesifikasi_json : (json_decode($a->spesifikasi_json, true) ?? []);
                $merk = $spec['merk'] ?? ($spec['type'] ?? ($spec['konstruksi'] ?? ''));
                $jenisKode = $a->jenisAstap ? $a->jenisAstap->jenis : substr($a->kode_108, 0, 5);
                $jenisNama = $a->jenisAstap ? $a->jenisAstap->nama_jenis : '';
                return [
                    'id' => $a->id,
                    'kode' => $a->kode_108,
                    'nama' => $a->nama_barang,
                    'jenis_kode' => $jenisKode,
                    'jenis_nama' => $jenisNama,
                    'kategori' => $a->category,
                    'merk' => $merk,
                    'satuan' => $a->satuan ?: 'Unit',
                ];
            });

        $nibarList = \App\Models\AstapRegister::select('id', 'nibar', 'kode_108', 'ruang_pemegang', 'kondisi', 'status_mutasi')
            ->where('status_mutasi', 'Tersedia')
            ->orderBy('kode_108')
            ->orderBy('no_register_int')
            ->get()
            ->map(function($r) {
                return [
                    'id'      => $r->id,
                    'nibar'   => $r->nibar,
                    'kode'    => $r->kode_108,
                    'ruang'   => $r->ruang_pemegang ?: '-',
                    'kondisi' => $r->kondisi,
                    'status'  => $r->status_mutasi,
                ];
            });

        return view('pages.form_distribusi', compact('units', 'jenisAstapList', 'astapList', 'nibarList'));
    })->name('distribusi.create');

    Route::get('/distribusi/{id}/edit', function ($id) {
        $units = \App\Models\Unit::orderBy('id', 'asc')->get()->map(function($u) {
            return [
                'id' => $u->id,
                'nama' => $u->nama,
                'tipe' => $u->tipe,
                'kepala' => $u->kepala,
                'nip' => $u->nip ?: '-',
                'jabatan' => 'Kepala / Penanggung Jawab ' . $u->nama
            ];
        });

        $jenisAstapList = \App\Models\JenisAstap::whereNotNull('nama_jenis')
            ->where('nama_jenis', '!=', '')
            ->where('nama_jenis', '!=', '-')
            ->select('jenis', 'nama_jenis')
            ->distinct()
            ->orderBy('jenis')
            ->get()
            ->filter(fn($j) => !empty(trim($j->nama_jenis ?? '')))
            ->map(function($j) {
                return [
                    'kode' => $j->jenis,
                    'nama' => trim($j->nama_jenis),
                ];
            })
            ->unique('nama')
            ->values();

        if ($jenisAstapList->isEmpty()) {
            $jenisAstapList = collect([
                ['kode' => '1.3.1', 'nama' => 'TANAH'],
                ['kode' => '1.3.2', 'nama' => 'PERALATAN DAN MESIN'],
                ['kode' => '1.3.3', 'nama' => 'GEDUNG DAN BANGUNAN'],
                ['kode' => '1.3.4', 'nama' => 'JALAN, IRIGASI DAN JARINGAN'],
                ['kode' => '1.3.5', 'nama' => 'ASET TETAP LAINNYA'],
                ['kode' => '1.3.6', 'nama' => 'KONSTRUKSI DALAM PENGERJAAN'],
                ['kode' => '1.5.3', 'nama' => 'ASET TIDAK BERWUJUD'],
            ]);
        }

        $astapList = \App\Models\Astap::with('jenisAstap')
            ->orderBy('nama_barang', 'asc')
            ->get()
            ->map(function($a) {
                $spec = is_array($a->spesifikasi_json) ? $a->spesifikasi_json : (json_decode($a->spesifikasi_json, true) ?? []);
                $merk = $spec['merk'] ?? ($spec['type'] ?? ($spec['konstruksi'] ?? ''));
                $jenisKode = $a->jenisAstap ? $a->jenisAstap->jenis : substr($a->kode_108, 0, 5);
                $jenisNama = $a->jenisAstap ? $a->jenisAstap->nama_jenis : '';
                return [
                    'id' => $a->id,
                    'kode' => $a->kode_108,
                    'nama' => $a->nama_barang,
                    'jenis_kode' => $jenisKode,
                    'jenis_nama' => $jenisNama,
                    'kategori' => $a->category,
                    'merk' => $merk,
                    'satuan' => $a->satuan ?: 'Unit',
                ];
            });

        $nibarList = \App\Models\AstapRegister::select('id', 'nibar', 'kode_108', 'ruang_pemegang', 'kondisi', 'status_mutasi')
            ->where('status_mutasi', 'Tersedia')
            ->orderBy('kode_108')
            ->orderBy('no_register_int')
            ->get()
            ->map(function($r) {
                return [
                    'id'      => $r->id,
                    'nibar'   => $r->nibar,
                    'kode'    => $r->kode_108,
                    'ruang'   => $r->ruang_pemegang ?: '-',
                    'kondisi' => $r->kondisi,
                    'status'  => $r->status_mutasi,
                ];
            });

        return view('pages.form_distribusi', compact('units', 'jenisAstapList', 'astapList', 'nibarList', 'id'));
    })->name('distribusi.edit');

    // 4. Mutasi Aset Pages & Forms
    Route::get('/mutasi-aset', function () {
        return view('pages.mutasi_aset');
    })->name('mutasi.index');

    Route::get('/mutasi-aset/create', function () {
        return view('pages.form_mutasi_aset');
    })->name('mutasi.create');

    Route::get('/mutasi-aset/{id}/edit', function ($id) {
        return view('pages.form_mutasi_aset', ['id' => $id]);
    })->name('mutasi.edit');

    // 5. Unit & Paviliun Index (Read-only for Sub Admin, full for Admin)
    Route::get('/unit-paviliun', [UnitController::class, 'index'])->name('unit.index');

    // 6. Pemeliharaan Index (Read-only for Sub Admin, full for Admin)
    Route::get('/pemeliharaan', function () {
        return view('pages.pemeliharaan');
    })->name('pemeliharaan.index');

    // Rute Khusus Master Admin & Admin Operasional (Sub Admin Dibatasi)
    Route::middleware([RoleMiddleware::class . ':master_admin,admin'])->group(function () {
        // Berita Acara (BAST)
        Route::get('/berita-acara', function () {
            return view('pages.berita_acara');
        })->name('bast.index');

        Route::get('/berita-acara/create', function () {
            return view('pages.form_berita_acara');
        })->name('bast.create');

        Route::get('/berita-acara/{id}/edit', function ($id) {
            return view('pages.form_berita_acara', ['id' => $id]);
        })->name('bast.edit');

        // Form Tambah & Edit ASTAP
        Route::get('/astap/create', function () {
            $dbMaster108 = \App\Models\JenisAstap::getNested108();
            $dbJenisPengadaans = \App\Models\JenisPengadaan::all();
            $dbRekeningBelanjas = \App\Models\RekeningBelanja::all();
            return view('pages.form_astap', compact('dbMaster108', 'dbJenisPengadaans', 'dbRekeningBelanjas'));
        })->name('astap.create');

        Route::get('/astap/{id}/edit', function ($id) {
            $dbMaster108 = \App\Models\JenisAstap::getNested108();
            $dbJenisPengadaans = \App\Models\JenisPengadaan::all();
            $dbRekeningBelanjas = \App\Models\RekeningBelanja::all();
            return view('pages.form_astap', [
                'id' => $id, 
                'dbMaster108' => $dbMaster108,
                'dbJenisPengadaans' => $dbJenisPengadaans,
                'dbRekeningBelanjas' => $dbRekeningBelanjas
            ]);
        })->name('astap.edit');

        Route::delete('/astap/{id}', function ($id) {
            $astap = \App\Models\Astap::find($id);
            if ($astap) {
                $astap->registers()->delete();
                $astap->delete();
            }
            return response()->json(['success' => true, 'message' => 'Data ASTAP berhasil dihapus.']);
        })->name('astap.destroy');

        // Form Tambah, Simpan, Edit, Update & Hapus Unit / Paviliun
        Route::get('/unit-paviliun/create', [UnitController::class, 'create'])->name('unit.create');
        Route::post('/unit-paviliun', [UnitController::class, 'store'])->name('unit.store');
        Route::get('/unit-paviliun/{id}/edit', [UnitController::class, 'edit'])->name('unit.edit');
        Route::put('/unit-paviliun/{id}', [UnitController::class, 'update'])->name('unit.update');
        Route::delete('/unit-paviliun/{id}', [UnitController::class, 'destroy'])->name('unit.destroy');

        // Form Tambah & Edit Pemeliharaan
        Route::get('/pemeliharaan/create', function () {
            return view('pages.form_pemeliharaan');
        })->name('pemeliharaan.create');

        Route::get('/pemeliharaan/{id}/edit', function ($id) {
            return view('pages.form_pemeliharaan', ['id' => $id]);
        })->name('pemeliharaan.edit');
    });

    // Master Data Users CRUD Routes
    Route::get('/master-data/users', [UserController::class, 'index'])->name('master.users');
    Route::post('/master-data/users', [UserController::class, 'store'])->name('master.users.store');
    Route::put('/master-data/users/{id}', [UserController::class, 'update'])->name('master.users.update');
    Route::delete('/master-data/users/{id}', [UserController::class, 'destroy'])->name('master.users.destroy');
    Route::post('/master-data/users/{id}/reset-password', [UserController::class, 'resetPassword'])->name('master.users.reset_password');

    Route::get('/master-data/jenis-astap', [JenisAstapController::class, 'index'])->name('master.jenis_astap');
    Route::post('/master-data/jenis-astap', [JenisAstapController::class, 'store'])->name('master.jenis_astap.store');
    Route::post('/master-data/jenis-astap/import', [JenisAstapController::class, 'import'])->name('master.jenis_astap.import');
    Route::get('/master-data/jenis-astap/download-template', [JenisAstapController::class, 'downloadTemplate'])->name('master.jenis_astap.template');
    Route::put('/master-data/jenis-astap/{id}', [JenisAstapController::class, 'update'])->name('master.jenis_astap.update');
    Route::delete('/master-data/jenis-astap/{id}', [JenisAstapController::class, 'destroy'])->name('master.jenis_astap.destroy');

    // Master Data Jenis Pengadaan (SIPD)
    Route::get('/master-data/jenis-pengadaan', [JenisPengadaanController::class, 'index'])->name('master.jenis_pengadaan');
    Route::post('/master-data/jenis-pengadaan', [JenisPengadaanController::class, 'store'])->name('master.jenis_pengadaan.store');
    Route::put('/master-data/jenis-pengadaan/{id}', [JenisPengadaanController::class, 'update'])->name('master.jenis_pengadaan.update');
    Route::delete('/master-data/jenis-pengadaan/{id}', [JenisPengadaanController::class, 'destroy'])->name('master.jenis_pengadaan.destroy');

    // Master Data Rekening Belanja (SIPD)
    Route::get('/master-data/rekening-belanja', [RekeningBelanjaController::class, 'index'])->name('master.rekening_belanja');
    Route::post('/master-data/rekening-belanja', [RekeningBelanjaController::class, 'store'])->name('master.rekening_belanja.store');
    Route::put('/master-data/rekening-belanja/{id}', [RekeningBelanjaController::class, 'update'])->name('master.rekening_belanja.update');
    Route::delete('/master-data/rekening-belanja/{id}', [RekeningBelanjaController::class, 'destroy'])->name('master.rekening_belanja.destroy');
});
