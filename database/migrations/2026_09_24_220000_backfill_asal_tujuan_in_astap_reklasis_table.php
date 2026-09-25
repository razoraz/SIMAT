<?php

use App\Models\Astap;
use App\Models\AstapReklas;
use App\Models\JenisReklasifikasi;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Backfill data snapshot asal & tujuan pada data reklasifikasi yang sudah ada
        $allTemplateRows = JenisReklasifikasi::all()->keyBy('id');

        $reklasItems = AstapReklas::with(['astap.jenisAstap', 'jenisReklasAsal', 'jenisReklasTujuan'])->get();

        foreach ($reklasItems as $item) {
            $updated = false;

            $astap = $item->astap;
            $jenisAsal = $item->jenisReklasAsal;
            $jenisTujuan = $item->jenisReklasTujuan;

            // 1. Asal Kode & Nama
            if (empty($item->asal_kode) || empty($item->asal_nama)) {
                if ($item->jenis_reklas === 'KAPITALISASI_INTRAKOM') {
                    $item->asal_kode = 'KOR_EXTRACOM';
                    $item->asal_nama = 'Aset Ekstrakomptabel (≤ Rp 300.000)';
                } elseif ($item->jenis_reklas === 'HIBAH_MASUK') {
                    $item->asal_kode = 'KOR_HIBAH';
                    $item->asal_nama = 'Penerimaan Hibah / Bantuan Pihak Ketiga';
                } elseif ($item->jenis_reklas === 'KDP_TO_DEFINITIF') {
                    $item->asal_kode = '1.3.6.01.01';
                    $item->asal_nama = 'Konstruksi Dalam Pengerjaan (KIB F)';
                } elseif ($item->jenis_reklas === 'KOREKSI_LAIN' && $item->asal_kib === 'KOREKSI') {
                    $item->asal_kode = 'KOR_LAIN';
                    $item->asal_nama = 'Koreksi Nilai / Penyeimbang Neraca';
                } else {
                    // Gunakan data jenis reklas asal atau jenis astap
                    $item->asal_kode = $jenisAsal?->kode_prefix ?: ($astap?->jenisAstap?->sub_sub_rincian_objek ?: ($astap?->jenisAstap?->sub_rincian_objek ?: $astap?->jenisAstap?->jenis));
                    $item->asal_nama = $jenisAsal?->nama_sub_rincian ?: ($astap?->jenisAstap?->uraian_sub_sub_rincian ?: ($astap?->jenisAstap?->uraian_sub_rincian ?: $astap?->nama_barang));
                }
                $updated = true;
            }

            // 2. Tujuan Kode & Nama
            if (empty($item->tujuan_kode) || empty($item->tujuan_nama)) {
                if ($item->jenis_reklas === 'EKSTRAKOMPTABEL') {
                    $item->tujuan_kode = 'KOR_EXTRACOM';
                    $item->tujuan_nama = 'Koreksi Di Bawah Batas Kapitalisasi (Ekstrakomptabel)';
                } elseif ($item->jenis_reklas === 'KOREKSI_LAIN' && $item->tujuan_kib === 'KOREKSI') {
                    $item->tujuan_kode = 'KOR_LAIN';
                    $item->tujuan_nama = 'Koreksi Nilai / Penyeimbang Neraca';
                } elseif ($item->jenis_reklas === 'KAPITALISASI_INTRAKOM') {
                    $item->tujuan_kode = $jenisTujuan?->kode_prefix ?: ($astap?->jenisAstap?->sub_sub_rincian_objek ?: ($astap?->jenisAstap?->sub_rincian_objek ?: $astap?->jenisAstap?->jenis));
                    $item->tujuan_nama = $jenisTujuan?->nama_sub_rincian ?: ($astap?->jenisAstap?->uraian_sub_sub_rincian ?: ($astap?->jenisAstap?->uraian_sub_rincian ?: $astap?->nama_barang));
                } else {
                    $item->tujuan_kode = $jenisTujuan?->kode_prefix ?: ($astap?->jenisAstap?->sub_sub_rincian_objek ?: ($astap?->jenisAstap?->sub_rincian_objek ?: $astap?->jenisAstap?->jenis));
                    $item->tujuan_nama = $jenisTujuan?->nama_sub_rincian ?: ($astap?->jenisAstap?->uraian_sub_sub_rincian ?: ($astap?->jenisAstap?->uraian_sub_rincian ?: $astap?->nama_barang));
                }
                $updated = true;
            }

            if ($updated) {
                $item->save();
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No down needed for backfill data
    }
};
