<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('astap_kemitraans', function (Blueprint $table) {
            $table->date('tanggal_terminasi')->nullable()->after('dokumen_adendum_path');
            $table->string('jenis_terminasi', 50)->nullable()->after('tanggal_terminasi'); // 'dikembalikan_ke_mitra' | 'serah_terima_rsud'
            $table->string('nomor_ba_terminasi', 150)->nullable()->after('jenis_terminasi');
            $table->date('tanggal_ba_terminasi')->nullable()->after('nomor_ba_terminasi');
            $table->string('berkas_ba_terminasi', 500)->nullable()->after('tanggal_ba_terminasi');
            $table->string('kondisi_akhir', 50)->nullable()->default('Baik')->after('berkas_ba_terminasi'); // 'Baik', 'Rusak Ringan', 'Rusak Berat'
            $table->string('pihak_penyerah_nama', 255)->nullable()->after('kondisi_akhir');
            $table->string('pihak_penyerah_jabatan', 255)->nullable()->after('pihak_penyerah_nama');
            $table->string('pihak_penerima_nama', 255)->nullable()->after('pihak_penyerah_jabatan');
            $table->string('pihak_penerima_jabatan', 255)->nullable()->after('pihak_penerima_nama');
            $table->text('catatan_terminasi')->nullable()->after('pihak_penerima_jabatan');
            $table->boolean('is_transferred_to_rsud')->default(false)->after('catatan_terminasi');
            $table->unsignedBigInteger('reklas_id')->nullable()->after('is_transferred_to_rsud');
            $table->string('target_kib_reklas', 50)->nullable()->after('reklas_id'); // 'KIB_A', 'KIB_B', 'KIB_C', 'KIB_D', 'KIB_E'
            $table->string('target_kode_108', 50)->nullable()->after('target_kib_reklas');
            $table->decimal('nilai_perolehan_rsud', 18, 2)->nullable()->after('target_kode_108');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('astap_kemitraans', function (Blueprint $table) {
            $table->dropColumn([
                'tanggal_terminasi',
                'jenis_terminasi',
                'nomor_ba_terminasi',
                'tanggal_ba_terminasi',
                'berkas_ba_terminasi',
                'kondisi_akhir',
                'pihak_penyerah_nama',
                'pihak_penyerah_jabatan',
                'pihak_penerima_nama',
                'pihak_penerima_jabatan',
                'catatan_terminasi',
                'is_transferred_to_rsud',
                'reklas_id',
                'target_kib_reklas',
                'target_kode_108',
                'nilai_perolehan_rsud',
            ]);
        });
    }
};
