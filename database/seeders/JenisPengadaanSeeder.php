<?php

namespace Database\Seeders;

use App\Models\JenisPengadaan;
use Illuminate\Database\Seeder;

class JenisPengadaanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'program_kode'      => '0.00.01',
                'program_nama'      => 'Program Penunjang Urusan Pemerintah Daerah Kabupaten/kota',
                'kegiatan_kode'     => '0.00.01.2.10',
                'kegiatan_nama'     => 'Peningkatan Pelayanan BLUD',
                'sub_kegiatan_kode' => '0.00.01.2.10.0001',
                'sub_kegiatan_nama' => 'Pelayanan dan Penunjang Pelayanan BLUD',
            ],
            [
                'program_kode'      => '0.00.01',
                'program_nama'      => 'Program Penunjang Urusan Pemerintah Daerah Kabupaten/kota',
                'kegiatan_kode'     => '0.00.01.2.10',
                'kegiatan_nama'     => 'Peningkatan Pelayanan BLUD',
                'sub_kegiatan_kode' => '0.00.01.2.10.0002',
                'sub_kegiatan_nama' => 'Pengadaan Sarana dan Prasarana Pendukung Fasilitas Pelayanan Kesehatan',
            ],
            [
                'program_kode'      => '1.02.02',
                'program_nama'      => 'Program Pemenuhan Upaya Kesehatan Perorangan dan Upaya Kesehatan Masyarakat',
                'kegiatan_kode'     => '1.02.02.2.02',
                'kegiatan_nama'     => 'Penyediaan Fasilitas Pelayanan Kesehatan untuk UKP dan UKM Rujukan',
                'sub_kegiatan_kode' => '1.02.02.2.02.0005',
                'sub_kegiatan_nama' => 'Pembangunan / Renovasi Gedung Rumah Sakit dan Sarana Penunjang',
            ],
            [
                'program_kode'      => '1.02.02',
                'program_nama'      => 'Program Pemenuhan Upaya Kesehatan Perorangan dan Upaya Kesehatan Masyarakat',
                'kegiatan_kode'     => '1.02.02.2.02',
                'kegiatan_nama'     => 'Penyediaan Fasilitas Pelayanan Kesehatan untuk UKP dan UKM Rujukan',
                'sub_kegiatan_kode' => '1.02.02.2.02.0012',
                'sub_kegiatan_nama' => 'Pengadaan Alat Kesehatan / Alat Penunjang Medik Fasilitas Pelayanan Kesehatan',
            ],
            [
                'program_kode'      => '1.02.03',
                'program_nama'      => 'Program Peningkatan Kapasitas Sumber Daya Manusia Kesehatan',
                'kegiatan_kode'     => '1.02.03.2.01',
                'kegiatan_nama'     => 'Pengembangan Mutu dan Akreditasi Fasilitas Pelayanan Kesehatan',
                'sub_kegiatan_kode' => '1.02.03.2.01.0003',
                'sub_kegiatan_nama' => 'Pengadaan Sistem Informasi Kesehatan & Software Manajemen SIMRS',
            ],
            [
                'program_kode'      => '1.02.03',
                'program_nama'      => 'Program Peningkatan Kapasitas Sumber Daya Manusia Kesehatan',
                'kegiatan_kode'     => '1.02.03.2.02',
                'kegiatan_nama'     => 'Pengembangan SDM dan Sumber Daya Rumah Sakit',
                'sub_kegiatan_kode' => '1.02.03.2.02.0001',
                'sub_kegiatan_nama' => 'Penyelenggaraan Pelatihan dan Peningkatan Kapasitas Tenaga Kesehatan',
            ],
        ];

        foreach ($data as $item) {
            JenisPengadaan::updateOrCreate(
                ['sub_kegiatan_kode' => $item['sub_kegiatan_kode']],
                $item
            );
        }
    }
}
