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
        Schema::create('jenis_pengadaans', function (Blueprint $table) {
            $table->id();
            $table->string('program_kode');
            $table->string('program_nama');
            $table->string('kegiatan_kode');
            $table->string('kegiatan_nama');
            $table->string('sub_kegiatan_kode');
            $table->string('sub_kegiatan_nama');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jenis_pengadaans');
    }
};
