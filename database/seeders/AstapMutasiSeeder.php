<?php

namespace Database\Seeders;

use App\Models\AstapMutasi;
use App\Models\AstapRegister;
use App\Models\Unit;
use Illuminate\Database\Seeder;

class AstapMutasiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil register yang ada
        $registers = AstapRegister::with('astap', 'unit')->get();
        if ($registers->isEmpty()) {
            return;
        }

        $reg1 = $registers->get(0);
        $reg2 = $registers->get(1) ?? $reg1;
        $reg3 = $registers->get(2) ?? $reg1;
        $reg4 = $registers->get(3) ?? $reg1;

        $units = Unit::all();
        $unitMelati      = $units->firstWhere('nama', 'Pav. Melati') ?? $units->get(0);
        $unitBougenville = $units->firstWhere('nama', 'Pav. Bougenville') ?? $units->get(1);
        $unitIgd         = $units->firstWhere('nama', 'IGD') ?? $units->get(2);
        $unitIpsrs       = $units->firstWhere('nama', 'Inst. IPS RS') ?? $units->get(3);
        $unitAnggrek     = $units->firstWhere('nama', 'Pav. Anggrek') ?? $units->get(4);
        $unitPembekalan  = $units->firstWhere('nama', 'Bagian Rumah Tangga & Inst Perbekalan') ?? $units->get(5);

        $data = [
            [
                'astap_register_id'        => $reg1?->id,
                'nomor_bamb'               => 'MTS-2026-0000001',
                'tanggal_mutasi'           => '2026-08-10',
                'jenis_mutasi'             => 'Ajukan Mutasi',
                'ruangan_asal'             => $unitMelati?->nama ?? 'Pav. Melati',
                'ruangan_tujuan'           => $unitBougenville?->nama ?? 'Pav. Bougenville',
                'penanggung_jawab_asal'    => $unitMelati?->kepala ?? 'FETTY FATKHIYAH, S.ST.M.Si',
                'penanggung_jawab_tujuan'  => $unitBougenville?->kepala ?? 'PUJE ANGGAYUNI, S.Kep.Ns',
                'alasan_mutasi'            => 'Penambahan kapasitas bed cadangan untuk peningkatan pelayanan pasien rawat inap.',
                'catatan_penerima'         => 'Barang telah diterima dan ditempatkan di kamar 3 Pav. Bougenville.',
                'persetujuan_pengirim'     => true,
                'persetujuan_penerima'     => true,
                'persetujuan_admin'        => true,
                'tgl_persetujuan_pengirim' => '2026-08-10 09:15:00',
                'tgl_persetujuan_penerima' => '2026-08-10 14:30:00',
                'tgl_persetujuan_admin'    => '2026-08-11 10:00:00',
                'status'                   => 'Disetujui Admin (Selesai)',
            ],
            [
                'astap_register_id'        => $reg2?->id,
                'nomor_bamb'               => 'MTS-2026-0000002',
                'tanggal_mutasi'           => '2026-08-12',
                'jenis_mutasi'             => 'Perbaikan',
                'ruangan_asal'             => $unitIgd?->nama ?? 'IGD',
                'ruangan_tujuan'           => $unitIpsrs?->nama ?? 'Inst. IPS RS',
                'penanggung_jawab_asal'    => $unitIgd?->kepala ?? 'dr. ADHI SUDARMADJI',
                'penanggung_jawab_tujuan'  => $unitIpsrs?->kepala ?? 'TEKNISI IPRS / SARANA',
                'alasan_mutasi'            => 'Alat mengalami alarm error dan sensor macet, perlu kalibrasi dan perbaikan komponen.',
                'catatan_penerima'         => 'Sedang dalam antrean pengecekan teknis elektromedik.',
                'persetujuan_pengirim'     => true,
                'persetujuan_penerima'     => true,
                'persetujuan_admin'        => false,
                'tgl_persetujuan_pengirim' => '2026-08-12 08:30:00',
                'tgl_persetujuan_penerima' => '2026-08-12 11:20:00',
                'tgl_persetujuan_admin'    => null,
                'status'                   => 'Disetujui 2 Pihak (Menunggu Admin)',
            ],
            [
                'astap_register_id'        => $reg3?->id,
                'nomor_bamb'               => 'MTS-2026-0000003',
                'tanggal_mutasi'           => '2026-08-14',
                'jenis_mutasi'             => 'Minta Mutasi',
                'ruangan_asal'             => $unitAnggrek?->nama ?? 'Pav. Anggrek',
                'ruangan_tujuan'           => $unitMelati?->nama ?? 'Pav. Melati',
                'penanggung_jawab_asal'    => $unitAnggrek?->kepala ?? 'SUTANTI PUSPOSARI, S.Kep.Ns.',
                'penanggung_jawab_tujuan'  => $unitMelati?->kepala ?? 'FETTY FATKHIYAH, S.ST.M.Si',
                'alasan_mutasi'            => 'Pengajuan permintaan mutasi komputer entri resep elektronik untuk percepatan sistem RME Pav. Melati.',
                'catatan_penerima'         => null,
                'persetujuan_pengirim'     => true,
                'persetujuan_penerima'     => false,
                'persetujuan_admin'        => false,
                'tgl_persetujuan_pengirim' => '2026-08-14 13:45:00',
                'tgl_persetujuan_penerima' => null,
                'tgl_persetujuan_admin'    => null,
                'status'                   => 'Menunggu Persetujuan Penerima',
            ],
            [
                'astap_register_id'        => $reg4?->id,
                'nomor_bamb'               => 'MTS-2026-0000004',
                'tanggal_mutasi'           => '2026-08-15',
                'jenis_mutasi'             => 'Pengembalian',
                'ruangan_asal'             => $unitMelati?->nama ?? 'Pav. Melati',
                'ruangan_tujuan'           => $unitPembekalan?->nama ?? 'Bagian Rumah Tangga & Inst Perbekalan',
                'penanggung_jawab_asal'    => $unitMelati?->kepala ?? 'FETTY FATKHIYAH, S.ST.M.Si',
                'penanggung_jawab_tujuan'  => $unitPembekalan?->kepala ?? 'PENGURUS BARANG / ADMIN',
                'alasan_mutasi'            => 'Kondisi rusak berat permanen, frame patah dan aus sehingga tidak ekonomis untuk diperbaiki.',
                'catatan_penerima'         => null,
                'persetujuan_pengirim'     => true,
                'persetujuan_penerima'     => false,
                'persetujuan_admin'        => false,
                'tgl_persetujuan_pengirim' => '2026-08-15 10:00:00',
                'tgl_persetujuan_penerima' => null,
                'tgl_persetujuan_admin'    => null,
                'status'                   => 'Menunggu Persetujuan Penerima',
            ],
        ];

        foreach ($data as $item) {
            AstapMutasi::updateOrCreate(
                ['nomor_bamb' => $item['nomor_bamb']],
                $item
            );
        }
    }
}
