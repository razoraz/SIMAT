<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$astaps = \App\Models\Astap::with('registers', 'jenisAstap')->get();
foreach ($astaps as $a) {
    $kode108 = $a->jenisAstap ? $a->jenisAstap->sub_sub_rincian_objek : '-';
    echo sprintf(
        "ID: %d | Kode: %s | Nama: %s | Thn: %s | Vol: %s %s | Hrg: %s | Real: %s | Extracom: %s | Regs: %d\n",
        $a->id,
        $kode108,
        $a->nama_barang,
        $a->tahun_perolehan,
        $a->jumlah_volume,
        $a->satuan,
        number_format($a->harga_satuan, 0, ',', '.'),
        number_format($a->total_realisasi, 0, ',', '.'),
        $a->is_extracom ? 'YES' : 'NO',
        $a->registers->count()
    );
    if (!empty($a->spesifikasi_json)) {
        echo "  Spec keys: " . implode(', ', array_keys($a->spesifikasi_json)) . "\n";
        if (isset($a->spesifikasi_json['mesin_items'])) {
            echo "  Mesin items count: " . count($a->spesifikasi_json['mesin_items']) . "\n";
        }
        if (isset($a->spesifikasi_json['tanah_items'])) {
            echo "  Tanah items count: " . count($a->spesifikasi_json['tanah_items']) . "\n";
        }
    }
    foreach ($a->registers as $idx => $r) {
        if ($idx < 3 || $idx >= $a->registers->count() - 2) {
            echo "    Reg[{$idx}]: ID={$r->id}, NoReg={$r->no_register_int}, NIBAR={$r->nibar}, Ruang={$r->ruang_pemegang}, Kondisi={$r->kondisi}\n";
        } elseif ($idx === 3) {
            echo "    ... (total " . $a->registers->count() . " registers)\n";
        }
    }
    echo "--------------------------------------------------------\n";
}
