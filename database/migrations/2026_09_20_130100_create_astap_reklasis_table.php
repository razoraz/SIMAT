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
        Schema::create('astap_reklasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('astap_id')->constrained('astaps')->cascadeOnDelete();
            $table->foreignId('jenis_reklasifikasi_asal_id')->nullable()->constrained('jenis_reklasifikasis')->nullOnDelete();
            $table->foreignId('jenis_reklasifikasi_tujuan_id')->nullable()->constrained('jenis_reklasifikasis')->nullOnDelete();
            
            $table->string('jenis_reklas', 50); // KOREKSI_REKENING, KDP_TO_DEFINITIF, EKSTRAKOMPTABEL, HIBAH_MASUK, KOREKSI_LAIN
            $table->string('asal_kib', 50)->nullable();
            $table->string('tujuan_kib', 50)->nullable();
            
            $table->decimal('nilai_reklas', 18, 2)->default(0);
            $table->date('tanggal_reklas');
            $table->tinyInteger('triwulan')->default(1);
            $table->year('tahun');
            $table->string('nomor_ba_reklas', 150)->nullable();
            $table->text('keterangan')->nullable();
            
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('astap_reklasis');
    }
};
