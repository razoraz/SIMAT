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
            $table->json('spesifikasi_lama')->nullable()->after('keterangan');
            $table->json('spesifikasi_baru')->nullable()->after('spesifikasi_lama');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('astap_reklasis', function (Blueprint $table) {
            $table->dropColumn(['spesifikasi_lama', 'spesifikasi_baru']);
        });
    }

};
