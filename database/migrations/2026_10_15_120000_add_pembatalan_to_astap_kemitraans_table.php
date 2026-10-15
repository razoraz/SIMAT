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
                if (!Schema::hasColumn('astap_kemitraans', 'alasan_batal')) {
                    $table->text('alasan_batal')->nullable()->after('catatan_terminasi');
                }
                if (!Schema::hasColumn('astap_kemitraans', 'dibatalkan_at')) {
                    $table->dateTime('dibatalkan_at')->nullable()->after('alasan_batal');
                }
                if (!Schema::hasColumn('astap_kemitraans', 'dibatalkan_by')) {
                    $table->string('dibatalkan_by', 255)->nullable()->after('dibatalkan_at');
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
                if (Schema::hasColumn('astap_kemitraans', 'dibatalkan_by')) {
                    $table->dropColumn('dibatalkan_by');
                }
                if (Schema::hasColumn('astap_kemitraans', 'dibatalkan_at')) {
                    $table->dropColumn('dibatalkan_at');
                }
                if (Schema::hasColumn('astap_kemitraans', 'alasan_batal')) {
                    $table->dropColumn('alasan_batal');
                }
            });
        }
    }
};
