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
        Schema::create('astaps', function (Blueprint $table) {
            $table->id();
            
            // Relasi Foreign Key ke Master Data
            $table->foreignId('jenis_pengadaan_id')->nullable()->constrained('jenis_pengadaans')->nullOnDelete();
            $table->foreignId('rekening_belanja_id')->nullable()->constrained('rekening_belanjas')->nullOnDelete();
            $table->foreignId('jenis_astap_id')->nullable()->constrained('jenis_astaps')->nullOnDelete();
            $table->foreignId('unit_id')->nullable()->constrained('units')->nullOnDelete();

            // Klasifikasi KIB & Identitas Barang (Kode PMDN 108)
            $table->string('category', 25); // 'KIB A', 'KIB B', 'KIB C', 'KIB D', 'KIB E', 'KIB F', 'ATB', 'EXTRACOM'
            $table->string('kode_108', 30); // Ambil dari jenis_astaps.sub_sub_rincian_objek (12 Digit)
            $table->string('nama_barang', 255);
            $table->year('tahun_perolehan');
            
            // Kuantitas & Nilai Realisasi Belanja
            $table->integer('jumlah_volume')->default(1);
            $table->string('satuan', 50)->default('Unit');
            $table->decimal('harga_satuan', 18, 2)->default(0);
            $table->decimal('total_realisasi', 18, 2)->default(0);
            $table->decimal('biaya_administrasi_proyek', 18, 2)->default(0);
            $table->boolean('is_extracomtable')->default(false); // TRUE jika KIB B < Rp 300.000

            // Dokumen Pengadaan & Serah Terima (Langkah 3)
            $table->string('spk_nomor', 100)->nullable();
            $table->date('spk_tanggal')->nullable();
            $table->string('surat_pesanan_nomor', 100)->nullable();
            $table->date('surat_pesanan_tanggal')->nullable();
            $table->string('kwitansi_nomor', 100)->nullable();
            $table->date('kwitansi_tanggal')->nullable();
            $table->string('faktur_nomor', 100)->nullable();
            $table->date('faktur_tanggal')->nullable();
            $table->string('sp2d_nomor', 100)->nullable();
            $table->date('sp2d_tanggal')->nullable();
            $table->string('bast_dokumen_nomor', 100)->nullable();
            $table->date('bast_dokumen_tanggal')->nullable();

            // Pihak Penyedia, PPK & Alamat Penempatan (Langkah 4)
            $table->text('alamat_barang')->nullable();
            $table->string('penyedia_nama', 255)->nullable();
            $table->string('penyedia_pemilik', 255)->nullable();
            $table->string('penyedia_rekening_nama', 255)->nullable();
            $table->string('penyedia_rekening_nomor', 100)->nullable();
            $table->text('penyedia_alamat')->nullable();
            $table->string('ppk_nama', 255)->nullable();
            $table->string('ppk_nip', 50)->nullable();
            $table->text('keterangan_tambahan')->nullable();

            // Spesifikasi Khusus KIB A/B/C/D/E/F/ATB dalam format JSON
            $table->json('spesifikasi_json')->nullable();

            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('astaps');
    }
};
