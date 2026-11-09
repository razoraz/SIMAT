<?php

namespace App\Http\Controllers;

use App\Models\Astap;
use App\Models\PeriodeTutupBuku;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TutupBukuController extends Controller
{
    /**
     * Halaman Khusus Manajemen Tutup Buku BMD (Triwulanan & Tahunan)
     */
    public function index(Request $request)
    {
        $currentYear = (int) ($request->input('tahun', date('Y')));
        $currentTab = $request->input('tab', 'triwulan');
        $actualYear = (int) date('Y');
        $actualMonth = (int) date('n');
        $actualQuarter = (int) ceil($actualMonth / 3);

        // Ambil data semua aset non-deleted beserta seluruh relasi sumber perolehan
        $astaps = Astap::where('is_deleted', 0)
            ->with([
                'registers' => function($q) { 
                    $q->where('is_deleted', 0)->with('unit'); 
                },
                'jenisAstap',
                'rekeningBelanja',
                'jenisPengadaan',
                'unit',
                'reklas' => function($q) {
                    $q->orderBy('id', 'desc');
                },
                'kemitraan',
                'belanjaModal',
                'belanjaBarang',
                'hibahDetail',
                'hibahs',
                'mutasiEksternal',
                'mutasiEksternalMasuk',
                'mutasiEksternals' => function($q) {
                    $q->where('is_deleted', 0)->orderBy('id', 'desc');
                }
            ])
            ->orderBy('id', 'desc')
            ->get();

        // 1. Matriks Triwulan Tahun Berjalan (TW 1 s/d TW 4)
        $triwulanDefs = [
            1 => ['nama' => 'Triwulan I', 'rentang' => 'Januari - Maret', 'badge' => 'TW I', 'bulan_akhir' => 3],
            2 => ['nama' => 'Triwulan II', 'rentang' => 'April - Juni', 'badge' => 'TW II', 'bulan_akhir' => 6],
            3 => ['nama' => 'Triwulan III', 'rentang' => 'Juli - September', 'badge' => 'TW III', 'bulan_akhir' => 9],
            4 => ['nama' => 'Triwulan IV', 'rentang' => 'Oktober - Desember', 'badge' => 'TW IV', 'bulan_akhir' => 12],
        ];

        // Status DB jika ada catatan kunci manual
        $lockedRecords = PeriodeTutupBuku::where('tahun', $currentYear)->get()->keyBy('triwulan');
        $annualRecord = $lockedRecords->get(0);
        $isAnnualLocked = ($currentYear < $actualYear) || (bool) ($annualRecord?->is_locked ?? false);

        $triwulanData = [];
        foreach ($triwulanDefs as $tw => $def) {
            // Tutup buku sekarang berbasis per tahun:
            // Triwulan di tahun berjalan terbuka (tidak dikunci otomatis berdasarkan bulan).
            // Triwulan hanya terkunci jika:
            // 1. Tahun tersebut merupakan tahun lampau ($currentYear < $actualYear) ATAU Tutup Buku Tahunan terkunci ($isAnnualLocked)
            // 2. ATAU ada catatan kunci manual di tabel periode_tutup_bukus untuk triwulan tersebut
            $manualRecord = $lockedRecords->get($tw);
            $isLocked = $isAnnualLocked || (bool) ($manualRecord?->is_locked ?? false);
            $isActive = ($currentYear == $actualYear && $tw == $actualQuarter);
            $isFuture = ($currentYear > $actualYear) || ($currentYear == $actualYear && $tw > $actualQuarter);

            // Filter aset untuk triwulan ini
            $itemsTw = $astaps->filter(function($item) use ($currentYear, $tw) {
                $itemYear = (int) ($item->tahun_perolehan ?: ($item->kemitraan?->tahun ?? 0));
                if ($itemYear !== $currentYear) return false;
                $itemTw = PeriodeTutupBuku::parseTriwulan($item->triwulan);
                return $itemTw === $tw;
            });

            $totalItem = $itemsTw->count();
            $totalVolume = $itemsTw->sum(fn($i) => (int) ($i->jumlah_volume ?: 1));
            $totalNominal = $itemsTw->sum(fn($i) => (float) ($i->total_realisasi ?: 0));

            $triwulanData[] = [
                'triwulan'                => $tw,
                'nama'                    => $def['nama'],
                'rentang'                 => $def['rentang'],
                'badge'                   => $def['badge'],
                'is_locked'               => $isLocked,
                'is_auto_passed'          => false,
                'is_active'               => $isActive,
                'is_future'               => $isFuture,
                'status_label'            => $isLocked ? 'Ditutup Buku' : ($isActive ? 'Sedang Berjalan' : ($currentYear == $actualYear ? 'Terbuka' : 'Belum Berjalan')),
                'total_item'              => $totalItem,
                'total_volume'            => $totalVolume,
                'total_nominal'           => $totalNominal,
                'total_nominal_formatted' => 'Rp ' . number_format($totalNominal, 0, ',', '.'),
                'nomor_bar'               => $manualRecord?->nomor_bar_bpkad ?: ($isAnnualLocked ? ($annualRecord?->nomor_bar_bpkad ?: 'Tutup Buku Tahunan') : '-'),
                'tanggal_tutup'           => $manualRecord?->tanggal_tutup?->format('d/m/Y') ?: ($isAnnualLocked ? ($annualRecord?->tanggal_tutup?->format('d/m/Y') ?: 'Tutup Buku Tahunan') : '-'),
            ];
        }

        // 2. Tutup Buku Tahunan (Arsip Tahun-Tahun Sebelumnya)
        $distinctYears = $astaps->pluck('tahun_perolehan')
            ->filter()
            ->map(fn($y) => (int)$y)
            ->unique()
            ->filter(fn($y) => $y > 2000)
            ->sortDesc()
            ->values();

        if (!$distinctYears->contains($actualYear)) {
            $distinctYears->prepend($actualYear);
            $distinctYears = $distinctYears->sortDesc()->values();
        }

        $tahunanData = [];
        foreach ($distinctYears as $yr) {
            $isPastYear = $yr < $actualYear;
            $itemsYr = $astaps->filter(fn($i) => (int)$i->tahun_perolehan === $yr);

            $totalItem = $itemsYr->count();
            $totalVolume = $itemsYr->sum(fn($i) => (int) ($i->jumlah_volume ?: 1));
            $totalNominal = $itemsYr->sum(fn($i) => (float) ($i->total_realisasi ?: 0));

            $annualRecord = PeriodeTutupBuku::where('tahun', $yr)->where('triwulan', 0)->first();
            $isLocked = $isPastYear || (bool) ($annualRecord?->is_locked ?? false);

            $tahunanData[] = [
                'tahun'                   => $yr,
                'nama'                    => "Tahun Anggaran {$yr}",
                'keterangan'              => $isPastYear ? 'Tutup Buku Tahunan Final (Audited LKPD BPK RI)' : 'Tahun Berjalan (Belum Year-End Closing)',
                'is_locked'               => $isLocked,
                'is_past_year'            => $isPastYear,
                'status_label'            => $isLocked ? 'Ditutup Buku Tahunan' : 'Tahun Berjalan',
                'total_item'              => $totalItem,
                'total_volume'            => $totalVolume,
                'total_nominal'           => $totalNominal,
                'total_nominal_formatted' => 'Rp ' . number_format($totalNominal, 0, ',', '.'),
                'nomor_bar'               => $annualRecord?->nomor_bar_bpkad ?: ($isPastYear ? 'LKPD BPK RI / BPKAD' : '-'),
            ];
        }

        // Mapped Astap List untuk Javascript Alpine.js
        $astapsJson = $astaps->map(function($a) {
            $spec = is_array($a->spesifikasi_json) ? $a->spesifikasi_json : (json_decode($a->spesifikasi_json, true) ?? []);
            $ja = $a->jenisAstap;
            $kode108Val = $a->kode_108 ?: ($ja ? ($ja->sub_sub_rincian_objek ?: $ja->jenis) : '');
            $latestReklas = $a->reklas ? $a->reklas->first() : null;

            // Klasifikasi Sumber Perolehan / Dana Eksplisit (BLUD, Belanja Modal, Hibah, Mutasi, Kemitraan)
            $sumberType = 'belanja_modal';
            $sumberLabel = 'BELANJA MODAL';

            if ($a->isKemitraan()) {
                $sumberType = 'kemitraan';
                $sumberLabel = 'KEMITRAAN';
            } elseif ($a->isHibah() || $a->sumber_dana === 'hibah') {
                $sumberType = 'hibah';
                $sumberLabel = 'HIBAH';
            } elseif ($a->isMutasiMasuk() || $a->isMutasiEksternal() || in_array($a->sumber_dana, ['mutasi', 'mutasi_masuk', 'mutasi_eksternal', 'pelimpahan', 'pelimpahan_skpd'], true)) {
                $sumberType = 'mutasi';
                $sumberLabel = 'MUTASI';
            } elseif ($a->sumber_dana === 'blud' || $a->isBelanjaBarang() || str_contains(strtolower($a->asal_usul ?? ''), 'blud')) {
                $sumberType = 'blud';
                $sumberLabel = 'BLUD';
            } else {
                $sumberType = 'belanja_modal';
                $sumberLabel = 'BELANJA MODAL';
            }

            $reklasHistory = $a->reklas && $a->reklas->count() > 0 
                ? $a->reklas->map(function($r, $idx) {
                    return [
                        'urutan'           => $idx + 1,
                        'id'               => $r->id,
                        'tanggal_reklas'   => $r->tanggal_reklas ? $r->tanggal_reklas->format('d/m/Y') : ($r->created_at ? $r->created_at->format('d/m/Y') : '-'),
                        'nomor_ba_reklas'  => $r->nomor_ba_reklas ?: '-',
                        'jenis_reklas'     => $r->jenis_reklas ?: 'Reklasifikasi Akun',
                        'sub_koreksi_label'=> $r->sub_koreksi_label,
                        'alasan_reklas'    => $r->alasan_reklas ?: ($r->keterangan ?: 'Penyesuaian klasifikasi akun Simda BMD 108'),
                        'nilai_reklas'     => (float) ($r->nilai_reklas ?: 0),
                        'nilai_formatted'  => 'Rp ' . number_format($r->nilai_reklas ?: 0, 0, ',', '.'),
                        'asal_kib'         => $r->asal_kib ?: '-',
                        'tujuan_kib'       => $r->tujuan_kib ?: '-',
                        'asal_kode'        => $r->asal_kode ?: '-',
                        'tujuan_kode'      => $r->tujuan_kode ?: '-',
                    ];
                })->values()->all() 
                : [];

            if (empty($reklasHistory) && ($a->is_reklas || !empty($a->jenis_reklas))) {
                $reklasHistory[] = [
                    'urutan'           => 1,
                    'id'               => 'legacy_' . $a->id,
                    'tanggal_reklas'   => $a->tanggal_spk ? $a->tanggal_spk->format('d/m/Y') : ($a->created_at ? $a->created_at->format('d/m/Y') : '-'),
                    'nomor_ba_reklas'  => $a->nomor_ba_reklas ?: ($a->nomor_spk ?: '-'),
                    'jenis_reklas'     => $a->jenis_reklas ?: 'Reklasifikasi Akun',
                    'sub_koreksi_label'=> null,
                    'alasan_reklas'    => $a->keterangan ?: 'Pencatatan Reklasifikasi Buku Inventaris',
                    'nilai_reklas'     => (float) ($a->total_realisasi ?: 0),
                    'nilai_formatted'  => 'Rp ' . number_format($a->total_realisasi ?: 0, 0, ',', '.'),
                    'asal_kib'         => $a->category ?: '-',
                    'tujuan_kib'       => $a->category ?: '-',
                    'asal_kode'        => $a->kode_108 ?: '-',
                    'tujuan_kode'      => $a->kode_108 ?: '-',
                ];
            }

            $reklasCount = count($reklasHistory);
            $reklasJenis = $latestReklas?->jenis_reklas ?? $a->jenis_reklas ?? '';

            // Sumber Perolehan Relasional
            $bm = $a->belanjaModal;
            $bb = $a->belanjaBarang;
            $km = $a->kemitraan;
            $hibahDetail = $a->hibahDetail;
            $mutasiMasuk = $a->mutasiEksternalMasuk ?: $a->mutasiEksternal;
            $mutasiKeluar = $a->mutasiEksternals ? $a->mutasiEksternals->firstWhere('tipe', 'keluar') : null;
            $hibahKeluarModel = $a->hibahs ? $a->hibahs->firstWhere('tipe', 'keluar') : null;

            // 1. Data Kemitraan Pihak Ketiga (KSO / Sewa BMD)
            $isKemitraanAset = $a->isKemitraan() || $sumberType === 'kemitraan' || !empty($km);
            $kemitraanData = $km ? [
                'id'              => $km->id,
                'mitra_nama'      => $km->mitra_nama ?: ($spec['mitra_nama'] ?? ($a->penyedia_nama ?: '-')),
                'nomor_pks'       => $km->nomor_pks ?: ($spec['nomor_pks'] ?? ($a->bast_dokumen_nomor ?: '-')),
                'tanggal_pks'     => $km->tanggal_pks ? $km->tanggal_pks->format('Y-m-d') : ($spec['tanggal_pks'] ?? ($a->bast_dokumen_tanggal ? (is_string($a->bast_dokumen_tanggal) ? $a->bast_dokumen_tanggal : $a->bast_dokumen_tanggal->format('Y-m-d')) : null)),
                'skema_kemitraan' => $km->skema_kemitraan ?: ($spec['skema_kemitraan'] ?? 'Sewa BMD'),
                'tipe_kemitraan'  => $km->tipe_kemitraan ?: 'dimanfaatkan',
                'status_konsesi'  => $km->status_konsesi ?: 'Aktif',
                'tanggal_mulai'   => $km->tanggal_mulai ? $km->tanggal_mulai->format('Y-m-d') : ($spec['tanggal_mulai'] ?? null),
                'tanggal_selesai' => $km->tanggal_selesai ? $km->tanggal_selesai->format('Y-m-d') : ($spec['tanggal_selesai'] ?? null),
                'sisa_hari'       => $km->sisa_hari_konsesi,
                'keterangan'      => $km->keterangan ?: ($spec['keterangan'] ?? null),
            ] : ($isKemitraanAset ? [
                'id'              => null,
                'mitra_nama'      => $spec['mitra_nama'] ?? ($a->penyedia_nama ?: 'Mitra Pihak Ketiga'),
                'nomor_pks'       => $a->bast_dokumen_nomor ?: ($spec['nomor_pks'] ?? '-'),
                'tanggal_pks'     => $a->bast_dokumen_tanggal ? (is_string($a->bast_dokumen_tanggal) ? $a->bast_dokumen_tanggal : $a->bast_dokumen_tanggal->format('Y-m-d')) : ($spec['tanggal_pks'] ?? null),
                'skema_kemitraan' => $spec['skema_kemitraan'] ?? 'Sewa BMD',
                'tipe_kemitraan'  => 'dimanfaatkan',
                'status_konsesi'  => 'Aktif',
                'tanggal_mulai'   => $spec['tanggal_mulai'] ?? null,
                'tanggal_selesai' => $spec['tanggal_selesai'] ?? null,
                'sisa_hari'       => null,
                'keterangan'      => $spec['keterangan'] ?? null,
            ] : null);

            // 2. Data Hibah Masuk & Hibah Keluar
            $hibahMasukData = $hibahDetail ? [
                'id'               => $hibahDetail->id,
                'pemberi_hibah'    => $hibahDetail->pemberi_hibah ?: ($a->hibah_pemberi ?: ($spec['pemberi_hibah'] ?? ($spec['hibah_pemberi'] ?? ''))),
                'pimpinan_pemberi' => $hibahDetail->pimpinan_pemberi ?: ($spec['pimpinan_pemberi'] ?? ($a->penyedia_pemilik ?? '')),
                'nomor_bast'       => $hibahDetail->nomor_bast ?: ($a->hibah_nomor_bast ?: ($a->bast_dokumen_nomor ?: ($spec['nomor_bast'] ?? ''))),
                'tanggal_bast'     => $hibahDetail->tanggal_bast ? $hibahDetail->tanggal_bast->format('Y-m-d') : ($a->hibah_tanggal_bast ? (is_string($a->hibah_tanggal_bast) ? $a->hibah_tanggal_bast : $a->hibah_tanggal_bast->format('Y-m-d')) : ($a->bast_dokumen_tanggal ? (is_string($a->bast_dokumen_tanggal) ? $a->bast_dokumen_tanggal : $a->bast_dokumen_tanggal->format('Y-m-d')) : ($spec['tanggal_bast'] ?? null))),
                'nilai_hibah'      => (float) ($hibahDetail->nilai_hibah ?: $a->total_realisasi),
                'keterangan'       => $hibahDetail->keterangan ?: ($a->hibah_keterangan ?: ($spec['keterangan'] ?? '')),
            ] : null;

            $hibahKeluarData = $hibahKeluarModel ? [
                'id'               => $hibahKeluarModel->id,
                'penerima_hibah'   => $hibahKeluarModel->penerima_hibah ?: ($hibahKeluarModel->penerima ?: ($a->hibah_pemberi ?: ($spec['penerima_hibah'] ?? ''))),
                'nama_penerima'    => $hibahKeluarModel->nama_penerima ?: ($spec['nama_penerima'] ?? ''),
                'jabatan_penerima' => $hibahKeluarModel->jabatan_penerima ?: ($spec['jabatan_penerima'] ?? ''),
                'nip_penerima'     => $hibahKeluarModel->nip_penerima ?: ($spec['nip_penerima'] ?? ''),
                'alamat_penerima'  => $hibahKeluarModel->alamat_penerima ?: ($spec['alamat_penerima'] ?? ($a->alamat_barang ?: '')),
                'nomor_bast'       => $hibahKeluarModel->nomor_bast ?: ($hibahKeluarModel->nomor_naskah ?: ($a->bast_dokumen_nomor ?: '')),
                'tanggal_bast'     => $hibahKeluarModel->tanggal_bast ? $hibahKeluarModel->tanggal_bast->format('Y-m-d') : ($a->bast_dokumen_tanggal ? (is_string($a->bast_dokumen_tanggal) ? $a->bast_dokumen_tanggal : $a->bast_dokumen_tanggal->format('Y-m-d')) : null),
            ] : null;

            $isHibahKeluarAset = !empty($hibahKeluarData) || ($a->jenis_reklas === 'HIBAH_KELUAR') || $a->isHibahKeluar();

            // 3. Data Mutasi Eksternal
            $mutasiKeluarData = $mutasiKeluar ? [
                'id'                => $mutasiKeluar->id,
                'opd_asal'          => $mutasiKeluar->opd_asal ?: 'RSUD Dr. H. Koesnandi',
                'opd_tujuan'        => $mutasiKeluar->opd_tujuan ?: '-',
                'pj_tujuan_nama'    => $mutasiKeluar->pj_tujuan_nama ?: '-',
                'pj_tujuan_jabatan' => $mutasiKeluar->pj_tujuan_jabatan ?: '-',
                'pj_tujuan_nip'     => $mutasiKeluar->pj_tujuan_nip ?: '-',
                'nomor_bamb'        => $mutasiKeluar->nomor_bamb ?: ($a->bast_dokumen_nomor ?: '-'),
                'tanggal_mutasi'    => $mutasiKeluar->tanggal_mutasi ? $mutasiKeluar->tanggal_mutasi->format('Y-m-d') : ($a->bast_dokumen_tanggal ? (is_string($a->bast_dokumen_tanggal) ? $a->bast_dokumen_tanggal : $a->bast_dokumen_tanggal->format('Y-m-d')) : null),
            ] : null;

            // 4. Data Rekanan Vendor & PPK (Belanja Modal / Belanja Barang)
            $ppkNama = $a->ppk_nama ?: ($spec['ppk_nama'] ?? ($bm?->ppk_nama ?? 'BUDI HARTONO, S.Sos'));
            $ppkNip = $a->ppk_nip ?: ($spec['ppk_nip'] ?? ($bm?->ppk_nip ?? '19760229 200801 1 010'));
            $penyediaNama = $bm?->penyedia_nama ?: ($a->penyedia_nama ?: ($bb?->nama_toko ?: ($spec['penyedia_nama'] ?? ($spec['mitra_nama'] ?? ''))));
            $penyediaPemilik = $bm?->penyedia_pemilik ?: ($a->penyedia_pemilik ?: ($spec['penyedia_pemilik'] ?? ''));
            $penyediaAlamat = $bm?->penyedia_alamat ?: ($a->penyedia_alamat ?: ($spec['penyedia_alamat'] ?? ''));
            $penyediaTelepon = $bm?->penyedia_telepon ?: ($a->penyedia_telepon ?: ($spec['penyedia_telepon'] ?? ''));
            $penyediaRekNama = $bm?->penyedia_rekening_nama ?: ($a->penyedia_rekening_nama ?: ($spec['penyedia_rekening_nama'] ?? ''));
            $penyediaRekNomor = $bm?->penyedia_rekening_nomor ?: ($a->penyedia_rekening_nomor ?: ($spec['penyedia_rekening_nomor'] ?? ''));

            return [
                'id'                    => $a->id,
                'category'              => $a->category,
                'kode_barang'           => $kode108Val,
                'nama_barang'           => $a->nama_barang,
                'tahun_perolehan'       => (int) ($a->tahun_perolehan ?: date('Y')),
                'triwulan'              => $a->triwulan ?: ($spec['triwulan'] ?? 'TW I'),
                'triwulan_num'          => PeriodeTutupBuku::parseTriwulan($a->triwulan),
                'volume_satuan'         => ($a->jumlah_volume ?: 1) . ' ' . ($a->satuan ?: 'Aset'),
                'jumlah_volume'         => (int) ($a->jumlah_volume ?: 1),
                'satuan'                => $a->satuan ?: 'Unit',
                'harga_satuan'          => (float) $a->harga_satuan,
                'jumlah_anggaran'       => (float) ($a->jumlah_anggaran ?: 0),
                'jumlah_realisasi'      => 'Rp ' . number_format($a->total_realisasi, 0, ',', '.'),
                'total_realisasi_num'   => (float) $a->total_realisasi,
                'kondisi'               => $a->kondisi ?: 'Baik',
                'sumber_dana'           => $sumberType,
                'sumber_dana_raw'       => $a->sumber_dana,
                'sumber_type'           => $sumberType,
                'sumber_label'          => $sumberLabel,
                'asal_usul'             => $a->asal_usul ?: ($sumberType === 'blud' ? 'BLUD RSUD' : 'APBD Kabupaten Bondowoso'),
                'jenis_aset_nama'       => $ja ? ($ja->uraian_sub_rincian ?: $ja->nama_jenis) : 'Aset Tetap',
                'reklas_count'          => $reklasCount,
                'reklas_history'        => $reklasHistory,
                'is_reklas'             => $reklasCount > 0,
                'jenis_reklas'          => $reklasJenis,
                'is_tutup_buku'         => $a->isTutupBuku(),
                'unit_id'               => $a->unit_id,
                'unit_nama'             => $a->unit?->nama ?? '',
                'rekening_belanja'      => $a->rekeningBelanja ? $a->rekeningBelanja->nama_rekening : '',
                'spk_nomor'             => $bm?->spk_nomor ?: ($a->nomor_spk ?: ($spec['spk_nomor'] ?? '')),
                'spk_tanggal'           => $bm?->spk_tanggal ? (is_string($bm->spk_tanggal) ? $bm->spk_tanggal : $bm->spk_tanggal->format('Y-m-d')) : ($a->tanggal_spk ? $a->tanggal_spk->format('Y-m-d') : ($spec['spk_tanggal'] ?? '')),
                'bap_nomor'             => $bm?->bap_nomor ?: ($a->nomor_bap ?: ($spec['bap_nomor'] ?? '')),
                'bap_tanggal'           => $bm?->bap_tanggal ? (is_string($bm->bap_tanggal) ? $bm->bap_tanggal : $bm->bap_tanggal->format('Y-m-d')) : ($a->tanggal_bap ? $a->tanggal_bap->format('Y-m-d') : ($spec['bap_tanggal'] ?? '')),
                'bast_dokumen_nomor'    => $bm?->bast_dokumen_nomor ?: ($a->bast_dokumen_nomor ?: ($a->nomor_bast ?: ($spec['bast_dokumen_nomor'] ?? ''))),
                'bast_dokumen_tanggal'  => $bm?->bast_dokumen_tanggal ? (is_string($bm->bast_dokumen_tanggal) ? $bm->bast_dokumen_tanggal : $bm->bast_dokumen_tanggal->format('Y-m-d')) : ($a->bast_dokumen_tanggal ? (is_string($a->bast_dokumen_tanggal) ? $a->bast_dokumen_tanggal : $a->bast_dokumen_tanggal->format('Y-m-d')) : ($a->tanggal_bast ? $a->tanggal_bast->format('Y-m-d') : ($spec['bast_dokumen_tanggal'] ?? ''))),
                'faktur_nomor'          => $bm?->faktur_nomor ?: ($bb?->nomor_faktur ?: ($a->faktur_nomor ?: ($spec['faktur_nomor'] ?? ''))),
                'faktur_tanggal'        => $bm?->faktur_tanggal ? (is_string($bm->faktur_tanggal) ? $bm->faktur_tanggal : $bm->faktur_tanggal->format('Y-m-d')) : ($bb?->tanggal_faktur ? (is_string($bb->tanggal_faktur) ? $bb->tanggal_faktur : $bb->tanggal_faktur->format('Y-m-d')) : ($a->faktur_tanggal ? $a->faktur_tanggal->format('Y-m-d') : ($spec['faktur_tanggal'] ?? ''))),
                'sp2d_nomor'            => $bm?->sp2d_nomor ?: ($a->sp2d_nomor ?: ($spec['sp2d_nomor'] ?? '')),
                'sp2d_tanggal'          => $bm?->sp2d_tanggal ? (is_string($bm->sp2d_tanggal) ? $bm->sp2d_tanggal : $bm->sp2d_tanggal->format('Y-m-d')) : ($a->sp2d_tanggal ? $a->sp2d_tanggal->format('Y-m-d') : ($spec['sp2d_tanggal'] ?? '')),
                'ppk_nama'              => $ppkNama,
                'ppk_nip'               => $ppkNip,
                'penyedia_nama'         => $penyediaNama,
                'penyedia_pemilik'      => $penyediaPemilik,
                'penyedia_alamat'       => $penyediaAlamat,
                'penyedia_telepon'      => $penyediaTelepon,
                'penyedia_rekening_nama'  => $penyediaRekNama,
                'penyedia_rekening_nomor' => $penyediaRekNomor,
                'alamat_barang'         => $a->alamat_barang ?: ($spec['alamat_barang'] ?? 'RSUD Dr. H. Koesnandi Bondowoso, Jl. Piere Tendean No. 1'),
                'keterangan_tambahan'   => $a->keterangan_tambahan ?: ($a->keterangan ?: ($spec['keterangan'] ?? '-')),
                'keterangan'            => $a->keterangan,
                'kemitraan'             => $kemitraanData,
                'hibah_masuk'           => $hibahMasukData,
                'hibah_keluar'          => $hibahKeluarData,
                'is_hibah_keluar'       => $isHibahKeluarAset,
                'hibah_pemberi'         => $a->hibah_pemberi ?: ($spec['hibah_pemberi'] ?? ''),
                'hibah_nomor_bast'      => $a->hibah_nomor_bast ?: ($spec['hibah_nomor_bast'] ?? ''),
                'hibah_tanggal_bast'    => $a->hibah_tanggal_bast ? (is_string($a->hibah_tanggal_bast) ? $a->hibah_tanggal_bast : $a->hibah_tanggal_bast->format('Y-m-d')) : ($spec['hibah_tanggal_bast'] ?? ''),
                'hibah_keterangan'      => $a->hibah_keterangan ?: ($spec['hibah_keterangan'] ?? ''),
                'mutasi_keluar'         => $mutasiKeluarData,
                'mutasi_asal'           => $mutasiMasuk?->opd_asal ?: ($a->mutasi_asal ?: ($spec['skpd_asal'] ?? '')),
                'mutasi_nomor_bamb'     => $mutasiMasuk?->nomor_bamb ?: ($a->mutasi_nomor_bamb ?: ($spec['nomor_bamb'] ?? '')),
                'mutasi_tanggal'        => $mutasiMasuk?->tanggal_mutasi ? $mutasiMasuk->tanggal_mutasi->format('Y-m-d') : ($a->mutasi_tanggal ? (is_string($a->mutasi_tanggal) ? $a->mutasi_tanggal : $a->mutasi_tanggal->format('Y-m-d')) : ($spec['mutasi_tanggal'] ?? '')),
                'mutasi_keterangan'     => $mutasiMasuk?->alasan_mutasi ?: ($a->mutasi_keterangan ?: ($spec['mutasi_keterangan'] ?? '')),
                'rekening_penyedia'     => $bb?->nama_toko ?: ($spec['rekening_penyedia'] ?? $penyediaNama),
                'rekening_nomor_faktur' => $bb?->nomor_faktur ?: ($spec['rekening_nomor_faktur'] ?? ($a->faktur_nomor ?: '')),
                'rekening_tanggal_faktur'=> $bb?->tanggal_faktur ? (is_string($bb->tanggal_faktur) ? $bb->tanggal_faktur : $bb->tanggal_faktur->format('Y-m-d')) : ($a->faktur_tanggal ? $a->faktur_tanggal->format('Y-m-d') : ''),
                'rekening_keterangan'   => $bb?->keterangan ?: ($spec['rekening_keterangan'] ?? ''),
                'registers'             => $a->registers ? $a->registers->sortBy(function($r) {
                    return $r->no_register_int ?: intval(substr($r->nibar ?? '', -7));
                })->values()->map(function($r) {
                    return [
                        'id'              => $r->id,
                        'unit_id'         => $r->unit_id,
                        'no_register_int' => $r->no_register_int ?: intval(substr($r->nibar ?? '', -7)),
                        'no_register'     => $r->nibar ?: $r->no_register,
                        'nibar'           => $r->nibar,
                        'ruang_pemegang'  => $r->ruang_pemegang ?: ($r->unit?->nama ?? '-'),
                        'kondisi'         => $r->kondisi ?: 'Baik',
                        'status'          => $r->status ?: 'Tersedia',
                        'qr_code_path'    => $r->qr_code_path ?: ('/scan/' . ($r->nibar ?: $r->no_register)),
                        'nama_barang'     => $r->nama_barang,
                    ];
                })->values() : [],
                'spesifikasi_json'      => $spec,
            ];
        });

        $dbMaster108 = \App\Models\JenisAstap::getNested108();
        $dbMitraKemitraans = \App\Models\AstapKemitraan::getDistinctMitras();

        $astaps = $astapsJson;

        return view('pages.tutup_buku.index', compact(
            'currentYear',
            'currentTab',
            'actualYear',
            'actualQuarter',
            'triwulanData',
            'tahunanData',
            'astaps',
            'astapsJson',
            'distinctYears',
            'dbMaster108',
            'dbMitraKemitraans'
        ));
    }

    /**
     * Dapatkan status penutupan buku untuk semua triwulan pada tahun tertentu
     */
    public function status(Request $request)
    {
        $tahun = (int) ($request->input('tahun', date('Y')));

        $records = PeriodeTutupBuku::with(['lockedByUser', 'unlockedByUser'])
            ->where('tahun', $tahun)
            ->get()
            ->keyBy('triwulan');

        $isAnnualLocked = (bool) ($records->get(0)?->is_locked ?? false);

        // Ambil data aset ASTAP untuk tahun terpilih untuk menghitung nilai dan volume terkunci
        $astapsForYear = \App\Models\Astap::where('is_deleted', 0)
            ->where('tahun_perolehan', $tahun)
            ->get();

        $twGroups = [];
        foreach ($astapsForYear as $item) {
            $itemTw = PeriodeTutupBuku::parseTriwulan($item->triwulan);
            if (!isset($twGroups[$itemTw])) {
                $twGroups[$itemTw] = ['count' => 0, 'volume' => 0, 'nominal' => 0];
            }
            $twGroups[$itemTw]['count']++;
            $twGroups[$itemTw]['volume'] += (int) ($item->jumlah_volume ?: 1);
            $twGroups[$itemTw]['nominal'] += (float) ($item->total_realisasi ?: 0);
        }

        $periods = [];
        $definitions = [
            1 => ['nama' => 'Triwulan 1', 'rentang' => 'Januari - Maret', 'badge' => 'TW 1'],
            2 => ['nama' => 'Triwulan 2', 'rentang' => 'April - Juni', 'badge' => 'TW 2'],
            3 => ['nama' => 'Triwulan 3', 'rentang' => 'Juli - September', 'badge' => 'TW 3'],
            4 => ['nama' => 'Triwulan 4', 'rentang' => 'Oktober - Desember', 'badge' => 'TW 4'],
            0 => ['nama' => 'Tutup Buku Tahunan', 'rentang' => 'Per 31 Desember (Audit LKPD BPK RI)', 'badge' => 'Tahunan'],
        ];

        foreach ($definitions as $tw => $def) {
            $rec = $records->get($tw);
            $lockedDirectly = (bool) ($rec?->is_locked ?? false);
            // Jika triwulan 1-4, tapi kunci tahunan (0) aktif, maka otomatis terkunci
            $effectiveLocked = ($tw !== 0 && $isAnnualLocked) ? true : $lockedDirectly;

            if ($tw === 0) {
                $statCount = $astapsForYear->count();
                $statVol = $astapsForYear->sum(fn($i) => (int)($i->jumlah_volume ?: 1));
                $statNominal = $astapsForYear->sum(fn($i) => (float)($i->total_realisasi ?: 0));
            } else {
                $statCount = $twGroups[$tw]['count'] ?? 0;
                $statVol = $twGroups[$tw]['volume'] ?? 0;
                $statNominal = $twGroups[$tw]['nominal'] ?? 0;
            }

            $periods[] = [
                'triwulan'                => $tw,
                'nama'                    => $def['nama'],
                'rentang'                 => $def['rentang'],
                'badge'                   => $def['badge'],
                'is_locked'               => $effectiveLocked,
                'is_annual_parent'        => ($tw !== 0 && $isAnnualLocked && !$lockedDirectly),
                'nomor_bar_bpkad'         => $rec?->nomor_bar_bpkad ?: ($isAnnualLocked ? ($records->get(0)?->nomor_bar_bpkad ?: '-') : '-'),
                'tanggal_tutup'           => $rec?->tanggal_tutup?->format('d/m/Y') ?: ($isAnnualLocked ? ($records->get(0)?->tanggal_tutup?->format('d/m/Y') ?: '-') : '-'),
                'keterangan'              => $rec?->keterangan ?: '-',
                'locked_by_name'          => $rec?->lockedByUser?->name ?: ($isAnnualLocked ? ($records->get(0)?->lockedByUser?->name ?: '-') : '-'),
                'locked_at'               => $rec?->locked_at?->format('d/m/Y H:i') ?: '-',
                'unlocked_by_name'        => $rec?->unlockedByUser?->name ?: null,
                'unlocked_at'             => $rec?->unlocked_at?->format('d/m/Y H:i') ?: null,
                'alasan_unlock'           => $rec?->alasan_unlock ?: null,
                'total_item'              => $statCount,
                'total_volume'            => $statVol,
                'total_nominal'           => $statNominal,
                'total_nominal_formatted' => 'Rp ' . number_format($statNominal, 0, ',', '.'),
            ];
        }

        return response()->json([
            'success'          => true,
            'tahun'            => $tahun,
            'is_annual_locked' => $isAnnualLocked,
            'periods'          => $periods,
        ]);
    }

    /**
     * Eksekusi Penguncian Periode (Tutup Buku Triwulanan / Tahunan)
     */
    public function lock(Request $request)
    {
        $validated = $request->validate([
            'tahun'           => 'required|integer|min:2000|max:2100',
            'triwulan'        => 'required|integer|in:0,1,2,3,4',
            'nomor_bar_bpkad' => 'required|string|max:150',
            'tanggal_tutup'   => 'required|date',
            'keterangan'      => 'nullable|string|max:1000',
        ], [
            'nomor_bar_bpkad.required' => 'Nomor Berita Acara Rekonsiliasi (BAR) BPKAD wajib diisi sebagai dasar legalitas penutupan buku.',
            'tanggal_tutup.required'   => 'Tanggal penutupan buku wajib diisi.',
        ]);

        $user = Auth::user();

        $record = PeriodeTutupBuku::updateOrCreate(
            [
                'tahun'    => $validated['tahun'],
                'triwulan' => $validated['triwulan'],
            ],
            [
                'is_locked'       => true,
                'nomor_bar_bpkad' => $validated['nomor_bar_bpkad'],
                'tanggal_tutup'   => $validated['tanggal_tutup'],
                'keterangan'      => $validated['keterangan'] ?? null,
                'locked_by'       => $user->id,
                'locked_at'       => now(),
            ]
        );

        $label = $validated['triwulan'] == 0 
            ? "Tutup Buku Tahunan T.A. {$validated['tahun']}" 
            : "Triwulan {$validated['triwulan']} T.A. {$validated['tahun']}";

        return response()->json([
            'success' => true,
            'message' => "Periode {$label} berhasil ditutup buku resmi. Data aset kini berstatus Read-Only dan dilindungi dari perubahan.",
            'data'    => $record,
        ]);
    }

    /**
     * Eksekusi Pembukaan Kunci Periode (Emergency Revision / Buka Buku)
     * Hanya diizinkan untuk peran Master Admin dengan alasan tertulis
     */
    public function unlock(Request $request)
    {
        $validated = $request->validate([
            'tahun'         => 'required|integer|min:2000|max:2100',
            'triwulan'      => 'required|integer|in:0,1,2,3,4',
            'alasan_unlock' => 'required|string|min:5|max:1000',
        ], [
            'alasan_unlock.required' => 'Alasan pembukaan kunci wajib diisi demi transparansi dan kepatuhan audit trail BPK.',
            'alasan_unlock.min'      => 'Uraikan alasan pembukaan kunci dengan jelas (minimal 5 karakter).',
        ]);

        $user = Auth::user();
        if ($user->role !== 'master_admin') {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Pembukaan kunci periode tutup buku hanya dapat dilakukan oleh Master Administrator.',
            ], 403);
        }

        $record = PeriodeTutupBuku::where('tahun', $validated['tahun'])
            ->where('triwulan', $validated['triwulan'])
            ->first();

        if (!$record || !$record->is_locked) {
            return response()->json([
                'success' => false,
                'message' => 'Periode tersebut saat ini tidak dalam status terkunci.',
            ], 422);
        }

        $record->update([
            'is_locked'     => false,
            'unlocked_by'   => $user->id,
            'unlocked_at'   => now(),
            'alasan_unlock' => $validated['alasan_unlock'],
        ]);

        $label = $validated['triwulan'] == 0 
            ? "Tutup Buku Tahunan T.A. {$validated['tahun']}" 
            : "Triwulan {$validated['triwulan']} T.A. {$validated['tahun']}";

        return response()->json([
            'success' => true,
            'message' => "Kunci periode {$label} berhasil dibuka kembali. Alasan revisi telah tercatat di jejak audit.",
            'data'    => $record,
        ]);
    }
}
