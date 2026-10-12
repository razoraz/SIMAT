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
                if (!Schema::hasColumn('astap_kemitraans', 'riwayat_adendum')) {
                    $table->json('riwayat_adendum')->nullable()->after('dokumen_path');
                }
                if (!Schema::hasColumn('astap_kemitraans', 'is_extended')) {
                    $table->boolean('is_extended')->default(false)->after('riwayat_adendum');
                }
                if (!Schema::hasColumn('astap_kemitraans', 'jumlah_extend')) {
                    $table->unsignedInteger('jumlah_extend')->default(0)->after('is_extended');
                }
                if (!Schema::hasColumn('astap_kemitraans', 'dokumen_adendum_path')) {
                    $table->string('dokumen_adendum_path', 500)->nullable()->after('jumlah_extend');
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
                $columns = ['riwayat_adendum', 'is_extended', 'jumlah_extend', 'dokumen_adendum_path'];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('astap_kemitraans', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
