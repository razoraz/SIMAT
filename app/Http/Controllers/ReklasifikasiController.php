<?php

namespace App\Http\Controllers;

use App\Models\Astap;
use App\Models\AstapReklas;
use App\Models\JenisAstap;
use App\Models\JenisReklasifikasi;
use App\Models\MutasiEksternal;
use App\Models\MutasiEksternalRegister;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReklasifikasiController extends Controller
{
    /**
     * Menampilkan Halaman Master Reklasifikasi Aset (Matriks 5 Kolom & Log Transaksi)
     */
    public function index(Request $request)
    {
        $selectedTahun = (int) $request->get('tahun', date('Y'));
        $selectedTw = $request->get('triwulan', 'all');
        $selectedJenis = $request->get('jenis_reklas', 'all');

        // Daftar tahun perolehan yang ada di sistem
        $tahunList = Astap::select('tahun_perolehan')
            ->distinct()
            ->orderBy('tahun_perolehan', 'desc')
            ->pluck('tahun_perolehan')
            ->toArray();

        if (!in_array($selectedTahun, $tahunList)) {
            array_unshift($tahunList, $selectedTahun);
        }

        // Ambil 42 baris master template reklasifikasi
        $templateRows = JenisReklasifikasi::active()->get();

        // 1. Ambil data perolehan belanja modal ASTAP untuk tahun terpilih
        $astapQuery = Astap::where('is_deleted', 0)
            ->where('tahun_perolehan', $selectedTahun)
            ->with(['jenisAstap', 'belanjaModal']);

        // Filter triwulan berdasarkan sp2d_tanggal atau bast_dokumen_tanggal atau created_at jika ada
        if ($selectedTw !== 'all') {
            $tw = (int) $selectedTw;
            $startMonth = ($tw - 1) * 3 + 1;
            $endMonth = $tw * 3;
            $startDate = sprintf('%04d-%02d-01', $selectedTahun, $startMonth);
            $endDate = date('Y-m-t', strtotime(sprintf('%04d-%02d-01', $selectedTahun, $endMonth)));

            $triwulanValues = [
                1 => ['1', 'TW1', 'TW 1', 'TW I', 'Triwulan I', 'Triwulan 1', 'Q1', 'I'],
                2 => ['2', 'TW2', 'TW 2', 'TW II', 'Triwulan II', 'Triwulan 2', 'Q2', 'II'],
                3 => ['3', 'TW3', 'TW 3', 'TW III', 'Triwulan III', 'Triwulan 3', 'Q3', 'III'],
                4 => ['4', 'TW4', 'TW 4', 'TW IV', 'Triwulan IV', 'Triwulan 4', 'Q4', 'IV'],
            ];
            $twValues = $triwulanValues[$tw] ?? [(string) $tw];

            $astapQuery->where(function ($q) use ($twValues, $startDate, $endDate) {
                $q->whereIn('triwulan', $twValues)
                  ->orWhereHas('belanjaModal', function ($bm) use ($startDate, $endDate) {
                      $bm->where(function ($bmSub) use ($startDate, $endDate) {
                          $bmSub->whereBetween('sp2d_tanggal', [$startDate, $endDate])
                                ->orWhere(function ($bast) use ($startDate, $endDate) {
                                    $bast->whereNull('sp2d_tanggal')
                                         ->whereBetween('bast_dokumen_tanggal', [$startDate, $endDate]);
                                });
                      });
                  })
                  ->orWhere(function ($sub2) use ($startDate, $endDate) {
                      $sub2->whereDoesntHave('belanjaModal')
                           ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
                  });
            });
        }

        $astaps = $astapQuery->get();

        // 2. Ambil seluruh transaksi mutasi reklasifikasi untuk tahun & tw terpilih
        $reklasQuery = AstapReklas::active()->where('tahun', $selectedTahun);
        if ($selectedTw !== 'all') {
            $reklasQuery->where('triwulan', (int) $selectedTw);
        }
        $reklasMutasis = $reklasQuery->with(['astap.jenisAstap', 'jenisReklasAsal', 'jenisReklasTujuan'])->get();

        // Kumpulkan riwayat seluruh reklasifikasi per astap pada tahun bersangkutan
        $allReklasByAstap = AstapReklas::active()->where('tahun', $selectedTahun)
            ->orderBy('id', 'asc')
            ->get()
            ->groupBy('astap_id');

        // Kumpulkan ID astap yang sudah memiliki transaksi reklas EKSTRAKOMPTABEL pada periode ini
        $processedExtracomAstapIds = $reklasMutasis
            ->where('jenis_reklas', 'EKSTRAKOMPTABEL')
            ->pluck('astap_id')
            ->filter()
            ->toArray();

        // 3. Susun Matriks 5 Kolom: Uraian, Saldo Awal, Mutasi Tambah, Mutasi Kurang, Saldo Akhir
        $matriks = [];
        $subtotals = [
            'KIB A' => ['awal' => 0, 'tambah' => 0, 'kurang' => 0, 'akhir' => 0],
            'KIB B' => ['awal' => 0, 'tambah' => 0, 'kurang' => 0, 'akhir' => 0],
            'KIB C' => ['awal' => 0, 'tambah' => 0, 'kurang' => 0, 'akhir' => 0],
            'KIB D' => ['awal' => 0, 'tambah' => 0, 'kurang' => 0, 'akhir' => 0],
            'KIB E' => ['awal' => 0, 'tambah' => 0, 'kurang' => 0, 'akhir' => 0],
            'KIB F' => ['awal' => 0, 'tambah' => 0, 'kurang' => 0, 'akhir' => 0],
            'ASET LAINNYA' => ['awal' => 0, 'tambah' => 0, 'kurang' => 0, 'akhir' => 0],
            'KOREKSI' => ['awal' => 0, 'tambah' => 0, 'kurang' => 0, 'akhir' => 0],
        ];

        $grandTotal = ['awal' => 0, 'tambah' => 0, 'kurang' => 0, 'akhir' => 0];
        $jumlahAsetTetap  = ['awal' => 0, 'tambah' => 0, 'kurang' => 0, 'akhir' => 0];
        $jumlahAsetLainnya = ['awal' => 0, 'tambah' => 0, 'kurang' => 0, 'akhir' => 0];

        $kibAsetTetap   = ['KIB A', 'KIB B', 'KIB C', 'KIB D', 'KIB E', 'KIB F'];
        $kibAsetLainnya = ['ASET LAINNYA'];

        // Hitung total nilai aset ekstrakomptabel tanpa log transaksi reklas untuk penyeimbang double-entry
        $totalUnloggedExtracom = 0;
        foreach ($astaps as $astap) {
            if ($astap->is_extracomtable && !in_array($astap->id, $processedExtracomAstapIds)) {
                $totalUnloggedExtracom += (float) ($astap->total_realisasi ?: ($astap->jumlah_anggaran ?: 0));
            }
        }

        foreach ($templateRows as $row) {
            $prefix = $row->kode_prefix;
            $saldoAwal = 0;
            $mutasiTambah = 0;
            $mutasiKurang = 0;

            // 1. Hitung Saldo Awal Belanja Modal dari ASTAP berdasarkan Prefix PMDN 108
            // Catatan: Aset yang direklasifikasi ditempatkan saldo awalnya pada rekening asal pengadaan
            if ($prefix && !str_starts_with($prefix, 'KOR_')) {
                foreach ($astaps as $astap) {
                    $matchedRowId = $this->resolveAstapSaldoAwalRowId($astap, $templateRows, $allReklasByAstap);
                    if ($matchedRowId === $row->id) {
                        $saldoAwal += (float) ($astap->total_realisasi ?: ($astap->jumlah_anggaran ?: 0));
                    }
                }
            }

            // 2. Hitung Mutasi dari Transaksi Reklasifikasi (AstapReklas)
            foreach ($reklasMutasis as $reklas) {
                $asalRowId = $this->resolveAsalRowId($reklas, $templateRows);
                $tujuanRowId = $this->resolveTujuanRowId($reklas, $templateRows);

                // Baris ini sebagai ASAL => Mutasi Kurang (-)
                if ($asalRowId === $row->id) {
                    $mutasiKurang += (float) $reklas->nilai_reklas;
                }

                // Baris ini sebagai TUJUAN => Mutasi Tambah (+)
                if ($tujuanRowId === $row->id) {
                    $mutasiTambah += (float) $reklas->nilai_reklas;
                }
            }

            // 3. Tangani aset dengan status is_extracomtable di ASTAP yang belum tercatat di log reklasifikasi
            if ($prefix && !str_starts_with($prefix, 'KOR_')) {
                foreach ($astaps as $astap) {
                    if ($astap->is_extracomtable && !in_array($astap->id, $processedExtracomAstapIds)) {
                        $matchedRow = $this->matchAstapToRow($astap, $templateRows);
                        if ($matchedRow && $matchedRow->id === $row->id) {
                            $val = (float) ($astap->total_realisasi ?: ($astap->jumlah_anggaran ?: 0));
                            $mutasiKurang += $val;
                        }
                    }
                }
            }

            // 4. Khusus baris Koreksi Ekstrakomptabel (KOR_EXTRACOM)
            // Rekap penyeimbang: menampung mutasi tambah untuk seluruh aset ekstrakomptabel
            if ($prefix === 'KOR_EXTRACOM') {
                $mutasiTambah += $totalUnloggedExtracom;
            }

            $saldoAkhir = $saldoAwal + $mutasiTambah - $mutasiKurang;

            $kib = $row->kelompok_kib;
            if (isset($subtotals[$kib])) {
                $subtotals[$kib]['awal']   += $saldoAwal;
                $subtotals[$kib]['tambah'] += $mutasiTambah;
                $subtotals[$kib]['kurang'] += $mutasiKurang;
                $subtotals[$kib]['akhir']  += $saldoAkhir;
            }

            // Akumulasi Jumlah Aset Tetap (KIB A-F)
            if (in_array($kib, $kibAsetTetap)) {
                $jumlahAsetTetap['awal']   += $saldoAwal;
                $jumlahAsetTetap['tambah'] += $mutasiTambah;
                $jumlahAsetTetap['kurang'] += $mutasiKurang;
                $jumlahAsetTetap['akhir']  += $saldoAkhir;
            }

            // Akumulasi Jumlah Aset Lainnya (1.5.x)
            if (in_array($kib, $kibAsetLainnya)) {
                $jumlahAsetLainnya['awal']   += $saldoAwal;
                $jumlahAsetLainnya['tambah'] += $mutasiTambah;
                $jumlahAsetLainnya['kurang'] += $mutasiKurang;
                $jumlahAsetLainnya['akhir']  += $saldoAkhir;
            }

            // Grand Total = Aset Tetap + Aset Lainnya (TIDAK termasuk baris KOREKSI)
            if ($kib !== 'KOREKSI') {
                $grandTotal['awal']   += $saldoAwal;
                $grandTotal['tambah'] += $mutasiTambah;
                $grandTotal['kurang'] += $mutasiKurang;
                $grandTotal['akhir']  += $saldoAkhir;
            }

            $matriks[] = [
                'id' => $row->id,
                'urutan' => $row->urutan,
                'kelompok_kib' => $row->kelompok_kib,
                'kode_prefix' => $row->kode_prefix,
                'nama_sub_rincian' => $row->nama_sub_rincian,
                'saldo_awal' => $saldoAwal,
                'mutasi_tambah' => $mutasiTambah,
                'mutasi_kurang' => $mutasiKurang,
                'saldo_akhir' => $saldoAkhir,
            ];
        }

        // 4. Ambil Log Transaksi Reklasifikasi
        $logReklas = AstapReklas::active()->where('tahun', $selectedTahun)
            ->when($selectedTw !== 'all', fn($q) => $q->where('triwulan', (int) $selectedTw))
            ->when($selectedJenis !== 'all', fn($q) => $q->where('jenis_reklas', $selectedJenis))
            ->with(['astap.jenisAstap', 'jenisReklasAsal', 'jenisReklasTujuan', 'user'])
            ->orderBy('tanggal_reklas', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        // 5. Ambil kandidat aset untuk modal tambah reklasifikasi
        $kandidatAstaps = Astap::where('is_deleted', 0)
            ->where('tahun_perolehan', $selectedTahun)
            ->select('id', 'nama_barang', 'total_realisasi', 'jumlah_anggaran', 'tahun_perolehan', 'category', 'asal_kib', 'jenis_astap_id', 'is_reklas', 'jenis_reklas', 'satuan', 'merk_type', 'alamat_barang', 'spesifikasi_json', 'jumlah_volume', 'harga_satuan', 'is_extracomtable')
            ->with(['jenisAstap', 'registers'])
            ->orderBy('nama_barang', 'asc')
            ->get();

        $dbSkpdDirectory = \App\Http\Controllers\MutasiEksternalController::getSkpdDirectory();
        $dbSkpdAsals = array_values(array_unique(array_keys($dbSkpdDirectory)));
        $dbPejabats = \App\Http\Controllers\MutasiEksternalController::getDistinctPejabats();

        return view('pages.reklasifikasi.index', [
            'matriks'           => $matriks,
            'subtotals'         => $subtotals,
            'grandTotal'        => $grandTotal,
            'jumlahAsetTetap'   => $jumlahAsetTetap,
            'jumlahAsetLainnya' => $jumlahAsetLainnya,
            'logReklas'         => $logReklas,
            'tahunList'         => $tahunList,
            'selectedTahun'     => $selectedTahun,
            'selectedTw'        => $selectedTw,
            'selectedJenis'     => $selectedJenis,
            'templateRows'      => $templateRows,
            'kandidatAstaps'    => $kandidatAstaps,
            'dbMaster108'       => JenisAstap::getNested108(),
            'dbMitraKemitraans' => \App\Models\AstapKemitraan::getDistinctMitras(),
            'dbSkpdDirectory'   => $dbSkpdDirectory,
            'dbSkpdAsals'       => $dbSkpdAsals,
            'dbPejabats'        => $dbPejabats,
        ]);
    }

    /**
     * Menyimpan transaksi reklasifikasi baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'astap_id' => 'required|exists:astaps,id',
            'jenis_reklas' => 'required|string|max:50',
            'jenis_reklasifikasi_asal_id' => 'nullable|exists:jenis_reklasifikasis,id',
            'jenis_reklasifikasi_tujuan_id' => 'nullable|exists:jenis_reklasifikasis,id',
            'asal_kib' => 'nullable|string|max:50',
            'tujuan_kib' => 'nullable|string|max:50',
            'tujuan_kode' => 'nullable|string|max:100',
            'tujuan_nama' => 'nullable|string|max:255',
            'kode_108' => 'nullable|string|max:100',
            'nilai_reklas' => 'required|numeric|min:0',
            'tanggal_reklas' => 'required|date',
            'triwulan' => 'required|integer|between:1,4',
            'tahun' => 'required|integer|min:2000|max:2099',
            'nomor_ba_reklas' => 'nullable|string|max:150',
            'alasan_reklas' => 'required|string|min:3|max:1000',
            'keterangan' => 'nullable|string',
            'reklas_items' => 'nullable|array',
            'spesifikasi_baru' => 'nullable|array',
            'jumlah_anggaran' => 'nullable|numeric|min:0',
            'tipe_koreksi' => 'nullable|string|in:kurang,tambah',
            'sub_koreksi' => 'nullable|string|in:biasa,lkd,manset',
            'tanggal_dokumen_koreksi' => 'nullable|date',
            'pihak_hibah' => 'nullable|string|max:255',
            'skpd_tujuan' => 'nullable|string|max:255',
            'tanggal_bast' => 'nullable|date|before_or_equal:today',
            'pejabat_opd_tujuan' => 'nullable|string|max:255',
            'nip_pejabat_opd_tujuan' => 'nullable|string|max:50',
            'jabatan_opd_tujuan' => 'nullable|string|max:255',
            'pj_asal_nama' => 'nullable|string|max:255',
            'pj_asal_nip' => 'nullable|string|max:50',
            'pj_asal_jabatan' => 'nullable|string|max:255',
            'dokumen_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'register_kondisi_map' => 'nullable',
            'alamat_instansi' => 'nullable|string|max:500',
            'nomor_sk_dasar' => 'nullable|string|max:255',
            'selected_register_ids' => 'nullable',
            'is_partial_reklas' => 'nullable|boolean',
            'selected_indices' => 'nullable|array',
            'selected_indices.*' => 'integer',
            'selected_items' => 'nullable|array',
            'unselected_items' => 'nullable|array',
        ], [
            'alasan_reklas.required' => 'Alasan kenapa melakukan reklasifikasi wajib diisi untuk semua jenis reklasifikasi.',
            'alasan_reklas.min' => 'Alasan reklasifikasi minimal berisi 3 karakter penjelasan.',
            'tanggal_bast.before_or_equal' => 'Tanggal dokumen BAST Mutasi tidak boleh melebihi tanggal hari ini.',
            'dokumen_file.max' => 'Ukuran berkas lampiran BAST maksimal 10 MB.',
            'dokumen_file.mimes' => 'Format berkas lampiran BAST harus berupa PDF, JPG, JPEG, atau PNG.',
        ]);

        // Jika jenis reklasifikasi adalah Ekstrakomptabel atau Kapitalisasi ke Intrakomptabel
        $isExtracom = ($validated['jenis_reklas'] === 'EKSTRAKOMPTABEL');
        $isIntracom = ($validated['jenis_reklas'] === 'KAPITALISASI_INTRAKOM');

        if (($isExtracom || $isIntracom) && !empty($request->reklas_items) && is_array($request->reklas_items)) {
            // Validasi tiap rincian barang: volume minimal 1, batas Rp 300.000 dan > Rp 0 sebelum transaksi DB
            foreach ($request->reklas_items as $itemIdx => $rItem) {
                $qty = (int) ($rItem['jumlah_volume'] ?? 1);
                $harga = (float) ($rItem['harga_satuan'] ?? 0);
                $nama = trim((string) ($rItem['nama_barang'] ?? 'Barang #' . ($itemIdx + 1)));

                if ($qty < 1) {
                    return response()->json([
                        'success' => false,
                        'message' => "Volume / Jumlah unit untuk {$nama} minimal bernilai 1.",
                    ], 422);
                }

                if ($harga <= 0) {
                    return response()->json([
                        'success' => false,
                        'message' => "Harga satuan untuk {$nama} harus lebih dari Rp 0.",
                    ], 422);
                }

                if ($isExtracom && $harga > 300000) {
                    return response()->json([
                        'success' => false,
                        'message' => "Harga satuan untuk {$nama} (Rp " . number_format($harga, 0, ',', '.') . ") melebihi batas Ekstrakomptabel (Maksimal Rp 300.000).",
                    ], 422);
                }

                if ($isIntracom && $harga <= 300000) {
                    return response()->json([
                        'success' => false,
                        'message' => "Harga satuan untuk {$nama} (Rp " . number_format($harga, 0, ',', '.') . ") harus lebih dari Rp 300.000 untuk masuk ke Intrakomptabel / Aset Tetap.",
                    ], 422);
                }
            }
        }



        $astap = Astap::findOrFail($validated['astap_id']);
        $existingKem = $astap->kemitraan ?? \App\Models\AstapKemitraan::where(function($q) use ($astap) {
            $q->where('astap_id', $astap->id)->orWhere('objek_astap_id', $astap->id);
        })->where('is_deleted', 0)->first();
        $specLamaData = is_array($astap->spesifikasi_json) ? $astap->spesifikasi_json : (json_decode($astap->spesifikasi_json, true) ?? []);

        // Validasi Mandatory Form Kemitraan Pihak Ketiga (Akun 1.5.2)
        if (($validated['tujuan_kib'] ?? null) === 'KEMITRAAN') {
            $spek = $request->input('spesifikasi_baru', []);
            $firstItem = (!empty($spek['items']) && is_array($spek['items']) && !empty($spek['items'][0])) ? $spek['items'][0] : $spek;

            $mitraNama = trim((string) ($firstItem['kemitraan_mitra'] ?? ($spek['kemitraan_mitra'] ?? ($existingKem->mitra_nama ?? ($specLamaData['kemitraan_mitra'] ?? ($specLamaData['mitra'] ?? ''))))));
            $pimpinan = trim((string) ($firstItem['kemitraan_pimpinan'] ?? ($spek['kemitraan_pimpinan'] ?? ($existingKem->mitra_pimpinan ?? ($specLamaData['kemitraan_pimpinan'] ?? ($specLamaData['pimpinan'] ?? 'Mitra'))))));
            $pksNo = trim((string) ($firstItem['kemitraan_perjanjian_no'] ?? ($spek['kemitraan_perjanjian_no'] ?? ($existingKem->nomor_pks ?? ($specLamaData['kemitraan_perjanjian_no'] ?? ($specLamaData['nomor_pks'] ?? ''))))));
            $pksTgl = trim((string) ($firstItem['kemitraan_tanggal_pks'] ?? ($spek['kemitraan_tanggal_pks'] ?? ($existingKem->tanggal_pks ?? ($specLamaData['kemitraan_tanggal_pks'] ?? ($specLamaData['tanggal_pks'] ?? ''))))));
            $tglMulai = trim((string) ($firstItem['kemitraan_tanggal_mulai'] ?? ($spek['kemitraan_tanggal_mulai'] ?? ($existingKem->tanggal_mulai ?? ($existingKem->tanggal_pks ?? ($specLamaData['kemitraan_tanggal_mulai'] ?? ($specLamaData['tanggal_mulai'] ?? '')))))));
            $tglSelesai = trim((string) ($firstItem['kemitraan_tanggal_selesai'] ?? ($spek['kemitraan_tanggal_selesai'] ?? ($existingKem->tanggal_selesai ?? ($specLamaData['kemitraan_tanggal_selesai'] ?? ($specLamaData['tanggal_selesai'] ?? ''))))));

            if (empty($mitraNama)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nama Perusahaan Mitra / Rekanan wajib diisi untuk reklasifikasi ke Kemitraan.',
                ], 422);
            }
            if (empty($pimpinan)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pejabat Mitra / Direktur Rekanan wajib diisi untuk reklasifikasi ke Kemitraan.',
                ], 422);
            }
            if (empty($pksNo)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nomor Dokumen Perjanjian (PKS / MoU) wajib diisi untuk reklasifikasi ke Kemitraan.',
                ], 422);
            }
            if (empty($pksTgl)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tanggal Penandatanganan PKS wajib dipilih untuk reklasifikasi ke Kemitraan.',
                ], 422);
            }
            if (empty($tglMulai) && empty($existingKem)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tanggal Mulai Berlaku Konsesi wajib dipilih untuk reklasifikasi ke Kemitraan.',
                ], 422);
            }
            if (empty($tglSelesai) && empty($existingKem)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tanggal Berakhir Konsesi wajib dipilih untuk reklasifikasi ke Kemitraan.',
                ], 422);
            }
            if ($tglMulai && $tglSelesai && strtotime($tglSelesai) < strtotime($tglMulai)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tanggal Berakhir Konsesi tidak boleh mendahului Tanggal Mulai Berlaku.',
                ], 422);
            }
        }

        // Proteksi Validasi: Aset atau unit yang sudah dimutasi keluar ke OPD lain tidak dapat dimutasi lagi
        if ($validated['jenis_reklas'] === 'MUTASI_EKSTERNAL') {
            $isAlreadyFullOut = ($astap->jenis_reklas === 'MUTASI_EKSTERNAL')
                || (bool) $astap->is_mutasi_keluar_full;

            $activeRegistersCount = $astap->registers()
                ->where('is_deleted', 0)
                ->where(function($q) {
                    $q->whereNull('status')
                      ->orWhere('status', '!=', 'Mutasi Keluar OPD');
                })
                ->count();
            $totalRegistersCount = $astap->registers()->where('is_deleted', 0)->count();

            if ($isAlreadyFullOut || ($totalRegistersCount > 0 && $activeRegistersCount === 0)) {
                return response()->json([
                    'success' => false,
                    'message' => "Aset '{$astap->nama_barang}' sudah pernah dimutasi keluar ke OPD lain (Mutasi Eksternal) dan tidak dapat dimutasi lagi.",
                ], 422);
            }

            // Validasi register yang dipilih
            $selectedRegIds = $request->input('selected_register_ids');
            if (is_array($selectedRegIds) && count($selectedRegIds) === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pilih minimal 1 unit register NIBAR yang akan dimutasikan ke OPD luar.',
                ], 422);
            }

            if (!empty($selectedRegIds) && is_array($selectedRegIds)) {
                $alreadyOutRegs = \App\Models\AstapRegister::whereIn('id', $selectedRegIds)
                    ->where('status', 'Mutasi Keluar OPD')
                    ->pluck('nibar')
                    ->toArray();

                if (!empty($alreadyOutRegs)) {
                    $nibarList = implode(', ', $alreadyOutRegs);
                    return response()->json([
                        'success' => false,
                        'message' => "Unit register NIBAR ({$nibarList}) sudah pernah dimutasi keluar ke OPD lain dan tidak dapat dimutasi lagi.",
                    ], 422);
                }
            }
        }

        // Proteksi Validasi Duplikasi: Validasi Berbasis Status Riil Aset Saat Ini (Bukan Riwayat Log Masa Lalu)
        if ($validated['jenis_reklas'] === 'EKSTRAKOMPTABEL') {
            $alreadyExtracom = (bool) $astap->is_extracomtable || $astap->jenis_reklas === 'EKSTRAKOMPTABEL' || $astap->category === 'EKSTRAKOMPTABEL';
            if ($alreadyExtracom) {
                return response()->json([
                    'success' => false,
                    'message' => "Aset '{$astap->nama_barang}' saat ini sudah berstatus EKSTRAKOMPTABEL. Transaksi reklasifikasi ke Ekstrakomptabel tidak dapat dilakukan kembali.",
                ], 422);
            }
        } elseif ($validated['jenis_reklas'] === 'KAPITALISASI_INTRAKOM') {
            $alreadyIntracom = !$astap->is_extracomtable && $astap->jenis_reklas !== 'EKSTRAKOMPTABEL' && $astap->category !== 'EKSTRAKOMPTABEL';
            if ($alreadyIntracom && empty($request->reklas_items)) {
                return response()->json([
                    'success' => false,
                    'message' => "Aset '{$astap->nama_barang}' saat ini sudah berstatus INTRAKOMPTABEL dan tidak memiliki penambahan rincian nilai kapitalisasi.",
                ], 422);
            }
        } elseif ($validated['jenis_reklas'] === 'DEFINITIF_TO_KDP') {
            $kodeJenis = (string) ($astap->jenisAstap?->jenis ?? $astap->jenisAstap?->sub_sub_rincian_objek ?? '');
            $alreadyKdp = $astap->category === 'KDP' || str_starts_with($kodeJenis, '1.3.6') || $astap->jenis_reklas === 'DEFINITIF_TO_KDP';
            if ($alreadyKdp) {
                return response()->json([
                    'success' => false,
                    'message' => "Aset '{$astap->nama_barang}' saat ini sudah berstatus Konstruksi Dalam Pengerjaan (KDP). Transaksi ke KDP tidak dapat dilakukan kembali.",
                ], 422);
            }
        }

        if (!empty($validated['nomor_ba_reklas'])) {
            $baseBa = trim($validated['nomor_ba_reklas']);
            $duplicateBa = AstapReklas::where('astap_id', $astap->id)
                ->where('nomor_ba_reklas', $baseBa)
                ->where('is_deleted', 0)
                ->exists();

            if ($duplicateBa) {
                // Cek apakah nomor BA ini merupakan rujukan dokumen pengadaan (BAST / SPK / SP / Faktur / Kwitansi / SP2D)
                // atau nomor bawaan pengadaan aset. Jika ya, jangan memblokir transaksi reklasifikasi lanjutan aset ini,
                // melainkan beri suffix urutan reklas secara otomatis agar tetap unik dan terdata rapi di audit log.
                $isAutoProcurementDoc = str_starts_with($baseBa, 'BAST:') 
                    || str_starts_with($baseBa, 'SPK:') 
                    || str_starts_with($baseBa, 'SP:') 
                    || str_starts_with($baseBa, 'Faktur:') 
                    || str_starts_with($baseBa, 'Kwitansi:') 
                    || str_starts_with($baseBa, 'SP2D:')
                    || ($astap->bast_dokumen_nomor && str_contains($baseBa, trim($astap->bast_dokumen_nomor)))
                    || ($astap->spk_nomor && str_contains($baseBa, trim($astap->spk_nomor)));

                if ($isAutoProcurementDoc) {
                    $existingCount = AstapReklas::where('astap_id', $astap->id)
                        ->where('nomor_ba_reklas', 'like', $baseBa . '%')
                        ->where('is_deleted', 0)
                        ->count();
                    $validated['nomor_ba_reklas'] = "{$baseBa} (Reklas #" . ($existingCount + 1) . ")";
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => "Nomor Berita Acara '{$validated['nomor_ba_reklas']}' sudah pernah digunakan untuk transaksi reklasifikasi aset ini.",
                    ], 422);
                }
            }
        }

        // Proteksi Validasi Aturan Bisnis: Kemitraan Aktif (Akun 1.5.2)
        $kemitraan = $astap->kemitraan ?? \App\Models\AstapKemitraan::where(function ($q) use ($astap) {
            $q->where('astap_id', $astap->id)->orWhere('objek_astap_id', $astap->id);
        })->where('is_deleted', 0)->first();

        if ($kemitraan && in_array($kemitraan->status_konsesi, ['Dikembalikan ke Rekanan', 'Dibatalkan'])) {
            return response()->json([
                'success' => false,
                'message' => "Aset kemitraan telah dialihkan / dikembalikan kepada pihak ketiga rekanan kerja sama (Status: {$kemitraan->status_konsesi}) dan terkunci permanen dari reklasifikasi.",
            ], 422);
        }

        $rawStatusKonsesi = $kemitraan ? strtolower(trim((string) $kemitraan->status_konsesi)) : '';
        $endedStatuses = [
            'konsesi berakhir',
            'selesai / reklasifikasi',
            'selesai',
            'dihentikan',
            'berakhir',
            'putus kontrak',
        ];

        $isKemitraanAktif = false;
        if ($kemitraan) {
            $isKemitraanAktif = empty($rawStatusKonsesi) || !in_array($rawStatusKonsesi, $endedStatuses, true);
        } else {
            $kodeAsetNow = $astap->jenisAstap?->sub_sub_rincian_objek ?: ($astap->jenisAstap?->sub_rincian_objek ?: $astap->jenisAstap?->jenis);
            if ($astap->category === 'KEMITRAAN' || str_starts_with((string) $kodeAsetNow, '1.5.2') || $astap->sumber_dana === 'kemitraan') {
                $isKemitraanAktif = true;
            }
        }

        if ($isKemitraanAktif) {
            // 1. Dilarang reklasifikasi keluar ke Ekstrakomptabel, KDP, Hibah, atau Mutasi Eksternal
            if ($validated['jenis_reklas'] !== 'KOREKSI_REKENING') {
                return response()->json([
                    'success' => false,
                    'message' => 'Aset masih terikat perjanjian kerja sama kemitraan yang aktif (Akun 1.5.2). Reklasifikasi ke Ekstrakomptabel, KDP, Hibah, atau Mutasi Eksternal tidak diperbolehkan selama masa konsesi PKS masih aktif.',
                ], 422);
            }

            // 2. Untuk KOREKSI_REKENING, hanya boleh seputar Akun 1.5.2 Kemitraan (tujuan_kib = KEMITRAAN dan kode_108 berawalan 1.5.2)
            $targetKibCheck = $validated['tujuan_kib'] ?? null;
            $targetKodeCheck = $request->input('tujuan_kode') ?: ($request->input('kode_108') ?: null);

            if ($targetKibCheck !== 'KEMITRAAN') {
                return response()->json([
                    'success' => false,
                    'message' => 'Status kemitraan masih aktif! Reklasifikasi pindah rekening hanya diperbolehkan seputar sub-rekening Kemitraan Pihak Ketiga (Akun 1.5.2). Aset tidak dapat dipindahkan ke KIB A–F atau Aset Lain-Lain (1.5.4) sebelum masa konsesi PKS resmi berakhir / diselesaikan.',
                ], 422);
            }

            if (!empty($targetKodeCheck) && !str_starts_with((string) $targetKodeCheck, '1.5.2')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kode rekening tujuan harus merupakan sub-rekening di bawah Akun 1.5.2 (Kemitraan dengan Pihak Ketiga).',
                ], 422);
            }
        }

        // Proteksi Validasi Aturan Bisnis: Aset Konstruksi Dalam Pengerjaan (KDP / KIB F / Akun 1.3.6)
        // KDP merupakan bangunan/proyek fisik yang belum selesai dan belum berwujud barang definitif.
        // Aset KDP tidak dapat dipindah KIB biasa, dihibahkan, dimutasi ke luar OPD, atau diekstrakomptabelkan.
        $kodeAsetAsal = (string) ($astap->kode_108 ?: ($astap->jenisAstap?->sub_sub_rincian_objek ?: ($astap->jenisAstap?->jenis ?? '')));
        $isKdpAsset = ($astap->category === 'KIB F' || $astap->category === 'KDP' || str_starts_with($kodeAsetAsal, '1.3.6'));

        if ($isKdpAsset && in_array($validated['jenis_reklas'], ['KOREKSI_REKENING', 'HIBAH_MASUK', 'HIBAH_KELUAR', 'MUTASI_EKSTERNAL', 'EKSTRAKOMPTABEL', 'KAPITALISASI_INTRAKOM'], true)) {
            return response()->json([
                'success' => false,
                'message' => "Aset '{$astap->nama_barang}' merupakan aset Konstruksi Dalam Pengerjaan (KDP / KIB F) yang belum selesai. Aset KDP tidak dapat dipindah KIB, dihibahkan, atau dimutasikan ke luar OPD. Silakan gunakan opsi 'Kapitalisasi KDP Selesai' jika fisik pekerjaan telah rampung 100%, atau opsi 'Koreksi Nilai Realisasi / BPK'.",
            ], 422);
        }

        // Proteksi Validasi Koreksi Nilai Realisasi / BPK / Manset (KOREKSI_LAIN)
        if ($validated['jenis_reklas'] === 'KOREKSI_LAIN') {
            $selisihCheck = (float) ($validated['nilai_reklas'] ?? 0);
            $subKoreksiCheck = in_array($request->input('sub_koreksi'), ['biasa', 'lkd', 'manset']) ? $request->input('sub_koreksi') : 'biasa';

            if ($request->has('reklas_items') && is_array($request->reklas_items) && count($request->reklas_items) > 0) {
                $totalBaruCheck = 0;
                foreach ($request->reklas_items as $rItem) {
                    $vol = (int) ($rItem['jumlah_volume'] ?? 1);
                    $hrg = (float) ($rItem['harga_satuan'] ?? 0);
                    $totalBaruCheck += ($vol * $hrg);
                }
                $selisihCheck = abs($totalBaruCheck - (float) $astap->total_realisasi);
            } elseif ($request->filled('nilai_realisasi_baru')) {
                $nilaiBaruCheck = (float) $request->input('nilai_realisasi_baru');
                $selisihCheck = abs($nilaiBaruCheck - (float) $astap->total_realisasi);
            }

            if ($selisihCheck <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nilai realisasi baru sama dengan nilai semula (selisih Rp 0). Tidak ada perubahan nominal untuk disimpan.'
                ], 422);
            }

            if (in_array($subKoreksiCheck, ['lkd', 'manset']) && empty(trim((string)($validated['nomor_ba_reklas'] ?? '')))) {
                $docName = ($subKoreksiCheck === 'lkd') ? 'Nomor LHP BPK RI / Rekomendasi' : 'Nomor BA Rekonsiliasi Manset BPKAD';
                return response()->json([
                    'success' => false,
                    'message' => "{$docName} wajib diisi sebagai dasar hukum penyesuaian nilai."
                ], 422);
            }
        }

        DB::beginTransaction();
        try {
            $specLama = is_array($astap->spesifikasi_json) ? $astap->spesifikasi_json : (json_decode($astap->spesifikasi_json, true) ?? []);

            $selectedIndices = $request->input('selected_indices');
            $isPartial = $request->boolean('is_partial_reklas') 
                && is_array($selectedIndices) 
                && count($selectedIndices) > 0 
                && (
                    (is_array($request->input('unselected_items')) && count($request->input('unselected_items')) > 0)
                    || count($selectedIndices) < max(1, (int) $astap->jumlah_volume)
                );

            // Catat identitas rekening & KIB asal sebelum aset dimodifikasi
            $asalKibAset = ($validated['asal_kib'] ?? null) ?: ($astap->category ?: 'KIB B');
            $asalKodeAset = $astap->jenisAstap?->sub_sub_rincian_objek ?: ($astap->jenisAstap?->sub_rincian_objek ?: $astap->jenisAstap?->jenis);
            $asalNamaAset = $astap->jenisAstap?->uraian_sub_sub_rincian 
                ?: ($astap->jenisAstap?->uraian_sub_rincian 
                ?: ($astap->jenisAstap?->nama_jenis 
                ?: ($astap->nama_barang ?: 'Aset Tidak Dikenal')));
            $tujuanKodeAset = $request->input('tujuan_kode') ?: ($request->input('kode_108') ?: null);
            $tujuanNamaAset = $request->input('tujuan_nama');

            if (($isExtracom || $isIntracom) && !empty($request->reklas_items) && is_array($request->reklas_items)) {
                $totalBaru = 0;
                $totalVolume = 0;
                
                foreach ($request->reklas_items as $rItem) {
                    $harga = (float) ($rItem['harga_satuan'] ?? 0);
                    $qty = (int) ($rItem['jumlah_volume'] ?? 1);
                    $totalBaru += ($qty * $harga);
                    $totalVolume += $qty;
                }

                $spec = is_array($astap->spesifikasi_json) ? $astap->spesifikasi_json : (json_decode($astap->spesifikasi_json, true) ?? []);

                if (isset($spec['mesin_items']) && is_array($spec['mesin_items'])) {
                    $updatedMesinItems = [];
                    foreach ($request->reklas_items as $idx => $rItem) {
                        $existing = $spec['mesin_items'][$idx] ?? ($spec['mesin_items'][0] ?? []);
                        $existing['mesin_nama_barang'] = $rItem['nama_barang'] ?? ($existing['mesin_nama_barang'] ?? $astap->nama_barang);
                        $existing['mesin_jumlah_barang'] = (int) ($rItem['jumlah_volume'] ?? 1);
                        $existing['mesin_satuan'] = $rItem['satuan'] ?? ($existing['mesin_satuan'] ?? ($astap->satuan ?: 'Unit'));
                        $existing['mesin_nilai_satuan'] = (float) ($rItem['harga_satuan'] ?? 0);
                        $existing['mesin_total_nilai'] = (int) ($rItem['jumlah_volume'] ?? 1) * (float) ($rItem['harga_satuan'] ?? 0);
                        $updatedMesinItems[] = $existing;
                    }
                    $spec['mesin_items'] = $updatedMesinItems;
                } elseif (isset($spec['lainnya_items']) && is_array($spec['lainnya_items'])) {
                    $updatedLainnyaItems = [];
                    foreach ($request->reklas_items as $idx => $rItem) {
                        $existing = $spec['lainnya_items'][$idx] ?? ($spec['lainnya_items'][0] ?? []);
                        $existing['lainnya_nama_barang'] = $rItem['nama_barang'] ?? ($existing['lainnya_nama_barang'] ?? $astap->nama_barang);
                        $existing['lainnya_jumlah_barang'] = (int) ($rItem['jumlah_volume'] ?? 1);
                        $existing['lainnya_satuan'] = $rItem['satuan'] ?? ($existing['lainnya_satuan'] ?? ($astap->satuan ?: 'Unit'));
                        $existing['lainnya_nilai_satuan'] = (float) ($rItem['harga_satuan'] ?? 0);
                        $existing['lainnya_total_nilai'] = (int) ($rItem['jumlah_volume'] ?? 1) * (float) ($rItem['harga_satuan'] ?? 0);
                        $updatedLainnyaItems[] = $existing;
                    }
                    $spec['lainnya_items'] = $updatedLainnyaItems;
                } else {
                    if (count($request->reklas_items) > 1) {
                        $updatedMesinItems = [];
                        foreach ($request->reklas_items as $idx => $rItem) {
                            $updatedMesinItems[] = [
                                'mesin_nama_barang' => $rItem['nama_barang'] ?? $astap->nama_barang,
                                'mesin_jumlah_barang' => (int) ($rItem['jumlah_volume'] ?? 1),
                                'mesin_satuan' => $rItem['satuan'] ?? ($astap->satuan ?: 'Unit'),
                                'mesin_nilai_satuan' => (float) ($rItem['harga_satuan'] ?? 0),
                                'mesin_total_nilai' => (int) ($rItem['jumlah_volume'] ?? 1) * (float) ($rItem['harga_satuan'] ?? 0),
                            ];
                        }
                        $spec['mesin_items'] = $updatedMesinItems;
                    } else {
                        $firstItem = $request->reklas_items[0] ?? [];
                        if (!empty($firstItem['nama_barang'])) {
                            $astap->nama_barang = $firstItem['nama_barang'];
                        }
                    }
                }

                $firstHarga = count($request->reklas_items) > 0 ? (float) $request->reklas_items[0]['harga_satuan'] : (float) ($totalBaru / max(1, $totalVolume));
                $astap->spesifikasi_json = $spec;
                $astap->harga_satuan = $firstHarga;
                $astap->jumlah_volume = $totalVolume;
                $astap->total_realisasi = $totalBaru;

                if ($isIntracom) {
                    $astap->is_extracomtable = false;
                    $targetKib = $validated['tujuan_kib'] ?: 'KIB B';
                    $astap->category = $targetKib;
                    if ($targetKib === 'KIB E') {
                        if (!$astap->jenisAstap || !str_starts_with($astap->jenisAstap->jenis, '1.3.5')) {
                            $matchingJenis = JenisAstap::where('jenis', 'like', '1.3.5%')->first();
                            if ($matchingJenis) $astap->jenis_astap_id = $matchingJenis->id;
                        }
                    } elseif ($targetKib === 'KIB B') {
                        if (!$astap->jenisAstap || !str_starts_with($astap->jenisAstap->jenis, '1.3.2')) {
                            $matchingJenis = JenisAstap::where('jenis', 'like', '1.3.2%')->first();
                            if ($matchingJenis) $astap->jenis_astap_id = $matchingJenis->id;
                        }
                    }
                } else {
                    $astap->is_extracomtable = true;
                }

                $validated['nilai_reklas'] = $totalBaru;
            }

            if (!$isPartial) {
                $astap->is_reklas = true;
                $astap->jenis_reklas = $validated['jenis_reklas'];
            }
            if ($validated['jenis_reklas'] === 'EKSTRAKOMPTABEL') {
                $astap->is_extracomtable = true;
                $tujuanKodeAset = 'KOR_EXTRACOM';
                $tujuanNamaAset = 'Koreksi Di Bawah Batas Kapitalisasi (Ekstrakomptabel)';
            } elseif ($validated['jenis_reklas'] === 'KAPITALISASI_INTRAKOM') {
                $astap->is_extracomtable = false;
                $asalKodeAset = 'KOR_EXTRACOM';
                $asalNamaAset = 'Aset Ekstrakomptabel (≤ Rp 300.000)';
                $targetKib = $validated['tujuan_kib'] ?: 'KIB B';
                $astap->category = $targetKib;
                if ($targetKib === 'KIB E') {
                    if (!$astap->jenisAstap || !str_starts_with($astap->jenisAstap->jenis, '1.3.5')) {
                        $matchingJenis = JenisAstap::where('jenis', 'like', '1.3.5%')->first();
                        if ($matchingJenis) $astap->jenis_astap_id = $matchingJenis->id;
                    }
                } elseif ($targetKib === 'KIB B') {
                    if (!$astap->jenisAstap || !str_starts_with($astap->jenisAstap->jenis, '1.3.2')) {
                        $matchingJenis = JenisAstap::where('jenis', 'like', '1.3.2%')->first();
                        if ($matchingJenis) $astap->jenis_astap_id = $matchingJenis->id;
                    }
                }
                $tujuanKodeAset = $astap->jenisAstap?->sub_sub_rincian_objek ?: ($astap->jenisAstap?->sub_rincian_objek ?: $astap->jenisAstap?->jenis);
                $tujuanNamaAset = $astap->jenisAstap?->uraian_sub_sub_rincian ?: ($astap->jenisAstap?->uraian_sub_rincian ?: ($astap->jenisAstap?->nama_jenis ?: $astap->nama_barang));
            } elseif ($validated['jenis_reklas'] === 'DEFINITIF_TO_KDP') {
                $matchingJenis = JenisAstap::where('jenis', 'like', '1.3.6%')->first();
                if (!$matchingJenis) {
                    $matchingJenis = JenisAstap::firstOrCreate(
                        ['jenis' => '1.3.6.01'],
                        [
                            'nama_jenis' => 'Konstruksi Dalam Pengerjaan',
                            'sub_rincian_objek' => '1.3.6.01.01',
                            'uraian_sub_rincian' => 'Konstruksi Dalam Pengerjaan',
                            'sub_sub_rincian_objek' => '1.3.6.01.01.01',
                            'uraian_sub_sub_rincian' => 'Konstruksi Dalam Pengerjaan',
                        ]
                    );
                }
                if ($matchingJenis) $astap->jenis_astap_id = $matchingJenis->id;
                $tujuanKodeAset = '1.3.6.01.01';
                $tujuanNamaAset = 'Konstruksi Dalam Pengerjaan';
            } elseif ($validated['jenis_reklas'] === 'KDP_TO_DEFINITIF') {
                $asalKodeAset = '1.3.6.01.01';
                $asalNamaAset = 'Konstruksi Dalam Pengerjaan (KIB F)';
                $targetKib = ($validated['tujuan_kib'] === 'KIB D') ? 'KIB D' : 'KIB C';
                $astap->category = $targetKib;

                // Mutlak KDP: Progres fisik konstruksi diubah dan dikunci menjadi 100% (Pekerjaan Selesai Penuh & Terbit BAST)
                $specKdp = is_array($astap->spesifikasi_json) ? $astap->spesifikasi_json : (json_decode($astap->spesifikasi_json, true) ?? []);
                $specKdp['progres_persen'] = 100;
                $specKdp['kdp_progres_persen'] = 100;
                $specKdp['progres_fisik'] = '100%';
                $specKdp['status_kdp'] = 'Selesai 100%';
                if (isset($specKdp['kdp_items']) && is_array($specKdp['kdp_items'])) {
                    foreach ($specKdp['kdp_items'] as &$kItem) {
                        $kItem['kdp_progres_persen'] = 100;
                        $kItem['progres_persen'] = 100;
                        $kItem['progres_fisik'] = '100%';
                    }
                    unset($kItem);
                }
                $astap->spesifikasi_json = $specKdp;
                $specBaru = $specKdp;
                if ($targetKib === 'KIB D') {
                    $matchingJenis = JenisAstap::where('jenis', 'like', '1.3.4%')->first();
                    if (!$matchingJenis) {
                        $matchingJenis = JenisAstap::firstOrCreate(
                            ['jenis' => '1.3.4.01'],
                            [
                                'nama_jenis' => 'Jalan, Jaringan dan Irigasi',
                                'sub_rincian_objek' => '1.3.4.01.01',
                                'uraian_sub_rincian' => 'Jalan, Jaringan dan Irigasi',
                                'sub_sub_rincian_objek' => '1.3.4.01.01.01',
                                'uraian_sub_sub_rincian' => 'Jalan, Jaringan dan Irigasi',
                            ]
                        );
                    }
                    if ($matchingJenis) $astap->jenis_astap_id = $matchingJenis->id;
                } else {
                    $matchingJenis = JenisAstap::where('jenis', 'like', '1.3.3%')->first();
                    if (!$matchingJenis) {
                        $matchingJenis = JenisAstap::firstOrCreate(
                            ['jenis' => '1.3.3.01'],
                            [
                                'nama_jenis' => 'Gedung dan Bangunan',
                                'sub_rincian_objek' => '1.3.3.01.01',
                                'uraian_sub_rincian' => 'Gedung dan Bangunan',
                                'sub_sub_rincian_objek' => '1.3.3.01.01.01',
                                'uraian_sub_sub_rincian' => 'Gedung dan Bangunan',
                            ]
                        );
                    }
                    if ($matchingJenis) $astap->jenis_astap_id = $matchingJenis->id;
                }
                $tujuanKodeAset = $astap->jenisAstap?->sub_sub_rincian_objek ?: ($astap->jenisAstap?->sub_rincian_objek ?: '1.3.3.01.01');
                $tujuanNamaAset = $astap->jenisAstap?->uraian_sub_sub_rincian ?: ($astap->jenisAstap?->uraian_sub_rincian ?: 'Bangunan Gedung Definitif');
            } elseif ($validated['jenis_reklas'] === 'KOREKSI_LAIN') {
                $selisih = (float) $validated['nilai_reklas'];
                $tipe = $request->input('tipe_koreksi', 'kurang');
                $subKoreksi = in_array($request->input('sub_koreksi'), ['biasa', 'lkd', 'manset']) ? $request->input('sub_koreksi') : 'biasa';
                $validated['sub_koreksi'] = $subKoreksi;

                // Defensive validation: Cegah koreksi nilai dengan selisih 0 jika bukan dari reklas_items
                if ($selisih <= 0 && !$request->has('reklas_items') && !$request->filled('nilai_realisasi_baru')) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Nilai realisasi baru sama dengan nilai semula (selisih Rp 0). Tidak ada perubahan nominal untuk disimpan.'
                    ], 422);
                }

                // Defensive validation: Wajibkan nomor dokumen untuk LKD dan Manset
                if (in_array($subKoreksi, ['lkd', 'manset']) && empty(trim((string)($validated['nomor_ba_reklas'] ?? '')))) {
                    DB::rollBack();
                    $docName = ($subKoreksi === 'lkd') ? 'Nomor LHP BPK RI / Rekomendasi' : 'Nomor BA Rekonsiliasi Manset BPKAD';
                    return response()->json([
                        'success' => false,
                        'message' => "{$docName} wajib diisi sebagai dasar hukum penyesuaian nilai."
                    ], 422);
                }

                // Handle berkas lampiran LHP / BA jika diunggah
                $dokumenLampiranPath = null;
                if ($request->hasFile('dokumen_file')) {
                    $docFile = $request->file('dokumen_file');
                    $docFilename = 'KOREKSI_' . time() . '_' . preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $docFile->getClientOriginalName());
                    $dokumenLampiranPath = $docFile->storeAs('dokumen_reklasifikasi', $docFilename, 'public');
                }

                // Nilai anggaran hanya berlaku untuk Belanja Modal APBD/BLUD (bukan Hibah/Kemitraan)
                if ($astap->isBelanjaModal() && $request->filled('jumlah_anggaran')) {
                    $astap->jumlah_anggaran = (float) $request->input('jumlah_anggaran');
                }

                // Jika ada penyesuaian nilai kapitalisasi rincian barang 1, 2, dst
                if ($request->has('reklas_items') && is_array($request->reklas_items) && count($request->reklas_items) > 0) {
                    $spec = is_array($astap->spesifikasi_json) ? $astap->spesifikasi_json : (json_decode($astap->spesifikasi_json, true) ?? []);
                    $totalBaru = 0;
                    $oldRealisasi = (float) $astap->total_realisasi;

                    $repeaterKeys = ['mesin_items', 'lainnya_items', 'gedung_items', 'jaringan_items', 'kdp_items', 'tanah_items', 'atb_items'];
                    $activeKey = null;
                    foreach ($repeaterKeys as $rk) {
                        if (!empty($spec[$rk]) && is_array($spec[$rk])) {
                            $activeKey = $rk;
                            break;
                        }
                    }

                    foreach ($request->reklas_items as $idx => $rItem) {
                        $vol = (int) ($rItem['jumlah_volume'] ?? 1);
                        $hrg = (float) ($rItem['harga_satuan'] ?? 0);
                        $totalBaru += ($vol * $hrg);

                        if ($activeKey && isset($spec[$activeKey][$idx])) {
                            if ($activeKey === 'mesin_items') {
                                $spec[$activeKey][$idx]['mesin_nilai_satuan'] = $hrg;
                                $spec[$activeKey][$idx]['mesin_total_nilai'] = $vol * $hrg;
                            } elseif ($activeKey === 'lainnya_items') {
                                $spec[$activeKey][$idx]['lainnya_nilai_satuan'] = $hrg;
                                $spec[$activeKey][$idx]['lainnya_total_nilai'] = $vol * $hrg;
                            } elseif ($activeKey === 'gedung_items') {
                                $spec[$activeKey][$idx]['gedung_nilai_fisik'] = $hrg;
                            } elseif ($activeKey === 'jaringan_items') {
                                $spec[$activeKey][$idx]['jaringan_nilai_fisik'] = $hrg;
                            } elseif ($activeKey === 'kdp_items') {
                                $spec[$activeKey][$idx]['kdp_nilai_fisik'] = $hrg;
                            } elseif ($activeKey === 'tanah_items') {
                                $spec[$activeKey][$idx]['tanah_nilai_fisik'] = $hrg;
                            } elseif ($activeKey === 'atb_items') {
                                $spec[$activeKey][$idx]['atb_nilai_satuan'] = $hrg;
                            }
                        }
                    }

                    if (!$activeKey && count($request->reklas_items) === 1) {
                        $firstItem = $request->reklas_items[0];
                        $astap->harga_satuan = (float) ($firstItem['harga_satuan'] ?? 0);
                    }

                    if ($totalBaru >= 0) {
                        $astap->total_realisasi = $totalBaru;
                        if ($astap->jumlah_volume > 0) {
                            $astap->harga_satuan = $totalBaru / $astap->jumlah_volume;
                        }
                        $selisih = abs($totalBaru - $oldRealisasi);
                        if ($selisih <= 0) {
                            DB::rollBack();
                            return response()->json([
                                'success' => false,
                                'message' => 'Nilai realisasi baru sama dengan nilai semula (selisih Rp 0). Tidak ada perubahan nominal untuk disimpan.'
                            ], 422);
                        }
                        $validated['nilai_reklas'] = $selisih;
                        $tipe = ($totalBaru >= $oldRealisasi) ? 'tambah' : 'kurang';
                    }

                    $spec['koreksi_info'] = [
                        'sub_koreksi'            => $subKoreksi,
                        'tipe_koreksi'           => $tipe,
                        'nilai_semula'           => $oldRealisasi,
                        'nilai_baru'             => (float) $astap->total_realisasi,
                        'selisih'                => $selisih,
                        'nomor_dokumen'          => $validated['nomor_ba_reklas'] ?? null,
                        'tanggal_dokumen'        => $request->input('tanggal_dokumen_koreksi') ?? ($validated['tanggal_reklas'] ?? null),
                        'alasan'                 => $validated['alasan_reklas'] ?? null,
                        'dokumen_path'           => $dokumenLampiranPath ?? null,
                    ];
                    $astap->spesifikasi_json = $spec;
                    $specBaru = $spec;
                } else {
                    $oldRealisasi = (float) $astap->total_realisasi;
                    if ($request->filled('nilai_realisasi_baru')) {
                        $totalBaru = (float) $request->input('nilai_realisasi_baru');
                        $selisih = abs($totalBaru - $oldRealisasi);
                        if ($selisih <= 0) {
                            DB::rollBack();
                            return response()->json([
                                'success' => false,
                                'message' => 'Nilai realisasi baru sama dengan nilai semula (selisih Rp 0). Tidak ada perubahan nominal untuk disimpan.'
                            ], 422);
                        }
                        $tipe = ($totalBaru >= $oldRealisasi) ? 'tambah' : 'kurang';
                        $astap->total_realisasi = $totalBaru;
                    } else {
                        if ($tipe === 'kurang') {
                            $astap->total_realisasi = max(0, (float) $astap->total_realisasi - $selisih);
                        } else {
                            $astap->total_realisasi = (float) $astap->total_realisasi + $selisih;
                        }
                    }
                    $validated['nilai_reklas'] = $selisih;
                    if ($astap->jumlah_volume > 0) {
                        $astap->harga_satuan = $astap->total_realisasi / $astap->jumlah_volume;
                    }

                    $currSpec = is_array($astap->spesifikasi_json) ? $astap->spesifikasi_json : (json_decode($astap->spesifikasi_json, true) ?? []);
                    $currSpec['koreksi_info'] = [
                        'sub_koreksi'            => $subKoreksi,
                        'tipe_koreksi'           => $tipe,
                        'nilai_semula'           => $oldRealisasi,
                        'nilai_baru'             => (float) $astap->total_realisasi,
                        'selisih'                => $selisih,
                        'nomor_dokumen'          => $validated['nomor_ba_reklas'] ?? null,
                        'tanggal_dokumen'        => $request->input('tanggal_dokumen_koreksi') ?? ($validated['tanggal_reklas'] ?? null),
                        'alasan'                 => $validated['alasan_reklas'] ?? null,
                        'dokumen_path'           => $dokumenLampiranPath ?? null,
                    ];
                    $astap->spesifikasi_json = $currSpec;
                    $specBaru = $currSpec;
                }

                // Tentukan baris penyeimbang di neraca untuk KOREKSI_LAIN
                $allTemplateRows = JenisReklasifikasi::active()->get();
                $korLainRow = $allTemplateRows->firstWhere('kode_prefix', 'KOR_LAIN');
                $astapRow = $this->matchAstapToRow($astap, $allTemplateRows);

                $labelSub = match ($subKoreksi) {
                    'lkd'    => 'Temuan Audit LHP BPK',
                    'manset' => 'Penyelarasan Bidang Aset BPKAD / Manset',
                    default  => 'Rekonsiliasi Internal Kas RSUD',
                };

                if ($tipe === 'tambah') {
                    // Nilai aset bertambah: Penyeimbang (Asal) -> Akun Aset Tetap (Tujuan)
                    $validated['jenis_reklasifikasi_asal_id'] = $korLainRow?->id;
                    $validated['jenis_reklasifikasi_tujuan_id'] = $astapRow?->id;
                    $validated['asal_kib'] = 'KOREKSI';
                    $validated['tujuan_kib'] = $astap->category ?: ($astapRow?->kelompok_kib ?: 'KIB B');
                    $tujuanKodeAset = $asalKodeAset;
                    $tujuanNamaAset = $asalNamaAset;
                    $asalKodeAset = 'KOR_LAIN';
                    $asalNamaAset = "Koreksi Lain-Lain (Penambahan Nilai - {$labelSub})";
                } else {
                    // Nilai aset berkurang: Akun Aset Tetap (Asal) -> Penyeimbang (Tujuan)
                    $validated['jenis_reklasifikasi_asal_id'] = $astapRow?->id;
                    $validated['jenis_reklasifikasi_tujuan_id'] = $korLainRow?->id;
                    $validated['asal_kib'] = $astap->category ?: ($astapRow?->kelompok_kib ?: 'KIB B');
                    $validated['tujuan_kib'] = 'KOREKSI';
                    $tujuanKodeAset = 'KOR_LAIN';
                    $tujuanNamaAset = "Koreksi Lain-Lain (Pengurangan Nilai - {$labelSub})";
                }
            } elseif ($validated['jenis_reklas'] === 'KOREKSI_REKENING') {
                $targetKib = $validated['tujuan_kib'] ?? null;
                $targetKode = $request->input('tujuan_kode') ?: ($request->input('kode_108') ?: null);
                $targetNama = $request->input('tujuan_nama');

                if ($targetKib && !$isPartial) {
                    $astap->category = $targetKib;
                }

                if ($targetKode) {
                    $matchingJenis = JenisAstap::where('sub_sub_rincian_objek', $targetKode)
                        ->orWhere('sub_rincian_objek', $targetKode)
                        ->orWhere('jenis', $targetKode)
                        ->first();
                    if ($matchingJenis) {
                        if (!$isPartial) {
                            $astap->jenis_astap_id = $matchingJenis->id;
                        }
                        $tujuanKodeAset = $targetKode;
                        $tujuanNamaAset = $targetNama ?: ($matchingJenis->uraian_sub_sub_rincian ?: ($matchingJenis->uraian_sub_rincian ?: $matchingJenis->nama_jenis));
                    }
                    if ($targetNama && !empty($matchingJenis?->sub_sub_rincian_objek) && !$isPartial) {
                        // Untuk Kemitraan (Akun 1.5.2), pertahankan nama fisik aset (jangan ditimpa nama rekening generic 108)
                        if ($targetKib !== 'KEMITRAAN') {
                            $astap->nama_barang = $targetNama;
                        }
                    }
                } elseif ($targetKib) {
                    $kibPrefixMap = [
                        'KIB A'          => '1.3.1',
                        'KIB B'          => '1.3.2',
                        'KIB C'          => '1.3.3',
                        'KIB D'          => '1.3.4',
                        'KIB E'          => '1.3.5',
                        'ATB'            => '1.5.3',
                        'KEMITRAAN'      => '1.5.2',
                        'ASET LAIN'      => '1.5.4',
                        'ASET LAIN-LAIN' => '1.5.4',
                    ];
                    $prefix = $kibPrefixMap[$targetKib] ?? null;
                    if ($prefix) {
                        $matchingJenis = JenisAstap::where('jenis', 'like', $prefix . '%')->first();
                        if ($matchingJenis) {
                            if (!$isPartial) {
                                $astap->jenis_astap_id = $matchingJenis->id;
                            }
                            $tujuanKodeAset = $matchingJenis->sub_sub_rincian_objek ?: ($matchingJenis->sub_rincian_objek ?: $matchingJenis->jenis);
                            $tujuanNamaAset = $targetNama ?: ($matchingJenis->uraian_sub_sub_rincian ?: ($matchingJenis->uraian_sub_rincian ?: $matchingJenis->nama_jenis));
                        }
                    }
                }
            }

            // Defensif pastikan targetKib terdefinisi
            $targetKib = $validated['tujuan_kib'] ?? ($targetKib ?? null);
            if (!$targetKib && !empty($validated['jenis_reklasifikasi_tujuan_id'])) {
                $tujuanRow = JenisReklasifikasi::find($validated['jenis_reklasifikasi_tujuan_id']);
                if ($tujuanRow) {
                    $targetKib = $tujuanRow->kelompok_kib;
                }
            }

            $allTemplateRows = JenisReklasifikasi::active()->get();

            // Auto-isi baris matriks tujuan jika kosong tapi targetKib / jenis_reklas tersedia
            if (empty($validated['jenis_reklasifikasi_tujuan_id'])) {
                $matchingTujuanRow = null;
                if ($validated['jenis_reklas'] === 'EKSTRAKOMPTABEL' || $targetKib === 'EKSTRAKOMPTABEL') {
                    $matchingTujuanRow = $allTemplateRows->firstWhere('kode_prefix', 'KOR_EXTRACOM');
                } elseif ($validated['jenis_reklas'] === 'HIBAH_KELUAR' || $targetKib === 'HIBAH') {
                    $matchingTujuanRow = $allTemplateRows->firstWhere('kode_prefix', 'KOR_HIBAH');
                } elseif ($validated['jenis_reklas'] === 'MUTASI_EKSTERNAL') {
                    $matchingTujuanRow = $allTemplateRows->firstWhere('kode_prefix', 'KOR_LAIN');
                } elseif ($validated['jenis_reklas'] === 'DEFINITIF_TO_KDP') {
                    $matchingTujuanRow = $allTemplateRows->firstWhere('kode_prefix', '1.3.6.01');
                } elseif ($validated['jenis_reklas'] === 'KDP_TO_DEFINITIF') {
                    $matchingTujuanRow = $allTemplateRows->firstWhere('kode_prefix', '1.3.3.01');
                } elseif ($targetKib) {
                    if ($targetKib === 'ATB') {
                        $matchingTujuanRow = $allTemplateRows->firstWhere('kode_prefix', '1.5.3');
                    } elseif ($targetKib === 'ASET LAIN' || $targetKib === 'ASET LAIN-LAIN' || $targetKib === 'ASET LAINNYA') {
                        $matchingTujuanRow = $allTemplateRows->firstWhere('kode_prefix', '1.5.4');
                    } elseif ($targetKib === 'KEMITRAAN') {
                        $matchingTujuanRow = $allTemplateRows->firstWhere('kode_prefix', '1.5.2');
                    } else {
                        $matchingTujuanRow = $allTemplateRows->where('kelompok_kib', $targetKib)->sortBy('urutan')->first();
                    }
                }
                if ($matchingTujuanRow) {
                    $validated['jenis_reklasifikasi_tujuan_id'] = $matchingTujuanRow->id;
                }
            }

            // Auto-isi baris matriks asal jika kosong
            if (empty($validated['jenis_reklasifikasi_asal_id'])) {
                $matchingAsalRow = null;
                if ($validated['jenis_reklas'] === 'KAPITALISASI_INTRAKOM') {
                    $matchingAsalRow = $allTemplateRows->firstWhere('kode_prefix', 'KOR_EXTRACOM');
                } elseif ($validated['jenis_reklas'] === 'HIBAH_MASUK') {
                    $matchingAsalRow = $allTemplateRows->firstWhere('kode_prefix', 'KOR_HIBAH');
                } elseif ($validated['jenis_reklas'] === 'KDP_TO_DEFINITIF') {
                    $matchingAsalRow = $allTemplateRows->firstWhere('kode_prefix', '1.3.6.01');
                } else {
                    $matchingAsalRow = $this->matchAstapToRow($astap, $allTemplateRows);
                    if (!$matchingAsalRow && !empty($validated['asal_kib'])) {
                        $matchingAsalRow = $allTemplateRows->where('kelompok_kib', $validated['asal_kib'])->sortBy('urutan')->first();
                    }
                }
                if ($matchingAsalRow) {
                    $validated['jenis_reklasifikasi_asal_id'] = $matchingAsalRow->id;
                }
            }

            // Penyesuaian spesifikasi fisik sesuai KIB Tujuan (misal KIB B Mesin -> KIB A Tanah, atau KDP -> Gedung)
            if (!isset($specBaru)) {
                $specBaru = null;
            }

            if ($request->has('spesifikasi_baru') && is_array($request->input('spesifikasi_baru')) && !empty($targetKib)) {
                $rawNew = $request->input('spesifikasi_baru');
                
                // Pertahankan metadata administrasi pengadaan (SPK, PPK, BAST, SP2D)
                $newSpec = [];
                $preserveKeys = [
                    'spk_nomor', 'spk_tanggal', 'surat_pesanan_nomor', 'surat_pesanan_tanggal',
                    'faktur_nomor', 'faktur_tanggal', 'kwitansi_nomor', 'kwitansi_tanggal',
                    'sp2d_nomor', 'sp2d_tanggal', 'bast_dokumen_nomor', 'bast_dokumen_tanggal',
                    'ppk_nama', 'ppk_nip', 'penyedia_nama', 'rekening_belanja', 'tahun_anggaran', 'triwulan'
                ];
                foreach ($preserveKeys as $pk) {
                    if (isset($specLama[$pk])) {
                        $newSpec[$pk] = $specLama[$pk];
                    }
                }

                // Deteksi jika spesifikasi baru dikirim dalam format multi-unit/multi-bidang
                $unitItems = (!empty($rawNew['items']) && is_array($rawNew['items'])) ? $rawNew['items'] : [$rawNew];
                $unitCount = max(1, count($unitItems));
                $isPartialMode = $request->boolean('is_partial_reklas') && is_array($request->input('selected_indices')) && count($request->input('selected_indices')) > 0;
                $unitPrice = $isPartialMode
                    ? (float) ((float) $validated['nilai_reklas'] / $unitCount)
                    : (float) ($astap->total_realisasi / $unitCount);

                if ($targetKib === 'KIB A') {
                    $tanahItems = [];
                    $totalLuas = 0;
                    $allSertifikat = [];
                    foreach ($unitItems as $uIdx => $uItem) {
                        $t = [
                            'tanah_hak'            => $uItem['tanah_hak'] ?? 'Hak Pakai',
                            'tanah_sertifikat_tgl' => $uItem['tanah_sertifikat_tgl'] ?? null,
                            'tanah_sertifikat_no'  => $uItem['tanah_sertifikat_no'] ?? null,
                            'tanah_penggunaan'     => $uItem['tanah_penggunaan'] ?? ($astap->nama_barang ?: 'Gedung RSUD'),
                            'tanah_asal_usul'      => $uItem['tanah_asal_usul'] ?? 'Pengadaan APBD / BLUD',
                            'tanah_luas_m2'        => (float) ($uItem['tanah_luas_m2'] ?? 0),
                            'tanah_nilai_fisik'    => $unitPrice,
                            'tanah_kondisi'        => 'Baik',
                            'tanah_alamat'         => $uItem['tanah_alamat'] ?? ($astap->alamat_barang ?: 'RSUD Dr. H. Koesnandi'),
                        ];
                        $tanahItems[] = $t;
                        $totalLuas += $t['tanah_luas_m2'];
                        if (!empty($t['tanah_sertifikat_no']) && $t['tanah_sertifikat_no'] !== '-') {
                            $allSertifikat[] = $t['tanah_sertifikat_no'];
                        }
                    }

                    $newSpec['tanah_items']     = $tanahItems;
                    $newSpec['luas_m2']         = $totalLuas > 0 ? $totalLuas : (float)($rawNew['tanah_luas_m2'] ?? 0);
                    $newSpec['hak_tanah']       = $tanahItems[0]['tanah_hak'] ?? 'Hak Pakai';
                    $newSpec['sertifikat_no']   = count($allSertifikat) > 0 ? implode(', ', $allSertifikat) : ($tanahItems[0]['tanah_sertifikat_no'] ?? null);
                    $newSpec['sertifikat_tgl']  = $tanahItems[0]['tanah_sertifikat_tgl'] ?? null;
                    $newSpec['penggunaan']      = $tanahItems[0]['tanah_penggunaan'] ?? null;
                    $astap->satuan              = 'M2';
                    $astap->alamat_barang       = $tanahItems[0]['tanah_alamat'] ?? ($astap->alamat_barang ?: 'RSUD Dr. H. Koesnandi');

                } elseif ($targetKib === 'KIB B') {
                    $mesinItems = [];
                    $allNoPabrik = [];
                    $regCursor = 0;

                    foreach ($unitItems as $uIdx => $uItem) {
                        $vol = max(1, (int) ($uItem['item_volume'] ?? ($uItem['mesin_jumlah_barang'] ?? 1)));
                        $unitItemPrice = isset($uItem['harga_satuan']) && (float)$uItem['harga_satuan'] > 0 
                            ? (float)$uItem['harga_satuan'] 
                            : ($astap->harga_satuan ?: ($astap->total_realisasi / max(1, $astap->jumlah_volume)));

                        $ukuranVal = $uItem['mesin_ukuran'] ?? ($uItem['mesin_ukuran_cc'] ?? ($uItem['ukuran'] ?? ($uItem['ukuran_cc'] ?? ($rawNew['mesin_ukuran'] ?? ($rawNew['mesin_ukuran_cc'] ?? ($rawNew['ukuran'] ?? '-'))))));
                        $m = [
                            'mesin_nama_barang'   => $uItem['item_nama'] ?? ($uItem['mesin_nama_barang'] ?? $astap->nama_barang),
                            'mesin_merk'          => $uItem['mesin_merk'] ?? ($rawNew['mesin_merk'] ?? '-'),
                            'mesin_type'          => $uItem['mesin_type'] ?? ($rawNew['mesin_type'] ?? '-'),
                            'mesin_ukuran'        => $ukuranVal,
                            'mesin_ukuran_cc'     => $ukuranVal,
                            'mesin_bahan'         => $uItem['mesin_bahan'] ?? ($rawNew['mesin_bahan'] ?? 'Logam / Komponen Elektronik'),
                            'mesin_no_pabrik'     => $uItem['mesin_no_pabrik'] ?? ($rawNew['mesin_no_pabrik'] ?? '-'),
                            'mesin_no_rangka'     => $uItem['mesin_no_rangka'] ?? ($rawNew['mesin_no_rangka'] ?? '-'),
                            'mesin_no_mesin'      => $uItem['mesin_no_mesin'] ?? ($rawNew['mesin_no_mesin'] ?? '-'),
                            'mesin_no_polisi'     => $uItem['mesin_no_polisi'] ?? ($rawNew['mesin_no_polisi'] ?? '-'),
                            'mesin_no_bpkb'       => $uItem['mesin_no_bpkb'] ?? ($rawNew['mesin_no_bpkb'] ?? '-'),
                            'mesin_kondisi'       => $uItem['mesin_kondisi'] ?? 'Baik',
                            'mesin_ruang_pemegang'=> $uItem['mesin_ruang_pemegang'] ?? ($astap->registers->skip($regCursor)->first()?->ruang_pemegang ?? 'Instalasi Perbekalan'),
                            'mesin_jumlah_barang' => $vol,
                            'mesin_satuan'        => $uItem['item_satuan'] ?? ($astap->satuan ?: 'Unit'),
                            'mesin_nilai_satuan'  => $unitItemPrice,
                            'mesin_total_nilai'   => $vol * $unitItemPrice,
                        ];
                        $mesinItems[] = $m;
                        if (!empty($m['mesin_no_pabrik']) && $m['mesin_no_pabrik'] !== '-') {
                            $allNoPabrik[] = $m['mesin_no_pabrik'];
                        }

                        // Sinkronkan penempatan ruangan & kondisi ke seluruh register yang dicakup barang ini
                        for ($r = 0; $r < $vol; $r++) {
                            $targetRegister = $astap->registers->skip($regCursor)->first();
                            if ($targetRegister) {
                                $targetRegister->update([
                                    'ruang_pemegang' => $m['mesin_ruang_pemegang'],
                                    'kondisi'        => $m['mesin_kondisi'],
                                ]);
                            }
                            $regCursor++;
                        }
                    }

                    $newSpec['mesin_items']     = $mesinItems;
                    $newSpec['merk']            = $mesinItems[0]['mesin_merk'] ?? '-';
                    $newSpec['type']            = $mesinItems[0]['mesin_type'] ?? '-';
                    $newSpec['ukuran']          = $mesinItems[0]['mesin_ukuran'] ?? '-';
                    $newSpec['mesin_ukuran']    = $newSpec['ukuran'];
                    $newSpec['mesin_ukuran_cc'] = $newSpec['ukuran'];
                    $newSpec['bahan']           = $mesinItems[0]['mesin_bahan'] ?? 'Logam / Komponen Elektronik';
                    $newSpec['no_pabrik']       = count($allNoPabrik) > 0 ? implode(', ', $allNoPabrik) : ($mesinItems[0]['mesin_no_pabrik'] ?? '-');
                    if (\Illuminate\Support\Facades\Schema::hasColumn('astaps', 'merk_type')) {
                        $astap->merk_type = trim(($mesinItems[0]['mesin_merk'] ?? '') . ' ' . ($mesinItems[0]['mesin_type'] ?? ''));
                    }

                } elseif ($targetKib === 'KIB C') {
                    $gedungItems = [];
                    $totalLuas = 0;
                    $isFromKemitraan = in_array($asalKibAset, ['KEMITRAAN', '1.5.2'], true)
                        || ($astap->category === 'KEMITRAAN')
                        || ($astap->sumber_dana === 'kemitraan')
                        || str_starts_with((string) $astap->kode_108, '1.5.2')
                        || ($astap->kemitraan !== null);
                    $defaultIsBaru = $isFromKemitraan ? 'Lama' : 'Baru';

                    $oldGedungList = $specLama['gedung_items'] ?? [];
                    foreach ($unitItems as $uIdx => $uItem) {
                        $oldG = $oldGedungList[$uIdx] ?? ($oldGedungList[0] ?? []);
                        $isBaruVal = $uItem['gedung_is_baru'] ?? ($rawNew['gedung_is_baru'] ?? ($oldG['gedung_is_baru'] ?? $defaultIsBaru));
                        $tahunInduk = ($isBaruVal === 'Baru') ? null : ($uItem['gedung_kapitalisasi_tahun_induk'] ?? ($rawNew['gedung_kapitalisasi_tahun_induk'] ?? ($oldG['gedung_kapitalisasi_tahun_induk'] ?? ($specLama['tahun_induk'] ?? null))));
                        $nilaiInduk = ($isBaruVal === 'Baru') ? 0 : (float) ($uItem['gedung_kapitalisasi_nilai_induk'] ?? ($rawNew['gedung_kapitalisasi_nilai_induk'] ?? ($oldG['gedung_kapitalisasi_nilai_induk'] ?? ($specLama['nilai_induk'] ?? 0))));

                        $luasGedung = (float) ($uItem['gedung_luas_lantai_m2'] ?? ($uItem['gedung_luas_lantai'] ?? ($rawNew['gedung_luas_lantai_m2'] ?? ($rawNew['gedung_luas_lantai'] ?? ($oldG['gedung_luas_lantai_m2'] ?? ($oldG['gedung_luas_lantai'] ?? ($oldG['luas_lantai'] ?? ($oldG['gedung_luas_m2'] ?? ($specLama['luas_lantai_m2'] ?? ($specLama['gedung_luas_lantai'] ?? 0))))))))));
                        $dokNomor = $uItem['gedung_dokumen_nomor'] ?? ($uItem['gedung_dokumen_no'] ?? ($rawNew['gedung_dokumen_nomor'] ?? ($rawNew['gedung_dokumen_no'] ?? ($oldG['gedung_dokumen_nomor'] ?? ($oldG['gedung_dokumen_no'] ?? ($oldG['dokumen_nomor'] ?? ($specLama['dokumen_nomor'] ?? null)))))));
                        $bertingkat = $uItem['gedung_konstruksi_bertingkat'] ?? ($rawNew['gedung_konstruksi_bertingkat'] ?? ($oldG['gedung_konstruksi_bertingkat'] ?? ($oldG['gedung_bertingkat'] ?? ($oldG['bertingkat'] ?? ($specLama['konstruksi_bertingkat'] ?? 'Bertingkat')))));
                        $beton = $uItem['gedung_konstruksi_beton'] ?? ($rawNew['gedung_konstruksi_beton'] ?? ($oldG['gedung_konstruksi_beton'] ?? ($oldG['gedung_beton'] ?? ($oldG['beton'] ?? ($specLama['konstruksi_beton'] ?? 'Beton')))));

                        $g = [
                            'gedung_nama_bangunan'        => $astap->nama_barang,
                            'gedung_konstruksi_bertingkat'=> $bertingkat,
                            'gedung_bertingkat'           => $bertingkat,
                            'gedung_konstruksi_beton'     => $beton,
                            'gedung_beton'                => $beton,
                            'gedung_luas_lantai'          => $luasGedung,
                            'gedung_luas_lantai_m2'       => $luasGedung,
                            'gedung_alamat'               => $uItem['gedung_alamat'] ?? ($rawNew['gedung_alamat'] ?? ($oldG['gedung_alamat'] ?? ($oldG['alamat'] ?? ($astap->alamat_barang ?: 'Kompleks RSUD Dr. H. Koesnandi')))),
                            'gedung_dokumen_tgl'          => $uItem['gedung_dokumen_tgl'] ?? ($rawNew['gedung_dokumen_tgl'] ?? ($oldG['gedung_dokumen_tgl'] ?? ($oldG['gedung_dokumen_tanggal'] ?? ($oldG['dokumen_tgl'] ?? ($specLama['dokumen_tgl'] ?? null))))),
                            'gedung_dokumen_no'           => $dokNomor,
                            'gedung_dokumen_nomor'        => $dokNomor,
                            'gedung_status_tanah'         => $uItem['gedung_status_tanah'] ?? ($rawNew['gedung_status_tanah'] ?? ($oldG['gedung_status_tanah'] ?? ($oldG['status_tanah'] ?? ($specLama['status_tanah'] ?? 'Tanah Pemda')))),
                            'gedung_kondisi'              => $uItem['gedung_kondisi'] ?? ($rawNew['gedung_kondisi'] ?? ($oldG['gedung_kondisi'] ?? ($oldG['kondisi'] ?? 'Baik'))),
                            'gedung_is_baru'              => $isBaruVal,
                            'gedung_kapitalisasi_tahun_induk' => $tahunInduk,
                            'gedung_kapitalisasi_nilai_induk' => $nilaiInduk,
                            'gedung_nilai_fisik'          => $unitPrice,
                        ];
                        $gedungItems[] = $g;
                        $totalLuas += $g['gedung_luas_lantai_m2'];
                    }

                    $newSpec['gedung_items']         = $gedungItems;
                    $newSpec['luas_lantai_m2']        = $totalLuas > 0 ? $totalLuas : (float)($rawNew['gedung_luas_lantai_m2'] ?? 0);
                    $newSpec['gedung_luas_lantai']    = $newSpec['luas_lantai_m2'];
                    $newSpec['konstruksi_bertingkat'] = $gedungItems[0]['gedung_konstruksi_bertingkat'] ?? 'Bertingkat';
                    $newSpec['bertingkat']            = $newSpec['konstruksi_bertingkat'];
                    $newSpec['konstruksi_beton']      = $gedungItems[0]['gedung_konstruksi_beton'] ?? 'Beton';
                    $newSpec['beton']                 = $newSpec['konstruksi_beton'];
                    $newSpec['dokumen_nomor']         = $gedungItems[0]['gedung_dokumen_nomor'] ?? null;
                    $newSpec['dokumen_no']            = $newSpec['dokumen_nomor'];
                    $newSpec['dokumen_tgl']           = $gedungItems[0]['gedung_dokumen_tgl'] ?? null;
                    $newSpec['gedung_is_baru']        = $gedungItems[0]['gedung_is_baru'] ?? $defaultIsBaru;
                    $newSpec['is_baru']               = $gedungItems[0]['gedung_is_baru'] ?? $defaultIsBaru;
                    $newSpec['gedung_kapitalisasi_tahun_induk'] = $gedungItems[0]['gedung_kapitalisasi_tahun_induk'] ?? null;
                    $newSpec['gedung_kapitalisasi_nilai_induk'] = $gedungItems[0]['gedung_kapitalisasi_nilai_induk'] ?? 0;
                    $astap->satuan                    = 'Gedung / Unit';
                    $astap->alamat_barang             = $gedungItems[0]['gedung_alamat'] ?? ($astap->alamat_barang ?: 'Kompleks RSUD Dr. H. Koesnandi');

                } elseif ($targetKib === 'KIB D') {
                    $jaringanItems = [];
                    $totalLuas = 0;
                    $isFromKemitraan = in_array($asalKibAset, ['KEMITRAAN', '1.5.2'], true)
                        || ($astap->category === 'KEMITRAAN')
                        || ($astap->sumber_dana === 'kemitraan')
                        || str_starts_with((string) $astap->kode_108, '1.5.2')
                        || ($astap->kemitraan !== null);
                    $defaultIsBaru = $isFromKemitraan ? 'Lama' : 'Baru';

                    $oldJaringanList = $specLama['jaringan_items'] ?? [];
                    foreach ($unitItems as $uIdx => $uItem) {
                        $oldJ = $oldJaringanList[$uIdx] ?? ($oldJaringanList[0] ?? []);
                        $isBaruVal = $uItem['jaringan_is_baru'] ?? ($rawNew['jaringan_is_baru'] ?? ($oldJ['jaringan_is_baru'] ?? $defaultIsBaru));
                        $tahunInduk = ($isBaruVal === 'Baru') ? null : ($uItem['jaringan_kapitalisasi_tahun_induk'] ?? ($rawNew['jaringan_kapitalisasi_tahun_induk'] ?? ($oldJ['jaringan_kapitalisasi_tahun_induk'] ?? ($specLama['tahun_induk'] ?? null))));
                        $nilaiInduk = ($isBaruVal === 'Baru') ? 0 : (float) ($uItem['jaringan_kapitalisasi_nilai_induk'] ?? ($rawNew['jaringan_kapitalisasi_nilai_induk'] ?? ($oldJ['jaringan_kapitalisasi_nilai_induk'] ?? ($specLama['nilai_induk'] ?? 0))));

                        $panjangVal = $uItem['jaringan_panjang'] ?? ($uItem['jaringan_panjang_km'] ?? ($rawNew['jaringan_panjang'] ?? ($rawNew['jaringan_panjang_km'] ?? ($oldJ['jaringan_panjang'] ?? ($oldJ['jaringan_panjang_km'] ?? null)))));
                        $lebarVal = $uItem['jaringan_lebar'] ?? ($uItem['jaringan_lebar_m'] ?? ($rawNew['jaringan_lebar'] ?? ($rawNew['jaringan_lebar_m'] ?? ($oldJ['jaringan_lebar'] ?? ($oldJ['jaringan_lebar_m'] ?? null)))));
                        $luasVal = (float) ($uItem['jaringan_luas'] ?? ($uItem['jaringan_luas_m2'] ?? ($rawNew['jaringan_luas'] ?? ($rawNew['jaringan_luas_m2'] ?? ($oldJ['jaringan_luas'] ?? ($oldJ['jaringan_luas_m2'] ?? ($specLama['luas_m2'] ?? 0)))))));
                        $dokNo = $uItem['jaringan_dokumen_no'] ?? ($uItem['jaringan_dokumen_nomor'] ?? ($rawNew['jaringan_dokumen_no'] ?? ($rawNew['jaringan_dokumen_nomor'] ?? ($oldJ['jaringan_dokumen_no'] ?? ($oldJ['jaringan_dokumen_nomor'] ?? ($specLama['dokumen_nomor'] ?? null))))));
                        $dokTgl = $uItem['jaringan_dokumen_tgl'] ?? ($rawNew['jaringan_dokumen_tgl'] ?? ($oldJ['jaringan_dokumen_tgl'] ?? ($specLama['dokumen_tgl'] ?? null)));

                        $j = [
                            'jaringan_nama'          => $astap->nama_barang,
                            'jaringan_nama_barang'   => $astap->nama_barang,
                            'jaringan_konstruksi'    => $uItem['jaringan_konstruksi'] ?? ($rawNew['jaringan_konstruksi'] ?? ($oldJ['jaringan_konstruksi'] ?? ($specLama['konstruksi'] ?? 'Aspal / Beton'))),
                            'jaringan_panjang'       => $panjangVal,
                            'jaringan_panjang_km'    => $panjangVal,
                            'jaringan_lebar'         => $lebarVal,
                            'jaringan_lebar_m'       => $lebarVal,
                            'jaringan_luas'          => $luasVal,
                            'jaringan_luas_m2'       => $luasVal,
                            'jaringan_alamat'        => $uItem['jaringan_alamat'] ?? ($rawNew['jaringan_alamat'] ?? ($oldJ['jaringan_alamat'] ?? ($astap->alamat_barang ?: 'Kompleks RSUD'))),
                            'jaringan_dokumen_no'    => $dokNo,
                            'jaringan_dokumen_nomor' => $dokNo,
                            'jaringan_dokumen_tgl'   => $dokTgl,
                            'jaringan_status_tanah'  => 'Tanah Pemda',
                            'jaringan_kondisi'       => $uItem['jaringan_kondisi'] ?? ($rawNew['jaringan_kondisi'] ?? ($oldJ['jaringan_kondisi'] ?? 'Baik')),
                            'jaringan_is_baru'       => $isBaruVal,
                            'jaringan_kapitalisasi_tahun_induk' => $tahunInduk,
                            'jaringan_kapitalisasi_nilai_induk' => $nilaiInduk,
                            'jaringan_nilai_fisik'   => $unitPrice,
                        ];
                        $jaringanItems[] = $j;
                        $totalLuas += $j['jaringan_luas_m2'];
                    }

                    $newSpec['jaringan_items']       = $jaringanItems;
                    $newSpec['konstruksi']           = $jaringanItems[0]['jaringan_konstruksi'] ?? 'Aspal / Beton';
                    $newSpec['panjang_km']           = $jaringanItems[0]['jaringan_panjang'] ?? null;
                    $newSpec['jaringan_panjang']     = $newSpec['panjang_km'];
                    $newSpec['lebar_m']              = $jaringanItems[0]['jaringan_lebar'] ?? null;
                    $newSpec['jaringan_lebar']       = $newSpec['lebar_m'];
                    $newSpec['luas_m2']              = $totalLuas > 0 ? $totalLuas : ($jaringanItems[0]['jaringan_luas'] ?? 0);
                    $newSpec['jaringan_luas']        = $newSpec['luas_m2'];
                    $newSpec['dokumen_nomor']         = $jaringanItems[0]['jaringan_dokumen_nomor'] ?? null;
                    $newSpec['dokumen_no']            = $newSpec['dokumen_nomor'];
                    $newSpec['dokumen_tgl']           = $jaringanItems[0]['jaringan_dokumen_tgl'] ?? null;
                    $newSpec['jaringan_is_baru']     = $jaringanItems[0]['jaringan_is_baru'] ?? $defaultIsBaru;
                    $newSpec['is_baru']              = $jaringanItems[0]['jaringan_is_baru'] ?? $defaultIsBaru;
                    $newSpec['jaringan_kapitalisasi_tahun_induk'] = $jaringanItems[0]['jaringan_kapitalisasi_tahun_induk'] ?? null;
                    $newSpec['jaringan_kapitalisasi_nilai_induk'] = $jaringanItems[0]['jaringan_kapitalisasi_nilai_induk'] ?? 0;
                    $astap->satuan                   = 'Ruas / Titik';
                    $astap->alamat_barang            = $jaringanItems[0]['jaringan_alamat'] ?? ($astap->alamat_barang ?: 'Kompleks RSUD');

                } elseif ($targetKib === 'KIB E') {
                    $lainnyaItems = [];
                    $regCursor = 0;
                    foreach ($unitItems as $uIdx => $uItem) {
                        $vol = max(1, (int) ($uItem['item_volume'] ?? ($uItem['lainnya_jumlah_barang'] ?? 1)));
                        $unitItemPrice = isset($uItem['harga_satuan']) && (float)$uItem['harga_satuan'] > 0 
                            ? (float)$uItem['harga_satuan'] 
                            : ($astap->harga_satuan ?: ($astap->total_realisasi / max(1, $astap->jumlah_volume)));

                        $kibEType = $uItem['kib_e_type'] ?? ($uItem['kib_e_sub_type'] ?? ($rawNew['kib_e_type'] ?? ($rawNew['kib_e_sub_type'] ?? 'buku')));
                        $kondisiVal = $uItem['kondisi'] ?? ($uItem['lainnya_kondisi'] ?? ($rawNew['kondisi'] ?? ($rawNew['lainnya_kondisi'] ?? 'Baik')));

                        $isTargetExtracom = !empty($rawNew['is_extracom']) || !empty($uItem['is_extracom']) || ($astap->category === 'EXTRACOM') || ($unitItemPrice <= 300000);

                        $itemJudul = $uItem['lainnya_buku_judul'] ?? ($uItem['lainnya_kesenian_judul'] ?? ($uItem['lainnya_hewan_judul'] ?? ($uItem['lainnya_hewan_jenis'] ?? ($uItem['lainnya_judul'] ?? ($rawNew['lainnya_buku_judul'] ?? ($rawNew['lainnya_kesenian_judul'] ?? ($rawNew['lainnya_hewan_judul'] ?? ($rawNew['lainnya_judul'] ?? ($uItem['item_nama'] ?? $astap->nama_barang)))))))));
                        $itemPencipta = $uItem['lainnya_buku_pencipta'] ?? ($uItem['lainnya_kesenian_pencipta'] ?? ($uItem['lainnya_pencipta'] ?? ($rawNew['lainnya_buku_pencipta'] ?? ($rawNew['lainnya_kesenian_pencipta'] ?? ($rawNew['lainnya_pencipta'] ?? '-')))));
                        $itemSpesifikasi = $uItem['lainnya_buku_spesifikasi'] ?? ($uItem['lainnya_kesenian_spesifikasi'] ?? ($uItem['lainnya_hewan_spesifikasi'] ?? ($uItem['lainnya_spesifikasi'] ?? ($rawNew['lainnya_buku_spesifikasi'] ?? ($rawNew['lainnya_kesenian_spesifikasi'] ?? ($rawNew['lainnya_hewan_spesifikasi'] ?? ($rawNew['lainnya_spesifikasi'] ?? '-')))))));
                        $itemAsalDaerah = $uItem['lainnya_kesenian_asal'] ?? ($uItem['lainnya_asal_daerah'] ?? ($rawNew['lainnya_kesenian_asal'] ?? ($rawNew['lainnya_asal_daerah'] ?? '-')));
                        $itemBahan = $uItem['lainnya_kesenian_bahan'] ?? ($uItem['lainnya_bahan'] ?? ($rawNew['lainnya_kesenian_bahan'] ?? ($rawNew['lainnya_bahan'] ?? 'Kertas / Kanvas / Lainnya')));
                        $itemUkuran = $uItem['lainnya_kesenian_ukuran'] ?? ($uItem['lainnya_ukuran'] ?? ($rawNew['lainnya_kesenian_ukuran'] ?? ($rawNew['lainnya_ukuran'] ?? '-')));

                        $l = [
                            'kib_e_type'             => $kibEType,
                            'kib_e_sub_type'         => $kibEType,
                            'lainnya_nama_barang'    => $uItem['item_nama'] ?? ($uItem['lainnya_nama_barang'] ?? ($uItem['lainnya_judul'] ?? $astap->nama_barang)),
                            'lainnya_judul'          => $itemJudul,
                            'lainnya_judul_pencipta' => $uItem['lainnya_judul_pencipta'] ?? (($itemJudul && $itemPencipta && $itemPencipta !== '-') ? "{$itemJudul} - {$itemPencipta}" : $itemJudul),
                            'lainnya_pencipta'       => $itemPencipta,
                            'lainnya_spesifikasi'    => $itemSpesifikasi,
                            'lainnya_asal_daerah'    => $itemAsalDaerah,
                            'lainnya_bahan'          => $itemBahan,
                            'lainnya_ukuran'         => $itemUkuran,
                            'lainnya_no_pabrik'      => $uItem['lainnya_no_pabrik'] ?? ($rawNew['lainnya_no_pabrik'] ?? null),
                            'lainnya_keterangan'     => $uItem['lainnya_keterangan'] ?? ($rawNew['lainnya_keterangan'] ?? null),
                            'is_extracom'            => $isTargetExtracom,
                            'lainnya_kondisi'        => $kondisiVal,
                            'lainnya_jumlah'         => $vol,
                            'lainnya_jumlah_barang'  => $vol,
                            'lainnya_satuan'         => $uItem['item_satuan'] ?? ($astap->satuan ?: 'Buah'),
                            'lainnya_nilai_satuan'   => $unitItemPrice,
                            'lainnya_total_nilai'    => $vol * $unitItemPrice,

                            // Sub-category alias keys for compatibility with ASTAP detail modal & input form:
                            'lainnya_buku_judul'           => $itemJudul,
                            'lainnya_buku_pencipta'        => $itemPencipta,
                            'lainnya_buku_spesifikasi'     => $itemSpesifikasi,
                            'lainnya_kesenian_judul'       => $itemJudul,
                            'lainnya_kesenian_asal'        => $itemAsalDaerah,
                            'lainnya_kesenian_pencipta'    => $itemPencipta,
                            'lainnya_kesenian_spesifikasi' => $itemSpesifikasi,
                            'lainnya_kesenian_bahan'       => $itemBahan,
                            'lainnya_kesenian_ukuran'      => $itemUkuran,
                            'lainnya_hewan_jenis'          => $itemJudul,
                            'lainnya_hewan_judul'          => $itemJudul,
                            'lainnya_hewan_spesifikasi'    => $itemSpesifikasi,
                        ];
                        $lainnyaItems[] = $l;

                        // Sinkronkan penempatan ruangan & kondisi ke register yang dicakup
                        for ($r = 0; $r < $vol; $r++) {
                            $targetRegister = $astap->registers->skip($regCursor)->first();
                            if ($targetRegister) {
                                $targetRegister->update([
                                    'kondisi' => $kondisiVal,
                                ]);
                            }
                            $regCursor++;
                        }
                    }
                    $newSpec['lainnya_items']        = $lainnyaItems;
                    $newSpec['kategori_kib']         = 'KIB E (Aset Tetap Lainnya)';
                    $newSpec['kib_e_type']           = $lainnyaItems[0]['kib_e_type'] ?? 'buku';
                    $newSpec['kib_e_sub_type']       = $lainnyaItems[0]['kib_e_type'] ?? 'buku';
                    $newSpec['judul']                = $lainnyaItems[0]['lainnya_judul'] ?? null;
                    $newSpec['pencipta']             = $lainnyaItems[0]['lainnya_pencipta'] ?? null;
                    $newSpec['spesifikasi']          = $lainnyaItems[0]['lainnya_spesifikasi'] ?? null;
                    $newSpec['bahan']                = $lainnyaItems[0]['lainnya_bahan'] ?? null;
                    $newSpec['ukuran']               = $lainnyaItems[0]['lainnya_ukuran'] ?? null;
                    $newSpec['asal_daerah']          = $lainnyaItems[0]['lainnya_asal_daerah'] ?? null;
                    $newSpec['kondisi']              = $lainnyaItems[0]['lainnya_kondisi'] ?? 'Baik';
                    $newSpec['is_extracom']          = $isTargetExtracom;
                    $newSpec['is_extracomtable']     = $isTargetExtracom;

                    // Sub-category alias keys on root spec:
                    $newSpec['buku_judul']           = $lainnyaItems[0]['lainnya_judul'] ?? null;
                    $newSpec['buku_pencipta']        = $lainnyaItems[0]['lainnya_pencipta'] ?? null;
                    $newSpec['buku_spesifikasi']     = $lainnyaItems[0]['lainnya_spesifikasi'] ?? null;
                    $newSpec['kesenian_judul']       = $lainnyaItems[0]['lainnya_judul'] ?? null;
                    $newSpec['kesenian_asal']        = $lainnyaItems[0]['lainnya_asal_daerah'] ?? null;
                    $newSpec['kesenian_pencipta']    = $lainnyaItems[0]['lainnya_pencipta'] ?? null;
                    $newSpec['kesenian_spesifikasi'] = $lainnyaItems[0]['lainnya_spesifikasi'] ?? null;
                    $newSpec['kesenian_bahan']       = $lainnyaItems[0]['lainnya_bahan'] ?? null;
                    $newSpec['kesenian_ukuran']      = $lainnyaItems[0]['lainnya_ukuran'] ?? null;
                    $newSpec['hewan_jenis']          = $lainnyaItems[0]['lainnya_judul'] ?? null;
                    $newSpec['hewan_judul']          = $lainnyaItems[0]['lainnya_judul'] ?? null;
                    $newSpec['hewan_spesifikasi']    = $lainnyaItems[0]['lainnya_spesifikasi'] ?? null;

                    $astap->satuan                   = $lainnyaItems[0]['lainnya_satuan'] ?? ($astap->satuan ?: 'Buah');
                    if (!empty($lainnyaItems[0]['lainnya_judul']) && $targetKib !== 'KEMITRAAN') {
                        $astap->nama_barang = $lainnyaItems[0]['lainnya_judul'];
                    }
                    unset($newSpec['mesin_items']);

                } elseif ($targetKib === 'ATB') {
                    $atbItems = [];
                    foreach ($unitItems as $uIdx => $uItem) {
                        $judulNama = $uItem['atb_judul_nama'] ?? ($uItem['atb_nama_software'] ?? ($rawNew['atb_judul_nama'] ?? ($rawNew['atb_nama_software'] ?? $astap->nama_barang)));
                        $pencipta = $uItem['atb_pencipta'] ?? ($uItem['atb_pengembang'] ?? ($rawNew['atb_pencipta'] ?? ($rawNew['atb_pengembang'] ?? '-')));
                        $spesifikasi = $uItem['atb_spesifikasi'] ?? ($rawNew['atb_spesifikasi'] ?? '-');

                        $atbItems[] = [
                            'atb_judul_nama'    => $judulNama,
                            'atb_nama_software' => $judulNama,
                            'atb_nama_barang'   => $astap->nama_barang,
                            'atb_pencipta'      => $pencipta,
                            'atb_pengembang'    => $pencipta,
                            'atb_spesifikasi'   => $spesifikasi,
                            'atb_jenis'         => $uItem['atb_jenis'] ?? ($rawNew['atb_jenis'] ?? 'Software Aplikasi SIMRS / Lisensi'),
                            'atb_masa_manfaat'  => (int) ($uItem['atb_masa_manfaat'] ?? ($rawNew['atb_masa_manfaat'] ?? 4)),
                            'atb_versi'         => $uItem['atb_versi'] ?? ($rawNew['atb_versi'] ?? 'v1.0'),
                            'atb_nomor_lisensi' => $uItem['atb_nomor_lisensi'] ?? ($rawNew['atb_nomor_lisensi'] ?? '-'),
                            'atb_nilai_satuan'  => $unitPrice,
                            'atb_jumlah'        => (int) ($uItem['item_volume'] ?? 1),
                            'atb_satuan'        => $uItem['item_satuan'] ?? ($astap->satuan ?: 'Lisensi'),
                            'ruang_pemegang'    => $uItem['ruang_pemegang'] ?? ($astap->ruang_unit ?? '-'),
                        ];
                    }
                    $newSpec['atb_items']         = $atbItems;
                    $newSpec['atb_judul_nama']    = $atbItems[0]['atb_judul_nama'];
                    $newSpec['atb_nama_software'] = $atbItems[0]['atb_nama_software'];
                    $newSpec['atb_pencipta']      = $atbItems[0]['atb_pencipta'];
                    $newSpec['atb_pengembang']    = $atbItems[0]['atb_pengembang'];
                    $newSpec['atb_spesifikasi']   = $atbItems[0]['atb_spesifikasi'];
                    $newSpec['kategori_kib']      = 'ATB';
                    $astap->satuan                = 'Paket / Lisensi';
                    if (!empty($atbItems[0]['atb_judul_nama']) && $targetKib !== 'KEMITRAAN') {
                        $astap->nama_barang = $atbItems[0]['atb_judul_nama'];
                    }
                    unset($newSpec['mesin_items']);
                } elseif ($targetKib === 'KEMITRAAN') {
                    $kemitraanItems = [];
                    $tujuanKodeForKemitraan = (string) ($validated['tujuan_kode'] ?? ($request->input('tujuan_kode') ?? ($astap->kode_barang ?? '')));
                    foreach ($unitItems as $uIdx => $uItem) {
                        $skema = $uItem['kemitraan_skema'] ?? ($rawNew['kemitraan_skema'] ?? null);
                        if (!$skema || $skema === 'Sewa') {
                            if (str_contains($tujuanKodeForKemitraan, '.01.01.02') || str_ends_with($tujuanKodeForKemitraan, '.02')) {
                                $skema = 'Kerjasama Pemanfaatan (KP)';
                            } elseif (str_contains($tujuanKodeForKemitraan, '.01.01.03') || str_ends_with($tujuanKodeForKemitraan, '.03')) {
                                $skema = 'BGS/BSG';
                            } elseif (str_contains($tujuanKodeForKemitraan, '.01.01.04') || str_ends_with($tujuanKodeForKemitraan, '.04')) {
                                $skema = 'KSPI';
                            }
                        }
                        $skema = $skema ?: 'Sewa';
                        $kemitraanItems[] = [
                            'skema_kemitraan'       => $skema,
                            'mitra_nama'            => $uItem['kemitraan_mitra'] ?? ($rawNew['kemitraan_mitra'] ?? 'Pihak Ketiga Mitra RSUD'),
                            'mitra_pimpinan'        => $uItem['kemitraan_pimpinan'] ?? ($rawNew['kemitraan_pimpinan'] ?? null),
                            'mitra_alamat'          => $uItem['kemitraan_alamat'] ?? ($rawNew['kemitraan_alamat'] ?? null),
                            'perjanjian_nomor'      => $uItem['kemitraan_perjanjian_no'] ?? ($rawNew['kemitraan_perjanjian_no'] ?? '-'),
                            'tanggal_pks'           => $uItem['kemitraan_tanggal_pks'] ?? ($rawNew['kemitraan_tanggal_pks'] ?? null),
                            'tanggal_mulai'         => $uItem['kemitraan_tanggal_mulai'] ?? ($rawNew['kemitraan_tanggal_mulai'] ?? null),
                            'tanggal_selesai'       => $uItem['kemitraan_tanggal_selesai'] ?? ($rawNew['kemitraan_tanggal_selesai'] ?? null),
                            'jangka_waktu'          => $uItem['kemitraan_jangka_waktu'] ?? ($rawNew['kemitraan_jangka_waktu'] ?? '5 Tahun'),
                            'harga_sewa'            => $uItem['kemitraan_harga_sewa'] ?? ($rawNew['kemitraan_harga_sewa'] ?? null),
                            'periode_sewa'          => $uItem['kemitraan_periode_sewa'] ?? ($rawNew['kemitraan_periode_sewa'] ?? 'Per Tahun'),
                            'nilai_kontribusi'      => $uItem['kemitraan_nilai_kontribusi'] ?? ($rawNew['kemitraan_nilai_kontribusi'] ?? null),
                            'keterangan_kontribusi' => $uItem['kemitraan_keterangan_kontribusi'] ?? ($rawNew['kemitraan_keterangan_kontribusi'] ?? null),
                            'kemitraan_nilai'       => $unitPrice,
                        ];
                    }
                    $newSpec['kemitraan_items']       = $kemitraanItems;
                    $newSpec['tipe_kemitraan']        = 'dimanfaatkan';
                    $newSpec['skema_kemitraan']       = $kemitraanItems[0]['skema_kemitraan'] ?? 'Sewa';
                    $newSpec['mitra_nama']            = $kemitraanItems[0]['mitra_nama'] ?? '-';
                    $newSpec['mitra_pimpinan']        = $kemitraanItems[0]['mitra_pimpinan'] ?? null;
                    $newSpec['mitra_alamat']          = $kemitraanItems[0]['mitra_alamat'] ?? null;
                    $newSpec['perjanjian_nomor']      = $kemitraanItems[0]['perjanjian_nomor'] ?? '-';
                    $newSpec['nomor_pks']             = $kemitraanItems[0]['perjanjian_nomor'] ?? '-';
                    $newSpec['tanggal_pks']           = $kemitraanItems[0]['tanggal_pks'] ?? null;
                    $newSpec['tanggal_mulai']         = $kemitraanItems[0]['tanggal_mulai'] ?? null;
                    $newSpec['tanggal_selesai']       = $kemitraanItems[0]['tanggal_selesai'] ?? null;
                    $newSpec['jangka_waktu']          = $kemitraanItems[0]['jangka_waktu'] ?? '5 Tahun';
                    $newSpec['harga_sewa']            = $kemitraanItems[0]['harga_sewa'] ?? null;
                    $newSpec['periode_sewa']          = $kemitraanItems[0]['periode_sewa'] ?? 'Per Tahun';
                    $newSpec['nilai_kontribusi']      = $kemitraanItems[0]['nilai_kontribusi'] ?? null;
                    $newSpec['keterangan_kontribusi'] = $kemitraanItems[0]['keterangan_kontribusi'] ?? null;

                    // Simpan juga spesifikasi fisik (Tanah / Gedung / Jaringan / Lainnya / Mesin) sesuai wujud asal aset & kode 108 tujuan
                    $isTanah = in_array($asalKibAset, ['KIB A', 'A', 'TANAH']) || str_ends_with($tujuanKodeForKemitraan, '.001') || (!empty($specLama['tanah_items']) && empty($specLama['mesin_items'])) || (!empty($rawNew['tanah_luas_m2']) && empty($rawNew['mesin_merk']));
                    $isGedung = in_array($asalKibAset, ['KIB C', 'C', 'GEDUNG', 'BANGUNAN']) || str_ends_with($tujuanKodeForKemitraan, '.003') || (!empty($specLama['gedung_items']) && empty($specLama['mesin_items'])) || (!empty($rawNew['gedung_luas_lantai_m2']) && empty($rawNew['mesin_merk']));
                    $isJaringan = in_array($asalKibAset, ['KIB D', 'D', 'JALAN', 'JARINGAN', 'IRIGASI']) || str_ends_with($tujuanKodeForKemitraan, '.004') || (!empty($specLama['jaringan_items']) && empty($specLama['mesin_items']));
                    $isLainnya = in_array($asalKibAset, ['KIB E', 'E', 'ASET TETAP LAINNYA', 'LAINNYA']) || str_ends_with($tujuanKodeForKemitraan, '.005') || (!empty($specLama['lainnya_items']) && (!isset($specLama['mesin_items']) || collect($specLama['mesin_items'])->every(fn($m) => empty($m['mesin_nama_barang']) || in_array(trim($m['mesin_nama_barang']), ['-', '--', '- -'])))) || !empty($rawNew['lainnya_judul']) || !empty($rawNew['lainnya_pencipta']);

                    $isTargetExtracom = !empty($rawNew['is_extracom']) || ($astap->category === 'EXTRACOM') || ($unitPrice <= 300000);
                    $newSpec['is_extracom'] = $isTargetExtracom;
                    $newSpec['is_extracomtable'] = $isTargetExtracom;

                    if ($isTanah) {
                        $tanahItems = [];
                        $totalLuas = 0;
                        $allSertifikat = [];
                        $oldTanahList = $specLama['tanah_items'] ?? [];
                        foreach ($unitItems as $uIdx => $uItem) {
                            $oldT = $oldTanahList[$uIdx] ?? ($oldTanahList[0] ?? []);
                            $t = [
                                'tanah_hak'            => $uItem['tanah_hak'] ?? ($rawNew['tanah_hak'] ?? ($oldT['tanah_hak'] ?? ($specLama['hak_tanah'] ?? 'Hak Pakai'))),
                                'tanah_sertifikat_tgl' => $uItem['tanah_sertifikat_tgl'] ?? ($rawNew['tanah_sertifikat_tgl'] ?? ($oldT['tanah_sertifikat_tgl'] ?? ($specLama['sertifikat_tgl'] ?? null))),
                                'tanah_sertifikat_no'  => $uItem['tanah_sertifikat_no'] ?? ($rawNew['tanah_sertifikat_no'] ?? ($oldT['tanah_sertifikat_no'] ?? ($specLama['sertifikat_no'] ?? null))),
                                'tanah_penggunaan'     => $uItem['tanah_penggunaan'] ?? ($rawNew['tanah_penggunaan'] ?? ($oldT['tanah_penggunaan'] ?? ($specLama['penggunaan'] ?? ($astap->nama_barang ?: 'Gedung RSUD')))),
                                'tanah_asal_usul'      => $uItem['tanah_asal_usul'] ?? ($oldT['tanah_asal_usul'] ?? 'Pengadaan APBD / BLUD'),
                                'tanah_luas_m2'        => (float) ($uItem['tanah_luas_m2'] ?? ($rawNew['tanah_luas_m2'] ?? ($oldT['tanah_luas_m2'] ?? ($specLama['luas_m2'] ?? 0)))),
                                'tanah_nilai_fisik'    => $unitPrice,
                                'tanah_kondisi'        => $oldT['tanah_kondisi'] ?? 'Baik',
                                'tanah_alamat'         => $uItem['tanah_alamat'] ?? ($rawNew['tanah_alamat'] ?? ($oldT['tanah_alamat'] ?? ($astap->alamat_barang ?: 'RSUD Dr. H. Koesnandi'))),
                            ];
                            $tanahItems[] = $t;
                            $totalLuas += $t['tanah_luas_m2'];
                            if (!empty($t['tanah_sertifikat_no']) && $t['tanah_sertifikat_no'] !== '-') {
                                $allSertifikat[] = $t['tanah_sertifikat_no'];
                            }
                        }
                        $newSpec['tanah_items']    = $tanahItems;
                        $newSpec['luas_m2']        = $totalLuas > 0 ? $totalLuas : (float)($rawNew['tanah_luas_m2'] ?? ($specLama['luas_m2'] ?? 0));
                        $newSpec['hak_tanah']      = $tanahItems[0]['tanah_hak'] ?? ($specLama['hak_tanah'] ?? 'Hak Pakai');
                        $newSpec['sertifikat_no']  = count($allSertifikat) > 0 ? implode(', ', $allSertifikat) : ($tanahItems[0]['tanah_sertifikat_no'] ?? ($specLama['sertifikat_no'] ?? null));
                        $newSpec['sertifikat_tgl'] = $tanahItems[0]['tanah_sertifikat_tgl'] ?? ($specLama['sertifikat_tgl'] ?? null);
                        $newSpec['penggunaan']     = $tanahItems[0]['tanah_penggunaan'] ?? ($specLama['penggunaan'] ?? null);
                    } elseif ($isGedung) {
                        $gedungItems = [];
                        $totalLuas = 0;
                        $oldGedungList = $specLama['gedung_items'] ?? [];
                        foreach ($unitItems as $uIdx => $uItem) {
                            $oldG = $oldGedungList[$uIdx] ?? ($oldGedungList[0] ?? []);
                            $luasGedung = (float) ($uItem['gedung_luas_lantai_m2'] ?? ($uItem['gedung_luas_lantai'] ?? ($rawNew['gedung_luas_lantai_m2'] ?? ($rawNew['gedung_luas_lantai'] ?? ($oldG['gedung_luas_lantai_m2'] ?? ($oldG['gedung_luas_lantai'] ?? ($oldG['luas_lantai'] ?? ($oldG['gedung_luas_m2'] ?? ($specLama['luas_lantai_m2'] ?? ($specLama['gedung_luas_lantai'] ?? 0))))))))));
                            $dokNomor = $uItem['gedung_dokumen_nomor'] ?? ($uItem['gedung_dokumen_no'] ?? ($rawNew['gedung_dokumen_nomor'] ?? ($rawNew['gedung_dokumen_no'] ?? ($oldG['gedung_dokumen_nomor'] ?? ($oldG['gedung_dokumen_no'] ?? ($oldG['dokumen_nomor'] ?? ($specLama['dokumen_nomor'] ?? null)))))));
                            $bertingkat = $uItem['gedung_konstruksi_bertingkat'] ?? ($rawNew['gedung_konstruksi_bertingkat'] ?? ($oldG['gedung_konstruksi_bertingkat'] ?? ($oldG['gedung_bertingkat'] ?? ($oldG['bertingkat'] ?? ($specLama['konstruksi_bertingkat'] ?? 'Bertingkat')))));
                            $beton = $uItem['gedung_konstruksi_beton'] ?? ($rawNew['gedung_konstruksi_beton'] ?? ($oldG['gedung_konstruksi_beton'] ?? ($oldG['gedung_beton'] ?? ($oldG['beton'] ?? ($specLama['konstruksi_beton'] ?? 'Beton')))));

                            $g = [
                                'gedung_nama_bangunan'        => $astap->nama_barang,
                                'gedung_konstruksi_bertingkat'=> $bertingkat,
                                'gedung_bertingkat'           => $bertingkat,
                                'gedung_konstruksi_beton'     => $beton,
                                'gedung_beton'                => $beton,
                                'gedung_luas_lantai'          => $luasGedung,
                                'gedung_luas_lantai_m2'       => $luasGedung,
                                'gedung_alamat'               => $uItem['gedung_alamat'] ?? ($rawNew['gedung_alamat'] ?? ($oldG['gedung_alamat'] ?? ($oldG['alamat'] ?? ($astap->alamat_barang ?: 'Kompleks RSUD Dr. H. Koesnandi')))),
                                'gedung_dokumen_tgl'          => $uItem['gedung_dokumen_tgl'] ?? ($rawNew['gedung_dokumen_tgl'] ?? ($oldG['gedung_dokumen_tgl'] ?? ($oldG['gedung_dokumen_tanggal'] ?? ($oldG['dokumen_tgl'] ?? ($specLama['dokumen_tgl'] ?? null))))),
                                'gedung_dokumen_no'           => $dokNomor,
                                'gedung_dokumen_nomor'        => $dokNomor,
                                'gedung_status_tanah'         => $uItem['gedung_status_tanah'] ?? ($rawNew['gedung_status_tanah'] ?? ($oldG['gedung_status_tanah'] ?? ($oldG['status_tanah'] ?? ($specLama['status_tanah'] ?? 'Tanah Pemda')))),
                                'gedung_kondisi'              => $uItem['gedung_kondisi'] ?? ($rawNew['gedung_kondisi'] ?? ($oldG['gedung_kondisi'] ?? ($oldG['kondisi'] ?? 'Baik'))),
                                'gedung_nilai_fisik'          => $unitPrice,
                            ];
                            $gedungItems[] = $g;
                            $totalLuas += $g['gedung_luas_lantai_m2'];
                        }
                        $newSpec['gedung_items']         = $gedungItems;
                        $newSpec['luas_lantai_m2']        = $totalLuas > 0 ? $totalLuas : (float)($rawNew['gedung_luas_lantai_m2'] ?? ($specLama['luas_lantai_m2'] ?? 0));
                        $newSpec['gedung_luas_lantai']    = $newSpec['luas_lantai_m2'];
                        $newSpec['konstruksi_bertingkat'] = $gedungItems[0]['gedung_konstruksi_bertingkat'] ?? 'Bertingkat';
                        $newSpec['bertingkat']            = $newSpec['konstruksi_bertingkat'];
                        $newSpec['konstruksi_beton']      = $gedungItems[0]['gedung_konstruksi_beton'] ?? 'Beton';
                        $newSpec['beton']                 = $newSpec['konstruksi_beton'];
                        $newSpec['dokumen_nomor']         = $gedungItems[0]['gedung_dokumen_nomor'] ?? null;
                        $newSpec['dokumen_no']            = $newSpec['dokumen_nomor'];
                    } elseif ($isJaringan) {
                        $jaringanItems = [];
                        $totalLuas = 0;
                        $oldJaringanList = $specLama['jaringan_items'] ?? [];
                        foreach ($unitItems as $uIdx => $uItem) {
                            $oldJ = $oldJaringanList[$uIdx] ?? ($oldJaringanList[0] ?? []);
                            $panjangVal = $uItem['jaringan_panjang'] ?? ($uItem['jaringan_panjang_km'] ?? ($rawNew['jaringan_panjang'] ?? ($rawNew['jaringan_panjang_km'] ?? ($oldJ['jaringan_panjang'] ?? ($oldJ['jaringan_panjang_km'] ?? null)))));
                            $lebarVal = $uItem['jaringan_lebar'] ?? ($uItem['jaringan_lebar_m'] ?? ($rawNew['jaringan_lebar'] ?? ($rawNew['jaringan_lebar_m'] ?? ($oldJ['jaringan_lebar'] ?? ($oldJ['jaringan_lebar_m'] ?? null)))));
                            $luasVal = (float) ($uItem['jaringan_luas'] ?? ($uItem['jaringan_luas_m2'] ?? ($rawNew['jaringan_luas'] ?? ($rawNew['jaringan_luas_m2'] ?? ($oldJ['jaringan_luas'] ?? ($oldJ['jaringan_luas_m2'] ?? 0))))));
                            $j = [
                                'jaringan_nama'          => $astap->nama_barang,
                                'jaringan_nama_barang'   => $astap->nama_barang,
                                'jaringan_konstruksi'    => $uItem['jaringan_konstruksi'] ?? ($rawNew['jaringan_konstruksi'] ?? ($oldJ['jaringan_konstruksi'] ?? 'Aspal / Beton')),
                                'jaringan_panjang'       => $panjangVal,
                                'jaringan_panjang_km'    => $panjangVal,
                                'jaringan_lebar'         => $lebarVal,
                                'jaringan_lebar_m'       => $lebarVal,
                                'jaringan_luas'          => $luasVal,
                                'jaringan_luas_m2'       => $luasVal,
                                'jaringan_alamat'        => $uItem['jaringan_alamat'] ?? ($rawNew['jaringan_alamat'] ?? ($oldJ['jaringan_alamat'] ?? ($astap->alamat_barang ?: 'Kompleks RSUD'))),
                                'jaringan_kondisi'       => $oldJ['jaringan_kondisi'] ?? 'Baik',
                                'jaringan_nilai_fisik'   => $unitPrice,
                            ];
                            $jaringanItems[] = $j;
                            $totalLuas += $j['jaringan_luas_m2'];
                        }
                        $newSpec['jaringan_items']   = $jaringanItems;
                        $newSpec['konstruksi']       = $jaringanItems[0]['jaringan_konstruksi'] ?? 'Aspal / Beton';
                        $newSpec['panjang_km']       = $jaringanItems[0]['jaringan_panjang'] ?? null;
                        $newSpec['jaringan_panjang'] = $newSpec['panjang_km'];
                        $newSpec['lebar_m']          = $jaringanItems[0]['jaringan_lebar'] ?? null;
                        $newSpec['jaringan_lebar']   = $newSpec['lebar_m'];
                        $newSpec['luas_m2']          = $totalLuas > 0 ? $totalLuas : ($jaringanItems[0]['jaringan_luas'] ?? 0);
                        $newSpec['jaringan_luas']    = $newSpec['luas_m2'];
                    } elseif ($isLainnya) {
                        $lainnyaItems = [];
                        $oldLainnyaList = $specLama['lainnya_items'] ?? [];
                        foreach ($unitItems as $uIdx => $uItem) {
                            $oldL = $oldLainnyaList[$uIdx] ?? ($oldLainnyaList[0] ?? []);
                            $vol = max(1, (int) ($uItem['item_volume'] ?? ($uItem['lainnya_jumlah_barang'] ?? 1)));
                            $l = [
                                'lainnya_nama_barang'    => $uItem['item_nama'] ?? ($uItem['lainnya_nama_barang'] ?? ($rawNew['lainnya_nama_barang'] ?? ($oldL['lainnya_nama_barang'] ?? $astap->nama_barang))),
                                'lainnya_judul'          => $uItem['lainnya_judul'] ?? ($rawNew['lainnya_judul'] ?? ($oldL['lainnya_judul'] ?? ($rawNew['lainnya_judul_pencipta'] ?? $astap->nama_barang))),
                                'lainnya_pencipta'       => $uItem['lainnya_pencipta'] ?? ($rawNew['lainnya_pencipta'] ?? ($oldL['lainnya_pencipta'] ?? null)),
                                'lainnya_judul_pencipta' => $uItem['lainnya_judul_pencipta'] ?? ($rawNew['lainnya_judul_pencipta'] ?? ($oldL['lainnya_judul_pencipta'] ?? $astap->nama_barang)),
                                'lainnya_spesifikasi'    => $uItem['lainnya_spesifikasi'] ?? ($rawNew['lainnya_spesifikasi'] ?? ($oldL['lainnya_spesifikasi'] ?? '-')),
                                'lainnya_asal_daerah'    => $uItem['lainnya_asal_daerah'] ?? ($rawNew['lainnya_asal_daerah'] ?? ($oldL['lainnya_asal_daerah'] ?? '-')),
                                'lainnya_bahan'          => $uItem['lainnya_bahan'] ?? ($rawNew['lainnya_bahan'] ?? ($oldL['lainnya_bahan'] ?? 'Kertas / Kanvas / Lainnya')),
                                'lainnya_ukuran'         => $uItem['lainnya_ukuran'] ?? ($rawNew['lainnya_ukuran'] ?? ($oldL['lainnya_ukuran'] ?? '-')),
                                'lainnya_no_pabrik'      => $uItem['lainnya_no_pabrik'] ?? ($rawNew['lainnya_no_pabrik'] ?? ($oldL['lainnya_no_pabrik'] ?? null)),
                                'lainnya_kondisi'        => $uItem['lainnya_kondisi'] ?? ($rawNew['lainnya_kondisi'] ?? ($oldL['lainnya_kondisi'] ?? 'Baik')),
                                'kib_e_type'             => $uItem['kib_e_type'] ?? ($rawNew['kib_e_type'] ?? ($oldL['kib_e_type'] ?? 'buku')),
                                'is_extracom'            => $isTargetExtracom,
                                'lainnya_jumlah'         => $vol,
                                'lainnya_jumlah_barang'  => $vol,
                                'lainnya_satuan'         => $uItem['item_satuan'] ?? ($astap->satuan ?: 'Buah'),
                                'lainnya_nilai_satuan'   => $unitPrice,
                                'lainnya_total_nilai'    => $vol * $unitPrice,
                            ];
                            $lainnyaItems[] = $l;
                        }
                        $newSpec['lainnya_items'] = $lainnyaItems;
                        $newSpec['kategori_kib']  = 'KIB E (Aset Tetap Lainnya)';
                        $newSpec['kib_e_type']    = $lainnyaItems[0]['kib_e_type'] ?? 'buku';
                        $newSpec['judul']         = $lainnyaItems[0]['lainnya_judul'] ?? null;
                        $newSpec['pencipta']      = $lainnyaItems[0]['lainnya_pencipta'] ?? null;
                        $newSpec['spesifikasi']   = $lainnyaItems[0]['lainnya_spesifikasi'] ?? null;
                        $newSpec['bahan']         = $lainnyaItems[0]['lainnya_bahan'] ?? 'Kertas / Kanvas / Lainnya';
                        $newSpec['ukuran']        = $lainnyaItems[0]['lainnya_ukuran'] ?? null;
                        unset($newSpec['mesin_items']);
                    } else {
                        // KIB B Mesin & Peralatan
                        $mesinItems = [];
                        $allNoPabrik = [];
                        $regCursor = 0;
                        $oldMesinList = $specLama['mesin_items'] ?? [];
                        foreach ($unitItems as $uIdx => $uItem) {
                            $oldM = $oldMesinList[$uIdx] ?? ($oldMesinList[0] ?? []);
                            $vol = max(1, (int) ($uItem['item_volume'] ?? ($uItem['mesin_jumlah_barang'] ?? 1)));
                            $ukuranVal = $uItem['mesin_ukuran'] ?? ($uItem['mesin_ukuran_cc'] ?? ($uItem['ukuran'] ?? ($uItem['ukuran_cc'] ?? ($rawNew['mesin_ukuran'] ?? ($rawNew['mesin_ukuran_cc'] ?? ($rawNew['ukuran'] ?? ($oldM['mesin_ukuran'] ?? ($oldM['mesin_ukuran_cc'] ?? ($oldM['ukuran'] ?? '-')))))))));
                            $m = [
                                'mesin_nama_barang'   => $uItem['item_nama'] ?? ($uItem['mesin_nama_barang'] ?? ($rawNew['mesin_nama_barang'] ?? ($oldM['mesin_nama_barang'] ?? $astap->nama_barang))),
                                'mesin_merk'          => $uItem['mesin_merk'] ?? ($rawNew['mesin_merk'] ?? ($oldM['mesin_merk'] ?? ($specLama['merk'] ?? '-'))),
                                'mesin_type'          => $uItem['mesin_type'] ?? ($rawNew['mesin_type'] ?? ($oldM['mesin_type'] ?? ($specLama['type'] ?? '-'))),
                                'mesin_ukuran'        => $ukuranVal,
                                'mesin_ukuran_cc'     => $ukuranVal,
                                'mesin_bahan'         => $uItem['mesin_bahan'] ?? ($rawNew['mesin_bahan'] ?? ($oldM['mesin_bahan'] ?? 'Logam / Komponen Elektronik')),
                                'mesin_no_pabrik'     => $uItem['mesin_no_pabrik'] ?? ($rawNew['mesin_no_pabrik'] ?? ($oldM['mesin_no_pabrik'] ?? ($specLama['no_pabrik'] ?? '-'))),
                                'mesin_no_rangka'     => $uItem['mesin_no_rangka'] ?? ($rawNew['mesin_no_rangka'] ?? ($oldM['mesin_no_rangka'] ?? '-')),
                                'mesin_no_mesin'      => $uItem['mesin_no_mesin'] ?? ($rawNew['mesin_no_mesin'] ?? ($oldM['mesin_no_mesin'] ?? '-')),
                                'mesin_no_polisi'     => $uItem['mesin_no_polisi'] ?? ($rawNew['mesin_no_polisi'] ?? ($oldM['mesin_no_polisi'] ?? '-')),
                                'mesin_no_bpkb'       => $uItem['mesin_no_bpkb'] ?? ($rawNew['mesin_no_bpkb'] ?? ($oldM['mesin_no_bpkb'] ?? '-')),
                                'mesin_tahun_pembuatan'=> $uItem['mesin_tahun_pembuatan'] ?? ($rawNew['mesin_tahun_pembuatan'] ?? ($oldM['mesin_tahun_pembuatan'] ?? null)),
                                'mesin_kondisi'       => $uItem['mesin_kondisi'] ?? ($rawNew['mesin_kondisi'] ?? ($oldM['mesin_kondisi'] ?? 'Baik')),
                                'mesin_keterangan'    => $uItem['mesin_keterangan'] ?? ($rawNew['mesin_keterangan'] ?? ($oldM['mesin_keterangan'] ?? null)),
                                'mesin_ruang_pemegang'=> $uItem['mesin_ruang_pemegang'] ?? ($astap->registers->skip($regCursor)->first()?->ruang_pemegang ?? 'Instalasi Perbekalan'),
                                'mesin_jumlah_barang' => $vol,
                                'mesin_satuan'        => $uItem['item_satuan'] ?? ($astap->satuan ?: 'Unit'),
                                'mesin_nilai_satuan'  => $unitPrice,
                                'mesin_total_nilai'   => $vol * $unitPrice,
                                'is_extracom'         => $isTargetExtracom,
                            ];
                            $mesinItems[] = $m;
                            if (!empty($m['mesin_no_pabrik']) && $m['mesin_no_pabrik'] !== '-') {
                                $allNoPabrik[] = $m['mesin_no_pabrik'];
                            }
                            $regCursor += $vol;
                        }
                        $newSpec['mesin_items']     = $mesinItems;
                        $newSpec['merk']            = $mesinItems[0]['mesin_merk'] ?? '-';
                        $newSpec['type']            = $mesinItems[0]['mesin_type'] ?? '-';
                        $newSpec['ukuran']          = $mesinItems[0]['mesin_ukuran'] ?? '-';
                        $newSpec['mesin_ukuran']    = $newSpec['ukuran'];
                        $newSpec['mesin_ukuran_cc'] = $newSpec['ukuran'];
                        $newSpec['bahan']           = $mesinItems[0]['mesin_bahan'] ?? 'Logam / Komponen Elektronik';
                        $newSpec['no_pabrik']       = count($allNoPabrik) > 0 ? implode(', ', $allNoPabrik) : ($mesinItems[0]['mesin_no_pabrik'] ?? '-');
                        if (\Illuminate\Support\Facades\Schema::hasColumn('astaps', 'merk_type')) {
                            $astap->merk_type = trim(($mesinItems[0]['mesin_merk'] ?? '') . ' ' . ($mesinItems[0]['mesin_type'] ?? ''));
                        }
                    }
                } elseif ($targetKib === 'ASET LAIN' || $targetKib === 'ASET LAIN-LAIN' || $targetKib === 'ASET LAINNYA') {
                    $rawKondisi = $rawNew['aset_lain_kondisi'] ?? ($rawNew['kondisi'] ?? 'Rusak Berat (Menunggu Penghapusan)');
                    $stdKondisi = 'Rusak Berat';
                    if (stripos($rawKondisi, 'Baik') !== false && stripos($rawKondisi, 'Kurang') === false && stripos($rawKondisi, 'Rusak') === false) {
                        $stdKondisi = 'Baik';
                    } elseif (stripos($rawKondisi, 'Kurang') !== false) {
                        $stdKondisi = 'Kurang Baik';
                    }

                    $asetLainItems = [];
                    foreach ($unitItems as $uIdx => $uItem) {
                        $uKondisi = $uItem['aset_lain_kondisi'] ?? $rawKondisi;
                        $asetLainItems[] = [
                            'item_nama'            => $uItem['item_nama'] ?? $astap->nama_barang,
                            'item_volume'          => $uItem['item_volume'] ?? 1,
                            'item_satuan'          => $uItem['item_satuan'] ?? ($astap->satuan ?? 'Unit'),
                            'aset_lain_kondisi'    => $uKondisi,
                            'aset_lain_alasan'     => $uItem['aset_lain_alasan'] ?? ($validated['alasan_reklas'] ?? 'Dialihkan ke Akun 1.5.4 Aset Lain-Lain'),
                            'aset_lain_nilai'      => $unitPrice,
                            'ruang_pemegang'       => $uItem['ruang_pemegang'] ?? ($astap->ruang_unit ?? '-'),
                        ];
                    }
                    $newSpec['aset_lain_items'] = $asetLainItems;
                    $newSpec['kondisi_barang']  = $asetLainItems[0]['aset_lain_kondisi'] ?? $rawKondisi;
                    $newSpec['aset_lain_kondisi'] = $asetLainItems[0]['aset_lain_kondisi'] ?? $rawKondisi;
                    $newSpec['kategori_kib']    = 'ASET LAIN';
                    if (\Illuminate\Support\Facades\Schema::hasColumn('astaps', 'kondisi')) {
                        $astap->kondisi = $stdKondisi;
                    }
                }

                if ($validated['jenis_reklas'] === 'KDP_TO_DEFINITIF') {
                    $newSpec['progres_persen'] = 100;
                    $newSpec['kdp_progres_persen'] = 100;
                    $newSpec['progres_fisik'] = '100%';
                    $newSpec['status_kdp'] = 'Selesai 100%';
                }

                $astap->spesifikasi_json = $newSpec;
                $specBaru = $newSpec;
            }

            // Penanganan khusus status dan spesifikasi untuk Hibah Keluar, Hibah Masuk, dan Mutasi Eksternal
            if ($validated['jenis_reklas'] === 'HIBAH_KELUAR') {
                $astap->is_reklas = 1;
                $astap->jenis_reklas = 'HIBAH_KELUAR';
                $tujuanKodeAset = 'KOR_HIBAH';
                $tujuanNamaAset = 'Koreksi Hibah / Bantuan Pemerintah (Dihibahkan)';
                if (\Illuminate\Support\Facades\Schema::hasColumn('astaps', 'kondisi')) {
                    $astap->kondisi = 'Dihibahkan';
                }
                $currSpec = is_array($astap->spesifikasi_json) ? $astap->spesifikasi_json : (json_decode($astap->spesifikasi_json, true) ?? []);
                $currSpec['status_barang'] = 'Dihibahkan';
                $currSpec['hibah_info'] = [
                    'tipe'         => 'keluar',
                    'pihak'        => $validated['pihak_hibah'] ?? ($request->input('pihak_hibah') ?? null),
                    'nomor_bast'   => $validated['nomor_ba_reklas'] ?? null,
                    'tanggal_bast' => $validated['tanggal_bast'] ?? ($request->input('tanggal_bast') ?? null),
                    'alasan'       => $validated['alasan_reklas'],
                ];
                $astap->spesifikasi_json = $currSpec;
                $specBaru = $currSpec;
            } elseif ($validated['jenis_reklas'] === 'HIBAH_MASUK') {
                $astap->is_reklas = 1;
                $astap->jenis_reklas = 'HIBAH_MASUK';
                $astap->sumber_dana = 'hibah';
                $asalKodeAset = 'KOR_HIBAH';
                $asalNamaAset = 'Koreksi Hibah / Bantuan Pemerintah Masuk';
                $currSpec = is_array($astap->spesifikasi_json) ? $astap->spesifikasi_json : (json_decode($astap->spesifikasi_json, true) ?? []);
                $currSpec['hibah_info'] = [
                    'tipe'         => 'masuk',
                    'pihak'        => $validated['pihak_hibah'] ?? ($request->input('pihak_hibah') ?? null),
                    'nomor_bast'   => $validated['nomor_ba_reklas'] ?? null,
                    'tanggal_bast' => $validated['tanggal_bast'] ?? ($request->input('tanggal_bast') ?? null),
                    'alasan'       => $validated['alasan_reklas'],
                ];
                $astap->spesifikasi_json = $currSpec;
                $specBaru = $currSpec;
            } elseif ($validated['jenis_reklas'] === 'MUTASI_EKSTERNAL') {
                $skpdTujuanNama = $validated['skpd_tujuan'] ?? ($request->input('skpd_tujuan') ?? 'OPD Luar');
                $selectedRegisterIds = $request->input('selected_register_ids', []);
                if (!is_array($selectedRegisterIds)) {
                    $selectedRegisterIds = [];
                }

                $activeRegsQuery = $astap->registers()->where('is_deleted', 0)->where(function($sr) {
                    $sr->whereNull('status')->orWhere('status', '!=', 'Mutasi Keluar OPD');
                });
                $totalActiveRegs = $activeRegsQuery->count();

                // Cek apakah transfer mutasi keluar ini mencakup seluruh unit aktif (100%) atau hanya sebagian (parsial)
                $isFullTransfer = false;
                if ($totalActiveRegs === 0) {
                    $isFullTransfer = true;
                } elseif (!empty($selectedRegisterIds)) {
                    $isFullTransfer = count($selectedRegisterIds) >= $totalActiveRegs;
                } else {
                    $isFullTransfer = true;
                }

                $astap->is_reklas = 1;
                // Hanya jika transfer 100% penuh, set jenis_reklas = 'MUTASI_EKSTERNAL' agar disembunyikan dari tabel aktif ASTAP
                if ($isFullTransfer) {
                    $astap->jenis_reklas = 'MUTASI_EKSTERNAL';
                    $astap->alamat_barang = 'Mutasi Keluar: ' . $skpdTujuanNama;
                    if (\Illuminate\Support\Facades\Schema::hasColumn('astaps', 'kondisi')) {
                        $astap->kondisi = 'Mutasi Keluar OPD';
                    }
                }

                $tujuanKodeAset = 'KOR_LAIN';
                $tujuanNamaAset = 'Koreksi Lain-Lain (Mutasi Keluar Antar-OPD)';

                $currSpec = is_array($astap->spesifikasi_json) ? $astap->spesifikasi_json : (json_decode($astap->spesifikasi_json, true) ?? []);
                if ($isFullTransfer) {
                    $currSpec['status_barang'] = 'Mutasi Keluar OPD';
                }
                $currSpec['mutasi_info'] = [
                    'skpd_tujuan'           => $skpdTujuanNama,
                    'nomor_bast'            => $validated['nomor_ba_reklas'] ?? null,
                    'tanggal_bast'          => $validated['tanggal_bast'] ?? ($request->input('tanggal_bast') ?? null),
                    'alasan'                => $validated['alasan_reklas'],
                    'selected_register_ids' => $selectedRegisterIds,
                    'is_full_transfer'      => $isFullTransfer,
                    'transferred_count'     => count($selectedRegisterIds),
                ];
                $astap->spesifikasi_json = $currSpec;
                $specBaru = $currSpec;
            }

            if ($specBaru === null) {
                $specBaru = $astap->spesifikasi_json;
            }

            // Fallback audit: Jika tujuan adalah KIB C atau KIB D dari Kemitraan, pastikan status fisik otomatis Lama
            $isFromKemitraanOrigin = in_array($asalKibAset, ['KEMITRAAN', '1.5.2'], true)
                || ($astap->category === 'KEMITRAAN')
                || ($astap->sumber_dana === 'kemitraan')
                || str_starts_with((string) $astap->kode_108, '1.5.2')
                || ($astap->kemitraan !== null);

            if ($isFromKemitraanOrigin && is_array($specBaru)) {
                if ($targetKib === 'KIB C') {
                    if (empty($specBaru['gedung_is_baru'])) {
                        $specBaru['gedung_is_baru'] = 'Lama';
                        $specBaru['is_baru'] = 'Lama';
                    }
                    if (!empty($specBaru['gedung_items']) && is_array($specBaru['gedung_items'])) {
                        foreach ($specBaru['gedung_items'] as &$gItemLoop) {
                            if (is_array($gItemLoop) && empty($gItemLoop['gedung_is_baru'])) {
                                $gItemLoop['gedung_is_baru'] = 'Lama';
                            }
                        }
                        unset($gItemLoop);
                    }
                    $astap->spesifikasi_json = $specBaru;
                } elseif ($targetKib === 'KIB D') {
                    if (empty($specBaru['jaringan_is_baru'])) {
                        $specBaru['jaringan_is_baru'] = 'Lama';
                        $specBaru['is_baru'] = 'Lama';
                    }
                    if (!empty($specBaru['jaringan_items']) && is_array($specBaru['jaringan_items'])) {
                        foreach ($specBaru['jaringan_items'] as &$jItemLoop) {
                            if (is_array($jItemLoop) && empty($jItemLoop['jaringan_is_baru'])) {
                                $jItemLoop['jaringan_is_baru'] = 'Lama';
                            }
                        }
                        unset($jItemLoop);
                    }
                    $astap->spesifikasi_json = $specBaru;
                }
            }

            // Pastikan fallback nama dan kode asal/tujuan jika masih kosong
            if (empty($tujuanNamaAset) && !empty($validated['jenis_reklasifikasi_tujuan_id'])) {
                $tRow = $allTemplateRows->firstWhere('id', $validated['jenis_reklasifikasi_tujuan_id']);
                if ($tRow) {
                    $tujuanNamaAset = $tRow->nama_sub_rincian;
                    $tujuanKodeAset = $tujuanKodeAset ?: $tRow->kode_prefix;
                }
            }
            if (empty($asalNamaAset) && !empty($validated['jenis_reklasifikasi_asal_id'])) {
                $aRow = $allTemplateRows->firstWhere('id', $validated['jenis_reklasifikasi_asal_id']);
                if ($aRow) {
                    $asalNamaAset = $aRow->nama_sub_rincian;
                    $asalKodeAset = $asalKodeAset ?: $aRow->kode_prefix;
                }
            }

            $asalId = $validated['jenis_reklasifikasi_asal_id'] ?? null;
            $tujuanId = $validated['jenis_reklasifikasi_tujuan_id'] ?? null;
            $asalKibFinal = $validated['asal_kib'] ?? ($asalKibAset ?? null);
            $tujuanKibFinal = $validated['tujuan_kib'] ?? null;

            // Upload berkas lampiran jika diunggah dan belum diproses sebelumnya
            if (empty($dokumenLampiranPath) && $request->hasFile('dokumen_file')) {
                $docFile = $request->file('dokumen_file');
                $prefix = ($validated['jenis_reklas'] === 'MUTASI_EKSTERNAL') ? 'BAST_KELUAR_' : 'REKLAS_';
                $subFolder = ($validated['jenis_reklas'] === 'MUTASI_EKSTERNAL') ? 'dokumen_mutasi_eksternal' : 'dokumen_reklasifikasi';
                $docFilename = $prefix . time() . '_' . preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $docFile->getClientOriginalName());
                $dokumenLampiranPath = $docFile->storeAs($subFolder, $docFilename, 'public');
            }

            // Resolusi otomatis ID Baris Asal jika belum disertakan
            if (empty($asalId)) {
                if ($validated['jenis_reklas'] === 'HIBAH_MASUK' || $asalKibFinal === 'HIBAH') {
                    $asalId = $allTemplateRows->firstWhere('kode_prefix', 'KOR_HIBAH')?->id;
                } elseif ($validated['jenis_reklas'] === 'KAPITALISASI_INTRAKOM' || $asalKibFinal === 'EKSTRAKOMPTABEL') {
                    $asalId = $allTemplateRows->firstWhere('kode_prefix', 'KOR_EXTRACOM')?->id;
                } elseif ($asalKibFinal === 'KEMITRAAN') {
                    $asalId = $allTemplateRows->firstWhere('kode_prefix', '1.5.2')?->id;
                } elseif ($asalKibFinal === 'ATB') {
                    $asalId = $allTemplateRows->firstWhere('kode_prefix', '1.5.3')?->id;
                } elseif (in_array($asalKibFinal, ['ASET LAIN', 'ASET LAIN-LAIN', 'ASET LAINNYA'])) {
                    $asalId = $allTemplateRows->firstWhere('kode_prefix', '1.5.4')?->id;
                } elseif ($asalKodeAset) {
                    $matched = $allTemplateRows->first(function($r) use ($asalKodeAset) {
                        return $r->kode_prefix && (str_starts_with($asalKodeAset, $r->kode_prefix) || str_starts_with($r->kode_prefix, $asalKodeAset));
                    });
                    if ($matched) $asalId = $matched->id;
                } elseif ($asalKibFinal) {
                    $asalId = $allTemplateRows->firstWhere('kelompok_kib', $asalKibFinal)?->id;
                }
            }

            // Resolusi otomatis ID Baris Tujuan jika belum disertakan
            if (empty($tujuanId)) {
                if ($validated['jenis_reklas'] === 'HIBAH_KELUAR' || $tujuanKibFinal === 'HIBAH') {
                    $tujuanId = $allTemplateRows->firstWhere('kode_prefix', 'KOR_HIBAH')?->id;
                } elseif ($validated['jenis_reklas'] === 'MUTASI_EKSTERNAL') {
                    $tujuanId = $allTemplateRows->firstWhere('kode_prefix', 'KOR_LAIN')?->id;
                } elseif ($validated['jenis_reklas'] === 'EKSTRAKOMPTABEL' || $tujuanKibFinal === 'EKSTRAKOMPTABEL') {
                    $tujuanId = $allTemplateRows->firstWhere('kode_prefix', 'KOR_EXTRACOM')?->id;
                } elseif ($tujuanKibFinal === 'KEMITRAAN') {
                    $tujuanId = $allTemplateRows->firstWhere('kode_prefix', '1.5.2')?->id;
                } elseif ($tujuanKibFinal === 'ATB') {
                    $tujuanId = $allTemplateRows->firstWhere('kode_prefix', '1.5.3')?->id;
                } elseif (in_array($tujuanKibFinal, ['ASET LAIN', 'ASET LAIN-LAIN', 'ASET LAINNYA'])) {
                    $tujuanId = $allTemplateRows->firstWhere('kode_prefix', '1.5.4')?->id;
                } elseif ($tujuanKodeAset) {
                    $matched = $allTemplateRows->first(function($r) use ($tujuanKodeAset) {
                        return $r->kode_prefix && (str_starts_with($tujuanKodeAset, $r->kode_prefix) || str_starts_with($r->kode_prefix, $tujuanKodeAset));
                    });
                    if ($matched) $tujuanId = $matched->id;
                } elseif ($tujuanKibFinal) {
                    $tujuanId = $allTemplateRows->firstWhere('kelompok_kib', $tujuanKibFinal)?->id;
                }
            }

            if ($isPartial) {
                // =========================================================================
                // OPSI 2: AUTO-SPLIT / REKLASIFIKASI PARSIAL
                // Item terpilih dialihkan ke KIB/Rekening Tujuan (misal Kemitraan 1.5.2).
                // Sisa item yang tidak dipilih tetap berada di KIB asal (misal KIB A Tanah).
                // =========================================================================
                $selectedCount = count($selectedIndices);
                $unselectedItems = $request->input('unselected_items', []);
                $totalOrigVol = max(1, (int) $astap->jumlah_volume);
                $effectiveTotalVol = max($totalOrigVol, $selectedCount + count($unselectedItems));
                $remainingVol = max(1, $effectiveTotalVol - $selectedCount);

                $nilaiReklasSplit = (float) $validated['nilai_reklas'];
                if ($nilaiReklasSplit <= 0) {
                    $nilaiReklasSplit = round(((float) $astap->total_realisasi / $effectiveTotalVol) * $selectedCount);
                }
                $sisaRealisasi = max(0, (float) $astap->total_realisasi - $nilaiReklasSplit);

                $anggaranReklasSplit = round(((float) $astap->jumlah_anggaran / $effectiveTotalVol) * $selectedCount);
                $sisaAnggaran = max(0, (float) $astap->jumlah_anggaran - $anggaranReklasSplit);

                // 1. Buat record baru untuk unit yang dialihkan ($targetAstap)
                $targetAstap = $astap->replicate();
                $targetAstap->jumlah_volume = $selectedCount;
                $targetAstap->total_realisasi = $nilaiReklasSplit;
                $targetAstap->harga_satuan = $selectedCount > 0 ? ($nilaiReklasSplit / $selectedCount) : $nilaiReklasSplit;
                $targetAstap->jumlah_anggaran = $anggaranReklasSplit;
                $targetAstap->is_reklas = 1;
                $targetAstap->jenis_reklas = $validated['jenis_reklas'];

                if ($targetKib === 'KEMITRAAN') {
                    $targetAstap->sumber_dana = 'kemitraan';
                    $targetAstap->unit_id = null; // Lepaskan dari ruangan KIR
                    $kemitraanJenis = null;
                    if ($tujuanKodeAset) {
                        $kemitraanJenis = JenisAstap::where('sub_sub_rincian_objek', $tujuanKodeAset)
                            ->orWhere('sub_rincian_objek', $tujuanKodeAset)
                            ->orWhere('jenis', $tujuanKodeAset)
                            ->first();
                    }
                    if (!$kemitraanJenis) {
                        $kemitraanJenis = JenisAstap::where('jenis', 'like', '1.5.2%')->first();
                    }
                    if ($kemitraanJenis) {
                        $targetAstap->jenis_astap_id = $kemitraanJenis->id;
                    }
                } elseif ($tujuanKodeAset) {
                    $tJenis = JenisAstap::where('sub_sub_rincian_objek', $tujuanKodeAset)
                        ->orWhere('sub_rincian_objek', $tujuanKodeAset)
                        ->orWhere('jenis', $tujuanKodeAset)
                        ->first();
                    if ($tJenis) {
                        $targetAstap->jenis_astap_id = $tJenis->id;
                    }
                }

                $targetSpec = is_array($specBaru) ? $specBaru : [];
                $targetSpec['split_parent_id'] = $astap->id;
                $targetSpec['split_is_partial'] = true;
                $targetSpec['split_volume'] = $selectedCount;

                // Salin item spesifikasi fisik asli dari unit yang dipilih ke aset baru hasil split
                $repeaterKeys = ['tanah_items', 'mesin_items', 'gedung_items', 'jaringan_items', 'lainnya_items', 'atb_items'];
                foreach ($repeaterKeys as $rk) {
                    if (!empty($specLama[$rk]) && is_array($specLama[$rk])) {
                        $transferredItems = [];
                        foreach ($selectedIndices as $sIdx) {
                            if (isset($specLama[$rk][$sIdx])) {
                                $transferredItems[] = $specLama[$rk][$sIdx];
                            }
                        }
                        if (!empty($transferredItems)) {
                            $targetSpec[$rk] = $transferredItems;
                            if ($rk === 'tanah_items') {
                                $targetSpec['luas_m2'] = array_sum(array_column($transferredItems, 'tanah_luas_m2'));
                                $targetSpec['hak_tanah'] = $transferredItems[0]['tanah_hak'] ?? ($specLama['hak_tanah'] ?? 'Hak Pakai');
                                $targetSpec['sertifikat_no'] = $transferredItems[0]['tanah_sertifikat_no'] ?? ($specLama['sertifikat_no'] ?? null);
                                $targetSpec['sertifikat_tgl'] = $transferredItems[0]['tanah_sertifikat_tgl'] ?? ($specLama['sertifikat_tgl'] ?? null);
                                $targetSpec['penggunaan'] = $transferredItems[0]['tanah_penggunaan'] ?? ($specLama['penggunaan'] ?? null);
                                unset($targetSpec['mesin_items']);
                            } elseif ($rk === 'gedung_items') {
                                $targetSpec['luas_lantai_m2'] = array_sum(array_column($transferredItems, 'gedung_luas_lantai_m2'));
                                unset($targetSpec['mesin_items']);
                            } elseif ($rk === 'jaringan_items') {
                                $targetSpec['luas_m2'] = array_sum(array_column($transferredItems, 'jaringan_luas_m2'));
                                unset($targetSpec['mesin_items']);
                            }
                        }
                    }
                }

                if ($targetKib === 'ATB') {
                    $targetSpec['kategori_kib'] = 'ATB';
                    $targetAstap->satuan = 'Paket / Lisensi';
                    $judulAtb = $targetSpec['atb_judul_nama'] ?? ($targetSpec['atb_nama_software'] ?? null);
                    if ($judulAtb) {
                        $targetAstap->nama_barang = $judulAtb;
                    }
                    if (empty($targetSpec['atb_items'])) {
                        $targetSpec['atb_items'] = [[
                            'atb_judul_nama'    => $judulAtb ?: $targetAstap->nama_barang,
                            'atb_nama_software' => $judulAtb ?: $targetAstap->nama_barang,
                            'atb_nama_barang'   => $targetAstap->nama_barang,
                            'atb_pencipta'      => $targetSpec['atb_pencipta'] ?? ($targetSpec['atb_pengembang'] ?? '-'),
                            'atb_pengembang'    => $targetSpec['atb_pencipta'] ?? ($targetSpec['atb_pengembang'] ?? '-'),
                            'atb_spesifikasi'   => $targetSpec['atb_spesifikasi'] ?? '-',
                            'atb_nilai_satuan'  => $nilaiReklasSplit / max(1, $selectedCount),
                            'atb_jumlah'        => $selectedCount,
                            'atb_satuan'        => 'Paket / Lisensi',
                        ]];
                    }
                    unset($targetSpec['mesin_items']);
                }

                if (in_array($targetKib, ['ASET LAIN', 'ASET LAIN-LAIN', 'ASET LAINNYA'], true)) {
                    $targetSpec['kategori_kib'] = 'ASET LAIN';
                    $rawKnd = $targetSpec['kondisi_barang'] ?? ($targetSpec['aset_lain_kondisi'] ?? ($validated['spesifikasi_baru']['aset_lain_kondisi'] ?? 'Rusak Berat (Menunggu Penghapusan)'));
                    $stdKondisi = 'Rusak Berat';
                    if (stripos($rawKnd, 'Baik') !== false && stripos($rawKnd, 'Kurang') === false && stripos($rawKnd, 'Rusak') === false) {
                        $stdKondisi = 'Baik';
                    } elseif (stripos($rawKnd, 'Kurang') !== false) {
                        $stdKondisi = 'Kurang Baik';
                    }

                    $targetSpec['kondisi_barang'] = $rawKnd;
                    $targetSpec['aset_lain_kondisi'] = $rawKnd;

                    if (empty($targetSpec['aset_lain_items'])) {
                        $targetSpec['aset_lain_items'] = [[
                            'item_nama'         => $targetAstap->nama_barang,
                            'item_volume'       => $selectedCount,
                            'item_satuan'       => $targetAstap->satuan ?? 'Unit',
                            'aset_lain_kondisi' => $rawKnd,
                            'aset_lain_alasan'  => $validated['alasan_reklas'] ?? 'Dialihkan ke Akun 1.5.4 Aset Lain-Lain',
                            'aset_lain_nilai'   => $nilaiReklasSplit,
                            'ruang_pemegang'    => $targetAstap->ruang_unit ?? '-',
                        ]];
                    }

                    foreach (['lainnya_items', 'mesin_items', 'gedung_items', 'jaringan_items', 'tanah_items', 'atb_items'] as $rk) {
                        if (!empty($targetSpec[$rk]) && is_array($targetSpec[$rk])) {
                            foreach ($targetSpec[$rk] as &$tItem) {
                                if (isset($tItem['lainnya_kondisi'])) $tItem['lainnya_kondisi'] = $stdKondisi;
                                if (isset($tItem['mesin_kondisi'])) $tItem['mesin_kondisi'] = $stdKondisi;
                                if (isset($tItem['gedung_kondisi'])) $tItem['gedung_kondisi'] = $stdKondisi;
                                if (isset($tItem['jaringan_kondisi'])) $tItem['jaringan_kondisi'] = $stdKondisi;
                                if (isset($tItem['kondisi'])) $tItem['kondisi'] = $stdKondisi;
                            }
                            unset($tItem);
                        }
                    }

                    if (\Illuminate\Support\Facades\Schema::hasColumn('astaps', 'kondisi')) {
                        $targetAstap->kondisi = $stdKondisi;
                    }
                }

                $targetAstap->spesifikasi_json = $targetSpec;
                $targetAstap->save();

                // Replikasi dokumen belanja modal jika ada
                $origBm = \App\Models\AstapBelanjaModal::where('astap_id', $astap->id)->first();
                if ($origBm) {
                    $targetBm = $origBm->replicate();
                    $targetBm->astap_id = $targetAstap->id;
                    $targetBm->save();
                }

                // Pindahkan register NIBAR terpilih ke $targetAstap (NIBAR fisik asli dipertahankan sebagai NIK barang)
                $allRegisters = $astap->registers()->orderBy('no_register_int', 'asc')->orderBy('id', 'asc')->get();
                $firstTransferredReg = null;
                foreach ($selectedIndices as $sIdx) {
                    if (isset($allRegisters[$sIdx])) {
                        $regToMove = $allRegisters[$sIdx];
                        $regToMove->astap_id = $targetAstap->id;
                        if ($targetKib === 'KEMITRAAN') {
                            $regToMove->status = 'Kemitraan';
                            $regToMove->unit_id = null; // Lepaskan dari unit ruangan KIR
                            $mName = $targetSpec['mitra_nama'] ?? ($validated['spesifikasi_baru']['kemitraan_mitra'] ?? 'Pihak Ketiga');
                            $regToMove->ruang_pemegang = 'Kemitraan: ' . $mName;
                        } elseif (in_array($targetKib, ['ASET LAIN', 'ASET LAIN-LAIN', 'ASET LAINNYA'], true)) {
                            $rawKnd = $targetSpec['kondisi_barang'] ?? ($targetSpec['aset_lain_kondisi'] ?? 'Rusak Berat (Menunggu Penghapusan)');
                            $stdKondisi = 'Rusak Berat';
                            if (stripos($rawKnd, 'Baik') !== false && stripos($rawKnd, 'Kurang') === false && stripos($rawKnd, 'Rusak') === false) {
                                $stdKondisi = 'Baik';
                            } elseif (stripos($rawKnd, 'Kurang') !== false) {
                                $stdKondisi = 'Kurang Baik';
                            }
                            $regToMove->kondisi = $stdKondisi;
                            $regToMove->status  = ($stdKondisi === 'Baik') ? 'Tersedia' : 'Rusak Berat';
                        }
                        $regToMove->save();
                        if (!$firstTransferredReg) {
                            $firstTransferredReg = $regToMove;
                        }
                    }
                }

                // 2. Perbarui aset asal ($astap) yang tetap berada di KIB asal (misal KIB A Tanah)
                $astap->jumlah_volume = $remainingVol;
                $astap->total_realisasi = $sisaRealisasi;
                $astap->harga_satuan = $remainingVol > 0 ? ($sisaRealisasi / $remainingVol) : $sisaRealisasi;
                $astap->jumlah_anggaran = $sisaAnggaran;

                // Filter spesifikasi item di aset asal agar hanya tersisa unit yang TIDAK dialihkan
                $currOrigSpec = is_array($astap->spesifikasi_json) ? $astap->spesifikasi_json : (json_decode($astap->spesifikasi_json, true) ?? []);
                $repeaterKeys = ['tanah_items', 'mesin_items', 'gedung_items', 'jaringan_items', 'lainnya_items', 'atb_items', 'kemitraan_items', 'kdp_items', 'aset_lain_items'];
                foreach ($repeaterKeys as $rk) {
                    if (!empty($currOrigSpec[$rk]) && is_array($currOrigSpec[$rk])) {
                        $filtered = [];
                        foreach ($currOrigSpec[$rk] as $idx => $item) {
                            if (!in_array($idx, $selectedIndices, true)) {
                                $filtered[] = $item;
                            }
                        }
                        $currOrigSpec[$rk] = !empty($filtered) ? array_values($filtered) : (!empty($unselectedItems) ? $unselectedItems : []);
                    }
                }
                if (!empty($currOrigSpec['tanah_items'])) {
                    $currOrigSpec['luas_m2'] = array_sum(array_column($currOrigSpec['tanah_items'], 'tanah_luas_m2'));
                }
                if (!isset($currOrigSpec['riwayat_split'])) {
                    $currOrigSpec['riwayat_split'] = [];
                }
                $currOrigSpec['riwayat_split'][] = [
                    'tanggal'          => $validated['tanggal_reklas'],
                    'tipe'             => 'split_partial_out',
                    'target_astap_id'  => $targetAstap->id,
                    'volume_dipindah'  => $selectedCount,
                    'nilai_dipindah'   => $nilaiReklasSplit,
                    'tujuan_kib'       => $targetKib ?: $tujuanKibFinal,
                    'keterangan'       => "Pemecahan parsial: {$selectedCount} unit dialihkan ke " . ($targetKib ?: $tujuanKibFinal) . " (Aset Baru ID #{$targetAstap->id})",
                ];
                $astap->spesifikasi_json = $currOrigSpec;
                $astap->save();

                // Sinkronkan record kemitraan jika aset asal merupakan aset kemitraan
                if ($astap->kemitraan) {
                    $astap->kemitraan->update([
                        'jumlah_volume' => $remainingVol,
                        'nilai_aset'    => $sisaRealisasi,
                    ]);
                }

                // Simpan audit log reklasifikasi untuk $targetAstap
                $reklasData = [
                    'astap_id'                      => $targetAstap->id,
                    'jenis_reklasifikasi_asal_id'   => $asalId,
                    'jenis_reklasifikasi_tujuan_id' => $tujuanId,
                    'jenis_reklas'                  => $validated['jenis_reklas'],
                    'sub_koreksi'                   => $validated['sub_koreksi'] ?? ($subKoreksi ?? null),
                    'asal_kib'                      => $asalKibFinal,
                    'asal_kode'                     => $asalKodeAset ?? null,
                    'asal_nama'                     => $asalNamaAset ?: 'Aset Tidak Dikenal',
                    'tujuan_kib'                    => $tujuanKibFinal,
                    'tujuan_kode'                   => $tujuanKodeAset ?? null,
                    'tujuan_nama'                   => $tujuanNamaAset ?? null,
                    'nilai_reklas'                  => $nilaiReklasSplit,
                    'tanggal_reklas'                => $validated['tanggal_reklas'],
                    'triwulan'                      => $validated['triwulan'],
                    'tahun'                         => $validated['tahun'],
                    'nomor_ba_reklas'               => $validated['nomor_ba_reklas'] ?? null,
                    'alasan_reklas'                 => $validated['alasan_reklas'] ?? ($request->input('alasan_reklas') ?? null),
                    'keterangan'                    => ($validated['keterangan'] ? ($validated['keterangan'] . ' | ') : '')
                        . "Reklasifikasi Parsial dari {$astap->nama_barang} (ID Asal: #{$astap->id}) - {$selectedCount} dari {$effectiveTotalVol} unit dipisahkan ke " . ($targetKib ?: $tujuanKibFinal),
                    'spesifikasi_lama'              => $specLama,
                    'spesifikasi_baru'              => $targetSpec,
                    'dokumen_path'                  => $dokumenLampiranPath ?? null,
                    'user_id'                       => Auth::id(),
                ];
                $reklas = AstapReklas::create($reklasData);

                // Simpan Kemitraan untuk $targetAstap (ditautkan ke $astap sebagai objek_astap_id)
                if ($targetKib === 'KEMITRAAN') {
                    $spec = $targetSpec;
                    $kemitraanData = [
                        'astap_id'         => $targetAstap->id,
                        'tipe_kemitraan'   => 'dimanfaatkan',
                        'objek_astap_id'   => $astap->id,
                        'objek_register_id'=> $firstTransferredReg?->id ?? null,
                        'objek_nibar'      => $firstTransferredReg?->nibar ?? null,
                        'mitra_nama'       => $spec['mitra_nama'] ?? ($validated['spesifikasi_baru']['kemitraan_mitra'] ?? 'Mitra Pihak Ketiga'),
                        'mitra_pimpinan'   => $spec['mitra_pimpinan'] ?? ($validated['spesifikasi_baru']['kemitraan_pimpinan'] ?? null),
                        'mitra_alamat'     => $spec['mitra_alamat'] ?? ($validated['spesifikasi_baru']['kemitraan_alamat'] ?? null),
                        'nomor_pks'        => $spec['perjanjian_nomor'] ?? ($validated['spesifikasi_baru']['kemitraan_perjanjian_no'] ?? ($validated['nomor_ba_reklas'] ?? '-')),
                        'tanggal_pks'      => $spec['tanggal_pks'] ?? ($validated['spesifikasi_baru']['kemitraan_tanggal_pks'] ?? ($validated['tanggal_reklas'] ?? now())),
                        'skema_kemitraan'  => (function() use ($spec, $validated, $targetAstap) {
                            $targetCode = (string) ($targetAstap->kode_barang ?? ($validated['tujuan_kode'] ?? ''));
                            $s = $spec['skema_kemitraan'] ?? ($validated['spesifikasi_baru']['kemitraan_skema'] ?? null);
                            if (!$s || $s === 'Sewa') {
                                if (str_contains($targetCode, '.01.01.02') || str_ends_with($targetCode, '.02')) return 'Kerjasama Pemanfaatan (KP)';
                                if (str_contains($targetCode, '.01.01.03') || str_ends_with($targetCode, '.03')) return 'BGS/BSG';
                                if (str_contains($targetCode, '.01.01.04') || str_ends_with($targetCode, '.04')) return 'KSPI';
                            }
                            return $s ?: 'Sewa';
                        })(),
                        'tanggal_mulai'    => $spec['tanggal_mulai'] ?? ($validated['spesifikasi_baru']['kemitraan_tanggal_mulai'] ?? null),
                        'tanggal_selesai'  => $spec['tanggal_selesai'] ?? ($validated['spesifikasi_baru']['kemitraan_tanggal_selesai'] ?? null),
                        'status_konsesi'   => 'Aktif',
                        'jumlah_volume'    => $selectedCount,
                        'satuan'           => $targetAstap->satuan ?: ($astap->satuan ?: 'Bidang / Titik'),
                        'nilai_aset'       => $nilaiReklasSplit,
                        'tahun'            => (int) ($validated['tahun'] ?? date('Y')),
                        'triwulan'         => (string) ($validated['triwulan'] ?? 1),
                        'keterangan'       => (!empty($validated['keterangan']) ? ($validated['keterangan'] . ' | ') : '')
                            . (!empty($spec['harga_sewa']) ? ('Tarif Sewa: Rp ' . number_format((float) $spec['harga_sewa'], 0, ',', '.') . ' (' . ($spec['periode_sewa'] ?? 'Per Tahun') . ')') : (!empty($spec['nilai_kontribusi']) ? ('Nilai Kontribusi: Rp ' . number_format((float) $spec['nilai_kontribusi'], 0, ',', '.') . ($spec['keterangan_kontribusi'] ? ' - ' . $spec['keterangan_kontribusi'] : '')) : ($targetAstap->keterangan_tambahan ?? ''))),
                        'user_id'          => Auth::id(),
                    ];
                    \App\Models\AstapKemitraan::create($kemitraanData);
                }
            } else {
                // =========================================================================
                // ALUR NORMAL: REKLASIFIKASI PENUH (100% UNIT ASET)
                // =========================================================================
                $astap->save();

                // Sinkronkan data ke register jika ada perubahan pada kolom register yang valid (JANGAN timpa register jika MUTASI_EKSTERNAL)
                if ($validated['jenis_reklas'] !== 'MUTASI_EKSTERNAL' && $astap->registers()->exists()) {
                    $regUpdate = [];
                    if (\Illuminate\Support\Facades\Schema::hasColumn('astap_registers', 'ruang_pemegang') && !empty($astap->ruang_unit)) {
                        $regUpdate['ruang_pemegang'] = $astap->ruang_unit;
                    }
                    if (\Illuminate\Support\Facades\Schema::hasColumn('astap_registers', 'kondisi')) {
                        if (in_array($targetKib, ['ASET LAIN', 'ASET LAIN-LAIN', 'ASET LAINNYA'], true)) {
                            $rawK = $newSpec['aset_lain_kondisi'] ?? ($newSpec['kondisi_barang'] ?? 'Rusak Berat');
                            $stdK = (stripos($rawK, 'Baik') !== false && stripos($rawK, 'Kurang') === false && stripos($rawK, 'Rusak') === false) ? 'Baik' : ((stripos($rawK, 'Kurang') !== false) ? 'Kurang Baik' : 'Rusak Berat');
                            $regUpdate['kondisi'] = $stdK;
                            if (\Illuminate\Support\Facades\Schema::hasColumn('astap_registers', 'status')) {
                                $regUpdate['status']  = ($stdK === 'Baik') ? 'Tersedia' : 'Rusak Berat';
                            }
                        } elseif (!empty($astap->kondisi)) {
                            $regUpdate['kondisi'] = $astap->kondisi;
                        }
                    }
                    if (!empty($regUpdate)) {
                        $astap->registers()->update($regUpdate);
                    }
                }

                // Simpan audit log reklasifikasi
                $reklasData = [
                    'astap_id'                      => $validated['astap_id'],
                    'jenis_reklasifikasi_asal_id'   => $asalId,
                    'jenis_reklasifikasi_tujuan_id' => $tujuanId,
                    'jenis_reklas'                  => $validated['jenis_reklas'],
                    'sub_koreksi'                   => $validated['sub_koreksi'] ?? ($subKoreksi ?? null),
                    'asal_kib'                      => $asalKibFinal,
                    'asal_kode'                     => $asalKodeAset ?? null,
                    'asal_nama'                     => $asalNamaAset ?: 'Aset Tidak Dikenal',
                    'tujuan_kib'                    => $tujuanKibFinal,
                    'tujuan_kode'                   => $tujuanKodeAset ?? null,
                    'tujuan_nama'                   => $tujuanNamaAset ?? null,
                    'nilai_reklas'                  => $validated['nilai_reklas'],
                    'tanggal_reklas'                => $validated['tanggal_reklas'],
                    'triwulan'                      => $validated['triwulan'],
                    'tahun'                         => $validated['tahun'],
                    'nomor_ba_reklas'               => $validated['nomor_ba_reklas'] ?? null,
                    'alasan_reklas'                 => $validated['alasan_reklas'] ?? ($request->input('alasan_reklas') ?? null),
                    'keterangan'                    => $validated['keterangan'] ?? null,
                    'spesifikasi_lama'              => $specLama,
                    'spesifikasi_baru'              => $specBaru,
                    'dokumen_path'                  => $dokumenLampiranPath ?? null,
                    'user_id'                       => Auth::id(),
                ];
                $reklas = AstapReklas::create($reklasData);

                // Sinkronisasi status dan data Kemitraan (Akun 1.5.2)
                if ($targetKib === 'KEMITRAAN') {
                    $astap->sumber_dana = 'kemitraan';
                    $spec = $astap->spesifikasi_json ?? [];
                    if (!empty($astap->unit_id)) {
                        $spec['unit_id_asal'] = $astap->unit_id;
                        $spec['ruang_asal'] = $astap->ruang_unit;
                        $astap->spesifikasi_json = $spec;
                        $astap->unit_id = null; // Lepaskan dari unit ruangan KIR
                    }
                    $astap->save();
                    $existingKemitraan = $astap->kemitraan ?? $kemitraan;
                    $kemitraanData = [
                        'astap_id'         => $astap->id,
                        'tipe_kemitraan'   => 'dimanfaatkan',
                        'objek_astap_id'   => null,
                        'objek_register_id'=> null,
                        'mitra_nama'       => $spec['mitra_nama'] ?? ($validated['spesifikasi_baru']['kemitraan_mitra'] ?? ($existingKemitraan?->mitra_nama ?? 'Mitra Pihak Ketiga')),
                        'mitra_pimpinan'   => $spec['mitra_pimpinan'] ?? ($validated['spesifikasi_baru']['kemitraan_pimpinan'] ?? ($existingKemitraan?->mitra_pimpinan ?? null)),
                        'mitra_alamat'     => $spec['mitra_alamat'] ?? ($validated['spesifikasi_baru']['kemitraan_alamat'] ?? ($existingKemitraan?->mitra_alamat ?? null)),
                        'nomor_pks'        => $spec['perjanjian_nomor'] ?? ($validated['spesifikasi_baru']['kemitraan_perjanjian_no'] ?? ($existingKemitraan?->nomor_pks ?? ($validated['nomor_ba_reklas'] ?? '-'))),
                        'tanggal_pks'      => $spec['tanggal_pks'] ?? ($validated['spesifikasi_baru']['kemitraan_tanggal_pks'] ?? ($existingKemitraan?->tanggal_pks ?? ($validated['tanggal_reklas'] ?? now()))),
                        'skema_kemitraan'  => (function() use ($spec, $validated, $existingKemitraan, $tujuanKodeAset, $astap) {
                            $targetCode = (string) ($tujuanKodeAset ?? ($astap->kode_barang ?? ($validated['tujuan_kode'] ?? '')));
                            $s = $spec['skema_kemitraan'] ?? ($validated['spesifikasi_baru']['kemitraan_skema'] ?? ($existingKemitraan?->skema_kemitraan ?? null));
                            if (!$s || $s === 'Sewa') {
                                if (str_contains($targetCode, '.01.01.02') || str_ends_with($targetCode, '.02')) return 'Kerjasama Pemanfaatan (KP)';
                                if (str_contains($targetCode, '.01.01.03') || str_ends_with($targetCode, '.03')) return 'BGS/BSG';
                                if (str_contains($targetCode, '.01.01.04') || str_ends_with($targetCode, '.04')) return 'KSPI';
                            }
                            return $s ?: 'Sewa';
                        })(),
                        'tanggal_mulai'    => $spec['tanggal_mulai'] ?? ($validated['spesifikasi_baru']['kemitraan_tanggal_mulai'] ?? ($existingKemitraan?->tanggal_mulai ?? null)),
                        'tanggal_selesai'  => $spec['tanggal_selesai'] ?? ($validated['spesifikasi_baru']['kemitraan_tanggal_selesai'] ?? ($existingKemitraan?->tanggal_selesai ?? null)),
                        'status_konsesi'   => 'Aktif',
                        'jumlah_volume'    => max(1, (int) $astap->jumlah_volume),
                        'satuan'           => $astap->satuan ?: 'Bidang / Titik',
                        'nilai_aset'       => (float) $astap->total_realisasi,
                        'tahun'            => (int) ($validated['tahun'] ?? date('Y')),
                        'triwulan'         => (string) ($validated['triwulan'] ?? 1),
                        'keterangan'       => (!empty($validated['keterangan']) ? ($validated['keterangan'] . ' | ') : '')
                            . (!empty($spec['harga_sewa']) ? ('Tarif Sewa: Rp ' . number_format((float) $spec['harga_sewa'], 0, ',', '.') . ' (' . ($spec['periode_sewa'] ?? 'Per Tahun') . ')') : (!empty($spec['nilai_kontribusi']) ? ('Nilai Kontribusi: Rp ' . number_format((float) $spec['nilai_kontribusi'], 0, ',', '.') . ($spec['keterangan_kontribusi'] ? ' - ' . $spec['keterangan_kontribusi'] : '')) : ($astap->keterangan_tambahan ?? ''))),
                        'user_id'          => Auth::id(),
                    ];

                    if ($astap->kemitraan) {
                        $astap->kemitraan->update($kemitraanData);
                    } elseif ($kemitraan) {
                        $kemitraan->update($kemitraanData);
                    } else {
                        \App\Models\AstapKemitraan::create($kemitraanData);
                    }

                    $mNameFull = $spec['mitra_nama'] ?? ($validated['spesifikasi_baru']['kemitraan_mitra'] ?? 'Mitra Pihak Ketiga');
                    $astap->registers()->update([
                        'status'         => 'Kemitraan',
                        'unit_id'        => null, // Lepaskan dari unit ruangan KIR
                        'ruang_pemegang' => 'Kemitraan: ' . $mNameFull,
                    ]);
                } elseif ($astap->kemitraan && $asalKibFinal === 'KEMITRAAN') {
                    // Hanya perbarui status menjadi Selesai jika asal reklas adalah Kemitraan (berakhirnya masa konsesi)
                    $astap->kemitraan->update([
                        'status_konsesi' => 'Selesai / Reklasifikasi'
                    ]);

                    // Sinkronkan objek aset BMD terkait (2 arah)
                    if ($astap->kemitraan->objek_astap_id) {
                        \App\Models\AstapKemitraan::where('astap_id', $astap->kemitraan->objek_astap_id)
                            ->update(['status_konsesi' => 'Selesai / Reklasifikasi']);
                    }
                    \App\Models\AstapKemitraan::where('objek_astap_id', $astap->id)
                        ->update(['status_konsesi' => 'Selesai / Reklasifikasi']);

                    // Kembalikan status register fisik menjadi Tersedia (bukan lagi terikat operasional kemitraan)
                    $astap->registers()->where('status', 'Kemitraan')->update([
                        'status' => 'Tersedia'
                    ]);
                }
            }

            // Sinkronisasi otomatis Mutasi Antar-OPD Keluar (MUTASI_EKSTERNAL)
            $isFullTransferFinal = true;
            if ($validated['jenis_reklas'] === 'MUTASI_EKSTERNAL') {
                $skpdTujuan = $validated['skpd_tujuan'] ?? ($request->input('skpd_tujuan') ?? 'SKPD / OPD Luar');
                $tglBast = $validated['tanggal_bast'] ?? ($request->input('tanggal_bast') ?? ($validated['tanggal_reklas'] ?? date('Y-m-d')));
                $nomorBast = $validated['nomor_ba_reklas'] ?? ('000.2.3.2/BAST-KLR-' . str_pad($astap->id, 3, '0', STR_PAD_LEFT) . '/430.10.7/' . date('Y'));
                
                $pjAsal = $request->input('pj_asal_nama') ?: ($astap->ppk_nama ?: 'BUDI HARTONO, S.Sos');
                $pjAsalNip = $request->input('pj_asal_nip') ?: ($astap->ppk_nip ?: '19760229 200801 1 010');
                $pjAsalJabatan = $request->input('pj_asal_jabatan') ?: 'Pengurus Barang Pengguna';
                
                $pjTujuan = $request->input('pejabat_opd_tujuan') ?: ($request->input('pj_tujuan_nama') ?: 'Pejabat Penerima OPD');
                $pjTujuanNip = $request->input('nip_pejabat_opd_tujuan') ?: ($request->input('pj_tujuan_nip') ?: '-');
                $pjTujuanJabatan = $request->input('jabatan_opd_tujuan') ?: ($request->input('pj_tujuan_jabatan') ?: 'Pejabat Penerima OPD');
                $alamatInstansi = $request->input('alamat_instansi') ?: '';
                $nomorSk = $request->input('nomor_sk_dasar') ?: $nomorBast;

                // Handle berkas lampiran BAST fisik jika diunggah
                $dokumenLampiranPath = null;
                if ($request->hasFile('dokumen_file')) {
                    $docFile = $request->file('dokumen_file');
                    $docFilename = 'BAST_KELUAR_' . time() . '_' . preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $docFile->getClientOriginalName());
                    $dokumenLampiranPath = $docFile->storeAs('dokumen_mutasi_eksternal', $docFilename, 'public');
                }

                // Ambil register yang dipilih
                $selectedRegisterIds = $request->input('selected_register_ids', []);
                if (is_string($selectedRegisterIds)) {
                    $selectedRegisterIds = json_decode($selectedRegisterIds, true) ?: [];
                }
                if (!is_array($selectedRegisterIds)) {
                    $selectedRegisterIds = [];
                }

                $targetRegisters = collect();
                if ($astap->registers()->exists()) {
                    if (!empty($selectedRegisterIds)) {
                        $targetRegisters = $astap->registers()->whereIn('id', $selectedRegisterIds)->get();
                    } else {
                        // Fallback jika tidak ada id spesifik yang dikirim: seluruh register aktif
                        $targetRegisters = $astap->registers()->where('status', '!=', 'Mutasi Keluar OPD')->get();
                        if ($targetRegisters->isEmpty()) {
                            $targetRegisters = $astap->registers;
                        }
                    }
                } else {
                    // Penanganan Graceful jika aset belum memiliki register fisik (Zero-Register Fallback)
                    $vol = max(1, (int) $astap->jumlah_volume);
                    $thn = $astap->tahun_perolehan ?: date('Y');
                    $startReg = \App\Models\AstapRegister::where('tahun_perolehan', $thn)->max('no_register_int') ?: 0;
                    $kode108Clean = preg_replace('/[^0-9]/', '', $astap->jenisAstap->sub_rincian_objek ?? $astap->kode_barang ?? '000000000000');
                    for ($i = 0; $i < $vol; $i++) {
                        $regNum = $startReg + $i + 1;
                        $noRegStr = str_pad($regNum, 7, '0', STR_PAD_LEFT);
                        $nibar = "1201351102000000280000{$thn}{$kode108Clean}{$noRegStr}";
                        $regCreated = \App\Models\AstapRegister::create([
                            'astap_id'        => $astap->id,
                            'tahun_perolehan' => $thn,
                            'no_register_int' => $regNum,
                            'no_register'     => $nibar,
                            'nibar'           => $nibar,
                            'qr_code_path'    => "/scan/{$nibar}",
                            'ruang_pemegang'  => null,
                            'kondisi'         => $astap->kondisi ?: 'Baik',
                            'status'          => 'Mutasi Keluar OPD',
                            'harga_satuan'    => (float) ($astap->total_realisasi / $vol),
                        ]);
                        $targetRegisters->push($regCreated);
                    }
                }

                $selectedVol = $targetRegisters->isNotEmpty() ? $targetRegisters->count() : max(1, (int) $astap->jumlah_volume);
                
                // Ambil pemetaan kondisi serah terima dari form
                $regKondisiMap = $request->input('register_kondisi_map', []);
                if (is_string($regKondisiMap)) {
                    $regKondisiMap = json_decode($regKondisiMap, true) ?: [];
                }

                // Hitung total nilai perolehan untuk unit yang terpilih
                $totalUnitVal = 0;
                $kondisiSerahUtama = 'Baik';
                if ($targetRegisters->isNotEmpty()) {
                    foreach ($targetRegisters as $idxReg => $tReg) {
                        $totalUnitVal += (float) ($tReg->harga_satuan ?: ($astap->total_realisasi / max(1, (int)$astap->jumlah_volume)));
                        if ($idxReg === 0) {
                            $kondisiSerahUtama = $regKondisiMap[$tReg->id] ?? ($tReg->kondisi ?: 'Baik');
                        }
                    }
                } else {
                    $totalUnitVal = (float) ($validated['nilai_reklas'] ?? $astap->total_realisasi);
                }
                if ($totalUnitVal <= 0) {
                    $totalUnitVal = (float) ($validated['nilai_reklas'] ?? $astap->total_realisasi);
                }

                $mutasiData = [
                    'astap_id'            => $astap->id,
                    'nomor_bamb'          => $nomorBast,
                    'tanggal_mutasi'      => $tglBast,
                    'jenis_mutasi'        => 'Transfer Antar-OPD',
                    'tipe'                => 'keluar',
                    'opd_asal'            => 'RSUD Dr. H. Koesnadi',
                    'opd_tujuan'          => $skpdTujuan,
                    'unit_id'             => $astap->unit_id,
                    'ruangan_tujuan'      => $astap->ruang_unit ?: ($astap->alamat_barang ?: 'RSUD Dr. H. Koesnadi'),
                    'pj_asal_nama'        => $pjAsal,
                    'pj_asal_nip'         => $pjAsalNip,
                    'pj_asal_jabatan'     => $pjAsalJabatan,
                    'pj_tujuan_nama'      => $pjTujuan,
                    'pj_tujuan_nip'       => $pjTujuanNip,
                    'pj_tujuan_jabatan'   => $pjTujuanJabatan,
                    'nomor_sk_dasar'      => $nomorSk,
                    'status'              => 'Disahkan (Selesai)',
                    'jumlah_volume'       => $selectedVol,
                    'satuan'              => $astap->satuan ?: 'Unit',
                    'nilai_perolehan'     => $totalUnitVal,
                    'kondisi'             => $kondisiSerahUtama,
                    'alasan_mutasi'       => $validated['alasan_reklas'] ?? 'Pemindahtanganan aset RSUD ke SKPD / OPD luar (Koreksi Baris 42 / Kolom 13).',
                    'alamat_instansi'     => $alamatInstansi,
                    'user_id'             => Auth::id(),
                    'is_deleted'          => 0,
                ];

                if ($dokumenLampiranPath) {
                    $mutasiData['dokumen_lampiran'] = $dokumenLampiranPath;
                }

                $mutasiKeluar = MutasiEksternal::where('astap_id', $astap->id)
                    ->where('tipe', 'keluar')
                    ->where('nomor_bamb', $nomorBast)
                    ->where('is_deleted', 0)
                    ->first();

                if (!$mutasiKeluar) {
                    $mutasiKeluar = MutasiEksternal::where('astap_id', $astap->id)
                        ->where('tipe', 'keluar')
                        ->where('is_deleted', 0)
                        ->first();
                }

                if ($mutasiKeluar) {
                    $mutasiKeluar->update($mutasiData);
                } else {
                    $mutasiKeluar = MutasiEksternal::create($mutasiData);
                }

                // Update status HANYA register yang dipilih menjadi 'Mutasi Keluar OPD' & link ke mutasi_eksternal_registers
                // Kosongkan ruang_pemegang menjadi null (nama OPD disimpan di mutasi_eksternals & catatan)
                if ($targetRegisters->isNotEmpty()) {
                    foreach ($targetRegisters as $reg) {
                        $kondisiSerah = $regKondisiMap[$reg->id] ?? ($reg->kondisi ?: 'Baik');

                        $reg->update([
                            'status'         => 'Mutasi Keluar OPD',
                            'unit_id'        => null, // Lepaskan dari unit ruangan KIR
                            'ruang_pemegang' => null,
                            'kondisi'        => $kondisiSerah,
                        ]);

                        if (\Illuminate\Support\Facades\Schema::hasTable('mutasi_eksternal_registers')) {
                            MutasiEksternalRegister::updateOrCreate(
                                [
                                    'mutasi_eksternal_id' => $mutasiKeluar->id,
                                    'astap_register_id'   => $reg->id,
                                ],
                                [
                                    'kondisi' => $kondisiSerah,
                                    'catatan' => 'Diserahkan ke ' . $skpdTujuan,
                                ]
                            );
                        }
                    }
                }

                // Cek sisa register aktif di RSUD setelah transaksi ini
                $remainingActiveCount = $astap->registers()
                    ->where('is_deleted', 0)
                    ->where(function($sr) {
                        $sr->whereNull('status')
                           ->orWhere('status', '!=', 'Mutasi Keluar OPD');
                    })
                    ->count();

                $isFullTransferFinal = ($remainingActiveCount === 0);
                if ($isFullTransferFinal) {
                    $astap->jenis_reklas = 'MUTASI_EKSTERNAL';
                    if (\Illuminate\Support\Facades\Schema::hasColumn('astaps', 'unit_id')) {
                        $astap->unit_id = null; // Lepaskan dari unit ruangan KIR
                    }
                    $astap->save();
                }
            }

            DB::commit();

            $astap->refresh();
            $responseTarget = ($isPartial && isset($targetAstap)) ? $targetAstap : $astap;

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $isPartial
                        ? "Reklasifikasi parsial berhasil! Sebanyak {$selectedCount} unit dialihkan ke " . ($targetKib ?: $tujuanKibFinal) . " dan sisa {$remainingVol} unit tetap di {$asalKibFinal}."
                        : 'Transaksi reklasifikasi aset berhasil dicatat!',
                    'data' => $reklas,
                    'is_partial' => $isPartial,
                    'split_volume' => $isPartial ? $selectedCount : null,
                    'remaining_volume' => $isPartial ? $remainingVol : null,
                    'target_astap_id' => $isPartial ? $targetAstap->id : null,
                    'original_astap_id' => $astap->id,
                    'tujuan_kib' => $targetKib ?: $tujuanKibFinal,
                    'asal_kib' => $asalKibFinal,
                    'is_kemitraan' => ($targetKib === 'KEMITRAAN'),
                    'cetak_bast_url' => ($targetKib === 'KEMITRAAN' && \Illuminate\Support\Facades\Route::has('astap.kemitraan.cetak_bast')) ? route('astap.kemitraan.cetak_bast', ['id' => $responseTarget->id]) : null,
                    'is_mutasi_keluar' => ($validated['jenis_reklas'] === 'MUTASI_EKSTERNAL'),
                    'is_full_transfer' => $isFullTransferFinal,
                    'cetak_bast_mutasi_url' => ($validated['jenis_reklas'] === 'MUTASI_EKSTERNAL' && isset($mutasiKeluar)) ? route('mutasi.eksternal.cetak', ['id' => $mutasiKeluar->id]) : null,
                    'mutasi_eksternal_url' => ($validated['jenis_reklas'] === 'MUTASI_EKSTERNAL') ? route('mutasi.eksternal') : null,
                    'mutasi_keluar' => isset($mutasiKeluar) ? [
                        'id' => $mutasiKeluar->id,
                        'nomor_bamb' => $mutasiKeluar->nomor_bamb,
                        'opd_tujuan' => $mutasiKeluar->opd_tujuan,
                        'jumlah_volume' => $mutasiKeluar->jumlah_volume,
                        'nilai_perolehan' => $mutasiKeluar->nilai_perolehan,
                    ] : null,
                    'astap' => [
                        'id' => $astap->id,
                        'total_realisasi' => 'Rp ' . number_format($astap->total_realisasi, 0, ',', '.'),
                        'total_realisasi_num' => (float) $astap->total_realisasi,
                        'jumlah_anggaran' => (float) $astap->jumlah_anggaran,
                        'harga_satuan' => (float) $astap->harga_satuan,
                        'jumlah_volume' => (int) $astap->jumlah_volume,
                        'is_extracomtable' => (bool) $astap->is_extracomtable,
                        'category' => $astap->category,
                        'is_reklas' => (bool) $astap->is_reklas,
                        'jenis_reklas' => $astap->jenis_reklas,
                        'spesifikasi_json' => $astap->spesifikasi_json,
                    ],
                ]);
            }

            return redirect()->back()->with('success', 'Transaksi reklasifikasi aset berhasil dicatat!');
        } catch (\Throwable $th) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('DEBUG REKLAS ERROR: ' . $th->getMessage(), [
                'exception' => $th,
                'sql' => ($th instanceof \Illuminate\Database\QueryException) ? $th->getSql() : null,
                'bindings' => ($th instanceof \Illuminate\Database\QueryException) ? $th->getBindings() : null,
            ]);
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mencatat reklasifikasi: ' . $th->getMessage(),
                ], 500);
            }
            return redirect()->back()->with('error', 'Gagal mencatat reklasifikasi: ' . $th->getMessage());
        }
    }

    /**
     * Menghapus transaksi reklasifikasi
     */
    public function destroy(Request $request, $id)
    {
        $reklas = AstapReklas::findOrFail($id);

        // Proteksi Integritas Tutup Buku (Periode Terkunci)
        if (\App\Models\PeriodeTutupBuku::isLocked($reklas->tahun, $reklas->triwulan)) {
            $lockInfo = \App\Models\PeriodeTutupBuku::getLockInfo($reklas->tahun, $reklas->triwulan);
            $barNo = $lockInfo?->nomor_bar_bpkad ?: '-';
            return response()->json([
                'success' => false,
                'message' => "Pembatalan reklasifikasi ditolak karena periode T.A. {$reklas->tahun} Triwulan {$reklas->triwulan} telah resmi DITUTUP BUKU (BAR BPKAD: {$barNo}). Buka kunci periode terlebih dahulu jika terdapat koreksi darurat.",
            ], 422);
        }

        $astapId  = $reklas->astap_id;
        $jenisReklas = $reklas->jenis_reklas;

        DB::beginTransaction();
        try {
            $reklas->softDelete($request->input('alasan', 'Dihapus dari modul Reklasifikasi Aset'));

            // Ambil sisa transaksi reklas untuk astap ini (yang masih aktif setelah dihapus)
            $remainingReklas = AstapReklas::active()->where('astap_id', $astapId)
                ->orderBy('created_at', 'asc')
                ->get();

            $updateData = [];

            if ($remainingReklas->isEmpty()) {
                // Tidak ada transaksi tersisa — reset semua flag reklas
                $updateData = [
                    'is_reklas'        => false,
                    'jenis_reklas'     => null,
                    'is_extracomtable' => false,
                ];
                if ($reklas->asal_kib && !in_array($reklas->asal_kib, ['KOREKSI', 'KOR_LAIN', 'KOR_EXTRACOM', 'KOR_HIBAH'])) {
                    $updateData['category'] = $reklas->asal_kib;
                }
                if (!empty($reklas->spesifikasi_lama) && is_array($reklas->spesifikasi_lama)) {
                    $updateData['spesifikasi_json'] = $reklas->spesifikasi_lama;
                }
            } else {
                // Masih ada transaksi lain — cek status ekstrakomptabel dari sisa log
                if ($jenisReklas === 'EKSTRAKOMPTABEL') {
                    // Dihapus: EKSTRAKOMPTABEL → cek apakah masih ada KAPITALISASI_INTRAKOM di log
                    // Jika tidak ada, aset kembali ke intrakomptabel (is_extracomtable = false)
                    $hasIntrakom = $remainingReklas->contains('jenis_reklas', 'KAPITALISASI_INTRAKOM');
                    if (! $hasIntrakom) {
                        $updateData['is_extracomtable'] = false;
                    }
                } elseif ($jenisReklas === 'KAPITALISASI_INTRAKOM') {
                    // Dihapus: KAPITALISASI_INTRAKOM → aset kembali ke status ekstrakomptabel
                    // jika masih ada EKSTRAKOMPTABEL di log
                    $hasExtracom = $remainingReklas->contains('jenis_reklas', 'EKSTRAKOMPTABEL');
                    $updateData['is_extracomtable'] = $hasExtracom;
                }

                $latestRemaining = $remainingReklas->last();
                if ($latestRemaining) {
                    $updateData['jenis_reklas'] = $latestRemaining->jenis_reklas;
                    if ($latestRemaining->tujuan_kib) {
                        $updateData['category'] = $latestRemaining->tujuan_kib;
                    }
                }
            }

            $astap = Astap::find($astapId);
            if ($astap) {
                $targetSpec = is_array($astap->spesifikasi_json) ? $astap->spesifikasi_json : (json_decode($astap->spesifikasi_json, true) ?? []);
                $splitParentId = $targetSpec['split_parent_id'] ?? null;
                if ($splitParentId) {
                    $parentAstap = Astap::find($splitParentId);
                    if ($parentAstap) {
                        $parentAstap->jumlah_volume = (int) $parentAstap->jumlah_volume + (int) $astap->jumlah_volume;
                        $parentAstap->total_realisasi = (float) $parentAstap->total_realisasi + (float) $astap->total_realisasi;
                        $parentAstap->jumlah_anggaran = (float) $parentAstap->jumlah_anggaran + (float) $astap->jumlah_anggaran;
                        if ($parentAstap->jumlah_volume > 0) {
                            $parentAstap->harga_satuan = $parentAstap->total_realisasi / $parentAstap->jumlah_volume;
                        }
                        // Kembalikan registers yang dipindahkan ke parent
                        $astap->registers()->update([
                            'astap_id' => $parentAstap->id,
                            'status' => 'Tersedia',
                            'ruang_pemegang' => null,
                        ]);
                        $parentAstap->save();
                    }
                    if ($astap->kemitraan) {
                        $astap->kemitraan->delete();
                    }
                    $astap->is_deleted = 1;
                    $astap->deleted_at = now();
                    $astap->save();
                } else {
                    if (! empty($updateData)) {
                        $astap->update($updateData);
                    }

                    // Sinkronkan kembali ke register fisik jika category dipulihkan
                    if (isset($updateData['category']) && $astap->registers()->exists()) {
                        $astap->registers()->update(['kelompok_kib' => $updateData['category']]);
                    }

                    // Pulihkan status kemitraan jika aset terkait dengan Akun 1.5.2 Kemitraan
                    if ($reklas->tujuan_kib === 'KEMITRAAN') {
                        $astap->sumber_dana = 'belanja_modal';
                        $astap->save();
                        $astap->registers()->update([
                            'status' => 'Tersedia',
                            'ruang_pemegang' => null,
                        ]);
                        if ($astap->kemitraan) {
                            $astap->kemitraan->delete();
                        }
                    } elseif ($astap->kemitraan) {
                        $statusPulih = 'Aktif';
                        if ($astap->kemitraan->tanggal_selesai && \Carbon\Carbon::parse($astap->kemitraan->tanggal_selesai)->isPast()) {
                            $statusPulih = 'Konsesi Berakhir';
                        }
                        $astap->kemitraan->update([
                            'status_konsesi' => $statusPulih
                        ]);
                    }
                }

                // Pulihkan status Mutasi Eksternal Keluar jika transaksi yang dibatalkan adalah MUTASI_EKSTERNAL
                if ($reklas->jenis_reklas === 'MUTASI_EKSTERNAL' || $jenisReklas === 'MUTASI_EKSTERNAL') {
                    $mutasiKeluar = MutasiEksternal::where('astap_id', $astapId)
                        ->where('tipe', 'keluar')
                        ->where('is_deleted', 0)
                        ->first();
                    if ($mutasiKeluar) {
                        $mutasiKeluar->update([
                            'is_deleted'    => 1,
                            'deleted_by'    => Auth::user()?->name ?? 'System',
                            'deleted_by_id' => Auth::id(),
                            'deleted_at'    => now(),
                        ]);
                    }

                    // Kembalikan status register NIBAR sesuai ketersediaan ruangan (defensive reversion)
                    if ($astap && $astap->registers()->exists()) {
                        $astap->registers()->where('status', 'Mutasi Keluar OPD')->with('unit')->get()->each(function($r) {
                            $hasUnit = !empty($r->unit_id) || !empty($r->unit);
                            $r->update([
                                'status'         => $hasUnit ? 'Tidak Tersedia' : 'Tersedia',
                                'ruang_pemegang' => $hasUnit ? ($r->unit?->nama ?? $r->ruang_pemegang) : null,
                            ]);
                        });
                    }
                }
            }

            DB::commit();

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Transaksi reklasifikasi berhasil dipindahkan ke Pusat Data Terhapus.',
                ]);
            }

            return redirect()->back()->with('success', 'Transaksi reklasifikasi berhasil dipindahkan ke Pusat Data Terhapus.');
        } catch (\Throwable $th) {
            DB::rollBack();
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus reklasifikasi: ' . $th->getMessage(),
                ], 500);
            }
            return redirect()->back()->with('error', 'Gagal menghapus reklasifikasi: ' . $th->getMessage());
        }
    }

    /**
     * Mencocokkan data ASTAP ke salah satu baris dari 42 baris master template PMDN 108
     */
    private function matchAstapToRow(Astap $astap, $templateRows)
    {
        $subRincian = $astap->jenisAstap->sub_rincian_objek ?? '';
        $subSubRincian = $astap->jenisAstap->sub_sub_rincian_objek ?? '';
        $jenisKode = $astap->jenisAstap->jenis ?? '';

        // 1. Cek kecocokan prefix spesifik kode 108
        if ($subRincian || $subSubRincian || $jenisKode) {
            foreach ($templateRows as $row) {
                $p = $row->kode_prefix;
                if (!$p || str_starts_with($p, 'KOR_')) continue;

                if (str_starts_with($subRincian, $p) ||
                    str_starts_with($subSubRincian, $p) ||
                    str_starts_with($jenisKode, $p) ||
                    str_starts_with($p, substr($subRincian, 0, 8))
                ) {
                    return $row;
                }
            }
        }

        // 2. Fallback berdasarkan kategori KIB aset atau nama barang
        $category = strtoupper(trim((string) ($astap->category ?? '')));
        if ($category === 'EXTRACOM' && !empty($astap->asal_kib)) {
            $category = strtoupper(trim((string) $astap->asal_kib));
        }

        // KIB A
        if ($category === 'KIB A') {
            return $templateRows->firstWhere('kode_prefix', '1.3.1.01');
        }

        // KIB B (Peralatan & Mesin)
        if ($category === 'KIB B') {
            $nama = strtoupper(trim((string) $astap->nama_barang));
            if (str_contains($nama, 'KOMPUTER') || str_contains($nama, 'LAPTOP') || str_contains($nama, 'PC')) {
                return $templateRows->firstWhere('kode_prefix', '1.3.2.10') ?? $templateRows->firstWhere('kelompok_kib', 'KIB B');
            }
            if (str_contains($nama, 'KEDOKTERAN') || str_contains($nama, 'MEDIS') || str_contains($nama, 'STETOSKOP') || str_contains($nama, 'TENSIMETER')) {
                return $templateRows->firstWhere('kode_prefix', '1.3.2.07') ?? $templateRows->firstWhere('kelompok_kib', 'KIB B');
            }
            if (str_contains($nama, 'KANTOR') || str_contains($nama, 'MEJA') || str_contains($nama, 'KURSI') || str_contains($nama, 'LEMARI')) {
                return $templateRows->firstWhere('kode_prefix', '1.3.2.05') ?? $templateRows->firstWhere('kelompok_kib', 'KIB B');
            }
            if (str_contains($nama, 'ANGKUTAN') || str_contains($nama, 'MOBIL') || str_contains($nama, 'MOTOR') || str_contains($nama, 'AMBULANCE')) {
                return $templateRows->firstWhere('kode_prefix', '1.3.2.02') ?? $templateRows->firstWhere('kelompok_kib', 'KIB B');
            }
            return $templateRows->firstWhere('kode_prefix', '1.3.2.01') ?? $templateRows->firstWhere('kelompok_kib', 'KIB B');
        }

        // KIB C
        if ($category === 'KIB C') {
            return $templateRows->firstWhere('kode_prefix', '1.3.3.01');
        }

        // KIB D
        if ($category === 'KIB D') {
            return $templateRows->firstWhere('kode_prefix', '1.3.4.01');
        }

        // KIB E
        if ($category === 'KIB E') {
            return $templateRows->firstWhere('kode_prefix', '1.3.5.01') ?? $templateRows->firstWhere('kelompok_kib', 'KIB E');
        }

        // KIB F
        if ($category === 'KIB F') {
            return $templateRows->firstWhere('kode_prefix', '1.3.6.01');
        }

        // ATB
        if ($category === 'ATB') {
            return $templateRows->firstWhere('kode_prefix', '1.5.3');
        }

        // Kemitraan Pihak Ketiga
        if ($category === 'KEMITRAAN') {
            return $templateRows->firstWhere('kode_prefix', '1.5.2') ?? $templateRows->firstWhere('kode_prefix', '1.5.1');
        }

        // Aset Lain-Lain (Rusak Berat / Menunggu Penghapusan)
        if ($category === 'ASET LAIN') {
            return $templateRows->firstWhere('kode_prefix', '1.5.4');
        }

        // Default jika KIB B atau terindikasi barang modal
        return $templateRows->firstWhere('kode_prefix', '1.3.2.05') ?? $templateRows->firstWhere('kelompok_kib', 'KIB B');
    }

    /**
     * Resolusi ID baris asal transaksi reklasifikasi (Mutasi Kurang -)
     */
    private function resolveAsalRowId(AstapReklas $reklas, $templateRows): ?int
    {
        if ($reklas->jenis_reklasifikasi_asal_id) {
            return (int) $reklas->jenis_reklasifikasi_asal_id;
        }

        if ($reklas->jenis_reklas === 'KAPITALISASI_INTRAKOM') {
            return (int) ($templateRows->firstWhere('kode_prefix', 'KOR_EXTRACOM')?->id);
        }

        if ($reklas->jenis_reklas === 'HIBAH_MASUK') {
            return (int) ($templateRows->firstWhere('kode_prefix', 'KOR_HIBAH')?->id);
        }

        if ($reklas->jenis_reklas === 'KOREKSI_LAIN' && $reklas->keterangan && str_contains($reklas->keterangan, 'penambahan nilai')) {
            return (int) ($templateRows->firstWhere('kode_prefix', 'KOR_LAIN')?->id);
        }

        if ($reklas->jenis_reklas === 'KDP_TO_DEFINITIF') {
            return (int) ($templateRows->firstWhere('kode_prefix', '1.3.6.01')?->id);
        }

        // Resolusi prioritas dari asal_kib
        if ($reklas->asal_kib) {
            if ($reklas->asal_kib === 'KEMITRAAN') {
                return (int) ($templateRows->firstWhere('kode_prefix', '1.5.2')?->id);
            }
            if ($reklas->asal_kib === 'ATB') {
                return (int) ($templateRows->firstWhere('kode_prefix', '1.5.3')?->id);
            }
            if (in_array($reklas->asal_kib, ['ASET LAIN', 'ASET LAIN-LAIN', 'ASET LAINNYA'])) {
                return (int) ($templateRows->firstWhere('kode_prefix', '1.5.4')?->id);
            }
            $row = $templateRows->firstWhere('kelompok_kib', $reklas->asal_kib);
            if ($row) return (int) $row->id;
        }

        // Resolusi dari asal_kode jika ada
        if ($reklas->asal_kode) {
            $matched = $templateRows->first(function($r) use ($reklas) {
                return $r->kode_prefix && (str_starts_with($reklas->asal_kode, $r->kode_prefix) || str_starts_with($r->kode_prefix, $reklas->asal_kode));
            });
            if ($matched) return (int) $matched->id;
        }

        // Cek dari ASTAP terkait
        if ($reklas->astap) {
            $row = $this->matchAstapToRow($reklas->astap, $templateRows);
            if ($row) return (int) $row->id;
        }

        return null;
    }

    /**
     * Resolusi ID baris tujuan transaksi reklasifikasi (Mutasi Tambah +)
     */
    private function resolveTujuanRowId(AstapReklas $reklas, $templateRows): ?int
    {
        if ($reklas->jenis_reklasifikasi_tujuan_id) {
            return (int) $reklas->jenis_reklasifikasi_tujuan_id;
        }

        if ($reklas->jenis_reklas === 'EKSTRAKOMPTABEL' || $reklas->tujuan_kib === 'EKSTRAKOMPTABEL') {
            return (int) ($templateRows->firstWhere('kode_prefix', 'KOR_EXTRACOM')?->id);
        }

        if ($reklas->jenis_reklas === 'HIBAH_KELUAR' || $reklas->tujuan_kib === 'HIBAH') {
            return (int) ($templateRows->firstWhere('kode_prefix', 'KOR_HIBAH')?->id);
        }

        if ($reklas->jenis_reklas === 'MUTASI_EKSTERNAL' || $reklas->tujuan_kib === 'MUTASI_OPD') {
            return (int) ($templateRows->firstWhere('kode_prefix', 'KOR_LAIN')?->id);
        }

        if ($reklas->jenis_reklas === 'KOREKSI_LAIN') {
            if (!($reklas->keterangan && str_contains($reklas->keterangan, 'penambahan nilai'))) {
                return (int) ($templateRows->firstWhere('kode_prefix', 'KOR_LAIN')?->id);
            }
        }

        if ($reklas->jenis_reklas === 'HIBAH_MASUK') {
            $targetKib = $reklas->tujuan_kib ?: ($reklas->astap?->category ?: 'KIB B');
            $row = $templateRows->firstWhere('kelompok_kib', $targetKib);
            if ($row) return (int) $row->id;
        }

        if ($reklas->jenis_reklas === 'DEFINITIF_TO_KDP') {
            return (int) ($templateRows->firstWhere('kode_prefix', '1.3.6.01')?->id);
        }

        if ($reklas->jenis_reklas === 'KDP_TO_DEFINITIF') {
            $targetKib = $reklas->tujuan_kib ?: 'KIB C';
            if ($targetKib === 'KIB D') return (int) ($templateRows->firstWhere('kode_prefix', '1.3.4.01')?->id);
            if ($targetKib === 'KIB B') return (int) ($templateRows->firstWhere('kode_prefix', '1.3.2.01')?->id);
            return (int) ($templateRows->firstWhere('kode_prefix', '1.3.3.01')?->id);
        }

        // Resolusi dari tujuan_kode jika ada
        if ($reklas->tujuan_kode) {
            $matched = $templateRows->first(function($r) use ($reklas) {
                return $r->kode_prefix && (str_starts_with($reklas->tujuan_kode, $r->kode_prefix) || str_starts_with($r->kode_prefix, $reklas->tujuan_kode));
            });
            if ($matched) return (int) $matched->id;
        }

        if ($reklas->tujuan_kib) {
            if ($reklas->tujuan_kib === 'KEMITRAAN') {
                return (int) ($templateRows->firstWhere('kode_prefix', '1.5.2')?->id);
            }
            if ($reklas->tujuan_kib === 'ATB') {
                return (int) ($templateRows->firstWhere('kode_prefix', '1.5.3')?->id);
            }
            if (in_array($reklas->tujuan_kib, ['ASET LAIN', 'ASET LAIN-LAIN', 'ASET LAINNYA'])) {
                return (int) ($templateRows->firstWhere('kode_prefix', '1.5.4')?->id);
            }
            $row = $templateRows->firstWhere('kelompok_kib', $reklas->tujuan_kib);
            if ($row) return (int) $row->id;
        }

        return null;
    }

    /**
     * Menentukan ID baris template matriks untuk Saldo Awal Belanja Modal suatu aset.
     * Jika aset pernah direklasifikasi (misal Koreksi Rekening atau Ekstrakomptabel),
     * Saldo Awal ditempatkan pada rekening/baris ASAL pengadaan semula agar neraca balance.
     */
    private function resolveAstapSaldoAwalRowId(Astap $astap, $templateRows, $allReklasByAstap): ?int
    {
        $history = $allReklasByAstap[$astap->id] ?? null;
        if ($history && $history->isNotEmpty()) {
            $firstReklas = $history->first();

            // Aset yang masuk dari Kapitalisasi Intrakomptabel atau Hibah Masuk
            // bukan belanja modal aset tetap tahun berjalan => tidak memiliki saldo awal
            if (in_array($firstReklas->jenis_reklas, ['KAPITALISASI_INTRAKOM', 'HIBAH_MASUK'])) {
                return null;
            }

            // Aset yang direklasifikasi/pindah kamar: tempatkan saldo awal di rekening asal pengadaan
            $asalId = $firstReklas->jenis_reklasifikasi_asal_id;
            if ($asalId) {
                return (int) $asalId;
            }

            if ($firstReklas->asal_kib) {
                $row = $templateRows->firstWhere('kelompok_kib', $firstReklas->asal_kib);
                if ($row) return (int) $row->id;
            }
        }

        // Jika aset berasal dari pengadaan belanja modal tahun berjalan tapi ditandai extracom tanpa log reklas
        // Saldo awal tetap dicatat di akun belanjanya semula agar mutasi kurang penyeimbang tidak membuat minus
        $row = $this->matchAstapToRow($astap, $templateRows);
        return $row ? (int) $row->id : null;
    }
}
