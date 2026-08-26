<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$astaps = \App\Models\Astap::with('jenisAstap')->get();
foreach ($astaps as $a) {
    $jaNama = $a->jenisAstap ? $a->jenisAstap->uraian_sub_sub_rincian : 'NULL';
    $jaKode = $a->jenisAstap ? $a->jenisAstap->sub_sub_rincian_objek : 'NULL';
    echo "ID: {$a->id} | nama_barang: {$a->nama_barang} | kode_108: {$a->kode_108} | jaKode: {$jaKode} | jaNama: {$jaNama}" . PHP_EOL;
}
