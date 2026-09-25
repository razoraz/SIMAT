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
        Schema::table('astap_reklasis', function (Blueprint $table) {
            $table->string('asal_kode', 100)->nullable()->after('asal_kib');
            $table->string('asal_nama', 255)->nullable()->after('asal_kode');
            $table->string('tujuan_kode', 100)->nullable()->after('tujuan_kib');
            $table->string('tujuan_nama', 255)->nullable()->after('tujuan_kode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('astap_reklasis', function (Blueprint $table) {
            $table->dropColumn(['asal_kode', 'asal_nama', 'tujuan_kode', 'tujuan_nama']);
        });
    }
};
