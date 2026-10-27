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
        if (Schema::hasTable('astap_reklasis') && !Schema::hasColumn('astap_reklasis', 'dokumen_path')) {
            Schema::table('astap_reklasis', function (Blueprint $table) {
                $table->string('dokumen_path', 500)->nullable()->after('nomor_ba_reklas');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('astap_reklasis') && Schema::hasColumn('astap_reklasis', 'dokumen_path')) {
            Schema::table('astap_reklasis', function (Blueprint $table) {
                $table->dropColumn('dokumen_path');
            });
        }
    }
};
