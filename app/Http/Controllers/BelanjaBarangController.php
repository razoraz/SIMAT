<?php

namespace App\Http\Controllers;

use App\Models\Astap;
use App\Models\AstapRegister;
use App\Models\AstapBelanjaBarang;
use App\Models\AstapBelanjaModal;
use App\Models\JenisAstap;
use App\Models\Unit;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class BelanjaBarangController extends Controller
{
    /**
     * Tampilkan Halaman Utama Master Belanja Barang (Akun 5.1.02 / Perbekalan Ruangan)
     */
    public function index(Request $request)
    {
        Carbon::setLocale('id');

        // Sinkronisasi otomatis: Jika ada Astap belanja_barang yang belum terhubung ke faktur belanja_barangs
        $unlinkedBelanja = Astap::whereIn('sumber_dana', ['belanja_barang', 'belanja_rekening'])
            ->where('is_deleted', 0)
            ->whereNull('belanja_barang_id')
            ->get();

        foreach ($unlinkedBelanja as $astap) {
            $spec = $astap->spesifikasi_json ?? [];
            $header = AstapBelanjaBarang::create([
                'astap_id'        => $astap->id,
                'toko_penyedia'   => $spec['toko_penyedia'] ?? ($spec['rekening_penyedia'] ?? 'Pusat Perbekalan RSUD'),
                'nomor_faktur'    => $spec['nomor_faktur'] ?? ($spec['rekening_nomor_faktur'] ?? '-'),
                'tanggal_faktur'  => $spec['tanggal_faktur'] ?? ($spec['rekening_tanggal_faktur'] ?? now()),
                'total_pembelian' => (float) $astap->total_realisasi,
                'status_alokasi'  => !empty($astap->unit_id) ? 'ruangan' : 'gudang',
                'keterangan'      => $astap->keterangan_tambahan ?? ($spec['keterangan'] ?? null),
            ]);
            $astap->update(['belanja_barang_id' => $header->id]);
        }

        $filterTahun = $request->query('tahun', 'all');
        $filterTw    = $request->query('triwulan', 'all');
        $search      = trim($request->query('search', ''));

        // Query utama data Faktur Belanja Barang (hanya yang aktif)
        $query = AstapBelanjaBarang::with([
                'astaps.jenisAstap',
                'astaps.registers.unit',
                'astaps.unit',
                'user'
            ])
            ->where('is_deleted', 0)
            ->orderBy('tanggal_faktur', 'desc')
            ->orderBy('id', 'desc');

        if ($filterTahun !== 'all') {
            $query->where(function ($q) use ($filterTahun) {
                $q->whereYear('tanggal_faktur', $filterTahun)
                  ->orWhereHas('astaps', fn($sq) => $sq->where('tahun_perolehan', $filterTahun));
            });
        }

        if ($filterTw !== 'all') {
            $query->whereHas('astaps', function ($q) use ($filterTw) {
                $q->where('triwulan', $filterTw);
            });
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('toko_penyedia', 'like', "%{$search}%")
                  ->orWhere('nomor_faktur', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%")
                  ->orWhereHas('astaps', function ($sq) use ($search) {
                      $sq->where('nama_barang', 'like', "%{$search}%")
                        ->orWhereHas('unit', function ($uq) use ($search) {
                            $uq->where('nama', 'like', "%{$search}%");
                        })
                        ->orWhereHas('registers', function ($rq) use ($search) {
                            $rq->where('nibar', 'like', "%{$search}%")
                              ->orWhere('ruang_pemegang', 'like', "%{$search}%");
                        });
                  });
            });
        }

        $belanjaBarangRecords = $query->get();

        // Hitung Statistik KPI (hanya yang aktif)
        $allBelanja = AstapBelanjaBarang::where('is_deleted', 0)
            ->with('astaps')
            ->get();

        $totalNilaiBelanja = (float) $allBelanja->sum('total_pembelian');
        $totalVolumeUnit   = (int) $allBelanja->sum(fn($b) => $b->astaps->sum('jumlah_volume'));
        $totalFaktur       = $allBelanja->pluck('nomor_faktur')->filter()->unique()->count();
        $totalTokoUnik     = $allBelanja->pluck('toko_penyedia')->filter()->unique()->count();

        // Daftar Unit & Jenis 108 untuk modal atau filter
        $dbUnits = Unit::orderBy('nama')->get();
        $dbMaster108 = JenisAstap::getNested108();

        return view('pages.belanja_barang.index', compact(
            'belanjaBarangRecords',
            'totalNilaiBelanja',
            'totalVolumeUnit',
            'totalFaktur',
            'totalTokoUnik',
            'dbUnits',
            'dbMaster108',
            'filterTahun',
            'filterTw',
            'search'
        ));
    }

    /**
     * Tampilkan Form Input Belanja Barang (Akun 5.1.02)
     */
    public function create()
    {
        $rawMaster108 = JenisAstap::getNested108();
        $dbMaster108 = array_values(array_filter($rawMaster108, function ($j) {
            $kode = (string) ($j['kode'] ?? '');
            $nama = strtolower($j['nama'] ?? '');
            if (str_starts_with($kode, '1.5.2') || str_starts_with($kode, '1.4') || str_contains($nama, 'kemitraan')) return false;
            if (str_starts_with($kode, '1.5.4') || str_contains($nama, 'aset lain-lain')) return false;
            return true;
        }));

        $dbUnits = Unit::orderBy('nama')->get();

        $dbPenyedias = AstapBelanjaModal::whereNotNull('penyedia_nama')
            ->where('penyedia_nama', '!=', '')
            ->distinct()
            ->pluck('penyedia_nama')
            ->values();

        $dbPejabats = Astap::whereNotNull('ppk_nama')
            ->where('ppk_nama', '!=', '')
            ->distinct()
            ->pluck('ppk_nama')
            ->values();

        // Riwayat pintar toko penyedia untuk combobox autocomplete
        $dbTokoPenyedias = AstapBelanjaBarang::whereNotNull('toko_penyedia')
            ->where('toko_penyedia', '!=', '')
            ->distinct()
            ->pluck('toko_penyedia')
            ->merge(
                Astap::whereIn('sumber_dana', ['belanja_barang', 'belanja_rekening'])
                    ->get()
                    ->map(fn($a) => $a->spesifikasi_json['toko_penyedia'] ?? ($a->spesifikasi_json['rekening_penyedia'] ?? null))
                    ->filter()
            )
            ->merge([
                'CV. Sahabat Medika Bondowoso',
                'PT. Kimia Farma Trading & Distribution',
                'Toko Buku & ATK Berkah Jaya',
                'CV. Sumber Logistik Mandiri',
                'Pusat Perbekalan Farmasi RSUD',
            ])
            ->map(fn($v) => trim($v))
            ->filter()
            ->unique()
            ->values();

        return view('pages.belanja_barang.form', compact(
            'dbMaster108',
            'dbUnits',
            'dbPenyedias',
            'dbPejabats',
            'dbTokoPenyedias'
        ));
    }

    /**
     * Simpan Data Belanja Barang Multi-Item ke SIMAT-RK
     */
    public function store(Request $request)
    {
        // 1. Ekstrak data item (dukungan array langsung maupun JSON-string dari FormData)
        $rawItems = $request->input('items');
        if (empty($rawItems) && $request->filled('items_json')) {
            $rawItems = json_decode($request->input('items_json'), true);
        }
        if (!is_array($rawItems)) {
            $rawItems = [];
        }

        // Backward compatibility jika masih input single-item format lama
        if (empty($rawItems) && $request->filled('nama_barang') && $request->filled('jenis_astap_id')) {
            $rawItems = [[
                'nama_barang'      => $request->input('nama_barang'),
                'jenis_astap_id'   => $request->input('jenis_astap_id'),
                'jumlah_volume'    => $request->input('jumlah_volume', 1),
                'satuan'           => $request->input('satuan', 'Unit'),
                'harga_satuan'     => $request->input('harga_satuan', 0) ?: ($request->input('total_realisasi', 0) / max(1, (int)$request->input('jumlah_volume', 1))),
                'mode_penempatan'  => $request->input('unit_id') ? 'ruangan' : 'gudang',
                'unit_id'          => $request->input('unit_id'),
                'kondisi'          => $request->input('kondisi', 'Baik'),
                'alamat_barang'    => $request->input('alamat_barang', 'RSUD Dr. H. Koesnandi'),
                'spesifikasi_json' => $request->input('spesifikasi_json', []),
            ]];
        }

        $request->merge(['items' => $rawItems]);

        // 2. Validasi Dokumen Faktur Header & Items
        $rules = [
            'tahun_perolehan'         => 'required|integer|min:1990|max:2100',
            'triwulan'                => 'required|string|in:TW I,TW II,TW III,TW IV',
            'rekening_penyedia'       => 'required|string|max:500',
            'rekening_nomor_faktur'   => 'required|string|max:255',
            'rekening_tanggal_faktur' => 'required',
            'rekening_keterangan'     => 'nullable|string|max:2000',
            'status_alokasi_global'   => 'nullable|string|in:gudang,ruangan,custom',
            'dokumen_faktur'          => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'dokumen_kuitansi'        => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'items'                   => 'required|array|min:1',
            'items.*.nama_barang'     => 'required|string|max:500',
            'items.*.jenis_astap_id'  => 'required|integer|exists:jenis_astaps,id',
            'items.*.jumlah_volume'   => 'required|integer|min:1',
            'items.*.satuan'          => 'required|string|max:100',
            'items.*.harga_satuan'    => 'required|numeric|min:0',
        ];

        $validated = $request->validate($rules, [
            'dokumen_faktur.required' => 'Wajib mengunggah Nota / Faktur Pembelian Asli Toko Rekanan (PDF/Gambar max 5MB).',
            'dokumen_faktur.mimes'    => 'Format Nota / Faktur harus berupa PDF, JPG, JPEG, atau PNG.',
            'dokumen_faktur.max'      => 'Ukuran file Nota / Faktur maksimal 5 Megabytes (MB).',
            'items.required'          => 'Minimal harus ada 1 item barang yang dibeli dalam faktur.',
            'items.min'               => 'Minimal harus ada 1 item barang yang dibeli dalam faktur.',
            'items.*.nama_barang.required' => 'Nama barang pada rincian item wajib diisi.',
            'items.*.jenis_astap_id.required' => 'Kode Klasifikasi 108 pada rincian item wajib dipilih.',
        ]);

        $tahun = (int) $validated['tahun_perolehan'];
        $triwulan = $validated['triwulan'];
        $tokoPenyedia = trim($validated['rekening_penyedia']);
        $nomorFaktur = trim($validated['rekening_nomor_faktur']);
        $tanggalFaktur = $validated['rekening_tanggal_faktur'];
        $keteranganFaktur = $validated['rekening_keterangan'] ?? null;

        // 3. Upload File Dokumen (Disimpan di storage/app/public/belanja_barang/{tahun}/)
        $dokumenFakturPath = null;
        if ($request->hasFile('dokumen_faktur') && $request->file('dokumen_faktur')->isValid()) {
            $dokumenFakturPath = $request->file('dokumen_faktur')->store(
                'belanja_barang/' . $tahun . '/faktur',
                'public'
            );
        }

        $dokumenKuitansiPath = null;
        if ($request->hasFile('dokumen_kuitansi') && $request->file('dokumen_kuitansi')->isValid()) {
            $dokumenKuitansiPath = $request->file('dokumen_kuitansi')->store(
                'belanja_barang/' . $tahun . '/kuitansi',
                'public'
            );
        }

        // 4. Hitung Total Pembelian Seluruh Item
        $itemsList = $validated['items'];
        $totalPembelian = 0;
        foreach ($itemsList as $it) {
            $vol = max(1, (int) ($it['jumlah_volume'] ?? 1));
            $hrg = (float) ($it['harga_satuan'] ?? 0);
            $totalPembelian += ($vol * $hrg);
        }

        // Tentukan status alokasi faktur
        $hasGudang = false;
        $hasRuangan = false;
        foreach ($itemsList as $it) {
            $mode = $it['mode_penempatan'] ?? 'gudang';
            if ($mode === 'ruangan' && !empty($it['unit_id'])) {
                $hasRuangan = true;
            } else {
                $hasGudang = true;
            }
        }
        $statusAlokasiFaktur = ($hasGudang && $hasRuangan) ? 'sebagian' : ($hasRuangan ? 'ruangan' : 'gudang');

        // 5. Eksekusi Atomic Database Transaction
        $fakturHeader = DB::transaction(function () use (
            $itemsList,
            $tokoPenyedia,
            $nomorFaktur,
            $tanggalFaktur,
            $totalPembelian,
            $keteranganFaktur,
            $dokumenFakturPath,
            $dokumenKuitansiPath,
            $statusAlokasiFaktur,
            $tahun,
            $triwulan,
            $request
        ) {
            // A. Simpan Dokumen Faktur Header
            $faktur = AstapBelanjaBarang::create([
                'toko_penyedia'    => $tokoPenyedia,
                'nomor_faktur'     => $nomorFaktur,
                'tanggal_faktur'   => $tanggalFaktur,
                'total_pembelian'  => $totalPembelian,
                'dokumen_faktur'   => $dokumenFakturPath,
                'dokumen_kuitansi' => $dokumenKuitansiPath,
                'status_alokasi'   => $statusAlokasiFaktur,
                'keterangan'       => $keteranganFaktur,
                'user_id'          => Auth::id(),
                'is_deleted'       => 0,
            ]);

            $firstAstapId = null;

            // B. Simpan Setiap Item Barang sebagai entitas Astap & Register NIBAR
            foreach ($itemsList as $idx => $it) {
                $namaBarang   = trim($it['nama_barang']);
                $jenisAstapId = (int) $it['jenis_astap_id'];
                $vol          = max(1, (int) $it['jumlah_volume']);
                $satuan       = trim($it['satuan'] ?? 'Unit');
                $hargaSatuan  = (float) $it['harga_satuan'];
                $subtotal     = $vol * $hargaSatuan;

                $modePenempatan = $it['mode_penempatan'] ?? 'gudang';
                $unitId = ($modePenempatan === 'ruangan' && !empty($it['unit_id'])) ? (int) $it['unit_id'] : null;
                $unitModel = $unitId ? Unit::find($unitId) : null;

                $kondisi = !empty($it['kondisi']) ? $it['kondisi'] : 'Baik';
                $alamatBarang = !empty($it['alamat_barang']) ? $it['alamat_barang'] : ($unitModel ? $unitModel->nama : 'RSUD Dr. H. Koesnandi');

                // Siapkan JSON spesifikasi teknis
                $spesifikasi = [
                    'sumber_dana'        => 'belanja_barang',
                    'toko_penyedia'      => $tokoPenyedia,
                    'nomor_faktur'       => $nomorFaktur,
                    'tanggal_faktur'     => $tanggalFaktur,
                    'mode_penempatan'    => $modePenempatan,
                    'kondisi'            => $kondisi,
                    'dokumen_faktur'     => $dokumenFakturPath,
                    'dokumen_kuitansi'   => $dokumenKuitansiPath,
                ];

                if (!empty($it['spesifikasi_json']) && is_array($it['spesifikasi_json'])) {
                    $spesifikasi = array_merge($spesifikasi, $it['spesifikasi_json']);
                }

                // Buat record Astap
                $astap = Astap::create([
                    'belanja_barang_id'         => $faktur->id,
                    'nama_barang'               => $namaBarang,
                    'jenis_astap_id'            => $jenisAstapId,
                    'tahun_perolehan'           => $tahun,
                    'jumlah_volume'             => $vol,
                    'satuan'                    => $satuan,
                    'harga_satuan'              => $hargaSatuan,
                    'jumlah_anggaran'           => $subtotal,
                    'total_realisasi'           => $subtotal,
                    'biaya_administrasi_proyek' => 0,
                    'triwulan'                  => $triwulan,
                    'sumber_dana'               => 'belanja_barang',
                    'keterangan_tambahan'       => $keteranganFaktur,
                    'unit_id'                   => $unitId,
                    'alamat_barang'             => $alamatBarang,
                    'user_id'                   => Auth::id(),
                    'is_extracomtable'          => true,
                    'is_reklas'                 => false,
                    'is_deleted'                => 0,
                    'spesifikasi_json'          => $spesifikasi,
                ]);

                if (!$firstAstapId) {
                    $firstAstapId = $astap->id;
                }

                // C. Buat Register NIBAR untuk setiap unit fisik barang
                $ja = JenisAstap::find($jenisAstapId);
                $kode108Raw = $ja ? ($ja->sub_sub_rincian_objek ?: $ja->jenis) : '1.3.2.00.00.00';
                $kode108Clean = str_replace('.', '', $kode108Raw);

                $maxRegInt = AstapRegister::where('tahun_perolehan', $tahun)
                    ->whereHas('astap', fn($sq) => $sq->where('jenis_astap_id', $jenisAstapId))
                    ->max('no_register_int') ?? 0;

                $runningRegNum = (int) $maxRegInt;

                // Tentukan lokasi & status register sesuai Opsi A:
                // - Jika di Gudang: unit_id = null, ruang_pemegang = 'Gudang Perbekalan', status = 'Tersedia' (siap didistribusikan via BAST Distribusi)
                // - Jika di Ruangan: unit_id = $unitId, ruang_pemegang = $unitModel->nama, status = 'Tidak Tersedia' (muncul di KIR Ruangan)
                $isDirectToRoom = ($modePenempatan === 'ruangan' && $unitId && $unitModel);
                $ruangPemegang = $isDirectToRoom ? $unitModel->nama : 'Gudang Perbekalan';
                $registerStatus = $isDirectToRoom ? 'Tidak Tersedia' : 'Tersedia';

                for ($i = 0; $i < $vol; $i++) {
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
                        'astap_id'        => $astap->id,
                        'unit_id'         => $isDirectToRoom ? $unitId : null,
                        'tahun_perolehan' => $tahun,
                        'no_register_int' => $runningRegNum,
                        'no_register'     => $nibar,
                        'nibar'           => $nibar,
                        'nama_barang'     => $namaBarang,
                        'qr_code_path'    => $qrPath,
                        'ruang_pemegang'  => $ruangPemegang,
                        'kondisi'         => $kondisi,
                        'status'          => $registerStatus,
                        'is_deleted'      => 0,
                    ]);
                }
            }

            // Tautkan astap_id pada faktur ke item pertama untuk backward-compatibility query lama
            if ($firstAstapId) {
                $faktur->update(['astap_id' => $firstAstapId]);
            }

            return $faktur;
        });

        // 6. Kirim Notifikasi Sistem ke Admin & Super Admin
        try {
            $totalItemsCount = count($itemsList);
            NotificationService::sendToAdminAndMaster(
                "Faktur Belanja Barang: {$nomorFaktur}",
                "{$totalItemsCount} Macam Barang dari {$tokoPenyedia} • Total " . 'Rp ' . number_format($totalPembelian, 0, ',', '.'),
                'belanja_barang',
                route('master.belanja_barang')
            );
        } catch (\Throwable $e) {
            Log::warning("Gagal kirim notif belanja barang: " . $e->getMessage());
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success'  => true,
                'message'  => 'Faktur Belanja Barang "' . $nomorFaktur . '" (' . count($itemsList) . ' macam barang) berhasil disimpan ke database SIMAT-RK!',
                'redirect' => route('master.belanja_barang')
            ]);
        }

        return redirect()->route('master.belanja_barang')
            ->with('success', 'Faktur Belanja Barang "' . $nomorFaktur . '" (' . count($itemsList) . ' macam barang) berhasil ditambahkan.');
    }

    /**
     * Hapus / Pindahkan Catatan Belanja Barang ke Pusat Pemulihan Data (Soft Delete)
     */
    public function destroy(Request $request, $id)
    {
        $belanja = AstapBelanjaBarang::with('astaps.registers')->findOrFail($id);

        DB::transaction(function () use ($belanja) {
            $user = Auth::user();
            $deleterName = $user ? ($user->name . ' (' . ucfirst($user->role ?? 'user') . ')') : 'Administrator';
            $deleterId = $user?->id;
            $now = now();

            // 1. Soft delete faktur header
            $belanja->softDelete();

            // 2. Soft delete seluruh item Astap dan register NIBAR terkait
            $astaps = $belanja->astaps;
            if ($astaps->isEmpty() && $belanja->astap) {
                $astaps = collect([$belanja->astap]);
            }

            foreach ($astaps as $astap) {
                AstapRegister::where('astap_id', $astap->id)->update([
                    'is_deleted'    => 1,
                    'deleted_by'    => $deleterName,
                    'deleted_by_id' => $deleterId,
                    'deleted_at'    => $now,
                ]);

                $astap->update([
                    'is_deleted'    => 1,
                    'deleted_by'    => $deleterName,
                    'deleted_by_id' => $deleterId,
                    'deleted_at'    => $now,
                ]);
            }
        });

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Faktur Belanja Barang beserta seluruh item aset terkait berhasil dipindahkan ke Pusat Pemulihan Data (Recycle Bin).'
            ]);
        }

        return redirect()->route('master.belanja_barang')
            ->with('success', 'Faktur Belanja Barang beserta seluruh item aset terkait berhasil dipindahkan ke Pusat Pemulihan Data (Recycle Bin).');
    }
}
