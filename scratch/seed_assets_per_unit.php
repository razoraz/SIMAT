<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Unit;
use App\Models\Astap;
use App\Models\AstapRegister;

$units = Unit::all();
echo "Found " . $units->count() . " units in database.\n";

$sampleBarangs = [
    [
        'nama' => 'Komputer PC Workstation RSUD',
        'spesifikasi' => 'Intel Core i7, 16GB RAM, SSD 512GB',
        'harga' => 12500000,
        'kondisi' => 'Baik',
        'nibar_prefix' => '1.3.2.04.001'
    ],
    [
        'nama' => 'Printer Multifungsi Laserjet',
        'spesifikasi' => 'Print / Scan / Copy Duplex',
        'harga' => 4800000,
        'kondisi' => 'Baik',
        'nibar_prefix' => '1.3.2.04.002'
    ],
    [
        'nama' => 'AC Split Inverter 1.5 PK',
        'spesifikasi' => 'Daikin Inverter R32 Eco Friendly',
        'harga' => 6200000,
        'kondisi' => 'Baik',
        'nibar_prefix' => '1.3.2.05.001'
    ],
    [
        'nama' => 'Meja Kerja Kayu Jati Premium',
        'spesifikasi' => 'Laci 3 Tingkat Kunci Sentral',
        'harga' => 2750000,
        'kondisi' => 'Baik',
        'nibar_prefix' => '1.3.2.02.001'
    ],
    [
        'nama' => 'Kursi Kerja Ergonomis Mesh',
        'spesifikasi' => 'Headrest, Armrest Adjustable 3D',
        'harga' => 1850000,
        'kondisi' => 'Baik',
        'nibar_prefix' => '1.3.2.02.002'
    ],
    [
        'nama' => 'Lemari Arsip Besi 4 Pintu',
        'spesifikasi' => 'Besi Plat 0.8mm Anti Karat',
        'harga' => 3400000,
        'kondisi' => 'Baik',
        'nibar_prefix' => '1.3.2.02.003'
    ],
    [
        'nama' => 'Alat Suction Pump Medik',
        'spesifikasi' => 'Suction Portable High Vacuum',
        'harga' => 8900000,
        'kondisi' => 'Baik',
        'nibar_prefix' => '1.3.2.08.001'
    ],
    [
        'nama' => 'Bed Pasien Crank 3 Manual',
        'spesifikasi' => 'Struktur Baja, Matras Waterproof',
        'harga' => 11200000,
        'kondisi' => 'Baik',
        'nibar_prefix' => '1.3.2.08.002'
    ]
];

$totalAdded = 0;

foreach ($units as $unit) {
    // Count existing registers for this unit
    $existingCount = AstapRegister::where('unit_id', $unit->id)
        ->orWhere('ruang_pemegang', $unit->nama)
        ->count();

    $needed = 3 - $existingCount;
    if ($needed <= 0) {
        echo "Unit [{$unit->nama}] already has {$existingCount} items. Skipping.\n";
        continue;
    }

    echo "Unit [{$unit->nama}] has {$existingCount} items. Adding {$needed} items...\n";

    for ($i = 0; $i < $needed; $i++) {
        $sample = $sampleBarangs[array_rand($sampleBarangs)];
        
        // Find or create Astap master for sample nama
        $astapMaster = Astap::firstOrCreate(
            ['nama_barang' => $sample['nama']],
            [
                'tahun_perolehan'    => 2026,
                'jenis_astap_id'     => 1,
                'spesifikasi'        => $sample['spesifikasi'],
                'tahun_pengadaan'    => 2026,
            ]
        );

        $regIndex = rand(100, 999);
        $nibar = sprintf("%s.2026.%04d", $sample['nibar_prefix'], $unit->id * 100 + $i + $regIndex);

        // Check if nibar exists
        while (AstapRegister::where('nibar', $nibar)->exists()) {
            $nibar = sprintf("%s.2026.%04d", $sample['nibar_prefix'], rand(1000, 9999));
        }

        AstapRegister::create([
            'astap_id'        => $astapMaster->id,
            'unit_id'         => $unit->id,
            'nibar'           => $nibar,
            'ruang_pemegang'  => $unit->nama,
            'kondisi'         => $sample['kondisi'],
            'harga'           => $sample['harga'],
            'status'          => 'Tersedia',
            'no_register'     => sprintf("%04d", $regIndex),
            'no_register_int' => $regIndex,
            'tahun_perolehan' => 2026,
            'keterangan'      => 'Aset terdaftar di ' . $unit->nama,
        ]);

        $totalAdded++;
    }
}

echo "Finished! Total {$totalAdded} new register assets added across units.\n";
