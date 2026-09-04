<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Astap;
use App\Models\AstapRegister;
use Illuminate\Support\Facades\DB;

DB::beginTransaction();

try {
    // 1. Perbaiki ASTAP ID 10
    $a10 = Astap::find(10);
    if ($a10) {
        $a10->satuan = 'Unit';
        $a10->is_extracomtable = 1; // 85.000 <= 1.000.000
        
        $a10->spesifikasi_json = [
            'jumlah_anggaran' => (float)$a10->jumlah_anggaran,
            'merk' => '-',
            'type' => '-',
            'bahan' => '-',
            'no_pabrik' => '-',
            'kondisi' => 'Baik',
            'mesin_items' => [
                [
                    'mesin_nama_barang' => $a10->nama_barang,
                    'mesin_kode_barang' => '1.3.2.01.01.01.001',
                    'mesin_uraian_barang' => 'Crawler Tractor + Attachment',
                    'mesin_jumlah_barang' => 10,
                    'mesin_satuan' => 'Unit',
                    'mesin_nilai_satuan' => 85000,
                    'mesin_administrasi_proyek' => 0,
                    'mesin_total_realisasi' => 850000,
                    'mesin_merk' => '-',
                    'mesin_type' => '-',
                    'mesin_ukuran' => '-',
                    'mesin_bahan' => '-',
                    'mesin_nomor_pabrik' => '-',
                    'mesin_nomor_rangka' => '-',
                    'mesin_nomor_mesin' => '-',
                    'mesin_nomor_polisi' => '-',
                    'mesin_nomor_bpkb' => '-',
                    'mesin_kondisi' => 'Baik',
                    'ruang_pemegang_mesin' => 'Depo Farmasi & Bedah Sentral',
                    'is_extracom' => true
                ]
            ]
        ];
        $a10->save();

        // Register sync untuk ID 10: 10 registers (NoReg 18 s/d 27)
        $kode108Clean = '132010101001';
        $tahun = $a10->tahun_perolehan;
        
        $maxRegInt = AstapRegister::where('tahun_perolehan', $tahun)
            ->where('astap_id', '!=', 10)
            ->whereHas('astap', fn($sq) => $sq->where('jenis_astap_id', $a10->jenis_astap_id))
            ->max('no_register_int') ?? 0;
        
        $runningReg = (int)$maxRegInt;
        
        $existingRegs = $a10->registers()->orderBy('id')->get();
        for ($i = 0; $i < 10; $i++) {
            $runningReg++;
            $noRegStr = str_pad($runningReg, 7, '0', STR_PAD_LEFT);
            $nibar = "1201351102000000280000{$tahun}{$kode108Clean}{$noRegStr}";
            
            if ($i < $existingRegs->count()) {
                $reg = $existingRegs[$i];
                $reg->tahun_perolehan = $tahun;
                $reg->no_register_int = $runningReg;
                $reg->no_register = $nibar;
                $reg->nibar = $nibar;
                $reg->qr_code_path = "/scan/{$nibar}";
                $reg->ruang_pemegang = 'Depo Farmasi & Bedah Sentral';
                $reg->kondisi = 'Baik';
                $reg->save();
            } else {
                AstapRegister::create([
                    'astap_id' => 10,
                    'tahun_perolehan' => $tahun,
                    'no_register_int' => $runningReg,
                    'no_register' => $nibar,
                    'nibar' => $nibar,
                    'qr_code_path' => "/scan/{$nibar}",
                    'ruang_pemegang' => 'Depo Farmasi & Bedah Sentral',
                    'kondisi' => 'Baik',
                    'status' => 'Tersedia'
                ]);
            }
        }
        echo "ASTAP 10 fixed successfully: 10 Unit, 10 Registers (NoReg " . ($maxRegInt + 1) . " to {$runningReg})\n";
    }

    // 2. Perbaiki ASTAP ID 11
    $a11 = Astap::find(11);
    if ($a11) {
        $a11->satuan = 'Unit';
        $a11->is_extracomtable = 0; // 350.000 >= 300.000 atau intrakom
        
        $a11->spesifikasi_json = [
            'jumlah_anggaran' => (float)$a11->jumlah_anggaran,
            'merk' => 'Crown Baby',
            'type' => 'CR-20 Analog',
            'bahan' => 'Plastik ABS & Pegas Baja',
            'no_pabrik' => 'CRW-2026-01',
            'kondisi' => 'Baik',
            'mesin_items' => [
                [
                    'mesin_nama_barang' => $a11->nama_barang,
                    'mesin_kode_barang' => '1.3.2.03.03.10.006',
                    'mesin_uraian_barang' => 'Timbangan Bbi Capasitas 15 Kg ( Timbangan Bayi )',
                    'mesin_jumlah_barang' => 3,
                    'mesin_satuan' => 'Unit',
                    'mesin_nilai_satuan' => 350000,
                    'mesin_administrasi_proyek' => 0,
                    'mesin_total_realisasi' => 1050000,
                    'mesin_merk' => 'Crown Baby',
                    'mesin_type' => 'CR-20 Analog',
                    'mesin_ukuran' => '15 Kg',
                    'mesin_bahan' => 'Plastik ABS & Pegas Baja',
                    'mesin_nomor_pabrik' => 'CRW-2026-01',
                    'mesin_nomor_rangka' => '-',
                    'mesin_nomor_mesin' => '-',
                    'mesin_nomor_polisi' => '-',
                    'mesin_nomor_bpkb' => '-',
                    'mesin_kondisi' => 'Baik',
                    'ruang_pemegang_mesin' => 'Pav. Dahlia',
                    'is_extracom' => false
                ],
                [
                    'mesin_nama_barang' => $a11->nama_barang,
                    'mesin_kode_barang' => '1.3.2.03.03.10.006',
                    'mesin_uraian_barang' => 'Timbangan Bbi Capasitas 15 Kg ( Timbangan Bayi )',
                    'mesin_jumlah_barang' => 2,
                    'mesin_satuan' => 'Unit',
                    'mesin_nilai_satuan' => 350000,
                    'mesin_administrasi_proyek' => 0,
                    'mesin_total_realisasi' => 700000,
                    'mesin_merk' => 'Crown Baby',
                    'mesin_type' => 'CR-20 Analog',
                    'mesin_ukuran' => '15 Kg',
                    'mesin_bahan' => 'Plastik ABS & Pegas Baja',
                    'mesin_nomor_pabrik' => 'CRW-2026-02',
                    'mesin_nomor_rangka' => '-',
                    'mesin_nomor_mesin' => '-',
                    'mesin_nomor_polisi' => '-',
                    'mesin_nomor_bpkb' => '-',
                    'mesin_kondisi' => 'Kurang Baik',
                    'ruang_pemegang_mesin' => 'Paviliun Anak & Perinatologi',
                    'is_extracom' => false
                ]
            ]
        ];
        $a11->save();

        // Update registers NIBAR dengan kode 108 yang benar (132030310006)
        $kode108Clean = '132030310006';
        $tahun = $a11->tahun_perolehan;
        $regs11 = $a11->registers()->orderBy('id')->get();
        foreach ($regs11 as $idx => $r) {
            $regNum = $idx + 1;
            $noRegStr = str_pad($regNum, 7, '0', STR_PAD_LEFT);
            $nibar = "1201351102000000280000{$tahun}{$kode108Clean}{$noRegStr}";
            $r->no_register_int = $regNum;
            $r->no_register = $nibar;
            $r->nibar = $nibar;
            $r->qr_code_path = "/scan/{$nibar}";
            $r->save();
        }
        echo "ASTAP 11 fixed successfully: 5 Unit, NIBAR updated to {$kode108Clean}\n";
    }

    // 3. Periksa ASTAP Peralatan & Mesin lainnya jika belum punya mesin_items (misal ID 1, ID 2, ID 3)
    $otherMesin = Astap::whereIn('id', [1, 2, 3])->get();
    foreach ($otherMesin as $om) {
        $spec = $om->spesifikasi_json ?? [];
        if (!isset($spec['mesin_items']) || empty($spec['mesin_items'])) {
            $kode108 = $om->jenisAstap ? $om->jenisAstap->sub_sub_rincian_objek : '1.3.2.00.00.00.000';
            $firstReg = $om->registers()->first();
            $ruang = $firstReg ? $firstReg->ruang_pemegang : '-';
            $spec['mesin_items'] = [
                [
                    'mesin_nama_barang' => $om->nama_barang,
                    'mesin_kode_barang' => $kode108,
                    'mesin_uraian_barang' => $om->nama_barang,
                    'mesin_jumlah_barang' => (int)$om->jumlah_volume,
                    'mesin_satuan' => $om->satuan ?? 'Unit',
                    'mesin_nilai_satuan' => (float)$om->harga_satuan,
                    'mesin_administrasi_proyek' => (float)$om->biaya_administrasi_proyek,
                    'mesin_total_realisasi' => (float)$om->total_realisasi,
                    'mesin_merk' => $spec['merk'] ?? '-',
                    'mesin_type' => $spec['type'] ?? '-',
                    'mesin_ukuran' => $spec['ukuran'] ?? '-',
                    'mesin_bahan' => $spec['bahan'] ?? '-',
                    'mesin_nomor_pabrik' => $spec['no_pabrik'] ?? '-',
                    'mesin_nomor_rangka' => $spec['no_rangka'] ?? '-',
                    'mesin_nomor_mesin' => $spec['no_mesin'] ?? '-',
                    'mesin_nomor_polisi' => $spec['no_polisi'] ?? '-',
                    'mesin_nomor_bpkb' => $spec['no_bpkb'] ?? '-',
                    'mesin_kondisi' => $spec['kondisi'] ?? 'Baik',
                    'ruang_pemegang_mesin' => $ruang,
                    'is_extracom' => (bool)$om->is_extracom
                ]
            ];
            $om->spesifikasi_json = $spec;
            $om->save();
            echo "ASTAP {$om->id} ({$om->nama_barang}) upgraded with mesin_items.\n";
        }
    }

    DB::commit();
    echo "ALL UPDATES COMPLETED SUCCESSFULLY!\n";
} catch (\Exception $e) {
    DB::rollBack();
    echo "ERROR: " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n";
}
