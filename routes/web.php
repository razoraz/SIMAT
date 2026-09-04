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
                'unit'                => null,
                'user'                => $user,
                'distribusisList'     => [],
                'totalAsetCount'      => 0,
                'totalNilaiFormatted' => 'Rp 0',
                'kondisiBaik'         => 0,
                'kondisiKurangBaik'   => 0,
                'kondisiRusakRingan'  => 0,
                'kondisiRusakBerat'   => 0,
                'totalRusak'          => 0,
                'attentionAssets'     => [],
                'unitNama'            => '',
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

        // 2. Data Register Aset di Ruangan Ini — HANYA berdasarkan unit_id (ketat, tidak pakai OR)
        $registers = \App\Models\AstapRegister::with(['astap.jenisAstap'])
            ->where('unit_id', $unitId)
            ->get();

        $totalAsetCount = $registers->count();
        $totalNilaiNum = $registers->sum(fn($r) => $r->astap ? (float) ($r->astap->harga_satuan ?: ($r->astap->total_realisasi / max(1, $r->astap->jumlah_volume))) : 0);
        $totalNilaiFormatted = 'Rp ' . number_format($totalNilaiNum, 0, ',', '.');

        $kondisiBaik = $registers->where('kondisi', 'Baik')->count();
        $kondisiKurangBaik = $registers->where('kondisi', 'Kurang Baik')->count();
        $kondisiRusakRingan = $registers->where('kondisi', 'Rusak Ringan')->count();
        $kondisiRusakBerat = $registers->whereIn('kondisi', ['Rusak Berat', 'Rusak'])->count();
        $totalRusak = $kondisiKurangBaik + $kondisiRusakRingan + $kondisiRusakBerat;

        // 3. Aset yang perlu perhatian / rusak di ruangan ini
        $attentionAssets = $registers->filter(fn($r) => $r->kondisi !== 'Baik')->map(function($r) {
            return [
                'id'      => $r->id,
                'kode'    => $r->nibar ?: $r->no_register,
                'nama'    => $r->astap?->nama_barang ?? 'Barang Inventaris',
                'status'  => $r->kondisi,
                'lokasi'  => $r->ruang_pemegang ?: 'Ruangan',
                'catatan' => 'Kondisi fisik unit tercatat: ' . $r->kondisi . ' (Perlu pengecekan berkala / servis)'
            ];
        })->values()->all();

        return view('dashboards.sub_admin', [
            'unit'                => $unit,
            'user'                => $user,
            'distribusisList'     => $distribusisList,
            'totalAsetCount'      => $totalAsetCount,
            'totalNilaiFormatted' => $totalNilaiFormatted,
            'kondisiBaik'         => $kondisiBaik,
            'kondisiKurangBaik'   => $kondisiKurangBaik,
            'kondisiRusakRingan'  => $kondisiRusakRingan,
            'kondisiRusakBerat'   => $kondisiRusakBerat,
            'totalRusak'          => $totalRusak,
            'attentionAssets'     => $attentionAssets,
            'unitNama'            => $unitNama,
        ]);
    })->name('subadmin.dashboard');
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
                    'spk_tanggal' => $a->spk_tanggal ? $a->spk_tanggal->format('Y-m-d') : ($spec['spk_tanggal'] ?? '-'),
                    'surat_pesanan_nomor' => $a->surat_pesanan_nomor ?: ($spec['surat_pesanan_nomor'] ?? '-'),
                    'surat_pesanan_tanggal' => $a->surat_pesanan_tanggal ? $a->surat_pesanan_tanggal->format('Y-m-d') : ($spec['surat_pesanan_tanggal'] ?? '-'),
                    'kwitansi_nomor' => $a->kwitansi_nomor ?: ($spec['kwitansi_nomor'] ?? '-'),
                    'kwitansi_tanggal' => $a->kwitansi_tanggal ? $a->kwitansi_tanggal->format('Y-m-d') : ($spec['kwitansi_tanggal'] ?? '-'),
                    'faktur_nomor' => $a->faktur_nomor ?: ($spec['faktur_nomor'] ?? ($spec['invoice_nomor'] ?? '-')),
                    'faktur_tanggal' => $a->faktur_tanggal ? $a->faktur_tanggal->format('Y-m-d') : ($spec['faktur_tanggal'] ?? ($spec['invoice_tanggal'] ?? '-')),
                    'sp2d_nomor' => $a->sp2d_nomor ?: ($spec['sp2d_nomor'] ?? '-'),
                    'sp2d_tanggal' => $a->sp2d_tanggal ? $a->sp2d_tanggal->format('Y-m-d') : ($spec['sp2d_tanggal'] ?? '-'),
                    'bast_dokumen_nomor' => $a->bast_dokumen_nomor ?: ($spec['bast_dokumen_nomor'] ?? '-'),
                    'bast_dokumen_tanggal' => $a->bast_dokumen_tanggal ? $a->bast_dokumen_tanggal->format('Y-m-d') : ($spec['bast_dokumen_tanggal'] ?? '-'),
                    
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

                    // Rincian Jalan, Irigasi & Jaringan (KIB D)
                    'jaringan_konstruksi' => $spec['konstruksi'] ?? ($spec['jaringan_konstruksi'] ?? '-'),
                    'jaringan_panjang_m' => (float) ($spec['panjang_m'] ?? ($spec['jaringan_panjang_m'] ?? 0)),
                    'jaringan_lebar_m' => (float) ($spec['lebar_m'] ?? ($spec['jaringan_lebar_m'] ?? 0)),
                    'jaringan_luas_m2' => (float) ($spec['luas_m2'] ?? ($spec['jaringan_luas_m2'] ?? 0)),
                    'jaringan_status_tanah' => $spec['status_tanah'] ?? ($spec['jaringan_status_tanah'] ?? 'Tanah Hak Pakai RSUD'),
                    'jaringan_kode_aset_tanah' => $spec['kode_tanah'] ?? ($spec['jaringan_kode_aset_tanah'] ?? '-'),
                    'jaringan_nilai_perencanaan' => (float) ($spec['nilai_perencanaan'] ?? ($spec['jaringan_nilai_perencanaan'] ?? 0)),
                    'jaringan_nilai_fisik' => (float) ($spec['nilai_fisik'] ?? ($spec['jaringan_nilai_fisik'] ?? $a->total_realisasi)),
                    'jaringan_nilai_pengawasan' => (float) ($spec['nilai_pengawasan'] ?? ($spec['jaringan_nilai_pengawasan'] ?? 0)),

                    // Rincian KIB E, F, ATB & EXTRACOM
                    'judul_pencipta' => $spec['judul'] ?? ($spec['pencipta'] ?? ($spec['buku_judul'] ?? ($spec['judul_lisensi'] ?? '-'))),
                    'spesifikasi' => $spec['spesifikasi'] ?? ($spec['buku_spesifikasi'] ?? ($spec['spesifikasi_lisensi'] ?? '-')),
                    'asal_kesenian' => $spec['asal_kesenian'] ?? ($spec['daerah_asal'] ?? ($spec['penerbit'] ?? '-')),
                    'progres_fisik' => $spec['progres_fisik'] ?? ($spec['capaian_fisik'] ?? '100%'),

                    // LANGKAH 4 (REKANAN, PPK, KETERANGAN)
                    'alamat_barang' => $a->alamat_barang ?: ($spec['alamat_barang'] ?? 'RSUD Dr. H. Koesnandi'),
                    'penyedia_nama' => $a->penyedia_nama ?: ($spec['penyedia_nama'] ?? '-'),
                    'penyedia_pemilik' => $a->penyedia_pemilik ?: ($spec['penyedia_pemilik'] ?? '-'),
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
                    })->values() : []
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

        if ($newStatus === 'Telah Diterima') {
            $dst->signed = true;
            if (!$dst->tgl_signed) {
                $dst->tgl_signed = now()->format('d/m/Y H:i') . ' WIB';
            }
        }

        if ($newStatus === 'Ditolak') {
            // Reset tanda tangan digital
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
            'success' => true,
            'status'  => $dst->status,
            'message' => "Status distribusi {$dst->kode} berhasil diperbarui menjadi '{$dst->status}'."
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

    // 5. Unit & Paviliun Index (Read-only for Sub Admin, full for Admin)
    Route::get('/unit-paviliun', [UnitController::class, 'index'])->name('unit.index');

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

            $namaInput = match(true) {
                $jenisPrefix === '1.3.1' => $data['tanah_nama_barang'] ?? null,
                $jenisPrefix === '1.3.2' => $data['mesin_nama_barang'] ?? null,
                $jenisPrefix === '1.3.3' => $data['gedung_nama_barang'] ?? null,
                $jenisPrefix === '1.3.4' => $data['jaringan_nama_barang'] ?? null,
                $jenisPrefix === '1.3.5' => $data['lainnya_nama_barang'] ?? null,
                $jenisPrefix === '1.5.3' => $data['atb_nama_barang'] ?? null,
                $jenisPrefix === '1.3.6' => $data['kdp_nama_barang'] ?? null,
                default => null
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
                
                $hargaSatuan = (float) match(true) {
                    $jenisPrefix === '1.3.2' => $data['mesin_nilai_satuan'] ?? ($data['harga_satuan'] ?? 0),
                    $jenisPrefix === '1.3.5' => $data['lainnya_nilai_satuan'] ?? ($data['harga_satuan'] ?? 0),
                    $jenisPrefix === '1.5.3' => $data['atb_nilai_satuan'] ?? ($data['harga_satuan'] ?? 0),
                    default => ($totalRealisasi > 0 && $volume > 0) ? ($totalRealisasi / $volume) : ($data['harga_satuan'] ?? 0)
                };

                $biayaAdm = (float) ($data['biaya_administrasi_proyek'] ?? ($data['mesin_administrasi_proyek'] ?? ($data['lainnya_administrasi_proyek'] ?? ($data['atb_administrasi_proyek'] ?? 0))));

                // Extracom: harga < 300rb atau flag manual
                $isExtracom = !empty($data['is_extracomtable']) || ($jenisPrefix === '1.3.2' && $hargaSatuan > 0 && $hargaSatuan < 300000);

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
                    'kondisi' => $data['tanah_kondisi'] ?? ($data['mesin_kondisi'] ?? ($data['gedung_kondisi'] ?? ($data['jaringan_kondisi'] ?? ($data['kdp_kondisi'] ?? null)))),
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
                    'buku_judul' => $data['lainnya_buku_judul'] ?? null,
                    'buku_pencipta' => $data['lainnya_buku_pencipta'] ?? null,
                    'buku_spesifikasi' => $data['lainnya_buku_spesifikasi'] ?? null,
                    'kesenian_asal' => $data['lainnya_kesenian_asal'] ?? null,
                    'kesenian_pencipta' => $data['lainnya_kesenian_pencipta'] ?? null,
                    'kesenian_spesifikasi' => $data['lainnya_kesenian_spesifikasi'] ?? null,
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
                ];

                $specJson = array_filter($specJson, fn($v) => !is_null($v) && $v !== '');

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
                    'ruang_pemegang' => $data['ruang_pemegang'] ?? null,
                    'kondisi' => in_array($data['kondisi'] ?? '', ['Baik', 'Rusak Ringan', 'Rusak Berat']) ? $data['kondisi'] : 'Baik',
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
                
                $hrgSat = (float) match(true) {
                    $prefix === '1.3.2' => $d['mesin_nilai_satuan'] ?? ($d['harga_satuan'] ?? 0),
                    $prefix === '1.3.5' => $d['lainnya_nilai_satuan'] ?? ($d['harga_satuan'] ?? 0),
                    $prefix === '1.5.3' => $d['atb_nilai_satuan'] ?? ($d['harga_satuan'] ?? 0),
                    default => ($totReal > 0 && $vol > 0) ? ($totReal / $vol) : ($d['harga_satuan'] ?? 0)
                };

                $biaya = (float) ($d['biaya_administrasi_proyek'] ?? ($d['mesin_administrasi_proyek'] ?? ($d['lainnya_administrasi_proyek'] ?? ($d['atb_administrasi_proyek'] ?? 0))));

                $extracom = !empty($d['is_extracomtable']) || ($prefix === '1.3.2' && $hrgSat > 0 && $hrgSat < 300000);

                $spec = [
                    'jumlah_anggaran' => $d['jumlah_anggaran'] ?? null,
                    'luas_m2' => $d['tanah_luas_m2'] ?? ($d['gedung_luas_m2'] ?? ($d['jaringan_luas_m2'] ?? ($d['kdp_luas_m2'] ?? null))),
                    'hak_tanah' => $d['tanah_hak'] ?? null,
                    'sertifikat_no' => $d['tanah_sertifikat_no'] ?? ($d['kdp_sertifikat_no'] ?? null),
                    'sertifikat_tgl' => $d['tanah_sertifikat_tgl'] ?? ($d['kdp_sertifikat_tgl'] ?? null),
                    'penggunaan' => $d['tanah_penggunaan'] ?? null,
                    'nilai_perencanaan' => $d['tanah_nilai_perencanaan'] ?? ($d['gedung_nilai_perencanaan'] ?? ($d['jaringan_nilai_perencanaan'] ?? ($d['kdp_nilai_perencanaan'] ?? 0))),
                    'nilai_pengawasan' => $d['tanah_nilai_pengawasan'] ?? ($d['gedung_nilai_pengawasan'] ?? ($d['jaringan_nilai_pengawasan'] ?? ($d['kdp_nilai_pengawasan'] ?? 0))),
                    'nilai_pip' => $d['gedung_nilai_pip'] ?? ($d['jaringan_nilai_pip'] ?? ($d['kdp_nilai_pip'] ?? 0)),
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
                    'buku_judul' => $d['lainnya_buku_judul'] ?? null,
                    'buku_pencipta' => $d['lainnya_buku_pencipta'] ?? null,
                    'buku_spesifikasi' => $d['lainnya_buku_spesifikasi'] ?? null,
                    'kesenian_asal' => $d['lainnya_kesenian_asal'] ?? null,
                    'kesenian_pencipta' => $d['lainnya_kesenian_pencipta'] ?? null,
                    'kesenian_spesifikasi' => $d['lainnya_kesenian_spesifikasi'] ?? null,
                    'hewan_jenis' => $d['lainnya_hewan_jenis'] ?? null,
                    'hewan_spesifikasi' => $d['lainnya_hewan_spesifikasi'] ?? null,
                    'atb_judul' => $d['atb_judul'] ?? null,
                    'atb_pencipta' => $d['atb_pencipta'] ?? null,
                    'atb_jenis_lisensi' => $d['atb_jenis_lisensi'] ?? null,
                    'atb_spesifikasi' => $d['atb_spesifikasi'] ?? null,
                    'progres_persen' => $d['kdp_progres_persen'] ?? null,
                    'tgl_mulai' => $d['kdp_tgl_mulai'] ?? null,
                    'tgl_target_selesai' => $d['kdp_tgl_target_selesai'] ?? null,
                ];
                $spec = array_filter($spec, fn($v) => !is_null($v) && $v !== '');

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

            $ext = $extractAstapPayload($data, $jenisPrefix);

            $namaInput = match(true) {
                $jenisPrefix === '1.3.1' => $data['tanah_nama_barang'] ?? null,
                $jenisPrefix === '1.3.2' => $data['mesin_nama_barang'] ?? null,
                $jenisPrefix === '1.3.3' => $data['gedung_nama_barang'] ?? null,
                $jenisPrefix === '1.3.4' => $data['jaringan_nama_barang'] ?? null,
                $jenisPrefix === '1.3.5' => $data['lainnya_nama_barang'] ?? null,
                $jenisPrefix === '1.5.3' => $data['atb_nama_barang'] ?? null,
                $jenisPrefix === '1.3.6' => $data['kdp_nama_barang'] ?? null,
                default => null
            };

            if ($jenisAstapRecord && !empty($jenisAstapRecord->uraian_sub_sub_rincian)) {
                $astap->nama_barang = $jenisAstapRecord->uraian_sub_sub_rincian;
            } elseif ($namaInput) {
                $astap->nama_barang = $namaInput;
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

            session()->flash('success', 'Data ASTAP "' . ($astap->nama_barang ?? 'Aset Tetap') . '" berhasil diperbarui.');
            return response()->json(['success' => true, 'message' => 'Data ASTAP berhasil diperbarui!']);
        })->name('astap.update');

        Route::delete('/astap/{id}', function ($id) {
            $astap = \App\Models\Astap::find($id);
            if ($astap) {
                $astap->registers()->delete();
                $astap->delete();
            }
            session()->flash('success', 'Data ASTAP berhasil dihapus.');
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
            if (isset($data['kondisi']) && in_array($data['kondisi'], ['Baik', 'Kurang Baik', 'Rusak Ringan', 'Rusak Berat'])) {
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
            session()->flash('success', 'Unit register NIBAR berhasil dihapus.');
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
