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
        Schema::table('astaps', function (Blueprint $table) {
            $table->boolean('is_reklas')->default(false)->after('is_extracomtable');
            $table->string('jenis_reklas', 50)->nullable()->after('is_reklas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('astaps', function (Blueprint $table) {
            $table->dropColumn(['is_reklas', 'jenis_reklas']);
        });
    }
};
