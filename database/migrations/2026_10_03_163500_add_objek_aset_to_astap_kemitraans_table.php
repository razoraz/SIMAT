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
                if (!Schema::hasColumn('astap_kemitraans', 'objek_astap_id')) {
                    $table->foreignId('objek_astap_id')->nullable()->after('astap_id')->constrained('astaps')->nullOnDelete();
                }
                if (!Schema::hasColumn('astap_kemitraans', 'objek_register_id')) {
                    $table->foreignId('objek_register_id')->nullable()->after('objek_astap_id')->constrained('astap_registers')->nullOnDelete();
                }
                if (!Schema::hasColumn('astap_kemitraans', 'objek_nibar')) {
                    $table->string('objek_nibar', 50)->nullable()->after('objek_register_id');
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
                if (Schema::hasColumn('astap_kemitraans', 'objek_astap_id')) {
                    $table->dropForeign(['objek_astap_id']);
                    $table->dropColumn('objek_astap_id');
                }
                if (Schema::hasColumn('astap_kemitraans', 'objek_register_id')) {
                    $table->dropForeign(['objek_register_id']);
                    $table->dropColumn('objek_register_id');
                }
                if (Schema::hasColumn('astap_kemitraans', 'objek_nibar')) {
                    $table->dropColumn('objek_nibar');
                }
            });
        }
    }
};
