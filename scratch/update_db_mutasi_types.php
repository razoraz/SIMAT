<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\AstapMutasi;

// Update existing records
AstapMutasi::where('jenis_mutasi', 'Pemindahan')->where('nomor_bamb', 'MTS-2026-002')->update(['jenis_mutasi' => 'Ajukan Mutasi']);
AstapMutasi::where('jenis_mutasi', 'Pemindahan')->where('nomor_bamb', 'MTS-2026-009')->update(['jenis_mutasi' => 'Minta Mutasi']);
AstapMutasi::where('jenis_mutasi', 'Pemindahan')->update(['jenis_mutasi' => 'Ajukan Mutasi']);
AstapMutasi::where('jenis_mutasi', 'Penghapusan')->update(['jenis_mutasi' => 'Pengembalian']);

echo "Updated mutasi records:\n";
foreach (AstapMutasi::all() as $m) {
    echo "ID: {$m->id} | BAMB: {$m->nomor_bamb} | New Jenis: '{$m->jenis_mutasi}' | From: {$m->ruangan_asal} -> To: {$m->ruangan_tujuan}\n";
}
