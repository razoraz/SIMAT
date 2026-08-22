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
        Schema::create('astap_mutasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('astap_register_id')->constrained('astap_registers')->cascadeOnDelete();
            
            $table->string('nomor_bamb', 100); // Nomor Berita Acara Mutasi Barang
            $table->date('tanggal_mutasi');
            $table->string('ruangan_asal', 255);
            $table->string('ruangan_tujuan', 255);
            $table->string('penanggung_jawab_asal', 255)->nullable();
            $table->string('penanggung_jawab_tujuan', 255)->nullable();
            $table->text('alasan_mutasi')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('astap_mutasis');
    }
};
