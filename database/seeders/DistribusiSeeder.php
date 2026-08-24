<?php

namespace Database\Seeders;

use App\Models\Distribusi;
use App\Models\DistribusiItem;
use App\Models\Astap;
use App\Models\AstapRegister;
use App\Models\Unit;
use Illuminate\Database\Seeder;

class DistribusiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $foUnit = Unit::where('nama', 'LIKE', '%Front Office%')->orWhere('nama', 'LIKE', '%Rawat Inap%')->first() ?? Unit::first();
        $igdUnit = Unit::where('nama', 'LIKE', '%IGD%')->orWhere('nama', 'LIKE', '%Gawat Darurat%')->first() ?? Unit::skip(1)->first() ?? Unit::first();

        // Ambil data master ASTAP
        $astapKasur = Astap::where('nama_barang', 'LIKE', '%Kasur%')->orWhere('kode_108', 'LIKE', '1.3.2%')->first() ?? Astap::first();
        $astapBed = Astap::where('nama_barang', 'LIKE', '%Bed%')->orWhere('nama_barang', 'LIKE', '%Patient%')->first() ?? Astap::skip(1)->first() ?? Astap::first();
        $astapMonitor = Astap::where('nama_barang', 'LIKE', '%Monitor%')->first() ?? Astap::skip(2)->first() ?? Astap::first();

        // 1. Distribusi Transaksi 1 (Front Office)
        if ($foUnit && $astapKasur) {
            $dist1 = Distribusi::create([
                'kode'               => 'DST-2026-004',
                'bast_nomor'         => '032 / 034 / 430.10.7 / 2026',
                'tanggal_distribusi' => '2026-08-13',
                'unit_id'            => $foUnit->id, // PK Unit
                'status'             => 'Telah Diterima',
                'signed'             => true,
                'tgl_signed'         => '13/08/2026 11:30 WIB',
                'keterangan'         => 'BLUD-2024 u/Petugas Jaga FO R.Inap',
            ]);

            // Ambil NIBAR langsung dari data ASTAP
            $nibarsKasur = AstapRegister::where('astap_id', $astapKasur->id)->take(2)->pluck('nibar')->toArray();

            // Item 1
            DistribusiItem::create([
                'distribusi_id' => $dist1->id,
                'astap_id'      => $astapKasur->id, // PK ASTAP
                'qty'           => 2,
                'nibar_list'    => $nibarsKasur, // Array NIBAR dari data ASTAP
                'kondisi'       => 'Baik',
                'keterangan'    => 'BLUD-2024 u/Petugas Jaga FO R.Inap',
            ]);

            if ($astapBed) {
                $nibarsBed = AstapRegister::where('astap_id', $astapBed->id)->take(2)->pluck('nibar')->toArray();
                DistribusiItem::create([
                    'distribusi_id' => $dist1->id,
                    'astap_id'      => $astapBed->id, // PK ASTAP
                    'qty'           => 2,
                    'nibar_list'    => $nibarsBed,
                    'kondisi'       => 'Baik',
                    'keterangan'    => 'Ruang Rawat Observasi FO',
                ]);
            }
        }

        // 2. Distribusi Transaksi 2 (IGD)
        if ($igdUnit && $astapMonitor) {
            $dist2 = Distribusi::create([
                'kode'               => 'DST-2026-008',
                'bast_nomor'         => '034 / 034 / 430.10.7 / 2026',
                'tanggal_distribusi' => '2026-08-14',
                'unit_id'            => $igdUnit->id, // PK Unit
                'status'             => 'Telah Diterima',
                'signed'             => true,
                'tgl_signed'         => '14/08/2026 14:15 WIB',
                'keterangan'         => 'Pengadaan DAK Kesehatan 2024 u/IGD Kritis',
            ]);

            $nibarsMonitor = AstapRegister::where('astap_id', $astapMonitor->id)->take(4)->pluck('nibar')->toArray();

            DistribusiItem::create([
                'distribusi_id' => $dist2->id,
                'astap_id'      => $astapMonitor->id, // PK ASTAP
                'qty'           => 4,
                'nibar_list'    => $nibarsMonitor,
                'kondisi'       => 'Baik',
                'keterangan'    => 'Zona Kritis Resusitasi IGD',
            ]);
        }
    }
}
