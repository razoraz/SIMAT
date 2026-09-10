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
    $mts = \App\Models\AstapMutasi::where('nomor_bamb', $hash)->first();
    if ($mts) {
        $judul = 'Berita Acara Mutasi Barang (BAMB)';
        $nomor = $mts->nomor_bamb;
        $nama = $mts->penanggung_jawab_asal ?: 'Kepala Ruangan Pengirim';
        $nip = '-';
        $jabatan = 'Penanggung Jawab Ruangan ' . ($mts->ruangan_asal ?? '');
        $tgl = $mts->tgl_persetujuan_admin ?: ($mts->tanggal_mutasi ? date('d/m/Y', strtotime($mts->tanggal_mutasi)) . ' WIB' : date('d/m/Y H:i') . ' WIB');
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
    Route::get('/sub-admin/dashboard', function () {
        $user = Auth::user();
        $unit = null;
        if ($user->unit_id) {
            $unit = \App\Models\Unit::find($user->unit_id);
        }
        
        // Jika sub_admin belum memiliki unit_id, tampilkan dashboard kosong
        // (JANGAN fallback ke unit lain — berbahaya untuk keamanan data)
        $unitId   = $unit ? $unit->id   : null;
        $unitNama = $unit ? $unit->nama  : '';

        // Jika tidak ada unit, kembalikan view dengan data kosong
        if (!$unitId) {
            return view('dashboards.sub_admin', [
                'unit'                        => null,
                'user'                        => $user,
                'distribusisList'             => [],
                'totalAsetCount'              => 0,
                'totalNilaiNum'               => 0,
                'totalNilaiFormatted'         => 'Rp 0',
                'kondisiBaik'                 => 0,
                'kondisiKurangBaik'           => 0,
                'kondisiRusakRingan'          => 0,
                'kondisiRusakBerat'           => 0,
                'totalRusak'                  => 0,
                'attentionAssets'             => [],
                'unitNama'                    => '',
                'chartYears'                  => ['Thn ' . date('Y')],
                'chartRoomVolume'             => [0],
                'chartRoomHarga'              => [0],
                'chartRoomHargaJuta'          => [0],
                'chartRoomKumulatifVolume'    => [0],
                'chartRoomKumulatifHargaJuta' => [0],
            ]);
        }

        // 1. Distribusi data riil khusus unit ini
        $dbDistribusis = \App\Models\Distribusi::with([
                'unit',
                'items.astap.jenisAstap',
                'items.registers.astapRegister'
            ])
            ->where('unit_id', $unitId)
            ->orderBy('id', 'desc')
            ->get();

        $distribusisList = $dbDistribusis->map(function($d) use ($unit, $user) {
            $itemsMapped = $d->items->map(function($it) {
                $spec = is_array($it->astap?->spesifikasi_json)
                    ? $it->astap->spesifikasi_json
                    : (json_decode($it->astap?->spesifikasi_json ?? '', true) ?? []);
                $merk = $spec['merk'] ?? ($spec['type'] ?? ($spec['konstruksi'] ?? '-'));
                
                $nibarList = $it->registers->map(fn($r) => $r->astapRegister?->nibar)->filter()->values()->all();
                $firstKondisi = $it->registers->first()?->astapRegister?->kondisi ?? 'Baik';

                return [
                    'nama'       => $it->astap?->nama_barang ?? 'Barang ASTAP',
                    'merk'       => $merk,
                    'qty'        => $it->qty . ' ' . ($it->astap?->satuan ?: 'Unit'),
                    'kondisi'    => $firstKondisi,
                    'nibar_list' => $nibarList
                ];
            });

            $firstItemName = $itemsMapped->first()['nama'] ?? 'Barang ASTAP';
            $moreCount = $itemsMapped->count() > 1 ? ' + ' . ($itemsMapped->count() - 1) . ' item lainnya' : '';
            $totalVol = $d->items->sum('qty');

            $tglCarbon = $d->tanggal_distribusi;
            $bulanIndo = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
            $tglStr = $tglCarbon ? ($tglCarbon->day . ' ' . ($bulanIndo[$tglCarbon->month] ?? '') . ' ' . $tglCarbon->year) : '-';

            return [
                'id'         => $d->id,
                'kode'       => $d->kode,
                'bast_nomor' => $d->bast_nomor ?: '-',
                'nama'       => $firstItemName . $moreCount,
                'qty'        => $totalVol . ' Item',
                'tgl'        => $tglStr,
                'status'     => $d->status,
                'keterangan' => $d->keterangan ?: 'Permohonan kebutuhan inventaris ruangan',
                'pengaju'    => $unit?->kepala ?? $user->name,
                'ruangan'    => $unit?->nama ?? 'Ruangan',
                'items'      => $itemsMapped->values()->all()
            ];
        })->values()->all();

        // 2. Data Register Aset di Ruangan Ini — Mendukung unit_id dan ruang_pemegang (sesuai katalog Unit)
        $registers = \App\Models\AstapRegister::with(['astap.jenisAstap'])
            ->where(function($q) use ($unitId, $unitNama) {
                if ($unitId) {
                    $q->where('unit_id', $unitId);
                }
                if ($unitNama && $unitNama !== 'Ruangan Saya') {
                    $q->orWhere(function($q2) use ($unitId, $unitNama) {
                        // Jangan sertakan jika aset sudah tercatat milik unit lain
                        if ($unitId) {
                            $q2->where(function($q3) use ($unitId) {
                                $q3->whereNull('unit_id')->orWhere('unit_id', $unitId);
                            });
                        }
                        $q2->whereNotNull('ruang_pemegang')
                           ->where('ruang_pemegang', '!=', '')
                           ->where(function($q4) use ($unitNama) {
                               $q4->where('ruang_pemegang', $unitNama)
                                  ->orWhere('ruang_pemegang', 'LIKE', '%' . $unitNama . '%');
                           });
                    });
                }
            })
            ->get();

        // Ambil ASTAP yang terhubung langsung via unit_id jika belum masuk dalam register
        $directRegisters = collect();
        if ($unitId) {
            $astapDirectIds = \App\Models\Astap::where('unit_id', $unitId)
                ->whereNotIn('id', $registers->pluck('astap_id')->filter()->unique())
                ->pluck('id');

            if ($astapDirectIds->isNotEmpty()) {
                $directRegisters = \App\Models\AstapRegister::with(['astap.jenisAstap'])
                    ->whereIn('astap_id', $astapDirectIds)
                    ->get();
            }
        }

        $allRegisters = $registers->concat($directRegisters)->unique('id');

        $totalAsetCount = $allRegisters->count();
        $totalNilaiNum = $allRegisters->sum(fn($r) => $r->astap ? (float) ($r->astap->harga_satuan ?: ($r->astap->total_realisasi / max(1, $r->astap->jumlah_volume))) : 0);
        $totalNilaiFormatted = 'Rp ' . number_format($totalNilaiNum, 0, ',', '.');

        $kondisiBaik = $allRegisters->where('kondisi', 'Baik')->count();
        $kondisiKurangBaik = $allRegisters->where('kondisi', 'Kurang Baik')->count();
        $kondisiRusakRingan = $allRegisters->where('kondisi', 'Rusak Ringan')->count();
        $kondisiRusakBerat = $allRegisters->whereIn('kondisi', ['Rusak Berat', 'Rusak'])->count();
        $totalRusak = $kondisiKurangBaik + $kondisiRusakRingan + $kondisiRusakBerat;

        // 3. Aset yang perlu perhatian / rusak di ruangan ini
        $attentionAssets = $allRegisters->filter(fn($r) => $r->kondisi !== 'Baik')->map(function($r) {
            return [
                'id'      => $r->id,
                'kode'    => $r->nibar ?: $r->no_register,
                'nama'    => $r->astap?->nama_barang ?? 'Barang Inventaris',
                'status'  => $r->kondisi,
                'lokasi'  => $r->ruang_pemegang ?: 'Ruangan',
                'catatan' => 'Kondisi fisik unit tercatat: ' . $r->kondisi . ' (Perlu pengecekan berkala / servis)'
            ];
        })->values()->all();

        // 4. Data Agregasi Grafik Nilai Aset Ruangan (Berdasarkan Tahun Perolehan)
        $yearlyMap = [];
        $categoryMap = [];

        foreach ($allRegisters as $r) {
            $astap = $r->astap;
            $year = (int) ($astap?->tahun_perolehan ?: ($astap?->created_at ? $astap->created_at->year : date('Y')));
            if ($year < 1970 || $year > ((int)date('Y') + 1)) {
                $year = (int) date('Y');
            }

            $hargaSatuan = $astap ? (float) ($astap->harga_satuan ?: ($astap->total_realisasi / max(1, $astap->jumlah_volume))) : 0;

            if (!isset($yearlyMap[$year])) {
                $yearlyMap[$year] = ['volume' => 0, 'harga' => 0];
            }
            $yearlyMap[$year]['volume'] += 1;
            $yearlyMap[$year]['harga'] += $hargaSatuan;

            $catName = $astap?->jenisAstap?->nama_jenis ?: ($astap?->category ?: 'Peralatan & Mesin');
            if (!isset($categoryMap[$catName])) {
                $categoryMap[$catName] = ['volume' => 0, 'harga' => 0];
            }
            $categoryMap[$catName]['volume'] += 1;
            $categoryMap[$catName]['harga'] += $hargaSatuan;
        }

        ksort($yearlyMap);

        $chartYears = [];
        $chartRoomVolume = [];
        $chartRoomHarga = [];
        $chartRoomHargaJuta = [];
        $chartRoomKumulatifVolume = [];
        $chartRoomKumulatifHargaJuta = [];

        $runVol = 0;
        $runHarga = 0;

        foreach ($yearlyMap as $yr => $stat) {
            $runVol += $stat['volume'];
            $runHarga += $stat['harga'];

            $chartYears[] = 'Thn ' . $yr;
            $chartRoomVolume[] = $stat['volume'];
            $chartRoomHarga[] = round($stat['harga'], 2);
            $chartRoomHargaJuta[] = round($stat['harga'] / 1000000, 2);
            $chartRoomKumulatifVolume[] = $runVol;
            $chartRoomKumulatifHargaJuta[] = round($runHarga / 1000000, 2);
        }

        if (empty($chartYears)) {
            $chartYears = ['Thn ' . date('Y')];
            $chartRoomVolume = [0];
            $chartRoomHarga = [0];
            $chartRoomHargaJuta = [0];
            $chartRoomKumulatifVolume = [0];
            $chartRoomKumulatifHargaJuta = [0];
        }

        return view('dashboards.sub_admin', [
            'unit'                        => $unit,
            'user'                        => $user,
            'distribusisList'             => $distribusisList,
            'totalAsetCount'              => $totalAsetCount,
            'totalNilaiNum'               => $totalNilaiNum,
            'totalNilaiFormatted'         => $totalNilaiFormatted,
            'kondisiBaik'                 => $kondisiBaik,
            'kondisiKurangBaik'           => $kondisiKurangBaik,
            'kondisiRusakRingan'          => $kondisiRusakRingan,
            'kondisiRusakBerat'           => $kondisiRusakBerat,
            'totalRusak'                  => $totalRusak,
            'attentionAssets'             => $attentionAssets,
            'unitNama'                    => $unitNama,
            // Chart Data
            'chartYears'                  => $chartYears,
            'chartRoomVolume'             => $chartRoomVolume,
            'chartRoomHarga'              => $chartRoomHarga,
            'chartRoomHargaJuta'          => $chartRoomHargaJuta,
            'chartRoomKumulatifVolume'    => $chartRoomKumulatifVolume,
            'chartRoomKumulatifHargaJuta' => $chartRoomKumulatifHargaJuta,
        ]);
    })->name('subadmin.dashboard');

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
    Route::get('/astap', function () {
        $astaps = \App\Models\Astap::with([
                'registers.mutasis' => function($q) {
                    $q->where('status', 'Disetujui Admin (Selesai)');
                }, 
                'jenisAstap', 
                'rekeningBelanja', 
                'jenisPengadaan', 
                'unit'
            ])
            ->orderBy('id', 'desc')
            ->get()
            ->map(function($a) {
                $spec = is_array($a->spesifikasi_json) ? $a->spesifikasi_json : (json_decode($a->spesifikasi_json, true) ?? []);
                $firstReg = $a->registers ? $a->registers->first() : null;
                $ja = $a->jenisAstap;
                $jp = $a->jenisPengadaan;
                $rb = $a->rekeningBelanja;
                $kode108Val = $a->kode_108 ?: ($ja ? ($ja->sub_sub_rincian_objek ?: $ja->jenis) : '');

                return [
                    'id' => $a->id,
                    'created_at' => $a->created_at ? $a->created_at->format('Y-m-d H:i:s') : null,
                    'category' => $a->category,
                    'kode_barang' => $kode108Val,
                    'nama_barang' => $a->nama_barang,
                    'tahun_perolehan' => (string) $a->tahun_perolehan,
                    'triwulan' => $a->triwulan ?: ($spec['triwulan'] ?? 'TW I'),
                    'volume_satuan' => $a->jumlah_volume . ' ' . ($a->satuan ?: 'Unit'),
                    
                    // LANGKAH 1
                    'program_kode' => $jp ? ($jp->program_kode ?: '0.00.01') : '0.00.01',
                    'program_nama' => $jp ? ($jp->program_nama ?: 'Program Penunjang Urusan Pemerintah Daerah Kabupaten/Kota') : 'Program Penunjang Urusan Pemerintah Daerah Kabupaten/Kota',
                    'kegiatan_kode' => $jp ? ($jp->kegiatan_kode ?: '0.00.01.2.10') : '0.00.01.2.10',
                    'kegiatan_nama' => $jp ? ($jp->kegiatan_nama ?: 'Peningkatan Pelayanan BLUD') : 'Peningkatan Pelayanan BLUD',
                    'sub_kegiatan_kode' => $jp ? ($jp->sub_kegiatan_kode ?: '0.00.01.2.10.0001') : '0.00.01.2.10.0001',
                    'sub_kegiatan_nama' => $jp ? ($jp->sub_kegiatan_nama ?: 'Pelayanan dan Penunjang Pelayanan BLUD') : 'Pelayanan dan Penunjang Pelayanan BLUD',

                    // LANGKAH 2
                    'rekening_kode' => $rb ? ($rb->kode_rek ?: '5.2.02.01.01.0001') : '5.2.02.01.01.0001',
                    'rekening_nama' => $rb ? ($rb->nama_belanja ?: 'Belanja Modal Pengadaan Aset Tetap') : 'Belanja Modal Pengadaan Aset Tetap',
                    'jenis_aset_kode' => $ja ? ($ja->jenis ?: (substr($kode108Val, 0, 5) ?: '1.3.1')) : (substr($kode108Val, 0, 5) ?: '1.3.1'),
                    'jenis_aset_nama' => $ja ? ($ja->nama_jenis ?: 'ASET TETAP') : 'ASET TETAP',
                    'sub_rincian_kode' => $ja ? ($ja->sub_rincian_objek ?: (strlen($kode108Val) >= 14 ? substr($kode108Val, 0, 14) : '1.3.1.01.01.01')) : (strlen($kode108Val) >= 14 ? substr($kode108Val, 0, 14) : '1.3.1.01.01.01'),
                    'sub_rincian_nama' => $ja ? ($ja->uraian_sub_rincian ?: '-') : '-',
                    'jumlah_anggaran' => (float) ($a->jumlah_anggaran ?: ($spec['jumlah_anggaran'] ?? $a->total_realisasi)),
                    'jumlah_realisasi' => 'Rp ' . number_format($a->total_realisasi, 0, ',', '.'),
                    'total_realisasi_num' => (float) $a->total_realisasi,
                    'jumlah_volume' => (int) ($a->jumlah_volume ?: 1),
                    'satuan' => $a->satuan ?: 'Unit',
                    'harga_satuan' => (float) ($a->harga_satuan ?: ($a->jumlah_volume > 0 ? ($a->total_realisasi / $a->jumlah_volume) : $a->total_realisasi)),
                    'biaya_administrasi_proyek' => (float) ($a->biaya_administrasi_proyek ?: ($spec['admin_proyek'] ?? 0)),

                    // LANGKAH 3 SPESIFIKASI & DOKUMEN
                    'spk_nomor' => $a->spk_nomor ?: ($spec['spk_nomor'] ?? '-'),
                    'spk_tanggal' => $a->spk_tanggal ? $a->spk_tanggal->format('d/m/Y') : ($spec['spk_tanggal'] ?? '-'),
                    'surat_pesanan_nomor' => $a->surat_pesanan_nomor ?: ($spec['surat_pesanan_nomor'] ?? '-'),
                    'surat_pesanan_tanggal' => $a->surat_pesanan_tanggal ? $a->surat_pesanan_tanggal->format('d/m/Y') : ($spec['surat_pesanan_tanggal'] ?? '-'),
                    'kwitansi_nomor' => $a->kwitansi_nomor ?: ($spec['kwitansi_nomor'] ?? '-'),
                    'kwitansi_tanggal' => $a->kwitansi_tanggal ? $a->kwitansi_tanggal->format('d/m/Y') : ($spec['kwitansi_tanggal'] ?? '-'),
                    'faktur_nomor' => $a->faktur_nomor ?: ($spec['faktur_nomor'] ?? ($spec['invoice_nomor'] ?? '-')),
                    'faktur_tanggal' => $a->faktur_tanggal ? $a->faktur_tanggal->format('d/m/Y') : ($spec['faktur_tanggal'] ?? ($spec['invoice_tanggal'] ?? '-')),
                    'sp2d_nomor' => $a->sp2d_nomor ?: ($spec['sp2d_nomor'] ?? '-'),
                    'sp2d_tanggal' => $a->sp2d_tanggal ? $a->sp2d_tanggal->format('d/m/Y') : ($spec['sp2d_tanggal'] ?? '-'),
                    'bast_dokumen_nomor' => $a->bast_dokumen_nomor ?: ($spec['bast_dokumen_nomor'] ?? '-'),
                    'bast_dokumen_tanggal' => $a->bast_dokumen_tanggal ? $a->bast_dokumen_tanggal->format('d/m/Y') : ($spec['bast_dokumen_tanggal'] ?? '-'),
                    
                    // Rincian Tanah Khusus (KIB A)
                    'hak_tanah' => $spec['hak_tanah'] ?? ($spec['tanah_hak'] ?? 'Hak Pakai'),
                    'sertifikat_nomor' => $spec['sertifikat_no'] ?? ($spec['tanah_sertifikat_no'] ?? '-'),
                    'sertifikat_tanggal' => $spec['sertifikat_tgl'] ?? ($spec['tanah_sertifikat_tgl'] ?? '-'),
                    'luas_m2' => (float) ($spec['luas_m2'] ?? ($spec['tanah_luas_m2'] ?? 0)),
                    'penggunaan' => $spec['penggunaan'] ?? ($spec['tanah_penggunaan'] ?? 'Bangunan Rumah Sakit & Fasilitas'),
                    'jumlah_bidang' => (int) ($spec['tanah_jumlah_bidang'] ?? ($a->jumlah_volume ?: 1)),
                    'nilai_perencanaan' => (float) ($spec['nilai_perencanaan'] ?? ($spec['tanah_nilai_perencanaan'] ?? 0)),
                    'nilai_fisik' => (float) ($spec['nilai_fisik'] ?? ($spec['tanah_nilai_fisik'] ?? $a->total_realisasi)),
                    'nilai_pengawasan' => (float) ($spec['nilai_pengawasan'] ?? ($spec['tanah_nilai_pengawasan'] ?? 0)),
                    
                    // Rincian Mesin & Peralatan (KIB B)
                    'merk' => $spec['merk'] ?? ($spec['buku_judul'] ?? ($spec['judul_lisensi'] ?? ($spec['konstruksi'] ?? '-'))),
                    'type' => $spec['type'] ?? ($spec['tipe'] ?? ($spec['model'] ?? '-')),
                    'ukuran' => $spec['ukuran'] ?? (isset($spec['luas_m2']) ? $spec['luas_m2'] . ' m²' : ($spec['buku_spesifikasi'] ?? '-')),
                    'no_pabrik' => $spec['no_pabrik'] ?? ($spec['sertifikat_no'] ?? '-'),
                    'no_rangka' => $spec['no_rangka'] ?? ($spec['rangka'] ?? '-'),
                    'no_mesin' => $spec['no_mesin'] ?? ($spec['mesin'] ?? '-'),
                    'no_btkb' => $spec['no_btkb'] ?? ($spec['btkb'] ?? '-'),
                    'no_polisi' => $spec['no_polisi'] ?? ($spec['polisi'] ?? ($spec['nopol'] ?? '-')),
                    'bahan' => $spec['bahan'] ?? '-',
                    'kondisi' => $firstReg ? $firstReg->kondisi : ($spec['kondisi'] ?? 'Baik'),
                    'ruang_unit' => $firstReg ? $firstReg->ruang_pemegang : ($spec['ruang_unit'] ?? ($a->unit ? $a->unit->nama_unit : '-')),
                    'asal_usul' => 'BLUD RSUD',

                    // Rincian Gedung & Bangunan (KIB C)
                    'gedung_bertingkat' => $spec['bertingkat'] ?? ($spec['gedung_bertingkat'] ?? '-'),
                    'gedung_beton' => $spec['beton'] ?? ($spec['gedung_beton'] ?? '-'),
                    'gedung_status_tanah' => $spec['status_tanah'] ?? ($spec['gedung_status_tanah'] ?? 'Tanah Hak Pakai RSUD'),
                    'gedung_kode_aset_tanah' => $spec['kode_tanah'] ?? ($spec['gedung_kode_aset_tanah'] ?? '-'),
                    'gedung_is_baru' => $spec['is_baru'] ?? ($spec['gedung_is_baru'] ?? 'Pengadaan Baru'),
                    'gedung_kapitalisasi_tahun_induk' => $spec['kapitalisasi_tahun_induk'] ?? ($spec['gedung_kapitalisasi_tahun_induk'] ?? '-'),
                    'gedung_kapitalisasi_nilai_induk' => (float) ($spec['kapitalisasi_nilai_induk'] ?? ($spec['gedung_kapitalisasi_nilai_induk'] ?? 0)),
                    'gedung_nilai_perencanaan' => (float) ($spec['nilai_perencanaan'] ?? ($spec['gedung_nilai_perencanaan'] ?? 0)),
                    'gedung_nilai_fisik' => (float) ($spec['nilai_fisik'] ?? ($spec['gedung_nilai_fisik'] ?? $a->total_realisasi)),
                    'gedung_nilai_pengawasan' => (float) ($spec['nilai_pengawasan'] ?? ($spec['gedung_nilai_pengawasan'] ?? 0)),
                    'gedung_nilai_ap' => (float) ($spec['nilai_ap'] ?? ($spec['gedung_nilai_ap'] ?? ($spec['nilai_pip'] ?? ($spec['gedung_nilai_pip'] ?? 0)))),
                    'gedung_nilai_pip' => (float) ($spec['nilai_ap'] ?? ($spec['gedung_nilai_ap'] ?? ($spec['nilai_pip'] ?? ($spec['gedung_nilai_pip'] ?? 0)))),

                    // Rincian Jalan, Irigasi & Jaringan (KIB D)
                    'jaringan_konstruksi' => $spec['konstruksi'] ?? ($spec['jaringan_konstruksi'] ?? '-'),
                    'jaringan_panjang_m' => (float) ($spec['panjang_m'] ?? ($spec['jaringan_panjang_m'] ?? 0)),
                    'jaringan_lebar_m' => (float) ($spec['lebar_m'] ?? ($spec['jaringan_lebar_m'] ?? 0)),
                    'jaringan_luas_m2' => (float) ($spec['luas_m2'] ?? ($spec['jaringan_luas_m2'] ?? 0)),
                    'jaringan_bertingkat' => $spec['bertingkat'] ?? ($spec['jaringan_bertingkat'] ?? '-'),
                    'jaringan_beton' => $spec['beton'] ?? ($spec['jaringan_beton'] ?? '-'),
                    'jaringan_status_tanah' => $spec['status_tanah'] ?? ($spec['jaringan_status_tanah'] ?? 'Tanah Hak Pakai RSUD'),
                    'jaringan_kode_aset_tanah' => $spec['kode_tanah'] ?? ($spec['jaringan_kode_aset_tanah'] ?? '-'),
                    'jaringan_is_baru' => $spec['is_baru'] ?? ($spec['jaringan_is_baru'] ?? 'Pengadaan Baru'),
                    'jaringan_kapitalisasi_tahun_induk' => $spec['kapitalisasi_tahun_induk'] ?? ($spec['jaringan_kapitalisasi_tahun_induk'] ?? '-'),
                    'jaringan_kapitalisasi_nilai_induk' => (float) ($spec['kapitalisasi_nilai_induk'] ?? ($spec['jaringan_kapitalisasi_nilai_induk'] ?? 0)),
                    'jaringan_nilai_perencanaan' => (float) ($spec['nilai_perencanaan'] ?? ($spec['jaringan_nilai_perencanaan'] ?? 0)),
                    'jaringan_nilai_fisik' => (float) ($spec['nilai_fisik'] ?? ($spec['jaringan_nilai_fisik'] ?? $a->total_realisasi)),
                    'jaringan_nilai_pengawasan' => (float) ($spec['nilai_pengawasan'] ?? ($spec['jaringan_nilai_pengawasan'] ?? 0)),
                    'jaringan_nilai_ap' => (float) ($spec['nilai_ap'] ?? ($spec['jaringan_nilai_ap'] ?? ($spec['nilai_pip'] ?? ($spec['jaringan_nilai_pip'] ?? 0)))),
                    'jaringan_nilai_pip' => (float) ($spec['nilai_ap'] ?? ($spec['jaringan_nilai_ap'] ?? ($spec['nilai_pip'] ?? ($spec['jaringan_nilai_pip'] ?? 0)))),

                    // Rincian KIB E, F, ATB & EXTRACOM
                    'judul_pencipta' => $spec['judul'] ?? ($spec['pencipta'] ?? ($spec['buku_judul'] ?? ($spec['judul_lisensi'] ?? '-'))),
                    'spesifikasi' => $spec['spesifikasi'] ?? ($spec['buku_spesifikasi'] ?? ($spec['spesifikasi_lisensi'] ?? '-')),
                    'asal_kesenian' => $spec['asal_kesenian'] ?? ($spec['daerah_asal'] ?? ($spec['penerbit'] ?? '-')),
                    'progres_fisik' => $spec['progres_fisik'] ?? ($spec['capaian_fisik'] ?? '100%'),

                    // LANGKAH 4 (REKANAN, PPK, KETERANGAN)
                    'alamat_barang' => $a->alamat_barang ?: ($spec['alamat_barang'] ?? 'RSUD Dr. H. Koesnandi'),
                    'penyedia_nama' => $a->penyedia_nama ?: ($spec['penyedia_nama'] ?? '-'),
                    'penyedia_pemilik' => $a->penyedia_pemilik ?: ($spec['penyedia_pemilik'] ?? '-'),
                    'penyedia_telepon' => $a->penyedia_telepon ?: ($spec['penyedia_telepon'] ?? ($spec['penyedia_kontak'] ?? '-')),
                    'penyedia_rekening_nama' => $a->penyedia_rekening_nama ?: ($a->penyedia_nama ?: ($spec['penyedia_rekening_nama'] ?? '-')),
                    'penyedia_rekening_nomor' => $a->penyedia_rekening_nomor ?: ($spec['penyedia_rekening_nomor'] ?? '-'),
                    'penyedia_alamat' => $a->penyedia_alamat ?: ($spec['penyedia_alamat'] ?? '-'),
                    'ppk_nama' => $a->ppk_nama ?: ($spec['ppk_nama'] ?? '-'),
                    'ppk_nip' => $a->ppk_nip ?: ($spec['ppk_nip'] ?? '-'),
                    'keterangan' => $a->keterangan_tambahan ?: ($a->keterangan ?: '-'),
                    'keterangan_tambahan' => $a->keterangan_tambahan ?: ($a->keterangan ?: '-'),

                    'registers' => $a->registers ? $a->registers->map(function($r) {
                        return [
                            'id' => $r->id,
                            'no_register' => $r->nibar ?: $r->no_register,
                            'nibar' => $r->nibar,
                            'ruang_pemegang' => $r->ruang_pemegang,
                            'kondisi' => $r->kondisi,
                            'status_mutasi' => $r->status_mutasi,
                            'qr_code_path' => $r->qr_code_path,
                            'mutasis' => $r->mutasis ? $r->mutasis->where('status', 'Disetujui Admin (Selesai)')->sortByDesc('tanggal_mutasi')->map(function($m) use ($r) {
                                return [
                                    'id' => $m->id,
                                    'nomor_bamb' => $m->nomor_bamb,
                                    'tanggal_mutasi' => $m->tanggal_mutasi ? $m->tanggal_mutasi->format('d M Y') : '-',
                                    'tanggal_mutasi_raw' => $m->tanggal_mutasi ? $m->tanggal_mutasi->format('Y-m-d') : '',
                                    'ruangan_asal' => $m->ruangan_asal,
                                    'ruangan_tujuan' => $m->ruangan_tujuan,
                                    'jenis_mutasi' => $m->jenis_mutasi ?? 'Mutasi',
                                    'kondisi' => $m->pivot?->kondisi ?: ($m->kondisi ?: ($r->kondisi ?: 'Baik')),
                                    'alasan_mutasi' => $m->alasan_mutasi ?: '-',
                                    'status' => $m->status,
                                    'penanggung_jawab_asal' => $m->penanggung_jawab_asal ?: '-',
                                    'penanggung_jawab_tujuan' => $m->penanggung_jawab_tujuan ?: '-',
                                    'catatan_penerima' => $m->catatan_penerima,
                                ];
                            })->values() : []
                        ];
                    })->values() : [],
                    'spesifikasi_json' => $spec
                ];
            });
        return view('pages.data_astap', compact('astaps'));
    })->name('astap.index');

    // API: Ambil riwayat mutasi spesifik unit register NIBAR
    Route::get('/astap/register-mutasi/{id}', function ($id) {
        $reg = \App\Models\AstapRegister::with(['mutasis' => function($q) {
            $q->where('status', 'Disetujui Admin (Selesai)')->orderBy('tanggal_mutasi', 'desc')->orderBy('id', 'desc');
        }])->find($id);

        if (!$reg) {
            return response()->json(['success' => false, 'mutasis' => []]);
        }

        $mutasis = $reg->mutasis->map(function($m) use ($reg) {
            return [
                'id'                      => $m->id,
                'nomor_bamb'              => $m->nomor_bamb,
                'tanggal_mutasi'          => $m->tanggal_mutasi ? $m->tanggal_mutasi->format('d M Y') : '-',
                'tanggal_mutasi_raw'      => $m->tanggal_mutasi ? $m->tanggal_mutasi->format('Y-m-d') : '',
                'ruangan_asal'            => $m->ruangan_asal,
                'ruangan_tujuan'          => $m->ruangan_tujuan,
                'jenis_mutasi'            => $m->jenis_mutasi ?? 'Mutasi',
                'kondisi'                 => $m->pivot?->kondisi ?: ($m->kondisi ?: ($reg->kondisi ?: 'Baik')),
                'alasan_mutasi'           => $m->alasan_mutasi ?: '-',
                'status'                  => $m->status,
                'penanggung_jawab_asal'   => $m->penanggung_jawab_asal ?: '-',
                'penanggung_jawab_tujuan' => $m->penanggung_jawab_tujuan ?: '-',
                'catatan_penerima'        => $m->catatan_penerima,
            ];
        });

        return response()->json([
            'success' => true,
            'kondisi' => $reg->kondisi,
            'ruang'   => $reg->ruang_pemegang,
            'mutasis' => $mutasis
        ]);
    });

    // 2. Distribusi Pages & Forms
    Route::get('/distribusi', [DistribusiController::class, 'index'])->name('distribusi.index');
    Route::get('/distribusi/create', [DistribusiController::class, 'create'])->name('distribusi.create');
    Route::get('/distribusi/next-bast', [DistribusiController::class, 'getNextBast'])->name('distribusi.next-bast');
    Route::get('/distribusi/{id}/edit', [DistribusiController::class, 'edit'])->name('distribusi.edit');
    Route::post('/distribusi/save', [DistribusiController::class, 'saveDistribusi'])->name('distribusi.save');
    Route::post('/distribusi', [DistribusiController::class, 'saveDistribusi'])->name('distribusi.store');
    Route::put('/distribusi/{id}', [DistribusiController::class, 'saveDistribusi'])->name('distribusi.update');
    Route::delete('/distribusi/{id}', [DistribusiController::class, 'destroy'])->name('distribusi.destroy');

    // API: Update Status Distribusi (misal: Sub Admin menandai Barang Diterima / Admin menolak)
    Route::patch('/distribusi/{id}/status', function (\Illuminate\Http\Request $request, $id) {
        $dst = \App\Models\Distribusi::with('items.registers')->find($id);
        if (!$dst) {
            return response()->json(['success' => false, 'message' => 'Data distribusi tidak ditemukan.'], 404);
        }
        $newStatus   = $request->input('status', 'Telah Diterima');
        $alasanTolak = $request->input('alasan_tolak', null);

        $dst->status = $newStatus;

        if (in_array($newStatus, ['Dalam Pengiriman', 'Telah Diterima', 'Dikirim', 'Diterima'])) {
            if ($newStatus === 'Telah Diterima') {
                $dst->signed = true;
                if (!$dst->tgl_signed) {
                    $dst->tgl_signed = now()->format('d/m/Y H:i') . ' WIB';
                }
            }

            // Terbitkan nomor BAST resmi jika belum ada
            $tahun = date('Y', strtotime($dst->tanggal_distribusi ?: now()));
            $isValidExistingBast = !empty($dst->bast_nomor) 
                && preg_match('/^032\s*\/\s*\d+\s*\/\s*430\.10\.7\s*\/\s*\d{4}$/', trim($dst->bast_nomor));

            if (!$isValidExistingBast) {
                $dst->bast_nomor = \App\Http\Controllers\DistribusiController::generateNextBastNomor((int)$tahun, $dst->id);
            }
        }

        if ($newStatus === 'Ditolak') {
            // Otomatis tidak memiliki nomor BAST
            $dst->bast_nomor = null;
            $dst->signed     = false;
            $dst->tgl_signed = null;

            if ($alasanTolak && \Schema::hasColumn('distribusis', 'alasan_tolak')) {
                $dst->alasan_tolak = $alasanTolak;
            }

            // Kembalikan semua register NIBAR ke status Tersedia & reset Vol ACC ke 0
            foreach ($dst->items as $item) {
                $regIds = $item->registers->pluck('astap_register_id')->filter()->toArray();
                if (!empty($regIds)) {
                    \App\Models\AstapRegister::whereIn('id', $regIds)->update([
                        'unit_id'        => null,
                        'ruang_pemegang' => null,
                        'status'         => 'Tersedia',
                    ]);
                }
                // Lepaskan relasi register NIBAR dari transaksi yang ditolak
                $item->registers()->delete();
                // Reset Vol ACC menjadi 0
                $item->update(['qty_acc' => 0]);
            }
        }

        $dst->save();

        return response()->json([
            'success'    => true,
            'status'     => $dst->status,
            'bast_nomor' => $dst->bast_nomor,
            'message'    => "Status distribusi {$dst->kode} berhasil diperbarui menjadi '{$dst->status}'."
        ]);
    })->name('distribusi.status.update');

    // API: Toggle Status TTD BSrE Distribusi (simpan ke database agar persist setelah reload)
    Route::patch('/distribusi/{id}/sign', function (\Illuminate\Http\Request $request, $id) {
        $dst = \App\Models\Distribusi::find($id);
        if (!$dst) {
            return response()->json(['success' => false, 'message' => 'Data distribusi tidak ditemukan.'], 404);
        }
        $newSigned = !$dst->signed;
        $dst->signed = $newSigned;
        if ($newSigned) {
            $dst->tgl_signed = now()->format('d/m/Y H:i') . ' WIB';
        } else {
            $dst->tgl_signed = null;
        }
        $dst->save();
        return response()->json([
            'success'    => true,
            'signed'     => $dst->signed,
            'tgl_signed' => $dst->tgl_signed ?? '-',
            'qr_hash'    => $dst->signed ? ('BSRE-KOESNANDI-' . $dst->kode) : '',
            'message'    => $dst->signed
                ? 'BAST berhasil ditandatangani secara digital (BSrE).'
                : 'Tanda tangan digital BSrE berhasil dibatalkan.',
        ]);
    })->name('distribusi.sign');

    // API: Update kondisi per Register NIBAR (dari halaman distribusi — semua role terautentikasi)
    Route::patch('/distribusi/register-kondisi/{id}', function (\Illuminate\Http\Request $request, $id) {
        $reg = \App\Models\AstapRegister::find($id);
        if (!$reg) {
            return response()->json(['success' => false, 'message' => 'Register tidak ditemukan.'], 404);
        }
        $kondisi = $request->input('kondisi');
        $allowed = ['Baik', 'Kurang Baik', 'Rusak Ringan', 'Rusak Berat'];
        if (!in_array($kondisi, $allowed)) {
            return response()->json(['success' => false, 'message' => 'Kondisi tidak valid.'], 422);
        }
        $reg->kondisi = $kondisi;
        $reg->save();
        return response()->json(['success' => true, 'kondisi' => $reg->kondisi, 'updated_at' => $reg->updated_at->toISOString()]);
    })->name('distribusi.register_kondisi.update');

    // API: Ambil kondisi terkini satu astap_register dari DB (untuk refresh realtime)
    Route::get('/distribusi/register-kondisi/{id}', function ($id) {
        $reg = \App\Models\AstapRegister::select('id','nibar','kondisi','ruang_pemegang','updated_at')->find($id);
        if (!$reg) return response()->json(['success' => false], 404);
        return response()->json(['success' => true, 'kondisi' => $reg->kondisi, 'ruang' => $reg->ruang_pemegang, 'updated_at' => $reg->updated_at]);
    })->name('distribusi.register_kondisi.show');

    // API: Ambil riwayat mutasi satu register NIBAR lengkap (tanggal, kondisi saat itu, alasan)
    Route::get('/astap/register-mutasi/{id}', function ($id) {
        $reg = \App\Models\AstapRegister::with(['mutasis' => function($q) {
            $q->where('status', 'Disetujui Admin (Selesai)')->orderBy('tanggal_mutasi', 'desc')->orderBy('id', 'desc');
        }])->find($id);
        if (!$reg) {
            return response()->json(['success' => false, 'message' => 'Register tidak ditemukan.'], 404);
        }
        $mutasis = $reg->mutasis->map(function($m) {
            return [
                'id'                      => $m->id,
                'nomor_bamb'              => $m->nomor_bamb,
                'tanggal_mutasi'          => $m->tanggal_mutasi ? $m->tanggal_mutasi->format('d M Y') : '-',
                'tanggal_mutasi_raw'      => $m->tanggal_mutasi ? $m->tanggal_mutasi->format('Y-m-d') : '',
                'ruangan_asal'            => $m->ruangan_asal,
                'ruangan_tujuan'          => $m->ruangan_tujuan,
                'jenis_mutasi'            => $m->jenis_mutasi ?? 'Mutasi',
                'kondisi'                 => $m->kondisi ?: ($m->register?->kondisi ?: 'Baik'),
                'alasan_mutasi'           => $m->alasan_mutasi ?: '-',
                'status'                  => $m->status,
                'penanggung_jawab_asal'   => $m->penanggung_jawab_asal ?: '-',
                'penanggung_jawab_tujuan' => $m->penanggung_jawab_tujuan ?: '-',
                'catatan_penerima'        => $m->catatan_penerima,
            ];
        })->values();
        return response()->json([
            'success' => true,
            'nibar'   => $reg->nibar ?: $reg->no_register,
            'kondisi' => $reg->kondisi,
            'ruang'   => $reg->ruang_pemegang,
            'mutasis' => $mutasis,
        ]);
    })->name('astap.register_mutasi');

    // 4. Mutasi Aset Pages & Forms
    Route::get('/mutasi-aset',                 [MutasiController::class, 'index'])->name('mutasi.index');
    Route::get('/mutasi-aset/create',          [MutasiController::class, 'create'])->name('mutasi.create');
    Route::post('/mutasi-aset',                [MutasiController::class, 'store'])->name('mutasi.store');
    Route::get('/mutasi-aset/{id}/edit',       [MutasiController::class, 'edit'])->name('mutasi.edit');
    Route::put('/mutasi-aset/{id}',            [MutasiController::class, 'update'])->name('mutasi.update');
    Route::delete('/mutasi-aset/{id}',         [MutasiController::class, 'destroy'])->name('mutasi.destroy');
    Route::post('/mutasi-aset/{id}/approve-pengirim', [MutasiController::class, 'approvePengirim'])->name('mutasi.approve.pengirim');
    Route::post('/mutasi-aset/{id}/approve-penerima', [MutasiController::class, 'approvePenerima'])->name('mutasi.approve.penerima');
    Route::post('/mutasi-aset/{id}/approve-admin',    [MutasiController::class, 'approveAdmin'])->name('mutasi.approve.admin');
    Route::post('/mutasi-aset/{id}/reject',           [MutasiController::class, 'reject'])->name('mutasi.reject');
    Route::get('/mutasi-aset/register/{id}',          [MutasiController::class, 'getRegisterData'])->name('mutasi.register.data');

    // 5. Unit & Paviliun Index
    Route::get('/unit-paviliun', [UnitController::class, 'index'])->name('unit.index');

    // 5b. Halaman Khusus Lembar Kartu Inventaris Ruangan (KIR)
    Route::get('/lembar-kir-ruangan', [UnitController::class, 'kir'])->name('kir.index');
    Route::patch('/lembar-kir-ruangan/kondisi/{id}', [UnitController::class, 'updateKondisi'])->name('kir.update_kondisi');

    // 6. Pemeliharaan Index (Read-only for Sub Admin, full for Admin)
    Route::get('/pemeliharaan', function () {
        return view('pages.pemeliharaan');
    })->name('pemeliharaan.index');

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

    // Rute Khusus Master Admin & Admin Operasional (Sub Admin Dibatasi)
    Route::middleware([RoleMiddleware::class . ':master_admin,admin'])->group(function () {
        // Berita Acara (BAST)
        Route::get('/berita-acara', [\App\Http\Controllers\BeritaAcaraController::class, 'index'])->name('bast.index');
        Route::post('/berita-acara/triwulan/{key}', [\App\Http\Controllers\BeritaAcaraController::class, 'saveTriwulan'])->name('bast.save_triwulan');
        Route::post('/berita-acara/triwulan/{key}/sign', [\App\Http\Controllers\BeritaAcaraController::class, 'signTriwulan'])->name('bast.sign_triwulan');

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
            $dbUnits = \App\Models\Unit::orderBy('nama')->get();
            return view('pages.form_astap', compact('dbMaster108', 'dbJenisPengadaans', 'dbRekeningBelanjas', 'dbUnits'));
        })->name('astap.create');

        Route::get('/astap/{id}/edit', function ($id) {
            $dbMaster108 = \App\Models\JenisAstap::getNested108();
            $dbJenisPengadaans = \App\Models\JenisPengadaan::all();
            $dbRekeningBelanjas = \App\Models\RekeningBelanja::all();
            $dbUnits = \App\Models\Unit::orderBy('nama')->get();
            $astap = \App\Models\Astap::with(['registers', 'jenisAstap', 'rekeningBelanja', 'jenisPengadaan'])->find($id);
            return view('pages.form_astap', [
                'id' => $id, 
                'astap' => $astap,
                'dbMaster108' => $dbMaster108,
                'dbJenisPengadaans' => $dbJenisPengadaans,
                'dbRekeningBelanjas' => $dbRekeningBelanjas,
                'dbUnits' => $dbUnits
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
                $jenisPrefix === '1.3.1' => $data['tanah_kode_barang'] ?? ($data['sub_rincian_kode'] ?? ($data['jenis_aset_kode'] ?? null)),
                $jenisPrefix === '1.3.2' => $data['mesin_kode_barang'] ?? ($data['sub_rincian_kode'] ?? ($data['jenis_aset_kode'] ?? null)),
                $jenisPrefix === '1.3.3' => $data['gedung_kode_barang'] ?? ($data['sub_rincian_kode'] ?? ($data['jenis_aset_kode'] ?? null)),
                $jenisPrefix === '1.3.4' => $data['jaringan_kode_barang'] ?? ($data['sub_rincian_kode'] ?? ($data['jenis_aset_kode'] ?? null)),
                $jenisPrefix === '1.3.5' => $data['lainnya_kode_barang'] ?? ($data['sub_rincian_kode'] ?? ($data['jenis_aset_kode'] ?? null)),
                $jenisPrefix === '1.5.3' => $data['atb_kode_barang'] ?? ($data['sub_rincian_kode'] ?? ($data['jenis_aset_kode'] ?? null)),
                $jenisPrefix === '1.3.6' => $data['kdp_kode_barang'] ?? ($data['sub_rincian_kode'] ?? ($data['jenis_aset_kode'] ?? null)),
                default => ($data['sub_rincian_kode'] ?? ($data['jenis_aset_kode'] ?? null))
            };
            
            $jenisAstapRecord = null;
            if ($kode108Submitted) {
                $jenisAstapRecord = \App\Models\JenisAstap::where('sub_sub_rincian_objek', $kode108Submitted)->first();
            }
            if (!$jenisAstapRecord && !empty($data['sub_rincian_kode'])) {
                $jenisAstapRecord = \App\Models\JenisAstap::where('sub_rincian_objek', $data['sub_rincian_kode'])->first()
                    ?? \App\Models\JenisAstap::where('jenis', substr($data['sub_rincian_kode'], 0, 5))->first();
            }
            if (!$jenisAstapRecord && !empty($data['jenis_aset_kode'])) {
                $jenisAstapRecord = \App\Models\JenisAstap::where('jenis', $data['jenis_aset_kode'])->first();
            }
            $jenisAstapId = $jenisAstapRecord ? $jenisAstapRecord->id : null;

            $namaInput = match(true) {
                $jenisPrefix === '1.3.1' => $data['tanah_nama_barang'] ?? ($data['sub_rincian_nama'] ?? ($data['jenis_aset_nama'] ?? 'Tanah')),
                $jenisPrefix === '1.3.2' => $data['mesin_nama_barang'] ?? ($data['sub_rincian_nama'] ?? ($data['jenis_aset_nama'] ?? 'Peralatan dan Mesin')),
                $jenisPrefix === '1.3.3' => $data['gedung_nama_barang'] ?? ($data['sub_rincian_nama'] ?? ($data['jenis_aset_nama'] ?? 'Gedung dan Bangunan')),
                $jenisPrefix === '1.3.4' => $data['jaringan_nama_barang'] ?? ($data['sub_rincian_nama'] ?? ($data['jenis_aset_nama'] ?? 'Jalan, Irigasi dan Jaringan')),
                $jenisPrefix === '1.3.5' => $data['lainnya_nama_barang'] ?? ($data['sub_rincian_nama'] ?? ($data['jenis_aset_nama'] ?? 'Aset Tetap Lainnya')),
                $jenisPrefix === '1.5.3' => $data['atb_nama_barang'] ?? ($data['sub_rincian_nama'] ?? ($data['jenis_aset_nama'] ?? 'Aset Tidak Berwujud')),
                $jenisPrefix === '1.3.6' => $data['kdp_nama_barang'] ?? ($data['sub_rincian_nama'] ?? ($data['jenis_aset_nama'] ?? 'Konstruksi Dalam Pengerjaan')),
                default => ($data['sub_rincian_nama'] ?? ($data['jenis_aset_nama'] ?? 'Aset Tetap'))
            };

            $namaBarang = 'Barang ASTAP';
            if ($jenisAstapRecord && !empty($jenisAstapRecord->uraian_sub_sub_rincian)) {
                $namaBarang = $jenisAstapRecord->uraian_sub_sub_rincian;
            } elseif ($namaInput) {
                $namaBarang = $namaInput;
            } elseif (!empty($data['nama_barang'])) {
                $namaBarang = $data['nama_barang'];
            }
            $extractAstapPayload = function($data, $jenisPrefix, $jenisAstapRecord) {
                // 1. Volume & Satuan
                $volume = (int) match(true) {
                    $jenisPrefix === '1.3.1' => $data['tanah_jumlah_bidang'] ?? ($data['jumlah_volume'] ?? 1),
                    $jenisPrefix === '1.3.2' => $data['mesin_jumlah_barang'] ?? ($data['jumlah_volume'] ?? 1),
                    $jenisPrefix === '1.3.3' => $data['gedung_jumlah_bangunan'] ?? ($data['jumlah_volume'] ?? 1),
                    $jenisPrefix === '1.3.4' => $data['jaringan_jumlah'] ?? ($data['jumlah_volume'] ?? 1),
                    $jenisPrefix === '1.3.5' => $data['lainnya_jumlah_barang'] ?? ($data['jumlah_volume'] ?? 1),
                    $jenisPrefix === '1.5.3' => $data['atb_jumlah'] ?? ($data['jumlah_volume'] ?? 1),
                    $jenisPrefix === '1.3.6' => $data['kdp_jumlah_bangunan'] ?? ($data['jumlah_volume'] ?? 1),
                    default => $data['jumlah_volume'] ?? 1
                };

                $satuan = match(true) {
                    $jenisPrefix === '1.3.1' => 'Bidang',
                    $jenisPrefix === '1.3.2' => $data['mesin_satuan'] ?? ($data['satuan'] ?? 'Unit'),
                    $jenisPrefix === '1.3.3' => $data['gedung_satuan'] ?? ($data['satuan'] ?? 'Gedung'),
                    $jenisPrefix === '1.3.4' => $data['jaringan_satuan'] ?? ($data['satuan'] ?? 'Paket'),
                    $jenisPrefix === '1.3.5' => $data['lainnya_satuan'] ?? ($data['satuan'] ?? 'Eksemplar'),
                    $jenisPrefix === '1.5.3' => $data['atb_satuan'] ?? ($data['satuan'] ?? 'Lisensi'),
                    $jenisPrefix === '1.3.6' => $data['kdp_satuan'] ?? ($data['satuan'] ?? 'Gedung'),
                    default => $data['satuan'] ?? 'Unit'
                };

                // 2. Harga Satuan & Realisasi
                $totalRealisasi = (float) ($data['jumlah_realisasi'] ?? ($data['tanah_nilai_fisik'] ?? ($data['gedung_nilai_fisik'] ?? ($data['jaringan_nilai_fisik'] ?? ($data['kdp_nilai_fisik'] ?? ($data['total_realisasi'] ?? 0))))));
                
                if ($jenisPrefix === '1.5.3' && (!empty($data['atb_items']) || !empty($data['atb_nilai_satuan']))) {
                    if (!empty($data['atb_items']) && is_array($data['atb_items'])) {
                        $atbSum = 0;
                        foreach ($data['atb_items'] as $ai) {
                            $atbSum += (max(1, (int)($ai['atb_jumlah'] ?? 1)) * (float)($ai['atb_nilai_satuan'] ?? 0)) + (float)($ai['atb_administrasi_proyek'] ?? 0);
                        }
                        if ($atbSum > 0) $totalRealisasi = $atbSum;
                    } elseif ($totalRealisasi <= 0) {
                        $totalRealisasi = ((int)($data['atb_jumlah'] ?? 1) * (float)($data['atb_nilai_satuan'] ?? 0)) + (float)($data['atb_administrasi_proyek'] ?? 0);
                    }
                }

                $hargaSatuan = (float) match(true) {
                    $jenisPrefix === '1.3.2' => $data['mesin_nilai_satuan'] ?? ($data['harga_satuan'] ?? 0),
                    $jenisPrefix === '1.3.5' => $data['lainnya_nilai_satuan'] ?? ($data['harga_satuan'] ?? 0),
                    $jenisPrefix === '1.5.3' => $data['atb_nilai_satuan'] ?? ($data['harga_satuan'] ?? 0),
                    default => ($totalRealisasi > 0 && $volume > 0) ? ($totalRealisasi / $volume) : ($data['harga_satuan'] ?? 0)
                };

                $biayaAdm = (float) ($data['biaya_administrasi_proyek'] ?? ($data['mesin_administrasi_proyek'] ?? ($data['lainnya_administrasi_proyek'] ?? ($data['atb_administrasi_proyek'] ?? 0))));
                if ($jenisPrefix === '1.5.3' && !empty($data['atb_items']) && is_array($data['atb_items'])) {
                    $atbAdmSum = 0;
                    foreach ($data['atb_items'] as $ai) {
                        $atbAdmSum += (float)($ai['atb_administrasi_proyek'] ?? 0);
                    }
                    if ($atbAdmSum > 0) $biayaAdm = $atbAdmSum;
                }

                // Extracom: murni dari pilihan user pada form
                $isExtracom = !empty($data['is_extracomtable']);

                // Spesifikasi JSON
                $specJson = [
                    'jumlah_anggaran' => $data['jumlah_anggaran'] ?? null,
                    'luas_m2' => $data['tanah_luas_m2'] ?? ($data['gedung_luas_m2'] ?? ($data['jaringan_luas_m2'] ?? ($data['kdp_luas_m2'] ?? null))),
                    'hak_tanah' => $data['tanah_hak'] ?? null,
                    'sertifikat_no' => $data['tanah_sertifikat_no'] ?? ($data['kdp_sertifikat_no'] ?? null),
                    'sertifikat_tgl' => $data['tanah_sertifikat_tgl'] ?? ($data['kdp_sertifikat_tgl'] ?? null),
                    'penggunaan' => $data['tanah_penggunaan'] ?? null,
                    'nilai_perencanaan' => $data['tanah_nilai_perencanaan'] ?? ($data['gedung_nilai_perencanaan'] ?? ($data['jaringan_nilai_perencanaan'] ?? ($data['kdp_nilai_perencanaan'] ?? 0))),
                    'nilai_pengawasan' => $data['tanah_nilai_pengawasan'] ?? ($data['gedung_nilai_pengawasan'] ?? ($data['jaringan_nilai_pengawasan'] ?? ($data['kdp_nilai_pengawasan'] ?? 0))),
                    'nilai_pip' => $data['gedung_nilai_pip'] ?? ($data['jaringan_nilai_pip'] ?? ($data['kdp_nilai_pip'] ?? 0)),
                    'merk' => $data['mesin_merk'] ?? null,
                    'type' => $data['mesin_type'] ?? null,
                    'ukuran' => $data['mesin_ukuran'] ?? ($data['lainnya_kesenian_ukuran'] ?? null),
                    'no_pabrik' => $data['mesin_no_pabrik'] ?? null,
                    'no_rangka' => $data['mesin_no_rangka'] ?? null,
                    'no_mesin' => $data['mesin_no_mesin'] ?? null,
                    'no_bpkb' => $data['mesin_no_bpkb'] ?? null,
                    'no_polisi' => $data['mesin_no_polisi'] ?? null,
                    'bahan' => $data['mesin_bahan'] ?? ($data['lainnya_kesenian_bahan'] ?? null),
                    'bertingkat' => $data['gedung_bertingkat'] ?? ($data['kdp_bangunan'] ?? null),
                    'beton' => $data['gedung_beton'] ?? ($data['kdp_beton'] ?? null),
                    'status_tanah' => $data['gedung_status_tanah'] ?? ($data['jaringan_status_tanah'] ?? ($data['kdp_status_tanah'] ?? null)),
                    'kode_aset_tanah' => $data['gedung_kode_aset_tanah'] ?? ($data['jaringan_kode_aset_tanah'] ?? ($data['kdp_kode_aset_tanah'] ?? null)),
                    'is_baru' => $data['gedung_is_baru'] ?? ($data['jaringan_is_baru'] ?? null),
                    'kapitalisasi_tahun_induk' => $data['gedung_kapitalisasi_tahun_induk'] ?? ($data['jaringan_kapitalisasi_tahun_induk'] ?? null),
                    'kapitalisasi_nilai_induk' => $data['gedung_kapitalisasi_nilai_induk'] ?? ($data['jaringan_kapitalisasi_nilai_induk'] ?? 0),
                    'konstruksi' => $data['jaringan_konstruksi'] ?? null,
                    'panjang_m' => $data['jaringan_panjang_m'] ?? null,
                    'lebar_m' => $data['jaringan_lebar_m'] ?? null,
                    'kib_e_sub_type' => $data['kib_e_sub_type'] ?? null,
                    'buku_judul' => $data['lainnya_buku_judul'] ?? null,
                    'buku_pencipta' => $data['lainnya_buku_pencipta'] ?? null,
                    'buku_spesifikasi' => $data['lainnya_buku_spesifikasi'] ?? null,
                    'kesenian_asal' => $data['lainnya_kesenian_asal'] ?? null,
                    'kesenian_pencipta' => $data['lainnya_kesenian_pencipta'] ?? null,
                    'kesenian_spesifikasi' => $data['lainnya_kesenian_spesifikasi'] ?? null,
                    'kesenian_bahan' => $data['lainnya_kesenian_bahan'] ?? null,
                    'kesenian_ukuran' => $data['lainnya_kesenian_ukuran'] ?? null,
                    'hewan_judul' => $data['lainnya_hewan_judul'] ?? ($data['lainnya_hewan_jenis'] ?? null),
                    'hewan_jenis' => $data['lainnya_hewan_jenis'] ?? null,
                    'hewan_spesifikasi' => $data['lainnya_hewan_spesifikasi'] ?? null,
                    'atb_judul' => $data['atb_judul_nama'] ?? ($data['atb_judul'] ?? null),
                    'atb_pencipta' => $data['atb_pencipta'] ?? null,
                    'atb_jenis_lisensi' => $data['atb_jenis_lisensi'] ?? null,
                    'atb_spesifikasi' => $data['atb_spesifikasi'] ?? null,
                    'progres_persen' => $data['kdp_progres_persen'] ?? null,
                    'tgl_mulai' => $data['kdp_tgl_mulai'] ?? null,
                    'tgl_target_selesai' => $data['kdp_tgl_target_selesai'] ?? null,
                    'ruang_pemegang' => $data['ruang_pemegang_mesin'] ?? ($data['ruang_pemegang_lainnya'] ?? ($data['ruang_pemegang_atb'] ?? null)),
                    'penyedia_telepon' => $data['penyedia_telepon'] ?? null,
                ];

                $specJson = array_filter($specJson, fn($v) => !is_null($v) && $v !== '');

                // KIB E: Simpan seluruh lainnya_items (multi-item repeater) agar item #2, #3, dst tidak hilang
                if (!empty($data['lainnya_items']) && is_array($data['lainnya_items']) && count($data['lainnya_items']) > 0) {
                    $cleanedItems = array_map(function($item) {
                        return array_filter($item, fn($v) => !is_null($v) && $v !== '' && $v !== false);
                    }, $data['lainnya_items']);
                    $specJson['lainnya_items'] = array_values($cleanedItems);
                }

                // KIB F: Simpan seluruh kdp_items (multi-item repeater)
                if (!empty($data['kdp_items']) && is_array($data['kdp_items']) && count($data['kdp_items']) > 0) {
                    $cleanedKdp = array_map(function($item) {
                        return array_filter($item, fn($v) => !is_null($v) && $v !== '' && $v !== false);
                    }, $data['kdp_items']);
                    $specJson['kdp_items'] = array_values($cleanedKdp);
                }

                // ATB: Simpan seluruh atb_items (multi-item repeater)
                if (!empty($data['atb_items']) && is_array($data['atb_items']) && count($data['atb_items']) > 0) {
                    $cleanedAtb = array_map(function($item) {
                        return array_filter($item, fn($v) => !is_null($v) && $v !== '' && $v !== false);
                    }, $data['atb_items']);
                    $specJson['atb_items'] = array_values($cleanedAtb);
                    // Sync legacy fields dari item pertama
                    $firstAtb = $data['atb_items'][0] ?? [];
                    if (!isset($specJson['atb_judul']) && !empty($firstAtb['atb_judul_nama'])) {
                        $specJson['atb_judul'] = $firstAtb['atb_judul_nama'];
                    }
                    if (!isset($specJson['atb_pencipta']) && !empty($firstAtb['atb_pencipta'])) {
                        $specJson['atb_pencipta'] = $firstAtb['atb_pencipta'];
                    }
                    if (!isset($specJson['atb_spesifikasi']) && !empty($firstAtb['atb_spesifikasi'])) {
                        $specJson['atb_spesifikasi'] = $firstAtb['atb_spesifikasi'];
                    }
                    if (!isset($specJson['ruang_pemegang']) && !empty($firstAtb['atb_ruang_pemegang'])) {
                        $specJson['ruang_pemegang'] = $firstAtb['atb_ruang_pemegang'];
                    }
                }

                return [
                    'volume' => max(1, $volume),
                    'satuan' => $satuan,
                    'harga_satuan' => $hargaSatuan,
                    'total_realisasi' => $totalRealisasi,
                    'biaya_administrasi_proyek' => $biayaAdm,
                    'is_extracomtable' => $isExtracom,
                    'spesifikasi_json' => $specJson
                ];
            };

            // =========================================================================
            // CHECK JENIS ASET
            // =========================================================================
            $isTanah = ($jenisPrefix === '1.3.1' || str_starts_with($jenisPrefix, '1.3.1') || str_starts_with($kode108Submitted ?? '', '1.3.1') || ($jenisAstapRecord && str_starts_with($jenisAstapRecord->sub_sub_rincian_objek ?? '', '1.3.1')));
            $isMesin = ($jenisPrefix === '1.3.2' || str_starts_with($jenisPrefix, '1.3.2') || str_starts_with($kode108Submitted ?? '', '1.3.2') || ($jenisAstapRecord && str_starts_with($jenisAstapRecord->sub_sub_rincian_objek ?? '', '1.3.2')));
            $isGedung = ($jenisPrefix === '1.3.3' || str_starts_with($jenisPrefix, '1.3.3') || str_starts_with($kode108Submitted ?? '', '1.3.3') || ($jenisAstapRecord && str_starts_with($jenisAstapRecord->sub_sub_rincian_objek ?? '', '1.3.3')));
            $isJaringan = ($jenisPrefix === '1.3.4' || str_starts_with($jenisPrefix, '1.3.4') || str_starts_with($kode108Submitted ?? '', '1.3.4') || ($jenisAstapRecord && str_starts_with($jenisAstapRecord->sub_sub_rincian_objek ?? '', '1.3.4')));
            $isKdp = ($jenisPrefix === '1.3.6' || str_starts_with($jenisPrefix, '1.3.6') || str_starts_with($kode108Submitted ?? '', '1.3.6') || ($jenisAstapRecord && str_starts_with($jenisAstapRecord->sub_sub_rincian_objek ?? '', '1.3.6')));
            $isAtb = ($jenisPrefix === '1.5.3' || str_starts_with($jenisPrefix, '1.5.3') || str_starts_with($kode108Submitted ?? '', '1.5.3') || ($jenisAstapRecord && str_starts_with($jenisAstapRecord->sub_sub_rincian_objek ?? '', '1.5.3')));

            $hasTanahItems = !empty($data['tanah_items']) && is_array($data['tanah_items']) && count($data['tanah_items']) > 0;
            $hasMesinItems = !empty($data['mesin_items']) && is_array($data['mesin_items']) && count($data['mesin_items']) > 0;
            $hasGedungItems = !empty($data['gedung_items']) && is_array($data['gedung_items']) && count($data['gedung_items']) > 0;
            $hasJaringanItems = !empty($data['jaringan_items']) && is_array($data['jaringan_items']) && count($data['jaringan_items']) > 0;
            $hasKdpItems = !empty($data['kdp_items']) && is_array($data['kdp_items']) && count($data['kdp_items']) > 0;
            $hasAtbItems = !empty($data['atb_items']) && is_array($data['atb_items']) && count($data['atb_items']) > 0;

            // =========================================================================
            // KHUSUS TANAH (1.3.1): MULTI-ITEM REPEATER BIDANG TANAH
            // =========================================================================
            if ($isTanah && $hasTanahItems) {
                $totalBidang = 0;
                $totalLuas = 0;
                $totalPerencanaan = 0;
                $totalFisik = 0;
                $totalPengawasan = 0;
                $allSertifikat = [];
                $allAlamat = [];

                foreach ($data['tanah_items'] as $tItem) {
                    $bidangCount = max(1, (int)($tItem['tanah_jumlah_bidang'] ?? 1));
                    $totalBidang += $bidangCount;
                    $totalLuas += (float)($tItem['tanah_luas_m2'] ?? 0);
                    $totalPerencanaan += (float)($tItem['tanah_nilai_perencanaan'] ?? 0);
                    $totalFisik += (float)($tItem['tanah_nilai_fisik'] ?? 0);
                    $totalPengawasan += (float)($tItem['tanah_nilai_pengawasan'] ?? 0);
                    if (!empty($tItem['tanah_sertifikat_no'])) $allSertifikat[] = $tItem['tanah_sertifikat_no'];
                    if (!empty($tItem['tanah_alamat'])) $allAlamat[] = $tItem['tanah_alamat'];
                }

                $totalRealisasi = $totalPerencanaan + $totalFisik + $totalPengawasan;
                $hargaSatuan = $totalBidang > 0 ? ($totalRealisasi / $totalBidang) : $totalRealisasi;
                $tahun = $data['tahun_perolehan'] ?? ($data['tahun_anggaran'] ?? date('Y'));
                $firstItem = $data['tanah_items'][0] ?? [];

                // Hitung running nomor register awal untuk tahun dan kode 108 ini
                $kode108Submitted = $data['tanah_kode_barang'] ?? ($data['sub_rincian_kode'] ?? ($data['jenis_aset_kode'] ?? null));
                $kode108Clean = '131000000000';
                if ($jenisAstapRecord && !empty($jenisAstapRecord->sub_sub_rincian_objek)) {
                    $kode108Clean = str_replace('.', '', $jenisAstapRecord->sub_sub_rincian_objek);
                } elseif (!empty($kode108Submitted)) {
                    $kode108Clean = str_replace('.', '', $kode108Submitted);
                }

                $maxRegInt = \App\Models\AstapRegister::where('tahun_perolehan', $tahun)
                    ->where(function($q) use ($jenisAstapId) {
                        if ($jenisAstapId) {
                            $q->whereHas('astap', fn($sq) => $sq->where('jenis_astap_id', $jenisAstapId));
                        }
                    })
                    ->max('no_register_int') ?? 0;

                $runningRegNum = (int) $maxRegInt;

                $specJson = [
                    'jumlah_anggaran' => $data['jumlah_anggaran'] ?? null,
                    'luas_m2' => $totalLuas,
                    'hak_tanah' => $firstItem['tanah_hak'] ?? 'Hak Pakai',
                    'sertifikat_no' => count($allSertifikat) > 0 ? implode(', ', $allSertifikat) : ($firstItem['tanah_sertifikat_no'] ?? null),
                    'sertifikat_tgl' => $firstItem['tanah_sertifikat_tgl'] ?? null,
                    'penggunaan' => $firstItem['tanah_penggunaan'] ?? 'Bangunan Rumah Sakit & Fasilitas',
                    'tanah_jumlah_bidang' => $totalBidang,
                    'nilai_perencanaan' => $totalPerencanaan,
                    'nilai_fisik' => $totalFisik,
                    'nilai_pengawasan' => $totalPengawasan,
                    'tanah_items' => $data['tanah_items']
                ];
                $specJson = array_filter($specJson, fn($v) => !is_null($v) && $v !== '');

                $astap = \Illuminate\Support\Facades\DB::transaction(function() use (
                    $data, $jenisPengadaanId, $rekeningBelanjaId, $jenisAstapId, $namaBarang,
                    $tahun, $totalBidang, $totalRealisasi, $hargaSatuan, $firstItem, $allAlamat,
                    $specJson, $kode108Clean, &$runningRegNum
                ) {
                    // 1. Buat 1 data ASTAP perolehan belanja modal tanah dengan total volume & total realisasi
                    $astapItem = \App\Models\Astap::create([
                        'jenis_pengadaan_id' => $jenisPengadaanId,
                        'rekening_belanja_id' => $rekeningBelanjaId,
                        'jenis_astap_id' => $jenisAstapId,
                        'nama_barang' => $namaBarang,
                        'tahun_perolehan' => $tahun,
                        'triwulan' => $data['triwulan'] ?? 'TW I',
                        'jumlah_volume' => $totalBidang,
                        'satuan' => 'Bidang',
                        'harga_satuan' => $hargaSatuan,
                        'jumlah_anggaran' => !empty($data['jumlah_anggaran']) ? (float) $data['jumlah_anggaran'] : $totalRealisasi,
                        'total_realisasi' => $totalRealisasi,
                        'biaya_administrasi_proyek' => 0,
                        'is_extracomtable' => false,
                        'spk_nomor' => $data['spk_nomor'] ?? null,
                        'spk_tanggal' => $data['spk_tanggal'] ?? null,
                        'surat_pesanan_nomor' => $data['surat_pesanan_nomor'] ?? null,
                        'surat_pesanan_tanggal' => $data['surat_pesanan_tanggal'] ?? null,
                        'kwitansi_nomor' => $data['kwitansi_nomor'] ?? null,
                        'kwitansi_tanggal' => $data['kwitansi_tanggal'] ?? null,
                        'faktur_nomor' => $data['faktur_nomor'] ?? null,
                        'faktur_tanggal' => $data['faktur_tanggal'] ?? null,
                        'sp2d_nomor' => $data['sp2d_nomor'] ?? null,
                        'sp2d_tanggal' => $data['sp2d_tanggal'] ?? null,
                        'bast_dokumen_nomor' => $data['bast_dokumen_nomor'] ?? null,
                        'bast_dokumen_tanggal' => $data['bast_dokumen_tanggal'] ?? null,
                        'alamat_barang' => $firstItem['tanah_alamat'] ?? ($data['alamat_barang'] ?? (count($allAlamat) > 0 ? implode('; ', $allAlamat) : null)),
                        'penyedia_nama' => $data['penyedia_nama'] ?? null,
                        'penyedia_pemilik' => $data['penyedia_pemilik'] ?? null,
                        'penyedia_rekening_nama' => $data['penyedia_rekening_nama'] ?? null,
                        'penyedia_rekening_nomor' => $data['penyedia_rekening_nomor'] ?? null,
                        'penyedia_alamat' => $data['penyedia_alamat'] ?? null,
                        'ppk_nama' => $data['ppk_nama'] ?? null,
                        'ppk_nip' => $data['ppk_nip'] ?? null,
                        'keterangan_tambahan' => $data['keterangan_tambahan'] ?? ($data['keterangan'] ?? null),
                        'spesifikasi_json' => $specJson,
                        'user_id' => auth()->id()
                    ]);

                    // 2. Buat seluruh AstapRegister untuk masing-masing item bidang tanah (1 NIBAR per baris tanah_items)
                    foreach ($data['tanah_items'] as $itemIdx => $tItem) {
                        $alamatLokasi = !empty($tItem['tanah_alamat']) ? $tItem['tanah_alamat'] : ($data['alamat_barang'] ?? 'Bidang #' . ($itemIdx + 1));
                        $rawKondisi = strtoupper(trim((string)($tItem['tanah_kondisi'] ?? 'Baik')));
                        $kondisiStr = ($rawKondisi === 'KB' || $rawKondisi === 'KURANG BAIK') ? 'Kurang Baik' : (($rawKondisi === 'RB' || $rawKondisi === 'RUSAK BERAT' || $rawKondisi === 'RUSAK') ? 'Rusak Berat' : (($rawKondisi === 'RR' || $rawKondisi === 'RUSAK RINGAN') ? 'Rusak Ringan' : 'Baik'));

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
                            'astap_id' => $astapItem->id,
                            'tahun_perolehan' => $tahun,
                            'no_register_int' => $runningRegNum,
                            'no_register' => $nibar,
                            'nibar' => $nibar,
                            'qr_code_path' => $qrPath,
                            'ruang_pemegang' => null,
                            'kondisi' => $kondisiStr,
                            'status' => 'Tersedia'
                        ]);
                    }

                    return $astapItem;
                });

                // Kirim Notifikasi Sistem ke Admin & Super Admin
                try {
                    \App\Services\NotificationService::sendToAdminAndMaster(
                        "Aset Tanah Baru ({$totalBidang} Bidang): {$namaBarang}",
                        "{$totalBidang} Bidang Tanah • " . $tahun,
                        'astap',
                        route('astap.index')
                    );
                } catch (\Throwable $e) {
                    \Log::warning("Gagal kirim notif astap store tanah: " . $e->getMessage());
                }

                session()->flash('success', 'Sebanyak ' . $totalBidang . ' Bidang Tanah "' . $namaBarang . '" berhasil disimpan ke database SIMAT-RK.');
                return response()->json([
                    'success' => true,
                    'message' => 'Sebanyak ' . $totalBidang . ' Bidang Tanah berhasil didaftarkan ke database SIMAT-RK!'
                ]);
            } elseif ($isMesin && $hasMesinItems) {
                // =========================================================================
                // KHUSUS PERALATAN DAN MESIN / EXTRACOM MULTI-ITEM REPEATER MESIN
                // =========================================================================
                $totalVolume = 0;
                $totalRealisasi = 0;
                $totalBiayaAdm = 0;
                $allMerk = [];
                $allType = [];
                $allUkuran = [];
                $allBahan = [];
                $allPabrik = [];

                foreach ($data['mesin_items'] as $mItem) {
                    $qty = max(1, (int)($mItem['mesin_jumlah_barang'] ?? 1));
                    $nilaiSatuan = (float)($mItem['mesin_nilai_satuan'] ?? 0);
                    $biayaAdmItem = (float)($mItem['mesin_administrasi_proyek'] ?? 0);
                    
                    $totalVolume += $qty;
                    $totalRealisasi += ($qty * $nilaiSatuan) + $biayaAdmItem;
                    $totalBiayaAdm += $biayaAdmItem;

                    if (!empty($mItem['mesin_merk'])) $allMerk[] = $mItem['mesin_merk'];
                    if (!empty($mItem['mesin_type'])) $allType[] = $mItem['mesin_type'];
                    if (!empty($mItem['mesin_ukuran'])) $allUkuran[] = $mItem['mesin_ukuran'];
                    if (!empty($mItem['mesin_bahan'])) $allBahan[] = $mItem['mesin_bahan'];
                    if (!empty($mItem['mesin_no_pabrik'])) $allPabrik[] = $mItem['mesin_no_pabrik'];
                }

                $firstItem = $data['mesin_items'][0] ?? [];
                $satuan = $firstItem['mesin_satuan'] ?? 'Unit';
                $hargaSatuanRata = $totalVolume > 0 ? ($totalRealisasi / $totalVolume) : 0;
                $tahun = $data['tahun_perolehan'] ?? ($data['tahun_anggaran'] ?? date('Y'));
                
                $isExtracom = !empty($data['is_extracomtable']);

                // Hitung running nomor register
                $kode108Submitted = $data['mesin_kode_barang'] ?? ($data['sub_rincian_kode'] ?? ($data['jenis_aset_kode'] ?? null));
                $kode108Clean = '132000000000';
                if ($jenisAstapRecord && !empty($jenisAstapRecord->sub_sub_rincian_objek)) {
                    $kode108Clean = str_replace('.', '', $jenisAstapRecord->sub_sub_rincian_objek);
                } elseif (!empty($kode108Submitted)) {
                    $kode108Clean = str_replace('.', '', $kode108Submitted);
                }

                $maxRegInt = \App\Models\AstapRegister::where('tahun_perolehan', $tahun)
                    ->where(function($q) use ($jenisAstapId) {
                        if ($jenisAstapId) {
                            $q->whereHas('astap', fn($sq) => $sq->where('jenis_astap_id', $jenisAstapId));
                        }
                    })
                    ->max('no_register_int') ?? 0;

                $runningRegNum = (int) $maxRegInt;

                $specJson = [
                    'jumlah_anggaran' => $data['jumlah_anggaran'] ?? null,
                    'merk' => count($allMerk) > 0 ? implode(', ', array_unique($allMerk)) : ($firstItem['mesin_merk'] ?? null),
                    'type' => count($allType) > 0 ? implode(', ', array_unique($allType)) : ($firstItem['mesin_type'] ?? null),
                    'ukuran' => count($allUkuran) > 0 ? implode(', ', array_unique($allUkuran)) : ($firstItem['mesin_ukuran'] ?? null),
                    'no_pabrik' => count($allPabrik) > 0 ? implode(', ', array_unique($allPabrik)) : ($firstItem['mesin_no_pabrik'] ?? null),
                    'no_rangka' => $firstItem['mesin_no_rangka'] ?? null,
                    'no_mesin' => $firstItem['mesin_no_mesin'] ?? null,
                    'no_bpkb' => $firstItem['mesin_no_bpkb'] ?? null,
                    'no_polisi' => $firstItem['mesin_no_polisi'] ?? null,
                    'bahan' => count($allBahan) > 0 ? implode(', ', array_unique($allBahan)) : ($firstItem['mesin_bahan'] ?? null),
                    'ruang_pemegang' => $firstItem['ruang_pemegang_mesin'] ?? null,
                    'mesin_items' => $data['mesin_items']
                ];
                $specJson = array_filter($specJson, fn($v) => !is_null($v) && $v !== '');

                $astap = \Illuminate\Support\Facades\DB::transaction(function() use (
                    $data, $jenisPengadaanId, $rekeningBelanjaId, $jenisAstapId, $namaBarang,
                    $tahun, $totalVolume, $satuan, $hargaSatuanRata, $totalRealisasi, $totalBiayaAdm,
                    $isExtracom, $specJson, $kode108Clean, &$runningRegNum
                ) {
                    $astapItem = \App\Models\Astap::create([
                        'jenis_pengadaan_id' => $jenisPengadaanId,
                        'rekening_belanja_id' => $rekeningBelanjaId,
                        'jenis_astap_id' => $jenisAstapId,
                        'nama_barang' => $namaBarang,
                        'tahun_perolehan' => $tahun,
                        'triwulan' => $data['triwulan'] ?? 'TW I',
                        'jumlah_volume' => $totalVolume,
                        'satuan' => $satuan,
                        'harga_satuan' => $hargaSatuanRata,
                        'jumlah_anggaran' => !empty($data['jumlah_anggaran']) ? (float) $data['jumlah_anggaran'] : $totalRealisasi,
                        'total_realisasi' => $totalRealisasi,
                        'biaya_administrasi_proyek' => $totalBiayaAdm,
                        'is_extracomtable' => $isExtracom,
                        'spk_nomor' => $data['spk_nomor'] ?? null,
                        'spk_tanggal' => $data['spk_tanggal'] ?? null,
                        'surat_pesanan_nomor' => $data['surat_pesanan_nomor'] ?? null,
                        'surat_pesanan_tanggal' => $data['surat_pesanan_tanggal'] ?? null,
                        'kwitansi_nomor' => $data['kwitansi_nomor'] ?? null,
                        'kwitansi_tanggal' => $data['kwitansi_tanggal'] ?? null,
                        'faktur_nomor' => $data['faktur_nomor'] ?? null,
                        'faktur_tanggal' => $data['faktur_tanggal'] ?? null,
                        'sp2d_nomor' => $data['sp2d_nomor'] ?? null,
                        'sp2d_tanggal' => $data['sp2d_tanggal'] ?? null,
                        'bast_dokumen_nomor' => $data['bast_dokumen_nomor'] ?? null,
                        'bast_dokumen_tanggal' => $data['bast_dokumen_tanggal'] ?? null,
                        'alamat_barang' => $data['alamat_barang'] ?? null,
                        'penyedia_nama' => $data['penyedia_nama'] ?? null,
                        'penyedia_pemilik' => $data['penyedia_pemilik'] ?? null,
                        'penyedia_rekening_nama' => $data['penyedia_rekening_nama'] ?? null,
                        'penyedia_rekening_nomor' => $data['penyedia_rekening_nomor'] ?? null,
                        'penyedia_alamat' => $data['penyedia_alamat'] ?? null,
                        'ppk_nama' => $data['ppk_nama'] ?? null,
                        'ppk_nip' => $data['ppk_nip'] ?? null,
                        'keterangan_tambahan' => $data['keterangan_tambahan'] ?? ($data['keterangan'] ?? null),
                        'spesifikasi_json' => $specJson,
                        'user_id' => auth()->id()
                    ]);

                    // Buat AstapRegister untuk setiap unit dalam mesin_items
                    foreach ($data['mesin_items'] as $itemIdx => $mItem) {
                        $itemQty = max(1, (int)($mItem['mesin_jumlah_barang'] ?? 1));
                        $rawKondisi = strtoupper(trim((string)($mItem['mesin_kondisi'] ?? 'Baik')));
                        $kondisiStr = ($rawKondisi === 'KB' || $rawKondisi === 'KURANG BAIK') ? 'Kurang Baik' : (($rawKondisi === 'RB' || $rawKondisi === 'RUSAK BERAT' || $rawKondisi === 'RUSAK') ? 'Rusak Berat' : (($rawKondisi === 'RR' || $rawKondisi === 'RUSAK RINGAN') ? 'Rusak Ringan' : 'Baik'));
                        $ruangPemegang = $mItem['ruang_pemegang_mesin'] ?? ($mItem['ruang_pemegang'] ?? null);

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
                                'astap_id' => $astapItem->id,
                                'tahun_perolehan' => $tahun,
                                'no_register_int' => $runningRegNum,
                                'no_register' => $nibar,
                                'nibar' => $nibar,
                                'qr_code_path' => $qrPath,
                                'ruang_pemegang' => $ruangPemegang,
                                'kondisi' => $kondisiStr,
                                'status' => 'Tersedia'
                            ]);
                        }
                    }

                    return $astapItem;
                });

                // Kirim Notifikasi Sistem ke Admin & Super Admin
                try {
                    \App\Services\NotificationService::sendToAdminAndMaster(
                        "Aset Peralatan & Mesin Baru ({$totalVolume} {$satuan}): {$namaBarang}",
                        "{$totalVolume} {$satuan} • " . $tahun,
                        'astap',
                        route('astap.index')
                    );
                } catch (\Throwable $e) {
                    \Log::warning("Gagal kirim notif astap store mesin: " . $e->getMessage());
                }

                session()->flash('success', 'Sebanyak ' . $totalVolume . ' ' . $satuan . ' Peralatan & Mesin "' . $namaBarang . '" berhasil disimpan ke database SIMAT-RK.');
                return response()->json([
                    'success' => true,
                    'message' => 'Sebanyak ' . $totalVolume . ' ' . $satuan . ' Peralatan & Mesin berhasil didaftarkan ke database SIMAT-RK!'
                ]);
            } elseif ($isGedung && $hasGedungItems) {
                $totalBangunan = 0;
                $totalLuas = 0;
                $totalPerencanaan = 0;
                $totalFisik = 0;
                $totalPengawasan = 0;
                $totalPip = 0;
                $allAlamat = [];

                foreach ($data['gedung_items'] as $gItem) {
                    $bCount = max(1, (int)($gItem['gedung_jumlah_bangunan'] ?? 1));
                    $totalBangunan += $bCount;
                    $totalLuas += (float)($gItem['gedung_luas_m2'] ?? 0);
                    $totalPerencanaan += (float)($gItem['gedung_nilai_perencanaan'] ?? 0);
                    $totalFisik += (float)($gItem['gedung_nilai_fisik'] ?? 0);
                    $totalPengawasan += (float)($gItem['gedung_nilai_pengawasan'] ?? 0);
                    $totalPip += (float)($gItem['gedung_nilai_pip'] ?? 0);
                    if (!empty($gItem['gedung_alamat'])) $allAlamat[] = $gItem['gedung_alamat'];
                }

                $totalRealisasi = $totalPerencanaan + $totalFisik + $totalPengawasan + $totalPip;
                $hargaSatuanRata = $totalBangunan > 0 ? ($totalRealisasi / $totalBangunan) : $totalRealisasi;
                $tahun = $data['tahun_perolehan'] ?? ($data['tahun_anggaran'] ?? date('Y'));
                $firstItem = $data['gedung_items'][0] ?? [];

                if (!empty($firstItem['gedung_nama_barang'])) {
                    $namaBarang = $firstItem['gedung_nama_barang'];
                }

                $kode108Clean = '133000000000';
                if ($jenisAstapRecord && !empty($jenisAstapRecord->sub_sub_rincian_objek)) {
                    $kode108Clean = str_replace('.', '', $jenisAstapRecord->sub_sub_rincian_objek);
                } elseif (!empty($data['gedung_kode_barang'])) {
                    $kode108Clean = str_replace('.', '', $data['gedung_kode_barang']);
                } elseif (!empty($firstItem['gedung_kode_barang'])) {
                    $kode108Clean = str_replace('.', '', $firstItem['gedung_kode_barang']);
                }

                $maxRegInt = \App\Models\AstapRegister::where('tahun_perolehan', $tahun)
                    ->where(function($q) use ($jenisAstapId) {
                        if ($jenisAstapId) {
                            $q->whereHas('astap', fn($sq) => $sq->where('jenis_astap_id', $jenisAstapId));
                        }
                    })
                    ->max('no_register_int') ?? 0;
                $runningRegNum = (int) $maxRegInt;

                $satuan = $firstItem['gedung_satuan'] ?? 'Gedung';
                $specJson = [
                    'jumlah_anggaran' => $data['jumlah_anggaran'] ?? null,
                    'luas_m2' => $totalLuas,
                    'bertingkat' => $firstItem['gedung_bertingkat'] ?? 'Bertingkat',
                    'beton' => $firstItem['gedung_beton'] ?? 'Beton',
                    'status_tanah' => $firstItem['gedung_status_tanah'] ?? 'Tanah Hak Pakai RSUD',
                    'kode_aset_tanah' => $firstItem['gedung_kode_aset_tanah'] ?? '1.3.1.01.01.02.013',
                    'is_baru' => $firstItem['gedung_is_baru'] ?? 'Baru',
                    'kapitalisasi_tahun_induk' => $firstItem['gedung_kapitalisasi_tahun_induk'] ?? null,
                    'kapitalisasi_nilai_induk' => $firstItem['gedung_kapitalisasi_nilai_induk'] ?? 0,
                    'gedung_jumlah_bangunan' => $totalBangunan,
                    'nilai_perencanaan' => $totalPerencanaan,
                    'nilai_fisik' => $totalFisik,
                    'nilai_pengawasan' => $totalPengawasan,
                    'nilai_pip' => $totalPip,
                    'gedung_items' => $data['gedung_items']
                ];
                $specJson = array_filter($specJson, fn($v) => !is_null($v) && $v !== '');

                $astap = \Illuminate\Support\Facades\DB::transaction(function() use (
                    $data, $jenisPengadaanId, $rekeningBelanjaId, $jenisAstapId, $namaBarang,
                    $tahun, $totalBangunan, $satuan, $hargaSatuanRata, $totalRealisasi,
                    $specJson, $allAlamat, $kode108Clean, &$runningRegNum
                ) {
                    $alamatStr = count($allAlamat) > 0 ? implode('; ', array_unique($allAlamat)) : ($data['alamat_barang'] ?? null);

                    $astapItem = \App\Models\Astap::create([
                        'jenis_pengadaan_id' => $jenisPengadaanId,
                        'rekening_belanja_id' => $rekeningBelanjaId,
                        'jenis_astap_id' => $jenisAstapId,
                        'nama_barang' => $namaBarang,
                        'tahun_perolehan' => $tahun,
                        'triwulan' => $data['triwulan'] ?? 'TW I',
                        'jumlah_volume' => $totalBangunan,
                        'satuan' => $satuan,
                        'harga_satuan' => $hargaSatuanRata,
                        'jumlah_anggaran' => !empty($data['jumlah_anggaran']) ? (float) $data['jumlah_anggaran'] : $totalRealisasi,
                        'total_realisasi' => $totalRealisasi,
                        'biaya_administrasi_proyek' => 0,
                        'is_extracomtable' => false,
                        'spk_nomor' => $data['spk_nomor'] ?? null,
                        'spk_tanggal' => $data['spk_tanggal'] ?? null,
                        'surat_pesanan_nomor' => $data['surat_pesanan_nomor'] ?? null,
                        'surat_pesanan_tanggal' => $data['surat_pesanan_tanggal'] ?? null,
                        'kwitansi_nomor' => $data['kwitansi_nomor'] ?? null,
                        'kwitansi_tanggal' => $data['kwitansi_tanggal'] ?? null,
                        'faktur_nomor' => $data['faktur_nomor'] ?? null,
                        'faktur_tanggal' => $data['faktur_tanggal'] ?? null,
                        'sp2d_nomor' => $data['sp2d_nomor'] ?? null,
                        'sp2d_tanggal' => $data['sp2d_tanggal'] ?? null,
                        'bast_dokumen_nomor' => $data['bast_dokumen_nomor'] ?? null,
                        'bast_dokumen_tanggal' => $data['bast_dokumen_tanggal'] ?? null,
                        'alamat_barang' => $alamatStr,
                        'penyedia_nama' => $data['penyedia_nama'] ?? null,
                        'penyedia_pemilik' => $data['penyedia_pemilik'] ?? null,
                        'penyedia_rekening_nama' => $data['penyedia_rekening_nama'] ?? null,
                        'penyedia_rekening_nomor' => $data['penyedia_rekening_nomor'] ?? null,
                        'penyedia_alamat' => $data['penyedia_alamat'] ?? null,
                        'ppk_nama' => $data['ppk_nama'] ?? null,
                        'ppk_nip' => $data['ppk_nip'] ?? null,
                        'keterangan_tambahan' => $data['keterangan_tambahan'] ?? ($data['keterangan'] ?? null),
                        'spesifikasi_json' => $specJson,
                        'user_id' => auth()->id()
                    ]);

                    // Buat AstapRegister untuk setiap unit dalam gedung_items
                    foreach ($data['gedung_items'] as $itemIdx => $gItem) {
                        $itemQty = max(1, (int)($gItem['gedung_jumlah_bangunan'] ?? 1));
                        $rawKondisi = strtoupper(trim((string)($gItem['gedung_kondisi'] ?? 'Baik')));
                        $kondisiStr = ($rawKondisi === 'KB' || $rawKondisi === 'KURANG BAIK') ? 'Kurang Baik' : (($rawKondisi === 'RB' || $rawKondisi === 'RUSAK BERAT' || $rawKondisi === 'RUSAK') ? 'Rusak Berat' : 'Baik');

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
                                'astap_id' => $astapItem->id,
                                'tahun_perolehan' => $tahun,
                                'no_register_int' => $runningRegNum,
                                'no_register' => $nibar,
                                'nibar' => $nibar,
                                'qr_code_path' => $qrPath,
                                'ruang_pemegang' => null,
                                'kondisi' => $kondisiStr,
                                'status' => 'Tersedia'
                            ]);
                        }
                    }

                    return $astapItem;
                });

                // Kirim Notifikasi Sistem ke Admin & Super Admin
                try {
                    \App\Services\NotificationService::sendToAdminAndMaster(
                        "Aset Gedung & Bangunan Baru ({$totalBangunan} {$satuan}): {$namaBarang}",
                        "{$totalBangunan} {$satuan} • " . $tahun,
                        'astap',
                        route('astap.index')
                    );
                } catch (\Throwable $e) {
                    \Log::warning("Gagal kirim notif astap store gedung: " . $e->getMessage());
                }

                session()->flash('success', 'Sebanyak ' . $totalBangunan . ' ' . $satuan . ' Gedung & Bangunan "' . $namaBarang . '" berhasil disimpan ke database SIMAT-RK.');
                return response()->json([
                    'success' => true,
                    'message' => 'Sebanyak ' . $totalBangunan . ' ' . $satuan . ' Gedung & Bangunan berhasil didaftarkan ke database SIMAT-RK!'
                ]);
            } elseif ($isJaringan && $hasJaringanItems) {
                $totalJaringan = 0;
                $totalLuas = 0;
                $totalPerencanaan = 0;
                $totalFisik = 0;
                $totalPengawasan = 0;
                $totalAp = 0;
                $allAlamat = [];

                foreach ($data['jaringan_items'] as $jItem) {
                    $jCount = max(1, (int)($jItem['jaringan_jumlah'] ?? ($jItem['jaringan_jumlah_jaringan'] ?? 1)));
                    $totalJaringan += $jCount;
                    $totalLuas += (float)($jItem['jaringan_luas_m2'] ?? 0);
                    $totalPerencanaan += (float)($jItem['jaringan_nilai_perencanaan'] ?? 0);
                    $totalFisik += (float)($jItem['jaringan_nilai_fisik'] ?? 0);
                    $totalPengawasan += (float)($jItem['jaringan_nilai_pengawasan'] ?? 0);
                    $totalAp += (float)($jItem['jaringan_nilai_ap'] ?? ($jItem['jaringan_nilai_pip'] ?? 0));
                    if (!empty($jItem['jaringan_alamat'])) $allAlamat[] = $jItem['jaringan_alamat'];
                }

                $totalRealisasi = $totalPerencanaan + $totalFisik + $totalPengawasan + $totalAp;
                $hargaSatuanRata = $totalJaringan > 0 ? ($totalRealisasi / $totalJaringan) : $totalRealisasi;
                $tahun = $data['tahun_perolehan'] ?? ($data['tahun_anggaran'] ?? date('Y'));
                $firstItem = $data['jaringan_items'][0] ?? [];

                if (!empty($firstItem['jaringan_nama_barang'])) {
                    $namaBarang = $firstItem['jaringan_nama_barang'];
                }

                $kode108Clean = '134000000000';
                if ($jenisAstapRecord && !empty($jenisAstapRecord->sub_sub_rincian_objek)) {
                    $kode108Clean = str_replace('.', '', $jenisAstapRecord->sub_sub_rincian_objek);
                } elseif (!empty($data['jaringan_kode_barang'])) {
                    $kode108Clean = str_replace('.', '', $data['jaringan_kode_barang']);
                } elseif (!empty($firstItem['jaringan_kode_barang'])) {
                    $kode108Clean = str_replace('.', '', $firstItem['jaringan_kode_barang']);
                }

                $maxRegInt = \App\Models\AstapRegister::where('tahun_perolehan', $tahun)
                    ->where(function($q) use ($jenisAstapId) {
                        if ($jenisAstapId) {
                            $q->whereHas('astap', fn($sq) => $sq->where('jenis_astap_id', $jenisAstapId));
                        }
                    })
                    ->max('no_register_int') ?? 0;
                $runningRegNum = (int) $maxRegInt;

                $satuan = $firstItem['jaringan_satuan'] ?? 'Paket';
                $specJson = [
                    'jumlah_anggaran' => $data['jumlah_anggaran'] ?? null,
                    'panjang_m' => (float)($firstItem['jaringan_panjang_m'] ?? 0),
                    'lebar_m' => (float)($firstItem['jaringan_lebar_m'] ?? 0),
                    'luas_m2' => $totalLuas,
                    'bertingkat' => $firstItem['jaringan_bertingkat'] ?? 'Bertingkat',
                    'beton' => $firstItem['jaringan_beton'] ?? 'Beton',
                    'status_tanah' => $firstItem['jaringan_status_tanah'] ?? 'Tanah Hak Pakai RSUD',
                    'kode_aset_tanah' => $firstItem['jaringan_kode_aset_tanah'] ?? '1.3.1.01.01.02.013',
                    'is_baru' => $firstItem['jaringan_is_baru'] ?? 'Baru',
                    'kapitalisasi_tahun_induk' => $firstItem['jaringan_kapitalisasi_tahun_induk'] ?? null,
                    'kapitalisasi_nilai_induk' => $firstItem['jaringan_kapitalisasi_nilai_induk'] ?? 0,
                    'jaringan_jumlah' => $totalJaringan,
                    'nilai_perencanaan' => $totalPerencanaan,
                    'nilai_fisik' => $totalFisik,
                    'nilai_pengawasan' => $totalPengawasan,
                    'nilai_ap' => $totalAp,
                    'nilai_pip' => $totalAp,
                    'jaringan_items' => $data['jaringan_items']
                ];
                $specJson = array_filter($specJson, fn($v) => !is_null($v) && $v !== '');

                $astap = \Illuminate\Support\Facades\DB::transaction(function() use (
                    $data, $jenisPengadaanId, $rekeningBelanjaId, $jenisAstapId, $namaBarang,
                    $tahun, $totalJaringan, $satuan, $hargaSatuanRata, $totalRealisasi,
                    $specJson, $allAlamat, $kode108Clean, &$runningRegNum
                ) {
                    $alamatStr = count($allAlamat) > 0 ? implode('; ', array_unique($allAlamat)) : ($data['alamat_barang'] ?? null);

                    $astapItem = \App\Models\Astap::create([
                        'jenis_pengadaan_id' => $jenisPengadaanId,
                        'rekening_belanja_id' => $rekeningBelanjaId,
                        'jenis_astap_id' => $jenisAstapId,
                        'nama_barang' => $namaBarang,
                        'tahun_perolehan' => $tahun,
                        'triwulan' => $data['triwulan'] ?? 'TW I',
                        'jumlah_volume' => $totalJaringan,
                        'satuan' => $satuan,
                        'harga_satuan' => $hargaSatuanRata,
                        'jumlah_anggaran' => !empty($data['jumlah_anggaran']) ? (float) $data['jumlah_anggaran'] : $totalRealisasi,
                        'total_realisasi' => $totalRealisasi,
                        'biaya_administrasi_proyek' => 0,
                        'is_extracomtable' => false,
                        'spk_nomor' => $data['spk_nomor'] ?? null,
                        'spk_tanggal' => $data['spk_tanggal'] ?? null,
                        'surat_pesanan_nomor' => $data['surat_pesanan_nomor'] ?? null,
                        'surat_pesanan_tanggal' => $data['surat_pesanan_tanggal'] ?? null,
                        'kwitansi_nomor' => $data['kwitansi_nomor'] ?? null,
                        'kwitansi_tanggal' => $data['kwitansi_tanggal'] ?? null,
                        'faktur_nomor' => $data['faktur_nomor'] ?? null,
                        'faktur_tanggal' => $data['faktur_tanggal'] ?? null,
                        'sp2d_nomor' => $data['sp2d_nomor'] ?? null,
                        'sp2d_tanggal' => $data['sp2d_tanggal'] ?? null,
                        'bast_dokumen_nomor' => $data['bast_dokumen_nomor'] ?? null,
                        'bast_dokumen_tanggal' => $data['bast_dokumen_tanggal'] ?? null,
                        'alamat_barang' => $alamatStr,
                        'penyedia_nama' => $data['penyedia_nama'] ?? null,
                        'penyedia_pemilik' => $data['penyedia_pemilik'] ?? null,
                        'penyedia_rekening_nama' => $data['penyedia_rekening_nama'] ?? null,
                        'penyedia_rekening_nomor' => $data['penyedia_rekening_nomor'] ?? null,
                        'penyedia_alamat' => $data['penyedia_alamat'] ?? null,
                        'ppk_nama' => $data['ppk_nama'] ?? null,
                        'ppk_nip' => $data['ppk_nip'] ?? null,
                        'keterangan_tambahan' => $data['keterangan_tambahan'] ?? ($data['keterangan'] ?? null),
                        'spesifikasi_json' => $specJson,
                        'user_id' => auth()->id()
                    ]);

                    // Buat AstapRegister untuk setiap unit dalam jaringan_items
                    foreach ($data['jaringan_items'] as $itemIdx => $jItem) {
                        $itemQty = max(1, (int)($jItem['jaringan_jumlah'] ?? ($jItem['jaringan_jumlah_jaringan'] ?? 1)));
                        $rawKondisi = strtoupper(trim((string)($jItem['jaringan_kondisi'] ?? 'Baik')));
                        $kondisiStr = ($rawKondisi === 'KB' || $rawKondisi === 'KURANG BAIK') ? 'Kurang Baik' : (($rawKondisi === 'RB' || $rawKondisi === 'RUSAK BERAT' || $rawKondisi === 'RUSAK') ? 'Rusak Berat' : 'Baik');

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
                                'astap_id' => $astapItem->id,
                                'tahun_perolehan' => $tahun,
                                'no_register_int' => $runningRegNum,
                                'no_register' => $nibar,
                                'nibar' => $nibar,
                                'qr_code_path' => $qrPath,
                                'ruang_pemegang' => null,
                                'kondisi' => $kondisiStr,
                                'status' => 'Tersedia'
                            ]);
                        }
                    }

                    return $astapItem;
                });

                // Kirim Notifikasi Sistem ke Admin & Super Admin
                try {
                    \App\Services\NotificationService::sendToAdminAndMaster(
                        "Aset Jalan, Irigasi & Jaringan Baru ({$totalJaringan} {$satuan}): {$namaBarang}",
                        "{$totalJaringan} {$satuan} • " . $tahun,
                        'astap',
                        route('astap.index')
                    );
                } catch (\Throwable $e) {
                    \Log::warning("Gagal kirim notif astap store jaringan: " . $e->getMessage());
                }

                session()->flash('success', 'Sebanyak ' . $totalJaringan . ' ' . $satuan . ' Jalan, Irigasi & Jaringan "' . $namaBarang . '" berhasil disimpan ke database SIMAT-RK.');
                return response()->json([
                    'success' => true,
                    'message' => 'Sebanyak ' . $totalJaringan . ' ' . $satuan . ' Jalan, Irigasi & Jaringan berhasil didaftarkan ke database SIMAT-RK!'
                ]);
            } elseif ($isKdp && $hasKdpItems) {
                $totalBangunan = 0;
                $totalLuas = 0;
                $totalPerencanaan = 0;
                $totalFisik = 0;
                $totalPengawasan = 0;
                $totalPip = 0;
                $allAlamat = [];

                foreach ($data['kdp_items'] as $kItem) {
                    $bCount = max(1, (int)($kItem['kdp_jumlah_bangunan'] ?? 1));
                    $totalBangunan += $bCount;
                    $totalLuas += (float)($kItem['kdp_luas_m2'] ?? 0);
                    $totalPerencanaan += (float)($kItem['kdp_nilai_perencanaan'] ?? 0);
                    $totalFisik += (float)($kItem['kdp_nilai_fisik'] ?? 0);
                    $totalPengawasan += (float)($kItem['kdp_nilai_pengawasan'] ?? 0);
                    $totalPip += (float)($kItem['kdp_nilai_ap'] ?? ($kItem['kdp_nilai_pip'] ?? 0));
                    if (!empty($kItem['kdp_alamat'])) $allAlamat[] = $kItem['kdp_alamat'];
                }

                $totalRealisasi = $totalPerencanaan + $totalFisik + $totalPengawasan + $totalPip;
                $hargaSatuanRata = $totalBangunan > 0 ? ($totalRealisasi / $totalBangunan) : $totalRealisasi;
                $tahun = $data['tahun_perolehan'] ?? ($data['tahun_anggaran'] ?? date('Y'));
                $firstItem = $data['kdp_items'][0] ?? [];

                if (!empty($firstItem['kdp_nama_barang'])) {
                    $namaBarang = $firstItem['kdp_nama_barang'];
                }

                $kode108Clean = '136000000000';
                if ($jenisAstapRecord && !empty($jenisAstapRecord->sub_sub_rincian_objek)) {
                    $kode108Clean = str_replace('.', '', $jenisAstapRecord->sub_sub_rincian_objek);
                } elseif (!empty($data['kdp_kode_barang'])) {
                    $kode108Clean = str_replace('.', '', $data['kdp_kode_barang']);
                } elseif (!empty($firstItem['kdp_kode_barang'])) {
                    $kode108Clean = str_replace('.', '', $firstItem['kdp_kode_barang']);
                }

                $maxRegInt = \App\Models\AstapRegister::where('tahun_perolehan', $tahun)
                    ->where(function($q) use ($jenisAstapId) {
                        if ($jenisAstapId) {
                            $q->whereHas('astap', fn($sq) => $sq->where('jenis_astap_id', $jenisAstapId));
                        }
                    })
                    ->max('no_register_int') ?? 0;
                $runningRegNum = (int) $maxRegInt;

                $satuan = $firstItem['kdp_satuan'] ?? 'Gedung';
                $specJson = [
                    'jumlah_anggaran' => $data['jumlah_anggaran'] ?? null,
                    'luas_m2' => $totalLuas,
                    'progres_persen' => $firstItem['kdp_progres_persen'] ?? 0,
                    'bertingkat' => $firstItem['kdp_bertingkat'] ?? 'Bertingkat',
                    'beton' => $firstItem['kdp_beton'] ?? 'Beton',
                    'status_tanah' => $firstItem['kdp_status_tanah'] ?? 'Tanah Hak Pakai RSUD',
                    'kode_aset_tanah' => $firstItem['kdp_kode_aset_tanah'] ?? '1.3.1.01.01.02.013',
                    'is_baru' => $firstItem['kdp_is_baru'] ?? 'Baru',
                    'kapitalisasi_tahun_induk' => $firstItem['kdp_kapitalisasi_tahun_induk'] ?? null,
                    'kapitalisasi_nilai_induk' => $firstItem['kdp_kapitalisasi_nilai_induk'] ?? 0,
                    'kdp_jumlah_bangunan' => $totalBangunan,
                    'nilai_perencanaan' => $totalPerencanaan,
                    'nilai_fisik' => $totalFisik,
                    'nilai_pengawasan' => $totalPengawasan,
                    'nilai_pip' => $totalPip,
                    'kdp_items' => $data['kdp_items']
                ];
                $specJson = array_filter($specJson, fn($v) => !is_null($v) && $v !== '');

                $astap = \Illuminate\Support\Facades\DB::transaction(function() use (
                    $data, $jenisPengadaanId, $rekeningBelanjaId, $jenisAstapId, $namaBarang,
                    $tahun, $totalBangunan, $satuan, $hargaSatuanRata, $totalRealisasi,
                    $specJson, $allAlamat, $kode108Clean, &$runningRegNum
                ) {
                    $alamatStr = count($allAlamat) > 0 ? implode('; ', array_unique($allAlamat)) : ($data['alamat_barang'] ?? null);

                    $astapItem = \App\Models\Astap::create([
                        'jenis_pengadaan_id' => $jenisPengadaanId,
                        'rekening_belanja_id' => $rekeningBelanjaId,
                        'jenis_astap_id' => $jenisAstapId,
                        'nama_barang' => $namaBarang,
                        'tahun_perolehan' => $tahun,
                        'triwulan' => $data['triwulan'] ?? 'TW I',
                        'jumlah_volume' => $totalBangunan,
                        'satuan' => $satuan,
                        'harga_satuan' => $hargaSatuanRata,
                        'jumlah_anggaran' => !empty($data['jumlah_anggaran']) ? (float) $data['jumlah_anggaran'] : $totalRealisasi,
                        'total_realisasi' => $totalRealisasi,
                        'biaya_administrasi_proyek' => 0,
                        'is_extracomtable' => false,
                        'spk_nomor' => $data['spk_nomor'] ?? null,
                        'spk_tanggal' => $data['spk_tanggal'] ?? null,
                        'surat_pesanan_nomor' => $data['surat_pesanan_nomor'] ?? null,
                        'surat_pesanan_tanggal' => $data['surat_pesanan_tanggal'] ?? null,
                        'kwitansi_nomor' => $data['kwitansi_nomor'] ?? null,
                        'kwitansi_tanggal' => $data['kwitansi_tanggal'] ?? null,
                        'faktur_nomor' => $data['faktur_nomor'] ?? null,
                        'faktur_tanggal' => $data['faktur_tanggal'] ?? null,
                        'sp2d_nomor' => $data['sp2d_nomor'] ?? null,
                        'sp2d_tanggal' => $data['sp2d_tanggal'] ?? null,
                        'bast_dokumen_nomor' => $data['bast_dokumen_nomor'] ?? null,
                        'bast_dokumen_tanggal' => $data['bast_dokumen_tanggal'] ?? null,
                        'alamat_barang' => $alamatStr,
                        'penyedia_nama' => $data['penyedia_nama'] ?? null,
                        'penyedia_pemilik' => $data['penyedia_pemilik'] ?? null,
                        'penyedia_rekening_nama' => $data['penyedia_rekening_nama'] ?? null,
                        'penyedia_rekening_nomor' => $data['penyedia_rekening_nomor'] ?? null,
                        'penyedia_alamat' => $data['penyedia_alamat'] ?? null,
                        'ppk_nama' => $data['ppk_nama'] ?? null,
                        'ppk_nip' => $data['ppk_nip'] ?? null,
                        'keterangan_tambahan' => $data['keterangan_tambahan'] ?? ($data['keterangan'] ?? null),
                        'spesifikasi_json' => $specJson,
                        'user_id' => auth()->id()
                    ]);

                    // Buat AstapRegister untuk setiap unit dalam kdp_items
                    foreach ($data['kdp_items'] as $itemIdx => $kItem) {
                        $itemQty = max(1, (int)($kItem['kdp_jumlah_bangunan'] ?? 1));
                        $rawKondisi = strtoupper(trim((string)($kItem['kdp_kondisi'] ?? 'Baik')));
                        $kondisiStr = ($rawKondisi === 'KB' || $rawKondisi === 'KURANG BAIK') ? 'Kurang Baik' : (($rawKondisi === 'RB' || $rawKondisi === 'RUSAK BERAT' || $rawKondisi === 'RUSAK') ? 'Rusak Berat' : 'Baik');

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
                                'astap_id' => $astapItem->id,
                                'tahun_perolehan' => $tahun,
                                'no_register_int' => $runningRegNum,
                                'no_register' => $nibar,
                                'nibar' => $nibar,
                                'qr_code_path' => $qrPath,
                                'ruang_pemegang' => null,
                                'kondisi' => $kondisiStr,
                                'status' => 'Tersedia'
                            ]);
                        }
                    }

                    return $astapItem;
                });

                // Kirim Notifikasi Sistem ke Admin & Super Admin
                try {
                    \App\Services\NotificationService::sendToAdminAndMaster(
                        "Aset KDP Baru ({$totalBangunan} {$satuan}): {$namaBarang}",
                        "{$totalBangunan} {$satuan} • " . $tahun,
                        'astap',
                        route('astap.index')
                    );
                } catch (\Throwable $e) {
                    \Log::warning("Gagal kirim notif astap store kdp: " . $e->getMessage());
                }

                session()->flash('success', 'Sebanyak ' . $totalBangunan . ' ' . $satuan . ' Konstruksi Dalam Pengerjaan "' . $namaBarang . '" berhasil disimpan ke database SIMAT-RK.');
                return response()->json([
                    'success' => true,
                    'message' => 'Sebanyak ' . $totalBangunan . ' ' . $satuan . ' Konstruksi Dalam Pengerjaan berhasil didaftarkan ke database SIMAT-RK!'
                ]);
            } elseif ($isAtb && $hasAtbItems) {
                // =========================================================================
                // KHUSUS ASET TIDAK BERWUJUD (ATB - 1.5.3) MULTI-ITEM REPEATER
                // =========================================================================
                $totalVolume = 0;
                $totalRealisasi = 0;
                $totalBiayaAdm = 0;
                $allJudul = [];
                $allPencipta = [];
                $allSpesifikasi = [];

                foreach ($data['atb_items'] as $aItem) {
                    $qty = max(1, (int)($aItem['atb_jumlah'] ?? 1));
                    $nilaiSatuan = (float)($aItem['atb_nilai_satuan'] ?? 0);
                    $biayaAdmItem = (float)($aItem['atb_administrasi_proyek'] ?? 0);

                    $totalVolume += $qty;
                    $totalRealisasi += ($qty * $nilaiSatuan) + $biayaAdmItem;
                    $totalBiayaAdm += $biayaAdmItem;

                    if (!empty($aItem['atb_judul_nama'])) $allJudul[] = $aItem['atb_judul_nama'];
                    if (!empty($aItem['atb_pencipta'])) $allPencipta[] = $aItem['atb_pencipta'];
                    if (!empty($aItem['atb_spesifikasi'])) $allSpesifikasi[] = $aItem['atb_spesifikasi'];
                }

                $firstItem = $data['atb_items'][0] ?? [];
                $satuan = $firstItem['atb_satuan'] ?? 'Lisensi';
                $hargaSatuanRata = $totalVolume > 0 ? ($totalRealisasi / $totalVolume) : 0;
                $tahun = $data['tahun_perolehan'] ?? ($data['tahun_anggaran'] ?? date('Y'));
                $isExtracom = !empty($data['is_extracomtable']);

                if (!empty($firstItem['atb_nama_barang'])) {
                    $namaBarang = $firstItem['atb_nama_barang'];
                }

                $kode108Submitted = $data['atb_kode_barang'] ?? ($firstItem['atb_kode_barang'] ?? ($data['sub_rincian_kode'] ?? ($data['jenis_aset_kode'] ?? null)));
                $kode108Clean = '153000000000';
                if ($jenisAstapRecord && !empty($jenisAstapRecord->sub_sub_rincian_objek)) {
                    $kode108Clean = str_replace('.', '', $jenisAstapRecord->sub_sub_rincian_objek);
                } elseif (!empty($kode108Submitted)) {
                    $kode108Clean = str_replace('.', '', $kode108Submitted);
                }

                $maxRegInt = \App\Models\AstapRegister::where('tahun_perolehan', $tahun)
                    ->where(function($q) use ($jenisAstapId) {
                        if ($jenisAstapId) {
                            $q->whereHas('astap', fn($sq) => $sq->where('jenis_astap_id', $jenisAstapId));
                        }
                    })
                    ->max('no_register_int') ?? 0;
                $runningRegNum = (int) $maxRegInt;

                $ruangFirst = $firstItem['atb_ruang_pemegang'] ?? ($data['ruang_pemegang_atb'] ?? ($data['ruang_pemegang'] ?? null));

                $cleanedAtb = array_map(function($item) {
                    return array_filter($item, fn($v) => !is_null($v) && $v !== '' && $v !== false);
                }, $data['atb_items']);

                $specJson = [
                    'jumlah_anggaran' => $data['jumlah_anggaran'] ?? null,
                    'atb_judul' => count($allJudul) > 0 ? implode(', ', array_unique($allJudul)) : ($firstItem['atb_judul_nama'] ?? null),
                    'atb_pencipta' => count($allPencipta) > 0 ? implode(', ', array_unique($allPencipta)) : ($firstItem['atb_pencipta'] ?? null),
                    'atb_spesifikasi' => count($allSpesifikasi) > 0 ? implode('; ', array_unique($allSpesifikasi)) : ($firstItem['atb_spesifikasi'] ?? null),
                    'atb_nama_barang' => $namaBarang,
                    'atb_kode_barang' => $kode108Submitted,
                    'ruang_pemegang' => $ruangFirst,
                    'atb_items' => array_values($cleanedAtb)
                ];
                $specJson = array_filter($specJson, fn($v) => !is_null($v) && $v !== '');

                $astap = \Illuminate\Support\Facades\DB::transaction(function() use (
                    $data, $jenisPengadaanId, $rekeningBelanjaId, $jenisAstapId, $namaBarang,
                    $tahun, $totalVolume, $satuan, $hargaSatuanRata, $totalRealisasi, $totalBiayaAdm,
                    $isExtracom, $specJson, $kode108Clean, &$runningRegNum
                ) {
                    $astapItem = \App\Models\Astap::create([
                        'jenis_pengadaan_id' => $jenisPengadaanId,
                        'rekening_belanja_id' => $rekeningBelanjaId,
                        'jenis_astap_id' => $jenisAstapId,
                        'nama_barang' => $namaBarang,
                        'tahun_perolehan' => $tahun,
                        'triwulan' => $data['triwulan'] ?? 'TW I',
                        'jumlah_volume' => $totalVolume,
                        'satuan' => $satuan,
                        'harga_satuan' => $hargaSatuanRata,
                        'jumlah_anggaran' => !empty($data['jumlah_anggaran']) ? (float) $data['jumlah_anggaran'] : $totalRealisasi,
                        'total_realisasi' => $totalRealisasi,
                        'biaya_administrasi_proyek' => $totalBiayaAdm,
                        'is_extracomtable' => $isExtracom,
                        'spk_nomor' => $data['spk_nomor'] ?? null,
                        'spk_tanggal' => $data['spk_tanggal'] ?? null,
                        'surat_pesanan_nomor' => $data['surat_pesanan_nomor'] ?? null,
                        'surat_pesanan_tanggal' => $data['surat_pesanan_tanggal'] ?? null,
                        'kwitansi_nomor' => $data['kwitansi_nomor'] ?? null,
                        'kwitansi_tanggal' => $data['kwitansi_tanggal'] ?? null,
                        'faktur_nomor' => $data['faktur_nomor'] ?? null,
                        'faktur_tanggal' => $data['faktur_tanggal'] ?? null,
                        'sp2d_nomor' => $data['sp2d_nomor'] ?? null,
                        'sp2d_tanggal' => $data['sp2d_tanggal'] ?? null,
                        'bast_dokumen_nomor' => $data['bast_dokumen_nomor'] ?? null,
                        'bast_dokumen_tanggal' => $data['bast_dokumen_tanggal'] ?? null,
                        'alamat_barang' => $data['alamat_barang'] ?? null,
                        'penyedia_nama' => $data['penyedia_nama'] ?? null,
                        'penyedia_pemilik' => $data['penyedia_pemilik'] ?? null,
                        'penyedia_rekening_nama' => $data['penyedia_rekening_nama'] ?? null,
                        'penyedia_rekening_nomor' => $data['penyedia_rekening_nomor'] ?? null,
                        'penyedia_alamat' => $data['penyedia_alamat'] ?? null,
                        'ppk_nama' => $data['ppk_nama'] ?? null,
                        'ppk_nip' => $data['ppk_nip'] ?? null,
                        'keterangan_tambahan' => $data['keterangan_tambahan'] ?? ($data['keterangan'] ?? null),
                        'spesifikasi_json' => $specJson,
                        'user_id' => auth()->id()
                    ]);

                    // Buat AstapRegister untuk setiap unit dalam atb_items
                    foreach ($data['atb_items'] as $itemIdx => $aItem) {
                        $itemQty = max(1, (int)($aItem['atb_jumlah'] ?? 1));
                        $rawKondisi = strtoupper(trim((string)($aItem['atb_kondisi'] ?? 'Baik')));
                        $kondisiStr = ($rawKondisi === 'KB' || $rawKondisi === 'KURANG BAIK') ? 'Kurang Baik' : (($rawKondisi === 'RB' || $rawKondisi === 'RUSAK BERAT' || $rawKondisi === 'RUSAK') ? 'Rusak Berat' : (($rawKondisi === 'RR' || $rawKondisi === 'RUSAK RINGAN') ? 'Rusak Ringan' : 'Baik'));
                        $ruang = $aItem['atb_ruang_pemegang'] ?? ($data['ruang_pemegang_atb'] ?? ($data['ruang_pemegang'] ?? null));

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
                                'astap_id' => $astapItem->id,
                                'tahun_perolehan' => $tahun,
                                'no_register_int' => $runningRegNum,
                                'no_register' => $nibar,
                                'nibar' => $nibar,
                                'qr_code_path' => $qrPath,
                                'ruang_pemegang' => $ruang,
                                'kondisi' => $kondisiStr,
                                'status' => 'Tersedia'
                            ]);
                        }
                    }

                    return $astapItem;
                });

                // Kirim Notifikasi Sistem ke Admin & Super Admin
                try {
                    \App\Services\NotificationService::sendToAdminAndMaster(
                        "Aset Tidak Berwujud Baru ({$totalVolume} {$satuan}): {$namaBarang}",
                        "{$totalVolume} {$satuan} • " . $tahun,
                        'astap',
                        route('astap.index')
                    );
                } catch (\Throwable $e) {
                    \Log::warning("Gagal kirim notif astap store atb: " . $e->getMessage());
                }

                session()->flash('success', 'Sebanyak ' . $totalVolume . ' ' . $satuan . ' Aset Tidak Berwujud "' . $namaBarang . '" berhasil disimpan ke database SIMAT-RK.');
                return response()->json([
                    'success' => true,
                    'message' => 'Sebanyak ' . $totalVolume . ' ' . $satuan . ' Aset Tidak Berwujud berhasil didaftarkan ke database SIMAT-RK!'
                ]);
            }

            // =========================================================================
            // DEFAULT / JENIS ASET LAINNYA
            // =========================================================================
            $extracted = $extractAstapPayload($data, $jenisPrefix, $jenisAstapRecord);

            $astap = \App\Models\Astap::create([
                'jenis_pengadaan_id' => $jenisPengadaanId,
                'rekening_belanja_id' => $rekeningBelanjaId,
                'jenis_astap_id' => $jenisAstapId,
                'nama_barang' => $namaBarang,
                'tahun_perolehan' => $data['tahun_perolehan'] ?? ($data['tahun_anggaran'] ?? date('Y')),
                'triwulan' => $data['triwulan'] ?? 'TW I',
                'jumlah_volume' => $extracted['volume'],
                'satuan' => $extracted['satuan'],
                'harga_satuan' => $extracted['harga_satuan'],
                'jumlah_anggaran' => !empty($data['jumlah_anggaran']) ? (float) $data['jumlah_anggaran'] : $extracted['total_realisasi'],
                'total_realisasi' => $extracted['total_realisasi'],
                'biaya_administrasi_proyek' => $extracted['biaya_administrasi_proyek'],
                'is_extracomtable' => $extracted['is_extracomtable'],
                'spk_nomor' => $data['spk_nomor'] ?? null,
                'spk_tanggal' => $data['spk_tanggal'] ?? null,
                'surat_pesanan_nomor' => $data['surat_pesanan_nomor'] ?? null,
                'surat_pesanan_tanggal' => $data['surat_pesanan_tanggal'] ?? null,
                'kwitansi_nomor' => $data['kwitansi_nomor'] ?? null,
                'kwitansi_tanggal' => $data['kwitansi_tanggal'] ?? null,
                'faktur_nomor' => $data['faktur_nomor'] ?? null,
                'faktur_tanggal' => $data['faktur_tanggal'] ?? null,
                'sp2d_nomor' => $data['sp2d_nomor'] ?? null,
                'sp2d_tanggal' => $data['sp2d_tanggal'] ?? null,
                'bast_dokumen_nomor' => $data['bast_dokumen_nomor'] ?? null,
                'bast_dokumen_tanggal' => $data['bast_dokumen_tanggal'] ?? null,
                'alamat_barang' => $data['alamat_barang'] ?? null,
                'penyedia_nama' => $data['penyedia_nama'] ?? null,
                'penyedia_pemilik' => $data['penyedia_pemilik'] ?? null,
                'penyedia_rekening_nama' => $data['penyedia_rekening_nama'] ?? null,
                'penyedia_rekening_nomor' => $data['penyedia_rekening_nomor'] ?? null,
                'penyedia_alamat' => $data['penyedia_alamat'] ?? null,
                'ppk_nama' => $data['ppk_nama'] ?? null,
                'ppk_nip' => $data['ppk_nip'] ?? null,
                'keterangan_tambahan' => $data['keterangan_tambahan'] ?? ($data['keterangan'] ?? null),
                'spesifikasi_json' => $extracted['spesifikasi_json'],
                'user_id' => auth()->id()
            ]);

            $vol = $extracted['volume'];
            $kode108Clean = str_replace('.', '', $astap->kode_108 ?: '132000000000');
            $tahun = $astap->tahun_perolehan;

            // Cari nomor register terakhir untuk kode 108 + tahun yang sama di SELURUH ASTAP
            $maxRegInt = \App\Models\AstapRegister::whereHas('astap', function($q) use ($astap) {
                $q->where('jenis_astap_id', $astap->jenis_astap_id)
                  ->where('tahun_perolehan', $astap->tahun_perolehan);
            })->max('no_register_int') ?? 0;

            $startFrom = $maxRegInt + 1;
            $ruangSingle = $data['ruang_pemegang'] ?? ($data['ruang_pemegang_mesin'] ?? ($data['ruang_pemegang_lainnya'] ?? ($data['ruang_pemegang_atb'] ?? null)));
            $rawKondisi = strtoupper(trim((string)($data['kondisi'] ?? ($data['mesin_kondisi'] ?? ($data['gedung_kondisi'] ?? ($data['tanah_kondisi'] ?? ($data['jaringan_kondisi'] ?? ($data['lainnya_kondisi'] ?? ($data['atb_kondisi'] ?? ($data['kdp_kondisi'] ?? 'Baik'))))))))));
            $kondisiStr = ($rawKondisi === 'KB' || $rawKondisi === 'KURANG BAIK') ? 'Kurang Baik' : (($rawKondisi === 'RB' || $rawKondisi === 'RUSAK BERAT' || $rawKondisi === 'RUSAK') ? 'Rusak Berat' : (($rawKondisi === 'RR' || $rawKondisi === 'RUSAK RINGAN') ? 'Rusak Ringan' : 'Baik'));

            for ($i = 0; $i < $vol; $i++) {
                $regNum = $startFrom + $i;
                $noRegStr = str_pad($regNum, 7, '0', STR_PAD_LEFT);
                $nibar = "1201351102000000280000{$tahun}{$kode108Clean}{$noRegStr}";
                $qrPath = "/scan/{$nibar}";
                \App\Models\AstapRegister::create([
                    'astap_id' => $astap->id,
                    'tahun_perolehan' => $tahun,
                    'no_register_int' => $regNum,
                    'no_register' => $nibar,
                    'nibar' => $nibar,
                    'qr_code_path' => $qrPath,
                    'ruang_pemegang' => $ruangSingle,
                    'kondisi' => $kondisiStr,
                    'status' => 'Tersedia'
                ]);
            }

            // Kirim Notifikasi Sistem ke Admin & Super Admin (Format Singkat & Rapi)
            try {
                \App\Services\NotificationService::sendToAdminAndMaster(
                    "Aset Baru: {$astap->nama_barang}",
                    "{$vol} {$extracted['satuan']} • " . ($astap->tahun_perolehan ?: date('Y')),
                    'astap',
                    route('astap.index')
                );
            } catch (\Throwable $e) {
                \Log::warning("Gagal kirim notif astap store: " . $e->getMessage());
            }

            session()->flash('success', 'Data ASTAP "' . ($astap->nama_barang ?? 'Aset Tetap') . '" berhasil ditambahkan.');
            return response()->json(['success' => true, 'message' => 'Data ASTAP berhasil disimpan ke database SIMAT-RK!']);
        })->name('astap.store');

        // API: Cek atau ambil pagu anggaran yang sudah ada berdasarkan Sub Rincian Objek + Tahun + Triwulan
        Route::post('/astap/check-subrincian-anggaran', function (\Illuminate\Http\Request $request) {
            $subRincianKode = $request->input('sub_rincian_kode');
            $tahun = (int) $request->input('tahun', date('Y'));
            $triwulan = $request->input('triwulan', 'TW I');

            if (empty($subRincianKode) || empty($tahun)) {
                return response()->json(['found' => false]);
            }

            // Normalisasi variasi triwulan (TW I / TW1 / dsb)
            $twVariants = match($triwulan) {
                'TW I', 'TW1' => ['TW I', 'TW1', 'TW 1', 'Triwulan I'],
                'TW II', 'TW2' => ['TW II', 'TW2', 'TW 2', 'Triwulan II'],
                'TW III', 'TW3' => ['TW III', 'TW3', 'TW 3', 'Triwulan III'],
                'TW IV', 'TW4' => ['TW IV', 'TW4', 'TW 4', 'Triwulan IV'],
                default => [$triwulan]
            };

            $existing = \App\Models\Astap::where(function($q) use ($subRincianKode) {
                    $q->whereHas('jenisAstap', function($jq) use ($subRincianKode) {
                        $jq->where('sub_rincian_objek', $subRincianKode)
                           ->orWhere('sub_rincian_objek', 'LIKE', $subRincianKode . '%');
                    })->orWhere('kode_108', 'LIKE', $subRincianKode . '%');
                })
                ->where('tahun_perolehan', $tahun)
                ->where(function($tq) use ($twVariants) {
                    $tq->whereIn('triwulan', $twVariants);
                    foreach ($twVariants as $tv) {
                        $tq->orWhereJsonContains('spesifikasi_json->triwulan', $tv);
                    }
                })
                ->whereNotNull('jumlah_anggaran')
                ->where('jumlah_anggaran', '>', 0)
                ->orderBy('id', 'desc')
                ->first();

            if ($existing) {
                $excludeId = $request->input('exclude_id');
                $sumQuery = \App\Models\Astap::where('tahun_perolehan', $tahun)
                    ->where(function($tq) use ($twVariants) {
                        $tq->whereIn('triwulan', $twVariants);
                        foreach ($twVariants as $tv) {
                            $tq->orWhereJsonContains('spesifikasi_json->triwulan', $tv);
                        }
                    })
                    ->where(function($q) use ($subRincianKode) {
                        $q->whereHas('jenisAstap', fn($jq) => $jq->where('sub_rincian_objek', $subRincianKode))
                          ->orWhere('kode_108', 'LIKE', $subRincianKode . '%');
                    });

                if (!empty($excludeId)) {
                    $sumQuery->where('id', '!=', $excludeId);
                }

                $sumRealisasi = $sumQuery->sum('total_realisasi');

                return response()->json([
                    'found' => true,
                    'jumlah_anggaran' => (float) $existing->jumlah_anggaran,
                    'total_realisasi_existing' => (float) $sumRealisasi,
                    'message' => "Pagu anggaran ditemukan untuk {$triwulan} {$tahun}"
                ]);
            }

            return response()->json(['found' => false]);
        })->name('astap.checkSubRincianAnggaran');

        // API: Cek duplikat kode 108 + tahun untuk konfirmasi sebelum submit
        Route::post('/astap/check-duplicate', function (\Illuminate\Http\Request $request) {
            $kode108 = $request->input('kode_108');
            $tahun = $request->input('tahun');

            if (empty($kode108) || empty($tahun)) {
                return response()->json(['exists' => false]);
            }

            $jenisAstap = \App\Models\JenisAstap::where('sub_sub_rincian_objek', $kode108)->first();
            if (!$jenisAstap) {
                return response()->json(['exists' => false]);
            }

            $existingAstaps = \App\Models\Astap::where('jenis_astap_id', $jenisAstap->id)
                ->where('tahun_perolehan', $tahun)
                ->get();

            if ($existingAstaps->isEmpty()) {
                return response()->json(['exists' => false]);
            }

            $totalUnit = \App\Models\AstapRegister::whereIn('astap_id', $existingAstaps->pluck('id'))
                ->count();

            $maxReg = \App\Models\AstapRegister::whereIn('astap_id', $existingAstaps->pluck('id'))
                ->max('no_register_int') ?? 0;

            return response()->json([
                'exists' => true,
                'nama_barang' => $existingAstaps->first()->nama_barang,
                'total_unit' => $totalUnit,
                'nibar_terakhir' => $maxReg,
                'nibar_selanjutnya' => $maxReg + 1,
                'jumlah_astap' => $existingAstaps->count()
            ]);
        })->name('astap.checkDuplicate');

        Route::put('/astap/{id}', function (\Illuminate\Http\Request $request, $id) {
            $astap = \App\Models\Astap::find($id);
            if (!$astap) {
                return response()->json(['success' => false, 'message' => 'Data ASTAP tidak ditemukan.'], 404);
            }

            $data = $request->all();

            // Dapatkan Kode 108 Sub-Sub Rincian berdasarkan jenis aset yang dipilih
            $jenisPrefix = substr($data['jenis_aset_kode'] ?? ($data['sub_rincian_kode'] ?? ''), 0, 5);
            $kode108Submitted = match(true) {
                $jenisPrefix === '1.3.1' => $data['tanah_kode_barang'] ?? ($data['tanah_items'][0]['tanah_kode_barang'] ?? null),
                $jenisPrefix === '1.3.2' => $data['mesin_kode_barang'] ?? ($data['mesin_items'][0]['mesin_kode_barang'] ?? null),
                $jenisPrefix === '1.3.3' => $data['gedung_kode_barang'] ?? ($data['gedung_items'][0]['gedung_kode_barang'] ?? null),
                $jenisPrefix === '1.3.4' => $data['jaringan_kode_barang'] ?? ($data['jaringan_items'][0]['jaringan_kode_barang'] ?? null),
                $jenisPrefix === '1.3.5' => $data['lainnya_kode_barang'] ?? null,
                $jenisPrefix === '1.5.3' => $data['atb_kode_barang'] ?? null,
                $jenisPrefix === '1.3.6' => $data['kdp_kode_barang'] ?? null,
                default => null
            };

            if (empty($kode108Submitted)) {
                $kode108Submitted = $data['sub_rincian_kode'] ?? ($data['jenis_aset_kode'] ?? null);
            }

            $jenisAstapRecord = null;
            if ($kode108Submitted) {
                $jenisAstapRecord = \App\Models\JenisAstap::where('sub_sub_rincian_objek', $kode108Submitted)->first();
            }
            if (!$jenisAstapRecord && !empty($data['sub_rincian_kode'])) {
                $jenisAstapRecord = \App\Models\JenisAstap::where('sub_rincian_objek', $data['sub_rincian_kode'])->first()
                    ?? \App\Models\JenisAstap::where('jenis', substr($data['sub_rincian_kode'], 0, 5))->first();
            }
            if ($jenisAstapRecord) {
                $astap->jenis_astap_id = $jenisAstapRecord->id;
            }

            $jenisPengadaanId = $data['jenis_pengadaan_id'] ?? null;
            if (!$jenisPengadaanId && !empty($data['sub_kegiatan_kode'])) {
                $jenisPengadaanId = \App\Models\JenisPengadaan::where('sub_kegiatan_kode', 'LIKE', '%'.$data['sub_kegiatan_kode'].'%')->value('id');
            }
            if ($jenisPengadaanId) $astap->jenis_pengadaan_id = $jenisPengadaanId;

            if (!empty($data['kode_rek'])) {
                $rekeningBelanjaId = \App\Models\RekeningBelanja::where('kode_rek', $data['kode_rek'])->value('id');
                if ($rekeningBelanjaId) $astap->rekening_belanja_id = $rekeningBelanjaId;
            }

            $extractAstapPayload = function($d, $prefix) {
                $vol = (int) match(true) {
                    $prefix === '1.3.1' => $d['tanah_jumlah_bidang'] ?? ($d['jumlah_volume'] ?? 1),
                    $prefix === '1.3.2' => $d['mesin_jumlah_barang'] ?? ($d['jumlah_volume'] ?? 1),
                    $prefix === '1.3.3' => $d['gedung_jumlah_bangunan'] ?? ($d['jumlah_volume'] ?? 1),
                    $prefix === '1.3.4' => $d['jaringan_jumlah'] ?? ($d['jumlah_volume'] ?? 1),
                    $prefix === '1.3.5' => $d['lainnya_jumlah_barang'] ?? ($d['jumlah_volume'] ?? 1),
                    $prefix === '1.5.3' => $d['atb_jumlah'] ?? ($d['jumlah_volume'] ?? 1),
                    $prefix === '1.3.6' => $d['kdp_jumlah_bangunan'] ?? ($d['jumlah_volume'] ?? 1),
                    default => $d['jumlah_volume'] ?? 1
                };

                $sat = match(true) {
                    $prefix === '1.3.1' => 'Bidang',
                    $prefix === '1.3.2' => $d['mesin_satuan'] ?? ($d['satuan'] ?? 'Unit'),
                    $prefix === '1.3.3' => $d['gedung_satuan'] ?? ($d['satuan'] ?? 'Gedung'),
                    $prefix === '1.3.4' => $d['jaringan_satuan'] ?? ($d['satuan'] ?? 'Paket'),
                    $prefix === '1.3.5' => $d['lainnya_satuan'] ?? ($d['satuan'] ?? 'Eksemplar'),
                    $prefix === '1.5.3' => $d['atb_satuan'] ?? ($d['satuan'] ?? 'Lisensi'),
                    $prefix === '1.3.6' => $d['kdp_satuan'] ?? ($d['satuan'] ?? 'Gedung'),
                    default => $d['satuan'] ?? 'Unit'
                };

                $totReal = (float) ($d['jumlah_realisasi'] ?? ($d['tanah_nilai_fisik'] ?? ($d['gedung_nilai_fisik'] ?? ($d['jaringan_nilai_fisik'] ?? ($d['kdp_nilai_fisik'] ?? ($d['total_realisasi'] ?? 0))))));
                
                if ($prefix === '1.5.3' && (!empty($d['atb_items']) || !empty($d['atb_nilai_satuan']))) {
                    if (!empty($d['atb_items']) && is_array($d['atb_items'])) {
                        $atbSum = 0;
                        foreach ($d['atb_items'] as $ai) {
                            $atbSum += (max(1, (int)($ai['atb_jumlah'] ?? 1)) * (float)($ai['atb_nilai_satuan'] ?? 0)) + (float)($ai['atb_administrasi_proyek'] ?? 0);
                        }
                        if ($atbSum > 0) $totReal = $atbSum;
                    } elseif ($totReal <= 0) {
                        $totReal = ((int)($d['atb_jumlah'] ?? 1) * (float)($d['atb_nilai_satuan'] ?? 0)) + (float)($d['atb_administrasi_proyek'] ?? 0);
                    }
                }

                $hrgSat = (float) match(true) {
                    $prefix === '1.3.2' => $d['mesin_nilai_satuan'] ?? ($d['harga_satuan'] ?? 0),
                    $prefix === '1.3.5' => $d['lainnya_nilai_satuan'] ?? ($d['harga_satuan'] ?? 0),
                    $prefix === '1.5.3' => $d['atb_nilai_satuan'] ?? ($d['harga_satuan'] ?? 0),
                    default => ($totReal > 0 && $vol > 0) ? ($totReal / $vol) : ($d['harga_satuan'] ?? 0)
                };

                $biaya = (float) ($d['biaya_administrasi_proyek'] ?? ($d['mesin_administrasi_proyek'] ?? ($d['lainnya_administrasi_proyek'] ?? ($d['atb_administrasi_proyek'] ?? 0))));
                if ($prefix === '1.5.3' && !empty($d['atb_items']) && is_array($d['atb_items'])) {
                    $atbAdmSum = 0;
                    foreach ($d['atb_items'] as $ai) {
                        $atbAdmSum += (float)($ai['atb_administrasi_proyek'] ?? 0);
                    }
                    if ($atbAdmSum > 0) $biaya = $atbAdmSum;
                }

                $extracom = !empty($d['is_extracomtable']);

                $spec = [
                    'jumlah_anggaran' => $d['jumlah_anggaran'] ?? null,
                    'luas_m2' => $d['tanah_luas_m2'] ?? ($d['gedung_luas_m2'] ?? ($d['jaringan_luas_m2'] ?? ($d['kdp_luas_m2'] ?? null))),
                    'hak_tanah' => $d['tanah_hak'] ?? null,
                    'sertifikat_no' => $d['tanah_sertifikat_no'] ?? ($d['kdp_sertifikat_no'] ?? null),
                    'sertifikat_tgl' => $d['tanah_sertifikat_tgl'] ?? ($d['kdp_sertifikat_tgl'] ?? null),
                    'penggunaan' => $d['tanah_penggunaan'] ?? null,
                    'nilai_perencanaan' => $d['tanah_nilai_perencanaan'] ?? ($d['gedung_nilai_perencanaan'] ?? ($d['jaringan_nilai_perencanaan'] ?? ($d['kdp_nilai_perencanaan'] ?? 0))),
                    'nilai_pengawasan' => $d['tanah_nilai_pengawasan'] ?? ($d['gedung_nilai_pengawasan'] ?? ($d['jaringan_nilai_pengawasan'] ?? ($d['kdp_nilai_pengawasan'] ?? 0))),
                    'nilai_ap' => $d['gedung_nilai_ap'] ?? ($d['gedung_nilai_pip'] ?? ($d['jaringan_nilai_pip'] ?? ($d['kdp_nilai_pip'] ?? 0))),
                    'nilai_pip' => $d['gedung_nilai_ap'] ?? ($d['gedung_nilai_pip'] ?? ($d['jaringan_nilai_pip'] ?? ($d['kdp_nilai_pip'] ?? 0))),
                    'merk' => $d['mesin_merk'] ?? null,
                    'type' => $d['mesin_type'] ?? null,
                    'ukuran' => $d['mesin_ukuran'] ?? ($d['lainnya_kesenian_ukuran'] ?? null),
                    'no_pabrik' => $d['mesin_no_pabrik'] ?? null,
                    'bahan' => $d['mesin_bahan'] ?? ($d['lainnya_kesenian_bahan'] ?? null),
                    'bertingkat' => $d['gedung_bertingkat'] ?? ($d['kdp_bangunan'] ?? null),
                    'beton' => $d['gedung_beton'] ?? ($d['kdp_beton'] ?? null),
                    'status_tanah' => $d['gedung_status_tanah'] ?? ($d['jaringan_status_tanah'] ?? ($d['kdp_status_tanah'] ?? null)),
                    'kode_aset_tanah' => $d['gedung_kode_aset_tanah'] ?? ($d['jaringan_kode_aset_tanah'] ?? ($d['kdp_kode_aset_tanah'] ?? null)),
                    'is_baru' => $d['gedung_is_baru'] ?? ($d['jaringan_is_baru'] ?? null),
                    'kapitalisasi_tahun_induk' => $d['gedung_kapitalisasi_tahun_induk'] ?? ($d['jaringan_kapitalisasi_tahun_induk'] ?? null),
                    'kapitalisasi_nilai_induk' => $d['gedung_kapitalisasi_nilai_induk'] ?? ($d['jaringan_kapitalisasi_nilai_induk'] ?? 0),
                    'konstruksi' => $d['jaringan_konstruksi'] ?? null,
                    'panjang_m' => $d['jaringan_panjang_m'] ?? null,
                    'lebar_m' => $d['jaringan_lebar_m'] ?? null,
                    'kib_e_sub_type' => $d['kib_e_sub_type'] ?? null,
                    'buku_judul' => $d['lainnya_buku_judul'] ?? null,
                    'buku_pencipta' => $d['lainnya_buku_pencipta'] ?? null,
                    'buku_spesifikasi' => $d['lainnya_buku_spesifikasi'] ?? null,
                    'kesenian_asal' => $d['lainnya_kesenian_asal'] ?? null,
                    'kesenian_pencipta' => $d['lainnya_kesenian_pencipta'] ?? null,
                    'kesenian_spesifikasi' => $d['lainnya_kesenian_spesifikasi'] ?? null,
                    'kesenian_bahan' => $d['lainnya_kesenian_bahan'] ?? null,
                    'kesenian_ukuran' => $d['lainnya_kesenian_ukuran'] ?? null,
                    'hewan_judul' => $d['lainnya_hewan_judul'] ?? ($d['lainnya_hewan_jenis'] ?? null),
                    'hewan_jenis' => $d['lainnya_hewan_jenis'] ?? null,
                    'hewan_spesifikasi' => $d['lainnya_hewan_spesifikasi'] ?? null,
                    'atb_judul' => $d['atb_judul_nama'] ?? ($d['atb_judul'] ?? null),
                    'atb_nama_barang' => $d['atb_nama_barang'] ?? null,
                    'atb_kode_barang' => $d['atb_kode_barang'] ?? null,
                    'atb_pencipta' => $d['atb_pencipta'] ?? null,
                    'atb_jenis_lisensi' => $d['atb_jenis_lisensi'] ?? null,
                    'atb_spesifikasi' => $d['atb_spesifikasi'] ?? null,
                    'progres_persen' => $d['kdp_progres_persen'] ?? null,
                    'tgl_mulai' => $d['kdp_tgl_mulai'] ?? null,
                    'tgl_target_selesai' => $d['kdp_tgl_target_selesai'] ?? null,
                    'penyedia_telepon' => $d['penyedia_telepon'] ?? null,
                ];
                $spec = array_filter($spec, fn($v) => !is_null($v) && $v !== '');

                // KIB E: Simpan seluruh lainnya_items (multi-item repeater) agar item #2, #3, dst tidak hilang saat update
                if (!empty($d['lainnya_items']) && is_array($d['lainnya_items']) && count($d['lainnya_items']) > 0) {
                    $cleanedItems = array_map(function($item) {
                        return array_filter($item, fn($v) => !is_null($v) && $v !== '' && $v !== false);
                    }, $d['lainnya_items']);
                    $spec['lainnya_items'] = array_values($cleanedItems);
                }

                // ATB: Simpan seluruh atb_items (multi-item repeater) saat update
                if (!empty($d['atb_items']) && is_array($d['atb_items']) && count($d['atb_items']) > 0) {
                    $cleanedAtb = array_map(function($item) {
                        return array_filter($item, fn($v) => !is_null($v) && $v !== '' && $v !== false);
                    }, $d['atb_items']);
                    $spec['atb_items'] = array_values($cleanedAtb);
                    // Sync legacy fields dari item pertama
                    $firstAtb = $d['atb_items'][0] ?? [];
                    if (!isset($spec['atb_judul']) && !empty($firstAtb['atb_judul_nama'])) {
                        $spec['atb_judul'] = $firstAtb['atb_judul_nama'];
                    }
                    if (!isset($spec['atb_pencipta']) && !empty($firstAtb['atb_pencipta'])) {
                        $spec['atb_pencipta'] = $firstAtb['atb_pencipta'];
                    }
                    if (!isset($spec['atb_spesifikasi']) && !empty($firstAtb['atb_spesifikasi'])) {
                        $spec['atb_spesifikasi'] = $firstAtb['atb_spesifikasi'];
                    }
                    if (!isset($spec['ruang_pemegang']) && !empty($firstAtb['atb_ruang_pemegang'])) {
                        $spec['ruang_pemegang'] = $firstAtb['atb_ruang_pemegang'];
                    }
                }

                return [

                    'vol' => max(1, $vol),
                    'sat' => $sat,
                    'hrgSat' => $hrgSat,
                    'totReal' => $totReal,
                    'biaya' => $biaya,
                    'extracom' => $extracom,
                    'spec' => $spec
                ];
            };

            $isTanah = ($jenisPrefix === '1.3.1' || str_starts_with($jenisPrefix, '1.3.1') || str_starts_with($kode108Submitted ?? '', '1.3.1') || ($jenisAstapRecord && str_starts_with($jenisAstapRecord->sub_sub_rincian_objek ?? '', '1.3.1')));
            $isMesin = ($jenisPrefix === '1.3.2' || str_starts_with($jenisPrefix, '1.3.2') || str_starts_with($kode108Submitted ?? '', '1.3.2') || ($jenisAstapRecord && str_starts_with($jenisAstapRecord->sub_sub_rincian_objek ?? '', '1.3.2')));
            $isGedung = ($jenisPrefix === '1.3.3' || str_starts_with($jenisPrefix, '1.3.3') || str_starts_with($kode108Submitted ?? '', '1.3.3') || ($jenisAstapRecord && str_starts_with($jenisAstapRecord->sub_sub_rincian_objek ?? '', '1.3.3')));
            $isJaringan = ($jenisPrefix === '1.3.4' || str_starts_with($jenisPrefix, '1.3.4') || str_starts_with($kode108Submitted ?? '', '1.3.4') || ($jenisAstapRecord && str_starts_with($jenisAstapRecord->sub_sub_rincian_objek ?? '', '1.3.4')));
            $isKdp = ($jenisPrefix === '1.3.6' || str_starts_with($jenisPrefix, '1.3.6') || str_starts_with($kode108Submitted ?? '', '1.3.6') || ($jenisAstapRecord && str_starts_with($jenisAstapRecord->sub_sub_rincian_objek ?? '', '1.3.6')));
            $isAtb = ($jenisPrefix === '1.5.3' || str_starts_with($jenisPrefix, '1.5.3') || str_starts_with($kode108Submitted ?? '', '1.5.3') || ($jenisAstapRecord && str_starts_with($jenisAstapRecord->sub_sub_rincian_objek ?? '', '1.5.3')));

            $hasTanahItems = !empty($data['tanah_items']) && is_array($data['tanah_items']) && count($data['tanah_items']) > 0;
            $hasMesinItems = !empty($data['mesin_items']) && is_array($data['mesin_items']) && count($data['mesin_items']) > 0;
            $hasGedungItems = !empty($data['gedung_items']) && is_array($data['gedung_items']) && count($data['gedung_items']) > 0;
            $hasJaringanItems = !empty($data['jaringan_items']) && is_array($data['jaringan_items']) && count($data['jaringan_items']) > 0;
            $hasKdpItems = !empty($data['kdp_items']) && is_array($data['kdp_items']) && count($data['kdp_items']) > 0;
            $hasAtbItems = !empty($data['atb_items']) && is_array($data['atb_items']) && count($data['atb_items']) > 0;

            if ($isTanah && $hasTanahItems) {
                $totalBidang = 0;
                $totalLuas = 0;
                $totalPerencanaan = 0;
                $totalFisik = 0;
                $totalPengawasan = 0;
                $allSertifikat = [];
                $allAlamat = [];

                foreach ($data['tanah_items'] as $tItem) {
                    $bidangCount = max(1, (int)($tItem['tanah_jumlah_bidang'] ?? 1));
                    $totalBidang += $bidangCount;
                    $totalLuas += (float)($tItem['tanah_luas_m2'] ?? 0);
                    $totalPerencanaan += (float)($tItem['tanah_nilai_perencanaan'] ?? 0);
                    $totalFisik += (float)($tItem['tanah_nilai_fisik'] ?? 0);
                    $totalPengawasan += (float)($tItem['tanah_nilai_pengawasan'] ?? 0);
                    if (!empty($tItem['tanah_sertifikat_no'])) $allSertifikat[] = $tItem['tanah_sertifikat_no'];
                    if (!empty($tItem['tanah_alamat'])) $allAlamat[] = $tItem['tanah_alamat'];
                }

                $totalRealisasi = $totalPerencanaan + $totalFisik + $totalPengawasan;
                $hargaSatuan = $totalBidang > 0 ? ($totalRealisasi / $totalBidang) : $totalRealisasi;
                $firstItem = $data['tanah_items'][0] ?? [];

                $spec = [
                    'jumlah_anggaran' => $data['jumlah_anggaran'] ?? null,
                    'luas_m2' => $totalLuas,
                    'hak_tanah' => $firstItem['tanah_hak'] ?? 'Hak Pakai',
                    'sertifikat_no' => count($allSertifikat) > 0 ? implode(', ', $allSertifikat) : ($firstItem['tanah_sertifikat_no'] ?? null),
                    'sertifikat_tgl' => $firstItem['tanah_sertifikat_tgl'] ?? null,
                    'penggunaan' => $firstItem['tanah_penggunaan'] ?? 'Bangunan Rumah Sakit & Fasilitas',
                    'tanah_jumlah_bidang' => $totalBidang,
                    'nilai_perencanaan' => $totalPerencanaan,
                    'nilai_fisik' => $totalFisik,
                    'nilai_pengawasan' => $totalPengawasan,
                    'tanah_items' => $data['tanah_items']
                ];
                $spec = array_filter($spec, fn($v) => !is_null($v) && $v !== '');

                $ext = [
                    'vol' => max(1, $totalBidang),
                    'sat' => 'Bidang',
                    'hrgSat' => $hargaSatuan,
                    'totReal' => $totalRealisasi,
                    'biaya' => 0,
                    'extracom' => false,
                    'spec' => $spec
                ];
            } elseif ($isMesin && $hasMesinItems) {
                $totalVolume = 0;
                $totalRealisasi = 0;
                $totalBiayaAdm = 0;
                $allMerk = [];
                $allType = [];
                $allUkuran = [];
                $allBahan = [];
                $allPabrik = [];

                foreach ($data['mesin_items'] as $mItem) {
                    $qty = max(1, (int)($mItem['mesin_jumlah_barang'] ?? 1));
                    $nilaiSatuan = (float)($mItem['mesin_nilai_satuan'] ?? 0);
                    $biayaAdmItem = (float)($mItem['mesin_administrasi_proyek'] ?? 0);
                    
                    $totalVolume += $qty;
                    $totalRealisasi += ($qty * $nilaiSatuan) + $biayaAdmItem;
                    $totalBiayaAdm += $biayaAdmItem;

                    if (!empty($mItem['mesin_merk'])) $allMerk[] = $mItem['mesin_merk'];
                    if (!empty($mItem['mesin_type'])) $allType[] = $mItem['mesin_type'];
                    if (!empty($mItem['mesin_ukuran'])) $allUkuran[] = $mItem['mesin_ukuran'];
                    if (!empty($mItem['mesin_bahan'])) $allBahan[] = $mItem['mesin_bahan'];
                    if (!empty($mItem['mesin_no_pabrik'])) $allPabrik[] = $mItem['mesin_no_pabrik'];
                }

                $firstItem = $data['mesin_items'][0] ?? [];
                $satuan = $firstItem['mesin_satuan'] ?? 'Unit';
                $hargaSatuanRata = $totalVolume > 0 ? ($totalRealisasi / $totalVolume) : 0;
                
                $isExtracom = !empty($data['is_extracomtable']);

                $ruangFirst = $firstItem['ruang_pemegang_mesin'] ?? ($firstItem['ruang_pemegang'] ?? ($data['ruang_pemegang_mesin'] ?? ($data['ruang_pemegang'] ?? null)));

                $spec = [
                    'jumlah_anggaran' => $data['jumlah_anggaran'] ?? null,
                    'merk' => count($allMerk) > 0 ? implode(', ', array_unique($allMerk)) : ($firstItem['mesin_merk'] ?? null),
                    'type' => count($allType) > 0 ? implode(', ', array_unique($allType)) : ($firstItem['mesin_type'] ?? null),
                    'ukuran' => count($allUkuran) > 0 ? implode(', ', array_unique($allUkuran)) : ($firstItem['mesin_ukuran'] ?? null),
                    'no_pabrik' => count($allPabrik) > 0 ? implode(', ', array_unique($allPabrik)) : ($firstItem['mesin_no_pabrik'] ?? null),
                    'no_rangka' => $firstItem['mesin_no_rangka'] ?? null,
                    'no_mesin' => $firstItem['mesin_no_mesin'] ?? null,
                    'no_bpkb' => $firstItem['mesin_no_bpkb'] ?? null,
                    'no_polisi' => $firstItem['mesin_no_polisi'] ?? null,
                    'bahan' => count($allBahan) > 0 ? implode(', ', array_unique($allBahan)) : ($firstItem['mesin_bahan'] ?? null),
                    'ruang_pemegang' => $ruangFirst,
                    'mesin_items' => $data['mesin_items']
                ];
                $spec = array_filter($spec, fn($v) => !is_null($v) && $v !== '');

                $ext = [
                    'vol' => max(1, $totalVolume),
                    'sat' => $satuan,
                    'hrgSat' => $hargaSatuanRata,
                    'totReal' => $totalRealisasi,
                    'biaya' => $totalBiayaAdm,
                    'extracom' => $isExtracom,
                    'spec' => $spec
                ];
            } elseif ($isGedung && $hasGedungItems) {
                $totalBangunan = 0;
                $totalLuas = 0;
                $totalPerencanaan = 0;
                $totalFisik = 0;
                $totalPengawasan = 0;
                $totalAp = 0;
                $allAlamat = [];

                foreach ($data['gedung_items'] as $gItem) {
                    $bCount = max(1, (int)($gItem['gedung_jumlah_bangunan'] ?? 1));
                    $totalBangunan += $bCount;
                    $totalLuas += (float)($gItem['gedung_luas_m2'] ?? 0);
                    $totalPerencanaan += (float)($gItem['gedung_nilai_perencanaan'] ?? 0);
                    $totalFisik += (float)($gItem['gedung_nilai_fisik'] ?? 0);
                    $totalPengawasan += (float)($gItem['gedung_nilai_pengawasan'] ?? 0);
                    $totalAp += (float)($gItem['gedung_nilai_ap'] ?? ($gItem['gedung_nilai_pip'] ?? 0));
                    if (!empty($gItem['gedung_alamat'])) $allAlamat[] = $gItem['gedung_alamat'];
                }

                $totalRealisasi = $totalPerencanaan + $totalFisik + $totalPengawasan + $totalAp;
                $hargaSatuanRata = $totalBangunan > 0 ? ($totalRealisasi / $totalBangunan) : $totalRealisasi;
                $firstItem = $data['gedung_items'][0] ?? [];
                $satuan = $firstItem['gedung_satuan'] ?? 'Gedung';

                $spec = [
                    'jumlah_anggaran' => $data['jumlah_anggaran'] ?? null,
                    'luas_m2' => $totalLuas,
                    'bertingkat' => $firstItem['gedung_bertingkat'] ?? 'Bertingkat',
                    'beton' => $firstItem['gedung_beton'] ?? 'Beton',
                    'status_tanah' => $firstItem['gedung_status_tanah'] ?? 'Tanah Hak Pakai RSUD',
                    'kode_aset_tanah' => $firstItem['gedung_kode_aset_tanah'] ?? '1.3.1.01.01.02.013',
                    'is_baru' => $firstItem['gedung_is_baru'] ?? 'Baru',
                    'kapitalisasi_tahun_induk' => $firstItem['gedung_kapitalisasi_tahun_induk'] ?? null,
                    'kapitalisasi_nilai_induk' => $firstItem['gedung_kapitalisasi_nilai_induk'] ?? 0,
                    'gedung_jumlah_bangunan' => $totalBangunan,
                    'nilai_perencanaan' => $totalPerencanaan,
                    'nilai_fisik' => $totalFisik,
                    'nilai_pengawasan' => $totalPengawasan,
                    'nilai_ap' => $totalAp,
                    'nilai_pip' => $totalAp,
                    'gedung_items' => $data['gedung_items']
                ];
                $spec = array_filter($spec, fn($v) => !is_null($v) && $v !== '');

                $ext = [
                    'vol' => max(1, $totalBangunan),
                    'sat' => $satuan,
                    'hrgSat' => $hargaSatuanRata,
                    'totReal' => $totalRealisasi,
                    'biaya' => 0,
                    'extracom' => false,
                    'spec' => $spec
                ];
            } elseif ($isJaringan && $hasJaringanItems) {
                $totalJaringan = 0;
                $totalLuas = 0;
                $totalPerencanaan = 0;
                $totalFisik = 0;
                $totalPengawasan = 0;
                $totalAp = 0;
                $allAlamat = [];

                foreach ($data['jaringan_items'] as $jItem) {
                    $jCount = max(1, (int)($jItem['jaringan_jumlah'] ?? ($jItem['jaringan_jumlah_jaringan'] ?? 1)));
                    $totalJaringan += $jCount;
                    $totalLuas += (float)($jItem['jaringan_luas_m2'] ?? 0);
                    $totalPerencanaan += (float)($jItem['jaringan_nilai_perencanaan'] ?? 0);
                    $totalFisik += (float)($jItem['jaringan_nilai_fisik'] ?? 0);
                    $totalPengawasan += (float)($jItem['jaringan_nilai_pengawasan'] ?? 0);
                    $totalAp += (float)($jItem['jaringan_nilai_ap'] ?? ($jItem['jaringan_nilai_pip'] ?? 0));
                    if (!empty($jItem['jaringan_alamat'])) $allAlamat[] = $jItem['jaringan_alamat'];
                }

                $totalRealisasi = $totalPerencanaan + $totalFisik + $totalPengawasan + $totalAp;
                $hargaSatuanRata = $totalJaringan > 0 ? ($totalRealisasi / $totalJaringan) : $totalRealisasi;
                $firstItem = $data['jaringan_items'][0] ?? [];
                $satuan = $firstItem['jaringan_satuan'] ?? 'Paket';

                $spec = [
                    'jumlah_anggaran' => $data['jumlah_anggaran'] ?? null,
                    'panjang_m' => (float)($firstItem['jaringan_panjang_m'] ?? 0),
                    'lebar_m' => (float)($firstItem['jaringan_lebar_m'] ?? 0),
                    'luas_m2' => $totalLuas,
                    'bertingkat' => $firstItem['jaringan_bertingkat'] ?? 'Bertingkat',
                    'beton' => $firstItem['jaringan_beton'] ?? 'Beton',
                    'status_tanah' => $firstItem['jaringan_status_tanah'] ?? 'Tanah Hak Pakai RSUD',
                    'kode_aset_tanah' => $firstItem['jaringan_kode_aset_tanah'] ?? '1.3.1.01.01.02.013',
                    'is_baru' => $firstItem['jaringan_is_baru'] ?? 'Baru',
                    'kapitalisasi_tahun_induk' => $firstItem['jaringan_kapitalisasi_tahun_induk'] ?? null,
                    'kapitalisasi_nilai_induk' => $firstItem['jaringan_kapitalisasi_nilai_induk'] ?? 0,
                    'jaringan_jumlah' => $totalJaringan,
                    'nilai_perencanaan' => $totalPerencanaan,
                    'nilai_fisik' => $totalFisik,
                    'nilai_pengawasan' => $totalPengawasan,
                    'nilai_ap' => $totalAp,
                    'nilai_pip' => $totalAp,
                    'jaringan_items' => $data['jaringan_items']
                ];
                $spec = array_filter($spec, fn($v) => !is_null($v) && $v !== '');

                $ext = [
                    'vol' => max(1, $totalJaringan),
                    'sat' => $satuan,
                    'hrgSat' => $hargaSatuanRata,
                    'totReal' => $totalRealisasi,
                    'biaya' => 0,
                    'extracom' => false,
                    'spec' => $spec
                ];
            } elseif ($isKdp && $hasKdpItems) {
                $totalBangunan = 0;
                $totalLuas = 0;
                $totalPerencanaan = 0;
                $totalFisik = 0;
                $totalPengawasan = 0;
                $totalPip = 0;
                $allAlamat = [];

                foreach ($data['kdp_items'] as $kItem) {
                    $bCount = max(1, (int)($kItem['kdp_jumlah_bangunan'] ?? 1));
                    $totalBangunan += $bCount;
                    $totalLuas += (float)($kItem['kdp_luas_m2'] ?? 0);
                    $totalPerencanaan += (float)($kItem['kdp_nilai_perencanaan'] ?? 0);
                    $totalFisik += (float)($kItem['kdp_nilai_fisik'] ?? 0);
                    $totalPengawasan += (float)($kItem['kdp_nilai_pengawasan'] ?? 0);
                    $totalPip += (float)($kItem['kdp_nilai_ap'] ?? ($kItem['kdp_nilai_pip'] ?? 0));
                    if (!empty($kItem['kdp_alamat'])) $allAlamat[] = $kItem['kdp_alamat'];
                }

                $totalRealisasi = $totalPerencanaan + $totalFisik + $totalPengawasan + $totalPip;
                $hargaSatuanRata = $totalBangunan > 0 ? ($totalRealisasi / $totalBangunan) : $totalRealisasi;
                $firstItem = $data['kdp_items'][0] ?? [];
                $satuan = $firstItem['kdp_satuan'] ?? 'Gedung';

                $spec = [
                    'jumlah_anggaran' => $data['jumlah_anggaran'] ?? null,
                    'luas_m2' => $totalLuas,
                    'progres_persen' => $firstItem['kdp_progres_persen'] ?? 0,
                    'bertingkat' => $firstItem['kdp_bertingkat'] ?? 'Bertingkat',
                    'beton' => $firstItem['kdp_beton'] ?? 'Beton',
                    'status_tanah' => $firstItem['kdp_status_tanah'] ?? 'Tanah Hak Pakai RSUD',
                    'kode_aset_tanah' => $firstItem['kdp_kode_aset_tanah'] ?? '1.3.1.01.01.02.013',
                    'is_baru' => $firstItem['kdp_is_baru'] ?? 'Baru',
                    'kapitalisasi_tahun_induk' => $firstItem['kdp_kapitalisasi_tahun_induk'] ?? null,
                    'kapitalisasi_nilai_induk' => $firstItem['kdp_kapitalisasi_nilai_induk'] ?? 0,
                    'kdp_jumlah_bangunan' => $totalBangunan,
                    'nilai_perencanaan' => $totalPerencanaan,
                    'nilai_fisik' => $totalFisik,
                    'nilai_pengawasan' => $totalPengawasan,
                    'nilai_pip' => $totalPip,
                    'kdp_items' => $data['kdp_items']
                ];
                $spec = array_filter($spec, fn($v) => !is_null($v) && $v !== '');

                $ext = [
                    'vol' => max(1, $totalBangunan),
                    'sat' => $satuan,
                    'hrgSat' => $hargaSatuanRata,
                    'totReal' => $totalRealisasi,
                    'biaya' => 0,
                    'extracom' => false,
                    'spec' => $spec
                ];
            } elseif ($isAtb && $hasAtbItems) {
                $totalVolume = 0;
                $totalRealisasi = 0;
                $totalBiayaAdm = 0;
                $allJudul = [];
                $allPencipta = [];
                $allSpesifikasi = [];

                foreach ($data['atb_items'] as $aItem) {
                    $qty = max(1, (int)($aItem['atb_jumlah'] ?? 1));
                    $nilaiSatuan = (float)($aItem['atb_nilai_satuan'] ?? 0);
                    $biayaAdmItem = (float)($aItem['atb_administrasi_proyek'] ?? 0);

                    $totalVolume += $qty;
                    $totalRealisasi += ($qty * $nilaiSatuan) + $biayaAdmItem;
                    $totalBiayaAdm += $biayaAdmItem;

                    if (!empty($aItem['atb_judul_nama'])) $allJudul[] = $aItem['atb_judul_nama'];
                    if (!empty($aItem['atb_pencipta'])) $allPencipta[] = $aItem['atb_pencipta'];
                    if (!empty($aItem['atb_spesifikasi'])) $allSpesifikasi[] = $aItem['atb_spesifikasi'];
                }

                $firstItem = $data['atb_items'][0] ?? [];
                $satuan = $firstItem['atb_satuan'] ?? 'Lisensi';
                $hargaSatuanRata = $totalVolume > 0 ? ($totalRealisasi / $totalVolume) : 0;
                $isExtracom = !empty($data['is_extracomtable']);
                $ruangFirst = $firstItem['atb_ruang_pemegang'] ?? ($data['ruang_pemegang_atb'] ?? ($data['ruang_pemegang'] ?? null));

                $cleanedAtb = array_map(function($item) {
                    return array_filter($item, fn($v) => !is_null($v) && $v !== '' && $v !== false);
                }, $data['atb_items']);

                $spec = [
                    'jumlah_anggaran' => $data['jumlah_anggaran'] ?? null,
                    'atb_judul' => count($allJudul) > 0 ? implode(', ', array_unique($allJudul)) : ($firstItem['atb_judul_nama'] ?? null),
                    'atb_pencipta' => count($allPencipta) > 0 ? implode(', ', array_unique($allPencipta)) : ($firstItem['atb_pencipta'] ?? null),
                    'atb_spesifikasi' => count($allSpesifikasi) > 0 ? implode('; ', array_unique($allSpesifikasi)) : ($firstItem['atb_spesifikasi'] ?? null),
                    'atb_nama_barang' => $firstItem['atb_nama_barang'] ?? ($data['atb_nama_barang'] ?? null),
                    'atb_kode_barang' => $firstItem['atb_kode_barang'] ?? ($data['atb_kode_barang'] ?? null),
                    'ruang_pemegang' => $ruangFirst,
                    'atb_items' => array_values($cleanedAtb)
                ];
                $spec = array_filter($spec, fn($v) => !is_null($v) && $v !== '');

                $ext = [
                    'vol' => max(1, $totalVolume),
                    'sat' => $satuan,
                    'hrgSat' => $hargaSatuanRata,
                    'totReal' => $totalRealisasi,
                    'biaya' => $totalBiayaAdm,
                    'extracom' => $isExtracom,
                    'spec' => $spec
                ];
            } else {
                $ext = $extractAstapPayload($data, $jenisPrefix);
            }

            $namaInput = match(true) {
                $jenisPrefix === '1.3.1' => $data['tanah_nama_barang'] ?? ($data['tanah_items'][0]['tanah_nama_barang'] ?? null),
                $jenisPrefix === '1.3.2' => $data['mesin_nama_barang'] ?? ($data['mesin_items'][0]['mesin_nama_barang'] ?? null),
                $jenisPrefix === '1.3.3' => $data['gedung_nama_barang'] ?? ($data['gedung_items'][0]['gedung_nama_barang'] ?? null),
                $jenisPrefix === '1.3.4' => $data['jaringan_nama_barang'] ?? ($data['jaringan_items'][0]['jaringan_nama_barang'] ?? null),
                $jenisPrefix === '1.3.5' => $data['lainnya_nama_barang'] ?? null,
                $jenisPrefix === '1.5.3' => $data['atb_nama_barang'] ?? ($data['atb_items'][0]['atb_nama_barang'] ?? null),
                $jenisPrefix === '1.3.6' => $data['kdp_nama_barang'] ?? ($data['kdp_items'][0]['kdp_nama_barang'] ?? null),
                default => $data['nama_barang'] ?? null
            };

            if (!empty($namaInput)) {
                $astap->nama_barang = $namaInput;
            } elseif ($jenisAstapRecord && !empty($jenisAstapRecord->uraian_sub_sub_rincian)) {
                $astap->nama_barang = $jenisAstapRecord->uraian_sub_sub_rincian;
            }

            if (!empty($data['tahun_perolehan'])) $astap->tahun_perolehan = $data['tahun_perolehan'];
            if (!empty($data['triwulan'])) $astap->triwulan = $data['triwulan'];
            $astap->jumlah_volume = $ext['vol'];
            $astap->satuan = $ext['sat'];
            $astap->harga_satuan = $ext['hrgSat'];
            $astap->jumlah_anggaran = !empty($data['jumlah_anggaran']) ? (float) $data['jumlah_anggaran'] : $ext['totReal'];
            $astap->total_realisasi = $ext['totReal'];
            $astap->biaya_administrasi_proyek = $ext['biaya'];
            $astap->is_extracomtable = $ext['extracom'];

            $astap->spk_nomor = $data['spk_nomor'] ?? null;
            $astap->spk_tanggal = $data['spk_tanggal'] ?? null;
            $astap->surat_pesanan_nomor = $data['surat_pesanan_nomor'] ?? null;
            $astap->surat_pesanan_tanggal = $data['surat_pesanan_tanggal'] ?? null;
            $astap->kwitansi_nomor = $data['kwitansi_nomor'] ?? null;
            $astap->kwitansi_tanggal = $data['kwitansi_tanggal'] ?? null;
            $astap->faktur_nomor = $data['faktur_nomor'] ?? null;
            $astap->faktur_tanggal = $data['faktur_tanggal'] ?? null;
            $astap->sp2d_nomor = $data['sp2d_nomor'] ?? null;
            $astap->sp2d_tanggal = $data['sp2d_tanggal'] ?? null;
            $astap->bast_dokumen_nomor = $data['bast_dokumen_nomor'] ?? null;
            $astap->bast_dokumen_tanggal = $data['bast_dokumen_tanggal'] ?? null;

            if (isset($data['alamat_barang'])) $astap->alamat_barang = $data['alamat_barang'];
            if (isset($data['penyedia_nama'])) $astap->penyedia_nama = $data['penyedia_nama'];
            if (isset($data['penyedia_pemilik'])) $astap->penyedia_pemilik = $data['penyedia_pemilik'];
            if (isset($data['penyedia_telepon']) && \Schema::hasColumn('astaps', 'penyedia_telepon')) $astap->penyedia_telepon = $data['penyedia_telepon'];
            if (isset($data['penyedia_rekening_nama'])) $astap->penyedia_rekening_nama = $data['penyedia_rekening_nama'];
            if (isset($data['penyedia_rekening_nomor'])) $astap->penyedia_rekening_nomor = $data['penyedia_rekening_nomor'];
            if (isset($data['penyedia_alamat'])) $astap->penyedia_alamat = $data['penyedia_alamat'];
            if (isset($data['ppk_nama'])) $astap->ppk_nama = $data['ppk_nama'];
            if (isset($data['ppk_nip'])) $astap->ppk_nip = $data['ppk_nip'];
            if (isset($data['keterangan_tambahan']) || isset($data['keterangan'])) {
                $astap->keterangan_tambahan = $data['keterangan_tambahan'] ?? ($data['keterangan'] ?? null);
            }
            $astap->spesifikasi_json = $ext['spec'];
            $astap->save();

            // Sync AstapRegisters jika tanah_items atau mesin_items
            if ($isTanah && $hasTanahItems) {
                $targetCount = count($data['tanah_items']);
                $existingRegs = $astap->registers()->orderBy('id')->get();
                $existingCount = $existingRegs->count();

                $kode108Clean = '131000000000';
                if ($jenisAstapRecord && !empty($jenisAstapRecord->sub_sub_rincian_objek)) {
                    $kode108Clean = str_replace('.', '', $jenisAstapRecord->sub_sub_rincian_objek);
                } elseif (!empty($data['tanah_kode_barang'])) {
                    $kode108Clean = str_replace('.', '', $data['tanah_kode_barang']);
                }

                $tahun = $astap->tahun_perolehan;

                $maxRegInt = \App\Models\AstapRegister::where('tahun_perolehan', $tahun)
                    ->where('astap_id', '!=', $astap->id)
                    ->where(function($q) use ($astap) {
                        if ($astap->jenis_astap_id) {
                            $q->whereHas('astap', fn($sq) => $sq->where('jenis_astap_id', $astap->jenis_astap_id));
                        }
                    })
                    ->max('no_register_int') ?? 0;
                $runningRegNum = (int) $maxRegInt;

                // 1. Update existing registers with current tanah_items
                foreach ($data['tanah_items'] as $itemIdx => $tItem) {
                    $rawKondisi = strtoupper(trim((string)($tItem['tanah_kondisi'] ?? 'Baik')));
                    $kondisiStr = ($rawKondisi === 'KB' || $rawKondisi === 'KURANG BAIK') ? 'Kurang Baik' : (($rawKondisi === 'RB' || $rawKondisi === 'RUSAK BERAT' || $rawKondisi === 'RUSAK') ? 'Rusak Berat' : (($rawKondisi === 'RR' || $rawKondisi === 'RUSAK RINGAN') ? 'Rusak Ringan' : 'Baik'));

                    $runningRegNum++;
                    $noRegStr = str_pad($runningRegNum, 7, '0', STR_PAD_LEFT);
                    $nibar = "1201351102000000280000{$tahun}{$kode108Clean}{$noRegStr}";

                    if ($itemIdx < $existingCount) {
                        $reg = $existingRegs[$itemIdx];
                        while (\App\Models\AstapRegister::where('nibar', $nibar)->where('id', '!=', $reg->id)->exists()) {
                            $runningRegNum++;
                            $noRegStr = str_pad($runningRegNum, 7, '0', STR_PAD_LEFT);
                            $nibar = "1201351102000000280000{$tahun}{$kode108Clean}{$noRegStr}";
                        }
                        $reg->tahun_perolehan = $tahun;
                        $reg->no_register_int = $runningRegNum;
                        $reg->no_register = $nibar;
                        $reg->nibar = $nibar;
                        $reg->qr_code_path = "/scan/{$nibar}";
                        $reg->kondisi = $kondisiStr;
                        $reg->save();
                    } else {
                        while (\App\Models\AstapRegister::where('nibar', $nibar)->exists()) {
                            $runningRegNum++;
                            $noRegStr = str_pad($runningRegNum, 7, '0', STR_PAD_LEFT);
                            $nibar = "1201351102000000280000{$tahun}{$kode108Clean}{$noRegStr}";
                        }

                        $qrPath = "/scan/{$nibar}";
                        \App\Models\AstapRegister::create([
                            'astap_id' => $astap->id,
                            'tahun_perolehan' => $tahun,
                            'no_register_int' => $runningRegNum,
                            'no_register' => $nibar,
                            'nibar' => $nibar,
                            'qr_code_path' => $qrPath,
                            'ruang_pemegang' => null,
                            'kondisi' => $kondisiStr,
                            'status' => 'Tersedia'
                        ]);
                    }
                }

                // Hapus register berlebih jika tanah_items dikurangi
                if ($existingCount > $targetCount) {
                    for ($k = $targetCount; $k < $existingCount; $k++) {
                        $existingRegs[$k]->delete();
                    }
                }
            } elseif ($hasMesinItems) {
                // Flatten target units from mesin_items
                $targetUnits = [];
                foreach ($data['mesin_items'] as $mItem) {
                    $qty = max(1, (int)($mItem['mesin_jumlah_barang'] ?? 1));
                    $rawKondisi = strtoupper(trim((string)($mItem['mesin_kondisi'] ?? 'Baik')));
                    $kondisiStr = ($rawKondisi === 'KB' || $rawKondisi === 'KURANG BAIK') ? 'Kurang Baik' : (($rawKondisi === 'RB' || $rawKondisi === 'RUSAK BERAT' || $rawKondisi === 'RUSAK') ? 'Rusak Berat' : (($rawKondisi === 'RR' || $rawKondisi === 'RUSAK RINGAN') ? 'Rusak Ringan' : 'Baik'));
                    $ruang = $mItem['ruang_pemegang_mesin'] ?? ($mItem['ruang_pemegang'] ?? ($data['ruang_pemegang_mesin'] ?? ($data['ruang_pemegang'] ?? null)));
                    for ($q = 0; $q < $qty; $q++) {
                        $targetUnits[] = [
                            'kondisi' => $kondisiStr,
                            'ruang_pemegang' => $ruang
                        ];
                    }
                }

                $targetCount = count($targetUnits);
                $existingRegs = $astap->registers()->orderBy('id')->get();
                $existingCount = $existingRegs->count();

                $kode108Clean = '132000000000';
                if ($jenisAstapRecord && !empty($jenisAstapRecord->sub_sub_rincian_objek)) {
                    $kode108Clean = str_replace('.', '', $jenisAstapRecord->sub_sub_rincian_objek);
                } elseif (!empty($data['mesin_kode_barang'])) {
                    $kode108Clean = str_replace('.', '', $data['mesin_kode_barang']);
                }

                $tahun = $astap->tahun_perolehan;

                $maxRegInt = \App\Models\AstapRegister::where('tahun_perolehan', $tahun)
                    ->where('astap_id', '!=', $astap->id)
                    ->where(function($q) use ($astap) {
                        if ($astap->jenis_astap_id) {
                            $q->whereHas('astap', fn($sq) => $sq->where('jenis_astap_id', $astap->jenis_astap_id));
                        }
                    })
                    ->max('no_register_int') ?? 0;
                $runningRegNum = (int) $maxRegInt;

                // 1. Update existing registers & create new if needed
                foreach ($targetUnits as $unitIdx => $unitData) {
                    $runningRegNum++;
                    $noRegStr = str_pad($runningRegNum, 7, '0', STR_PAD_LEFT);
                    $nibar = "1201351102000000280000{$tahun}{$kode108Clean}{$noRegStr}";

                    if ($unitIdx < $existingCount) {
                        $reg = $existingRegs[$unitIdx];
                        while (\App\Models\AstapRegister::where('nibar', $nibar)->where('id', '!=', $reg->id)->exists()) {
                            $runningRegNum++;
                            $noRegStr = str_pad($runningRegNum, 7, '0', STR_PAD_LEFT);
                            $nibar = "1201351102000000280000{$tahun}{$kode108Clean}{$noRegStr}";
                        }
                        $reg->tahun_perolehan = $tahun;
                        $reg->no_register_int = $runningRegNum;
                        $reg->no_register = $nibar;
                        $reg->nibar = $nibar;
                        $reg->qr_code_path = "/scan/{$nibar}";
                        $reg->kondisi = $unitData['kondisi'];
                        $reg->ruang_pemegang = $unitData['ruang_pemegang'];
                        $reg->save();
                    } else {
                        while (\App\Models\AstapRegister::where('nibar', $nibar)->exists()) {
                            $runningRegNum++;
                            $noRegStr = str_pad($runningRegNum, 7, '0', STR_PAD_LEFT);
                            $nibar = "1201351102000000280000{$tahun}{$kode108Clean}{$noRegStr}";
                        }

                        $qrPath = "/scan/{$nibar}";
                        \App\Models\AstapRegister::create([
                            'astap_id' => $astap->id,
                            'tahun_perolehan' => $tahun,
                            'no_register_int' => $runningRegNum,
                            'no_register' => $nibar,
                            'nibar' => $nibar,
                            'qr_code_path' => $qrPath,
                            'ruang_pemegang' => $unitData['ruang_pemegang'],
                            'kondisi' => $unitData['kondisi'],
                            'status' => 'Tersedia'
                        ]);
                    }
                }

                // Hapus register berlebih jika targetCount < existingCount
                if ($existingCount > $targetCount) {
                    for ($k = $targetCount; $k < $existingCount; $k++) {
                        $existingRegs[$k]->delete();
                    }
                }
            } elseif ($hasGedungItems) {
                $targetUnits = [];
                foreach ($data['gedung_items'] as $gItem) {
                    $qty = max(1, (int)($gItem['gedung_jumlah_bangunan'] ?? 1));
                    $rawKondisi = strtoupper(trim((string)($gItem['gedung_kondisi'] ?? 'Baik')));
                    $kondisiStr = ($rawKondisi === 'KB' || $rawKondisi === 'KURANG BAIK') ? 'Kurang Baik' : (($rawKondisi === 'RB' || $rawKondisi === 'RUSAK BERAT' || $rawKondisi === 'RUSAK') ? 'Rusak Berat' : 'Baik');
                    for ($q = 0; $q < $qty; $q++) {
                        $targetUnits[] = [
                            'kondisi' => $kondisiStr,
                            'ruang_pemegang' => null
                        ];
                    }
                }

                $targetCount = count($targetUnits);
                $existingRegs = $astap->registers()->orderBy('id')->get();
                $existingCount = $existingRegs->count();

                $kode108Clean = '133000000000';
                if ($jenisAstapRecord && !empty($jenisAstapRecord->sub_sub_rincian_objek)) {
                    $kode108Clean = str_replace('.', '', $jenisAstapRecord->sub_sub_rincian_objek);
                } elseif (!empty($data['gedung_kode_barang'])) {
                    $kode108Clean = str_replace('.', '', $data['gedung_kode_barang']);
                } elseif (!empty($data['gedung_items'][0]['gedung_kode_barang'])) {
                    $kode108Clean = str_replace('.', '', $data['gedung_items'][0]['gedung_kode_barang']);
                }

                $tahun = $astap->tahun_perolehan;

                $maxRegInt = \App\Models\AstapRegister::where('tahun_perolehan', $tahun)
                    ->where('astap_id', '!=', $astap->id)
                    ->where(function($q) use ($astap) {
                        if ($astap->jenis_astap_id) {
                            $q->whereHas('astap', fn($sq) => $sq->where('jenis_astap_id', $astap->jenis_astap_id));
                        }
                    })
                    ->max('no_register_int') ?? 0;
                $runningRegNum = (int) $maxRegInt;

                foreach ($targetUnits as $itemIdx => $uUnit) {
                    $runningRegNum++;
                    $noRegStr = str_pad($runningRegNum, 7, '0', STR_PAD_LEFT);
                    $nibar = "1201351102000000280000{$tahun}{$kode108Clean}{$noRegStr}";

                    if ($itemIdx < $existingCount) {
                        $reg = $existingRegs[$itemIdx];
                        while (\App\Models\AstapRegister::where('nibar', $nibar)->where('id', '!=', $reg->id)->exists()) {
                            $runningRegNum++;
                            $noRegStr = str_pad($runningRegNum, 7, '0', STR_PAD_LEFT);
                            $nibar = "1201351102000000280000{$tahun}{$kode108Clean}{$noRegStr}";
                        }
                        $reg->tahun_perolehan = $tahun;
                        $reg->no_register_int = $runningRegNum;
                        $reg->no_register = $nibar;
                        $reg->nibar = $nibar;
                        $reg->qr_code_path = "/scan/{$nibar}";
                        $reg->kondisi = $uUnit['kondisi'];
                        $reg->save();
                    } else {
                        while (\App\Models\AstapRegister::where('nibar', $nibar)->exists()) {
                            $runningRegNum++;
                            $noRegStr = str_pad($runningRegNum, 7, '0', STR_PAD_LEFT);
                            $nibar = "1201351102000000280000{$tahun}{$kode108Clean}{$noRegStr}";
                        }

                        $qrPath = "/scan/{$nibar}";
                        \App\Models\AstapRegister::create([
                            'astap_id' => $astap->id,
                            'tahun_perolehan' => $tahun,
                            'no_register_int' => $runningRegNum,
                            'no_register' => $nibar,
                            'nibar' => $nibar,
                            'qr_code_path' => $qrPath,
                            'ruang_pemegang' => null,
                            'kondisi' => $uUnit['kondisi'],
                            'status' => 'Tersedia'
                        ]);
                    }
                }

                if ($existingCount > $targetCount) {
                    for ($k = $targetCount; $k < $existingCount; $k++) {
                        $existingRegs[$k]->delete();
                    }
                }
            } elseif ($hasJaringanItems) {
                $targetUnits = [];
                foreach ($data['jaringan_items'] as $jItem) {
                    $qty = max(1, (int)($jItem['jaringan_jumlah'] ?? ($jItem['jaringan_jumlah_jaringan'] ?? 1)));
                    $rawKondisi = strtoupper(trim((string)($jItem['jaringan_kondisi'] ?? 'Baik')));
                    $kondisiStr = ($rawKondisi === 'KB' || $rawKondisi === 'KURANG BAIK') ? 'Kurang Baik' : (($rawKondisi === 'RB' || $rawKondisi === 'RUSAK BERAT' || $rawKondisi === 'RUSAK') ? 'Rusak Berat' : 'Baik');
                    for ($q = 0; $q < $qty; $q++) {
                        $targetUnits[] = [
                            'kondisi' => $kondisiStr,
                            'ruang_pemegang' => null
                        ];
                    }
                }

                $targetCount = count($targetUnits);
                $existingRegs = $astap->registers()->orderBy('id')->get();
                $existingCount = $existingRegs->count();

                $kode108Clean = '134000000000';
                if ($jenisAstapRecord && !empty($jenisAstapRecord->sub_sub_rincian_objek)) {
                    $kode108Clean = str_replace('.', '', $jenisAstapRecord->sub_sub_rincian_objek);
                } elseif (!empty($data['jaringan_kode_barang'])) {
                    $kode108Clean = str_replace('.', '', $data['jaringan_kode_barang']);
                } elseif (!empty($data['jaringan_items'][0]['jaringan_kode_barang'])) {
                    $kode108Clean = str_replace('.', '', $data['jaringan_items'][0]['jaringan_kode_barang']);
                }

                $tahun = $astap->tahun_perolehan;

                $maxRegInt = \App\Models\AstapRegister::where('tahun_perolehan', $tahun)
                    ->where('astap_id', '!=', $astap->id)
                    ->where(function($q) use ($astap) {
                        if ($astap->jenis_astap_id) {
                            $q->whereHas('astap', fn($sq) => $sq->where('jenis_astap_id', $astap->jenis_astap_id));
                        }
                    })
                    ->max('no_register_int') ?? 0;
                $runningRegNum = (int) $maxRegInt;

                foreach ($targetUnits as $itemIdx => $uUnit) {
                    $runningRegNum++;
                    $noRegStr = str_pad($runningRegNum, 7, '0', STR_PAD_LEFT);
                    $nibar = "1201351102000000280000{$tahun}{$kode108Clean}{$noRegStr}";

                    if ($itemIdx < $existingCount) {
                        $reg = $existingRegs[$itemIdx];
                        while (\App\Models\AstapRegister::where('nibar', $nibar)->where('id', '!=', $reg->id)->exists()) {
                            $runningRegNum++;
                            $noRegStr = str_pad($runningRegNum, 7, '0', STR_PAD_LEFT);
                            $nibar = "1201351102000000280000{$tahun}{$kode108Clean}{$noRegStr}";
                        }
                        $reg->tahun_perolehan = $tahun;
                        $reg->no_register_int = $runningRegNum;
                        $reg->no_register = $nibar;
                        $reg->nibar = $nibar;
                        $reg->qr_code_path = "/scan/{$nibar}";
                        $reg->kondisi = $uUnit['kondisi'];
                        $reg->save();
                    } else {
                        while (\App\Models\AstapRegister::where('nibar', $nibar)->exists()) {
                            $runningRegNum++;
                            $noRegStr = str_pad($runningRegNum, 7, '0', STR_PAD_LEFT);
                            $nibar = "1201351102000000280000{$tahun}{$kode108Clean}{$noRegStr}";
                        }

                        $qrPath = "/scan/{$nibar}";
                        \App\Models\AstapRegister::create([
                            'astap_id' => $astap->id,
                            'tahun_perolehan' => $tahun,
                            'no_register_int' => $runningRegNum,
                            'no_register' => $nibar,
                            'nibar' => $nibar,
                            'qr_code_path' => $qrPath,
                            'ruang_pemegang' => null,
                            'kondisi' => $uUnit['kondisi'],
                            'status' => 'Tersedia'
                        ]);
                    }
                }

                if ($existingCount > $targetCount) {
                    for ($k = $targetCount; $k < $existingCount; $k++) {
                        $existingRegs[$k]->delete();
                    }
                }
            } elseif ($isKdp && $hasKdpItems) {
                $targetUnits = [];
                foreach ($data['kdp_items'] as $kItem) {
                    $qty = max(1, (int)($kItem['kdp_jumlah_bangunan'] ?? 1));
                    $rawKondisi = strtoupper(trim((string)($kItem['kdp_kondisi'] ?? 'Baik')));
                    $kondisiStr = ($rawKondisi === 'KB' || $rawKondisi === 'KURANG BAIK') ? 'Kurang Baik' : (($rawKondisi === 'RB' || $rawKondisi === 'RUSAK BERAT' || $rawKondisi === 'RUSAK') ? 'Rusak Berat' : 'Baik');
                    for ($q = 0; $q < $qty; $q++) {
                        $targetUnits[] = [
                            'kondisi' => $kondisiStr,
                            'ruang_pemegang' => null
                        ];
                    }
                }

                $targetCount = count($targetUnits);
                $existingRegs = $astap->registers()->orderBy('id')->get();
                $existingCount = $existingRegs->count();

                $kode108Clean = '136000000000';
                if ($jenisAstapRecord && !empty($jenisAstapRecord->sub_sub_rincian_objek)) {
                    $kode108Clean = str_replace('.', '', $jenisAstapRecord->sub_sub_rincian_objek);
                } elseif (!empty($data['kdp_kode_barang'])) {
                    $kode108Clean = str_replace('.', '', $data['kdp_kode_barang']);
                } elseif (!empty($data['kdp_items'][0]['kdp_kode_barang'])) {
                    $kode108Clean = str_replace('.', '', $data['kdp_items'][0]['kdp_kode_barang']);
                }

                $tahun = $astap->tahun_perolehan;

                $maxRegInt = \App\Models\AstapRegister::where('tahun_perolehan', $tahun)
                    ->where('astap_id', '!=', $astap->id)
                    ->where(function($q) use ($astap) {
                        if ($astap->jenis_astap_id) {
                            $q->whereHas('astap', fn($sq) => $sq->where('jenis_astap_id', $astap->jenis_astap_id));
                        }
                    })
                    ->max('no_register_int') ?? 0;
                $runningRegNum = (int) $maxRegInt;

                foreach ($targetUnits as $itemIdx => $uUnit) {
                    $runningRegNum++;
                    $noRegStr = str_pad($runningRegNum, 7, '0', STR_PAD_LEFT);
                    $nibar = "1201351102000000280000{$tahun}{$kode108Clean}{$noRegStr}";

                    if ($itemIdx < $existingCount) {
                        $reg = $existingRegs[$itemIdx];
                        while (\App\Models\AstapRegister::where('nibar', $nibar)->where('id', '!=', $reg->id)->exists()) {
                            $runningRegNum++;
                            $noRegStr = str_pad($runningRegNum, 7, '0', STR_PAD_LEFT);
                            $nibar = "1201351102000000280000{$tahun}{$kode108Clean}{$noRegStr}";
                        }
                        $reg->tahun_perolehan = $tahun;
                        $reg->no_register_int = $runningRegNum;
                        $reg->no_register = $nibar;
                        $reg->nibar = $nibar;
                        $reg->qr_code_path = "/scan/{$nibar}";
                        $reg->kondisi = $uUnit['kondisi'];
                        $reg->save();
                    } else {
                        while (\App\Models\AstapRegister::where('nibar', $nibar)->exists()) {
                            $runningRegNum++;
                            $noRegStr = str_pad($runningRegNum, 7, '0', STR_PAD_LEFT);
                            $nibar = "1201351102000000280000{$tahun}{$kode108Clean}{$noRegStr}";
                        }

                        $qrPath = "/scan/{$nibar}";
                        \App\Models\AstapRegister::create([
                            'astap_id' => $astap->id,
                            'tahun_perolehan' => $tahun,
                            'no_register_int' => $runningRegNum,
                            'no_register' => $nibar,
                            'nibar' => $nibar,
                            'qr_code_path' => $qrPath,
                            'ruang_pemegang' => null,
                            'kondisi' => $uUnit['kondisi'],
                            'status' => 'Tersedia'
                        ]);
                    }
                }

                if ($existingCount > $targetCount) {
                    for ($k = $targetCount; $k < $existingCount; $k++) {
                        $existingRegs[$k]->delete();
                    }
                }
            } elseif ($isAtb && $hasAtbItems) {
                $targetUnits = [];
                foreach ($data['atb_items'] as $aItem) {
                    $qty = max(1, (int)($aItem['atb_jumlah'] ?? 1));
                    $rawKondisi = strtoupper(trim((string)($aItem['atb_kondisi'] ?? 'Baik')));
                    $kondisiStr = ($rawKondisi === 'KB' || $rawKondisi === 'KURANG BAIK') ? 'Kurang Baik' : (($rawKondisi === 'RB' || $rawKondisi === 'RUSAK BERAT' || $rawKondisi === 'RUSAK') ? 'Rusak Berat' : 'Baik');
                    $itemRuang = $aItem['atb_ruang_pemegang'] ?? ($data['ruang_pemegang_atb'] ?? ($data['ruang_pemegang'] ?? null));
                    for ($q = 0; $q < $qty; $q++) {
                        $targetUnits[] = [
                            'kondisi' => $kondisiStr,
                            'ruang_pemegang' => $itemRuang
                        ];
                    }
                }

                $targetCount = count($targetUnits);
                $existingRegs = $astap->registers()->orderBy('id')->get();
                $existingCount = $existingRegs->count();

                $kode108Clean = '153000000000';
                if ($jenisAstapRecord && !empty($jenisAstapRecord->sub_sub_rincian_objek)) {
                    $kode108Clean = str_replace('.', '', $jenisAstapRecord->sub_sub_rincian_objek);
                } elseif (!empty($data['atb_kode_barang'])) {
                    $kode108Clean = str_replace('.', '', $data['atb_kode_barang']);
                } elseif (!empty($data['atb_items'][0]['atb_kode_barang'])) {
                    $kode108Clean = str_replace('.', '', $data['atb_items'][0]['atb_kode_barang']);
                }

                $tahun = $astap->tahun_perolehan;

                $maxRegInt = \App\Models\AstapRegister::where('tahun_perolehan', $tahun)
                    ->where('astap_id', '!=', $astap->id)
                    ->where(function($q) use ($astap) {
                        if ($astap->jenis_astap_id) {
                            $q->whereHas('astap', fn($sq) => $sq->where('jenis_astap_id', $astap->jenis_astap_id));
                        }
                    })
                    ->max('no_register_int') ?? 0;
                $runningRegNum = (int) $maxRegInt;

                foreach ($targetUnits as $itemIdx => $uUnit) {
                    $runningRegNum++;
                    $noRegStr = str_pad($runningRegNum, 7, '0', STR_PAD_LEFT);
                    $nibar = "1201351102000000280000{$tahun}{$kode108Clean}{$noRegStr}";

                    if ($itemIdx < $existingCount) {
                        $reg = $existingRegs[$itemIdx];
                        while (\App\Models\AstapRegister::where('nibar', $nibar)->where('id', '!=', $reg->id)->exists()) {
                            $runningRegNum++;
                            $noRegStr = str_pad($runningRegNum, 7, '0', STR_PAD_LEFT);
                            $nibar = "1201351102000000280000{$tahun}{$kode108Clean}{$noRegStr}";
                        }
                        $reg->tahun_perolehan = $tahun;
                        $reg->no_register_int = $runningRegNum;
                        $reg->no_register = $nibar;
                        $reg->nibar = $nibar;
                        $reg->qr_code_path = "/scan/{$nibar}";
                        $reg->kondisi = $uUnit['kondisi'];
                        $reg->ruang_pemegang = $uUnit['ruang_pemegang'];
                        $reg->save();
                    } else {
                        while (\App\Models\AstapRegister::where('nibar', $nibar)->exists()) {
                            $runningRegNum++;
                            $noRegStr = str_pad($runningRegNum, 7, '0', STR_PAD_LEFT);
                            $nibar = "1201351102000000280000{$tahun}{$kode108Clean}{$noRegStr}";
                        }

                        $qrPath = "/scan/{$nibar}";
                        \App\Models\AstapRegister::create([
                            'astap_id' => $astap->id,
                            'tahun_perolehan' => $tahun,
                            'no_register_int' => $runningRegNum,
                            'no_register' => $nibar,
                            'nibar' => $nibar,
                            'qr_code_path' => $qrPath,
                            'ruang_pemegang' => $uUnit['ruang_pemegang'],
                            'kondisi' => $uUnit['kondisi'],
                            'status' => 'Tersedia'
                        ]);
                    }
                }

                if ($existingCount > $targetCount) {
                    for ($k = $targetCount; $k < $existingCount; $k++) {
                        $existingRegs[$k]->delete();
                    }
                }
            } else {
                // Untuk single item: sync register unit sesuai jumlah volume
                $targetCount = max(1, (int)$astap->jumlah_volume);
                $existingRegs = $astap->registers()->orderBy('id')->get();
                $existingCount = $existingRegs->count();

                $kode108Clean = '130000000000';
                if ($jenisAstapRecord && !empty($jenisAstapRecord->sub_sub_rincian_objek)) {
                    $kode108Clean = str_replace('.', '', $jenisAstapRecord->sub_sub_rincian_objek);
                } elseif (!empty($kode108Submitted)) {
                    $kode108Clean = str_replace('.', '', $kode108Submitted);
                }

                $tahun = $astap->tahun_perolehan;
                $ruangSingle = $data['ruang_pemegang'] ?? ($data['ruang_pemegang_mesin'] ?? ($data['ruang_pemegang_lainnya'] ?? ($data['ruang_pemegang_atb'] ?? null)));
                $rawKondisi = strtoupper(trim((string)($data['kondisi'] ?? ($data['mesin_kondisi'] ?? ($data['gedung_kondisi'] ?? ($data['tanah_kondisi'] ?? ($data['jaringan_kondisi'] ?? ($data['lainnya_kondisi'] ?? ($data['atb_kondisi'] ?? ($data['kdp_kondisi'] ?? 'Baik'))))))))));
                $kondisiStr = ($rawKondisi === 'KB' || $rawKondisi === 'KURANG BAIK') ? 'Kurang Baik' : (($rawKondisi === 'RB' || $rawKondisi === 'RUSAK BERAT' || $rawKondisi === 'RUSAK') ? 'Rusak Berat' : (($rawKondisi === 'RR' || $rawKondisi === 'RUSAK RINGAN') ? 'Rusak Ringan' : 'Baik'));

                $maxRegInt = \App\Models\AstapRegister::where('tahun_perolehan', $tahun)
                    ->where('astap_id', '!=', $astap->id)
                    ->where(function($q) use ($astap) {
                        if ($astap->jenis_astap_id) {
                            $q->whereHas('astap', fn($sq) => $sq->where('jenis_astap_id', $astap->jenis_astap_id));
                        }
                    })
                    ->max('no_register_int') ?? 0;
                $runningRegNum = (int) $maxRegInt;

                for ($u = 0; $u < $targetCount; $u++) {
                    $runningRegNum++;
                    $noRegStr = str_pad($runningRegNum, 7, '0', STR_PAD_LEFT);
                    $nibar = "1201351102000000280000{$tahun}{$kode108Clean}{$noRegStr}";

                    if ($u < $existingCount) {
                        $reg = $existingRegs[$u];
                        while (\App\Models\AstapRegister::where('nibar', $nibar)->where('id', '!=', $reg->id)->exists()) {
                            $runningRegNum++;
                            $noRegStr = str_pad($runningRegNum, 7, '0', STR_PAD_LEFT);
                            $nibar = "1201351102000000280000{$tahun}{$kode108Clean}{$noRegStr}";
                        }
                        $reg->tahun_perolehan = $tahun;
                        $reg->no_register_int = $runningRegNum;
                        $reg->no_register = $nibar;
                        $reg->nibar = $nibar;
                        $reg->qr_code_path = "/scan/{$nibar}";
                        $reg->ruang_pemegang = $ruangSingle;
                        $reg->kondisi = $kondisiStr;
                        $reg->save();
                    } else {
                        while (\App\Models\AstapRegister::where('nibar', $nibar)->exists()) {
                            $runningRegNum++;
                            $noRegStr = str_pad($runningRegNum, 7, '0', STR_PAD_LEFT);
                            $nibar = "1201351102000000280000{$tahun}{$kode108Clean}{$noRegStr}";
                        }
                        \App\Models\AstapRegister::create([
                            'astap_id' => $astap->id,
                            'tahun_perolehan' => $tahun,
                            'no_register_int' => $runningRegNum,
                            'no_register' => $nibar,
                            'nibar' => $nibar,
                            'qr_code_path' => "/scan/{$nibar}",
                            'ruang_pemegang' => $ruangSingle,
                            'kondisi' => $kondisiStr,
                            'status' => 'Tersedia'
                        ]);
                    }
                }

                if ($existingCount > $targetCount) {
                    for ($k = $targetCount; $k < $existingCount; $k++) {
                        $existingRegs[$k]->delete();
                    }
                }
            }

            // Kirim Notifikasi Sistem ke Admin & Super Admin saat Terjadi Perubahan ASTAP
            try {
                \App\Services\NotificationService::sendToAdminAndMaster(
                    "Aset Diperbarui: {$astap->nama_barang}",
                    "{$astap->jumlah_volume} {$astap->satuan} • " . ($astap->tahun_perolehan ?: date('Y')),
                    'astap',
                    route('astap.index')
                );
            } catch (\Throwable $e) {
                \Log::warning("Gagal kirim notif astap update: " . $e->getMessage());
            }

            session()->flash('success', 'Data ASTAP "' . ($astap->nama_barang ?? 'Aset Tetap') . '" berhasil diperbarui.');
            return response()->json(['success' => true, 'message' => 'Data ASTAP berhasil diperbarui!']);
        })->name('astap.update');

        Route::delete('/astap/{id}', function ($id) {
            $astap = \App\Models\Astap::find($id);
            if ($astap) {
                $namaBarang = $astap->nama_barang ?? 'Aset Tetap';
                $tahun = $astap->tahun_perolehan ?: date('Y');
                $vol = $astap->jumlah_volume . ' ' . ($astap->satuan ?: 'Unit');

                $astap->registers()->delete();
                $astap->delete();

                // Kirim Notifikasi Sistem saat Terjadi Penghapusan ASTAP
                try {
                    \App\Services\NotificationService::sendToAdminAndMaster(
                        "Aset Dihapus: {$namaBarang}",
                        "{$vol} • {$tahun}",
                        'astap',
                        route('astap.index')
                    );
                } catch (\Throwable $e) {
                    \Log::warning("Gagal kirim notif astap delete: " . $e->getMessage());
                }
            }
            session()->flash('success', 'Data ASTAP berhasil dihapus.');
            return response()->json(['success' => true, 'message' => 'Data ASTAP berhasil dihapus.']);
        })->name('astap.destroy');

        // Route Update & Delete Register ASTAP (NIBAR Per-Unit)
        Route::put('/astap-register/{id}', function (\Illuminate\Http\Request $request, $id) {
            $reg = \App\Models\AstapRegister::with('astap')->find($id);
            if (!$reg) {
                return response()->json(['success' => false, 'message' => 'Register tidak ditemukan.'], 404);
            }
            $data = $request->all();
            if (isset($data['ruang_pemegang'])) $reg->ruang_pemegang = $data['ruang_pemegang'];
            if (isset($data['kondisi']) && in_array($data['kondisi'], ['Baik', 'Kurang Baik', 'Rusak Ringan', 'Rusak Berat'])) {
                $reg->kondisi = $data['kondisi'];
            }
            if (isset($data['unit_id'])) $reg->unit_id = $data['unit_id'];
            $reg->save();

            return response()->json(['success' => true, 'message' => 'Data register NIBAR berhasil diperbarui!']);
        })->name('astap_register.update');

        Route::delete('/astap-register/{id}', function ($id) {
            $reg = \App\Models\AstapRegister::with('astap')->find($id);
            if ($reg) {
                $nibar = $reg->nibar ?: $reg->no_register;
                $nama = $reg->astap?->nama_barang ?? 'Aset ASTAP';
                $reg->delete();

                // Kirim Notifikasi Sistem saat Unit NIBAR Dihapus
                try {
                    \App\Services\NotificationService::sendToAdminAndMaster(
                        "Unit Register Dihapus: {$nibar}",
                        "{$nama} • Register dihapus",
                        'astap',
                        route('astap.index')
                    );
                } catch (\Throwable $e) {
                    \Log::warning("Gagal kirim notif register delete: " . $e->getMessage());
                }
            }
            session()->flash('success', 'Unit register NIBAR berhasil dihapus.');
            return response()->json(['success' => true, 'message' => 'Unit register berhasil dihapus.']);
        })->name('astap_register.destroy');

        // Route Khusus: Rapikan / Urutkan Ulang NIBAR (Auto-Resequence)
        Route::post('/astap/resequence-nibar', function (\Illuminate\Http\Request $request) {
            $tahun = $request->input('tahun', 'all');
            $category = $request->input('category', 'all');
            $astapId = $request->input('astap_id');

            $query = \App\Models\Astap::with(['registers' => fn($q) => $q->orderBy('id', 'asc'), 'jenisAstap']);

            if ($astapId) {
                $target = \App\Models\Astap::find($astapId);
                if (!$target) {
                    return response()->json(['success' => false, 'message' => 'Data ASTAP tidak ditemukan.'], 404);
                }
                $query->where('tahun_perolehan', $target->tahun_perolehan);
                if ($target->jenis_astap_id) {
                    $query->where('jenis_astap_id', $target->jenis_astap_id);
                } else {
                    $query->where('id', $target->id);
                }
            } else {
                if ($tahun !== 'all' && !empty($tahun)) {
                    $query->where('tahun_perolehan', $tahun);
                }
                if ($category !== 'all' && !empty($category)) {
                    $prefix = match($category) {
                        'KIB A' => '1.3.1',
                        'KIB B' => '1.3.2',
                        'KIB C' => '1.3.3',
                        'KIB D' => '1.3.4',
                        'KIB E' => '1.3.5',
                        'KIB F' => '1.3.6',
                        'ATB'   => '1.5.3',
                        default => null
                    };
                    if ($prefix) {
                        $query->whereHas('jenisAstap', function($q) use ($prefix) {
                            $q->where('kode_kelompok', 'like', $prefix . '%')
                              ->orWhere('kode', 'like', $prefix . '%')
                              ->orWhere('sub_sub_rincian_objek', 'like', $prefix . '%');
                        });
                    }
                }
            }

            $allAstaps = $query->get();
            if ($allAstaps->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Tidak ada data register pada filter yang dipilih.',
                    'count' => 0
                ]);
            }

            // Group ASTAPs by (tahun_perolehan, jenis_astap_id)
            $grouped = $allAstaps->groupBy(function($item) {
                return ($item->tahun_perolehan ?? '2026') . '_' . ($item->jenis_astap_id ?? 0);
            });

            $twOrder = ['TW I' => 1, 'TW 1' => 1, 'TW II' => 2, 'TW 2' => 2, 'TW III' => 3, 'TW 3' => 3, 'TW IV' => 4, 'TW 4' => 4];
            $totalUpdated = 0;

            \Illuminate\Support\Facades\DB::transaction(function() use ($grouped, $twOrder, &$totalUpdated) {
                foreach ($grouped as $key => $astapsInGroup) {
                    // Urutkan ASTAP berdasarkan Triwulan (TW I -> TW IV), lalu created_at, lalu id
                    $sortedAstaps = $astapsInGroup->sort(function($a, $b) use ($twOrder) {
                        $twA = $twOrder[$a->triwulan ?? 'TW I'] ?? 1;
                        $twB = $twOrder[$b->triwulan ?? 'TW I'] ?? 1;
                        if ($twA !== $twB) return $twA <=> $twB;
                        
                        $tA = $a->created_at ? $a->created_at->timestamp : $a->id;
                        $tB = $b->created_at ? $b->created_at->timestamp : $b->id;
                        if ($tA !== $tB) return $tA <=> $tB;

                        return $a->id <=> $b->id;
                    });

                    // 1. Berikan prefix temporer unik untuk menghindari tabrakan unique constraint
                    $allRegs = [];
                    foreach ($sortedAstaps as $astap) {
                        foreach ($astap->registers as $reg) {
                            $allRegs[] = ['reg' => $reg, 'astap' => $astap];
                            $reg->nibar = 'TEMP_' . $reg->id . '_' . uniqid();
                            $reg->no_register = $reg->nibar;
                            $reg->save();
                        }
                    }

                    // 2. Berikan nomor urut register murni berurutan tanpa celah dari 1
                    $runningNum = 0;
                    foreach ($allRegs as $item) {
                        $reg = $item['reg'];
                        $astap = $item['astap'];
                        $tahun = $astap->tahun_perolehan ?? '2026';

                        $kode108Clean = '132000000000';
                        if ($astap->jenisAstap && !empty($astap->jenisAstap->sub_sub_rincian_objek)) {
                            $kode108Clean = str_replace('.', '', $astap->jenisAstap->sub_sub_rincian_objek);
                        } elseif (!empty($astap->kode_barang)) {
                            $kode108Clean = str_replace('.', '', $astap->kode_barang);
                        }

                        $runningNum++;
                        $noRegStr = str_pad($runningNum, 7, '0', STR_PAD_LEFT);
                        $finalNibar = "1201351102000000280000{$tahun}{$kode108Clean}{$noRegStr}";

                        $reg->tahun_perolehan = $tahun;
                        $reg->no_register_int = $runningNum;
                        $reg->no_register = $finalNibar;
                        $reg->nibar = $finalNibar;
                        $reg->qr_code_path = "/scan/{$finalNibar}";
                        $reg->save();
                        $totalUpdated++;
                    }
                }
            });

            return response()->json([
                'success' => true,
                'message' => "Berhasil menyusun dan merapikan {$totalUpdated} unit register NIBAR secara berurutan tanpa celah.",
                'count' => $totalUpdated
            ]);
        })->name('astap.resequence_nibar');

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

