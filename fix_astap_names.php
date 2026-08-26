<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Fix ID 12 or any mismatch where nama_barang does not match sub_sub_rincian
$astaps = \App\Models\Astap::with('jenisAstap')->get();
foreach ($astaps as $a) {
    if ($a->jenisAstap && !empty($a->jenisAstap->uraian_sub_sub_rincian)) {
        if ($a->nama_barang !== $a->jenisAstap->uraian_sub_sub_rincian && $a->nama_barang === 'Tanah Bangunan Apotik / Rumah Sakit') {
            echo "Updating ID {$a->id}: '{$a->nama_barang}' -> '{$a->jenisAstap->uraian_sub_sub_rincian}'" . PHP_EOL;
            $a->nama_barang = $a->jenisAstap->uraian_sub_sub_rincian;
            $a->save();
        }
    }
}
