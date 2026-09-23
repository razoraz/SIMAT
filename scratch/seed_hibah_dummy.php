<?php
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Astap;
use App\Models\AstapRegister;
use App\Models\AstapHibah;
use App\Models\JenisAstap;
use App\Models\Unit;

echo "--- CHECKING CURRENT HIBAH DATA ---\n";
$hibahCount = AstapHibah::count();
echo "AstapHibah count: $hibahCount\n";
$hibahAstapCount = Astap::where('sumber_dana', 'hibah')->count();
echo "Astap sumber_dana=hibah count: $hibahAstapCount\n";

if ($hibahCount === 0) {
    echo "Creating dummy hibah data...\n";

    $unitPoli = Unit::where('nama', 'LIKE', '%Poli%')->orWhere('nama', 'LIKE', '%Radiologi%')->first() ?? Unit::first();
    $unitHd = Unit::where('nama', 'LIKE', '%HD%')->orWhere('nama', 'LIKE', '%Hemodialisa%')->orWhere('nama', 'LIKE', '%Rawat%')->first() ?? Unit::first();
    $unitPkm = Unit::first();

    // 1. HIBAH MASUK 1: USG Mindray
    $jenisUsg = JenisAstap::where('sub_sub_rincian_objek', 'LIKE', '1.3.2%')
        ->where('uraian_sub_sub_rincian', 'LIKE', '%USG%')
        ->first() 
        ?? JenisAstap::where('sub_sub_rincian_objek', 'LIKE', '1.3.2%')->first();

    $usg = Astap::create([
        'nama_barang' => 'USG 4D Color Doppler Portable Mindray DC-30',
        'jenis_astap_id' => $jenisUsg?->id,
        'tahun_perolehan' => 2026,
        'jumlah_volume' => 2,
        'satuan' => 'Unit',
        'harga_satuan' => 225000000,
        'jumlah_anggaran' => 0,
        'jumlah_realisasi' => 450000000,
        'total_realisasi' => 450000000,
        'triwulan' => 'TW I',
        'sumber_dana' => 'hibah',
        'hibah_pemberi' => 'Kementerian Kesehatan Republik Indonesia',
        'hibah_nomor_bast' => '020/BAST-HIBAH/KEMENKES/III/2026',
        'hibah_tanggal_bast' => '2026-03-15',
        'hibah_keterangan' => 'Hibah Penguatan Rujukan Maternal dan Neonatal Kemenkes RI',
        'bast_dokumen_nomor' => '020/BAST-HIBAH/KEMENKES/III/2026',
        'bast_dokumen_tanggal' => '2026-03-15',
        'unit_id' => $unitPoli?->id,
        'alamat_barang' => 'RSUD Dr. H. Koesnandi - Instalasi Radiologi & Kebidanan',
        'category' => 'KIB B',
        'kode_108' => $jenisUsg?->sub_sub_rincian_objek ?? '1.3.2.05.01.04.004',
        'ppk_nama' => 'dr. YUS PRIYATNA, Sp.P',
        'ppk_nip' => '19760815 200501 1 009',
        'is_extracomtable' => 0,
        'is_reklas' => 0,
        'is_deleted' => 0,
        'spesifikasi_json' => [
            'sumber_dana' => 'hibah',
            'pemberi' => 'Kementerian Kesehatan Republik Indonesia',
            'nomor_bast' => '020/BAST-HIBAH/KEMENKES/III/2026',
            'tanggal_bast' => '2026-03-15',
            'merk' => 'Mindray',
            'type' => 'DC-30 Color Doppler Portable',
            'no_pabrik' => 'SN-MN-99281-2026',
            'ukuran' => 'Standard Portable',
            'bahan' => 'Komposit Medis / Elektronik',
            'kondisi' => 'Baik',
            'ruang_unit' => $unitPoli?->nama ?? 'Instalasi Radiologi',
        ]
    ]);

    // Registers USG
    $kodeCleanUsg = str_replace('.', '', $usg->kode_108);
    for ($i = 1; $i <= 2; $i++) {
        $noRegStr = str_pad($i + 900, 7, '0', STR_PAD_LEFT);
        $nibar = "12013511020000002800002026{$kodeCleanUsg}{$noRegStr}";
        AstapRegister::create([
            'astap_id' => $usg->id,
            'unit_id' => $unitPoli?->id,
            'tahun_perolehan' => 2026,
            'no_register_int' => $i + 900,
            'no_register' => $nibar,
            'nibar' => $nibar,
            'qr_code_path' => "/scan/{$nibar}",
            'ruang_pemegang' => $unitPoli?->nama ?? 'Instalasi Radiologi',
            'kondisi' => 'Baik',
            'status' => 'Aktif',
            'is_deleted' => 0,
        ]);
    }

    // Catat ke AstapHibah (Masuk)
    AstapHibah::create([
        'tipe_hibah' => 'masuk',
        'astap_id' => $usg->id,
        'pihak_hibah' => 'Kementerian Kesehatan Republik Indonesia',
        'nomor_bast' => '020/BAST-HIBAH/KEMENKES/III/2026',
        'tanggal_bast' => '2026-03-15',
        'jumlah_volume' => 2,
        'satuan' => 'Unit',
        'nilai_aset' => 450000000,
        'tahun' => 2026,
        'triwulan' => 'TW I',
        'keterangan' => 'Hibah Penguatan Rujukan Maternal dan Neonatal Kemenkes RI',
    ]);
    echo "Created Hibah Masuk 1: USG Mindray\n";

    // 2. HIBAH MASUK 2: Mesin Hemodialisa
    $jenisHd = JenisAstap::where('sub_sub_rincian_objek', 'LIKE', '1.3.2%')
        ->where('uraian_sub_sub_rincian', 'LIKE', '%Dialisa%')
        ->first()
        ?? JenisAstap::where('sub_sub_rincian_objek', 'LIKE', '1.3.2%')->skip(1)->first();

    $hd = Astap::create([
        'nama_barang' => 'Mesin Hemodialisa Fresenius Medical Care 4008S NG',
        'jenis_astap_id' => $jenisHd?->id,
        'tahun_perolehan' => 2026,
        'jumlah_volume' => 1,
        'satuan' => 'Unit',
        'harga_satuan' => 380000000,
        'jumlah_anggaran' => 0,
        'jumlah_realisasi' => 380000000,
        'total_realisasi' => 380000000,
        'triwulan' => 'TW II',
        'sumber_dana' => 'hibah',
        'hibah_pemberi' => 'Dinas Kesehatan Provinsi Jawa Timur',
        'hibah_nomor_bast' => '445/882/BAST-HIBAH/DINKES-PROV/VI/2026',
        'hibah_tanggal_bast' => '2026-06-20',
        'hibah_keterangan' => 'Bantuan Sarana Pelayanan Cuci Darah Pasien Gagal Ginjal Kronik',
        'bast_dokumen_nomor' => '445/882/BAST-HIBAH/DINKES-PROV/VI/2026',
        'bast_dokumen_tanggal' => '2026-06-20',
        'unit_id' => $unitHd?->id,
        'alamat_barang' => 'RSUD Dr. H. Koesnandi - Ruang Hemodialisa (Paviliun Teratai)',
        'category' => 'KIB B',
        'kode_108' => $jenisHd?->sub_sub_rincian_objek ?? '1.3.2.05.01.05.001',
        'ppk_nama' => 'dr. YUS PRIYATNA, Sp.P',
        'ppk_nip' => '19760815 200501 1 009',
        'is_extracomtable' => 0,
        'is_reklas' => 0,
        'is_deleted' => 0,
        'spesifikasi_json' => [
            'sumber_dana' => 'hibah',
            'pemberi' => 'Dinas Kesehatan Provinsi Jawa Timur',
            'nomor_bast' => '445/882/BAST-HIBAH/DINKES-PROV/VI/2026',
            'tanggal_bast' => '2026-06-20',
            'merk' => 'Fresenius Medical Care',
            'type' => '4008S NG Next Generation',
            'no_pabrik' => 'FMC-4008S-88192',
            'ukuran' => '140 x 60 x 70 cm',
            'bahan' => 'Steel, Polimer Medis, Komponen Elektronik',
            'kondisi' => 'Baik',
            'ruang_unit' => $unitHd?->nama ?? 'Ruang Hemodialisa',
        ]
    ]);

    // Registers HD
    $kodeCleanHd = str_replace('.', '', $hd->kode_108);
    $noRegHd = str_pad(905, 7, '0', STR_PAD_LEFT);
    $nibarHd = "12013511020000002800002026{$kodeCleanHd}{$noRegHd}";
    AstapRegister::create([
        'astap_id' => $hd->id,
        'unit_id' => $unitHd?->id,
        'tahun_perolehan' => 2026,
        'no_register_int' => 905,
        'no_register' => $nibarHd,
        'nibar' => $nibarHd,
        'qr_code_path' => "/scan/{$nibarHd}",
        'ruang_pemegang' => $unitHd?->nama ?? 'Ruang Hemodialisa',
        'kondisi' => 'Baik',
        'status' => 'Aktif',
        'is_deleted' => 0,
    ]);

    AstapHibah::create([
        'tipe_hibah' => 'masuk',
        'astap_id' => $hd->id,
        'pihak_hibah' => 'Dinas Kesehatan Provinsi Jawa Timur',
        'nomor_bast' => '445/882/BAST-HIBAH/DINKES-PROV/VI/2026',
        'tanggal_bast' => '2026-06-20',
        'jumlah_volume' => 1,
        'satuan' => 'Unit',
        'nilai_aset' => 380000000,
        'tahun' => 2026,
        'triwulan' => 'TW II',
        'keterangan' => 'Bantuan Sarana Pelayanan Cuci Darah Pasien Gagal Ginjal Kronik',
    ]);
    echo "Created Hibah Masuk 2: Mesin Hemodialisa\n";

    // 3. HIBAH KELUAR: 1 Aset RSUD Dihibahkan ke Puskesmas Tamanan
    $jenisBed = JenisAstap::where('sub_sub_rincian_objek', 'LIKE', '1.3.2%')
        ->where('uraian_sub_sub_rincian', 'LIKE', '%Tempat Tidur%')
        ->first()
        ?? JenisAstap::where('sub_sub_rincian_objek', 'LIKE', '1.3.2%')->skip(2)->first();

    $bed = Astap::create([
        'nama_barang' => 'Tempat Tidur Pasien Manual 3 Crank Standard',
        'jenis_astap_id' => $jenisBed?->id,
        'tahun_perolehan' => 2023,
        'jumlah_volume' => 1,
        'satuan' => 'Unit',
        'harga_satuan' => 18500000,
        'jumlah_anggaran' => 18500000,
        'jumlah_realisasi' => 18500000,
        'total_realisasi' => 18500000,
        'triwulan' => 'TW II',
        'sumber_dana' => 'belanja_modal',
        'category' => 'KIB B',
        'kode_108' => $jenisBed?->sub_sub_rincian_objek ?? '1.3.2.05.01.01.002',
        'is_extracomtable' => 0,
        'is_reklas' => 0,
        'is_deleted' => 1,
        'deleted_at' => '2026-04-10 10:00:00',
        'deleted_by' => 'Administrator',
        'alasan_hapus' => 'Dihibahkan ke Puskesmas Tamanan (BAST: 028/BAST-KELUAR/RSUD/IV/2026)',
        'spesifikasi_json' => [
            'merk' => 'Paramount Bed',
            'type' => 'A-5 Series 3 Crank',
            'kondisi' => 'Baik',
            'ruang_unit' => 'Puskesmas Tamanan'
        ]
    ]);

    $kodeCleanBed = str_replace('.', '', $bed->kode_108);
    $noRegBed = str_pad(910, 7, '0', STR_PAD_LEFT);
    $nibarBed = "12013511020000002800002023{$kodeCleanBed}{$noRegBed}";
    $regBed = AstapRegister::create([
        'astap_id' => $bed->id,
        'unit_id' => $unitPkm?->id,
        'tahun_perolehan' => 2023,
        'no_register_int' => 910,
        'no_register' => $nibarBed,
        'nibar' => $nibarBed,
        'qr_code_path' => "/scan/{$nibarBed}",
        'ruang_pemegang' => 'Puskesmas Tamanan',
        'kondisi' => 'Baik',
        'status' => 'Dihibahkan',
        'is_deleted' => 1,
        'deleted_at' => '2026-04-10 10:00:00',
        'deleted_by' => 'Administrator',
        'alasan_hapus' => 'Dihibahkan ke Puskesmas Tamanan (BAST: 028/BAST-KELUAR/RSUD/IV/2026)'
    ]);

    AstapHibah::create([
        'tipe_hibah' => 'keluar',
        'astap_id' => $bed->id,
        'astap_register_id' => $regBed->id,
        'pihak_hibah' => 'Puskesmas Tamanan Kabupaten Bondowoso',
        'nomor_bast' => '028/BAST-KELUAR/RSUD/IV/2026',
        'tanggal_bast' => '2026-04-10',
        'jumlah_volume' => 1,
        'satuan' => 'Unit',
        'nilai_aset' => 18500000,
        'tahun' => 2026,
        'triwulan' => 'TW II',
        'keterangan' => 'Dihibahkan guna menunjang fasilitas rawat inap Puskesmas Tamanan sesuai SK Direktur RSUD',
    ]);
    echo "Created Hibah Keluar: Bed Pasien dihibahkan ke Puskesmas Tamanan\n";
}

echo "Done! Total AstapHibah now: " . AstapHibah::count() . "\n";
