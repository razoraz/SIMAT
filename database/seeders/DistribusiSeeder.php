<?php

namespace Database\Seeders;

use App\Models\Distribusi;
use App\Models\DistribusiItem;
use App\Models\DistribusiItemRegister;
use App\Models\Astap;
use App\Models\AstapRegister;
use App\Models\Unit;
use Illuminate\Database\Seeder;

class DistribusiSeeder extends Seeder
{
    public function run(): void
    {
        // Kosongkan tabel (cascade ke distribusi_items & distribusi_item_registers)
        Distribusi::query()->delete();

        // 1. Resolve Unit
        $unitRekamMedis = Unit::where('nama', 'LIKE', '%Rekam Medik%')->orWhere('nama', 'LIKE', '%Rekam Medis%')->first() ?? Unit::first();
        $unitIgd        = Unit::where('nama', 'LIKE', '%IGD%')->orWhere('nama', 'LIKE', '%Gawat Darurat%')->first() ?? Unit::first();
        $unitDahlia     = Unit::where('nama', 'LIKE', '%Dahlia%')->orWhere('nama', 'LIKE', '%Anak%')->first() ?? Unit::first();
        $unitPerpus     = Unit::where('nama', 'LIKE', '%Perpustakaan%')->orWhere('nama', 'LIKE', '%Perencanaan%')->first() ?? Unit::first();
        $unitMelati     = Unit::where('nama', 'LIKE', '%Melati%')->orWhere('nama', 'LIKE', '%Rawat Inap%')->first() ?? Unit::first();

        // 2. Resolve ASTAP
        $astapLaptop    = Astap::where('nama_barang', 'LIKE', '%Laptop%')->first();
        $astapGunting   = Astap::where('nama_barang', 'LIKE', '%Gunting%')->first();
        $astapTimbangan = Astap::where('nama_barang', 'LIKE', '%Timbangan%')->first();
        $astapBuku      = Astap::where('nama_barang', 'LIKE', '%Buku%')->orWhere('nama_barang', 'LIKE', '%Jurnal%')->first();

        // ── Helper: buat item + pivot registers (FK integer, bukan string NIBAR) ──
        $createItem = function($distribusi, $astap, $qty, $registers, $unit, $keterangan = null) {
            if (!$astap || $registers->isEmpty()) return;

            $item = DistribusiItem::create([
                'distribusi_id' => $distribusi->id,
                'astap_id'      => $astap->id,
                'qty'           => $registers->count() ?: $qty,
                // kondisi & nibar TIDAK disimpan di sini — dibaca live dari astap_registers via FK
            ]);

            foreach ($registers as $reg) {
                DistribusiItemRegister::create([
                    'distribusi_item_id' => $item->id,
                    'astap_register_id'  => $reg->id,   // FK integer ke astap_registers.id
                ]);
                // Update astap_registers: ruang & unit (kondisi TIDAK diubah, tetap di astap_registers)
                $reg->update([
                    'unit_id'        => $unit->id,
                    'ruang_pemegang' => $unit->nama,
                    'status'         => 'Tidak Tersedia',
                ]);
            }
        };

        // ── Transaksi 1: Laptop → Rekam Medis ──
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
            $regsLaptop = AstapRegister::where('astap_id', $astapLaptop->id)->take(2)->get();
            $createItem($dist1, $astapLaptop, 2, $regsLaptop, $unitRekamMedis, 'Penempatan Tim Integrasi SatuSehat & Pelaporan Medis');
        }

        // ── Transaksi 2: Gunting → IGD ──
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
            $regsGunting = AstapRegister::where('astap_id', $astapGunting->id)->take(2)->get();
            $createItem($dist2, $astapGunting, 2, $regsGunting, $unitIgd, 'Ruang Tindakan Cito IGD');
        }

        // ── Transaksi 3: Timbangan Bayi → Dahlia ──
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
            $regsTimbangan = AstapRegister::where('astap_id', $astapTimbangan->id)->take(3)->get();
            $createItem($dist3, $astapTimbangan, 3, $regsTimbangan, $unitDahlia, 'Ruang Perinatologi & Neonatus Dahlia');
        }

        // ── Transaksi 4: Buku Jurnal → Perpustakaan ──
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
            $regsBuku = AstapRegister::where('astap_id', $astapBuku->id)->take(3)->get();
            $createItem($dist4, $astapBuku, 3, $regsBuku, $unitPerpus, 'Rak Referensi Medis & Farmakologi Lantai 2');
        }

        // ── Transaksi 5: Laptop → Melati (Dalam Pengiriman) ──
        if ($unitMelati && $astapLaptop) {
            $dist5 = Distribusi::create([
                'kode'               => 'DST-2026-005',
                'bast_nomor'         => '042 / 034 / 430.10.7 / 2026',
                'tanggal_distribusi' => '2026-08-20',
                'unit_id'            => $unitMelati->id,
                'status'             => 'Dalam Pengiriman',
                'signed'             => false,
                'keterangan'         => 'Pengiriman Laptop SIMRS Ruang Perawat Paviliun Melati',
            ]);
            $regsLaptopMelati = AstapRegister::where('astap_id', $astapLaptop->id)->skip(2)->take(1)->get();
            $createItem($dist5, $astapLaptop, 1, $regsLaptopMelati, $unitMelati, 'Meja Nurse Station Paviliun Melati');
        }
    }
}

