<?php

namespace App\Http\Controllers;

use App\Models\Astap;
use App\Models\AstapPelimpahanSkpd;
use App\Models\AstapRegister;
use App\Models\JenisAstap;
use App\Models\MutasiEksternal;
use App\Models\Unit;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MutasiEksternalController extends Controller
{
    /**
     * Dapatkan daftar pejabat penanggung jawab (PPK / Pengurus Barang) yang pernah tercatat.
     */
    public static function getDistinctPejabats()
    {
        return Astap::whereNotNull('ppk_nama')
            ->where('ppk_nama', '!=', '')
            ->orderBy('id', 'desc')
            ->get(['ppk_nama', 'ppk_nip'])
            ->map(function ($a) {
                return [
                    'nama' => trim($a->ppk_nama),
                    'nip'  => $a->ppk_nip ?? '',
                ];
            })
            ->unique(fn($p) => strtolower(trim($p['nama'])))
            ->values();
    }

    /**
     * Ekstrak unit individual dari repeater item pada spesifikasi_json.
     * Mengembalikan array unit dengan field: ['nama', 'kondisi', 'ruang', 'unit_id', 'nilai_satuan']
     */
    public static function extractUnitsFromSpec(
        array $specJson,
        string $fallbackNama = 'Barang Pelimpahan',
        string $fallbackKondisi = 'Baik',
        ?string $fallbackRuang = null,
        ?int $fallbackUnitId = null
    ): array {
        $units = [];

        if (!empty($specJson['mesin_items']) && is_array($specJson['mesin_items'])) {
            foreach ($specJson['mesin_items'] as $item) {
                $qty = max(1, (int)($item['mesin_jumlah'] ?? ($item['mesin_jumlah_barang'] ?? 1)));
                $nama = !empty($item['mesin_nama_barang']) ? trim($item['mesin_nama_barang']) : $fallbackNama;
                $kondisi = !empty($item['mesin_kondisi']) ? trim($item['mesin_kondisi']) : $fallbackKondisi;
                $ruang = !empty($item['ruang_pemegang']) ? trim($item['ruang_pemegang']) : $fallbackRuang;
                $unitId = !empty($item['unit_id']) ? (int)$item['unit_id'] : $fallbackUnitId;
                $nilai = (float)($item['mesin_nilai_satuan'] ?? 0);
                for ($k = 0; $k < $qty; $k++) {
                    $units[] = [
                        'nama'         => $nama,
                        'kondisi'      => $kondisi,
                        'ruang'        => $ruang,
                        'unit_id'      => $unitId,
                        'nilai_satuan' => $nilai,
                    ];
                }
            }
        } elseif (!empty($specJson['tanah_items']) && is_array($specJson['tanah_items'])) {
            foreach ($specJson['tanah_items'] as $item) {
                $qty = max(1, (int)($item['tanah_jumlah_bidang'] ?? ($item['tanah_jumlah_barang'] ?? 1)));
                $nama = !empty($item['tanah_nama_barang']) ? trim($item['tanah_nama_barang']) : $fallbackNama;
                $kondisi = !empty($item['tanah_kondisi']) ? trim($item['tanah_kondisi']) : $fallbackKondisi;
                $ruang = !empty($item['ruang_pemegang']) ? trim($item['ruang_pemegang']) : $fallbackRuang;
                $unitId = !empty($item['unit_id']) ? (int)$item['unit_id'] : $fallbackUnitId;
                $nilai = (float)($item['tanah_nilai_satuan'] ?? 0);
                for ($k = 0; $k < $qty; $k++) {
                    $units[] = [
                        'nama'         => $nama,
                        'kondisi'      => $kondisi,
                        'ruang'        => $ruang,
                        'unit_id'      => $unitId,
                        'nilai_satuan' => $nilai,
                    ];
                }
            }
        } elseif (!empty($specJson['gedung_items']) && is_array($specJson['gedung_items'])) {
            foreach ($specJson['gedung_items'] as $item) {
                $qty = max(1, (int)($item['gedung_jumlah_bangunan'] ?? 1));
                $nama = !empty($item['gedung_nama_barang']) ? trim($item['gedung_nama_barang']) : $fallbackNama;
                $kondisi = !empty($item['gedung_kondisi']) ? trim($item['gedung_kondisi']) : $fallbackKondisi;
                $ruang = !empty($item['ruang_pemegang']) ? trim($item['ruang_pemegang']) : $fallbackRuang;
                $unitId = !empty($item['unit_id']) ? (int)$item['unit_id'] : $fallbackUnitId;
                $nilai = (float)($item['gedung_nilai_satuan'] ?? 0);
                for ($k = 0; $k < $qty; $k++) {
                    $units[] = [
                        'nama'         => $nama,
                        'kondisi'      => $kondisi,
                        'ruang'        => $ruang,
                        'unit_id'      => $unitId,
                        'nilai_satuan' => $nilai,
                    ];
                }
            }
        } elseif (!empty($specJson['jaringan_items']) && is_array($specJson['jaringan_items'])) {
            foreach ($specJson['jaringan_items'] as $item) {
                $qty = max(1, (int)($item['jaringan_jumlah'] ?? 1));
                $nama = !empty($item['jaringan_nama_barang']) ? trim($item['jaringan_nama_barang']) : $fallbackNama;
                $kondisi = !empty($item['jaringan_kondisi']) ? trim($item['jaringan_kondisi']) : $fallbackKondisi;
                $ruang = !empty($item['ruang_pemegang']) ? trim($item['ruang_pemegang']) : $fallbackRuang;
                $unitId = !empty($item['unit_id']) ? (int)$item['unit_id'] : $fallbackUnitId;
                $nilai = (float)($item['jaringan_nilai_satuan'] ?? 0);
                for ($k = 0; $k < $qty; $k++) {
                    $units[] = [
                        'nama'         => $nama,
                        'kondisi'      => $kondisi,
                        'ruang'        => $ruang,
                        'unit_id'      => $unitId,
                        'nilai_satuan' => $nilai,
                    ];
                }
            }
        } elseif (!empty($specJson['lainnya_items']) && is_array($specJson['lainnya_items'])) {
            foreach ($specJson['lainnya_items'] as $item) {
                $qty = max(1, (int)($item['lainnya_jumlah'] ?? 1));
                $nama = !empty($item['lainnya_nama_barang']) ? trim($item['lainnya_nama_barang']) : (!empty($item['lainnya_judul']) ? trim($item['lainnya_judul']) : $fallbackNama);
                $kondisi = !empty($item['lainnya_kondisi']) ? trim($item['lainnya_kondisi']) : $fallbackKondisi;
                $ruang = !empty($item['ruang_pemegang']) ? trim($item['ruang_pemegang']) : $fallbackRuang;
                $unitId = !empty($item['unit_id']) ? (int)$item['unit_id'] : $fallbackUnitId;
                $nilai = (float)($item['lainnya_nilai_satuan'] ?? 0);
                for ($k = 0; $k < $qty; $k++) {
                    $units[] = [
                        'nama'         => $nama,
                        'kondisi'      => $kondisi,
                        'ruang'        => $ruang,
                        'unit_id'      => $unitId,
                        'nilai_satuan' => $nilai,
                    ];
                }
            }
        }

        return $units;
    }

    /**
     * Hitung kondisi dominan dari daftar unit untuk penentuan ringkasan kondisi.
     */
    public static function determineDominantCondition(array $units, string $fallback = 'Baik'): string
    {
        if (empty($units)) {
            return $fallback;
        }
        $counts = [];
        foreach ($units as $u) {
            $k = !empty($u['kondisi']) ? $u['kondisi'] : 'Baik';
            $counts[$k] = ($counts[$k] ?? 0) + 1;
        }
        arsort($counts);
        return array_key_first($counts) ?: $fallback;
    }

    /**
     * Dapatkan direktori instansi/SKPD pengirim beserta riwayat data pejabat penyerahnya (Pihak Pertama).
     * Satu instansi dapat memiliki lebih dari 1 pejabat (Kepala Dinas, Pengurus Barang, PPK, atau pejabat baru pengganti).
     */
    public static function getSkpdDirectory()
    {
        // 1. Data default instansi umum di Bondowoso / Provinsi beserta opsi beberapa pejabat sebagai cadangan
        $defaultDirectory = [
            'Dinas Kesehatan Kabupaten Bondowoso' => [
                'nama'       => 'Dinas Kesehatan Kabupaten Bondowoso',
                'alamat'     => 'Jl. Imam Bonjol No. 34, Bondowoso',
                'pj_nama'    => 'dr. Mohammad Imron, M.MKes',
                'pj_nip'     => '197005121998031005',
                'pj_jabatan' => 'Kepala Dinas Kesehatan',
                'pejabats'   => [
                    [
                        'nama'    => 'dr. Mohammad Imron, M.MKes',
                        'nip'     => '197005121998031005',
                        'jabatan' => 'Kepala Dinas Kesehatan',
                    ],
                    [
                        'nama'    => 'Pengurus Barang Dinkes Bondowoso',
                        'nip'     => '-',
                        'jabatan' => 'Pengurus Barang Pengguna',
                    ],
                ],
            ],
            'BPKAD Kabupaten Bondowoso' => [
                'nama'       => 'BPKAD Kabupaten Bondowoso',
                'alamat'     => 'Jl. Letnan Karsono No. 2, Bondowoso',
                'pj_nama'    => 'Drs. H. Ansori, M.Si',
                'pj_nip'     => '196803151994021003',
                'pj_jabatan' => 'Kepala BPKAD Kabupaten Bondowoso',
                'pejabats'   => [
                    [
                        'nama'    => 'Drs. H. Ansori, M.Si',
                        'nip'     => '196803151994021003',
                        'jabatan' => 'Kepala BPKAD Kabupaten Bondowoso',
                    ],
                    [
                        'nama'    => 'Kepala Bidang Pengelolaan Aset Daerah BPKAD',
                        'nip'     => '-',
                        'jabatan' => 'Kabid Aset Daerah BPKAD',
                    ],
                ],
            ],
            'Pemerintah Kabupaten Bondowoso' => [
                'nama'       => 'Pemerintah Kabupaten Bondowoso',
                'alamat'     => 'Jl. Letnan Rantam No. 1, Bondowoso',
                'pj_nama'    => 'Hj. Haeriah Yuliati, S.Sos., M.M.',
                'pj_nip'     => '196907201990032005',
                'pj_jabatan' => 'Sekretaris Daerah Kab. Bondowoso',
                'pejabats'   => [
                    [
                        'nama'    => 'Hj. Haeriah Yuliati, S.Sos., M.M.',
                        'nip'     => '196907201990032005',
                        'jabatan' => 'Sekretaris Daerah Kab. Bondowoso',
                    ],
                ],
            ],
            'Dinas Kesehatan Provinsi Jawa Timur' => [
                'nama'       => 'Dinas Kesehatan Provinsi Jawa Timur',
                'alamat'     => 'Jl. Ahmad Yani No. 118, Gayungan, Surabaya',
                'pj_nama'    => 'Prof. Dr. dr. Erwin Astha Triyono, Sp.PD, K-PTI',
                'pj_nip'     => '196509181990031007',
                'pj_jabatan' => 'Kepala Dinas Kesehatan Provinsi Jatim',
                'pejabats'   => [
                    [
                        'nama'    => 'Prof. Dr. dr. Erwin Astha Triyono, Sp.PD, K-PTI',
                        'nip'     => '196509181990031007',
                        'jabatan' => 'Kepala Dinas Kesehatan Provinsi Jatim',
                    ],
                ],
            ],
        ];

        $directory = [];

        // 2. Query dari mutasi_eksternals (diurutkan desc agar data riwayat terbaru menjadi nomor 1 / teratas)
        try {
            $records = MutasiEksternal::whereNotNull('opd_asal')
                ->where('opd_asal', '!=', '')
                ->orderBy('id', 'desc')
                ->get(['opd_asal', 'alamat_instansi', 'pj_asal_nama', 'pj_asal_nip', 'pj_asal_jabatan']);

            foreach ($records as $r) {
                $nama = trim($r->opd_asal ?? '');
                if (!$nama) continue;

                $pjNama = ($r->pj_asal_nama && $r->pj_asal_nama !== 'Pejabat Penyerah OPD Pengirim') ? trim($r->pj_asal_nama) : '';
                $pjNip  = ($r->pj_asal_nip && $r->pj_asal_nip !== '-') ? trim($r->pj_asal_nip) : '';
                $pjJab  = ($r->pj_asal_jabatan && $r->pj_asal_jabatan !== 'Pengurus Barang / PPK Asal') ? trim($r->pj_asal_jabatan) : '';
                $alamat = trim($r->alamat_instansi ?? '');

                if (!isset($directory[$nama])) {
                    $directory[$nama] = [
                        'nama'       => $nama,
                        'alamat'     => $alamat,
                        'pj_nama'    => $pjNama,
                        'pj_nip'     => $pjNip,
                        'pj_jabatan' => $pjJab,
                        'pejabats'   => [],
                    ];
                }

                if ($pjNama) {
                    $exists = false;
                    foreach ($directory[$nama]['pejabats'] as $p) {
                        if (strtolower(trim($p['nama'])) === strtolower($pjNama)) {
                            $exists = true;
                            break;
                        }
                    }
                    if (!$exists) {
                        $directory[$nama]['pejabats'][] = [
                            'nama'    => $pjNama,
                            'nip'     => $pjNip,
                            'jabatan' => $pjJab,
                        ];
                    }
                    if (empty($directory[$nama]['pj_nama'])) {
                        $directory[$nama]['pj_nama'] = $pjNama;
                        $directory[$nama]['pj_nip'] = $pjNip;
                        $directory[$nama]['pj_jabatan'] = $pjJab;
                    }
                }
            }

            // 3. Tambahan dari Astap spesifikasi_json (diurutkan desc)
            $astaps = Astap::where('sumber_dana', 'pelimpahan_skpd')
                ->whereNotNull('mutasi_asal')
                ->where('mutasi_asal', '!=', '')
                ->orderBy('id', 'desc')
                ->get(['mutasi_asal', 'spesifikasi_json']);

            foreach ($astaps as $a) {
                $nama = trim($a->mutasi_asal ?? '');
                if (!$nama) continue;
                $spec = is_array($a->spesifikasi_json) ? $a->spesifikasi_json : (is_string($a->spesifikasi_json) ? json_decode($a->spesifikasi_json, true) : []);
                $pjNama = !empty($spec['pj_asal_nama']) ? trim($spec['pj_asal_nama']) : '';
                $pjNip  = !empty($spec['pj_asal_nip']) ? trim($spec['pj_asal_nip']) : '';
                $pjJab  = !empty($spec['pj_asal_jabatan']) ? trim($spec['pj_asal_jabatan']) : '';

                $alamat = !empty($spec['alamat_instansi']) ? trim($spec['alamat_instansi']) : '';

                if (!isset($directory[$nama])) {
                    $directory[$nama] = [
                        'nama'       => $nama,
                        'alamat'     => $alamat,
                        'pj_nama'    => $pjNama,
                        'pj_nip'     => $pjNip,
                        'pj_jabatan' => $pjJab,
                        'pejabats'   => [],
                    ];
                } else {
                    if (empty($directory[$nama]['alamat']) && !empty($alamat)) {
                        $directory[$nama]['alamat'] = $alamat;
                    }
                }

                if ($pjNama) {
                    $exists = false;
                    foreach ($directory[$nama]['pejabats'] as $p) {
                        if (strtolower(trim($p['nama'])) === strtolower($pjNama)) {
                            $exists = true;
                            break;
                        }
                    }
                    if (!$exists) {
                        $directory[$nama]['pejabats'][] = [
                            'nama'    => $pjNama,
                            'nip'     => $pjNip,
                            'jabatan' => $pjJab,
                        ];
                    }
                    if (empty($directory[$nama]['pj_nama'])) {
                        $directory[$nama]['pj_nama'] = $pjNama;
                        $directory[$nama]['pj_nip'] = $pjNip;
                        $directory[$nama]['pj_jabatan'] = $pjJab;
                    }
                }
            }

            // 4. Masukkan data default sebagai opsi cadangan di akhir (jika belum ada)
            foreach ($defaultDirectory as $defNama => $defData) {
                if (!isset($directory[$defNama])) {
                    $directory[$defNama] = $defData;
                } else {
                    if (empty($directory[$defNama]['alamat']) && !empty($defData['alamat'])) {
                        $directory[$defNama]['alamat'] = $defData['alamat'];
                    }
                    foreach ($defData['pejabats'] as $defP) {
                        $exists = false;
                        foreach ($directory[$defNama]['pejabats'] as $p) {
                            if (strtolower(trim($p['nama'])) === strtolower($defP['nama'])) {
                                $exists = true;
                                break;
                            }
                        }
                        if (!$exists) {
                            $directory[$defNama]['pejabats'][] = $defP;
                        }
                    }
                    if (empty($directory[$defNama]['pj_nama'])) {
                        $directory[$defNama]['pj_nama'] = $defData['pj_nama'];
                        $directory[$defNama]['pj_nip'] = $defData['pj_nip'];
                        $directory[$defNama]['pj_jabatan'] = $defData['pj_jabatan'];
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::error('getSkpdDirectory error: ' . $e->getMessage());
            if (empty($directory)) {
                $directory = $defaultDirectory;
            }
        }

        return $directory;
    }

    /**
     * Dapatkan daftar unik pejabat penyerah (Pihak Pertama) yang pernah tercatat.
     */
    public static function getDistinctPejabatPenyerahs()
    {
        $directory = self::getSkpdDirectory();
        $list = [];

        foreach ($directory as $skpd => $item) {
            if (!empty($item['pejabats'])) {
                foreach ($item['pejabats'] as $p) {
                    $list[] = [
                        'nama'    => $p['nama'],
                        'nip'     => $p['nip'] ?? '',
                        'jabatan' => $p['jabatan'] ?? '',
                        'skpd'    => $item['nama'] ?? $skpd,
                    ];
                }
            } elseif (!empty($item['pj_nama'])) {
                $list[] = [
                    'nama'    => $item['pj_nama'],
                    'nip'     => $item['pj_nip'] ?? '',
                    'jabatan' => $item['pj_jabatan'] ?? '',
                    'skpd'    => $item['nama'] ?? $skpd,
                ];
            }
        }

        return collect($list)
            ->unique(fn($i) => strtolower(trim($i['nama'])))
            ->values()
            ->all();
    }

    /**
     * Tampilkan katalog data Mutasi Eksternal (Transfer Antar-OPD / Pelimpahan SKPD).
     */
    public function index(Request $request)
    {
        if (Auth::user()?->role === 'sub_admin') {
            abort(403, 'Akses Ditolak: Sub Admin Ruangan tidak memiliki wewenang untuk mengakses Mutasi Eksternal.');
        }

        Carbon::setLocale('id');

        // Pastikan seluruh data pelimpahan di tabel astaps tersinkronisasi ke mutasi_eksternals
        $this->syncLegacyAstapPelimpahan();
        $this->syncReklasMutasiKeluar();

        // Ambil data aktif dari tabel mutasi_eksternals
        $records = MutasiEksternal::with([
                'astap.registers', 
                'astap.jenisAstap', 
                'unit', 
                'user',
                'mutasiRegisters.register.astap.jenisAstap',
                'mutasiRegisters.register.unit'
            ])
            ->where('is_deleted', 0)
            ->latest('tanggal_mutasi')
            ->latest('id')
            ->get();

        $mutasiEksternals = $records->map(function ($m) {
            $astap = $m->astap;
            $isKeluar = ($m->tipe === 'keluar');
            $nomorBamb = $m->nomor_bamb ?: ($astap?->bast_dokumen_nomor ?: ($isKeluar ? 'BAST-KELUAR-' . str_pad($m->id, 4, '0', STR_PAD_LEFT) : 'BAMB-SKPD-' . str_pad($m->id, 4, '0', STR_PAD_LEFT)));

            // Format tanggal
            $tglRaw = $m->tanggal_mutasi ? $m->tanggal_mutasi->format('Y-m-d') : ($astap?->created_at ? $astap->created_at->format('Y-m-d') : date('Y-m-d'));
            $tglFormatted = '-';
            try {
                $tglFormatted = Carbon::parse($tglRaw)->locale('id')->isoFormat('D MMM Y');
            } catch (\Throwable $e) {
                $tglFormatted = (string) $tglRaw;
            }

            // Pihak Pengirim & Penerima
            $ruangRSUD = $m->unit?->nama ?: ($m->ruangan_tujuan ?: ($astap?->alamat_barang ?: 'Gudang/Ruangan RSUD'));
            $opdAsal = $isKeluar ? ($m->opd_asal ?: 'RSUD Dr. H. Koesnadi') : ($m->opd_asal ?: ($astap?->mutasi_asal ?: 'SKPD / Instansi Luar'));
            $opdTujuan = $isKeluar ? ($m->opd_tujuan ?: 'SKPD / Instansi Luar') : ($m->opd_tujuan ?: ('RSUD dr. H. Koesnadi (' . $ruangRSUD . ')'));

            // Pejabat
            $pjNama = $m->pj_tujuan_nama ?: ($astap?->ppk_nama ?: 'Pengurus Barang RSUD');
            $pjNip  = $m->pj_tujuan_nip ?: ($astap?->ppk_nip ?: '-');

            // Kode 108 & Klasifikasi
            $kode108 = $astap?->kode_108 ?: ($astap?->jenisAstap?->sub_sub_rincian_objek ?: ($astap?->jenisAstap?->jenis ?: '-'));

            // Daftar registers / satuan barang
            $items = [];
            $registers = $astap?->registers ?? collect();

            if ($isKeluar && $m->mutasiRegisters && $m->mutasiRegisters->isNotEmpty()) {
                foreach ($m->mutasiRegisters as $idx => $mr) {
                    $reg = $mr->register;
                    $ast = $reg?->astap;
                    $kd108 = $ast?->kode_108 ?: ($ast?->jenisAstap?->sub_sub_rincian_objek ?: ($ast?->jenisAstap?->jenis ?: '-'));
                    $items[] = [
                        'no'          => $idx + 1,
                        'nama_barang' => $ast?->nama_barang ?: 'Aset RSUD',
                        'nibar'       => $reg?->nibar ?: '-',
                        'kode_108'    => $kd108,
                        'kondisi'     => $mr->kondisi ?: ($reg?->kondisi ?: ($m->kondisi ?: 'Baik')),
                        'ruangan_asal'=> $reg?->unit?->nama ?: ($m->ruangan_tujuan ?: 'RSUD Dr. H. Koesnadi'),
                        'satuan'      => $ast?->satuan ?: ($m->satuan ?: 'Unit'),
                        'volume'      => 1,
                        'nilai_satuan'=> (float)($reg?->harga_satuan ?: ($ast?->harga_satuan ?: 0)),
                    ];
                }
            } else {
                $specJson = is_array($astap?->spesifikasi_json) ? $astap->spesifikasi_json : (is_string($astap?->spesifikasi_json) ? json_decode($astap->spesifikasi_json, true) : []);
                $extractedUnits = self::extractUnitsFromSpec(
                    $specJson ?: [],
                    $astap?->nama_barang ?: 'Barang Mutasi Eksternal',
                    $m->kondisi ?: 'Baik',
                    $ruangRSUD,
                    $m->unit_id
                );

                if ($registers->isNotEmpty()) {
                    foreach ($registers as $idx => $reg) {
                        $unitNama = $extractedUnits[$idx]['nama'] ?? ($astap->nama_barang ?: 'Barang Mutasi');
                        $unitKondisi = $reg->kondisi ?: ($extractedUnits[$idx]['kondisi'] ?? ($m->kondisi ?: 'Baik'));
                        $items[] = [
                            'no'          => $idx + 1,
                            'nama_barang' => $unitNama,
                            'nibar'       => $reg->nibar ?: '-',
                            'kode_108'    => $kode108,
                            'kondisi'     => $unitKondisi,
                            'satuan'      => $astap->satuan ?: ($m->satuan ?: 'Unit'),
                            'volume'      => 1,
                        ];
                    }
                } else {
                    $items[] = [
                        'no'          => 1,
                        'nama_barang' => $astap?->nama_barang ?: 'Barang Mutasi Eksternal',
                        'nibar'       => '-',
                        'kode_108'    => $kode108,
                        'kondisi'     => $m->kondisi ?: 'Baik',
                        'satuan'      => $m->satuan ?: 'Unit',
                        'volume'      => (int) ($m->jumlah_volume ?: 1),
                    ];
                }
            }

            $itemCount = count($items);
            $firstNibar = (!empty($items[0]['nibar']) && $items[0]['nibar'] !== '-') ? $items[0]['nibar'] : ($kode108 ?: '1.3.2.00.00.00');
            $nilaiReal = (float) ($m->nilai_perolehan ?: ($astap?->total_realisasi ?: 0));
            $tahunMasuk = (string) ($astap?->tahun_perolehan ?: date('Y', strtotime($tglRaw)));
            $volAset = (int) ($m->jumlah_volume ?: ($itemCount ?: 1));

            return [
                'id'                        => $astap?->id ?: $m->id,
                'astap_id'                  => $astap?->id ?: $m->astap_id,
                'mutasi_id'                 => $m->id,
                'is_deleted'                => (int) $m->is_deleted,
                'kode'                      => $nomorBamb,
                'jenis'                     => $m->jenis_mutasi ?: ($isKeluar ? 'Transfer Keluar ke OPD' : 'Transfer Antar-OPD'),
                'tipe'                      => $m->tipe ?: 'masuk',
                'kategori_label'            => $isKeluar ? 'Transfer ke OPD (Mutasi Keluar)' : 'Pelimpahan SKPD (Mutasi Masuk)',
                'nama'                      => ($isKeluar && !empty($items[0]['nama_barang']) ? $items[0]['nama_barang'] : ($astap?->nama_barang ?: 'Barang Mutasi')) . ($itemCount > 1 ? " (+{$itemCount} unit)" : ''),
                'nama_murni'                => $isKeluar && !empty($items[0]['nama_barang']) ? $items[0]['nama_barang'] : ($astap?->nama_barang ?: 'Barang Mutasi'),
                'nama_barang'               => $isKeluar && !empty($items[0]['nama_barang']) ? $items[0]['nama_barang'] : ($astap?->nama_barang ?: 'Barang Mutasi'),
                'category'                  => $astap?->category ?: 'KIB B',
                'is_extracomtable'          => (bool) ($astap?->is_extracomtable ?? false),
                'is_reklas'                 => (bool) ($astap?->is_reklas ?? false),
                'jenis_reklas'              => $astap?->jenis_reklas,
                'sumber_dana'               => $astap?->sumber_dana ?: 'pelimpahan_skpd',
                'sumber_dana_raw'           => $astap?->sumber_dana ?: 'pelimpahan_skpd',
                'jenis_aset_nama'           => $astap?->jenisAstap?->nama_jenis ?: ($astap?->jenisAstap?->jenis ?: ($isKeluar ? 'TRANSFER KE OPD' : 'PELIMPAHAN SKPD')),
                'tahun_perolehan'           => $tahunMasuk,
                'volume_satuan'             => $volAset . ' Aset',
                'jumlah_volume'             => $volAset,
                'satuan'                    => $m->satuan ?: ($astap?->satuan ?: 'Unit'),
                'harga_satuan'              => (float) ($astap?->harga_satuan ?: ($volAset > 0 ? ($nilaiReal / $volAset) : $nilaiReal)),
                'jumlah_realisasi'          => 'Rp ' . number_format($nilaiReal, 0, ',', '.'),
                'total_realisasi_num'       => $nilaiReal,
                'item_count'                => $itemCount,
                'items'                     => $items,
                'registers'                 => $registers->toArray(),
                'kode_barang'               => $kode108 ?: $firstNibar,
                'kode_108'                  => $kode108,
                'kondisi'                   => $items[0]['kondisi'] ?? ($m->kondisi ?: 'Baik'),
                'opd_asal'                  => $opdAsal,
                'ruangan_asal'              => $isKeluar ? ($items[0]['ruangan_asal'] ?? ($m->ruangan_tujuan ?: 'RSUD Dr. H. Koesnadi')) : $opdAsal,
                'pj_asal_nama'              => $m->pj_asal_nama ?: ($isKeluar ? 'Pengurus Barang RSUD Dr. H. Koesnadi' : 'Pejabat Penyerah SKPD Pengirim'),
                'pj_asal_nip'               => $m->pj_asal_nip ?: '-',
                'pj_asal_jabatan'           => $m->pj_asal_jabatan ?: ($isKeluar ? 'Pengurus Barang Pengguna' : 'Pengurus Barang / PPK Asal'),
                'opd_tujuan'                => $opdTujuan,
                'ruangan_tujuan'            => $isKeluar ? $opdTujuan : $ruangRSUD,
                'pejabat_opd_tujuan'        => $isKeluar ? ($m->pj_tujuan_nama ?: 'Pejabat Penerima OPD') : $pjNama,
                'nip_pejabat_opd_tujuan'    => $isKeluar ? ($m->pj_tujuan_nip ?: '-') : $pjNip,
                'jabatan_opd_tujuan'        => $m->pj_tujuan_jabatan ?: ($isKeluar ? 'Pejabat Penerima OPD' : 'Pengurus Barang / PPK RSUD Dr. H. Koesnadi'),
                'nomor_sk_dasar'            => $m->nomor_sk_dasar ?: $nomorBamb,
                'alamat_instansi'           => $m->alamat_instansi ?: ($astap?->spesifikasi_json['alamat_instansi'] ?? ''),
                'tgl'                       => $tglFormatted,
                'tgl_raw'                   => (string) $tglRaw,
                'status'                    => $m->status ?: 'Disahkan (Selesai)',
                'alasan_mutasi'             => $m->alasan_mutasi ?: ($isKeluar ? 'Pemindahtanganan / transfer aset RSUD Dr. H. Koesnadi ke SKPD luar.' : 'Pelimpahan aset barang milik daerah dari SKPD/Dinas luar ke RSUD dr. H. Koesnadi.'),
                'tgl_estimasi_kembali'      => $m->tgl_estimasi_kembali ? $m->tgl_estimasi_kembali->format('Y-m-d') : null,
                'dokumen_lampiran'          => $m->dokumen_lampiran ?: ($astap?->spesifikasi_json['dokumen_lampiran'] ?? null),
                'dokumen_lampiran_url'      => ($m->dokumen_lampiran ?: ($astap?->spesifikasi_json['dokumen_lampiran'] ?? null)) ? asset('storage/' . ($m->dokumen_lampiran ?: $astap->spesifikasi_json['dokumen_lampiran'])) : null,
                'nilai_perolehan'           => $nilaiReal,
                'nilai_perolehan_formatted' => 'Rp ' . number_format($nilaiReal, 0, ',', '.'),
                'total_realisasi'           => 'Rp ' . number_format($nilaiReal, 0, ',', '.'),
                'nilai_realisasi'           => $nilaiReal,
                'bast_nomor'                => $nomorBamb,
                'mutasi_nomor_bamb'         => $nomorBamb,
                'mutasi_tanggal'            => (string) $tglRaw,
                'mutasi_asal'               => $opdAsal,
                'mutasi_keterangan'         => $m->alasan_mutasi,
                'ppk_nama'                  => $pjNama,
                'ppk_nip'                   => $pjNip,
                'alamat_barang'             => $astap?->alamat_barang ?: 'RSUD Dr. H. Koesnandi',
                'created_at'                => $astap?->created_at ? $astap->created_at->format('Y-m-d H:i:s') : (string) $tglRaw,
                'spesifikasi_json'          => $astap?->spesifikasi_json ?: [],
            ];
        })->values()->toArray();

        return view('pages.mutasi_eksternal.index', compact('mutasiEksternals'));
    }

    /**
     * Tampilkan form pembuatan mutasi eksternal (Pelimpahan SKPD).
     */
    public function create()
    {
        $dbMaster108 = JenisAstap::getNested108();
        $dbUnits = Unit::orderBy('nama')->get();
        $dbPejabats = self::getDistinctPejabats();
        $dbSkpdDirectory = self::getSkpdDirectory();
        $dbSkpdAsals = array_values(array_unique(array_keys($dbSkpdDirectory)));
        $dbPejabatPenyerahs = self::getDistinctPejabatPenyerahs();

        return view('pages.mutasi_eksternal.form', compact(
            'dbMaster108', 
            'dbUnits', 
            'dbPejabats', 
            'dbSkpdAsals', 
            'dbSkpdDirectory', 
            'dbPejabatPenyerahs'
        ));
    }

    /**
     * Simpan pendaftaran mutasi eksternal ke database.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_barang'        => 'required|string|max:500',
            'jenis_astap_id'     => 'required|integer|exists:jenis_astaps,id',
            'tahun_perolehan'    => 'required|integer|min:1990|max:2100',
            'jumlah_volume'      => 'required|integer|min:1',
            'satuan'             => 'required|string|max:100',
            'total_realisasi'    => 'required|numeric|min:0',
            'triwulan'           => 'required|string|in:TW I,TW II,TW III,TW IV',
            'mutasi_asal'        => 'required|string|max:500',
            'mutasi_nomor_bamb'  => 'required|string|max:255',
            'mutasi_tanggal'     => 'required',
            'mutasi_keterangan'  => 'nullable|string|max:2000',
            'unit_id'            => 'nullable|integer|exists:units,id',
            'alamat_barang'      => 'nullable|string|max:1000',
            'kondisi'            => 'nullable|string|max:50',
            'jenis_mutasi'       => 'nullable|string|max:100',
            'nomor_sk_dasar'     => 'nullable|string|max:255',
            'alamat_instansi'    => 'nullable|string|max:500',
            'pj_asal_nama'       => 'nullable|string|max:255',
            'pj_asal_nip'        => 'nullable|string|max:100',
            'pj_asal_jabatan'    => 'nullable|string|max:255',
            'dokumen_file'       => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'tgl_estimasi_kembali' => 'nullable',
        ]);

        $dokumenPath = null;
        if ($request->hasFile('dokumen_file')) {
            $file = $request->file('dokumen_file');
            $filename = 'BAST_' . time() . '_' . preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $file->getClientOriginalName());
            $dokumenPath = $file->storeAs('dokumen_mutasi_eksternal', $filename, 'public');
        }

        $totalRealisasi = (float) $data['total_realisasi'];
        $totalVolume    = max(1, (int) $data['jumlah_volume']);
        $hargaSatuan    = $totalRealisasi / $totalVolume;
        $tahun          = (int) $data['tahun_perolehan'];
        $kondisiItem    = $data['kondisi'] ?: 'Baik';
        $jenisMutasi    = $request->input('jenis_mutasi', 'Transfer Antar-OPD');

        // Rakit payload master ASTAP
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
            'sumber_dana'               => 'pelimpahan_skpd',
            'mutasi_asal'               => $data['mutasi_asal'],
            'mutasi_nomor_bamb'         => $data['mutasi_nomor_bamb'],
            'mutasi_tanggal'            => $data['mutasi_tanggal'],
            'mutasi_keterangan'         => $data['mutasi_keterangan'] ?? null,
            'bast_dokumen_nomor'        => $data['mutasi_nomor_bamb'],
            'bast_dokumen_tanggal'      => $data['mutasi_tanggal'],
            'keterangan_tambahan'       => $data['mutasi_keterangan'] ?? null,
            'unit_id'                   => $data['unit_id'] ?? null,
            'alamat_barang'             => $data['alamat_barang'] ?: 'RSUD Dr. H. Koesnadi',
            'user_id'                   => Auth::id(),
            'is_extracomtable'          => false,
            'is_reklas'                 => false,
            'is_deleted'                => 0,
            'spesifikasi_json'          => [
                'sumber_dana'     => 'pelimpahan_skpd',
                'skpd_asal'       => $data['mutasi_asal'],
                'nomor_bamb'      => $data['mutasi_nomor_bamb'],
                'tanggal_bamb'    => $data['mutasi_tanggal'],
                'kondisi'         => $kondisiItem,
                'keterangan'      => $data['mutasi_keterangan'] ?? null,
                'nomor_sk_dasar'  => $request->input('nomor_sk_dasar'),
                'alamat_instansi' => $request->input('alamat_instansi'),
                'pj_asal_nama'    => $request->input('pj_asal_nama'),
                'pj_asal_nip'     => $request->input('pj_asal_nip'),
                'pj_asal_jabatan' => $request->input('pj_asal_jabatan'),
            ],
        ];

        $specJson = $astapPayload['spesifikasi_json'];

        // Handle stringified or array spesifikasi_json
        if ($request->has('spesifikasi_json')) {
            $incomingSpec = $request->input('spesifikasi_json');
            if (is_string($incomingSpec)) {
                $decoded = json_decode($incomingSpec, true);
                if (is_array($decoded)) {
                    $incomingSpec = $decoded;
                }
            }
            if (is_array($incomingSpec)) {
                $specJson = array_merge($specJson, $incomingSpec);
            }
        }

        if ($dokumenPath) {
            $specJson['dokumen_lampiran'] = $dokumenPath;
        }

        $repeaterKeys = ['tanah_items', 'mesin_items', 'gedung_items', 'jaringan_items', 'lainnya_items', 'atb_items', 'kdp_items', 'merk', 'type', 'ukuran', 'bahan', 'no_pabrik', 'no_rangka', 'no_mesin', 'no_polisi', 'sertifikat_nomor'];
        foreach ($repeaterKeys as $rk) {
            if ($request->has($rk) && !is_null($request->input($rk))) {
                $val = $request->input($rk);
                if (is_string($val) && (str_starts_with(trim($val), '[') || str_starts_with(trim($val), '{'))) {
                    $decoded = json_decode($val, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $val = $decoded;
                    }
                }
                $specJson[$rk] = $val;
            }
        }

        // Unpack KIB A: Tanah
        if (!empty($specJson['tanah_items']) && is_array($specJson['tanah_items']) && count($specJson['tanah_items']) > 0) {
            $tItems = $specJson['tanah_items'];
            $firstT = $tItems[0];
            $totalLuas = array_sum(array_map(fn($it) => (float)($it['tanah_luas_m2'] ?? 0), $tItems));
            $allSertifikat = array_filter(array_map(fn($it) => $it['tanah_sertifikat_no'] ?? ($it['tanah_sertifikat_nomor'] ?? null), $tItems));
            $specJson['luas_m2'] = $totalLuas;
            $specJson['hak_tanah'] = $firstT['tanah_hak'] ?? 'Hak Pakai';
            $specJson['sertifikat_no'] = count($allSertifikat) > 0 ? implode(', ', $allSertifikat) : ($firstT['tanah_sertifikat_no'] ?? ($firstT['tanah_sertifikat_nomor'] ?? null));
            $specJson['sertifikat_tgl'] = $firstT['tanah_sertifikat_tgl'] ?? ($firstT['tanah_sertifikat_tanggal'] ?? null);
            $specJson['penggunaan'] = $firstT['tanah_penggunaan'] ?? null;
            $specJson['tanah_jumlah_bidang'] = count($tItems);
        }

        // Unpack KIB B: Peralatan & Mesin
        if (!empty($specJson['mesin_items']) && is_array($specJson['mesin_items']) && count($specJson['mesin_items']) > 0) {
            $firstM = $specJson['mesin_items'][0];
            $specJson['merk'] = $firstM['mesin_merk'] ?? ($firstM['merk'] ?? ($specJson['merk'] ?? ''));
            $specJson['type'] = $firstM['mesin_type'] ?? ($firstM['type'] ?? ($specJson['type'] ?? ''));
            $specJson['ukuran'] = $firstM['mesin_ukuran'] ?? ($firstM['ukuran'] ?? ($specJson['ukuran'] ?? ''));
            $specJson['bahan'] = $firstM['mesin_bahan'] ?? ($firstM['bahan'] ?? ($specJson['bahan'] ?? ''));
            $specJson['no_pabrik'] = $firstM['mesin_no_pabrik'] ?? ($firstM['no_pabrik'] ?? ($specJson['no_pabrik'] ?? ''));
            $specJson['no_rangka'] = $firstM['mesin_no_rangka'] ?? ($firstM['no_rangka'] ?? ($specJson['no_rangka'] ?? ''));
            $specJson['no_mesin'] = $firstM['mesin_no_mesin'] ?? ($firstM['no_mesin'] ?? ($specJson['no_mesin'] ?? ''));
            $specJson['no_polisi'] = $firstM['mesin_no_polisi'] ?? ($firstM['no_polisi'] ?? ($specJson['no_polisi'] ?? ''));
            $specJson['no_bpkb'] = $firstM['mesin_no_bpkb'] ?? ($firstM['no_bpkb'] ?? ($specJson['no_bpkb'] ?? ''));
        }

        // Unpack KIB C: Gedung & Bangunan
        if (!empty($specJson['gedung_items']) && is_array($specJson['gedung_items']) && count($specJson['gedung_items']) > 0) {
            $gItems = $specJson['gedung_items'];
            $firstG = $gItems[0];
            $totalLuasGedung = array_sum(array_map(fn($it) => (float)($it['gedung_luas_m2'] ?? 0), $gItems));
            $specJson['gedung_luas_m2'] = $totalLuasGedung;
            $specJson['luas_m2'] = $totalLuasGedung;
            $specJson['gedung_bertingkat'] = $firstG['gedung_bertingkat'] ?? 'Tidak';
            $specJson['gedung_beton'] = $firstG['gedung_beton'] ?? 'Beton';
            $specJson['gedung_status_tanah'] = $firstG['gedung_status_tanah'] ?? 'Tanah Pemda';
            $specJson['gedung_dokumen_no'] = $firstG['gedung_dokumen_no'] ?? ($firstG['gedung_dokumen_nomor'] ?? null);
        }

        // Unpack KIB D: Jalan, Irigasi & Jaringan
        if (!empty($specJson['jaringan_items']) && is_array($specJson['jaringan_items']) && count($specJson['jaringan_items']) > 0) {
            $jItems = $specJson['jaringan_items'];
            $firstJ = $jItems[0];
            $specJson['jaringan_konstruksi'] = $firstJ['jaringan_konstruksi'] ?? null;
            $specJson['jaringan_panjang_m'] = $firstJ['jaringan_panjang_m'] ?? null;
            $specJson['jaringan_luas_m2'] = $firstJ['jaringan_luas_m2'] ?? null;
        }

        // Unpack KIB E: Aset Tetap Lainnya
        if (!empty($specJson['lainnya_items']) && is_array($specJson['lainnya_items']) && count($specJson['lainnya_items']) > 0) {
            $lItems = $specJson['lainnya_items'];
            $firstL = $lItems[0];
            $specJson['lainnya_judul'] = $firstL['lainnya_judul'] ?? ($firstL['lainnya_nama_barang'] ?? null);
            $specJson['lainnya_nama_barang'] = $firstL['lainnya_nama_barang'] ?? ($firstL['lainnya_judul'] ?? null);
            $specJson['lainnya_kode_barang'] = $firstL['lainnya_kode_barang'] ?? null;
            $specJson['kib_e_type'] = $firstL['kib_e_type'] ?? 'buku';
            $specJson['is_extracom'] = !empty($firstL['is_extracom']);
            $specJson['lainnya_pencipta'] = $firstL['lainnya_pencipta'] ?? null;
            $specJson['lainnya_spesifikasi'] = $firstL['lainnya_spesifikasi'] ?? null;
            $specJson['lainnya_tahun'] = $firstL['lainnya_tahun'] ?? null;
            $specJson['lainnya_ukuran'] = $firstL['lainnya_ukuran'] ?? null;
            $specJson['lainnya_asal_daerah'] = $firstL['lainnya_asal_daerah'] ?? null;
            $specJson['lainnya_bahan'] = $firstL['lainnya_bahan'] ?? null;
            $specJson['lainnya_no_pabrik'] = $firstL['lainnya_no_pabrik'] ?? null;
            $specJson['lainnya_keterangan'] = $firstL['lainnya_keterangan'] ?? null;
            $specJson['ruang_pemegang'] = $firstL['ruang_pemegang'] ?? null;
        }

        $ppkNama = $request->input('ppk_nama', 'BUDI HARTONO, S.Sos');
        $ppkNip  = $request->input('ppk_nip', '197602292008011010');
        if ($request->filled('ppk_nama')) {
            $astapPayload['ppk_nama'] = $ppkNama;
            $specJson['ppk_nama'] = $ppkNama;
        }
        if ($request->filled('ppk_nip')) {
            $astapPayload['ppk_nip'] = $ppkNip;
            $specJson['ppk_nip'] = $ppkNip;
        }
        $astapPayload['spesifikasi_json'] = $specJson;

        // Validasi batasan nilai satuan Ekstrakomtabel (Extracom <= Rp 300.000)
        $mesinItems = $specJson['mesin_items'] ?? [];
        if (is_array($mesinItems)) {
            foreach ($mesinItems as $idx => $m) {
                if (!empty($m['is_extracom']) && (float)($m['mesin_nilai_satuan'] ?? 0) > 300000) {
                    $namaItem = $m['mesin_nama_barang'] ?? ('Item Mesin #' . ($idx + 1));
                    $msg = "Nilai satuan untuk barang Ekstrakomtabel (Extracom) '{$namaItem}' tidak boleh melebihi Rp 300.000.";
                    if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                        return response()->json(['success' => false, 'message' => $msg], 422);
                    }
                    return back()->withInput()->withErrors(['total_realisasi' => $msg]);
                }
            }
        }
        $lainnyaItems = $specJson['lainnya_items'] ?? [];
        if (is_array($lainnyaItems)) {
            foreach ($lainnyaItems as $idx => $l) {
                if (!empty($l['is_extracom']) && (float)($l['lainnya_nilai_satuan'] ?? 0) > 300000) {
                    $namaItem = $l['lainnya_nama_barang'] ?? ($l['lainnya_judul'] ?? ('Item Lainnya #' . ($idx + 1)));
                    $msg = "Nilai satuan untuk barang Ekstrakomtabel (Extracom) '{$namaItem}' tidak boleh melebihi Rp 300.000.";
                    if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                        return response()->json(['success' => false, 'message' => $msg], 422);
                    }
                    return back()->withInput()->withErrors(['total_realisasi' => $msg]);
                }
            }
        }

        $isExtracom = false;
        if (!empty($specJson['is_extracom'])) {
            $isExtracom = true;
        } elseif (!empty($specJson['mesin_items']) && is_array($specJson['mesin_items'])) {
            $isExtracom = collect($specJson['mesin_items'])->contains(fn($it) => !empty($it['is_extracom']));
        } elseif (!empty($specJson['lainnya_items']) && is_array($specJson['lainnya_items'])) {
            $isExtracom = collect($specJson['lainnya_items'])->contains(fn($it) => !empty($it['is_extracom']));
        }
        $astapPayload['is_extracomtable'] = $isExtracom;

        $unitModel = !empty($data['unit_id']) ? Unit::find($data['unit_id']) : null;
        $ruangNama = $unitModel ? $unitModel->nama : ($data['alamat_barang'] ?: 'RSUD Dr. H. Koesnadi');

        // Ekstrak rincian spesifik unit individual dan kondisi dominan
        $units = self::extractUnitsFromSpec($specJson, $data['nama_barang'], $kondisiItem, $ruangNama, $data['unit_id'] ?? null);
        $dominantKondisi = self::determineDominantCondition($units, $kondisiItem);
        $kondisiItem = $dominantKondisi;
        $specJson['kondisi'] = $dominantKondisi;
        $astapPayload['spesifikasi_json'] = $specJson;

        $item = DB::transaction(function () use ($astapPayload, $data, $totalVolume, $totalRealisasi, $tahun, $kondisiItem, $dominantKondisi, $units, $jenisMutasi, $ruangNama, $ppkNama, $ppkNip, $dokumenPath, $request) {
            // 1. Simpan ke tabel master astaps
            $item = Astap::create($astapPayload);

            // 2. Simpan ke tabel mutasi_eksternals (Database Mutasi Eksternal Utama)
            MutasiEksternal::create([
                'astap_id'            => $item->id,
                'nomor_bamb'          => $data['mutasi_nomor_bamb'],
                'tanggal_mutasi'      => $data['mutasi_tanggal'],
                'jenis_mutasi'        => $jenisMutasi,
                'tipe'                => 'masuk',
                'opd_asal'            => $data['mutasi_asal'],
                'alamat_instansi'     => $request->input('alamat_instansi'),
                'opd_tujuan'          => 'RSUD dr. H. Koesnadi (' . $ruangNama . ')',
                'unit_id'             => $data['unit_id'] ?? null,
                'ruangan_tujuan'      => $ruangNama,
                'pj_asal_nama'        => $request->input('pj_asal_nama') ?: 'Pejabat Penyerah OPD Pengirim',
                'pj_asal_nip'         => $request->input('pj_asal_nip') ?: '-',
                'pj_asal_jabatan'     => $request->input('pj_asal_jabatan') ?: 'Pengurus Barang / PPK Asal',
                'pj_tujuan_nama'      => $ppkNama,
                'pj_tujuan_nip'       => $ppkNip,
                'pj_tujuan_jabatan'   => 'Pengurus Barang Pengguna RSUD Dr. H. Koesnadi',
                'nomor_sk_dasar'      => $request->input('nomor_sk_dasar') ?: null,
                'tgl_estimasi_kembali'=> $request->input('tgl_estimasi_kembali'),
                'dokumen_lampiran'    => $dokumenPath,
                'status'              => 'Disahkan (Selesai)',
                'jumlah_volume'       => $totalVolume,
                'satuan'              => $data['satuan'],
                'nilai_perolehan'     => $totalRealisasi,
                'kondisi'             => $dominantKondisi,
                'alasan_mutasi'       => $data['mutasi_keterangan'] ?? null,
                'user_id'             => Auth::id(),
                'is_deleted'          => 0,
            ]);

            // 3. Simpan ke extension table legacy astap_pelimpahan_skpds
            AstapPelimpahanSkpd::create([
                'astap_id'        => $item->id,
                'skpd_asal'       => $data['mutasi_asal'],
                'alamat_instansi' => $request->input('alamat_instansi'),
                'nomor_bamb'      => $data['mutasi_nomor_bamb'],
                'tanggal_bamb'    => $data['mutasi_tanggal'],
                'nilai_perolehan' => $totalRealisasi,
                'keterangan'      => $data['mutasi_keterangan'] ?? null,
            ]);

            // 4. Generate nomor register unik NIBAR 45 digit untuk setiap unit aset dengan kondisi dan ruang masing-masing
            $ja = JenisAstap::find($data['jenis_astap_id']);
            $kode108Raw = $ja ? ($ja->sub_sub_rincian_objek ?: $ja->jenis) : '1.3.2.00.00.00';
            $kode108Clean = str_pad(substr(str_replace('.', '', $kode108Raw), 0, 12), 12, '0', STR_PAD_RIGHT);

            $maxRegInt = AstapRegister::where('tahun_perolehan', $tahun)
                ->whereHas('astap', fn($sq) => $sq->where('jenis_astap_id', $data['jenis_astap_id']))
                ->max('no_register_int') ?? 0;

            $runningRegNum = (int) $maxRegInt;
            for ($i = 0; $i < $totalVolume; $i++) {
                $runningRegNum++;
                $noRegStr = str_pad($runningRegNum, 7, '0', STR_PAD_LEFT);
                $nibar = "1201351102000000280000{$tahun}{$kode108Clean}{$noRegStr}";

                while (AstapRegister::where('nibar', $nibar)->exists()) {
                    $runningRegNum++;
                    $noRegStr = str_pad($runningRegNum, 7, '0', STR_PAD_LEFT);
                    $nibar = "1201351102000000280000{$tahun}{$kode108Clean}{$noRegStr}";
                }

                $uKondisi = $units[$i]['kondisi'] ?? $dominantKondisi;
                $uRuang   = $units[$i]['ruang'] ?? $ruangNama;
                $uUnitId  = $units[$i]['unit_id'] ?? ($data['unit_id'] ?? null);

                $qrPath = "/scan/{$nibar}";
                AstapRegister::create([
                    'astap_id'        => $item->id,
                    'unit_id'         => $uUnitId,
                    'tahun_perolehan' => $tahun,
                    'no_register_int' => $runningRegNum,
                    'no_register'     => $nibar,
                    'nibar'           => $nibar,
                    'qr_code_path'    => $qrPath,
                    'ruang_pemegang'  => $uRuang,
                    'kondisi'         => $uKondisi,
                    'status'          => 'Aktif',
                    'is_deleted'      => 0,
                ]);
            }

            // 5. Kirim Notifikasi Sistem
            try {
                NotificationService::sendToAdminAndMaster(
                    "Mutasi Eksternal Masuk: {$item->nama_barang}",
                    "BAST: {$data['mutasi_nomor_bamb']} • Dari {$data['mutasi_asal']} ke {$ruangNama}",
                    'mutasi',
                    route('mutasi.eksternal')
                );
            } catch (\Throwable $e) {
                Log::warning("Gagal kirim notif mutasi eksternal: " . $e->getMessage());
            }

            return $item;
        });

        $targetRedirect = $request->input('from') === 'eksternal' ? route('mutasi.eksternal') : route('astap.index');

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success'  => true,
                'message'  => 'Data Mutasi Eksternal "' . $item->nama_barang . '" berhasil disimpan ke database SIMAT-RK!',
                'redirect' => $targetRedirect
            ]);
        }

        return redirect()->to($targetRedirect)
            ->with('success', 'Data Mutasi Eksternal "' . $item->nama_barang . '" berhasil ditambahkan.');
    }

    /**
     * Tampilkan form edit mutasi eksternal.
     */
    public function edit(Request $request, $id)
    {
        $astap = Astap::with(['registers', 'jenisAstap', 'pelimpahanSkpd', 'mutasiEksternal', 'unit'])->findOrFail($id);
        $dbMaster108 = JenisAstap::getNested108();
        $dbUnits = Unit::orderBy('nama')->get();
        $dbPejabats = self::getDistinctPejabats();
        $dbSkpdDirectory = self::getSkpdDirectory();
        $dbSkpdAsals = array_values(array_unique(array_keys($dbSkpdDirectory)));
        $dbPejabatPenyerahs = self::getDistinctPejabatPenyerahs();

        return view('pages.mutasi_eksternal.form', compact(
            'astap', 
            'dbMaster108', 
            'dbUnits', 
            'dbPejabats', 
            'dbSkpdAsals', 
            'dbSkpdDirectory', 
            'dbPejabatPenyerahs'
        ));
    }

    /**
     * Update data mutasi eksternal.
     */
    public function update(Request $request, $id)
    {
        $item = Astap::with(['registers', 'pelimpahanSkpd', 'mutasiEksternal'])->findOrFail($id);

        $data = $request->validate([
            'nama_barang'        => 'required|string|max:500',
            'jenis_astap_id'     => 'required|integer|exists:jenis_astaps,id',
            'tahun_perolehan'    => 'required|integer|min:1990|max:2100',
            'jumlah_volume'      => 'required|integer|min:1',
            'satuan'             => 'required|string|max:100',
            'total_realisasi'    => 'required|numeric|min:0',
            'triwulan'           => 'required|string|in:TW I,TW II,TW III,TW IV',
            'mutasi_asal'        => 'required|string|max:500',
            'mutasi_nomor_bamb'  => 'required|string|max:255',
            'mutasi_tanggal'     => 'required',
            'mutasi_keterangan'  => 'nullable|string|max:2000',
            'unit_id'            => 'nullable|integer|exists:units,id',
            'alamat_barang'      => 'nullable|string|max:1000',
            'kondisi'            => 'nullable|string|max:50',
            'jenis_mutasi'       => 'nullable|string|max:100',
            'nomor_sk_dasar'     => 'nullable|string|max:255',
            'alamat_instansi'    => 'nullable|string|max:500',
            'pj_asal_nama'       => 'nullable|string|max:255',
            'pj_asal_nip'        => 'nullable|string|max:100',
            'pj_asal_jabatan'    => 'nullable|string|max:255',
            'dokumen_file'       => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'tgl_estimasi_kembali' => 'nullable',
        ]);

        $dokumenPath = $item->mutasiEksternal?->dokumen_lampiran;
        if ($request->hasFile('dokumen_file')) {
            $file = $request->file('dokumen_file');
            $filename = 'BAST_' . time() . '_' . preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $file->getClientOriginalName());
            $newPath = $file->storeAs('dokumen_mutasi_eksternal', $filename, 'public');
            if ($dokumenPath && Storage::disk('public')->exists($dokumenPath)) {
                Storage::disk('public')->delete($dokumenPath);
            }
            $dokumenPath = $newPath;
        }

        $totalRealisasi = (float) $data['total_realisasi'];
        $totalVolume    = max(1, (int) $data['jumlah_volume']);
        $hargaSatuan    = $totalRealisasi / $totalVolume;
        $tahun          = (int) $data['tahun_perolehan'];
        $kondisiItem    = $data['kondisi'] ?: 'Baik';
        $jenisMutasi    = $request->input('jenis_mutasi', 'Transfer Antar-OPD');

        $astapPayload = [
            'nama_barang'               => $data['nama_barang'],
            'jenis_astap_id'            => $data['jenis_astap_id'],
            'tahun_perolehan'           => $tahun,
            'jumlah_volume'             => $totalVolume,
            'satuan'                    => $data['satuan'],
            'harga_satuan'              => $hargaSatuan,
            'jumlah_realisasi'          => $totalRealisasi,
            'total_realisasi'           => $totalRealisasi,
            'triwulan'                  => $data['triwulan'],
            'mutasi_asal'               => $data['mutasi_asal'],
            'mutasi_nomor_bamb'         => $data['mutasi_nomor_bamb'],
            'mutasi_tanggal'            => $data['mutasi_tanggal'],
            'mutasi_keterangan'         => $data['mutasi_keterangan'] ?? null,
            'bast_dokumen_nomor'        => $data['mutasi_nomor_bamb'],
            'bast_dokumen_tanggal'      => $data['mutasi_tanggal'],
            'keterangan_tambahan'       => $data['mutasi_keterangan'] ?? null,
            'unit_id'                   => $data['unit_id'] ?? null,
            'alamat_barang'             => $data['alamat_barang'] ?: 'RSUD Dr. H. Koesnadi',
        ];

        $specJson = is_array($item->spesifikasi_json) ? $item->spesifikasi_json : [];
        $specJson['sumber_dana'] = 'pelimpahan_skpd';
        $specJson['skpd_asal'] = $data['mutasi_asal'];
        $specJson['nomor_bamb'] = $data['mutasi_nomor_bamb'];
        $specJson['tanggal_bamb'] = $data['mutasi_tanggal'];
        $specJson['kondisi'] = $kondisiItem;
        $specJson['keterangan'] = $data['mutasi_keterangan'] ?? null;
        $specJson['nomor_sk_dasar'] = $request->input('nomor_sk_dasar');
        $specJson['alamat_instansi'] = $request->input('alamat_instansi');
        $specJson['pj_asal_nama'] = $request->input('pj_asal_nama');
        $specJson['pj_asal_nip'] = $request->input('pj_asal_nip');
        $specJson['pj_asal_jabatan'] = $request->input('pj_asal_jabatan');
        $existingDoc = $item->mutasiEksternal?->dokumen_lampiran ?: ($specJson['dokumen_lampiran'] ?? null);

        // Handle stringified or array spesifikasi_json
        if ($request->has('spesifikasi_json')) {
            $incomingSpec = $request->input('spesifikasi_json');
            if (is_string($incomingSpec)) {
                $decoded = json_decode($incomingSpec, true);
                if (is_array($decoded)) {
                    $incomingSpec = $decoded;
                }
            }
            if (is_array($incomingSpec)) {
                $specJson = array_merge($specJson, $incomingSpec);
            }
        }

        if ($dokumenPath) {
            $specJson['dokumen_lampiran'] = $dokumenPath;
        } elseif (!empty($existingDoc)) {
            $specJson['dokumen_lampiran'] = $existingDoc;
        }

        $repeaterKeys = ['tanah_items', 'mesin_items', 'gedung_items', 'jaringan_items', 'lainnya_items', 'atb_items', 'kdp_items', 'merk', 'type', 'ukuran', 'bahan', 'no_pabrik', 'no_rangka', 'no_mesin', 'no_polisi', 'sertifikat_nomor'];
        foreach ($repeaterKeys as $rk) {
            if ($request->has($rk) && !is_null($request->input($rk))) {
                $val = $request->input($rk);
                if (is_string($val) && (str_starts_with(trim($val), '[') || str_starts_with(trim($val), '{'))) {
                    $decoded = json_decode($val, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $val = $decoded;
                    }
                }
                $specJson[$rk] = $val;
            }
        }

        // Unpack KIB A: Tanah
        if (!empty($specJson['tanah_items']) && is_array($specJson['tanah_items']) && count($specJson['tanah_items']) > 0) {
            $tItems = $specJson['tanah_items'];
            $firstT = $tItems[0];
            $totalLuas = array_sum(array_map(fn($it) => (float)($it['tanah_luas_m2'] ?? 0), $tItems));
            $allSertifikat = array_filter(array_map(fn($it) => $it['tanah_sertifikat_no'] ?? ($it['tanah_sertifikat_nomor'] ?? null), $tItems));
            $specJson['luas_m2'] = $totalLuas;
            $specJson['hak_tanah'] = $firstT['tanah_hak'] ?? 'Hak Pakai';
            $specJson['sertifikat_no'] = count($allSertifikat) > 0 ? implode(', ', $allSertifikat) : ($firstT['tanah_sertifikat_no'] ?? ($firstT['tanah_sertifikat_nomor'] ?? null));
            $specJson['sertifikat_tgl'] = $firstT['tanah_sertifikat_tgl'] ?? ($firstT['tanah_sertifikat_tanggal'] ?? null);
            $specJson['penggunaan'] = $firstT['tanah_penggunaan'] ?? null;
            $specJson['tanah_jumlah_bidang'] = count($tItems);
        }

        // Unpack KIB B: Peralatan & Mesin
        if (!empty($specJson['mesin_items']) && is_array($specJson['mesin_items']) && count($specJson['mesin_items']) > 0) {
            $firstM = $specJson['mesin_items'][0];
            $specJson['merk'] = $firstM['mesin_merk'] ?? ($firstM['merk'] ?? ($specJson['merk'] ?? ''));
            $specJson['type'] = $firstM['mesin_type'] ?? ($firstM['type'] ?? ($specJson['type'] ?? ''));
            $specJson['ukuran'] = $firstM['mesin_ukuran'] ?? ($firstM['ukuran'] ?? ($specJson['ukuran'] ?? ''));
            $specJson['bahan'] = $firstM['mesin_bahan'] ?? ($firstM['bahan'] ?? ($specJson['bahan'] ?? ''));
            $specJson['no_pabrik'] = $firstM['mesin_no_pabrik'] ?? ($firstM['no_pabrik'] ?? ($specJson['no_pabrik'] ?? ''));
            $specJson['no_rangka'] = $firstM['mesin_no_rangka'] ?? ($firstM['no_rangka'] ?? ($specJson['no_rangka'] ?? ''));
            $specJson['no_mesin'] = $firstM['mesin_no_mesin'] ?? ($firstM['no_mesin'] ?? ($specJson['no_mesin'] ?? ''));
            $specJson['no_polisi'] = $firstM['mesin_no_polisi'] ?? ($firstM['no_polisi'] ?? ($specJson['no_polisi'] ?? ''));
            $specJson['no_bpkb'] = $firstM['mesin_no_bpkb'] ?? ($firstM['no_bpkb'] ?? ($specJson['no_bpkb'] ?? ''));
        }

        // Unpack KIB C: Gedung & Bangunan
        if (!empty($specJson['gedung_items']) && is_array($specJson['gedung_items']) && count($specJson['gedung_items']) > 0) {
            $gItems = $specJson['gedung_items'];
            $firstG = $gItems[0];
            $totalLuasGedung = array_sum(array_map(fn($it) => (float)($it['gedung_luas_m2'] ?? 0), $gItems));
            $specJson['gedung_luas_m2'] = $totalLuasGedung;
            $specJson['luas_m2'] = $totalLuasGedung;
            $specJson['gedung_bertingkat'] = $firstG['gedung_bertingkat'] ?? 'Tidak';
            $specJson['gedung_beton'] = $firstG['gedung_beton'] ?? 'Beton';
            $specJson['gedung_status_tanah'] = $firstG['gedung_status_tanah'] ?? 'Tanah Pemda';
            $specJson['gedung_dokumen_no'] = $firstG['gedung_dokumen_no'] ?? ($firstG['gedung_dokumen_nomor'] ?? null);
        }

        // Unpack KIB D: Jalan, Irigasi & Jaringan
        if (!empty($specJson['jaringan_items']) && is_array($specJson['jaringan_items']) && count($specJson['jaringan_items']) > 0) {
            $jItems = $specJson['jaringan_items'];
            $firstJ = $jItems[0];
            $specJson['jaringan_konstruksi'] = $firstJ['jaringan_konstruksi'] ?? null;
            $specJson['jaringan_panjang_m'] = $firstJ['jaringan_panjang_m'] ?? null;
            $specJson['jaringan_luas_m2'] = $firstJ['jaringan_luas_m2'] ?? null;
        }

        // Unpack KIB E: Aset Tetap Lainnya
        if (!empty($specJson['lainnya_items']) && is_array($specJson['lainnya_items']) && count($specJson['lainnya_items']) > 0) {
            $lItems = $specJson['lainnya_items'];
            $firstL = $lItems[0];
            $specJson['lainnya_judul'] = $firstL['lainnya_judul'] ?? ($firstL['lainnya_nama_barang'] ?? null);
            $specJson['lainnya_nama_barang'] = $firstL['lainnya_nama_barang'] ?? ($firstL['lainnya_judul'] ?? null);
            $specJson['lainnya_kode_barang'] = $firstL['lainnya_kode_barang'] ?? null;
            $specJson['kib_e_type'] = $firstL['kib_e_type'] ?? 'buku';
            $specJson['is_extracom'] = !empty($firstL['is_extracom']);
            $specJson['lainnya_pencipta'] = $firstL['lainnya_pencipta'] ?? null;
            $specJson['lainnya_spesifikasi'] = $firstL['lainnya_spesifikasi'] ?? null;
            $specJson['lainnya_tahun'] = $firstL['lainnya_tahun'] ?? null;
            $specJson['lainnya_ukuran'] = $firstL['lainnya_ukuran'] ?? null;
            $specJson['lainnya_asal_daerah'] = $firstL['lainnya_asal_daerah'] ?? null;
            $specJson['lainnya_bahan'] = $firstL['lainnya_bahan'] ?? null;
            $specJson['lainnya_no_pabrik'] = $firstL['lainnya_no_pabrik'] ?? null;
            $specJson['lainnya_keterangan'] = $firstL['lainnya_keterangan'] ?? null;
            $specJson['ruang_pemegang'] = $firstL['ruang_pemegang'] ?? null;
        }

        $fallbackPbNama = ($item->ppk_nama && !str_contains(strtolower($item->ppk_nama), 'yus')) ? $item->ppk_nama : 'BUDI HARTONO, S.Sos';
        $fallbackPbNip  = ($item->ppk_nip && !str_contains($item->ppk_nip, '19771002') && !str_contains($item->ppk_nip, '19690412')) ? $item->ppk_nip : '197602292008011010';
        $ppkNama = $request->input('ppk_nama', $fallbackPbNama);
        $ppkNip  = $request->input('ppk_nip', $fallbackPbNip);
        if ($request->filled('ppk_nama')) {
            $astapPayload['ppk_nama'] = $ppkNama;
            $specJson['ppk_nama'] = $ppkNama;
        }
        if ($request->filled('ppk_nip')) {
            $astapPayload['ppk_nip'] = $ppkNip;
            $specJson['ppk_nip'] = $ppkNip;
        }
        $astapPayload['spesifikasi_json'] = $specJson;

        // Validasi batasan nilai satuan Ekstrakomtabel (Extracom <= Rp 300.000)
        $mesinItems = $specJson['mesin_items'] ?? [];
        if (is_array($mesinItems)) {
            foreach ($mesinItems as $idx => $m) {
                if (!empty($m['is_extracom']) && (float)($m['mesin_nilai_satuan'] ?? 0) > 300000) {
                    $namaItem = $m['mesin_nama_barang'] ?? ('Item Mesin #' . ($idx + 1));
                    $msg = "Nilai satuan untuk barang Ekstrakomtabel (Extracom) '{$namaItem}' tidak boleh melebihi Rp 300.000.";
                    if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                        return response()->json(['success' => false, 'message' => $msg], 422);
                    }
                    return back()->withInput()->withErrors(['total_realisasi' => $msg]);
                }
            }
        }
        $lainnyaItems = $specJson['lainnya_items'] ?? [];
        if (is_array($lainnyaItems)) {
            foreach ($lainnyaItems as $idx => $l) {
                if (!empty($l['is_extracom']) && (float)($l['lainnya_nilai_satuan'] ?? 0) > 300000) {
                    $namaItem = $l['lainnya_nama_barang'] ?? ($l['lainnya_judul'] ?? ('Item Lainnya #' . ($idx + 1)));
                    $msg = "Nilai satuan untuk barang Ekstrakomtabel (Extracom) '{$namaItem}' tidak boleh melebihi Rp 300.000.";
                    if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                        return response()->json(['success' => false, 'message' => $msg], 422);
                    }
                    return back()->withInput()->withErrors(['total_realisasi' => $msg]);
                }
            }
        }

        $isExtracom = false;
        if (!empty($specJson['is_extracom'])) {
            $isExtracom = true;
        } elseif (!empty($specJson['mesin_items']) && is_array($specJson['mesin_items'])) {
            $isExtracom = collect($specJson['mesin_items'])->contains(fn($it) => !empty($it['is_extracom']));
        } elseif (!empty($specJson['lainnya_items']) && is_array($specJson['lainnya_items'])) {
            $isExtracom = collect($specJson['lainnya_items'])->contains(fn($it) => !empty($it['is_extracom']));
        }
        $astapPayload['is_extracomtable'] = $isExtracom;

        $unitModel = !empty($data['unit_id']) ? Unit::find($data['unit_id']) : null;
        $ruangNama = $unitModel ? $unitModel->nama : ($data['alamat_barang'] ?: 'RSUD Dr. H. Koesnadi');

        // Ekstrak rincian spesifik unit individual dan kondisi dominan
        $units = self::extractUnitsFromSpec($specJson, $data['nama_barang'], $kondisiItem, $ruangNama, $data['unit_id'] ?? null);
        $dominantKondisi = self::determineDominantCondition($units, $kondisiItem);
        $kondisiItem = $dominantKondisi;
        $specJson['kondisi'] = $dominantKondisi;
        $astapPayload['spesifikasi_json'] = $specJson;

        DB::transaction(function () use ($item, $astapPayload, $data, $totalRealisasi, $totalVolume, $tahun, $kondisiItem, $dominantKondisi, $units, $jenisMutasi, $ruangNama, $ppkNama, $ppkNip, $dokumenPath, $existingDoc, $request) {
            // 1. Update master Astap
            $item->update($astapPayload);

            // 2. Update / Create MutasiEksternal
            MutasiEksternal::updateOrCreate(
                ['astap_id' => $item->id],
                [
                    'nomor_bamb'          => $data['mutasi_nomor_bamb'],
                    'tanggal_mutasi'      => $data['mutasi_tanggal'],
                    'jenis_mutasi'        => $jenisMutasi,
                    'tipe'                => 'masuk',
                    'opd_asal'            => $data['mutasi_asal'],
                    'alamat_instansi'     => $request->input('alamat_instansi'),
                    'opd_tujuan'          => 'RSUD dr. H. Koesnadi (' . $ruangNama . ')',
                    'unit_id'             => $data['unit_id'] ?? null,
                    'ruangan_tujuan'      => $ruangNama,
                    'pj_asal_nama'        => $request->input('pj_asal_nama') ?: ($item->mutasiEksternal?->pj_asal_nama ?: 'Pejabat Penyerah OPD Pengirim'),
                    'pj_asal_nip'         => $request->input('pj_asal_nip') ?: ($item->mutasiEksternal?->pj_asal_nip ?: '-'),
                    'pj_asal_jabatan'     => $request->input('pj_asal_jabatan') ?: ($item->mutasiEksternal?->pj_asal_jabatan ?: 'Pengurus Barang / PPK Asal'),
                    'pj_tujuan_nama'      => $ppkNama,
                    'pj_tujuan_nip'       => $ppkNip,
                    'pj_tujuan_jabatan'   => 'Pengurus Barang Pengguna RSUD Dr. H. Koesnadi',
                    'nomor_sk_dasar'      => $request->input('nomor_sk_dasar') ?: null,
                    'tgl_estimasi_kembali'=> $request->input('tgl_estimasi_kembali'),
                    'dokumen_lampiran'    => $dokumenPath ?: ($existingDoc ?: null),
                    'status'              => 'Disahkan (Selesai)',
                    'jumlah_volume'       => $totalVolume,
                    'satuan'              => $data['satuan'],
                    'nilai_perolehan'     => $totalRealisasi,
                    'kondisi'             => $dominantKondisi,
                    'alasan_mutasi'       => $data['mutasi_keterangan'] ?? null,
                ]
            );

            // 3. Update / Create AstapPelimpahanSkpd (legacy table)
            if ($item->pelimpahanSkpd) {
                $item->pelimpahanSkpd->update([
                    'skpd_asal'       => $data['mutasi_asal'],
                    'alamat_instansi' => $request->input('alamat_instansi'),
                    'nomor_bamb'      => $data['mutasi_nomor_bamb'],
                    'tanggal_bamb'    => $data['mutasi_tanggal'],
                    'nilai_perolehan' => $totalRealisasi,
                    'keterangan'      => $data['mutasi_keterangan'] ?? null,
                ]);
            } else {
                AstapPelimpahanSkpd::create([
                    'astap_id'        => $item->id,
                    'skpd_asal'       => $data['mutasi_asal'],
                    'alamat_instansi' => $request->input('alamat_instansi'),
                    'nomor_bamb'      => $data['mutasi_nomor_bamb'],
                    'tanggal_bamb'    => $data['mutasi_tanggal'],
                    'nilai_perolehan' => $totalRealisasi,
                    'keterangan'      => $data['mutasi_keterangan'] ?? null,
                ]);
            }

            // 4. Update seluruh register aset yang ada secara presisi per unit
            $existingRegs = AstapRegister::where('astap_id', $item->id)->orderBy('id')->get();
            foreach ($existingRegs as $idx => $reg) {
                $uKondisi = $units[$idx]['kondisi'] ?? $dominantKondisi;
                $uRuang   = $units[$idx]['ruang'] ?? $ruangNama;
                $uUnitId  = $units[$idx]['unit_id'] ?? ($data['unit_id'] ?? null);

                $reg->update([
                    'unit_id'        => $uUnitId,
                    'ruang_pemegang' => $uRuang,
                    'kondisi'        => $uKondisi,
                ]);
            }

            // 5. Jika jumlah_volume bertambah melebihi jumlah register saat ini, buat register baru
            $currentRegsCount = $existingRegs->count();
            if ($totalVolume > $currentRegsCount) {
                $ja = JenisAstap::find($data['jenis_astap_id']);
                $kode108Raw = $ja ? ($ja->sub_sub_rincian_objek ?: $ja->jenis) : '1.3.2.00.00.00';
                $kode108Clean = str_pad(substr(str_replace('.', '', $kode108Raw), 0, 12), 12, '0', STR_PAD_RIGHT);

                $maxRegInt = AstapRegister::where('tahun_perolehan', $tahun)
                    ->whereHas('astap', fn($sq) => $sq->where('jenis_astap_id', $data['jenis_astap_id']))
                    ->max('no_register_int') ?? 0;

                $runningRegNum = (int) $maxRegInt;
                for ($i = $currentRegsCount; $i < $totalVolume; $i++) {
                    $runningRegNum++;
                    $noRegStr = str_pad($runningRegNum, 7, '0', STR_PAD_LEFT);
                    $nibar = "1201351102000000280000{$tahun}{$kode108Clean}{$noRegStr}";

                    while (AstapRegister::where('nibar', $nibar)->exists()) {
                        $runningRegNum++;
                        $noRegStr = str_pad($runningRegNum, 7, '0', STR_PAD_LEFT);
                        $nibar = "1201351102000000280000{$tahun}{$kode108Clean}{$noRegStr}";
                    }

                    $uKondisi = $units[$i]['kondisi'] ?? $dominantKondisi;
                    $uRuang   = $units[$i]['ruang'] ?? $ruangNama;
                    $uUnitId  = $units[$i]['unit_id'] ?? ($data['unit_id'] ?? null);

                    $qrPath = "/scan/{$nibar}";
                    AstapRegister::create([
                        'astap_id'        => $item->id,
                        'unit_id'         => $uUnitId,
                        'tahun_perolehan' => $tahun,
                        'no_register_int' => $runningRegNum,
                        'no_register'     => $nibar,
                        'nibar'           => $nibar,
                        'qr_code_path'    => $qrPath,
                        'ruang_pemegang'  => $uRuang,
                        'kondisi'         => $uKondisi,
                        'status'          => 'Aktif',
                        'is_deleted'      => 0,
                    ]);
                }
            }
        });

        $targetRedirect = $request->input('from') === 'eksternal' ? route('mutasi.eksternal') : route('astap.index');

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success'  => true,
                'message'  => 'Data Mutasi Eksternal "' . $item->nama_barang . '" berhasil diperbarui!',
                'redirect' => $targetRedirect
            ]);
        }

        return redirect()->to($targetRedirect)
            ->with('success', 'Data Mutasi Eksternal "' . $item->nama_barang . '" berhasil diperbarui.');
    }

    /**
     * Hapus data mutasi eksternal (Soft Delete).
     */
    public function destroy(Request $request, $id)
    {
        $user = Auth::user();
        $deleterName = $user ? ($user->name . ' (' . ucfirst($user->role ?? 'user') . ')') : 'Administrator';
        $reason = $request->input('alasan', 'Dihapus dari modul Mutasi Eksternal');

        // Coba cari di MutasiEksternal terlebih dahulu (bisa ID mutasi atau astap_id)
        $mutasi = MutasiEksternal::find($id) ?: MutasiEksternal::where('astap_id', $id)->first();
        $astap  = $mutasi ? $mutasi->astap : Astap::find($id);

        if (!$mutasi && !$astap) {
            return response()->json(['success' => false, 'message' => 'Data Mutasi Eksternal tidak ditemukan.'], 404);
        }

        DB::transaction(function () use ($mutasi, $astap, $deleterName, $user, $reason) {
            if ($mutasi && $mutasi->tipe === 'keluar') {
                // Mutasi Keluar ke OPD: soft delete data transaksi mutasi keluar
                $payload = [
                    'is_deleted'    => 1,
                    'deleted_by'    => $deleterName,
                    'deleted_by_id' => $user?->id,
                    'deleted_at'    => now(),
                ];
                if (\Illuminate\Support\Facades\Schema::hasColumn('mutasi_eksternals', 'alasan_hapus')) {
                    $payload['alasan_hapus'] = $reason;
                }
                $mutasi->update($payload);

                // Pulihkan aset ASTAP RSUD kembali ke status Aktif (JANGAN di-soft-delete!)
                if ($astap) {
                    $astap->update([
                        'kondisi'      => 'Baik',
                        'is_reklas'    => 0,
                        'jenis_reklas' => null,
                    ]);

                    // Pulihkan register terkait kembali ke status 'Aktif'
                    AstapRegister::where('astap_id', $astap->id)
                        ->where('status', 'Mutasi Keluar OPD')
                        ->update([
                            'status' => 'Aktif',
                        ]);

                    // Soft-delete juga log transaksi reklasifikasi terkait jika ada
                    \App\Models\AstapReklas::where('astap_id', $astap->id)
                        ->where('jenis_reklas', 'MUTASI_EKSTERNAL')
                        ->where('is_deleted', 0)
                        ->update([
                            'is_deleted'    => 1,
                            'deleted_by'    => $deleterName,
                            'deleted_by_id' => $user?->id,
                            'deleted_at'    => now(),
                        ]);
                }
                return;
            }

            if ($mutasi) {
                $payload = [
                    'is_deleted'    => 1,
                    'deleted_by'    => $deleterName,
                    'deleted_by_id' => $user?->id,
                    'deleted_at'    => now(),
                ];
                if (\Illuminate\Support\Facades\Schema::hasColumn('mutasi_eksternals', 'alasan_hapus')) {
                    $payload['alasan_hapus'] = $reason;
                }
                $mutasi->update($payload);
            }

            if ($astap) {
                $astapPayload = [
                    'is_deleted'    => 1,
                    'deleted_by'    => $deleterName,
                    'deleted_by_id' => $user?->id,
                    'deleted_at'    => now(),
                ];
                if (\Illuminate\Support\Facades\Schema::hasColumn('astaps', 'alasan_hapus')) {
                    $astapPayload['alasan_hapus'] = $reason;
                }
                $astap->update($astapPayload);

                AstapRegister::where('astap_id', $astap->id)->update([
                    'is_deleted'    => 1,
                    'deleted_by'    => $deleterName,
                    'deleted_by_id' => $user?->id,
                    'deleted_at'    => now(),
                ]);
            }
        });

        $msg = 'Data Mutasi Eksternal berhasil dipindahkan ke Recycle Bin.';
        if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest' || $request->isJson()) {
            return response()->json([
                'success' => true,
                'message' => $msg,
            ]);
        }

        return redirect()->route('mutasi.eksternal')->with('success', $msg);
    }

    /**
     * Tampilkan detail transaksi mutasi eksternal (JSON).
     */
    public function show($id)
    {
        $mutasi = MutasiEksternal::with(['astap.registers', 'astap.jenisAstap', 'unit', 'user'])
            ->where('astap_id', $id)
            ->orWhere('id', $id)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data'    => $mutasi
        ]);
    }

    /**
     * Tampilkan lembar cetak resmi Berita Acara Serah Terima (BAST) Pelimpahan BMD dari OPD.
     */
    public function cetak($id)
    {
        $mutasi = MutasiEksternal::with(['astap.registers', 'astap.jenisAstap', 'unit', 'user'])
            ->where('astap_id', $id)
            ->orWhere('id', $id)
            ->firstOrFail();

        return view('pages.mutasi_eksternal.cetak_bast', compact('mutasi'));
    }

    /**
     * Helper privat untuk sinkronisasi otomatis data pelimpahan legacy ke tabel mutasi_eksternals
     */
    private function syncLegacyAstapPelimpahan(): void
    {
        $legacyAstaps = Astap::where('is_deleted', 0)
            ->whereIn('sumber_dana', ['pelimpahan_skpd', 'mutasi_masuk'])
            ->whereDoesntHave('mutasiEksternal')
            ->with(['pelimpahanSkpd', 'unit'])
            ->get();

        foreach ($legacyAstaps as $a) {
            $nomorBamb = $a->pelimpahanSkpd?->nomor_bamb 
                ?: ($a->mutasi_nomor_bamb ?: ($a->bast_dokumen_nomor ?: 'BAMB-SKPD-' . str_pad($a->id, 4, '0', STR_PAD_LEFT)));
            
            $tgl = $a->pelimpahanSkpd?->tanggal_bamb 
                ?: ($a->mutasi_tanggal ?: ($a->bast_dokumen_tanggal ?: ($a->created_at ? $a->created_at->format('Y-m-d') : date('Y-m-d'))));

            $opdAsal = $a->pelimpahanSkpd?->skpd_asal ?: ($a->mutasi_asal ?: 'SKPD / Instansi Luar');
            $ruangNama = $a->unit?->nama ?: ($a->alamat_barang ?: 'Gudang/Ruangan RSUD');
            $nilaiReal = (float) ($a->pelimpahanSkpd?->nilai_perolehan ?: ($a->total_realisasi ?: 0));

            MutasiEksternal::create([
                'astap_id'            => $a->id,
                'nomor_bamb'          => $nomorBamb,
                'tanggal_mutasi'      => $tgl,
                'jenis_mutasi'        => 'Transfer Antar-OPD',
                'tipe'                => 'masuk',
                'opd_asal'            => $opdAsal,
                'alamat_instansi'     => $a->pelimpahanSkpd?->alamat_instansi ?: ($a->spesifikasi_json['alamat_instansi'] ?? null),
                'opd_tujuan'          => 'RSUD dr. H. Koesnadi (' . $ruangNama . ')',
                'unit_id'             => $a->unit_id,
                'ruangan_tujuan'      => $ruangNama,
                'pj_asal_nama'        => 'Pejabat Penyerah OPD Pengirim',
                'pj_asal_nip'         => '-',
                'pj_asal_jabatan'     => 'Pengurus Barang / PPK Asal',
                'pj_tujuan_nama'      => 'BUDI HARTONO, S.Sos',
                'pj_tujuan_nip'       => '197602292008011010',
                'pj_tujuan_jabatan'   => 'Pengurus Barang Pengguna RSUD Dr. H. Koesnadi',
                'nomor_sk_dasar'      => $a->spesifikasi_json['nomor_sk_dasar'] ?? null,
                'status'              => 'Disahkan (Selesai)',
                'jumlah_volume'       => (int) ($a->jumlah_volume ?: 1),
                'satuan'              => $a->satuan ?: 'Unit',
                'nilai_perolehan'     => $nilaiReal,
                'kondisi'             => 'Baik',
                'alasan_mutasi'       => $a->pelimpahanSkpd?->keterangan ?: ($a->mutasi_keterangan ?: 'Pelimpahan aset barang milik daerah.'),
                'user_id'             => $a->user_id,
                'is_deleted'          => 0,
            ]);
        }
    }

    /**
     * Helper privat untuk sinkronisasi otomatis transaksi reklasifikasi Mutasi Eksternal Keluar ke tabel mutasi_eksternals
     */
    private function syncReklasMutasiKeluar(): void
    {
        $reklasKeluars = \App\Models\AstapReklas::where('jenis_reklas', 'MUTASI_EKSTERNAL')
            ->where('is_deleted', 0)
            ->with(['astap.registers', 'astap.unit'])
            ->get();

        foreach ($reklasKeluars as $rk) {
            $astap = $rk->astap;
            if (!$astap) continue;

            $exists = MutasiEksternal::where('astap_id', $astap->id)
                ->where('tipe', 'keluar')
                ->where('is_deleted', 0)
                ->exists();

            if (!$exists) {
                $spec = is_array($astap->spesifikasi_json) ? $astap->spesifikasi_json : (json_decode($astap->spesifikasi_json, true) ?? []);
                $mutInfo = $spec['mutasi_info'] ?? [];

                $skpdTujuan = $mutInfo['skpd_tujuan'] ?? ($rk->tujuan_nama ?: 'SKPD / OPD Luar');
                $tglBast = $mutInfo['tanggal_bast'] ?? ($rk->tanggal_reklas ? $rk->tanggal_reklas->format('Y-m-d') : date('Y-m-d'));
                $nomorBast = $rk->nomor_ba_reklas ?: ($mutInfo['nomor_bast'] ?? ('000.2.3.2/BAST-KLR-' . str_pad($astap->id, 3, '0', STR_PAD_LEFT) . '/430.10.7/' . date('Y')));

                $mutasiKeluar = MutasiEksternal::create([
                    'astap_id'            => $astap->id,
                    'nomor_bamb'          => $nomorBast,
                    'tanggal_mutasi'      => $tglBast,
                    'jenis_mutasi'        => 'Transfer Antar-OPD',
                    'tipe'                => 'keluar',
                    'opd_asal'            => 'RSUD Dr. H. Koesnadi',
                    'opd_tujuan'          => $skpdTujuan,
                    'unit_id'             => $astap->unit_id,
                    'ruangan_tujuan'      => $astap->ruang_unit ?: ($astap->alamat_barang ?: 'RSUD Dr. H. Koesnadi'),
                    'pj_asal_nama'        => $astap->ppk_nama ?: 'dr. H. Yus Priyatna, Sp.P',
                    'pj_asal_nip'         => $astap->ppk_nip ?: '196904121999031004',
                    'pj_asal_jabatan'     => 'Direktur RSUD Dr. H. Koesnadi',
                    'pj_tujuan_nama'      => $mutInfo['pj_tujuan_nama'] ?? 'Pejabat Penerima OPD',
                    'pj_tujuan_nip'       => $mutInfo['pj_tujuan_nip'] ?? '-',
                    'pj_tujuan_jabatan'   => $mutInfo['pj_tujuan_jabatan'] ?? 'Pejabat Penerima OPD',
                    'nomor_sk_dasar'      => $mutInfo['nomor_sk_dasar'] ?? $nomorBast,
                    'status'              => 'Disahkan (Selesai)',
                    'jumlah_volume'       => max(1, (int) $astap->jumlah_volume),
                    'satuan'              => $astap->satuan ?: 'Unit',
                    'nilai_perolehan'     => (float) ($rk->nilai_reklas ?: $astap->total_realisasi),
                    'kondisi'             => 'Baik',
                    'alasan_mutasi'       => $rk->alasan_reklas ?: 'Pemindahtanganan aset RSUD ke SKPD / OPD luar.',
                    'alamat_instansi'     => $mutInfo['alamat_instansi'] ?? '',
                    'user_id'             => $rk->user_id,
                    'is_deleted'          => 0,
                ]);

                // Link registers
                if ($astap->registers && $astap->registers->isNotEmpty()) {
                    foreach ($astap->registers as $reg) {
                        \App\Models\MutasiEksternalRegister::firstOrCreate(
                            [
                                'mutasi_eksternal_id' => $mutasiKeluar->id,
                                'astap_register_id'   => $reg->id,
                            ],
                            [
                                'kondisi' => $reg->kondisi ?: 'Baik',
                                'catatan' => 'Mutasi Keluar Antar-OPD Reklasifikasi',
                            ]
                        );
                        if ($reg->status !== 'Mutasi Keluar OPD') {
                            $reg->update(['status' => 'Mutasi Keluar OPD']);
                        }
                    }
                }
            }
        }
    }
}
