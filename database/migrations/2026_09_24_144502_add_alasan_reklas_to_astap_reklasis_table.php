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
            $table->text('alasan_reklas')->nullable()->after('nomor_ba_reklas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('astap_reklasis', function (Blueprint $table) {
            $table->dropColumn('alasan_reklas');
        });
    }
};
