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
        if (!Schema::hasColumn('astap_registers', 'nama_barang')) {
            Schema::table('astap_registers', function (Blueprint $table) {
                $table->string('nama_barang', 500)->nullable()->after('astap_id');
            });
        }

        // Backfill nama_barang untuk register yang sudah ada
        $registers = \App\Models\AstapRegister::withoutGlobalScopes()->with('astap')->get();
        foreach ($registers as $reg) {
            if ($reg->id == 765 || $reg->id == 766) {
                $reg->nama_barang = 'Crawler Tractor + Attachment';
            } elseif ($reg->id == 767) {
                $reg->nama_barang = 'Swamp Tractor + Attachment';
            } else {
                $astapNama = $reg->astap?->nama_barang;
                if ($astapNama && str_contains($astapNama, ',')) {
                    $parts = array_values(array_filter(array_map('trim', explode(',', $astapNama))));
                    $reg->nama_barang = $parts[0] ?? $astapNama;
                } else {
                    $reg->nama_barang = $astapNama ?: 'Aset ASTAP';
                }
            }
            $reg->saveQuietly();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('astap_registers', 'nama_barang')) {
            Schema::table('astap_registers', function (Blueprint $table) {
                $table->dropColumn('nama_barang');
            });
        }
    }
};
