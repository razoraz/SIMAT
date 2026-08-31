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
        Schema::create('astap_bast_triwulans', function (Blueprint $table) {
            $table->id();
            $table->string('tahun', 4)->default('2026');
            $table->string('triwulan', 5); // 'TW1', 'TW2', 'TW3', 'TW4'
            $table->string('nomor_surat', 100)->nullable();
            $table->date('tanggal_bast')->nullable();
            $table->string('lokasi')->default('Rumah Sakit Umum Daerah dr.H.Koesnandi Kabupaten Bondowoso');
            
            // Pihak 1 (PPK)
            $table->string('pihak1_nama', 150)->nullable();
            $table->string('pihak1_nip', 50)->nullable();
            $table->string('pihak1_jabatan', 255)->default('Pejabat Pembuat Komitmen (PPK) / Penerima Hasil Pengadaan RSUD dr.H.Koesnandi');
            
            // Pihak 2 (Pengurus Barang Aset)
            $table->string('pihak2_nama', 150)->nullable();
            $table->string('pihak2_nip', 50)->nullable();
            $table->string('pihak2_jabatan', 255)->default('Pengurus Barang Aset Pada RSUD dr.H.Koesnandi Kabupaten Bondowoso');
            
            // Mengetahui (Direktur)
            $table->string('direktur_nama', 150)->default('dr. DIAN ARISANDI, M.Kes');
            $table->string('direktur_nip', 50)->default('19730514 200212 2 003');
            
            // TTD & Status
            $table->string('status', 50)->default('Draft');
            $table->boolean('signed')->default(false);
            $table->string('tgl_signed')->nullable();
            $table->string('qr_hash')->nullable();
            $table->text('catatan')->nullable();
            
            $table->timestamps();
            
            $table->unique(['tahun', 'triwulan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('astap_bast_triwulans');
    }
};
