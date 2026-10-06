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
        Schema::create('periode_tutup_bukus', function (Blueprint $table) {
            $table->id();
            $table->year('tahun');
            $table->tinyInteger('triwulan')->comment('1-4: Triwulan, 0: Tutup Buku Tahunan');
            $table->boolean('is_locked')->default(false);
            
            // Informasi Penutupan Buku (Lock)
            $table->string('nomor_bar_bpkad', 150)->nullable()->comment('Nomor Berita Acara Rekonsiliasi BPKAD');
            $table->date('tanggal_tutup')->nullable();
            $table->text('keterangan')->nullable();
            $table->foreignId('locked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('locked_at')->nullable();

            // Informasi Pembukaan Kunci (Unlock / Emergency Revision)
            $table->foreignId('unlocked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('unlocked_at')->nullable();
            $table->text('alasan_unlock')->nullable();

            $table->timestamps();

            $table->unique(['tahun', 'triwulan'], 'uniq_tahun_triwulan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('periode_tutup_bukus');
    }
};
