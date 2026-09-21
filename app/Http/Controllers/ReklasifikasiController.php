<?php

namespace App\Http\Controllers;

use App\Models\Astap;
use App\Models\AstapReklas;
use App\Models\JenisAstap;
use App\Models\JenisReklasifikasi;
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
            ->with(['jenisAstap']);

        // Filter triwulan berdasarkan sp2d_tanggal atau bast_dokumen_tanggal atau created_at jika ada
        if ($selectedTw !== 'all') {
            $tw = (int) $selectedTw;
            $startMonth = ($tw - 1) * 3 + 1;
            $endMonth = $tw * 3;
            $startDate = sprintf('%04d-%02d-01', $selectedTahun, $startMonth);
            $endDate = date('Y-m-t', strtotime(sprintf('%04d-%02d-01', $selectedTahun, $endMonth)));

            $astapQuery->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('sp2d_tanggal', [$startDate, $endDate])
                  ->orWhere(function($sub) use ($startDate, $endDate) {
                      $sub->whereNull('sp2d_tanggal')
                          ->whereBetween('bast_dokumen_tanggal', [$startDate, $endDate]);
                  })
                  ->orWhere(function($sub2) use ($startDate, $endDate) {
                      $sub2->whereNull('sp2d_tanggal')
                           ->whereNull('bast_dokumen_tanggal')
                           ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
                  });
            });
        }

        $astaps = $astapQuery->get();

        // 2. Ambil seluruh transaksi mutasi reklasifikasi untuk tahun & tw terpilih
        $reklasQuery = AstapReklas::where('tahun', $selectedTahun);
        if ($selectedTw !== 'all') {
            $reklasQuery->where('triwulan', (int) $selectedTw);
        }
        $reklasMutasis = $reklasQuery->get();

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

        foreach ($templateRows as $row) {
            $prefix = $row->kode_prefix;
            $saldoAwal = 0;

            // Hitung Saldo Awal Belanja Modal dari ASTAP berdasarkan Prefix PMDN 108
            if ($prefix && !str_starts_with($prefix, 'KOR_')) {
                foreach ($astaps as $astap) {
                    $subRincian = $astap->jenisAstap->sub_rincian_objek ?? '';
                    $subSubRincian = $astap->jenisAstap->sub_sub_rincian_objek ?? '';
                    $jenisKode = $astap->jenisAstap->jenis ?? '';

                    // Cek kesesuaian prefix
                    if (str_starts_with($subRincian, $prefix) || 
                        str_starts_with($subSubRincian, $prefix) ||
                        ($prefix === '1.5.1' && str_starts_with($jenisKode, '1.5.1')) ||
                        ($prefix === '1.5.3' && str_starts_with($jenisKode, '1.5.3')) ||
                        ($prefix === '1.5.4' && str_starts_with($jenisKode, '1.5.4'))
                    ) {
                        $saldoAwal += (float) ($astap->total_realisasi ?: ($astap->jumlah_anggaran ?: 0));
                    }
                }
            }

            // Hitung Mutasi Tambah (Baris ini sebagai Tujuan)
            $mutasiTambah = (float) $reklasMutasis->where('jenis_reklasifikasi_tujuan_id', $row->id)->sum('nilai_reklas');

            // Hitung Mutasi Kurang (Baris ini sebagai Asal)
            $mutasiKurang = (float) $reklasMutasis->where('jenis_reklasifikasi_asal_id', $row->id)->sum('nilai_reklas');

            // Khusus baris Koreksi Ekstrakomptabel: Jika ada aset is_extracomtable, masukkan ke mutasi kurang koreksi
            if ($prefix === 'KOR_EXTRACOM') {
                $extracomTotal = (float) $astaps->where('is_extracomtable', true)->sum('total_realisasi');
                $mutasiKurang += $extracomTotal;
            }

            $saldoAkhir = $saldoAwal + $mutasiTambah - $mutasiKurang;

            $kib = $row->kelompok_kib;
            if (isset($subtotals[$kib])) {
                $subtotals[$kib]['awal'] += $saldoAwal;
                $subtotals[$kib]['tambah'] += $mutasiTambah;
                $subtotals[$kib]['kurang'] += $mutasiKurang;
                $subtotals[$kib]['akhir'] += $saldoAkhir;
            }

            $grandTotal['awal'] += $saldoAwal;
            $grandTotal['tambah'] += $mutasiTambah;
            $grandTotal['kurang'] += $mutasiKurang;
            $grandTotal['akhir'] += $saldoAkhir;

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
        $logReklas = AstapReklas::where('tahun', $selectedTahun)
            ->when($selectedTw !== 'all', fn($q) => $q->where('triwulan', (int) $selectedTw))
            ->with(['astap.jenisAstap', 'jenisReklasAsal', 'jenisReklasTujuan', 'user'])
            ->orderBy('tanggal_reklas', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        // 5. Ambil kandidat aset untuk modal tambah reklasifikasi
        $kandidatAstaps = Astap::where('is_deleted', 0)
            ->where('tahun_perolehan', $selectedTahun)
            ->select('id', 'nama_barang', 'total_realisasi', 'tahun_perolehan', 'jenis_astap_id', 'is_reklas')
            ->with('jenisAstap')
            ->orderBy('nama_barang', 'asc')
            ->get();

        return view('pages.master_reklasifikasi', [
            'matriks' => $matriks,
            'subtotals' => $subtotals,
            'grandTotal' => $grandTotal,
            'logReklas' => $logReklas,
            'tahunList' => $tahunList,
            'selectedTahun' => $selectedTahun,
            'selectedTw' => $selectedTw,
            'templateRows' => $templateRows,
            'kandidatAstaps' => $kandidatAstaps,
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
            'nilai_reklas' => 'required|numeric|min:0',
            'tanggal_reklas' => 'required|date',
            'triwulan' => 'required|integer|between:1,4',
            'tahun' => 'required|integer|min:2000|max:2099',
            'nomor_ba_reklas' => 'nullable|string|max:150',
            'keterangan' => 'nullable|string',
            'reklas_items' => 'nullable|array',
        ]);

        // Jika jenis reklasifikasi adalah Ekstrakomptabel atau Kapitalisasi ke Intrakomptabel
        $isExtracom = ($validated['jenis_reklas'] === 'EKSTRAKOMPTABEL');
        $isIntracom = ($validated['jenis_reklas'] === 'KAPITALISASI_INTRAKOM');

        if (($isExtracom || $isIntracom) && !empty($request->reklas_items) && is_array($request->reklas_items)) {
            // Validasi tiap rincian barang: batas Rp 300.000 dan > Rp 0 sebelum transaksi DB
            foreach ($request->reklas_items as $itemIdx => $rItem) {
                $harga = (float) ($rItem['harga_satuan'] ?? 0);
                $nama = trim((string) ($rItem['nama_barang'] ?? 'Barang #' . ($itemIdx + 1)));

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

        DB::beginTransaction();
        try {
            $astap = Astap::findOrFail($validated['astap_id']);

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

            $astap->is_reklas = true;
            $astap->jenis_reklas = $validated['jenis_reklas'];
            if ($validated['jenis_reklas'] === 'EKSTRAKOMPTABEL') {
                $astap->is_extracomtable = true;
            } elseif ($validated['jenis_reklas'] === 'KAPITALISASI_INTRAKOM') {
                $astap->is_extracomtable = false;
                $targetKib = $validated['tujuan_kib'] ?: 'KIB B';
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
            }
            $astap->save();

            // Set user dan simpan audit log reklasifikasi
            $validated['user_id'] = Auth::id();
            unset($validated['reklas_items']);
            $reklas = AstapReklas::create($validated);

            DB::commit();

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Transaksi reklasifikasi aset berhasil dicatat!',
                    'data' => $reklas,
                    'astap' => [
                        'id' => $astap->id,
                        'total_realisasi' => 'Rp ' . number_format($astap->total_realisasi, 0, ',', '.'),
                        'total_realisasi_num' => (float) $astap->total_realisasi,
                        'jumlah_volume' => (int) $astap->jumlah_volume,
                        'harga_satuan' => (float) $astap->harga_satuan,
                        'is_extracomtable' => (bool) $astap->is_extracomtable,
                        'category' => $astap->is_extracomtable ? 'EXTRACOM' : $astap->category,
                        'is_reklas' => (bool) $astap->is_reklas,
                        'jenis_reklas' => $astap->jenis_reklas,
                        'spesifikasi_json' => $astap->spesifikasi_json,
                    ],
                ]);
            }

            return redirect()->back()->with('success', 'Transaksi reklasifikasi aset berhasil dicatat!');
        } catch (\Throwable $th) {
            DB::rollBack();
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
        $astapId = $reklas->astap_id;

        DB::beginTransaction();
        try {
            $reklas->delete();

            // Cek apakah astap masih memiliki transaksi reklas lain
            $remaining = AstapReklas::where('astap_id', $astapId)->count();
            if ($remaining === 0) {
                Astap::where('id', $astapId)->update([
                    'is_reklas' => false,
                    'jenis_reklas' => null,
                ]);
            }

            DB::commit();

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Transaksi reklasifikasi berhasil dibatalkan/dihapus.',
                ]);
            }

            return redirect()->back()->with('success', 'Transaksi reklasifikasi berhasil dihapus.');
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
}
