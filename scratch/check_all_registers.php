<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- ALL REGISTERS WITH UNIT & RUANG --- \n";
foreach (App\Models\AstapRegister::with(['astap', 'unit'])->get() as $r) {
    echo "Reg ID: {$r->id} | NIBAR: {$r->nibar} | Unit ID: {$r->unit_id} (" . ($r->unit->nama ?? 'NULL') . ") | Ruang: {$r->ruang_pemegang} | ASTAP: " . ($r->astap->nama_barang ?? 'NULL') . "\n";
}
