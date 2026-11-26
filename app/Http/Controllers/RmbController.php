<?php

namespace App\Http\Controllers;

use App\Models\Astap;
use App\Models\AstapReklas;
use App\Models\JenisAstap;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RmbController extends Controller
{
    /**
     * Definisi Baku 36 Rekening Sub Rincian Objek Aset Tetap (KIB A s/d F - PMDN 108)
     */
    public static function getRmbDefinitions(): array
    {
        return [
            // KIB A - TANAH
            ['code' => '1.3.1.01', 'group' => 'KIB A - Tanah', 'group_key' => 'KIB A', 'label' => 'TANAH', 'prefix' => '1.3.1'],

            // KIB B - PERALATAN DAN MESIN (19 Sub Rincian Baku)
            ['code' => '1.3.2.01', 'group' => 'KIB B - Peralatan dan Mesin', 'group_key' => 'KIB B', 'label' => 'ALAT BESAR', 'prefix' => '1.3.2.01'],
            ['code' => '1.3.2.02', 'group' => 'KIB B - Peralatan dan Mesin', 'group_key' => 'KIB B', 'label' => 'ALAT ANGKUTAN', 'prefix' => '1.3.2.02'],
            ['code' => '1.3.2.03', 'group' => 'KIB B - Peralatan dan Mesin', 'group_key' => 'KIB B', 'label' => 'ALAT BENGKEL DAN ALAT UKUR', 'prefix' => '1.3.2.03'],
            ['code' => '1.3.2.04', 'group' => 'KIB B - Peralatan dan Mesin', 'group_key' => 'KIB B', 'label' => 'ALAT PERTANIAN', 'prefix' => '1.3.2.04'],
            ['code' => '1.3.2.05', 'group' => 'KIB B - Peralatan dan Mesin', 'group_key' => 'KIB B', 'label' => 'ALAT KANTOR DAN RUMAH TANGGA', 'prefix' => '1.3.2.05'],
            ['code' => '1.3.2.06', 'group' => 'KIB B - Peralatan dan Mesin', 'group_key' => 'KIB B', 'label' => 'ALAT STUDIO, KOMUNIKASI DAN PEMANCAR', 'prefix' => '1.3.2.06'],
            ['code' => '1.3.2.07', 'group' => 'KIB B - Peralatan dan Mesin', 'group_key' => 'KIB B', 'label' => 'ALAT KEDOKTERAN DAN KESEHATAN', 'prefix' => '1.3.2.07'],
            ['code' => '1.3.2.08', 'group' => 'KIB B - Peralatan dan Mesin', 'group_key' => 'KIB B', 'label' => 'ALAT LABORATORIUM', 'prefix' => '1.3.2.08'],
            ['code' => '1.3.2.09', 'group' => 'KIB B - Peralatan dan Mesin', 'group_key' => 'KIB B', 'label' => 'ALAT PERSENJATAAN', 'prefix' => '1.3.2.09'],
            ['code' => '1.3.2.10', 'group' => 'KIB B - Peralatan dan Mesin', 'group_key' => 'KIB B', 'label' => 'KOMPUTER', 'prefix' => '1.3.2.10'],
            ['code' => '1.3.2.11', 'group' => 'KIB B - Peralatan dan Mesin', 'group_key' => 'KIB B', 'label' => 'ALAT EKSPLORASI', 'prefix' => '1.3.2.11'],
            ['code' => '1.3.2.12', 'group' => 'KIB B - Peralatan dan Mesin', 'group_key' => 'KIB B', 'label' => 'ALAT PENGEBORAN', 'prefix' => '1.3.2.12'],
            ['code' => '1.3.2.13', 'group' => 'KIB B - Peralatan dan Mesin', 'group_key' => 'KIB B', 'label' => 'ALAT PRODUKSI, PENGOLAHAN DAN PEMURNIAN', 'prefix' => '1.3.2.13'],
            ['code' => '1.3.2.14', 'group' => 'KIB B - Peralatan dan Mesin', 'group_key' => 'KIB B', 'label' => 'ALAT BANTU EKSPLORASI', 'prefix' => '1.3.2.14'],
            ['code' => '1.3.2.15', 'group' => 'KIB B - Peralatan dan Mesin', 'group_key' => 'KIB B', 'label' => 'ALAT KESELAMATAN KERJA', 'prefix' => '1.3.2.15'],
            ['code' => '1.3.2.16', 'group' => 'KIB B - Peralatan dan Mesin', 'group_key' => 'KIB B', 'label' => 'ALAT PERAGA', 'prefix' => '1.3.2.16'],
            ['code' => '1.3.2.17', 'group' => 'KIB B - Peralatan dan Mesin', 'group_key' => 'KIB B', 'label' => 'PERALATAN PROSES/PRODUKSI', 'prefix' => '1.3.2.17'],
            ['code' => '1.3.2.18', 'group' => 'KIB B - Peralatan dan Mesin', 'group_key' => 'KIB B', 'label' => 'RAMBU - RAMBU', 'prefix' => '1.3.2.18'],
            ['code' => '1.3.2.19', 'group' => 'KIB B - Peralatan dan Mesin', 'group_key' => 'KIB B', 'label' => 'PERALATAN OLAH RAGA', 'prefix' => '1.3.2.19'],

            // KIB C - GEDUNG DAN BANGUNAN (4 Sub Rincian Baku)
            ['code' => '1.3.3.01', 'group' => 'KIB C - Gedung dan Bangunan', 'group_key' => 'KIB C', 'label' => 'BANGUNAN GEDUNG', 'prefix' => '1.3.3.01'],
            ['code' => '1.3.3.02', 'group' => 'KIB C - Gedung dan Bangunan', 'group_key' => 'KIB C', 'label' => 'MONUMEN', 'prefix' => '1.3.3.02'],
            ['code' => '1.3.3.03', 'group' => 'KIB C - Gedung dan Bangunan', 'group_key' => 'KIB C', 'label' => 'BANGUNAN MENARA', 'prefix' => '1.3.3.03'],
            ['code' => '1.3.3.04', 'group' => 'KIB C - Gedung dan Bangunan', 'group_key' => 'KIB C', 'label' => 'TUGU TITIK KONTROL/PASTI', 'prefix' => '1.3.3.04'],

            // KIB D - JALAN, JARINGAN DAN IRIGASI (4 Sub Rincian Baku)
            ['code' => '1.3.4.01', 'group' => 'KIB D - Jalan, Jaringan dan Irigasi', 'group_key' => 'KIB D', 'label' => 'JALAN DAN JEMBATAN', 'prefix' => '1.3.4.01'],
            ['code' => '1.3.4.02', 'group' => 'KIB D - Jalan, Jaringan dan Irigasi', 'group_key' => 'KIB D', 'label' => 'BANGUNAN AIR', 'prefix' => '1.3.4.02'],
            ['code' => '1.3.4.03', 'group' => 'KIB D - Jalan, Jaringan dan Irigasi', 'group_key' => 'KIB D', 'label' => 'INSTALASI', 'prefix' => '1.3.4.03'],
            ['code' => '1.3.4.04', 'group' => 'KIB D - Jalan, Jaringan dan Irigasi', 'group_key' => 'KIB D', 'label' => 'JARINGAN', 'prefix' => '1.3.4.04'],

            // KIB E - ASET TETAP LAINNYA (7 Sub Rincian Baku)
            ['code' => '1.3.5.01', 'group' => 'KIB E - Aset Tetap Lainnya', 'group_key' => 'KIB E', 'label' => 'BAHAN PERPUSTAKAAN', 'prefix' => '1.3.5.01'],
            ['code' => '1.3.5.02', 'group' => 'KIB E - Aset Tetap Lainnya', 'group_key' => 'KIB E', 'label' => 'BARANG BERCORAK KESENIAN/KEBUDAYAAN/OLAHRAGA', 'prefix' => '1.3.5.02'],
            ['code' => '1.3.5.03', 'group' => 'KIB E - Aset Tetap Lainnya', 'group_key' => 'KIB E', 'label' => 'HEWAN', 'prefix' => '1.3.5.03'],
            ['code' => '1.3.5.04', 'group' => 'KIB E - Aset Tetap Lainnya', 'group_key' => 'KIB E', 'label' => 'BIOTA PERAIRAN', 'prefix' => '1.3.5.04'],
            ['code' => '1.3.5.05', 'group' => 'KIB E - Aset Tetap Lainnya', 'group_key' => 'KIB E', 'label' => 'TANAMAN', 'prefix' => '1.3.5.05'],
            ['code' => '1.3.5.06', 'group' => 'KIB E - Aset Tetap Lainnya', 'group_key' => 'KIB E', 'label' => 'ASET TETAP LAINNYA', 'prefix' => '1.3.5.06'],
            ['code' => '1.3.5.07', 'group' => 'KIB E - Aset Tetap Lainnya', 'group_key' => 'KIB E', 'label' => 'ASET TETAP DALAM RENOVASI', 'prefix' => '1.3.5.07'],

            // KIB F - KONSTRUKSI DALAM PENGERJAAN (1 Sub Rincian Baku)
            ['code' => '1.3.6.01', 'group' => 'KIB F - Konstruksi Dalam Pengerjaan', 'group_key' => 'KIB F', 'label' => 'KONSTRUKSI DALAM PENGERJAAN', 'prefix' => '1.3.6'],
        ];
    }

    /**
     * Menampilkan Halaman Utama Rekonsiliasi Belanja Modal (RMB)
     */
    public function index(Request $request)
    {
        $selectedTahun = (int) $request->get('tahun', date('Y'));
        $selectedTw = $request->get('triwulan', 'all');

        // Daftar tahun perolehan yang ada di sistem
        $tahunList = Astap::select('tahun_perolehan')
            ->distinct()
            ->orderBy('tahun_perolehan', 'desc')
            ->pluck('tahun_perolehan')
            ->filter()
            ->toArray();

        if (!in_array($selectedTahun, $tahunList)) {
            array_unshift($tahunList, $selectedTahun);
        }

        // Hitung Data Rekonsiliasi 21 Kolom
        $rmbData = $this->calculateRmbData($selectedTahun, $selectedTw);

        return view('pages.rmb.index', [
            'selectedTahun' => $selectedTahun,
            'selectedTw'    => $selectedTw,
            'tahunList'     => $tahunList,
            'rmbRows'       => $rmbData['rows'],
            'subtotals'     => $rmbData['subtotals'],
            'grandTotal'    => $rmbData['grandTotal'],
            'kertasKerja'   => $rmbData['kertasKerja'],
            'kpis'          => $rmbData['kpis'],
        ]);
    }

    /**
     * Endpoint JSON untuk menyuplai data RMB ke modul lain (termasuk Ekspor Excel Sheet 4 ASTAP)
     */
    public function jsonData(Request $request)
    {
        $rawTahun = $request->get('tahun', date('Y'));
        $tahun = ($rawTahun === 'all' || $rawTahun === '' || $rawTahun === null) ? null : (int) $rawTahun;
        $triwulan = $this->normalizeTriwulanParam((string) $request->get('triwulan', 'all'));

        $rmbData = $this->calculateRmbData($tahun, $triwulan);

        return response()->json([
            'success' => true,
            'tahun' => $tahun ?? 'all',
            'triwulan' => $triwulan,
            'rows' => $rmbData['rows'],
            'subtotals' => $rmbData['subtotals'],
            'grandTotal' => $rmbData['grandTotal'],
            'kertasKerja' => $rmbData['kertasKerja'],
            'kpis' => $rmbData['kpis'],
        ]);
    }

    /**
     * Normalisasi parameter triwulan ('TW I', 'TW1', '1', 'all') menjadi 'all' atau '1'..'4'.
     */
    protected function normalizeTriwulanParam(string $rawTw): string
    {
        $clean = strtoupper(preg_replace('/[\s_]+/', '', trim($rawTw)));
        if ($clean === '' || $clean === 'ALL') {
            return 'all';
        }
        $map = [
            '1' => '1', 'I' => '1', 'TW1' => '1', 'TWI' => '1', 'Q1' => '1', 'TRIWULAN1' => '1', 'TRIWULANI' => '1',
            '2' => '2', 'II' => '2', 'TW2' => '2', 'TWII' => '2', 'Q2' => '2', 'TRIWULAN2' => '2', 'TRIWULANII' => '2',
            '3' => '3', 'III' => '3', 'TW3' => '3', 'TWIII' => '3', 'Q3' => '3', 'TRIWULAN3' => '3', 'TRIWULANIII' => '3',
            '4' => '4', 'IV' => '4', 'TW4' => '4', 'TWIV' => '4', 'Q4' => '4', 'TRIWULAN4' => '4', 'TRIWULANIV' => '4',
        ];
        return $map[$clean] ?? 'all';
    }

    /**
     * Hitung Matriks 21 Kolom RMB dan Kertas Kerja Rekonsiliasi
     */
    protected function calculateRmbData(?int $tahun, string $triwulan): array
    {
        $defs = self::getRmbDefinitions();

        // Template baris data
        $rows = [];
        foreach ($defs as $d) {
            $rows[$d['code']] = [
                'code'                  => $d['code'],
                'group'                 => $d['group'],
                'group_key'             => $d['group_key'],
                'label'                 => $d['label'],
                'saldo_awal'            => 0,
                // Penambahan (Kolom 1 - 8)
                'c1_belanja_modal'      => 0,
                'c2_hibah'              => 0,
                'c3_belanja_barang'     => 0,
                'c4_mutasi_tambah'      => 0,
                'c5_koreksi_rek_tambah' => 0,
                'c6_koreksi_lkd_tambah' => 0,
                'c7_koreksi_manset_tambah' => 0,
                'c8_kdp_tambah'         => 0,
                'total_penambahan'      => 0,
                // Pengurangan (Kolom 9 - 18)
                'c9_sk_penghapusan'     => 0,
                'c10_dihibahkan'        => 0,
                'c11_mutasi_kurang'     => 0,
                'c12_kapitalisasi_kurang' => 0, // Ekstrakomptabel (Batas Kapitalisasi)
                'c13_direklas_aset_lain'=> 0, // Ke 1.5.4
                'c14_hilang'            => 0,
                'c15_koreksi_kurang'    => 0,
                'c16_koreksi_lkd_kurang'=> 0,
                'c17_koreksi_manset_kurang' => 0,
                'c18_kdp_kurang'        => 0,
                'total_pengurangan'     => 0,
                // Saldo Akhir
                'saldo_akhir'           => 0,
            ];
        }

        // Subtotals per KIB
        $groupKeys = ['KIB A', 'KIB B', 'KIB C', 'KIB D', 'KIB E', 'KIB F'];
        $subtotals = [];
        foreach ($groupKeys as $gk) {
            $subtotals[$gk] = [
                'saldo_awal'            => 0,
                'c1_belanja_modal'      => 0,
                'c2_hibah'              => 0,
                'c3_belanja_barang'     => 0,
                'c4_mutasi_tambah'      => 0,
                'c5_koreksi_rek_tambah' => 0,
                'c6_koreksi_lkd_tambah' => 0,
                'c7_koreksi_manset_tambah' => 0,
                'c8_kdp_tambah'         => 0,
                'total_penambahan'      => 0,
                'c9_sk_penghapusan'     => 0,
                'c10_dihibahkan'        => 0,
                'c11_mutasi_kurang'     => 0,
                'c12_kapitalisasi_kurang' => 0,
                'c13_direklas_aset_lain'=> 0,
                'c14_hilang'            => 0,
                'c15_koreksi_kurang'    => 0,
                'c16_koreksi_lkd_kurang'=> 0,
                'c17_koreksi_manset_kurang' => 0,
                'c18_kdp_kurang'        => 0,
                'total_pengurangan'     => 0,
                'saldo_akhir'           => 0,
            ];
        }

        $grandTotal = $subtotals['KIB A']; // Struktur sama

        // 1. Ambil Seluruh Data ASTAP untuk tahun berjalan (atau seluruh tahun jika $tahun === null)
        $astapQuery = Astap::where('is_deleted', 0)
            ->when($tahun !== null, fn($q) => $q->where('tahun_perolehan', $tahun))
            ->with(['jenisAstap', 'belanjaModal', 'belanjaBarang', 'mutasiEksternalMasuk']);

        if ($triwulan !== 'all') {
            $tw = (int) $triwulan;
            $triwulanMap = [
                1 => ['1', 'TW1', 'TW 1', 'TW I', 'Triwulan I', 'Triwulan 1', 'Q1', 'I'],
                2 => ['2', 'TW2', 'TW 2', 'TW II', 'Triwulan II', 'Triwulan 2', 'Q2', 'II'],
                3 => ['3', 'TW3', 'TW 3', 'TW III', 'Triwulan III', 'Triwulan 3', 'Q3', 'III'],
                4 => ['4', 'TW4', 'TW 4', 'TW IV', 'Triwulan IV', 'Triwulan 4', 'Q4', 'IV'],
            ];
            $twValues = $triwulanMap[$tw] ?? [(string) $tw];
            $astapQuery->whereIn('triwulan', $twValues);
        }

        $astaps = $astapQuery->get();

        // 2. Ambil Riwayat Reklasifikasi Aktif (is_deleted = 0 dan ASTAP-nya aktif)
        $reklasQuery = AstapReklas::active()
            ->when($tahun !== null, fn($q) => $q->where('tahun', $tahun));
        if ($triwulan !== 'all') {
            $reklasQuery->where('triwulan', (int) $triwulan);
        }
        $reklases = $reklasQuery->with(['astap.jenisAstap', 'jenisReklasAsal', 'jenisReklasTujuan'])
            ->orderBy('id', 'asc')
            ->get()
            ->filter(fn($rk) => $rk->astap && (int) $rk->astap->is_deleted === 0)
            ->values();

        // Kumpulkan seluruh transaksi reklas aktif tahun ini, log ekstrakom lintas tahun, dan hibah tahun berjalan untuk cegah double-counting
        $yearReklasByAstap = AstapReklas::active()
            ->when($tahun !== null, fn($q) => $q->where('tahun', $tahun))
            ->with(['astap.jenisAstap', 'jenisReklasAsal', 'jenisReklasTujuan'])
            ->orderBy('id', 'asc')
            ->get()
            ->groupBy('astap_id');
        $reklasByAstap = $reklases->groupBy('astap_id');

        $allLoggedExtracomAstapIds = AstapReklas::active()
            ->whereIn('jenis_reklas', ['EKSTRAKOMPTABEL', 'KAPITALISASI_INTRAKOM'])
            ->pluck('astap_id')
            ->filter()
            ->unique()
            ->values()
            ->all();

        $yearHibahAstapIds = Astap::where('is_deleted', 0)
            ->when($tahun !== null, fn($q) => $q->where('tahun_perolehan', $tahun))
            ->where('sumber_dana', 'hibah')
            ->pluck('id')
            ->all();

        // 3. Akumulasi Belanja Modal & Penambahan dari ASTAP Tahun Berjalan
        foreach ($astaps as $astap) {
            // Jika aset mengalami KOREKSI_REKENING pada tahun ini, akui perolehan awalnya di rekening ASAL sebelum mutasi pindah KIB
            $code = null;
            $firstKoreksiRek = isset($yearReklasByAstap[$astap->id])
                ? $yearReklasByAstap[$astap->id]->firstWhere('jenis_reklas', 'KOREKSI_REKENING')
                : null;
            if ($firstKoreksiRek) {
                $code = $this->resolveRmbCodeForReklasAsal($firstKoreksiRek, $defs);
            }
            if (!$code) {
                $code = $this->resolveRmbCodeForAstap($astap, $defs);
            }
            if (!$code || !isset($rows[$code])) continue;

            $val = (float) ($astap->total_realisasi ?: ($astap->jumlah_anggaran ?: 0));

            // Jika aset memiliki transaksi koreksi nilai (KOREKSI_LAIN) pada periode ini,
            // kembalikan nilai awal ke nilai semula sebelum koreksi periode ini agar mutasi tambah/kurang neto akurat
            if (isset($reklasByAstap[$astap->id])) {
                $firstKorLain = $reklasByAstap[$astap->id]->firstWhere('jenis_reklas', 'KOREKSI_LAIN');
                if ($firstKorLain) {
                    $firstInfo = $firstKorLain->spesifikasi_baru['koreksi_info'] ?? null;
                    if (!empty($firstInfo['nilai_semula'])) {
                        $val = (float) $firstInfo['nilai_semula'];
                    } else {
                        foreach ($reklasByAstap[$astap->id] as $rkAstap) {
                            if ($rkAstap->jenis_reklas === 'KOREKSI_LAIN') {
                                $info = $rkAstap->spesifikasi_baru['koreksi_info'] ?? null;
                                $tipe = $info['tipe_koreksi'] ?? ($rkAstap->asal_kib === 'KOREKSI' || str_contains(strtolower($rkAstap->keterangan ?? ''), 'penambahan nilai') ? 'tambah' : 'kurang');
                                if ($tipe === 'kurang') {
                                    $val += (float) $rkAstap->nilai_reklas;
                                } elseif ($tipe === 'tambah') {
                                    $val = max(0, $val - (float) $rkAstap->nilai_reklas);
                                }
                            }
                        }
                    }
                }
            }

            $sumber = $astap->sumber_dana;

            // Klasifikasikan Penambahan
            if ($sumber === 'hibah') {
                $rows[$code]['c2_hibah'] += $val;
            } elseif ($sumber === 'belanja_barang' || $sumber === 'belanja_rekening') {
                $rows[$code]['c3_belanja_barang'] += $val;
            } elseif ($sumber === 'mutasi_eksternal' || $sumber === 'pelimpahan_skpd' || $sumber === 'mutasi_masuk') {
                $rows[$code]['c4_mutasi_tambah'] += $val;
            } else {
                // Belanja modal APBD/BLUD murni tahun berjalan
                $rows[$code]['c1_belanja_modal'] += $val;
            }

            // Ekstrakomptabel tanpa log transaksi reklasifikasi resmi (cegah double-counting lintas tahun)
            if ($astap->is_extracomtable && !in_array($astap->id, $allLoggedExtracomAstapIds, true)) {
                $rows[$code]['c12_kapitalisasi_kurang'] += $val;
            }
        }

        // 3B. Akui Saldo Awal untuk Aset Tahun Lampau (tahun_perolehan < $tahun) yang dimutasi pada periode ini
        if ($tahun !== null) {
            foreach ($reklasByAstap as $astapId => $rkGroup) {
                $firstRk = $rkGroup->first();
                $astap = $firstRk?->astap;
                if (!$astap || (int) $astap->tahun_perolehan >= $tahun) {
                    continue;
                }
                if ($firstRk->jenis_reklas === 'HIBAH_MASUK') {
                    continue;
                }

                $asalStartCode = $this->resolveRmbCodeForReklasAsal($firstRk, $defs);
                if (!$asalStartCode || !isset($rows[$asalStartCode])) {
                    continue;
                }

                $firstInfo = $firstRk->spesifikasi_baru['koreksi_info'] ?? null;
                if ($firstRk->jenis_reklas === 'KOREKSI_LAIN' && !empty($firstInfo['nilai_semula'])) {
                    $saldoAwalAset = (float) $firstInfo['nilai_semula'];
                } else {
                    $totalOutInPeriod = 0.0;
                    foreach ($rkGroup as $rkItem) {
                        if (in_array($rkItem->jenis_reklas, ['EKSTRAKOMPTABEL', 'HIBAH_KELUAR', 'MUTASI_EKSTERNAL', 'KDP_TO_DEFINITIF', 'KOREKSI_REKENING', 'ASET_LAIN_LAIN'], true)) {
                            $totalOutInPeriod += (float) $rkItem->nilai_reklas;
                        } elseif ($rkItem->jenis_reklas === 'KOREKSI_LAIN') {
                            $infoItem = $rkItem->spesifikasi_baru['koreksi_info'] ?? null;
                            $tipeItem = $infoItem['tipe_koreksi'] ?? ($rkItem->asal_kib === 'KOREKSI' || str_contains(strtolower($rkItem->keterangan ?? ''), 'penambahan nilai') ? 'tambah' : 'kurang');
                            if ($tipeItem === 'kurang') {
                                $totalOutInPeriod += (float) $rkItem->nilai_reklas;
                            }
                        }
                    }
                    $saldoAwalAset = max((float) $astap->total_realisasi, $totalOutInPeriod);
                }

                $rows[$asalStartCode]['saldo_awal'] += $saldoAwalAset;
            }
        }

        // 4. Akumulasi Pengurangan & Mutasi Reklasifikasi (AstapReklas)
        foreach ($reklases as $rk) {
            $val = (float) $rk->nilai_reklas;
            $astap = $rk->astap;
            $asalCode = $this->resolveRmbCodeForReklasAsal($rk, $defs);
            $tujuanCode = $this->resolveRmbCodeForReklasTujuan($rk, $defs);

            switch ($rk->jenis_reklas) {
                case 'EKSTRAKOMPTABEL':
                    if ($asalCode && isset($rows[$asalCode])) {
                        $rows[$asalCode]['c12_kapitalisasi_kurang'] += $val;
                    }
                    break;

                case 'KAPITALISASI_INTRAKOM':
                    if ($tujuanCode && isset($rows[$tujuanCode])) {
                        $rows[$tujuanCode]['c3_belanja_barang'] += $val;
                    }
                    break;

                case 'HIBAH_KELUAR':
                    if ($asalCode && isset($rows[$asalCode])) {
                        $rows[$asalCode]['c10_dihibahkan'] += $val;
                    }
                    break;

                case 'HIBAH_MASUK':
                    // Aset hibah tahun berjalan sudah dicatat pada triwulan perolehannya di Langkah 3
                    if (in_array($rk->astap_id, $yearHibahAstapIds, true)) {
                        break;
                    }
                    if ($tujuanCode && isset($rows[$tujuanCode])) {
                        $rows[$tujuanCode]['c2_hibah'] += $val;
                    }
                    break;

                case 'MUTASI_EKSTERNAL':
                    if ($asalCode && isset($rows[$asalCode])) {
                        $rows[$asalCode]['c11_mutasi_kurang'] += $val;
                    }
                    break;

                case 'KDP_TO_DEFINITIF':
                    if (isset($rows['1.3.6.01'])) {
                        $rows['1.3.6.01']['c18_kdp_kurang'] += $val;
                    }
                    if ($tujuanCode && isset($rows[$tujuanCode])) {
                        $rows[$tujuanCode]['c8_kdp_tambah'] += $val;
                    }
                    break;

                case 'KOREKSI_REKENING':
                    // Pindah KIB / Rekening (jika tujuan Aset Lain-Lain 1.5.4, catat ke c13_direklas_aset_lain)
                    $destKibUpper = strtoupper(trim((string) $rk->tujuan_kib));
                    $destKodeStr = trim((string) ($rk->tujuan_kode ?: $rk->jenisReklasTujuan?->kode_prefix ?: ''));
                    $isDestAsetLain = str_contains($destKibUpper, 'ASET LAIN') || str_starts_with($destKodeStr, '1.5.4');
                    if ($asalCode && isset($rows[$asalCode])) {
                        if ($isDestAsetLain) {
                            $rows[$asalCode]['c13_direklas_aset_lain'] += $val;
                        } else {
                            $rows[$asalCode]['c15_koreksi_kurang'] += $val;
                        }
                    }
                    if ($tujuanCode && isset($rows[$tujuanCode])) {
                        $rows[$tujuanCode]['c5_koreksi_rek_tambah'] += $val;
                    }
                    break;

                case 'KOREKSI_LAIN':
                    // Koreksi Nilai dibagi menjadi 3: Koreksi Biasa, Koreksi LKD, Koreksi Manset
                    $info = $rk->spesifikasi_baru['koreksi_info'] ?? null;
                    $tipe = $info['tipe_koreksi'] ?? ($rk->asal_kib === 'KOREKSI' || str_contains(strtolower($rk->keterangan ?? ''), 'penambahan nilai') ? 'tambah' : 'kurang');
                    $subKoreksi = $rk->sub_koreksi ?? ($info['sub_koreksi'] ?? 'biasa');
                    $targetRowCode = ($tipe === 'tambah') ? ($tujuanCode ?: ($astap ? $this->resolveRmbCodeForAstap($astap, $defs) : null)) : ($asalCode ?: ($astap ? $this->resolveRmbCodeForAstap($astap, $defs) : null));

                    if ($targetRowCode && isset($rows[$targetRowCode])) {
                        if ($tipe === 'tambah') {
                            if ($subKoreksi === 'lkd') {
                                $rows[$targetRowCode]['c6_koreksi_lkd_tambah'] += $val;
                            } elseif ($subKoreksi === 'manset') {
                                $rows[$targetRowCode]['c7_koreksi_manset_tambah'] += $val;
                            } else {
                                // Koreksi Biasa (Internal)
                                $rows[$targetRowCode]['c5_koreksi_rek_tambah'] += $val;
                            }
                        } else {
                            if ($subKoreksi === 'lkd') {
                                $rows[$targetRowCode]['c16_koreksi_lkd_kurang'] += $val;
                            } elseif ($subKoreksi === 'manset') {
                                $rows[$targetRowCode]['c17_koreksi_manset_kurang'] += $val;
                            } else {
                                // Koreksi Biasa (Internal)
                                $rows[$targetRowCode]['c15_koreksi_kurang'] += $val;
                            }
                        }
                    }
                    break;

                case 'ASET_LAIN_LAIN':
                    if ($asalCode && isset($rows[$asalCode])) {
                        $rows[$asalCode]['c13_direklas_aset_lain'] += $val;
                    }
                    break;
            }
        }

        // 5. Hitung Total Penambahan, Total Pengurangan, Saldo Akhir, dan Subtotals
        foreach ($rows as $code => &$r) {
            $r['total_penambahan'] = $r['c1_belanja_modal']
                + $r['c2_hibah']
                + $r['c3_belanja_barang']
                + $r['c4_mutasi_tambah']
                + $r['c5_koreksi_rek_tambah']
                + $r['c6_koreksi_lkd_tambah']
                + $r['c7_koreksi_manset_tambah']
                + $r['c8_kdp_tambah'];

            $r['total_pengurangan'] = $r['c9_sk_penghapusan']
                + $r['c10_dihibahkan']
                + $r['c11_mutasi_kurang']
                + $r['c12_kapitalisasi_kurang']
                + $r['c13_direklas_aset_lain']
                + $r['c14_hilang']
                + $r['c15_koreksi_kurang']
                + $r['c16_koreksi_lkd_kurang']
                + $r['c17_koreksi_manset_kurang']
                + $r['c18_kdp_kurang'];

            $r['saldo_akhir'] = $r['saldo_awal'] + $r['total_penambahan'] - $r['total_pengurangan'];

            // Akumulasi Subtotal
            $gk = $r['group_key'];
            if (isset($subtotals[$gk])) {
                foreach (array_keys($subtotals[$gk]) as $f) {
                    $subtotals[$gk][$f] += $r[$f];
                    $grandTotal[$f] += $r[$f];
                }
            }
        }
        unset($r);

        // 6. Kertas Kerja Rekonsiliasi (Neraca Kas vs Fisik Aset)
        $totalEkstrakom = $grandTotal['c12_kapitalisasi_kurang'];
        $totalHibahMasuk = $grandTotal['c2_hibah'];
        $totalHibahKeluar = $grandTotal['c10_dihibahkan'];
        $totalKoreksiLain = (
            $grandTotal['c9_sk_penghapusan']
            + $grandTotal['c11_mutasi_kurang']
            + $grandTotal['c13_direklas_aset_lain']
            + $grandTotal['c14_hilang']
            + $grandTotal['c15_koreksi_kurang']
            + $grandTotal['c16_koreksi_lkd_kurang']
            + $grandTotal['c17_koreksi_manset_kurang']
            + $grandTotal['c18_kdp_kurang']
        ) - (
            $grandTotal['c3_belanja_barang']
            + $grandTotal['c4_mutasi_tambah']
            + $grandTotal['c5_koreksi_rek_tambah']
            + $grandTotal['c6_koreksi_lkd_tambah']
            + $grandTotal['c7_koreksi_manset_tambah']
            + $grandTotal['c8_kdp_tambah']
        );

        $saldoAkhirAsetTetap = $grandTotal['saldo_akhir'];

        // Rumus Rekonsiliasi Belanja Modal Kasda:
        // Saldo Awal + Belanja Modal LRA = Saldo Akhir Aset Tetap + Penyeimbang Ekstrakom + Penyeimbang Hibah + Penyeimbang Koreksi Lain
        $penyeimbangHibahNetto = $totalHibahKeluar - $totalHibahMasuk;
        $rekonBelanjaModal = $saldoAkhirAsetTetap + $totalEkstrakom + $penyeimbangHibahNetto + $totalKoreksiLain;
        $realisasiKasda = $grandTotal['c1_belanja_modal'] + $grandTotal['saldo_awal']; // Basis Saldo Awal + Belanja Kas LRA
        $selisih = abs($rekonBelanjaModal - $realisasiKasda);

        $kertasKerja = [
            'saldo_awal_belanja'     => $realisasiKasda,
            'mutasi_tambah_netto'    => max(0, $grandTotal['total_penambahan'] - $grandTotal['c1_belanja_modal']),
            'mutasi_kurang'          => $grandTotal['total_pengurangan'],
            'saldo_akhir_aset_tetap' => $saldoAkhirAsetTetap,
            'koreksi_hibah'          => $penyeimbangHibahNetto,
            'koreksi_ekstrakom'      => $totalEkstrakom,
            'koreksi_lain_lain'      => $totalKoreksiLain,
            'total_rekon_belanja'    => $rekonBelanjaModal,
            'realisasi_kasda_lra'    => $realisasiKasda,
            'selisih'                => $selisih,
            'is_balance'             => ($selisih < 1), // Toleransi pembulatan pecahan sen
        ];

        $kpis = [
            'total_belanja_kas'            => $realisasiKasda,
            'belanja_modal_tahun_berjalan' => $grandTotal['c1_belanja_modal'],
            'saldo_awal_lampau'            => $grandTotal['saldo_awal'],
            'total_penambahan'             => $grandTotal['total_penambahan'],
            'total_pengurangan'            => $grandTotal['total_pengurangan'],
            'saldo_akhir_aset'             => $saldoAkhirAsetTetap,
            'is_balance'                   => ($selisih < 1),
            'selisih'                      => $selisih,
            'total_ekstrakom'              => $totalEkstrakom,
            'total_hibah'                  => $totalHibahMasuk + $totalHibahKeluar,
        ];

        return [
            'rows'        => $rows,
            'subtotals'   => $subtotals,
            'grandTotal'  => $grandTotal,
            'kertasKerja' => $kertasKerja,
            'kpis'        => $kpis,
        ];
    }

    /**
     * Petakan kode PMDN 108 / kelompok KIB ke kode baris RMB (hanya KIB A s/d F: 1.3.1 - 1.3.6).
     */
    protected function mapCodeOrKibToRmb(?string $code, ?string $kib, array $defs): ?string
    {
        $cleanCode = trim((string) $code);
        if ($cleanCode !== '') {
            // Aset Lainnya (1.5.2 Kemitraan, 1.5.3 ATB, 1.5.4 Aset Lain-Lain) berada di luar neraca Aset Tetap KIB A-F
            if (str_starts_with($cleanCode, '1.5.')) {
                return null;
            }
            foreach ($defs as $d) {
                if (str_starts_with($cleanCode, $d['prefix']) || $cleanCode === $d['code']) {
                    return $d['code'];
                }
            }
        }

        $cat = strtoupper(trim((string) $kib));
        $catMap = [
            'KIB A' => '1.3.1.01',
            'KIB B' => '1.3.2.05', // Default Alat Kantor & RT
            'KIB C' => '1.3.3.01',
            'KIB D' => '1.3.4.01',
            'KIB E' => '1.3.5.06',
            'KIB F' => '1.3.6.01',
        ];

        return $catMap[$cat] ?? null;
    }

    /**
     * Resolusi kode RMB 108 untuk ASTAP
     */
    protected function resolveRmbCodeForAstap(Astap $astap, array $defs): ?string
    {
        // Aset kemitraan pihak ketiga tercatat di Akun 1.5.2 (Aset Lainnya), bukan Belanja Modal KIB A-F
        if ($astap->sumber_dana === 'kemitraan' || strtoupper(trim((string) $astap->category)) === 'KEMITRAAN') {
            return null;
        }

        $code = null;
        if ($astap->jenisAstap) {
            $code = $astap->jenisAstap->sub_rincian_objek ?: $astap->jenisAstap->jenis;
        }

        return $this->mapCodeOrKibToRmb($code, $astap->category, $defs);
    }

    protected function resolveRmbCodeForReklasAsal(AstapReklas $rk, array $defs): ?string
    {
        $asalKode = $rk->asal_kode ?: $rk->jenisReklasAsal?->kode_prefix;
        if ($asalKode && !str_starts_with((string) $asalKode, 'KOR_')) {
            $mapped = $this->mapCodeOrKibToRmb($asalKode, $rk->asal_kib, $defs);
            if ($mapped) {
                return $mapped;
            }
        }

        if ($rk->asal_kib && !in_array(strtoupper((string) $rk->asal_kib), ['KOREKSI', 'EKSTRAKOMPTABEL', 'HIBAH', ''], true)) {
            $mapped = $this->mapCodeOrKibToRmb(null, $rk->asal_kib, $defs);
            if ($mapped) {
                return $mapped;
            }
        }

        return $rk->astap ? $this->resolveRmbCodeForAstap($rk->astap, $defs) : null;
    }

    protected function resolveRmbCodeForReklasTujuan(AstapReklas $rk, array $defs): ?string
    {
        $tujuanKode = $rk->tujuan_kode ?: $rk->jenisReklasTujuan?->kode_prefix;
        if ($tujuanKode && str_starts_with((string) $tujuanKode, '1.5.')) {
            return null;
        }

        return $this->mapCodeOrKibToRmb($tujuanKode, $rk->tujuan_kib, $defs);
    }
}
