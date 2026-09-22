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
            if (!Schema::hasColumn('astaps', 'sumber_dana')) {
                $table->enum('sumber_dana', ['belanja_modal', 'hibah'])->default('belanja_modal')->after('jenis_reklas');
            }
            if (!Schema::hasColumn('astaps', 'hibah_pemberi')) {
                $table->string('hibah_pemberi')->nullable()->after('sumber_dana');
            }
            if (!Schema::hasColumn('astaps', 'hibah_nomor_bast')) {
                $table->string('hibah_nomor_bast')->nullable()->after('hibah_pemberi');
            }
            if (!Schema::hasColumn('astaps', 'hibah_tanggal_bast')) {
                $table->date('hibah_tanggal_bast')->nullable()->after('hibah_nomor_bast');
            }
            if (!Schema::hasColumn('astaps', 'hibah_keterangan')) {
                $table->text('hibah_keterangan')->nullable()->after('hibah_tanggal_bast');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('astaps', function (Blueprint $table) {
            $table->dropColumn([
                'sumber_dana',
                'hibah_pemberi',
                'hibah_nomor_bast',
                'hibah_tanggal_bast',
                'hibah_keterangan',
            ]);
        });
    }
};
