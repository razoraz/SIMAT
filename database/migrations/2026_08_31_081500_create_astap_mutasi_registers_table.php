<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Buat tabel pivot/detail rincian register mutasi
        Schema::create('astap_mutasi_registers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('astap_mutasi_id')->constrained('astap_mutasis')->cascadeOnDelete();
            $table->foreignId('astap_register_id')->constrained('astap_registers')->cascadeOnDelete();
            $table->string('kondisi', 50)->default('Baik');
            $table->timestamps();
        });

        // 2. Migrasi data lama dari astap_mutasis ke astap_mutasi_registers
        $existingMutasis = DB::table('astap_mutasis')->get();
        foreach ($existingMutasis as $m) {
            if (!empty($m->astap_register_id)) {
                DB::table('astap_mutasi_registers')->insert([
                    'astap_mutasi_id'   => $m->id,
                    'astap_register_id' => $m->astap_register_id,
                    'kondisi'           => $m->kondisi ?? 'Baik',
                    'created_at'        => $m->created_at ?? now(),
                    'updated_at'        => $m->updated_at ?? now(),
                ]);
            }
        }

        // 3. Buat kolom astap_register_id di astap_mutasis menjadi nullable (untuk backward-compatibility)
        try {
            Schema::table('astap_mutasis', function (Blueprint $table) {
                $table->unsignedBigInteger('astap_register_id')->nullable()->change();
            });
        } catch (\Throwable $e) {
            // Ignore if DB driver doesn't support changing column directly
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('astap_mutasi_registers');
    }
};
