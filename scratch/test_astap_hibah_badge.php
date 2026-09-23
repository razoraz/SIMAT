<?php
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::first();
\Illuminate\Support\Facades\Auth::login($user);

$controller = new \App\Http\Controllers\AstapController();
$view = $controller->index();
$data = $view->getData();
$astaps = $data['astaps'] ?? [];

echo "Total ASTAP items in index: " . count($astaps) . "\n";
$hibahItems = array_filter($astaps->toArray(), fn($a) => ($a['sumber_dana'] === 'hibah' || ($a['sumber_dana_raw'] ?? '') === 'hibah'));
echo "Items with sumber_dana = hibah: " . count($hibahItems) . "\n";

foreach ($hibahItems as $h) {
    echo "  - [ID: {$h['id']}] {$h['nama_barang']} (sumber_dana: {$h['sumber_dana']}, pemberi: {$h['hibah_pemberi']}, bast: {$h['hibah_nomor_bast']}, nilai: {$h['jumlah_realisasi']})\n";
}
