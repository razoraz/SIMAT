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
     * Dapatkan daftar instansi/SKPD pengirim yang pernah tercatat di database.
     */
    public static function getDistinctSkpdAsals()
    {
        $directory = self::getSkpdDirectory();
        $list = array_keys($directory);

        $dbExtras = AstapPelimpahanSkpd::whereNotNull('skpd_asal')
            ->where('skpd_asal', '!=', '')
            ->distinct()
            ->pluck('skpd_asal')
            ->toArray();

        return collect(array_merge($list, $dbExtras))
            ->map(fn($v) => trim($v))
            ->filter()
            ->unique()
            ->values();
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

        // Ambil data aktif dari tabel mutasi_eksternals
        $records = MutasiEksternal::with(['astap.registers', 'astap.jenisAstap', 'unit', 'user'])
            ->where('is_deleted', 0)
            ->latest('tanggal_mutasi')
            ->latest('id')
            ->get();

        $mutasiEksternals = $records->map(function ($m) {
            $astap = $m->astap;
            $nomorBamb = $m->nomor_bamb ?: ($astap?->bast_dokumen_nomor ?: 'BAMB-SKPD-' . str_pad($m->id, 4, '0', STR_PAD_LEFT));

            // Format tanggal
            $tglRaw = $m->tanggal_mutasi ? $m->tanggal_mutasi->format('Y-m-d') : ($astap?->created_at ? $astap->created_at->format('Y-m-d') : date('Y-m-d'));
            $tglFormatted = '-';
            try {
                $tglFormatted = Carbon::parse($tglRaw)->locale('id')->isoFormat('D MMM Y');
            } catch (\Throwable $e) {
                $tglFormatted = (string) $tglRaw;
            }

            // Pihak Pengirim & Penerima
            $opdAsal = $m->opd_asal ?: ($astap?->mutasi_asal ?: 'SKPD / Instansi Luar');
            $ruangRSUD = $m->unit?->nama ?: ($m->ruangan_tujuan ?: ($astap?->alamat_barang ?: 'Gudang/Ruangan RSUD'));
            $opdTujuan = $m->opd_tujuan ?: ('RSUD dr. H. Koesnadi (' . $ruangRSUD . ')');

            // Pejabat
            $pjNama = $m->pj_tujuan_nama ?: ($astap?->ppk_nama ?: 'Pengurus Barang RSUD');
            $pjNip  = $m->pj_tujuan_nip ?: ($astap?->ppk_nip ?: '-');

            // Kode 108 & Klasifikasi
            $kode108 = $astap?->kode_108 ?: ($astap?->jenisAstap?->sub_sub_rincian_objek ?: ($astap?->jenisAstap?->jenis ?: '-'));

            // Daftar registers / satuan barang
            $items = [];
            $registers = $astap?->registers ?? collect();
            if ($registers->isNotEmpty()) {
                foreach ($registers as $idx => $reg) {
                    $items[] = [
                        'no'          => $idx + 1,
                        'nama_barang' => $astap->nama_barang,
                        'nibar'       => $reg->nibar ?: '-',
                        'kode_108'    => $kode108,
                        'kondisi'     => $reg->kondisi ?: ($m->kondisi ?: 'Baik'),
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

            $itemCount = count($items);
            $firstNibar = ($items[0]['nibar'] !== '-') ? $items[0]['nibar'] : ($kode108 ?: '1.3.2.00.00.00');
            $nilaiReal = (float) ($m->nilai_perolehan ?: ($astap?->total_realisasi ?: 0));
            $tahunMasuk = (string) ($astap?->tahun_perolehan ?: date('Y', strtotime($tglRaw)));
            $volAset = (int) ($m->jumlah_volume ?: ($itemCount ?: 1));

            return [
                'id'                        => $astap?->id ?: $m->id,
                'mutasi_id'                 => $m->id,
                'is_deleted'                => (int) $m->is_deleted,
                'kode'                      => $nomorBamb,
                'jenis'                     => $m->jenis_mutasi ?: 'Transfer Antar-OPD',
                'tipe'                      => $m->tipe ?: 'masuk',
                'kategori_label'            => 'Pelimpahan SKPD (Mutasi Masuk)',
                'nama'                      => ($astap?->nama_barang ?: 'Barang Mutasi') . ($itemCount > 1 ? " (+{$itemCount} unit)" : ''),
                'nama_murni'                => $astap?->nama_barang ?: 'Barang Mutasi',
                'nama_barang'               => $astap?->nama_barang ?: 'Barang Mutasi',
                'category'                  => $astap?->category ?: 'KIB B',
                'is_extracomtable'          => (bool) ($astap?->is_extracomtable ?? false),
                'is_reklas'                 => (bool) ($astap?->is_reklas ?? false),
                'jenis_reklas'              => $astap?->jenis_reklas,
                'sumber_dana'               => $astap?->sumber_dana ?: 'pelimpahan_skpd',
                'sumber_dana_raw'           => $astap?->sumber_dana ?: 'pelimpahan_skpd',
                'jenis_aset_nama'           => $astap?->jenisAstap?->nama_jenis ?: ($astap?->jenisAstap?->jenis ?: 'PELIMPAHAN SKPD'),
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
                'kondisi'                   => $items[0]['kondisi'] ?? 'Baik',
                'opd_asal'                  => $opdAsal,
                'ruangan_asal'              => $opdAsal,
                'pj_asal_nama'              => $m->pj_asal_nama ?: 'Pejabat Penyerah SKPD Pengirim',
                'pj_asal_nip'               => $m->pj_asal_nip ?: '-',
                'pj_asal_jabatan'           => $m->pj_asal_jabatan ?: 'Pengurus Barang / PPK Asal',
                'opd_tujuan'                => $opdTujuan,
                'ruangan_tujuan'            => $ruangRSUD,
                'pejabat_opd_tujuan'        => $pjNama,
                'nip_pejabat_opd_tujuan'    => $pjNip,
                'jabatan_opd_tujuan'        => $m->pj_tujuan_jabatan ?: 'Pengurus Barang / PPK RSUD Dr. H. Koesnadi',
                'nomor_sk_dasar'            => $m->nomor_sk_dasar ?: $nomorBamb,
                'alamat_instansi'           => $m->alamat_instansi ?: ($astap?->spesifikasi_json['alamat_instansi'] ?? ''),
                'tgl'                       => $tglFormatted,
                'tgl_raw'                   => (string) $tglRaw,
                'status'                    => $m->status ?: 'Disahkan (Selesai)',
                'alasan_mutasi'             => $m->alasan_mutasi ?: 'Pelimpahan aset barang milik daerah dari SKPD/Dinas luar ke RSUD dr. H. Koesnadi.',
                'tgl_estimasi_kembali'      => $m->tgl_estimasi_kembali ? $m->tgl_estimasi_kembali->format('Y-m-d') : null,
                'dokumen_lampiran'          => $m->dokumen_lampiran,
                'dokumen_lampiran_url'      => $m->dokumen_lampiran ? asset('storage/' . $m->dokumen_lampiran) : null,
                'nilai_perolehan'           => $nilaiReal,
                'nilai_perolehan_formatted' => 'Rp ' . number_format($nilaiReal, 0, ',', '.'),
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

        if ($dokumenPath) {
            $astapPayload['spesifikasi_json']['dokumen_lampiran'] = $dokumenPath;
        }

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

        $item = DB::transaction(function () use ($astapPayload, $data, $totalVolume, $totalRealisasi, $tahun, $kondisiItem, $jenisMutasi, $ppkNama, $ppkNip, $dokumenPath, $request) {
            // 1. Simpan ke tabel master astaps
            $item = Astap::create($astapPayload);

            // 2. Simpan ke tabel mutasi_eksternals (Database Mutasi Eksternal Utama)
            $unitModel = !empty($data['unit_id']) ? Unit::find($data['unit_id']) : null;
            $ruangNama = $unitModel ? $unitModel->nama : ($data['alamat_barang'] ?: 'RSUD Dr. H. Koesnadi');

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
                'kondisi'             => $kondisiItem,
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

            // 4. Generate nomor register unik NIBAR 45 digit untuk setiap unit aset
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

                $qrPath = "/scan/{$nibar}";
                AstapRegister::create([
                    'astap_id'        => $item->id,
                    'unit_id'         => $data['unit_id'] ?? null,
                    'tahun_perolehan' => $tahun,
                    'no_register_int' => $runningRegNum,
                    'no_register'     => $nibar,
                    'nibar'           => $nibar,
                    'qr_code_path'    => $qrPath,
                    'ruang_pemegang'  => $ruangNama,
                    'kondisi'         => $kondisiItem,
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
        if ($dokumenPath) {
            $specJson['dokumen_lampiran'] = $dokumenPath;
        }

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

        DB::transaction(function () use ($item, $astapPayload, $data, $totalRealisasi, $totalVolume, $tahun, $kondisiItem, $jenisMutasi, $ppkNama, $ppkNip, $dokumenPath, $request) {
            // 1. Update master Astap
            $item->update($astapPayload);

            // 2. Update / Create MutasiEksternal
            $unitModel = !empty($data['unit_id']) ? Unit::find($data['unit_id']) : null;
            $ruangNama = $unitModel ? $unitModel->nama : ($data['alamat_barang'] ?: 'RSUD Dr. H. Koesnadi');

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
                    'dokumen_lampiran'    => $dokumenPath,
                    'status'              => 'Disahkan (Selesai)',
                    'jumlah_volume'       => $totalVolume,
                    'satuan'              => $data['satuan'],
                    'nilai_perolehan'     => $totalRealisasi,
                    'kondisi'             => $kondisiItem,
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

            // 4. Update seluruh register aset yang ada
            AstapRegister::where('astap_id', $item->id)->update([
                'unit_id'        => $data['unit_id'] ?? null,
                'ruang_pemegang' => $ruangNama,
                'kondisi'        => $kondisiItem,
            ]);

            // 5. Jika jumlah_volume bertambah melebihi jumlah register saat ini, buat register baru
            $currentRegsCount = AstapRegister::where('astap_id', $item->id)->count();
            if ($totalVolume > $currentRegsCount) {
                $ja = JenisAstap::find($data['jenis_astap_id']);
                $kode108Raw = $ja ? ($ja->sub_sub_rincian_objek ?: $ja->jenis) : '1.3.2.00.00.00';
                $kode108Clean = str_pad(substr(str_replace('.', '', $kode108Raw), 0, 12), 12, '0', STR_PAD_RIGHT);

                $maxRegInt = AstapRegister::where('tahun_perolehan', $tahun)
                    ->whereHas('astap', fn($sq) => $sq->where('jenis_astap_id', $data['jenis_astap_id']))
                    ->max('no_register_int') ?? 0;

                $runningRegNum = (int) $maxRegInt;
                $needed = $totalVolume - $currentRegsCount;
                for ($i = 0; $i < $needed; $i++) {
                    $runningRegNum++;
                    $noRegStr = str_pad($runningRegNum, 7, '0', STR_PAD_LEFT);
                    $nibar = "1201351102000000280000{$tahun}{$kode108Clean}{$noRegStr}";

                    while (AstapRegister::where('nibar', $nibar)->exists()) {
                        $runningRegNum++;
                        $noRegStr = str_pad($runningRegNum, 7, '0', STR_PAD_LEFT);
                        $nibar = "1201351102000000280000{$tahun}{$kode108Clean}{$noRegStr}";
                    }

                    $qrPath = "/scan/{$nibar}";
                    AstapRegister::create([
                        'astap_id'        => $item->id,
                        'unit_id'         => $data['unit_id'] ?? null,
                        'tahun_perolehan' => $tahun,
                        'no_register_int' => $runningRegNum,
                        'no_register'     => $nibar,
                        'nibar'           => $nibar,
                        'qr_code_path'    => $qrPath,
                        'ruang_pemegang'  => $ruangNama,
                        'kondisi'         => $kondisiItem,
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

        // Coba cari di MutasiEksternal terlebih dahulu (bisa berdasarkan astap_id atau id mutasi)
        $mutasi = MutasiEksternal::where('astap_id', $id)->orWhere('id', $id)->first();
        $astap  = $mutasi ? $mutasi->astap : Astap::find($id);

        if (!$mutasi && !$astap) {
            return response()->json(['success' => false, 'message' => 'Data Mutasi Eksternal tidak ditemukan.'], 404);
        }

        DB::transaction(function () use ($mutasi, $astap, $deleterName, $user) {
            if ($mutasi) {
                $mutasi->update([
                    'is_deleted'    => 1,
                    'deleted_by'    => $deleterName,
                    'deleted_by_id' => $user?->id,
                    'deleted_at'    => now(),
                ]);
            }

            if ($astap) {
                $astap->update([
                    'is_deleted'    => 1,
                    'deleted_by'    => $deleterName,
                    'deleted_by_id' => $user?->id,
                    'deleted_at'    => now(),
                ]);

                AstapRegister::where('astap_id', $astap->id)->update([
                    'is_deleted'    => 1,
                    'deleted_by'    => $deleterName,
                    'deleted_by_id' => $user?->id,
                    'deleted_at'    => now(),
                ]);
            }
        });

        $msg = 'Data Mutasi Eksternal berhasil dipindahkan ke Recycle Bin.';
        if ($request->ajax() || $request->wantsJson()) {
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
}
