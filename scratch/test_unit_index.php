<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$controller = new App\Http\Controllers\UnitController();
$view = $controller->index();
$units = $view->getData()['units'];

foreach ($units as $u) {
    if ($u['total_aset'] > 0) {
        echo "{$u['kode']} | {$u['nama']} => Total Aset: {$u['total_aset']} Item | Total Nilai: {$u['total_nilai']} | Assets Count: " . count($u['assets']) . "\n";
    }
}
