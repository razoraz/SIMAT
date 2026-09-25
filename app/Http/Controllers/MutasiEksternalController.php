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

        return view('pages.mutasi_eksternal.form', compact('dbMaster108', 'dbUnits', 'dbPejabats'));
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
                'pj_asal_nama'    => $request->input('pj_asal_nama'),
                'pj_asal_nip'     => $request->input('pj_asal_nip'),
                'pj_asal_jabatan' => $request->input('pj_asal_jabatan'),
            ],
        ];

        if ($dokumenPath) {
            $astapPayload['spesifikasi_json']['dokumen_lampiran'] = $dokumenPath;
        }

        if ($request->has('spesifikasi_json') && is_array($request->input('spesifikasi_json'))) {
            $astapPayload['spesifikasi_json'] = array_merge($astapPayload['spesifikasi_json'], $request->input('spesifikasi_json'));
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
                $astapPayload['spesifikasi_json'][$rk] = $val;
            }
        }

        if ($request->has('tanah_items')) {
            $tItems = $request->input('tanah_items');
            if (is_string($tItems)) {
                $tItems = json_decode($tItems, true) ?: [];
            }
            if (is_array($tItems) && count($tItems) > 0) {
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
            }
        }

        $ppkNama = $request->input('ppk_nama', 'dr. H. Yus Priyatna, Sp.P');
        $ppkNip  = $request->input('ppk_nip', '196904121999031004');
        if ($request->filled('ppk_nama')) {
            $astapPayload['ppk_nama'] = $ppkNama;
            $astapPayload['spesifikasi_json']['ppk_nama'] = $ppkNama;
        }
        if ($request->filled('ppk_nip')) {
            $astapPayload['ppk_nip'] = $ppkNip;
            $astapPayload['spesifikasi_json']['ppk_nip'] = $ppkNip;
        }

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
                'opd_tujuan'          => 'RSUD dr. H. Koesnadi (' . $ruangNama . ')',
                'unit_id'             => $data['unit_id'] ?? null,
                'ruangan_tujuan'      => $ruangNama,
                'pj_asal_nama'        => $request->input('pj_asal_nama') ?: 'Pejabat Penyerah OPD Pengirim',
                'pj_asal_nip'         => $request->input('pj_asal_nip') ?: '-',
                'pj_asal_jabatan'     => $request->input('pj_asal_jabatan') ?: 'Pengurus Barang / PPK Asal',
                'pj_tujuan_nama'      => $ppkNama,
                'pj_tujuan_nip'       => $ppkNip,
                'pj_tujuan_jabatan'   => 'Pengurus Barang / PPK RSUD Dr. H. Koesnadi',
                'nomor_sk_dasar'      => $request->input('nomor_sk_dasar', $data['mutasi_nomor_bamb']),
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
                'nomor_bamb'      => $data['mutasi_nomor_bamb'],
                'tanggal_bamb'    => $data['mutasi_tanggal'],
                'nilai_perolehan' => $totalRealisasi,
                'keterangan'      => $data['mutasi_keterangan'] ?? null,
            ]);

            // 4. Generate nomor register unik NIBAR 45 digit untuk setiap unit aset
            $ja = JenisAstap::find($data['jenis_astap_id']);
            $kode108Raw = $ja ? ($ja->sub_sub_rincian_objek ?: $ja->jenis) : '1.3.2.00.00.00';
            $kode108Clean = str_replace('.', '', $kode108Raw);

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

        return view('pages.mutasi_eksternal.form', compact('astap', 'dbMaster108', 'dbUnits', 'dbPejabats'));
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
        $specJson['pj_asal_nama'] = $request->input('pj_asal_nama');
        $specJson['pj_asal_nip'] = $request->input('pj_asal_nip');
        $specJson['pj_asal_jabatan'] = $request->input('pj_asal_jabatan');
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

        if ($request->has('tanah_items')) {
            $tItems = $request->input('tanah_items');
            if (is_string($tItems)) {
                $tItems = json_decode($tItems, true) ?: [];
            }
            if (is_array($tItems) && count($tItems) > 0) {
                $specJson['tanah_items'] = $tItems;
                $firstT = $tItems[0];
                $totalLuas = array_sum(array_map(fn($it) => (float)($it['tanah_luas_m2'] ?? 0), $tItems));
                $allSertifikat = array_filter(array_map(fn($it) => $it['tanah_sertifikat_no'] ?? null, $tItems));
                $specJson['luas_m2'] = $totalLuas;
                $specJson['hak_tanah'] = $firstT['tanah_hak'] ?? 'Hak Pakai';
                $specJson['sertifikat_no'] = count($allSertifikat) > 0 ? implode(', ', $allSertifikat) : ($firstT['tanah_sertifikat_no'] ?? null);
                $specJson['sertifikat_tgl'] = $firstT['tanah_sertifikat_tgl'] ?? null;
                $specJson['penggunaan'] = $firstT['tanah_penggunaan'] ?? null;
                $specJson['tanah_jumlah_bidang'] = count($tItems);
            }
        }

        $ppkNama = $request->input('ppk_nama', $item->ppk_nama ?: 'dr. H. Yus Priyatna, Sp.P');
        $ppkNip  = $request->input('ppk_nip', $item->ppk_nip ?: '196904121999031004');
        if ($request->filled('ppk_nama')) {
            $astapPayload['ppk_nama'] = $ppkNama;
            $specJson['ppk_nama'] = $ppkNama;
        }
        if ($request->filled('ppk_nip')) {
            $astapPayload['ppk_nip'] = $ppkNip;
            $specJson['ppk_nip'] = $ppkNip;
        }
        $astapPayload['spesifikasi_json'] = $specJson;

        DB::transaction(function () use ($item, $astapPayload, $data, $totalRealisasi, $totalVolume, $kondisiItem, $jenisMutasi, $ppkNama, $ppkNip, $dokumenPath, $request) {
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
                    'opd_tujuan'          => 'RSUD dr. H. Koesnadi (' . $ruangNama . ')',
                    'unit_id'             => $data['unit_id'] ?? null,
                    'ruangan_tujuan'      => $ruangNama,
                    'pj_asal_nama'        => $request->input('pj_asal_nama') ?: ($item->mutasiEksternal?->pj_asal_nama ?: 'Pejabat Penyerah OPD Pengirim'),
                    'pj_asal_nip'         => $request->input('pj_asal_nip') ?: ($item->mutasiEksternal?->pj_asal_nip ?: '-'),
                    'pj_asal_jabatan'     => $request->input('pj_asal_jabatan') ?: ($item->mutasiEksternal?->pj_asal_jabatan ?: 'Pengurus Barang / PPK Asal'),
                    'pj_tujuan_nama'      => $ppkNama,
                    'pj_tujuan_nip'       => $ppkNip,
                    'pj_tujuan_jabatan'   => 'Pengurus Barang / PPK RSUD Dr. H. Koesnadi',
                    'nomor_sk_dasar'      => $request->input('nomor_sk_dasar', $data['mutasi_nomor_bamb']),
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
                    'nomor_bamb'      => $data['mutasi_nomor_bamb'],
                    'tanggal_bamb'    => $data['mutasi_tanggal'],
                    'nilai_perolehan' => $totalRealisasi,
                    'keterangan'      => $data['mutasi_keterangan'] ?? null,
                ]);
            } else {
                AstapPelimpahanSkpd::create([
                    'astap_id'        => $item->id,
                    'skpd_asal'       => $data['mutasi_asal'],
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
            ->where(function ($q) {
                $q->whereIn('sumber_dana', ['pelimpahan_skpd', 'mutasi_masuk'])
                  ->orWhereNotNull('mutasi_nomor_bamb')
                  ->orWhereNotNull('mutasi_asal');
            })
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
                'opd_tujuan'          => 'RSUD dr. H. Koesnadi (' . $ruangNama . ')',
                'unit_id'             => $a->unit_id,
                'ruangan_tujuan'      => $ruangNama,
                'pj_asal_nama'        => 'Pejabat Penyerah OPD Pengirim',
                'pj_asal_nip'         => '-',
                'pj_asal_jabatan'     => 'Pengurus Barang / PPK Asal',
                'pj_tujuan_nama'      => $a->ppk_nama ?: 'dr. H. Yus Priyatna, Sp.P',
                'pj_tujuan_nip'       => $a->ppk_nip ?: '196904121999031004',
                'pj_tujuan_jabatan'   => 'Pengurus Barang / PPK RSUD Dr. H. Koesnadi',
                'nomor_sk_dasar'      => $nomorBamb,
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
