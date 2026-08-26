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
            // Jenis Mutasi: Pemindahan | Perbaikan | Pengembalian | Penghapusan
            $table->string('jenis_mutasi', 50)->default('Pemindahan')->after('astap_register_id');

            // Kondisi barang sebelum & sesudah mutasi
            $table->string('kondisi_sebelum', 30)->default('Baik')->after('ruangan_tujuan');
            $table->string('kondisi_sesudah', 30)->nullable()->after('kondisi_sebelum');

            // Catatan dari unit penerima setelah menerima barang
            $table->text('catatan_penerima')->nullable()->after('alasan_mutasi');

            // Alur Persetujuan Bertingkat 3 Pihak
            $table->boolean('persetujuan_pengirim')->default(false)->after('catatan_penerima');
            $table->boolean('persetujuan_penerima')->default(false)->after('persetujuan_pengirim');
            $table->boolean('persetujuan_admin')->default(false)->after('persetujuan_penerima');

            // Waktu masing-masing persetujuan
            $table->timestamp('tgl_persetujuan_pengirim')->nullable()->after('persetujuan_admin');
            $table->timestamp('tgl_persetujuan_penerima')->nullable()->after('tgl_persetujuan_pengirim');
            $table->timestamp('tgl_persetujuan_admin')->nullable()->after('tgl_persetujuan_penerima');

            // Status ringkasan alur mutasi
            // Menunggu Penerima | Menunggu Admin | Disetujui | Ditolak
            $table->string('status', 50)->default('Menunggu Penerima')->after('tgl_persetujuan_admin');

            // Alasan penolakan jika ada
            $table->text('alasan_penolakan')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('astap_mutasis', function (Blueprint $table) {
            $table->dropColumn([
                'jenis_mutasi',
                'kondisi_sebelum',
                'kondisi_sesudah',
                'catatan_penerima',
                'persetujuan_pengirim',
                'persetujuan_penerima',
                'persetujuan_admin',
                'tgl_persetujuan_pengirim',
                'tgl_persetujuan_penerima',
                'tgl_persetujuan_admin',
                'status',
                'alasan_penolakan',
            ]);
        });
    }
};
