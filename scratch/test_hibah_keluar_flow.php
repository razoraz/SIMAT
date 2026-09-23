<?php
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::first();
\Illuminate\Support\Facades\Auth::login($user);

// Clean previous test if any
\App\Models\AstapRegister::where('nibar', 'LIKE', '%TEST%')->delete();
\App\Models\Astap::where('nama_barang', 'LIKE', '%Kursi Roda Pasien Stainless Test%')->delete();
\App\Models\AstapHibah::where('nomor_bast', '029/BAST-TEST/2026')->delete();

$uniqueSuffix = rand(10000, 99999);
$ja = \App\Models\JenisAstap::first();
$testAstap = \App\Models\Astap::create([
    'nama_barang' => 'Kursi Roda Pasien Stainless Test ' . $uniqueSuffix,
    'jenis_astap_id' => $ja->id,
    'tahun_perolehan' => 2025,
    'jumlah_volume' => 1,
    'satuan' => 'Unit',
    'harga_satuan' => 2500000,
    'jumlah_anggaran' => 2500000,
    'jumlah_realisasi' => 2500000,
    'total_realisasi' => 2500000,
    'triwulan' => 'TW I',
    'sumber_dana' => 'belanja_modal',
    'category' => 'KIB B',
    'kode_108' => $ja->sub_sub_rincian_objek ?? '1.3.2.05.01.01.001',
    'is_deleted' => 0,
]);

$reg = \App\Models\AstapRegister::create([
    'astap_id' => $testAstap->id,
    'tahun_perolehan' => 2025,
    'no_register_int' => $uniqueSuffix,
    'no_register' => 'REG-TEST-' . $uniqueSuffix,
    'nibar' => '12013511020000002800002025' . $uniqueSuffix,
    'kondisi' => 'Baik',
    'status' => 'Aktif',
    'is_deleted' => 0,
]);

echo "Created test item for Hibah Keluar: {$testAstap->nama_barang} (ID: {$testAstap->id})\n";

// Test storeHibahKeluar controller method
$controller = new \App\Http\Controllers\HibahController();
$req = \Illuminate\Http\Request::create('/master-data/hibah/keluar', 'POST', [
    'astap_id' => $testAstap->id,
    'register_ids' => [$reg->id],
    'penerima_hibah' => 'Puskesmas Grujugan',
    'nomor_bast' => '029/BAST-TEST/2026',
    'tanggal_bast' => '2026-04-12',
    'nilai_aset' => 2500000,
    'tahun' => 2026,
    'triwulan' => 'TW II',
    'keterangan' => 'Penyerahan kursi roda test untuk Puskesmas Grujugan'
]);

$response = $controller->storeHibahKeluar($req);
echo "storeHibahKeluar response status: " . $response->getStatusCode() . "\n";

$freshReg = \App\Models\AstapRegister::find($reg->id);
$freshAstap = \App\Models\Astap::find($testAstap->id);
echo "After hibah keluar:\n";
echo "  - Register status: {$freshReg->status}, is_deleted: {$freshReg->is_deleted}\n";
echo "  - Astap is_deleted: {$freshAstap->is_deleted}, keterangan: {$freshAstap->keterangan_tambahan}\n";

$createdHibah = \App\Models\AstapHibah::where('nomor_bast', '029/BAST-TEST/2026')->first();
echo "  - AstapHibah record created? " . ($createdHibah ? "YES (ID: {$createdHibah->id})" : "NO") . "\n";

// Now test destroy method (cancellation)
$destroyResp = $controller->destroy($createdHibah->id);
echo "destroy response status: " . $destroyResp->getStatusCode() . "\n";

$restoredReg = \App\Models\AstapRegister::find($reg->id);
$restoredAstap = \App\Models\Astap::find($testAstap->id);
echo "After cancel/destroy:\n";
echo "  - Register status: {$restoredReg->status}, is_deleted: {$restoredReg->is_deleted}\n";
echo "  - Astap is_deleted: {$restoredAstap->is_deleted}\n";

// Clean up test records
$reg->delete();
$testAstap->delete();
echo "Cleanup completed successfully!\n";
