<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF;');

            DB::statement('DROP TABLE IF EXISTS "astaps_clean";');

            DB::statement('
                CREATE TABLE "astaps_clean" (
                    "id" integer primary key autoincrement not null,
                    "jenis_astap_id" integer,
                    "nama_barang" varchar not null,
                    "tahun_perolehan" integer not null,
                    "jumlah_volume" integer not null default (\'1\'),
                    "satuan" varchar not null default (\'Unit\'),
                    "harga_satuan" numeric not null default (\'0\'),
                    "jumlah_anggaran" numeric not null default (\'0\'),
                    "total_realisasi" numeric not null default (\'0\'),
                    "biaya_administrasi_proyek" numeric not null default (\'0\'),
                    "is_extracomtable" tinyint(1) not null default (\'0\'),
                    "alamat_barang" text,
                    "ppk_nama" varchar,
                    "ppk_nip" varchar,
                    "keterangan_tambahan" text,
                    "spesifikasi_json" text,
                    "user_id" integer,
                    "created_at" datetime,
                    "updated_at" datetime,
                    "triwulan" varchar default (\'TW I\'),
                    "is_deleted" integer not null default (\'0\'),
                    "deleted_by" varchar,
                    "deleted_by_id" integer,
                    "deleted_at" datetime,
                    "is_reklas" tinyint(1) not null default (\'0\'),
                    "jenis_reklas" varchar,
                    "sumber_dana" varchar not null default \'belanja_modal\',
                    foreign key("user_id") references "users"("id") on delete set null on update no action,
                    foreign key("jenis_astap_id") references "jenis_astaps"("id") on delete set null on update no action
                );
            ');

            DB::statement('
                INSERT INTO "astaps_clean" (
                    "id", "jenis_astap_id", "nama_barang", "tahun_perolehan", "jumlah_volume", "satuan",
                    "harga_satuan", "jumlah_anggaran", "total_realisasi", "biaya_administrasi_proyek",
                    "is_extracomtable", "alamat_barang", "ppk_nama", "ppk_nip", "keterangan_tambahan",
                    "spesifikasi_json", "user_id", "created_at", "updated_at", "triwulan", "is_deleted",
                    "deleted_by", "deleted_by_id", "deleted_at", "is_reklas", "jenis_reklas", "sumber_dana"
                )
                SELECT 
                    "id", "jenis_astap_id", "nama_barang", "tahun_perolehan", "jumlah_volume", "satuan",
                    "harga_satuan", "jumlah_anggaran", "total_realisasi", "biaya_administrasi_proyek",
                    "is_extracomtable", "alamat_barang", "ppk_nama", "ppk_nip", "keterangan_tambahan",
                    "spesifikasi_json", "user_id", "created_at", "updated_at", "triwulan", "is_deleted",
                    "deleted_by", "deleted_by_id", "deleted_at", "is_reklas", "jenis_reklas", "sumber_dana"
                FROM "astaps";
            ');

            DB::statement('DROP TABLE "astaps";');
            DB::statement('ALTER TABLE "astaps_clean" RENAME TO "astaps";');

            DB::statement('PRAGMA foreign_keys = ON;');
        } else {
            // MySQL / PostgreSQL fallback
            Schema::table('astaps', function (Blueprint $table) {
                $table->dropForeign(['jenis_pengadaan_id']);
                $table->dropForeign(['rekening_belanja_id']);
                $table->dropColumn([
                    'jenis_pengadaan_id',
                    'rekening_belanja_id',
                    'spk_nomor',
                    'spk_tanggal',
                    'surat_pesanan_nomor',
                    'surat_pesanan_tanggal',
                    'kwitansi_nomor',
                    'kwitansi_tanggal',
                    'faktur_nomor',
                    'faktur_tanggal',
                    'sp2d_nomor',
                    'sp2d_tanggal',
                    'bast_dokumen_nomor',
                    'bast_dokumen_tanggal',
                    'penyedia_nama',
                    'penyedia_pemilik',
                    'penyedia_rekening_nama',
                    'penyedia_rekening_nomor',
                    'penyedia_alamat',
                    'hibah_pemberi',
                    'hibah_nomor_bast',
                    'hibah_tanggal_bast',
                    'hibah_keterangan',
                    'mutasi_asal',
                    'mutasi_nomor_bamb',
                    'mutasi_tanggal',
                    'mutasi_keterangan',
                ]);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Re-add columns if rolled back
        if (Schema::hasTable('astaps')) {
            Schema::table('astaps', function (Blueprint $table) {
                $table->foreignId('jenis_pengadaan_id')->nullable()->constrained('jenis_pengadaans')->nullOnDelete();
                $table->foreignId('rekening_belanja_id')->nullable()->constrained('rekening_belanjas')->nullOnDelete();
                $table->string('spk_nomor', 255)->nullable();
                $table->date('spk_tanggal')->nullable();
                $table->string('surat_pesanan_nomor', 255)->nullable();
                $table->date('surat_pesanan_tanggal')->nullable();
                $table->string('kwitansi_nomor', 255)->nullable();
                $table->date('kwitansi_tanggal')->nullable();
                $table->string('faktur_nomor', 255)->nullable();
                $table->date('faktur_tanggal')->nullable();
                $table->string('sp2d_nomor', 255)->nullable();
                $table->date('sp2d_tanggal')->nullable();
                $table->string('bast_dokumen_nomor', 255)->nullable();
                $table->date('bast_dokumen_tanggal')->nullable();
                $table->string('penyedia_nama', 500)->nullable();
                $table->string('penyedia_pemilik', 255)->nullable();
                $table->string('penyedia_rekening_nama', 255)->nullable();
                $table->string('penyedia_rekening_nomor', 100)->nullable();
                $table->text('penyedia_alamat')->nullable();
                $table->string('hibah_pemberi', 255)->nullable();
                $table->string('hibah_nomor_bast', 255)->nullable();
                $table->date('hibah_tanggal_bast')->nullable();
                $table->text('hibah_keterangan')->nullable();
                $table->string('mutasi_asal', 255)->nullable();
                $table->string('mutasi_nomor_bamb', 255)->nullable();
                $table->date('mutasi_tanggal')->nullable();
                $table->text('mutasi_keterangan')->nullable();
            });
        }
    }
};
