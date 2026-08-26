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
        Schema::create('astap_registers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('astap_id')->constrained('astaps')->cascadeOnDelete();
            $table->foreignId('unit_id')->nullable()->constrained('units')->nullOnDelete();

            // Kunci Auto-Increment NIBAR
            $table->year('tahun_perolehan');
            $table->unsignedInteger('no_register_int'); // 1, 2, 3, 4, 5... (Auto-increment per jenis astap & tahun)
            $table->string('no_register', 60); // 45 digit NIBAR / register
            
            // NIBAR Resmi 45 Digit Unik
            // Format: 120135110200000028000020261320502060010000001
            $table->string('nibar', 60)->unique();

            // Status Fisik & Penempatan Unit Ruangan
            $table->string('ruang_pemegang', 255)->nullable();
            $table->string('kondisi')->default('Baik');
            $table->string('status')->default('Tersedia');
            $table->string('qr_code_path', 255)->nullable();
            
            $table->timestamps();
            
            $table->index(['tahun_perolehan', 'no_register_int']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('astap_registers');
    }
};
