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
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('kode_unit', 50)->unique();
            $table->string('nama', 150);
            $table->string('tipe', 100)->default('Rawat Inap & Paviliun');
            $table->string('kepala', 150);
            $table->string('nip', 50)->nullable();
            $table->string('email', 100)->nullable();
            $table->json('id_aset')->nullable();
            $table->integer('total_aset')->default(0);
            $table->string('total_nilai', 50)->default('Rp 0');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
