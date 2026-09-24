<?php

namespace Database\Seeders;

use App\Models\JenisReklasifikasi;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class JenisReklasifikasiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Mengisi 42 template baku baris Matriks Neraca Reklasifikasi PMDN 108 untuk RSUD Dr. H. Koesnandi.
     */
    public function run(): void
    {
        $this->command->info('Mengosongkan dan mengisi tabel jenis_reklasifikasis...');
        Schema::disableForeignKeyConstraints();
        JenisReklasifikasi::truncate();
        Schema::enableForeignKeyConstraints();

        $rows = [
            // ─── KIB A : TANAH ───────────────────────────────────────────────
            [
                'urutan' => 1,
                'kelompok_kib' => 'KIB A',
                'kode_prefix' => '1.3.1.01',
                'nama_sub_rincian' => 'TANAH',
                'tipe_baris' => 'ITEM',
            ],

            // ─── KIB B : PERALATAN DAN MESIN (19 SUB RINCIAN) ─────────────────
            [
                'urutan' => 2,
                'kelompok_kib' => 'KIB B',
                'kode_prefix' => '1.3.2.01',
                'nama_sub_rincian' => 'ALAT BESAR',
                'tipe_baris' => 'ITEM',
            ],
            [
                'urutan' => 3,
                'kelompok_kib' => 'KIB B',
                'kode_prefix' => '1.3.2.02',
                'nama_sub_rincian' => 'ALAT ANGKUTAN',
                'tipe_baris' => 'ITEM',
            ],
            [
                'urutan' => 4,
                'kelompok_kib' => 'KIB B',
                'kode_prefix' => '1.3.2.03',
                'nama_sub_rincian' => 'ALAT BENGKEL DAN ALAT UKUR',
                'tipe_baris' => 'ITEM',
            ],
            [
                'urutan' => 5,
                'kelompok_kib' => 'KIB B',
                'kode_prefix' => '1.3.2.04',
                'nama_sub_rincian' => 'ALAT PERTANIAN',
                'tipe_baris' => 'ITEM',
            ],
            [
                'urutan' => 6,
                'kelompok_kib' => 'KIB B',
                'kode_prefix' => '1.3.2.05',
                'nama_sub_rincian' => 'ALAT KANTOR DAN RUMAH TANGGA',
                'tipe_baris' => 'ITEM',
            ],
            [
                'urutan' => 7,
                'kelompok_kib' => 'KIB B',
                'kode_prefix' => '1.3.2.06',
                'nama_sub_rincian' => 'ALAT STUDIO, KOMUNIKASI DAN PEMANCAR',
                'tipe_baris' => 'ITEM',
            ],
            [
                'urutan' => 8,
                'kelompok_kib' => 'KIB B',
                'kode_prefix' => '1.3.2.07',
                'nama_sub_rincian' => 'ALAT KEDOKTERAN DAN KESEHATAN',
                'tipe_baris' => 'ITEM',
            ],
            [
                'urutan' => 9,
                'kelompok_kib' => 'KIB B',
                'kode_prefix' => '1.3.2.08',
                'nama_sub_rincian' => 'ALAT LABORATORIUM',
                'tipe_baris' => 'ITEM',
            ],
            [
                'urutan' => 10,
                'kelompok_kib' => 'KIB B',
                'kode_prefix' => '1.3.2.09',
                'nama_sub_rincian' => 'ALAT PERSENJATAAN',
                'tipe_baris' => 'ITEM',
            ],
            [
                'urutan' => 11,
                'kelompok_kib' => 'KIB B',
                'kode_prefix' => '1.3.2.10',
                'nama_sub_rincian' => 'KOMPUTER',
                'tipe_baris' => 'ITEM',
            ],
            [
                'urutan' => 12,
                'kelompok_kib' => 'KIB B',
                'kode_prefix' => '1.3.2.11',
                'nama_sub_rincian' => 'ALAT EKSPLORASI',
                'tipe_baris' => 'ITEM',
            ],
            [
                'urutan' => 13,
                'kelompok_kib' => 'KIB B',
                'kode_prefix' => '1.3.2.12',
                'nama_sub_rincian' => 'ALAT PENGEBORAN',
                'tipe_baris' => 'ITEM',
            ],
            [
                'urutan' => 14,
                'kelompok_kib' => 'KIB B',
                'kode_prefix' => '1.3.2.13',
                'nama_sub_rincian' => 'ALAT PRODUKSI, PENGOLAHAN DAN PEMURNIAN',
                'tipe_baris' => 'ITEM',
            ],
            [
                'urutan' => 15,
                'kelompok_kib' => 'KIB B',
                'kode_prefix' => '1.3.2.14',
                'nama_sub_rincian' => 'ALAT BANTU EKSPLORASI',
                'tipe_baris' => 'ITEM',
            ],
            [
                'urutan' => 16,
                'kelompok_kib' => 'KIB B',
                'kode_prefix' => '1.3.2.15',
                'nama_sub_rincian' => 'ALAT KESELAMATAN KERJA',
                'tipe_baris' => 'ITEM',
            ],
            [
                'urutan' => 17,
                'kelompok_kib' => 'KIB B',
                'kode_prefix' => '1.3.2.16',
                'nama_sub_rincian' => 'ALAT PERAGA',
                'tipe_baris' => 'ITEM',
            ],
            [
                'urutan' => 18,
                'kelompok_kib' => 'KIB B',
                'kode_prefix' => '1.3.2.17',
                'nama_sub_rincian' => 'PERALATAN PROSES / PRODUKSI',
                'tipe_baris' => 'ITEM',
            ],
            [
                'urutan' => 19,
                'kelompok_kib' => 'KIB B',
                'kode_prefix' => '1.3.2.18',
                'nama_sub_rincian' => 'RAMBU-RAMBU',
                'tipe_baris' => 'ITEM',
            ],
            [
                'urutan' => 20,
                'kelompok_kib' => 'KIB B',
                'kode_prefix' => '1.3.2.19',
                'nama_sub_rincian' => 'PERALATAN OLAH RAGA',
                'tipe_baris' => 'ITEM',
            ],

            // ─── KIB C : GEDUNG DAN BANGUNAN ──────────────────────────────────
            [
                'urutan' => 21,
                'kelompok_kib' => 'KIB C',
                'kode_prefix' => '1.3.3.01',
                'nama_sub_rincian' => 'BANGUNAN GEDUNG',
                'tipe_baris' => 'ITEM',
            ],
            [
                'urutan' => 22,
                'kelompok_kib' => 'KIB C',
                'kode_prefix' => '1.3.3.02',
                'nama_sub_rincian' => 'MONUMEN',
                'tipe_baris' => 'ITEM',
            ],
            [
                'urutan' => 23,
                'kelompok_kib' => 'KIB C',
                'kode_prefix' => '1.3.3.03',
                'nama_sub_rincian' => 'BANGUNAN MENARA',
                'tipe_baris' => 'ITEM',
            ],
            [
                'urutan' => 24,
                'kelompok_kib' => 'KIB C',
                'kode_prefix' => '1.3.3.04',
                'nama_sub_rincian' => 'TUGU TITIK KONTROL/PASTI',
                'tipe_baris' => 'ITEM',
            ],

            // ─── KIB D : JALAN, IRIGASI DAN JARINGAN ──────────────────────────
            [
                'urutan' => 25,
                'kelompok_kib' => 'KIB D',
                'kode_prefix' => '1.3.4.01',
                'nama_sub_rincian' => 'JALAN DAN JEMBATAN',
                'tipe_baris' => 'ITEM',
            ],
            [
                'urutan' => 26,
                'kelompok_kib' => 'KIB D',
                'kode_prefix' => '1.3.4.02',
                'nama_sub_rincian' => 'BANGUNAN AIR',
                'tipe_baris' => 'ITEM',
            ],
            [
                'urutan' => 27,
                'kelompok_kib' => 'KIB D',
                'kode_prefix' => '1.3.4.03',
                'nama_sub_rincian' => 'INSTALASI',
                'tipe_baris' => 'ITEM',
            ],
            [
                'urutan' => 28,
                'kelompok_kib' => 'KIB D',
                'kode_prefix' => '1.3.4.04',
                'nama_sub_rincian' => 'JARINGAN',
                'tipe_baris' => 'ITEM',
            ],

            // ─── KIB E : ASET TETAP LAINNYA ───────────────────────────────────
            [
                'urutan' => 29,
                'kelompok_kib' => 'KIB E',
                'kode_prefix' => '1.3.5.01',
                'nama_sub_rincian' => 'BUKU / KEPUSTAKAAN',
                'tipe_baris' => 'ITEM',
            ],
            [
                'urutan' => 30,
                'kelompok_kib' => 'KIB E',
                'kode_prefix' => '1.3.5.02',
                'nama_sub_rincian' => 'BARANG BERCORAK KESENIAN/KEBUDAYAAN',
                'tipe_baris' => 'ITEM',
            ],
            [
                'urutan' => 31,
                'kelompok_kib' => 'KIB E',
                'kode_prefix' => '1.3.5.03',
                'nama_sub_rincian' => 'HEWAN',
                'tipe_baris' => 'ITEM',
            ],
            [
                'urutan' => 32,
                'kelompok_kib' => 'KIB E',
                'kode_prefix' => '1.3.5.04',
                'nama_sub_rincian' => 'BIOTA PERAIRAN',
                'tipe_baris' => 'ITEM',
            ],
            [
                'urutan' => 33,
                'kelompok_kib' => 'KIB E',
                'kode_prefix' => '1.3.5.05',
                'nama_sub_rincian' => 'TANAMAN',
                'tipe_baris' => 'ITEM',
            ],
            [
                'urutan' => 34,
                'kelompok_kib' => 'KIB E',
                'kode_prefix' => '1.3.5.06',
                'nama_sub_rincian' => 'BARANG KOLEKSI / BUKAN SENI',
                'tipe_baris' => 'ITEM',
            ],
            [
                'urutan' => 35,
                'kelompok_kib' => 'KIB E',
                'kode_prefix' => '1.3.5.07',
                'nama_sub_rincian' => 'ASET TETAP RENOVASI',
                'tipe_baris' => 'ITEM',
            ],

            // ─── KIB F : KONSTRUKSI DALAM PENGERJAAN ──────────────────────────
            [
                'urutan' => 36,
                'kelompok_kib' => 'KIB F',
                'kode_prefix' => '1.3.6.01',
                'nama_sub_rincian' => 'KONSTRUKSI DALAM PENGERJAAN',
                'tipe_baris' => 'ITEM',
            ],

            // ─── ASET LAINNYA ─────────────────────────────────────────────────
            [
                'urutan' => 37,
                'kelompok_kib' => 'ASET LAINNYA',
                'kode_prefix' => '1.5.2',
                'nama_sub_rincian' => 'KEMITRAAN DENGAN PIHAK KETIGA',
                'tipe_baris' => 'ITEM',
            ],
            [
                'urutan' => 38,
                'kelompok_kib' => 'ASET LAINNYA',
                'kode_prefix' => '1.5.3',
                'nama_sub_rincian' => 'ASET TIDAK BERWUJUD (ATB)',
                'tipe_baris' => 'ITEM',
            ],
            [
                'urutan' => 39,
                'kelompok_kib' => 'ASET LAINNYA',
                'kode_prefix' => '1.5.4',
                'nama_sub_rincian' => 'ASET LAIN-LAIN',
                'tipe_baris' => 'ITEM',
            ],

            // ─── KOREKSI ATAS ASET TETAP ──────────────────────────────────────
            [
                'urutan' => 40,
                'kelompok_kib' => 'KOREKSI',
                'kode_prefix' => 'KOR_HIBAH',
                'nama_sub_rincian' => 'Koreksi Hibah / Bantuan Pemerintah',
                'tipe_baris' => 'ITEM',
            ],
            [
                'urutan' => 41,
                'kelompok_kib' => 'KOREKSI',
                'kode_prefix' => 'KOR_EXTRACOM',
                'nama_sub_rincian' => 'Koreksi Dibawah Batas Kapitalisasi (Ekstrakomptabel)',
                'tipe_baris' => 'ITEM',
            ],
            [
                'urutan' => 42,
                'kelompok_kib' => 'KOREKSI',
                'kode_prefix' => 'KOR_LAIN',
                'nama_sub_rincian' => 'Koreksi Lain-Lain',
                'tipe_baris' => 'ITEM',
            ],
        ];

        foreach ($rows as $row) {
            JenisReklasifikasi::create($row);
        }

        $this->command->info('Berhasil mengimpor 42 baris template neraca reklasifikasi PMDN 108.');
    }
}
