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
        Schema::table('astap_kemitraans', function (Blueprint $table) {
            $table->string('tipe_kemitraan', 50)->nullable()->default('dimanfaatkan')->after('astap_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('astap_kemitraans', function (Blueprint $table) {
            $table->dropColumn('tipe_kemitraan');
        });
    }
};
