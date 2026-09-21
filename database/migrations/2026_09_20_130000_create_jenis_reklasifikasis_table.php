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
        Schema::create('jenis_reklasifikasis', function (Blueprint $table) {
            $table->id();
            $table->integer('urutan')->default(1);
            $table->string('kelompok_kib', 50); // KIB A, KIB B, KIB C, KIB D, KIB E, KIB F, ASET LAINNYA, KOREKSI
            $table->string('kode_prefix', 50)->nullable(); // contoh: 1.3.1.01, 1.3.2.01, 1.3.5.03
            $table->string('nama_sub_rincian', 255); // contoh: ALAT BESAR, HEWAN, TANAH
            $table->enum('tipe_baris', ['HEADER', 'ITEM', 'SUBTOTAL', 'TOTAL'])->default('ITEM');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jenis_reklasifikasis');
    }
};
