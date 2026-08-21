<?php

namespace Database\Seeders;

use App\Models\RekeningBelanja;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class RekeningBelanjaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Mengisi data awal Rekening Belanja Modal SIPD untuk RSUD Dr. H. Koesnandi.
     */
    public function run(): void
    {
        $this->command->info('Mengosongkan tabel rekening_belanjas...');
        Schema::disableForeignKeyConstraints();
        RekeningBelanja::truncate();
        Schema::enableForeignKeyConstraints();

        $data = [
            // ─── 5.2.01 · Belanja Modal Tanah ─────────────────────────────────────
            [
                'kelompok'      => '5.2.01',
                'nama_kelompok' => 'Belanja Modal Tanah',
                'kode_rek'      => '5.2.01.01.01.0001',
                'nama_belanja'  => 'Belanja Modal Pengadaan Tanah Fasilitas Umum',
            ],
            [
                'kelompok'      => '5.2.01',
                'nama_kelompok' => 'Belanja Modal Tanah',
                'kode_rek'      => '5.2.01.01.01.0002',
                'nama_belanja'  => 'Belanja Modal Pengadaan Tanah Fasilitas Pelayanan Kesehatan',
            ],
            [
                'kelompok'      => '5.2.01',
                'nama_kelompok' => 'Belanja Modal Tanah',
                'kode_rek'      => '5.2.01.01.01.0003',
                'nama_belanja'  => 'Belanja Modal Pengadaan Tanah Bangunan Instalasi & Penunjang RS',
            ],

            // ─── 5.2.02 · Belanja Modal Peralatan dan Mesin ───────────────────────
            [
                'kelompok'      => '5.2.02',
                'nama_kelompok' => 'Belanja Modal Peralatan dan Mesin',
                'kode_rek'      => '5.2.02.01.01.0001',
                'nama_belanja'  => 'Belanja Modal Alat Besar Darat (Generator / Kompresor)',
            ],
            [
                'kelompok'      => '5.2.02',
                'nama_kelompok' => 'Belanja Modal Peralatan dan Mesin',
                'kode_rek'      => '5.2.02.01.03.0005',
                'nama_belanja'  => 'Belanja Modal Pompa',
            ],
            [
                'kelompok'      => '5.2.02',
                'nama_kelompok' => 'Belanja Modal Peralatan dan Mesin',
                'kode_rek'      => '5.2.02.02.01.0001',
                'nama_belanja'  => 'Belanja Modal Kendaraan Dinas Ambulans / Transport',
            ],
            [
                'kelompok'      => '5.2.02',
                'nama_kelompok' => 'Belanja Modal Peralatan dan Mesin',
                'kode_rek'      => '5.2.02.03.01.0001',
                'nama_belanja'  => 'Belanja Modal Alat Kantor (Mesin Tik / Fotokopi)',
            ],
            [
                'kelompok'      => '5.2.02',
                'nama_kelompok' => 'Belanja Modal Peralatan dan Mesin',
                'kode_rek'      => '5.2.02.03.02.0001',
                'nama_belanja'  => 'Belanja Modal Meja Kerja & Kursi Pejabat',
            ],
            [
                'kelompok'      => '5.2.02',
                'nama_kelompok' => 'Belanja Modal Peralatan dan Mesin',
                'kode_rek'      => '5.2.02.04.01.0001',
                'nama_belanja'  => 'Belanja Modal Komputer / Server / PC Unit',
            ],
            [
                'kelompok'      => '5.2.02',
                'nama_kelompok' => 'Belanja Modal Peralatan dan Mesin',
                'kode_rek'      => '5.2.02.04.01.0002',
                'nama_belanja'  => 'Belanja Modal Laptop / Notebook',
            ],
            [
                'kelompok'      => '5.2.02',
                'nama_kelompok' => 'Belanja Modal Peralatan dan Mesin',
                'kode_rek'      => '5.2.02.04.02.0001',
                'nama_belanja'  => 'Belanja Modal Printer & Perangkat Cetak',
            ],
            [
                'kelompok'      => '5.2.02',
                'nama_kelompok' => 'Belanja Modal Peralatan dan Mesin',
                'kode_rek'      => '5.2.02.04.02.0002',
                'nama_belanja'  => 'Belanja Modal Jaringan / Switch Hub / Access Point',
            ],
            [
                'kelompok'      => '5.2.02',
                'nama_kelompok' => 'Belanja Modal Peralatan dan Mesin',
                'kode_rek'      => '5.2.02.05.01.0005',
                'nama_belanja'  => 'Belanja Modal Alat Kantor Lainnya',
            ],
            [
                'kelompok'      => '5.2.02',
                'nama_kelompok' => 'Belanja Modal Peralatan dan Mesin',
                'kode_rek'      => '5.2.02.05.02.0006',
                'nama_belanja'  => 'Belanja Modal Alat Rumah Tangga Lainnya (Home Use)',
            ],
            [
                'kelompok'      => '5.2.02',
                'nama_kelompok' => 'Belanja Modal Peralatan dan Mesin',
                'kode_rek'      => '5.2.02.06.01.0001',
                'nama_belanja'  => 'Belanja Modal Alat Studio (Kamera / Proyektor)',
            ],
            [
                'kelompok'      => '5.2.02',
                'nama_kelompok' => 'Belanja Modal Peralatan dan Mesin',
                'kode_rek'      => '5.2.02.07.01.0001',
                'nama_belanja'  => 'Belanja Modal Alat Laboratorium Umum',
            ],
            [
                'kelompok'      => '5.2.02',
                'nama_kelompok' => 'Belanja Modal Peralatan dan Mesin',
                'kode_rek'      => '5.2.02.07.01.0002',
                'nama_belanja'  => 'Belanja Modal Alat Laboratorium Klinik & Patologi',
            ],
            [
                'kelompok'      => '5.2.02',
                'nama_kelompok' => 'Belanja Modal Peralatan dan Mesin',
                'kode_rek'      => '5.2.02.08.01.0001',
                'nama_belanja'  => 'Belanja Modal Alat Kedokteran Umum',
            ],
            [
                'kelompok'      => '5.2.02',
                'nama_kelompok' => 'Belanja Modal Peralatan dan Mesin',
                'kode_rek'      => '5.2.02.08.01.0002',
                'nama_belanja'  => 'Belanja Modal Alat Kedokteran Bedah & Operasi (IBS)',
            ],
            [
                'kelompok'      => '5.2.02',
                'nama_kelompok' => 'Belanja Modal Peralatan dan Mesin',
                'kode_rek'      => '5.2.02.08.01.0003',
                'nama_belanja'  => 'Belanja Modal Alat Kedokteran IGD & Gawat Darurat',
            ],
            [
                'kelompok'      => '5.2.02',
                'nama_kelompok' => 'Belanja Modal Peralatan dan Mesin',
                'kode_rek'      => '5.2.02.08.01.0004',
                'nama_belanja'  => 'Belanja Modal Alat Kedokteran Kebidanan & Ginekologi',
            ],
            [
                'kelompok'      => '5.2.02',
                'nama_kelompok' => 'Belanja Modal Peralatan dan Mesin',
                'kode_rek'      => '5.2.02.08.01.0005',
                'nama_belanja'  => 'Belanja Modal Alat Kedokteran Radiologi & Imaging (CT-Scan / X-Ray)',
            ],
            [
                'kelompok'      => '5.2.02',
                'nama_kelompok' => 'Belanja Modal Peralatan dan Mesin',
                'kode_rek'      => '5.2.02.08.01.0006',
                'nama_belanja'  => 'Belanja Modal Alat Kedokteran Fisioterapi & Rehabilitasi',
            ],
            [
                'kelompok'      => '5.2.02',
                'nama_kelompok' => 'Belanja Modal Peralatan dan Mesin',
                'kode_rek'      => '5.2.02.08.01.0007',
                'nama_belanja'  => 'Belanja Modal Alat Kedokteran Anastesi & ICU',
            ],
            [
                'kelompok'      => '5.2.02',
                'nama_kelompok' => 'Belanja Modal Peralatan dan Mesin',
                'kode_rek'      => '5.2.02.08.01.0008',
                'nama_belanja'  => 'Belanja Modal Alat Kedokteran Hemodialisa',
            ],
            [
                'kelompok'      => '5.2.02',
                'nama_kelompok' => 'Belanja Modal Peralatan dan Mesin',
                'kode_rek'      => '5.2.02.08.01.0012',
                'nama_belanja'  => 'Belanja Modal Alat Kedokteran ICU & Ruang Rawat Intensif',
            ],
            [
                'kelompok'      => '5.2.02',
                'nama_kelompok' => 'Belanja Modal Peralatan dan Mesin',
                'kode_rek'      => '5.2.02.08.02.0001',
                'nama_belanja'  => 'Belanja Modal Alat Kesehatan / Alat Penunjang Medik',
            ],

            // ─── 5.2.03 · Belanja Modal Gedung dan Bangunan ───────────────────────
            [
                'kelompok'      => '5.2.03',
                'nama_kelompok' => 'Belanja Modal Gedung dan Bangunan',
                'kode_rek'      => '5.2.03.01.01.0001',
                'nama_belanja'  => 'Belanja Modal Bangunan Gedung Rawat Inap & Poliklinik',
            ],
            [
                'kelompok'      => '5.2.03',
                'nama_kelompok' => 'Belanja Modal Gedung dan Bangunan',
                'kode_rek'      => '5.2.03.01.01.0002',
                'nama_belanja'  => 'Belanja Modal Bangunan Gedung Kantor Administrasi RS',
            ],
            [
                'kelompok'      => '5.2.03',
                'nama_kelompok' => 'Belanja Modal Gedung dan Bangunan',
                'kode_rek'      => '5.2.03.01.01.0003',
                'nama_belanja'  => 'Belanja Modal Bangunan Instalasi Pengolahan Limbah (IPAL)',
            ],
            [
                'kelompok'      => '5.2.03',
                'nama_kelompok' => 'Belanja Modal Gedung dan Bangunan',
                'kode_rek'      => '5.2.03.01.02.0001',
                'nama_belanja'  => 'Belanja Modal Renovasi / Rehabilitasi Gedung Paviliun',
            ],

            // ─── 5.2.04 · Belanja Modal Jalan, Jaringan dan Irigasi ──────────────
            [
                'kelompok'      => '5.2.04',
                'nama_kelompok' => 'Belanja Modal Jalan, Jaringan dan Irigasi',
                'kode_rek'      => '5.2.04.01.01.0001',
                'nama_belanja'  => 'Belanja Modal Jalan & Area Parkir Rumah Sakit',
            ],
            [
                'kelompok'      => '5.2.04',
                'nama_kelompok' => 'Belanja Modal Jalan, Jaringan dan Irigasi',
                'kode_rek'      => '5.2.04.03.01.0001',
                'nama_belanja'  => 'Belanja Modal Instalasi Jaringan Listrik & Panel Sentral',
            ],
            [
                'kelompok'      => '5.2.04',
                'nama_kelompok' => 'Belanja Modal Jalan, Jaringan dan Irigasi',
                'kode_rek'      => '5.2.04.03.01.0002',
                'nama_belanja'  => 'Belanja Modal Instalasi Jaringan Air Bersih & PDAM',
            ],
            [
                'kelompok'      => '5.2.04',
                'nama_kelompok' => 'Belanja Modal Jalan, Jaringan dan Irigasi',
                'kode_rek'      => '5.2.04.03.01.0003',
                'nama_belanja'  => 'Belanja Modal Instalasi Jaringan Telepon & Komunikasi',
            ],
            [
                'kelompok'      => '5.2.04',
                'nama_kelompok' => 'Belanja Modal Jalan, Jaringan dan Irigasi',
                'kode_rek'      => '5.2.04.03.01.0004',
                'nama_belanja'  => 'Belanja Modal Instalasi Jaringan Pipa Gas Oksigen Sentral Medis',
            ],

            // ─── 5.2.05 · Belanja Modal Aset Tetap Lainnya ───────────────────────
            [
                'kelompok'      => '5.2.05',
                'nama_kelompok' => 'Belanja Modal Aset Tetap Lainnya',
                'kode_rek'      => '5.2.05.01.01.0001',
                'nama_belanja'  => 'Belanja Modal Buku Pedoman & Standar Prosedur Operasional',
            ],
            [
                'kelompok'      => '5.2.05',
                'nama_kelompok' => 'Belanja Modal Aset Tetap Lainnya',
                'kode_rek'      => '5.2.05.01.01.0002',
                'nama_belanja'  => 'Belanja Modal Koleksi Perpustakaan Medis & Keperawatan',
            ],
            [
                'kelompok'      => '5.2.05',
                'nama_kelompok' => 'Belanja Modal Aset Tetap Lainnya',
                'kode_rek'      => '5.2.05.01.01.0003',
                'nama_belanja'  => 'Belanja Modal Bahan Pustaka dan Jurnal Ilmiah Kedokteran',
            ],
            [
                'kelompok'      => '5.2.05',
                'nama_kelompok' => 'Belanja Modal Aset Tetap Lainnya',
                'kode_rek'      => '5.2.05.02.01.0001',
                'nama_belanja'  => 'Belanja Modal Aset Tetap Renovasi Gedung',
            ],

            // ─── 5.2.06 · Belanja Modal Aset Tidak Berwujud ──────────────────────
            [
                'kelompok'      => '5.2.06',
                'nama_kelompok' => 'Belanja Modal Aset Tidak Berwujud',
                'kode_rek'      => '5.2.06.01.01.0001',
                'nama_belanja'  => 'Belanja Modal Software Sistem Informasi Manajemen RS (SIMRS)',
            ],
            [
                'kelompok'      => '5.2.06',
                'nama_kelompok' => 'Belanja Modal Aset Tidak Berwujud',
                'kode_rek'      => '5.2.06.01.01.0002',
                'nama_belanja'  => 'Belanja Modal Lisensi Rekam Medis Elektronik (RME) & Integrasi SatuSehat',
            ],
            [
                'kelompok'      => '5.2.06',
                'nama_kelompok' => 'Belanja Modal Aset Tidak Berwujud',
                'kode_rek'      => '5.2.06.01.01.0003',
                'nama_belanja'  => 'Belanja Modal Aplikasi / Website & Portal Layanan Digital RS',
            ],
        ];

        $now = now();
        $records = array_map(fn($item) => array_merge($item, [
            'created_at' => $now,
            'updated_at' => $now,
        ]), $data);

        RekeningBelanja::insert($records);

        $total = RekeningBelanja::count();
        $this->command->info("Selesai! Berhasil memasukkan {$total} data Rekening Belanja SIPD ke database.");
    }
}
