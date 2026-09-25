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
        Schema::table('mutasi_eksternals', function (Blueprint $table) {
            if (!Schema::hasColumn('mutasi_eksternals', 'signed')) {
                $table->boolean('signed')->default(false)->after('status');
            }
            if (!Schema::hasColumn('mutasi_eksternals', 'tgl_signed')) {
                $table->string('tgl_signed', 100)->nullable()->after('signed');
            }
            if (!Schema::hasColumn('mutasi_eksternals', 'qr_hash')) {
                $table->string('qr_hash', 255)->nullable()->after('tgl_signed');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mutasi_eksternals', function (Blueprint $table) {
            if (Schema::hasColumn('mutasi_eksternals', 'qr_hash')) {
                $table->dropColumn('qr_hash');
            }
            if (Schema::hasColumn('mutasi_eksternals', 'tgl_signed')) {
                $table->dropColumn('tgl_signed');
            }
            if (Schema::hasColumn('mutasi_eksternals', 'signed')) {
                $table->dropColumn('signed');
            }
        });
    }
};
