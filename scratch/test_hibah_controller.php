<?php
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::first();
\Illuminate\Support\Facades\Auth::login($user);

$controller = new \App\Http\Controllers\HibahController();
$request = \Illuminate\Http\Request::create('/master-data/hibah', 'GET');
$view = $controller->index($request);
$data = $view->getData();

echo "Hibah Controller Index Data:\n";
echo "  - Total Records: " . count($data['hibahRecords']) . "\n";
echo "  - Total Masuk Unit: " . $data['totalMasukUnit'] . "\n";
echo "  - Total Masuk Nominal: Rp " . number_format($data['totalMasukNominal'], 0, ',', '.') . "\n";
echo "  - Total Keluar Unit: " . $data['totalKeluarUnit'] . "\n";
echo "  - Total Keluar Nominal: Rp " . number_format($data['totalKeluarNominal'], 0, ',', '.') . "\n";
echo "  - Active Astaps available for Hibah Keluar: " . count($data['activeAstaps']) . "\n";

foreach ($data['hibahRecords'] as $r) {
    echo "    * [{$r->tipe_hibah}] {$r->nomor_bast} | {$r->pihak_hibah} | Vol: {$r->jumlah_volume} {$r->satuan} | Nilai: Rp " . number_format($r->nilai_aset, 0, ',', '.') . "\n";
}
