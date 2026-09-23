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
            // Ubah / pastikan sumber_dana mendukung 4 tipe: belanja_modal, belanja_rekening, hibah, mutasi_masuk
            if (Schema::hasColumn('astaps', 'sumber_dana')) {
                // Untuk SQLite / MySQL, ubah menjadi string 50 agar fleksibel & aman untuk 4 varian
                $table->string('sumber_dana', 50)->default('belanja_modal')->change();
            } else {
                $table->string('sumber_dana', 50)->default('belanja_modal')->after('jenis_astap_id');
            }

            // Kolom pendukung untuk perolehan dari Mutasi Masuk (Pelimpahan dari luar/SKPD/Dinas)
            if (!Schema::hasColumn('astaps', 'mutasi_asal')) {
                $table->string('mutasi_asal', 255)->nullable()->after('hibah_keterangan');
            }
            if (!Schema::hasColumn('astaps', 'mutasi_nomor_bamb')) {
                $table->string('mutasi_nomor_bamb', 255)->nullable()->after('mutasi_asal');
            }
            if (!Schema::hasColumn('astaps', 'mutasi_tanggal')) {
                $table->date('mutasi_tanggal')->nullable()->after('mutasi_nomor_bamb');
            }
            if (!Schema::hasColumn('astaps', 'mutasi_keterangan')) {
                $table->text('mutasi_keterangan')->nullable()->after('mutasi_tanggal');
            }

            // Index untuk kecepatan query filter perolehan
            $table->index('sumber_dana');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('astaps', function (Blueprint $table) {
            $table->dropIndex(['sumber_dana']);
            $table->dropColumn([
                'mutasi_asal',
                'mutasi_nomor_bamb',
                'mutasi_tanggal',
                'mutasi_keterangan',
            ]);
        });
    }
};
