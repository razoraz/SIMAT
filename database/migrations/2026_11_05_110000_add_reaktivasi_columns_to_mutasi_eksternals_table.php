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
                if (!Schema::hasColumn('mutasi_eksternals', 'alasan_reaktivasi')) {
                    $table->text('alasan_reaktivasi')->nullable()->after('dibatalkan_by_id');
                }
                if (!Schema::hasColumn('mutasi_eksternals', 'diaktifkan_at')) {
                    $table->dateTime('diaktifkan_at')->nullable()->after('alasan_reaktivasi');
                }
                if (!Schema::hasColumn('mutasi_eksternals', 'diaktifkan_by')) {
                    $table->string('diaktifkan_by', 255)->nullable()->after('diaktifkan_at');
                }
                if (!Schema::hasColumn('mutasi_eksternals', 'diaktifkan_by_id')) {
                    $table->unsignedBigInteger('diaktifkan_by_id')->nullable()->after('diaktifkan_by');
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
                if (Schema::hasColumn('mutasi_eksternals', 'diaktifkan_by_id')) {
                    $table->dropColumn('diaktifkan_by_id');
                }
                if (Schema::hasColumn('mutasi_eksternals', 'diaktifkan_by')) {
                    $table->dropColumn('diaktifkan_by');
                }
                if (Schema::hasColumn('mutasi_eksternals', 'diaktifkan_at')) {
                    $table->dropColumn('diaktifkan_at');
                }
                if (Schema::hasColumn('mutasi_eksternals', 'alasan_reaktivasi')) {
                    $table->dropColumn('alasan_reaktivasi');
                }
            });
        }
    }
};

