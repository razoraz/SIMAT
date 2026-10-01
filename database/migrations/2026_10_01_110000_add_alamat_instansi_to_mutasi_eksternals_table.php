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
        if (Schema::hasTable('mutasi_eksternals')) {
            Schema::table('mutasi_eksternals', function (Blueprint $table) {
                if (!Schema::hasColumn('mutasi_eksternals', 'alamat_instansi')) {
                    $table->text('alamat_instansi')->nullable()->after('opd_asal');
                }
            });
        }

        if (Schema::hasTable('astap_pelimpahan_skpds')) {
            Schema::table('astap_pelimpahan_skpds', function (Blueprint $table) {
                if (!Schema::hasColumn('astap_pelimpahan_skpds', 'alamat_instansi')) {
                    $table->text('alamat_instansi')->nullable()->after('skpd_asal');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('mutasi_eksternals')) {
            Schema::table('mutasi_eksternals', function (Blueprint $table) {
                if (Schema::hasColumn('mutasi_eksternals', 'alamat_instansi')) {
                    $table->dropColumn('alamat_instansi');
                }
            });
        }

        if (Schema::hasTable('astap_pelimpahan_skpds')) {
            Schema::table('astap_pelimpahan_skpds', function (Blueprint $table) {
                if (Schema::hasColumn('astap_pelimpahan_skpds', 'alamat_instansi')) {
                    $table->dropColumn('alamat_instansi');
                }
            });
        }
    }
};
