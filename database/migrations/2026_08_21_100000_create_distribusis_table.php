<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('distribusi_item_registers');
        Schema::dropIfExists('distribusi_items');
        Schema::dropIfExists('distribusis');

        // 1. Header Transaksi Distribusi & BAST
        Schema::create('distribusis', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 50)->unique();           // DST-2026-001
            $table->string('bast_nomor', 100)->nullable();  // Nomor Berita Acara Serah Terima
            $table->date('tanggal_distribusi');

            // FK ke Unit tujuan — nama, kepala, nip dibaca langsung dari tabel units
            $table->foreignId('unit_id')->constrained('units')->cascadeOnDelete();

            $table->enum('status', ['Telah Diterima', 'Dalam Pengiriman', 'Menunggu Konfirmasi', 'Draft'])->default('Draft');
            $table->boolean('signed')->default(false);
            $table->string('tgl_signed', 50)->nullable();
            $table->text('keterangan')->nullable();

            $table->timestamps();
            $table->index(['tanggal_distribusi', 'status']);
        });

        // 2. Baris barang per jenis ASTAP dalam satu transaksi distribusi
        Schema::create('distribusi_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('distribusi_id')->constrained('distribusis')->cascadeOnDelete();

            // FK ke master ASTAP — nama, kode, satuan, merk dibaca langsung dari tabel astaps
            $table->foreignId('astap_id')->constrained('astaps')->cascadeOnDelete();

            $table->unsignedInteger('qty')->default(1); // Jumlah unit yang didistribusikan
            $table->text('keterangan')->nullable();

            $table->timestamps();
            $table->index(['distribusi_id', 'astap_id']);
        });

        // 3. Pivot: Setiap unit fisik (NIBAR) yang tercakup dalam item distribusi
        //    Menggantikan kolom JSON nibar_list — kondisi & ruang dibaca live dari astap_registers
        Schema::create('distribusi_item_registers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('distribusi_item_id')->constrained('distribusi_items')->cascadeOnDelete();

            // FK langsung ke unit fisik — kondisi, ruang_pemegang, nibar dibaca dari astap_registers
            $table->foreignId('astap_register_id')->constrained('astap_registers')->cascadeOnDelete();

            $table->timestamps();

            // Satu distribusi_item tidak boleh punya register yang sama dua kali
            $table->unique(['distribusi_item_id', 'astap_register_id'], 'dist_item_register_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('distribusi_item_registers');
        Schema::dropIfExists('distribusi_items');
        Schema::dropIfExists('distribusis');
    }
};
