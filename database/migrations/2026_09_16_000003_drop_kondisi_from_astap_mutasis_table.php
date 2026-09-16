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
        Schema::table('astap_mutasis', function (Blueprint $table) {
            if (Schema::hasColumn('astap_mutasis', 'kondisi')) {
                $table->dropColumn('kondisi');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('astap_mutasis', function (Blueprint $table) {
            if (!Schema::hasColumn('astap_mutasis', 'kondisi')) {
                $table->string('kondisi', 50)->default('Baik')->after('jenis_mutasi');
            }
        });
    }
};
