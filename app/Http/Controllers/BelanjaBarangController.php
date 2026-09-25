<?php

namespace App\Http\Controllers;

use App\Models\Astap;
use App\Models\AstapRegister;
use App\Models\AstapBelanjaBarang;
use App\Models\AstapBelanjaModal;
use App\Models\JenisAstap;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BelanjaBarangController extends Controller
{
    /**
     * Tampilkan Halaman Utama Master Belanja Barang (Akun 5.1.02 / Perbekalan Ruangan)
     */
    public function index(Request $request)
    {
        Carbon::setLocale('id');

        // Sinkronisasi otomatis: Jika ada Astap belanja_barang yang belum tercatat di astap_belanja_barangs
        $unlinkedBelanja = Astap::whereIn('sumber_dana', ['belanja_barang', 'belanja_rekening'])
            ->where('is_deleted', 0)
            ->whereDoesntHave('belanjaBarang')
            ->get();

        foreach ($unlinkedBelanja as $astap) {
            $spec = $astap->spesifikasi_json ?? [];
            AstapBelanjaBarang::create([
                'astap_id'        => $astap->id,
                'toko_penyedia'   => $spec['toko_penyedia'] ?? ($spec['rekening_penyedia'] ?? 'Pusat Perbekalan RSUD'),
                'nomor_faktur'    => $spec['nomor_faktur'] ?? ($spec['rekening_nomor_faktur'] ?? '-'),
                'tanggal_faktur'  => $spec['tanggal_faktur'] ?? ($spec['rekening_tanggal_faktur'] ?? now()),
                'total_pembelian' => (float) $astap->total_realisasi,
                'keterangan'      => $astap->keterangan_tambahan ?? ($spec['keterangan'] ?? null),
            ]);
        }

        $filterTahun = $request->query('tahun', 'all');
        $filterTw    = $request->query('triwulan', 'all');
        $search      = trim($request->query('search', ''));

        // Query utama data Belanja Barang
        $query = AstapBelanjaBarang::with(['astap.jenisAstap', 'astap.registers.unit', 'astap.unit'])
            ->whereHas('astap', function ($q) {
                $q->where('is_deleted', 0);
            })
            ->orderBy('tanggal_faktur', 'desc')
            ->orderBy('id', 'desc');

        if ($filterTahun !== 'all') {
            $query->whereHas('astap', function ($q) use ($filterTahun) {
                $q->where('tahun_perolehan', $filterTahun);
            });
        }

        if ($filterTw !== 'all') {
            $query->whereHas('astap', function ($q) use ($filterTw) {
                $q->where('triwulan', $filterTw);
            });
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('toko_penyedia', 'like', "%{$search}%")
                  ->orWhere('nomor_faktur', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%")
                  ->orWhereHas('astap', function ($sq) use ($search) {
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

        // Hitung Statistik KPI
        $allBelanja = AstapBelanjaBarang::whereHas('astap', fn($q) => $q->where('is_deleted', 0))
            ->with('astap')
            ->get();

        $totalNilaiBelanja = $allBelanja->sum('total_pembelian');
        $totalVolumeUnit   = $allBelanja->sum(fn($b) => max(1, (int) ($b->astap?->jumlah_volume ?? 1)));
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
     * Simpan Data Belanja Barang ke SIMAT-RK
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_barang'             => 'required|string|max:500',
            'jenis_astap_id'          => 'required|integer|exists:jenis_astaps,id',
            'tahun_perolehan'         => 'required|integer|min:1990|max:2100',
            'jumlah_volume'           => 'required|integer|min:1',
            'satuan'                  => 'required|string|max:100',
            'total_realisasi'         => 'required|numeric|min:0',
            'triwulan'                => 'required|string|in:TW I,TW II,TW III,TW IV',
            'rekening_penyedia'       => 'required|string|max:500',
            'rekening_nomor_faktur'   => 'required|string|max:255',
            'rekening_tanggal_faktur' => 'required',
            'rekening_keterangan'     => 'nullable|string|max:2000',
            'unit_id'                 => 'nullable|integer|exists:units,id',
            'alamat_barang'           => 'nullable|string|max:1000',
            'kondisi'                 => 'nullable|string|max:50',
        ]);

        $totalRealisasi = (float) $data['total_realisasi'];
        $totalVolume    = max(1, (int) $data['jumlah_volume']);
        $hargaSatuan    = $totalRealisasi / $totalVolume;
        $tahun          = (int) $data['tahun_perolehan'];
        $kondisiItem    = $data['kondisi'] ?: 'Baik';

        $astapPayload = [
            'nama_barang'               => $data['nama_barang'],
            'jenis_astap_id'            => $data['jenis_astap_id'],
            'tahun_perolehan'           => $tahun,
            'jumlah_volume'             => $totalVolume,
            'satuan'                    => $data['satuan'],
            'harga_satuan'              => $hargaSatuan,
            'jumlah_anggaran'           => $totalRealisasi,
            'total_realisasi'           => $totalRealisasi,
            'biaya_administrasi_proyek' => 0,
            'triwulan'                  => $data['triwulan'],
            'sumber_dana'               => 'belanja_barang',
            'keterangan_tambahan'       => $data['rekening_keterangan'] ?? null,
            'alamat_barang'             => $data['alamat_barang'] ?: 'RSUD Dr. H. Koesnandi',
            'user_id'                   => Auth::id(),
            'is_extracomtable'          => true,
            'is_reklas'                 => false,
            'is_deleted'                => 0,
            'spesifikasi_json'          => [
                'sumber_dana'     => 'belanja_barang',
                'toko_penyedia'   => $data['rekening_penyedia'],
                'nomor_faktur'    => $data['rekening_nomor_faktur'],
                'tanggal_faktur'  => $data['rekening_tanggal_faktur'],
                'kondisi'         => $kondisiItem,
                'keterangan'      => $data['rekening_keterangan'] ?? null,
            ],
        ];

        if ($request->has('spesifikasi_json') && is_array($request->input('spesifikasi_json'))) {
            $astapPayload['spesifikasi_json'] = array_merge($astapPayload['spesifikasi_json'], $request->input('spesifikasi_json'));
        }

        $repeaterKeys = ['tanah_items', 'mesin_items', 'gedung_items', 'jaringan_items', 'lainnya_items', 'atb_items', 'kdp_items', 'merk', 'type', 'ukuran', 'bahan', 'no_pabrik', 'no_rangka', 'no_mesin', 'no_polisi', 'sertifikat_nomor'];
        foreach ($repeaterKeys as $rk) {
            if ($request->has($rk) && !is_null($request->input($rk))) {
                $astapPayload['spesifikasi_json'][$rk] = $request->input($rk);
            }
        }

        if ($request->filled('ppk_nama')) {
            $astapPayload['ppk_nama'] = $request->input('ppk_nama');
        }
        if ($request->filled('ppk_nip')) {
            $astapPayload['ppk_nip'] = $request->input('ppk_nip');
        }

        $item = DB::transaction(function () use ($astapPayload, $data, $totalVolume, $totalRealisasi, $tahun, $kondisiItem) {
            $item = Astap::create($astapPayload);

            // Catat ke extension table astap_belanja_barangs
            AstapBelanjaBarang::create([
                'astap_id'        => $item->id,
                'toko_penyedia'   => $data['rekening_penyedia'],
                'nomor_faktur'    => $data['rekening_nomor_faktur'],
                'tanggal_faktur'  => $data['rekening_tanggal_faktur'],
                'total_pembelian' => $totalRealisasi,
                'keterangan'      => $data['rekening_keterangan'] ?? null,
            ]);

            // Buat AstapRegister untuk setiap unit barang (Ekstrakomptabel)
            $ja = JenisAstap::find($data['jenis_astap_id']);
            $kode108Raw = $ja ? ($ja->sub_sub_rincian_objek ?: $ja->jenis) : '1.3.2.00.00.00';
            $kode108Clean = str_replace('.', '', $kode108Raw);

            $maxRegInt = AstapRegister::where('tahun_perolehan', $tahun)
                ->whereHas('astap', fn($sq) => $sq->where('jenis_astap_id', $data['jenis_astap_id']))
                ->max('no_register_int') ?? 0;

            $runningRegNum = (int) $maxRegInt;
            $unitModel = !empty($data['unit_id']) ? Unit::find($data['unit_id']) : null;
            $ruangPemegang = $unitModel ? $unitModel->nama : ($data['alamat_barang'] ?: 'RSUD Dr. H. Koesnandi');

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
                    'ruang_pemegang'  => $ruangPemegang,
                    'kondisi'         => $kondisiItem,
                    'status'          => 'Aktif',
                    'is_deleted'      => 0,
                ]);
            }

            return $item;
        });

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success'  => true,
                'message'  => 'Data Belanja Barang "' . $item->nama_barang . '" berhasil disimpan ke database SIMAT-RK!',
                'redirect' => route('master.belanja_barang')
            ]);
        }

        return redirect()->route('master.belanja_barang')
            ->with('success', 'Data Belanja Barang "' . $item->nama_barang . '" berhasil ditambahkan.');
    }

    /**
     * Hapus / Batalkan Catatan Belanja Barang
     */
    public function destroy(Request $request, $id)
    {
        $belanja = AstapBelanjaBarang::findOrFail($id);

        DB::transaction(function () use ($belanja) {
            $astap = $belanja->astap;
            if ($astap) {
                // Soft delete registers
                AstapRegister::where('astap_id', $astap->id)->update(['is_deleted' => 1]);
                // Soft delete astap
                $astap->update(['is_deleted' => 1]);
            }
            $belanja->delete();
        });

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Data Belanja Barang berhasil dihapus dari SIMAT-RK.'
            ]);
        }

        return redirect()->route('master.belanja_barang')
            ->with('success', 'Data Belanja Barang berhasil dihapus.');
    }
}
