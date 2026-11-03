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
        if (Schema::hasTable('astap_hibahs')) {
            Schema::table('astap_hibahs', function (Blueprint $table) {
                if (!Schema::hasColumn('astap_hibahs', 'nama_penerima')) {
                    $table->string('nama_penerima', 255)->nullable()->after('pihak_hibah');
                }
                if (!Schema::hasColumn('astap_hibahs', 'jabatan_penerima')) {
                    $table->string('jabatan_penerima', 255)->nullable()->after('nama_penerima');
                }
                if (!Schema::hasColumn('astap_hibahs', 'nip_penerima')) {
                    $table->string('nip_penerima', 50)->nullable()->after('jabatan_penerima');
                }
                if (!Schema::hasColumn('astap_hibahs', 'alamat_penerima')) {
                    $table->text('alamat_penerima')->nullable()->after('nip_penerima');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('astap_hibahs')) {
            Schema::table('astap_hibahs', function (Blueprint $table) {
                $columns = ['nama_penerima', 'jabatan_penerima', 'nip_penerima', 'alamat_penerima'];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('astap_hibahs', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};

