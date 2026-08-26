<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$astaps = \App\Models\Astap::with(['registers', 'jenisAstap', 'rekeningBelanja', 'jenisPengadaan', 'unit'])
    ->orderBy('id', 'desc')
    ->get()
    ->map(function($a) {
        $spec = is_array($a->spesifikasi_json) ? $a->spesifikasi_json : (json_decode($a->spesifikasi_json, true) ?? []);
        return [
            'id' => $a->id,
            'category' => $a->category,
            'kode_barang' => $a->kode_108,
            'nama_barang' => $a->nama_barang,
        ];
    });

foreach ($astaps as $item) {
    echo "ID: {$item['id']} | cat: {$item['category']} | kode: {$item['kode_barang']} | nama: {$item['nama_barang']}" . PHP_EOL;
}
echo "Total: " . $astaps->count() . PHP_EOL;
