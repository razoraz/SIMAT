<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Astap;
use App\Models\AstapRegister;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Cari ASTAP kemitraan ID 61 atau yang memiliki kode 108 berakhiran .005 (Sewa Aset Tetap Lainnya)
        $corruptedAstaps = Astap::where(function ($q) {
            $q->where('id', 61)
              ->orWhere('kode_108', 'like', '%1.5.2.01.01.01.005%')
              ->orWhere('kode_108', 'like', '1.5.2%005');
        })->get();

        foreach ($corruptedAstaps as $astap) {
            $spec = is_array($astap->spesifikasi_json) ? $astap->spesifikasi_json : (json_decode($astap->spesifikasi_json ?? '[]', true) ?: []);
            
            // Bersihkan dummy mesin_items jika ada
            unset($spec['mesin_items']);
            unset($spec['merk']);
            unset($spec['type']);
            unset($spec['ukuran']);
            unset($spec['mesin_ukuran']);
            unset($spec['mesin_ukuran_cc']);
            unset($spec['bahan']);
            unset($spec['no_pabrik']);
            unset($spec['no_rangka']);
            unset($spec['no_mesin']);
            unset($spec['no_polisi']);
            unset($spec['no_bpkb']);
            
            // Pastikan kategori KIB adalah KIB E
            $spec['kategori_kib'] = 'KIB E (Aset Tetap Lainnya)';
            $spec['kib_e_type'] = $spec['kib_e_type'] ?? 'buku';
            
            // Ambil judul riil dari lainnya_items atau default aslinya
            $namaRiil = 'Pedoman Standar Pelayanan Klinis & Formularium RSUD Dr. H. Koesnandi';
            if (!empty($spec['lainnya_items']) && is_array($spec['lainnya_items'])) {
                foreach ($spec['lainnya_items'] as &$lItem) {
                    if (!empty($lItem['lainnya_judul']) && !str_starts_with(trim($lItem['lainnya_judul']), 'Sewa ')) {
                        $namaRiil = trim($lItem['lainnya_judul']);
                    } elseif (!empty($lItem['lainnya_nama_barang']) && !str_starts_with(trim($lItem['lainnya_nama_barang']), 'Sewa ')) {
                        $namaRiil = trim($lItem['lainnya_nama_barang']);
                    }
                    $lItem['lainnya_judul'] = $namaRiil;
                    $lItem['lainnya_nama_barang'] = $namaRiil;
                    $lItem['lainnya_bahan'] = $lItem['lainnya_bahan'] ?? 'Kertas / Kanvas / Lainnya';
                }
                unset($lItem);
            } else {
                $spec['lainnya_items'] = [[
                    'lainnya_nama_barang' => $namaRiil,
                    'lainnya_judul' => $namaRiil,
                    'lainnya_pencipta' => '-',
                    'lainnya_spesifikasi' => '-',
                    'lainnya_bahan' => 'Kertas / Kanvas / Lainnya',
                    'lainnya_ukuran' => '-',
                    'lainnya_kondisi' => 'Baik',
                    'lainnya_jumlah' => (int) ($astap->jumlah_volume ?: 2),
                    'lainnya_jumlah_barang' => (int) ($astap->jumlah_volume ?: 2),
                    'lainnya_satuan' => $astap->satuan ?: 'Buah',
                    'lainnya_nilai_satuan' => (float) ($astap->harga_satuan ?: 250000),
                    'lainnya_total_nilai' => (float) ($astap->total_realisasi ?: 500000),
                    'is_extracom' => true,
                ]];
            }
            
            $spec['judul'] = $namaRiil;
            $spec['bahan'] = 'Kertas / Kanvas / Lainnya';
            
            $astap->nama_barang = $namaRiil;
            $astap->spesifikasi_json = $spec;
            $astap->save();
            
            // Perbarui juga nama_barang di astap_registers terkait jika kolom nama_barang ada
            if (Schema::hasColumn('astap_registers', 'nama_barang')) {
                AstapRegister::where('astap_id', $astap->id)->update([
                    'nama_barang' => $namaRiil,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
