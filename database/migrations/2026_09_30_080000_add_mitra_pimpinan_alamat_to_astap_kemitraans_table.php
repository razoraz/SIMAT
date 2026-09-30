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
        if (Schema::hasTable('astap_kemitraans')) {
            Schema::table('astap_kemitraans', function (Blueprint $table) {
                if (!Schema::hasColumn('astap_kemitraans', 'mitra_pimpinan')) {
                    $table->string('mitra_pimpinan', 255)->nullable()->after('mitra_nama');
                }
                if (!Schema::hasColumn('astap_kemitraans', 'mitra_alamat')) {
                    $table->text('mitra_alamat')->nullable()->after('mitra_pimpinan');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('astap_kemitraans')) {
            Schema::table('astap_kemitraans', function (Blueprint $table) {
                if (Schema::hasColumn('astap_kemitraans', 'mitra_alamat')) {
                    $table->dropColumn('mitra_alamat');
                }
                if (Schema::hasColumn('astap_kemitraans', 'mitra_pimpinan')) {
                    $table->dropColumn('mitra_pimpinan');
                }
            });
        }
    }
};
