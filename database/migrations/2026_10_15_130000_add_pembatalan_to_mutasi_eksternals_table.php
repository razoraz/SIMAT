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
        if (Schema::hasTable('mutasi_eksternals')) {
            Schema::table('mutasi_eksternals', function (Blueprint $table) {
                if (!Schema::hasColumn('mutasi_eksternals', 'alasan_batal')) {
                    $table->text('alasan_batal')->nullable()->after('alasan_mutasi');
                }
                if (!Schema::hasColumn('mutasi_eksternals', 'dibatalkan_at')) {
                    $table->dateTime('dibatalkan_at')->nullable()->after('alasan_batal');
                }
                if (!Schema::hasColumn('mutasi_eksternals', 'dibatalkan_by')) {
                    $table->string('dibatalkan_by', 255)->nullable()->after('dibatalkan_at');
                }
                if (!Schema::hasColumn('mutasi_eksternals', 'dibatalkan_by_id')) {
                    $table->unsignedBigInteger('dibatalkan_by_id')->nullable()->after('dibatalkan_by');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('mutasi_eksternals')) {
            Schema::table('mutasi_eksternals', function (Blueprint $table) {
                if (Schema::hasColumn('mutasi_eksternals', 'dibatalkan_by_id')) {
                    $table->dropColumn('dibatalkan_by_id');
                }
                if (Schema::hasColumn('mutasi_eksternals', 'dibatalkan_by')) {
                    $table->dropColumn('dibatalkan_by');
                }
                if (Schema::hasColumn('mutasi_eksternals', 'dibatalkan_at')) {
                    $table->dropColumn('dibatalkan_at');
                }
                if (Schema::hasColumn('mutasi_eksternals', 'alasan_batal')) {
                    $table->dropColumn('alasan_batal');
                }
            });
        }
    }
};
