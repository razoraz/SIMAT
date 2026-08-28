<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- EXISTING ASTAP MUTASIS RECORDS ---\n";
foreach (App\Models\AstapMutasi::all() as $m) {
    echo "ID: {$m->id} | BAMB: {$m->nomor_bamb} | Old Jenis: '{$m->jenis_mutasi}' | From: '{$m->ruangan_asal}' | To: '{$m->ruangan_tujuan}'\n";
}
