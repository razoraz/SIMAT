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
        Schema::create('jenis_astaps', function (Blueprint $table) {
            $table->id();
            $table->string('jenis');
            $table->string('nama_jenis');
            $table->string('sub_rincian_objek');
            $table->string('uraian_sub_rincian');
            $table->string('sub_sub_rincian_objek');
            $table->string('uraian_sub_sub_rincian');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jenis_astaps');
    }
};