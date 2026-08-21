<?php

namespace Database\Seeders;

use App\Models\JenisAstap;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class JenisAstapSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jsonPath = database_path('data/kode_108_bmds.json');

        if (!File::exists($jsonPath)) {
            $this->command->error("File {$jsonPath} tidak ditemukan!");
            return;
        }

        $jsonContent = File::get($jsonPath);
        $data = json_decode($jsonContent, true);

        if (empty($data) || !is_array($data)) {
            $this->command->error("Data JSON kosong atau tidak valid!");
            return;
        }

        $this->command->info('Mengosongkan tabel jenis_astaps...');
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        JenisAstap::truncate();
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        $this->command->info('Mengimpor ' . count($data) . ' data Jenis ASTAP (Kode 108 BMD)...');

        $now = now();
        $chunks = array_chunk($data, 1000);

        foreach ($chunks as $index => $chunk) {
            $records = [];
            foreach ($chunk as $item) {
                $records[] = [
                    'jenis'                  => $item['jenis'] ?? '',
                    'nama_jenis'             => $item['nama_jenis'] ?? '',
                    'sub_rincian_objek'      => $item['sub_rincian_objek'] ?? '',
                    'uraian_sub_rincian'     => $item['uraian_sub_rincian'] ?? '',
                    'sub_sub_rincian_objek'  => $item['sub_sub_rincian_objek'] ?? '',
                    'uraian_sub_sub_rincian' => $item['uraian_sub_sub_rincian'] ?? '',
                    'created_at'             => $now,
                    'updated_at'             => $now,
                ];
            }

            DB::table('jenis_astaps')->insert($records);
            $this->command->info('Memproses chunk ' . ($index + 1) . ' dari ' . count($chunks) . '...');
        }

        $totalInserted = JenisAstap::count();
        $this->command->info("Selesai! Berhasil memasukkan {$totalInserted} data Jenis ASTAP ke database.");
    }
}
