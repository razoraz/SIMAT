<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Astap;
use App\Models\MutasiEksternal;
use App\Models\MutasiEksternalRegister;
use App\Models\AstapRegister;

class DummyMutasiKeluarSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ambil beberapa aset ASTAP yang valid dan aktif
        $astaps = Astap::with('registers')->where('is_deleted', 0)->take(3)->get();

        if ($astaps->isEmpty()) {
            return;
        }

        // Dummy Data 1: Transfer Laptop / Komputer ke Dinas Kesehatan
        $a1 = $astaps->get(0);
        if ($a1) {
            $m1 = MutasiEksternal::create([
                'astap_id'            => $a1->id,
                'nomor_bamb'          => '000.2.3.2/BAST-KLR-001/430.10.7/2026',
                'tanggal_mutasi'      => '2026-08-15',
                'jenis_mutasi'        => 'Transfer Antar-OPD',
                'tipe'                => 'keluar',
                'opd_asal'            => 'RSUD Dr. H. Koesnadi',
                'opd_tujuan'          => 'Dinas Kesehatan Kab. Bondowoso',
                'unit_id'             => $a1->unit_id,
                'ruangan_tujuan'      => 'Instalasi SIM-RS / IT',
                'pj_asal_nama'        => 'dr. H. Yus Priyatna, Sp.P',
                'pj_asal_nip'         => '196904121999031004',
                'pj_asal_jabatan'     => 'Direktur RSUD Dr. H. Koesnadi',
                'pj_tujuan_nama'      => 'dr. Mohammad Imron, M.M.Kes',
                'pj_tujuan_nip'       => '197205102002121003',
                'pj_tujuan_jabatan'   => 'Kepala Dinas Kesehatan Kab. Bondowoso',
                'nomor_sk_dasar'      => 'SK.BUP/028/430/2026 tentang Pemindahtanganan BMD Antar-OPD',
                'status'              => 'Disahkan (Selesai)',
                'jumlah_volume'       => 2,
                'satuan'              => $a1->satuan ?: 'Unit',
                'nilai_perolehan'     => 30000000,
                'kondisi'             => 'Baik',
                'alasan_mutasi'       => 'Pemindahtanganan 2 unit aset penunjang operasional layanan ke Dinas Kesehatan Kab. Bondowoso (Koreksi Baris 42 / Kolom 13).',
                'alamat_instansi'     => 'Jl. Imam Bonjol No. 13, Bondowoso',
                'user_id'             => 1,
                'is_deleted'          => 0,
            ]);

            // Link registers jika ada
            if ($a1->registers->isNotEmpty()) {
                foreach ($a1->registers->take(2) as $reg) {
                    MutasiEksternalRegister::create([
                        'mutasi_eksternal_id' => $m1->id,
                        'astap_register_id'   => $reg->id,
                        'kondisi'             => 'Baik',
                        'catatan'             => 'Diserahkan dalam kondisi baik dan berfungsi normal',
                    ]);
                }
            }
        }

        // Dummy Data 2: Pengalihan Pompa Submersible / Peralatan ke BPKAD
        $a2 = $astaps->get(1);
        if ($a2) {
            $m2 = MutasiEksternal::create([
                'astap_id'            => $a2->id,
                'nomor_bamb'          => '000.2.3.2/BAST-KLR-002/430.10.7/2026',
                'tanggal_mutasi'      => '2026-09-02',
                'jenis_mutasi'        => 'Transfer Antar-OPD',
                'tipe'                => 'keluar',
                'opd_asal'            => 'RSUD Dr. H. Koesnadi',
                'opd_tujuan'          => 'BPKAD Kab. Bondowoso',
                'unit_id'             => $a2->unit_id,
                'ruangan_tujuan'      => 'IPSRS / Sarana Prasarana',
                'pj_asal_nama'        => 'dr. H. Yus Priyatna, Sp.P',
                'pj_asal_nip'         => '196904121999031004',
                'pj_asal_jabatan'     => 'Direktur RSUD Dr. H. Koesnadi',
                'pj_tujuan_nama'      => 'Drs. Taufan Restuanto, M.Si',
                'pj_tujuan_nip'       => '197003151996021001',
                'pj_tujuan_jabatan'   => 'Kepala BPKAD Kab. Bondowoso',
                'nomor_sk_dasar'      => 'SK.BUP/034/430/2026 tentang Mutasi Aset Antar-Perangkat Daerah',
                'status'              => 'Disahkan (Selesai)',
                'jumlah_volume'       => 1,
                'satuan'              => $a2->satuan ?: 'Unit',
                'nilai_perolehan'     => (float) ($a2->total_realisasi ?: 42501900),
                'kondisi'             => 'Baik',
                'alasan_mutasi'       => 'Pengalihan aset sarana penunjang ke BPKAD Kabupaten Bondowoso dalam rangka penataan BMD Pemkab.',
                'alamat_instansi'     => 'Jl. Letnan Karsono No. 2, Bondowoso',
                'user_id'             => 1,
                'is_deleted'          => 0,
            ]);

            if ($a2->registers->isNotEmpty()) {
                foreach ($a2->registers->take(1) as $reg) {
                    MutasiEksternalRegister::create([
                        'mutasi_eksternal_id' => $m2->id,
                        'astap_register_id'   => $reg->id,
                        'kondisi'             => 'Baik',
                        'catatan'             => 'Penyerahan fisik dan dokumen kelengkapan aset',
                    ]);
                }
            }
        }

        // Dummy Data 3: Peminjaman / Transfer ke Dinas Sosial Kab. Bondowoso
        $a3 = $astaps->get(2);
        if ($a3) {
            $m3 = MutasiEksternal::create([
                'astap_id'            => $a3->id,
                'nomor_bamb'          => '000.2.3.2/BAST-KLR-003/430.10.7/2026',
                'tanggal_mutasi'      => '2026-09-20',
                'jenis_mutasi'        => 'Transfer Antar-OPD',
                'tipe'                => 'keluar',
                'opd_asal'            => 'RSUD Dr. H. Koesnadi',
                'opd_tujuan'          => 'Dinas Sosial Kab. Bondowoso',
                'unit_id'             => $a3->unit_id,
                'ruangan_tujuan'      => 'Gudang Logistik & Inventaris',
                'pj_asal_nama'        => 'dr. H. Yus Priyatna, Sp.P',
                'pj_asal_nip'         => '196904121999031004',
                'pj_asal_jabatan'     => 'Direktur RSUD Dr. H. Koesnadi',
                'pj_tujuan_nama'      => 'Anisatul Hamidah, M.Si',
                'pj_tujuan_nip'       => '197508242000032002',
                'pj_tujuan_jabatan'   => 'Kepala Dinas Sosial Kab. Bondowoso',
                'nomor_sk_dasar'      => 'SK.BUP/041/430/2026 tentang Distribusi Peralatan Daerah',
                'status'              => 'Disahkan (Selesai)',
                'jumlah_volume'       => 1,
                'satuan'              => $a3->satuan ?: 'Unit',
                'nilai_perolehan'     => 15000000,
                'kondisi'             => 'Baik',
                'alasan_mutasi'       => 'Penyaluran dan transfer aset penunjang operasional pelayanan sosial ke Dinsos Bondowoso.',
                'alamat_instansi'     => 'Jl. Mastrip No. 45, Bondowoso',
                'user_id'             => 1,
                'is_deleted'          => 0,
            ]);

            if ($a3->registers->isNotEmpty()) {
                foreach ($a3->registers->take(1) as $reg) {
                    MutasiEksternalRegister::create([
                        'mutasi_eksternal_id' => $m3->id,
                        'astap_register_id'   => $reg->id,
                        'kondisi'             => 'Baik',
                        'catatan'             => 'Diserahkan langsung dan dicatat di buku inventaris',
                    ]);
                }
            }
        }
    }
}
