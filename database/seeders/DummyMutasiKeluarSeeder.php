<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Astap;
use App\Models\AstapRegister;
use App\Models\AstapReklas;
use App\Models\JenisReklasifikasi;
use App\Models\MutasiEksternal;
use App\Models\MutasiEksternalRegister;

class DummyMutasiKeluarSeeder extends Seeder
{
    public function run(): void
    {
        // Cari baris penyeimbang Baris 42 (Koreksi Lain-Lain) di tabel jenis_reklasifikasis
        $korLainRow = JenisReklasifikasi::where('kode_prefix', 'KOR_LAIN')->first();

        // 1. Ambil beberapa aset ASTAP yang valid dan aktif
        $astaps = Astap::with('registers')->where('is_deleted', 0)->take(3)->get();

        if ($astaps->isEmpty()) {
            return;
        }

        // Hapus dummy mutasi keluar sebelumnya agar idempotens / bersih
        $existingDummyBambs = [
            '000.2.3.2/BAST-KLR-001/430.10.7/2026',
            '000.2.3.2/BAST-KLR-002/430.10.7/2026',
            '000.2.3.2/BAST-KLR-003/430.10.7/2026',
        ];
        MutasiEksternal::whereIn('nomor_bamb', $existingDummyBambs)->delete();
        AstapReklas::whereIn('nomor_ba_reklas', $existingDummyBambs)->delete();

        // Dummy Data 1: Transfer ke Dinas Kesehatan Kab. Bondowoso
        $a1 = $astaps->get(0);
        if ($a1) {
            $nomorBast1 = '000.2.3.2/BAST-KLR-001/430.10.7/2026';
            $nilai1 = min((float)$a1->total_realisasi, 30000000) ?: 30000000;

            $m1 = MutasiEksternal::create([
                'astap_id'            => $a1->id,
                'nomor_bamb'          => $nomorBast1,
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
                'jumlah_volume'       => max(1, (int)$a1->jumlah_volume),
                'satuan'              => $a1->satuan ?: 'Unit',
                'nilai_perolehan'     => $nilai1,
                'kondisi'             => 'Baik',
                'alasan_mutasi'       => 'Pemindahtanganan aset operasional penunjang layanan ke Dinas Kesehatan Kab. Bondowoso (Koreksi Baris 42 / Kolom 13).',
                'alamat_instansi'     => 'Jl. Imam Bonjol No. 13, Bondowoso',
                'user_id'             => 1,
                'is_deleted'          => 0,
            ]);

            // Tandai aset ASTAP RSUD
            $a1->update([
                'is_reklas'    => 1,
                'jenis_reklas' => 'MUTASI_EKSTERNAL',
            ]);

            // Link registers dan ubah status agar keluar dari stok ruangan (KIR)
            if ($a1->registers->isNotEmpty()) {
                foreach ($a1->registers as $reg) {
                    MutasiEksternalRegister::create([
                        'mutasi_eksternal_id' => $m1->id,
                        'astap_register_id'   => $reg->id,
                        'kondisi'             => 'Baik',
                        'catatan'             => 'Diserahkan dalam kondisi baik dan berfungsi normal',
                    ]);
                    $reg->update(['status' => 'Mutasi Keluar OPD']);
                }
            }

            // Catat transaksi Reklasifikasi Aset (Baris 42 Koreksi Neraca)
            AstapReklas::create([
                'astap_id'                      => $a1->id,
                'jenis_reklasifikasi_tujuan_id' => $korLainRow?->id,
                'jenis_reklas'                  => 'MUTASI_EKSTERNAL',
                'asal_kib'                      => $a1->category ?: 'KIB B',
                'tujuan_kib'                    => 'KOREKSI',
                'tujuan_kode'                   => 'KOR_LAIN',
                'tujuan_nama'                   => 'Koreksi Lain-Lain (Mutasi Keluar Antar-OPD)',
                'nilai_reklas'                  => $nilai1,
                'tanggal_reklas'                => '2026-08-15',
                'triwulan'                      => 3,
                'tahun'                         => 2026,
                'nomor_ba_reklas'               => $nomorBast1,
                'alasan_reklas'                 => 'Pemindahtanganan aset ke Dinas Kesehatan Kab. Bondowoso (Koreksi Baris 42 / Kolom 13).',
                'user_id'                       => 1,
                'is_deleted'                    => 0,
            ]);
        }

        // Dummy Data 2: Pengalihan ke BPKAD Kab. Bondowoso
        $a2 = $astaps->get(1);
        if ($a2) {
            $nomorBast2 = '000.2.3.2/BAST-KLR-002/430.10.7/2026';
            $nilai2 = (float) ($a2->total_realisasi ?: 42501900);

            $m2 = MutasiEksternal::create([
                'astap_id'            => $a2->id,
                'nomor_bamb'          => $nomorBast2,
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
                'jumlah_volume'       => max(1, (int)$a2->jumlah_volume),
                'satuan'              => $a2->satuan ?: 'Unit',
                'nilai_perolehan'     => $nilai2,
                'kondisi'             => 'Baik',
                'alasan_mutasi'       => 'Pengalihan aset sarana penunjang ke BPKAD Kabupaten Bondowoso dalam rangka penataan BMD Pemkab.',
                'alamat_instansi'     => 'Jl. Letnan Karsono No. 2, Bondowoso',
                'user_id'             => 1,
                'is_deleted'          => 0,
            ]);

            $a2->update([
                'is_reklas'    => 1,
                'jenis_reklas' => 'MUTASI_EKSTERNAL',
            ]);

            if ($a2->registers->isNotEmpty()) {
                foreach ($a2->registers as $reg) {
                    MutasiEksternalRegister::create([
                        'mutasi_eksternal_id' => $m2->id,
                        'astap_register_id'   => $reg->id,
                        'kondisi'             => 'Baik',
                        'catatan'             => 'Penyerahan fisik dan dokumen kelengkapan aset',
                    ]);
                    $reg->update(['status' => 'Mutasi Keluar OPD']);
                }
            }

            AstapReklas::create([
                'astap_id'                      => $a2->id,
                'jenis_reklasifikasi_tujuan_id' => $korLainRow?->id,
                'jenis_reklas'                  => 'MUTASI_EKSTERNAL',
                'asal_kib'                      => $a2->category ?: 'KIB B',
                'tujuan_kib'                    => 'KOREKSI',
                'tujuan_kode'                   => 'KOR_LAIN',
                'tujuan_nama'                   => 'Koreksi Lain-Lain (Mutasi Keluar Antar-OPD)',
                'nilai_reklas'                  => $nilai2,
                'tanggal_reklas'                => '2026-09-02',
                'triwulan'                      => 3,
                'tahun'                         => 2026,
                'nomor_ba_reklas'               => $nomorBast2,
                'alasan_reklas'                 => 'Pengalihan aset sarana penunjang ke BPKAD Kabupaten Bondowoso.',
                'user_id'                       => 1,
                'is_deleted'                    => 0,
            ]);
        }
    }
}
