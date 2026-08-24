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
        Schema::dropIfExists('distribusi_item_registers');
        Schema::dropIfExists('distribusi_items');
        Schema::dropIfExists('distribusis');

        // 1. Header Transaksi Distribusi & BAST (Hanya simpan PK unit_id untuk data tujuan)
        Schema::create('distribusis', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 50)->unique(); // Contoh: DST-2026-004
            $table->string('bast_nomor', 100)->nullable(); // Contoh: 032 / 034 / 430.10.7 / 2026
            $table->date('tanggal_distribusi');
            
            // Relasi FK ke Tabel Units (Ruangan / Unit & Pegawai Penerima)
            $table->foreignId('unit_id')->constrained('units')->cascadeOnDelete();
            
            // Status Distribusi & Validasi
            $table->enum('status', ['Telah Diterima', 'Dalam Pengiriman', 'Menunggu Konfirmasi', 'Draft'])->default('Draft');
            $table->boolean('signed')->default(false);
            $table->string('tgl_signed', 50)->nullable();
            $table->text('keterangan')->nullable();
            
            $table->timestamps();
            
            $table->index(['tanggal_distribusi', 'status']);
        });

        // 2. Detail Item Barang Distribusi (Hanya simpan PK astap_id, NIBAR diambil langsung dari data ASTAP)
        Schema::create('distribusi_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('distribusi_id')->constrained('distribusis')->cascadeOnDelete();
            $table->foreignId('astap_id')->constrained('astaps')->cascadeOnDelete();
            
            // Volume Barang
            $table->unsignedInteger('qty')->default(1);
            
            // Pilihan NIBAR yang diambil langsung dari data ASTAP (disimpan dalam format JSON)
            $table->json('nibar_list')->nullable();
            
            $table->enum('kondisi', ['Baik', 'Kurang Baik', 'Rusak Berat'])->default('Baik');
            $table->text('keterangan')->nullable();
            
            $table->timestamps();
            
            $table->index(['distribusi_id', 'astap_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('distribusi_item_registers');
        Schema::dropIfExists('distribusi_items');
        Schema::dropIfExists('distribusis');
    }
};
