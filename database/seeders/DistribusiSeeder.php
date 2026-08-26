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
        // Kosongkan tabel distribusi_items dan distribusis
        DistribusiItem::query()->delete();
        Distribusi::query()->delete();

        // 1. Helper Resolve Unit
        $unitRekamMedis = Unit::where('nama', 'LIKE', '%Rekam Medik%')->orWhere('nama', 'LIKE', '%Rekam Medis%')->first() ?? Unit::first();
        $unitIgd = Unit::where('nama', 'LIKE', '%IGD%')->orWhere('nama', 'LIKE', '%Gawat Darurat%')->first() ?? Unit::first();
        $unitDahlia = Unit::where('nama', 'LIKE', '%Dahlia%')->orWhere('nama', 'LIKE', '%Anak%')->first() ?? Unit::first();
        $unitPerpus = Unit::where('nama', 'LIKE', '%Perpustakaan%')->orWhere('nama', 'LIKE', '%Informasi%')->orWhere('nama', 'LIKE', '%Perencanaan%')->first() ?? Unit::first();
        $unitMelati = Unit::where('nama', 'LIKE', '%Melati%')->orWhere('nama', 'LIKE', '%Rawat Inap%')->first() ?? Unit::first();

        // 2. Ambil Master Data ASTAP
        $astapLaptop = Astap::where('nama_barang', 'LIKE', '%Laptop%')->first();
        $astapGunting = Astap::where('nama_barang', 'LIKE', '%Gunting%')->first();
        $astapTimbangan = Astap::where('nama_barang', 'LIKE', '%Timbangan%')->first();
        $astapBuku = Astap::where('nama_barang', 'LIKE', '%Buku%')->orWhere('nama_barang', 'LIKE', '%Jurnal%')->first();

        // Transaksi 1: Distribusi Laptop RME ke Unit Rekam Medis (Telah Diterima)
        if ($unitRekamMedis && $astapLaptop) {
            $dist1 = Distribusi::create([
                'kode'               => 'DST-2026-001',
                'bast_nomor'         => '028 / 034 / 430.10.7 / 2026',
                'tanggal_distribusi' => '2026-06-16',
                'unit_id'            => $unitRekamMedis->id,
                'status'             => 'Telah Diterima',
                'signed'             => true,
                'tgl_signed'         => '16/06/2026 10:15 WIB',
                'keterangan'         => 'Pengadaan Laptop Operasional RME SatuSehat BLUD 2026',
            ]);

            $nibarsLaptop = AstapRegister::where('astap_id', $astapLaptop->id)->take(2)->pluck('nibar')->toArray();

            DistribusiItem::create([
                'distribusi_id' => $dist1->id,
                'astap_id'      => $astapLaptop->id,
                'qty'           => count($nibarsLaptop) ?: 2,
                'nibar_list'    => $nibarsLaptop,
                'kondisi'       => 'Baik',
                'keterangan'    => 'Penempatan Tim Integrasi SatuSehat & Pelaporan Medis',
            ]);

            if (!empty($nibarsLaptop)) {
                AstapRegister::whereIn('nibar', $nibarsLaptop)->update([
                    'unit_id'        => $unitRekamMedis->id,
                    'ruang_pemegang' => $unitRekamMedis->nama,
                    'kondisi'        => 'Baik',
                    'status'         => 'Tidak Tersedia',
                ]);
            }
        }

        // Transaksi 2: Distribusi Gunting Jahitan Ekstrakomtabel ke IGD (Telah Diterima)
        if ($unitIgd && $astapGunting) {
            $dist2 = Distribusi::create([
                'kode'               => 'DST-2026-002',
                'bast_nomor'         => '031 / 034 / 430.10.7 / 2026',
                'tanggal_distribusi' => '2026-07-02',
                'unit_id'            => $unitIgd->id,
                'status'             => 'Telah Diterima',
                'signed'             => true,
                'tgl_signed'         => '02/07/2026 13:45 WIB',
                'keterangan'         => 'Distribusi Perlengkapan Tindakan Medis IGD & Bedah Minor',
            ]);

            $nibarsGunting = AstapRegister::where('astap_id', $astapGunting->id)->take(2)->pluck('nibar')->toArray();

            DistribusiItem::create([
                'distribusi_id' => $dist2->id,
                'astap_id'      => $astapGunting->id,
                'qty'           => count($nibarsGunting) ?: 2,
                'nibar_list'    => $nibarsGunting,
                'kondisi'       => 'Baik',
                'keterangan'    => 'Ruang Tindakan Cito IGD',
            ]);

            if (!empty($nibarsGunting)) {
                AstapRegister::whereIn('nibar', $nibarsGunting)->update([
                    'unit_id'        => $unitIgd->id,
                    'ruang_pemegang' => $unitIgd->nama,
                    'kondisi'        => 'Baik',
                    'status'         => 'Tidak Tersedia',
                ]);
            }
        }

        // Transaksi 3: Distribusi Timbangan Bayi ke Paviliun Dahlia (Telah Diterima)
        if ($unitDahlia && $astapTimbangan) {
            $dist3 = Distribusi::create([
                'kode'               => 'DST-2026-003',
                'bast_nomor'         => '034 / 034 / 430.10.7 / 2026',
                'tanggal_distribusi' => '2026-07-15',
                'unit_id'            => $unitDahlia->id,
                'status'             => 'Telah Diterima',
                'signed'             => true,
                'tgl_signed'         => '15/07/2026 09:30 WIB',
                'keterangan'         => 'Penempatan Alat Ukur Timbangan Bayi Ruang Neonatus & Poli Anak',
            ]);

            $nibarsTimbangan = AstapRegister::where('astap_id', $astapTimbangan->id)->take(3)->pluck('nibar')->toArray();

            DistribusiItem::create([
                'distribusi_id' => $dist3->id,
                'astap_id'      => $astapTimbangan->id,
                'qty'           => count($nibarsTimbangan) ?: 3,
                'nibar_list'    => $nibarsTimbangan,
                'kondisi'       => 'Baik',
                'keterangan'    => 'Ruang Perinatologi & Neonatus Dahlia',
            ]);

            if (!empty($nibarsTimbangan)) {
                AstapRegister::whereIn('nibar', $nibarsTimbangan)->update([
                    'unit_id'        => $unitDahlia->id,
                    'ruang_pemegang' => $unitDahlia->nama,
                    'kondisi'        => 'Baik',
                    'status'         => 'Tidak Tersedia',
                ]);
            }
        }

        // Transaksi 4: Distribusi Buku Jurnal Medis ke Bagian Perpustakaan & Litbang (Telah Diterima)
        if ($unitPerpus && $astapBuku) {
            $dist4 = Distribusi::create([
                'kode'               => 'DST-2026-004',
                'bast_nomor'         => '039 / 034 / 430.10.7 / 2026',
                'tanggal_distribusi' => '2026-08-01',
                'unit_id'            => $unitPerpus->id,
                'status'             => 'Telah Diterima',
                'signed'             => true,
                'tgl_signed'         => '01/08/2026 14:00 WIB',
                'keterangan'         => 'Koleksi Buku Referensi Klinis Dokter Spesialis RSUD',
            ]);

            $nibarsBuku = AstapRegister::where('astap_id', $astapBuku->id)->take(3)->pluck('nibar')->toArray();

            DistribusiItem::create([
                'distribusi_id' => $dist4->id,
                'astap_id'      => $astapBuku->id,
                'qty'           => count($nibarsBuku) ?: 3,
                'nibar_list'    => $nibarsBuku,
                'kondisi'       => 'Baik',
                'keterangan'    => 'Rak Referensi Medis & Farmakologi Lantai 2',
            ]);

            if (!empty($nibarsBuku)) {
                AstapRegister::whereIn('nibar', $nibarsBuku)->update([
                    'unit_id'        => $unitPerpus->id,
                    'ruang_pemegang' => $unitPerpus->nama,
                    'kondisi'        => 'Baik',
                    'status'         => 'Tidak Tersedia',
                ]);
            }
        }

        // Transaksi 5: Distribusi Laptop ke Paviliun Melati (Status: Dalam Pengiriman)
        if ($unitMelati && $astapLaptop) {
            $dist5 = Distribusi::create([
                'kode'               => 'DST-2026-005',
                'bast_nomor'         => '042 / 034 / 430.10.7 / 2026',
                'tanggal_distribusi' => '2026-08-20',
                'unit_id'            => $unitMelati->id,
                'status'             => 'Dalam Pengiriman',
                'signed'             => false,
                'tgl_signed'         => null,
                'keterangan'         => 'Pengiriman Laptop SIMRS Ruang Perawat Paviliun Melati',
            ]);

            // Ambil register laptop ke-3
            $nibarsLaptopMelati = AstapRegister::where('astap_id', $astapLaptop->id)->skip(2)->take(1)->pluck('nibar')->toArray();

            DistribusiItem::create([
                'distribusi_id' => $dist5->id,
                'astap_id'      => $astapLaptop->id,
                'qty'           => 1,
                'nibar_list'    => $nibarsLaptopMelati,
                'kondisi'       => 'Baik',
                'keterangan'    => 'Meja Nurse Station Paviliun Melati',
            ]);

            if (!empty($nibarsLaptopMelati)) {
                AstapRegister::whereIn('nibar', $nibarsLaptopMelati)->update([
                    'unit_id'        => $unitMelati->id,
                    'ruang_pemegang' => $unitMelati->nama,
                    'kondisi'        => 'Baik',
                    'status'         => 'Tidak Tersedia',
                ]);
            }
        }
    }
}
