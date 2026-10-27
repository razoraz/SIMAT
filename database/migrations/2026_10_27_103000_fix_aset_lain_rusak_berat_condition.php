<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Astap;
use App\Models\AstapRegister;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Cari aset-aset yang berada di Akun 1.5.4 (ASET LAIN-LAIN)
        // Terutama yang berada di kelompok 1.5.4.01.01.01 (ASET RUSAK BERAT/USANG)
        $astaps = Astap::where(function ($q) {
            $q->where('id', 440)
              ->orWhere('kode_108', 'like', '1.5.4.01.01.01%')
              ->orWhere('nama_barang', 'like', '%Mono%');
        })->get();

        foreach ($astaps as $astap) {
            // Cek apakah memang Akun 1.5.4
            $kode = $astap->kode_108 ?: ($astap->jenis_astap ? $astap->jenis_astap->sub_sub_rincian_objek : '');
            if (!str_starts_with($kode, '1.5.4') && $astap->id !== 440) {
                continue;
            }

            // Jika ini rekening Sewa Dibayar Dimuka (...019), biarkan Baik
            if (str_contains($kode, '.019')) {
                continue;
            }

            $spec = is_array($astap->spesifikasi_json) ? $astap->spesifikasi_json : (json_decode($astap->spesifikasi_json ?? '[]', true) ?: []);
            $spec['kondisi_barang']  = 'Rusak Berat (Menunggu Penghapusan)';
            $spec['aset_lain_kondisi'] = 'Rusak Berat (Menunggu Penghapusan)';
            $spec['kategori_kib']    = 'ASET LAIN';

            if (!empty($spec['aset_lain_items']) && is_array($spec['aset_lain_items'])) {
                foreach ($spec['aset_lain_items'] as &$alItem) {
                    $alItem['aset_lain_kondisi'] = 'Rusak Berat (Menunggu Penghapusan)';
                }
                unset($alItem);
            } else {
                $spec['aset_lain_items'] = [[
                    'item_nama'         => $astap->nama_barang,
                    'item_volume'       => $astap->jumlah_volume ?: 1,
                    'item_satuan'       => $astap->satuan ?: 'Unit',
                    'aset_lain_kondisi' => 'Rusak Berat (Menunggu Penghapusan)',
                    'aset_lain_alasan'  => 'Dialihkan ke Akun 1.5.4 Aset Rusak Berat / Usang',
                    'aset_lain_nilai'   => $astap->harga_satuan ?: $astap->total_realisasi,
                    'ruang_pemegang'    => $astap->ruang_unit ?: '-',
                ]];
            }

            // Perbarui juga repeater fisik yang mungkin tersimpan
            foreach (['lainnya_items', 'mesin_items', 'gedung_items', 'jaringan_items', 'tanah_items', 'atb_items'] as $rk) {
                if (!empty($spec[$rk]) && is_array($spec[$rk])) {
                    foreach ($spec[$rk] as &$it) {
                        if (isset($it['lainnya_kondisi'])) $it['lainnya_kondisi'] = 'Rusak Berat';
                        if (isset($it['mesin_kondisi'])) $it['mesin_kondisi'] = 'Rusak Berat';
                        if (isset($it['gedung_kondisi'])) $it['gedung_kondisi'] = 'Rusak Berat';
                        if (isset($it['jaringan_kondisi'])) $it['jaringan_kondisi'] = 'Rusak Berat';
                        if (isset($it['kondisi'])) $it['kondisi'] = 'Rusak Berat';
                    }
                    unset($it);
                }
            }

            $astap->spesifikasi_json = $spec;
            $astap->save();

            // Update registers fisik ke Rusak Berat
            foreach ($astap->registers as $reg) {
                $reg->kondisi = 'Rusak Berat';
                $reg->status  = 'Rusak Berat';
                $reg->save();
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op
    }
};
