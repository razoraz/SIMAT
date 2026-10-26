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
        if (!Schema::hasTable('mutasi_eksternal_registers')) {
            Schema::create('mutasi_eksternal_registers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('mutasi_eksternal_id')->constrained('mutasi_eksternals')->cascadeOnDelete();
                $table->foreignId('astap_register_id')->constrained('astap_registers')->cascadeOnDelete();
                $table->string('kondisi', 50)->default('Baik');
                $table->text('catatan')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mutasi_eksternal_registers');
    }
};
