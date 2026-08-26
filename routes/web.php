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
                            'no_register' => $r->nibar ?: $r->no_register,
                            'nibar' => $r->nibar,
                            'ruang_pemegang' => $r->ruang_pemegang,
                            'kondisi' => $r->kondisi,
                            'status_mutasi' => $r->status_mutasi,
                            'qr_code_path' => $r->qr_code_path
                        ];
                    })->values() : []
                ];
            });
        return view('pages.data_astap', compact('astaps'));
    })->name('astap.index');

    // 2. Distribusi Pages & Forms
    Route::get('/distribusi', [DistribusiController::class, 'index'])->name('distribusi.index');
    Route::get('/distribusi/create', [DistribusiController::class, 'create'])->name('distribusi.create');
    Route::get('/distribusi/{id}/edit', [DistribusiController::class, 'edit'])->name('distribusi.edit');
    Route::post('/distribusi/save', [DistribusiController::class, 'saveDistribusi'])->name('distribusi.save');

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
            $astap = \App\Models\Astap::with(['registers', 'jenisAstap', 'rekeningBelanja', 'jenisPengadaan'])->find($id);
            return view('pages.form_astap', [
                'id' => $id, 
                'astap' => $astap,
                'dbMaster108' => $dbMaster108,
                'dbJenisPengadaans' => $dbJenisPengadaans,
                'dbRekeningBelanjas' => $dbRekeningBelanjas
            ]);
        })->name('astap.edit');

        Route::post('/astap', function (\Illuminate\Http\Request $request) {
            $data = $request->all();
            
            $jenisPengadaanId = $data['jenis_pengadaan_id'] ?? null;
            if (!$jenisPengadaanId && !empty($data['sub_kegiatan_kode'])) {
                $jenisPengadaanId = \App\Models\JenisPengadaan::where('sub_kegiatan_kode', 'LIKE', '%'.$data['sub_kegiatan_kode'].'%')->value('id');
            }
            $rekeningBelanjaId = null;
            if (!empty($data['kode_rek'])) {
                $rekeningBelanjaId = \App\Models\RekeningBelanja::where('kode_rek', $data['kode_rek'])->value('id');
            }

            // Dapatkan Kode 108 Sub-Sub Rincian berdasarkan jenis aset yang dipilih
            $jenisPrefix = substr($data['jenis_aset_kode'] ?? ($data['sub_rincian_kode'] ?? ''), 0, 5);
            $kode108Submitted = match(true) {
                $jenisPrefix === '1.3.1' => $data['tanah_kode_barang'] ?? null,
                $jenisPrefix === '1.3.2' => $data['mesin_kode_barang'] ?? null,
                $jenisPrefix === '1.3.3' => $data['gedung_kode_barang'] ?? null,
                $jenisPrefix === '1.3.4' => $data['jaringan_kode_barang'] ?? null,
                $jenisPrefix === '1.3.5' => $data['lainnya_kode_barang'] ?? null,
                $jenisPrefix === '1.5.3' => $data['atb_kode_barang'] ?? null,
                $jenisPrefix === '1.3.6' => $data['kdp_kode_barang'] ?? null,
                default => null
            };
            
            $jenisAstapRecord = null;
            if ($kode108Submitted) {
                $jenisAstapRecord = \App\Models\JenisAstap::where('sub_sub_rincian_objek', $kode108Submitted)->first();
            }
            if (!$jenisAstapRecord && !empty($data['sub_rincian_kode'])) {
                $jenisAstapRecord = \App\Models\JenisAstap::where('sub_rincian_objek', $data['sub_rincian_kode'])->first()
                    ?? \App\Models\JenisAstap::where('jenis', substr($data['sub_rincian_kode'], 0, 5))->first();
            }
            $jenisAstapId = $jenisAstapRecord ? $jenisAstapRecord->id : null;

            // Nama barang dari form input sesuai jenis aset
            $namaInputForm = match(true) {
                $jenisPrefix === '1.3.1' => $data['tanah_nama_barang'] ?? null,
                $jenisPrefix === '1.3.2' => $data['mesin_nama_barang'] ?? null,
                $jenisPrefix === '1.3.3' => $data['gedung_nama_barang'] ?? null,
                $jenisPrefix === '1.3.4' => $data['jaringan_nama_barang'] ?? null,
                $jenisPrefix === '1.3.5' => $data['lainnya_nama_barang'] ?? null,
                $jenisPrefix === '1.5.3' => $data['atb_nama_barang'] ?? null,
                $jenisPrefix === '1.3.6' => $data['kdp_nama_barang'] ?? null,
                default => null
            };

            $namaBarang = ($jenisAstapRecord && !empty($jenisAstapRecord->uraian_sub_sub_rincian)) 
                ? $jenisAstapRecord->uraian_sub_sub_rincian 
                : ($namaInputForm ?? ($data['nama_barang'] ?? 'Aset Baru'));

            $astap = \App\Models\Astap::create([
                'jenis_pengadaan_id' => $jenisPengadaanId,
                'rekening_belanja_id' => $rekeningBelanjaId,
                'jenis_astap_id' => $jenisAstapId,
                'nama_barang' => $namaBarang,
                'tahun_perolehan' => $data['tahun_perolehan'] ?? date('Y'),
                'jumlah_volume' => $data['jumlah_volume'] ?? 1,
                'satuan' => $data['satuan'] ?? 'Unit',
                'harga_satuan' => $data['harga_satuan'] ?? 0,
                'total_realisasi' => $data['jumlah_realisasi'] ?? ($data['total_realisasi'] ?? 0),
                'biaya_administrasi_proyek' => $data['biaya_administrasi_proyek'] ?? 0,
                'is_extracomtable' => !empty($data['is_extracomtable']),
                'spk_nomor' => $data['spk_nomor'] ?? null,
                'spk_tanggal' => $data['spk_tanggal'] ?? null,
                'surat_pesanan_nomor' => $data['surat_pesanan_nomor'] ?? null,
                'surat_pesanan_tanggal' => $data['surat_pesanan_tanggal'] ?? null,
                'kwitansi_nomor' => $data['kwitansi_nomor'] ?? null,
                'kwitansi_tanggal' => $data['kwitansi_tanggal'] ?? null,
                'faktur_nomor' => $data['faktur_nomor'] ?? null,
                'faktur_tanggal' => $data['faktur_tanggal'] ?? null,
                'keterangan_tambahan' => $data['keterangan'] ?? ($data['keterangan_tambahan'] ?? null),
                'user_id' => auth()->id()
            ]);

            $vol = (int) ($data['jumlah_volume'] ?? 1);
            $kode108Clean = str_replace('.', '', $astap->kode_108 ?: '132000000000');
            for ($i = 1; $i <= max(1, $vol); $i++) {
                $noRegStr = str_pad($i, 7, '0', STR_PAD_LEFT);
                $nibar = "1201351102000000280000{$astap->tahun_perolehan}{$kode108Clean}{$noRegStr}";
                $qrPath = "/scan/{$nibar}";
                \App\Models\AstapRegister::create([
                    'astap_id' => $astap->id,
                    'tahun_perolehan' => $astap->tahun_perolehan,
                    'no_register_int' => $i,
                    'no_register' => $nibar,
                    'nibar' => $nibar,
                    'qr_code_path' => $qrPath,
                    'ruang_pemegang' => $data['ruang_pemegang'] ?? null,
                    'kondisi' => in_array($data['kondisi'] ?? '', ['Baik', 'Rusak Ringan', 'Rusak Berat']) ? $data['kondisi'] : 'Baik',
                    'status' => 'Tersedia'
                ]);
            }

            return response()->json(['success' => true, 'message' => 'Data ASTAP berhasil disimpan ke database SIMAT-RK!']);
        })->name('astap.store');

        Route::put('/astap/{id}', function (\Illuminate\Http\Request $request, $id) {
            $astap = \App\Models\Astap::find($id);
            if (!$astap) {
                return response()->json(['success' => false, 'message' => 'Data ASTAP tidak ditemukan.'], 404);
            }

            $data = $request->all();

            // Dapatkan Kode 108 Sub-Sub Rincian berdasarkan jenis aset yang dipilih
            $jenisPrefix = substr($data['jenis_aset_kode'] ?? ($data['sub_rincian_kode'] ?? ''), 0, 5);
            $kode108Submitted = match(true) {
                $jenisPrefix === '1.3.1' => $data['tanah_kode_barang'] ?? null,
                $jenisPrefix === '1.3.2' => $data['mesin_kode_barang'] ?? null,
                $jenisPrefix === '1.3.3' => $data['gedung_kode_barang'] ?? null,
                $jenisPrefix === '1.3.4' => $data['jaringan_kode_barang'] ?? null,
                $jenisPrefix === '1.3.5' => $data['lainnya_kode_barang'] ?? null,
                $jenisPrefix === '1.5.3' => $data['atb_kode_barang'] ?? null,
                $jenisPrefix === '1.3.6' => $data['kdp_kode_barang'] ?? null,
                default => null
            };

            if ($kode108Submitted) {
                $jaRec = \App\Models\JenisAstap::where('sub_sub_rincian_objek', $kode108Submitted)->first();
                if ($jaRec) {
                    $astap->jenis_astap_id = $jaRec->id;
                    $astap->nama_barang = $jaRec->uraian_sub_sub_rincian ?: $astap->nama_barang;
                }
            }

            if (!empty($data['tahun_perolehan'])) $astap->tahun_perolehan = $data['tahun_perolehan'];
            if (isset($data['jumlah_realisasi'])) $astap->total_realisasi = $data['jumlah_realisasi'];
            if (!empty($data['keterangan'])) $astap->keterangan_tambahan = $data['keterangan'];
            $astap->save();

            return response()->json(['success' => true, 'message' => 'Data ASTAP berhasil diperbarui!']);
        })->name('astap.update');

        Route::delete('/astap/{id}', function ($id) {
            $astap = \App\Models\Astap::find($id);
            if ($astap) {
                $astap->registers()->delete();
                $astap->delete();
            }
            return response()->json(['success' => true, 'message' => 'Data ASTAP berhasil dihapus.']);
        })->name('astap.destroy');

        // Route Update & Delete Register ASTAP (NIBAR Per-Unit)
        Route::put('/astap-register/{id}', function (\Illuminate\Http\Request $request, $id) {
            $reg = \App\Models\AstapRegister::find($id);
            if (!$reg) {
                return response()->json(['success' => false, 'message' => 'Register tidak ditemukan.'], 404);
            }
            $data = $request->all();
            if (isset($data['ruang_pemegang'])) $reg->ruang_pemegang = $data['ruang_pemegang'];
            if (isset($data['kondisi']) && in_array($data['kondisi'], ['Baik', 'Rusak Ringan', 'Rusak Berat'])) {
                $reg->kondisi = $data['kondisi'];
            }
            if (isset($data['unit_id'])) $reg->unit_id = $data['unit_id'];
            $reg->save();

            return response()->json(['success' => true, 'message' => 'Data register NIBAR berhasil diperbarui!']);
        })->name('astap_register.update');

        Route::delete('/astap-register/{id}', function ($id) {
            $reg = \App\Models\AstapRegister::find($id);
            if ($reg) {
                $reg->delete();
            }
            return response()->json(['success' => true, 'message' => 'Unit register berhasil dihapus.']);
        })->name('astap_register.destroy');

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
