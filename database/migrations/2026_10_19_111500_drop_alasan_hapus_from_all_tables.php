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
        $tables = [
            'astap_belanja_barangs',
            'astap_hibahs',
            'astap_kemitraans',
            'astap_reklasis',
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'alasan_hapus')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropColumn('alasan_hapus');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'astap_belanja_barangs',
            'astap_hibahs',
            'astap_kemitraans',
            'astap_reklasis',
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName) && !Schema::hasColumn($tableName, 'alasan_hapus')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->text('alasan_hapus')->nullable();
                });
            }
        }
    }
};
