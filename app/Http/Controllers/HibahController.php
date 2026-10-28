<?php

namespace App\Http\Controllers;

use App\Models\Astap;
use App\Models\AstapRegister;
use App\Models\AstapHibah;
use App\Models\JenisAstap;
use App\Models\Unit;
use App\Models\RekeningBelanja;
use App\Models\AstapBelanjaModal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class HibahController extends Controller
{
    /**
     * Tampilkan Halaman Utama Master Data Hibah Aset (Masuk & Keluar)
     */
    public function index(Request $request)
    {
        Carbon::setLocale('id');

        $filterTipe = $request->query('tipe', 'all'); // 'all', 'masuk', 'keluar'
        $filterTahun = $request->query('tahun', 'all');
        $filterTw = $request->query('triwulan', 'all');
        $search = trim($request->query('search', ''));

        // Query utama riwayat hibah (Hanya data aktif / belum dihapus)
        $query = AstapHibah::active()
            ->with(['astap.jenisAstap', 'astap.registers.unit', 'astap.unit', 'register.unit', 'user'])
            ->orderBy('tanggal_bast', 'desc')
            ->orderBy('id', 'desc');

        if ($filterTipe !== 'all') {
            $query->where('tipe_hibah', $filterTipe);
        }

        if ($filterTahun !== 'all') {
            $query->where('tahun', $filterTahun);
        }

        if ($filterTw !== 'all') {
            $query->where('triwulan', $filterTw);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('pihak_hibah', 'like', "%{$search}%")
                  ->orWhere('nomor_bast', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%")
                  ->orWhereHas('astap', function ($sq) use ($search) {
                      $sq->where('nama_barang', 'like', "%{$search}%")
                        ->orWhere('kode_108', 'like', "%{$search}%");
                  });
            });
        }

        $hibahRecords = $query->get();

        // Hitung Statistik KPI dari data aktif via aggregate query / collection
        $allHibahs = AstapHibah::active()->with('astap')->get();
        $totalMasukUnit = $allHibahs->where('tipe_hibah', 'masuk')->sum('jumlah_volume');
        $totalMasukNominal = $allHibahs->where('tipe_hibah', 'masuk')->sum('nilai_aset');
        $totalKeluarUnit = $allHibahs->where('tipe_hibah', 'keluar')->sum('jumlah_volume');
        $totalKeluarNominal = $allHibahs->where('tipe_hibah', 'keluar')->sum('nilai_aset');

        // Daftar Aset Aktif yang dapat dihibahkan keluar (berstatus 'Tersedia' atau 'Aktif', belum dihibahkan/dihapus)
        $activeAstaps = Astap::where('is_deleted', 0)
            ->whereHas('registers', function ($q) {
                $q->where('is_deleted', 0)
                  ->whereIn('status', ['Tersedia', 'Aktif']);
            })
            ->with(['registers' => function ($q) {
                $q->where('is_deleted', 0)
                  ->whereIn('status', ['Tersedia', 'Aktif']);
            }, 'jenisAstap'])
            ->orderBy('nama_barang', 'asc')
            ->get()
            ->map(function ($a) {
                $availRegisters = $a->registers;
                $availCount = $availRegisters->count();
                $originalVol = max(1, (int) $a->jumlah_volume);

                return [
                    'id' => $a->id,
                    'nama_barang' => $a->nama_barang,
                    'kode_barang' => $a->kode_108 ?: ($a->jenisAstap ? $a->jenisAstap->sub_sub_rincian_objek : '-'),
                    'category' => $a->category,
                    'tahun_perolehan' => $a->tahun_perolehan,
                    'jumlah_volume' => $availCount,
                    'satuan' => $a->satuan ?: 'Unit',
                    'harga_satuan' => (float) ($a->harga_satuan ?: ($a->total_realisasi / $originalVol)),
                    'total_realisasi' => (float) $a->total_realisasi,
                    'registers' => $availRegisters->map(function ($r) {
                        return [
                            'id' => $r->id,
                            'nibar' => $r->nibar ?: $r->no_register,
                            'ruang' => $r->ruang_pemegang ?: '-',
                            'kondisi' => $r->kondisi ?: 'Baik',
                            'status' => $r->status,
                        ];
                    })->values()->toArray(),
                ];
            });

        // Ambil data Astap Hibah lengkap untuk kebutuhan Engine Ekspor Excel Multi-Sheet (Client-Side)
        $hibahAstaps = Astap::with(['registers.unit', 'hibahs', 'jenisAstap', 'unit'])
            ->where('is_deleted', 0)
            ->where(function ($q) {
                $q->where('sumber_dana', 'hibah')
                  ->orWhereHas('hibahs', fn($sq) => $sq->where('is_deleted', 0));
            })
            ->get();

        // Daftar Unit & Jenis 108 untuk filter / modal
        $dbUnits = Unit::orderBy('nama')->get();
        $dbMaster108 = JenisAstap::getNested108();

        // Daftar Tahun Unik untuk Filter
        $availableYears = AstapHibah::active()->select('tahun')
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->pluck('tahun')
            ->toArray();

        if (!in_array(date('Y'), $availableYears)) {
            array_unshift($availableYears, (int) date('Y'));
        }

        return view('pages.hibah.index', compact(
            'hibahRecords',
            'hibahAstaps',
            'dbUnits',
            'dbMaster108',
            'totalMasukUnit',
            'totalMasukNominal',
            'totalKeluarUnit',
            'totalKeluarNominal',
            'activeAstaps',
            'availableYears',
            'filterTipe',
            'filterTahun',
            'filterTw',
            'search'
        ));
    }

    /**
     * Tampilkan Form Input Pencatatan Hibah Aset Baru (3 Langkah)
     */
    public function create()
    {
        $dbMaster108 = JenisAstap::getNested108();
        $dbUnits = Unit::orderBy('nama')->get();
        $dbRekeningBelanjas = RekeningBelanja::orderBy('kode_rek')->get();
        $dbPenyedias = self::getDistinctPenyedias();
        $dbPejabats = self::getDistinctPejabats();
        $dbPemberiHibahs = self::getDistinctPemberiHibahs();

        return view('pages.hibah.form', compact(
            'dbMaster108', 'dbUnits', 'dbRekeningBelanjas', 'dbPenyedias', 'dbPejabats', 'dbPemberiHibahs'
        ));
    }

    /**
     * Simpan Data Aset Hibah Baru ke Inventaris ASTAP & Riwayat Hibah
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_barang'        => 'required|string|max:500',
            'jenis_astap_id'     => 'nullable|integer|exists:jenis_astaps,id',
            'jenis_aset_kode'    => 'nullable|string|max:50',
            'jenis_aset_nama'    => 'nullable|string|max:255',
            'sub_rincian_kode'   => 'nullable|string|max:50',
            'sub_rincian_nama'   => 'nullable|string|max:255',
            'tahun_perolehan'    => 'required|integer|min:1990|max:2100',
            'jumlah_volume'      => 'required|integer|min:1',
            'satuan'             => 'required|string|max:100',
            'total_realisasi'    => 'required|numeric|min:0',
            'triwulan'           => 'required|string|in:TW I,TW II,TW III,TW IV',
            'hibah_pemberi'      => 'required|string|max:500',
            'hibah_nomor_bast'   => 'required|string|max:255',
            'hibah_tanggal_bast' => 'required',
            'hibah_keterangan'   => 'nullable|string|max:2000',
            'unit_id'            => 'nullable|integer|exists:units,id',
            'alamat_barang'      => 'nullable|string|max:1000',
            'kondisi'            => 'nullable|string|max:50',
            'dokumen_bast'       => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240',
        ]);

        // Resolve jenis_astap_id
        $jenisAstapId = !empty($data['jenis_astap_id']) ? (int) $data['jenis_astap_id'] : null;
        if (!$jenisAstapId && !empty($data['jenis_aset_kode'])) {
            $ja = JenisAstap::where('sub_sub_rincian_objek', $data['jenis_aset_kode'])
                ->orWhere('jenis', $data['jenis_aset_kode'])
                ->first();
            if ($ja) $jenisAstapId = $ja->id;
        }
        if (!$jenisAstapId) {
            return response()->json([
                'success' => false,
                'message' => 'Klasifikasi Jenis Aset (Kode 108) tidak valid atau tidak ditemukan di database.',
            ], 422);
        }
        $data['jenis_astap_id'] = $jenisAstapId;

        $totalRealisasi = (float) $data['total_realisasi'];
        $totalVolume    = max(1, (int) $data['jumlah_volume']);
        $hargaSatuan    = $totalRealisasi / $totalVolume;
        $tahun          = (int) $data['tahun_perolehan'];
        $kondisiItem    = $data['kondisi'] ?: 'Baik';

        // Upload file dokumen BAST jika ada
        $dokumenBastPath = null;
        if ($request->hasFile('dokumen_bast') && $request->file('dokumen_bast')->isValid()) {
            $dokumenBastPath = $request->file('dokumen_bast')->store(
                'hibah/bast/' . $tahun,
                'public'
            );
        }

        // Parse spesifikasi_json
        $spesifikasiExtra = [];
        if ($request->filled('spesifikasi_json')) {
            $decoded = json_decode($request->input('spesifikasi_json'), true);
            if (is_array($decoded)) {
                $spesifikasiExtra = $decoded;
            }
        }

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
            'sumber_dana'               => 'hibah',
            'hibah_pemberi'             => $data['hibah_pemberi'],
            'hibah_nomor_bast'          => $data['hibah_nomor_bast'],
            'hibah_tanggal_bast'        => $data['hibah_tanggal_bast'],
            'hibah_keterangan'          => $data['hibah_keterangan'] ?? null,
            'bast_dokumen_nomor'        => $data['hibah_nomor_bast'],
            'bast_dokumen_tanggal'      => $data['hibah_tanggal_bast'],
            'keterangan_tambahan'       => $data['hibah_keterangan'] ?? null,
            'unit_id'                   => $data['unit_id'] ?? null,
            'rekening_belanja_id'       => $request->input('rekening_belanja_id') ?: null,
            'alamat_barang'             => $data['alamat_barang'] ?: 'RSUD Dr. H. Koesnandi',
            'user_id'                   => auth()->id(),
            'is_extracomtable'          => filter_var($request->input('is_extracomtable', false), FILTER_VALIDATE_BOOLEAN),
            'is_reklas'                 => false,
            'is_deleted'                => 0,
            'spesifikasi_json'          => array_merge([
                'sumber_dana'        => 'hibah',
                'pemberi'            => $data['hibah_pemberi'],
                'nomor_bast'         => $data['hibah_nomor_bast'],
                'tanggal_bast'       => $data['hibah_tanggal_bast'],
                'kondisi'            => $kondisiItem,
                'keterangan'         => $data['hibah_keterangan'] ?? null,
                'jenis_aset_kode'    => $data['jenis_aset_kode'] ?? null,
                'jenis_aset_nama'    => $data['jenis_aset_nama'] ?? null,
                'sub_rincian_kode'   => $data['sub_rincian_kode'] ?? null,
                'sub_rincian_nama'   => $data['sub_rincian_nama'] ?? null,
                'tipe_hibah'         => $request->input('tipe_hibah', 'masuk'),
                'hibah_pimpinan'     => $request->input('hibah_pimpinan'),
                'hibah_alamat_pemberi' => $request->input('hibah_alamat_pemberi'),
                'dokumen_bast_path'  => $dokumenBastPath,
            ], $spesifikasiExtra),
        ];

        // Tangkap spesifikasi teknis tambahan
        $repeaterKeys = ['tanah_items', 'mesin_items', 'gedung_items', 'jaringan_items', 'lainnya_items', 'atb_items', 'kdp_items', 'merk', 'type', 'ukuran', 'bahan', 'no_pabrik', 'no_rangka', 'no_mesin', 'no_polisi', 'sertifikat_nomor'];
        foreach ($repeaterKeys as $rk) {
            if ($request->has($rk) && !is_null($request->input($rk))) {
                $astapPayload['spesifikasi_json'][$rk] = $request->input($rk);
            }
        }

        // Sinkronisasi spesifikasi spesifik Tanah jika KIB A
        if ($request->has('tanah_items') && is_array($request->input('tanah_items')) && count($request->input('tanah_items')) > 0) {
            $tItems = $request->input('tanah_items');
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

        if ($request->filled('ppk_nama')) {
            $astapPayload['ppk_nama'] = $request->input('ppk_nama');
            $astapPayload['spesifikasi_json']['ppk_nama'] = $request->input('ppk_nama');
        }
        if ($request->filled('ppk_nip')) {
            $astapPayload['ppk_nip'] = $request->input('ppk_nip');
            $astapPayload['spesifikasi_json']['ppk_nip'] = $request->input('ppk_nip');
        }

        $item = DB::transaction(function () use ($astapPayload, $data, $totalVolume, $totalRealisasi, $tahun, $kondisiItem) {
            $item = Astap::create($astapPayload);

            // Catat ke riwayat AstapHibah (tipe: masuk)
            AstapHibah::create([
                'tipe_hibah'        => 'masuk',
                'astap_id'          => $item->id,
                'astap_register_id' => null,
                'pihak_hibah'       => $data['hibah_pemberi'],
                'nomor_bast'        => $data['hibah_nomor_bast'],
                'tanggal_bast'      => $data['hibah_tanggal_bast'],
                'jumlah_volume'     => $totalVolume,
                'satuan'            => $data['satuan'],
                'nilai_aset'        => $totalRealisasi,
                'tahun'             => $tahun,
                'triwulan'          => $data['triwulan'],
                'keterangan'        => $data['hibah_keterangan'] ?? null,
                'user_id'           => auth()->id(),
            ]);

            // Buat AstapRegister untuk setiap unit barang hibah (Format resmi 45 digit SIMAT-RK)
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
                'success' => true,
                'message' => 'Data Hibah "' . $item->nama_barang . '" berhasil disimpan ke database SIMAT-RK!',
                'redirect' => route('master.hibah')
            ]);
        }

        return redirect()->route('master.hibah')
            ->with('success', 'Data Hibah "' . $item->nama_barang . '" berhasil ditambahkan.');
    }

    /**
     * Tampilkan Form Edit / Ubah Data Aset Hibah
     */
    public function edit($id)
    {
        $astap = Astap::with(['registers.unit', 'jenisAstap', 'hibahs', 'unit', 'rekeningBelanja'])->findOrFail($id);
        $dbMaster108 = JenisAstap::getNested108();
        $dbUnits = Unit::orderBy('nama')->get();
        $dbRekeningBelanjas = RekeningBelanja::orderBy('kode_rek')->get();
        $dbPenyedias = self::getDistinctPenyedias();
        $dbPejabats = self::getDistinctPejabats();
        $dbPemberiHibahs = self::getDistinctPemberiHibahs();

        return view('pages.hibah.form', compact(
            'astap',
            'dbMaster108',
            'dbUnits',
            'dbRekeningBelanjas',
            'dbPenyedias',
            'dbPejabats',
            'dbPemberiHibahs'
        ));
    }

    /**
     * Update Perubahan Data Aset Hibah
     */
    public function update(Request $request, $id)
    {
        $astap = Astap::with(['registers', 'hibahs', 'jenisAstap'])->findOrFail($id);

        // Normalisasi nilai format Rupiah jika dikirimkan sebagai string berformat
        $data = $request->all();
        if (isset($data['total_realisasi']) && is_string($data['total_realisasi'])) {
            $cleanVal = preg_replace('/[^\d]/', '', $data['total_realisasi']);
            $data['total_realisasi'] = $cleanVal !== '' ? (float) $cleanVal : 0;
            $request->merge(['total_realisasi' => $data['total_realisasi']]);
        }

        // Normalisasi tanggal_bast jika format dd/mm/yyyy
        if ($request->has('hibah_tanggal_bast') && is_string($request->hibah_tanggal_bast)) {
            $rawDate = trim($request->hibah_tanggal_bast);
            if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $rawDate, $m)) {
                $isoDate = sprintf('%04d-%02d-%02d', $m[3], $m[2], $m[1]);
                $request->merge(['hibah_tanggal_bast' => $isoDate]);
            }
        }

        $validated = $request->validate([
            'hibah_pemberi'      => 'required|string|max:500',
            'hibah_nomor_bast'   => 'required|string|max:255',
            'hibah_tanggal_bast' => 'required|date',
            'tahun_perolehan'    => 'required|integer|min:1990|max:2100',
            'triwulan'           => 'required|string|in:TW I,TW II,TW III,TW IV',
            'nama_barang'        => 'required|string|max:500',
            'jumlah_volume'      => 'required|integer|min:1',
            'satuan'             => 'required|string|max:100',
            'total_realisasi'    => 'required|numeric|min:0',
            'unit_id'            => 'nullable|integer|exists:units,id',
            'alamat_barang'      => 'nullable|string|max:1000',
            'ppk_nama'           => 'nullable|string|max:255',
            'ppk_nip'            => 'nullable|string|max:50',
            'hibah_keterangan'   => 'nullable|string|max:2000',
            'is_extracomtable'   => 'nullable|in:0,1,true,false',
            'dokumen_bast'       => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240',
        ]);

        // Tangani file upload jika diunggah ulang
        $dokumenPath = $astap->spesifikasi_json['dokumen_path'] ?? ($astap->spesifikasi_json['dokumen_bast_path'] ?? null);
        if ($request->hasFile('dokumen_bast') && $request->file('dokumen_bast')->isValid()) {
            $file = $request->file('dokumen_bast');
            $filename = 'BAST_HIBAH_' . time() . '_' . \Illuminate\Support\Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
            $dokumenPath = $file->storeAs('dokumen_hibah', $filename, 'public');
        }

        // Decode spesifikasi_json
        $specJson = $astap->spesifikasi_json ?? [];
        if ($request->has('spesifikasi_json')) {
            $incomingSpec = $request->input('spesifikasi_json');
            if (is_string($incomingSpec)) {
                $decoded = json_decode($incomingSpec, true);
                if (is_array($decoded)) {
                    $specJson = array_merge($specJson, $decoded);
                }
            } elseif (is_array($incomingSpec)) {
                $specJson = array_merge($specJson, $incomingSpec);
            }
        }
        $specJson['sumber_dana'] = 'hibah';
        $specJson['pemberi_hibah'] = $validated['hibah_pemberi'];
        $specJson['pemberi'] = $validated['hibah_pemberi'];
        $specJson['nomor_bast'] = $validated['hibah_nomor_bast'];
        $specJson['tanggal_bast'] = $validated['hibah_tanggal_bast'];
        if ($request->filled('hibah_pimpinan')) {
            $specJson['hibah_pimpinan'] = $request->input('hibah_pimpinan');
        }
        if ($request->filled('hibah_alamat_pemberi')) {
            $specJson['hibah_alamat_pemberi'] = $request->input('hibah_alamat_pemberi');
        }
        if ($dokumenPath) {
            $specJson['dokumen_path'] = $dokumenPath;
            $specJson['dokumen_bast_path'] = $dokumenPath;
        }

        // Tangkap repeater spesifikasi
        $repeaterKeys = ['tanah_items', 'mesin_items', 'gedung_items', 'jaringan_items', 'lainnya_items', 'atb_items', 'kdp_items', 'merk', 'type', 'ukuran', 'bahan', 'no_pabrik', 'no_rangka', 'no_mesin', 'no_polisi', 'sertifikat_nomor'];
        foreach ($repeaterKeys as $rk) {
            if ($request->has($rk) && !is_null($request->input($rk))) {
                $specJson[$rk] = $request->input($rk);
            }
        }

        $vol = max(1, (int) $validated['jumlah_volume']);
        $totalRealisasi = (float) $validated['total_realisasi'];
        $hargaSatuan = $vol > 0 ? ($totalRealisasi / $vol) : $totalRealisasi;
        $tahunPerolehan = (int) $validated['tahun_perolehan'];

        DB::transaction(function () use ($astap, $validated, $specJson, $hargaSatuan, $vol, $totalRealisasi, $tahunPerolehan, $request) {
            // 1. Update master ASTAP
            $astap->update([
                'jenis_astap_id'      => $request->input('jenis_astap_id') ?: $astap->jenis_astap_id,
                'nama_barang'         => $validated['nama_barang'],
                'satuan'              => $validated['satuan'],
                'jumlah_volume'       => $vol,
                'harga_satuan'        => $hargaSatuan,
                'total_realisasi'     => $totalRealisasi,
                'jumlah_realisasi'    => $totalRealisasi,
                'tahun_perolehan'     => $tahunPerolehan,
                'triwulan'            => $validated['triwulan'],
                'unit_id'             => $validated['unit_id'] ?? $astap->unit_id,
                'alamat_barang'       => $validated['alamat_barang'] ?? $astap->alamat_barang,
                'ppk_nama'            => $validated['ppk_nama'] ?? $astap->ppk_nama,
                'ppk_nip'             => $validated['ppk_nip'] ?? $astap->ppk_nip,
                'hibah_pemberi'       => $validated['hibah_pemberi'],
                'hibah_nomor_bast'    => $validated['hibah_nomor_bast'],
                'hibah_tanggal_bast'  => $validated['hibah_tanggal_bast'],
                'hibah_keterangan'    => $validated['hibah_keterangan'] ?? null,
                'bast_dokumen_nomor'  => $validated['hibah_nomor_bast'],
                'bast_dokumen_tanggal'=> $validated['hibah_tanggal_bast'],
                'is_extracomtable'    => !empty($validated['is_extracomtable']) ? 1 : 0,
                'spesifikasi_json'    => $specJson,
            ]);

            // 2. Update atau sync AstapHibah (tipe: masuk)
            $hibah = AstapHibah::where('astap_id', $astap->id)
                ->where('tipe_hibah', 'masuk')
                ->first();
            if ($hibah) {
                $hibah->update([
                    'pihak_hibah'   => $validated['hibah_pemberi'],
                    'nomor_bast'    => $validated['hibah_nomor_bast'],
                    'tanggal_bast'  => $validated['hibah_tanggal_bast'],
                    'jumlah_volume' => $vol,
                    'satuan'        => $validated['satuan'],
                    'nilai_aset'    => $totalRealisasi,
                    'tahun'         => $tahunPerolehan,
                    'triwulan'      => $validated['triwulan'],
                    'keterangan'    => $validated['hibah_keterangan'] ?? null,
                ]);
            }

            // 3. Update existing registers dan sesuaikan volume jika bertambah
            $existingRegisters = $astap->registers()->where('is_deleted', 0)->orderBy('no_register_int')->get();
            $unitNama = $astap->unit ? $astap->unit->nama : null;

            foreach ($existingRegisters as $reg) {
                $reg->update([
                    'nama_barang'     => $validated['nama_barang'],
                    'tahun_perolehan' => $tahunPerolehan,
                    'harga_satuan'    => $hargaSatuan,
                    'unit_id'         => $validated['unit_id'] ?? $reg->unit_id,
                    'ruang_pemegang'  => $unitNama ?: $reg->ruang_pemegang,
                ]);
            }

            // Jika volume bertambah, buat unit register baru dengan format 45 digit resmi
            if ($existingRegisters->count() < $vol) {
                $needed = $vol - $existingRegisters->count();
                $ja = $astap->jenisAstap ?: JenisAstap::find($astap->jenis_astap_id);
                $kode108Raw = $ja ? ($ja->sub_sub_rincian_objek ?: ($ja->sub_rincian_objek ?: $ja->jenis)) : '1.3.2.00.00.00';
                $kode108Clean = str_replace('.', '', $kode108Raw);

                $maxRegInt = AstapRegister::where('tahun_perolehan', $tahunPerolehan)
                    ->whereHas('astap', fn($sq) => $sq->where('jenis_astap_id', $astap->jenis_astap_id))
                    ->max('no_register_int') ?? 0;

                $runningRegNum = (int) $maxRegInt;

                for ($i = 1; $i <= $needed; $i++) {
                    $runningRegNum++;
                    $noRegStr = str_pad($runningRegNum, 7, '0', STR_PAD_LEFT);
                    $nibar = "1201351102000000280000{$tahunPerolehan}{$kode108Clean}{$noRegStr}";

                    while (AstapRegister::where('nibar', $nibar)->exists()) {
                        $runningRegNum++;
                        $noRegStr = str_pad($runningRegNum, 7, '0', STR_PAD_LEFT);
                        $nibar = "1201351102000000280000{$tahunPerolehan}{$kode108Clean}{$noRegStr}";
                    }

                    $qrPath = "/scan/{$nibar}";
                    AstapRegister::create([
                        'astap_id'        => $astap->id,
                        'unit_id'         => $validated['unit_id'] ?? null,
                        'tahun_perolehan' => $tahunPerolehan,
                        'no_register_int' => $runningRegNum,
                        'no_register'     => $nibar,
                        'nibar'           => $nibar,
                        'qr_code_path'    => $qrPath,
                        'ruang_pemegang'  => $unitNama ?: ($astap->alamat_barang ?: 'Gudang Aset'),
                        'kondisi'         => 'Baik',
                        'status'          => 'Tersedia',
                        'is_deleted'      => 0,
                    ]);
                }
            }
        });

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Perubahan Data Hibah "' . $astap->nama_barang . '" berhasil disimpan!',
                'redirect' => route('master.hibah')
            ]);
        }

        return redirect()->route('master.hibah')
            ->with('success', 'Perubahan Data Hibah "' . $astap->nama_barang . '" berhasil disimpan.');
    }

    /**
     * Simpan Transaksi Hibah Keluar (Pengurangan Barang RSUD yang dihibahkan ke luar)
     */
    public function storeHibahKeluar(Request $request)
    {
        // Normalisasi tanggal_bast jika format dd/mm/yyyy
        if ($request->has('tanggal_bast') && is_string($request->tanggal_bast)) {
            $rawDate = trim($request->tanggal_bast);
            if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $rawDate, $m)) {
                $isoDate = sprintf('%04d-%02d-%02d', $m[3], $m[2], $m[1]);
                $request->merge(['tanggal_bast' => $isoDate]);
                if ($request->isJson()) {
                    $request->json()->set('tanggal_bast', $isoDate);
                }
            }
        }

        $data = $request->validate([
            'astap_id'        => 'required|integer|exists:astaps,id',
            'register_ids'    => 'nullable|array',
            'register_ids.*'  => 'integer|exists:astap_registers,id',
            'penerima_hibah'  => 'required|string|max:255',
            'nomor_bast'      => 'required|string|max:150',
            'tanggal_bast'    => 'required|date',
            'nilai_aset'      => 'required|numeric|min:0',
            'tahun'           => 'required|integer',
            'triwulan'        => 'required|string|in:TW I,TW II,TW III,TW IV',
            'keterangan'      => 'nullable|string|max:2000',
        ]);

        $user = Auth::user();
        $astap = Astap::with('registers')->findOrFail($data['astap_id']);

        $selectedRegisterIds = $data['register_ids'] ?? [];
        if (count($selectedRegisterIds) > 0) {
            $selectedRegisterIds = AstapRegister::whereIn('id', $selectedRegisterIds)
                ->where('astap_id', $astap->id)
                ->where(function ($q) {
                    $q->where('status', 'Tersedia')
                      ->orWhere('status', 'Aktif')
                      ->orWhereNull('status');
                })
                ->where('is_deleted', 0)
                ->pluck('id')
                ->toArray();
        }
        $volumeKeluar = count($selectedRegisterIds) > 0 ? count($selectedRegisterIds) : max(1, (int) $astap->jumlah_volume);

        DB::transaction(function () use ($data, $user, $astap, $selectedRegisterIds, $volumeKeluar) {
            // 1. Buat catatan di astap_hibahs (tipe keluar)
            $firstRegId = count($selectedRegisterIds) === 1 ? $selectedRegisterIds[0] : null;

            AstapHibah::create([
                'tipe_hibah'        => 'keluar',
                'astap_id'          => $astap->id,
                'astap_register_id' => $firstRegId,
                'pihak_hibah'       => $data['penerima_hibah'],
                'nomor_bast'        => $data['nomor_bast'],
                'tanggal_bast'      => $data['tanggal_bast'],
                'jumlah_volume'     => $volumeKeluar,
                'satuan'            => $astap->satuan ?: 'Unit',
                'nilai_aset'        => (float) $data['nilai_aset'],
                'tahun'             => (int) $data['tahun'],
                'triwulan'          => $data['triwulan'],
                'keterangan'        => $data['keterangan'] ?? null,
                'user_id'           => $user?->id,
            ]);

            // 2. Tandai unit register yang dihibahkan menjadi tidak aktif / pengurangan
            $alasan = 'Dihibahkan ke ' . $data['penerima_hibah'] . ' (BAST: ' . $data['nomor_bast'] . ')';

            if (count($selectedRegisterIds) > 0) {
                AstapRegister::whereIn('id', $selectedRegisterIds)->update([
                    'status'        => 'Dihibahkan',
                    'is_deleted'    => 1,
                    'deleted_at'    => now(),
                    'deleted_by'    => $user?->name ?: 'Administrator',
                ]);

                // Cek sisa register aktif pada astap induk
                $sisaAktif = AstapRegister::where('astap_id', $astap->id)
                    ->where('is_deleted', 0)
                    ->count();

                if ($sisaAktif === 0) {
                    $astap->update([
                        'is_deleted'          => 1,
                        'deleted_at'          => now(),
                        'deleted_by'          => $user?->name ?: 'Administrator',
                        'keterangan_tambahan' => $alasan,
                    ]);
                } else {
                    $astap->update([
                        'jumlah_volume' => $sisaAktif,
                    ]);
                }
            } else {
                // Seluruh register dihibahkan
                AstapRegister::where('astap_id', $astap->id)->update([
                    'status'        => 'Dihibahkan',
                    'is_deleted'    => 1,
                    'deleted_at'    => now(),
                    'deleted_by'    => $user?->name ?: 'Administrator',
                ]);

                $astap->update([
                    'is_deleted'          => 1,
                    'deleted_at'          => now(),
                    'deleted_by'          => $user?->name ?: 'Administrator',
                    'keterangan_tambahan' => $alasan,
                ]);
            }
        });

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Barang "' . $astap->nama_barang . '" berhasil diproses hibah keluar (dihibahkan ke ' . $data['penerima_hibah'] . ')!',
            ]);
        }

        return redirect()->route('master.hibah')
            ->with('success', 'Barang "' . $astap->nama_barang . '" berhasil dihibahkan ke ' . $data['penerima_hibah'] . '.');
    }

    /**
     * Batalkan / Hapus Transaksi Hibah (Trackable Soft Delete ke Pusat Data Terhapus)
     */
    public function destroy(Request $request, $id)
    {
        $hibah = AstapHibah::findOrFail($id);

        DB::transaction(function () use ($hibah, $request) {
            $user = Auth::user();
            $deleterName = $user ? ($user->name . ' (' . ucfirst($user->role ?? 'user') . ')') : 'Administrator';
            $deleterId = $user?->id;
            $now = now();

            // 1. Jika hibah keluar dibatalkan/dihapus, pulihkan unit barang di ASTAP & register
            if ($hibah->tipe_hibah === 'keluar') {
                $astap = Astap::find($hibah->astap_id);

                // Ambil register yang dihibahkan
                if ($hibah->astap_register_id) {
                    $regs = AstapRegister::where('id', $hibah->astap_register_id)->get();
                } else {
                    $regs = AstapRegister::where('astap_id', $hibah->astap_id)
                        ->where('status', 'Dihibahkan')
                        ->get();
                }

                foreach ($regs as $reg) {
                    $statusBalik = (!empty($reg->ruang_pemegang) && !in_array($reg->ruang_pemegang, ['-', 'Belum Ditempatkan / Di Gudang', 'Gudang Aset']))
                        ? 'Aktif'
                        : 'Tersedia';

                    $reg->update([
                        'status'        => $statusBalik,
                        'is_deleted'    => 0,
                        'deleted_at'    => null,
                        'deleted_by'    => null,
                        'deleted_by_id' => null,
                    ]);
                }

                if ($astap) {
                    $sisaAktif = AstapRegister::where('astap_id', $astap->id)
                        ->where('is_deleted', 0)
                        ->count();

                    $astap->update([
                        'is_deleted'          => 0,
                        'deleted_at'          => null,
                        'deleted_by'          => null,
                        'deleted_by_id'       => null,
                        'keterangan_tambahan' => null,
                        'jumlah_volume'       => max(1, $sisaAktif),
                    ]);
                }
            } else {
                // 2. Jika hibah MASUK dihapus:
                // Soft delete juga catatan induk di astaps dan unit registernya (sama seperti kemitraan)
                if ($hibah->astap_id) {
                    $astap = Astap::find($hibah->astap_id);
                    if ($astap) {
                        $astap->update([
                            'is_deleted'    => 1,
                            'deleted_at'    => $now,
                            'deleted_by'    => $deleterName,
                            'deleted_by_id' => $deleterId,
                        ]);

                        AstapRegister::where('astap_id', $astap->id)->update([
                            'is_deleted'    => 1,
                            'deleted_at'    => $now,
                            'deleted_by'    => $deleterName,
                            'deleted_by_id' => $deleterId,
                        ]);
                    }
                }
            }

            // Tandai transaksi hibah terhapus secara aman (Soft Delete ke Pusat Recycle Bin)
            $hibah->update([
                'is_deleted'    => 1,
                'deleted_at'    => $now,
                'deleted_by'    => $deleterName,
                'deleted_by_id' => $deleterId,
            ]);
        });

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Catatan riwayat hibah berhasil dipindahkan ke Pusat Data Terhapus (Recycle Bin).',
            ]);
        }

        return redirect()->route('master.hibah')
            ->with('success', 'Catatan riwayat hibah berhasil dipindahkan ke Pusat Data Terhapus (Recycle Bin).');
    }

    /**
     * Cetak Lembar Berita Acara Serah Terima (BAST) Hibah Aset Resmi
     */
    public function cetak($id)
    {
        Carbon::setLocale('id');

        $hibah = AstapHibah::with([
            'astap.jenisAstap',
            'astap.registers',
            'register',
            'user',
        ])->findOrFail($id);

        return view('pages.hibah.cetak_bast', compact('hibah'));
    }

    /**
     * Helper: Ambil seluruh riwayat Pejabat Pembuat Komitmen (PPK) unik
     */
    public static function getDistinctPejabats()
    {
        return Astap::whereNotNull('ppk_nama')
            ->where('ppk_nama', '!=', '')
            ->orderBy('id', 'desc')
            ->get(['ppk_nama', 'ppk_nip'])
            ->map(function($a) {
                return [
                    'nama' => trim($a->ppk_nama),
                    'nip'  => $a->ppk_nip ?? '',
                ];
            })
            ->unique(fn($p) => strtolower(trim($p['nama'])))
            ->values();
    }

    /**
     * Helper: Ambil seluruh riwayat Penyedia unik
     */
    public static function getDistinctPenyedias()
    {
        try {
            return AstapBelanjaModal::whereNotNull('penyedia_nama')
                ->where('penyedia_nama', '!=', '')
                ->with('astap:id,spesifikasi_json')
                ->orderBy('id', 'desc')
                ->get(['astap_id', 'penyedia_nama', 'penyedia_pemilik', 'penyedia_rekening_nama', 'penyedia_rekening_nomor', 'penyedia_alamat'])
                ->map(function($m) {
                    $spec = $m->astap?->spesifikasi_json;
                    if (is_string($spec)) {
                        try { $spec = json_decode($spec, true); } catch (\Throwable $e) { $spec = []; }
                    }
                    $tel = is_array($spec) ? ($spec['penyedia_telepon'] ?? ($spec['penyedia_kontak'] ?? '')) : '';
                    return [
                        'nama'           => trim($m->penyedia_nama),
                        'pemilik'        => $m->penyedia_pemilik ?? '',
                        'telepon'        => $tel ?: '',
                        'rekening_nama'  => $m->penyedia_rekening_nama ?? '',
                        'rekening_nomor' => $m->penyedia_rekening_nomor ?? '',
                        'alamat'         => $m->penyedia_alamat ?? '',
                    ];
                })
                ->unique(fn($p) => strtolower(trim($p['nama'])))
                ->values();
        } catch (\Throwable $e) {
            return collect();
        }
    }

    /**
     * Helper: Ambil seluruh riwayat Instansi / Pemberi Hibah unik
     */
    public static function getDistinctPemberiHibahs()
    {
        try {
            return AstapHibah::whereNotNull('pihak_hibah')
                ->where('pihak_hibah', '!=', '')
                ->distinct()
                ->pluck('pihak_hibah')
                ->merge(
                    Astap::where('sumber_dana', 'hibah')
                        ->with('hibahDetail')
                        ->get()
                        ->map(function ($a) {
                            return $a->hibahDetail?->pihak_hibah
                                ?: ($a->spesifikasi_json['pemberi'] ?? ($a->spesifikasi_json['hibah_pemberi'] ?? null));
                        })
                        ->filter()
                )
                ->merge([
                    'Kementerian Kesehatan Republik Indonesia',
                    'Dinas Kesehatan Provinsi Jawa Timur',
                    'Pemerintah Kabupaten Bondowoso',
                    'Donatur Swasta / Yayasan CSR',
                ])
                ->map(fn($v) => trim($v))
                ->filter()
                ->unique()
                ->values();
        } catch (\Throwable $e) {
            return collect([
                'Kementerian Kesehatan Republik Indonesia',
                'Dinas Kesehatan Provinsi Jawa Timur',
                'Pemerintah Kabupaten Bondowoso',
                'Donatur Swasta / Yayasan CSR',
            ]);
        }
    }
}

