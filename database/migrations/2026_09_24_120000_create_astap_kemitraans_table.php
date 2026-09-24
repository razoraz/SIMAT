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
        if (!Schema::hasTable('astap_kemitraans')) {
            Schema::create('astap_kemitraans', function (Blueprint $table) {
                $table->id();
                $table->foreignId('astap_id')->constrained('astaps')->cascadeOnDelete();
                
                $table->string('mitra_nama', 255); // Nama perusahaan/rekanan mitra
                $table->string('nomor_pks', 150);  // Nomor dokumen PKS / MoU / Kontrak KSO
                $table->date('tanggal_pks');       // Tanggal penandatanganan PKS
                $table->string('skema_kemitraan', 50)->default('KSO'); // KSO, BGS, BSG, KSP, Sewa
                
                $table->date('tanggal_mulai')->nullable();   // Tanggal mulai konsesi
                $table->date('tanggal_selesai')->nullable(); // Tanggal berakhir konsesi
                $table->string('status_konsesi', 50)->default('Aktif'); // Aktif, Akan Berakhir, Selesai / Reklasifikasi, Dihentikan
                
                $table->integer('jumlah_volume')->default(1);
                $table->string('satuan', 50)->default('Unit');
                $table->decimal('nilai_aset', 18, 2)->default(0); // Taksiran nilai wajar aset kemitraan (Akun 1.5.2)
                
                $table->year('tahun')->default(2026);
                $table->string('triwulan', 20)->default('TW I');
                $table->text('keterangan')->nullable();
                $table->string('dokumen_path', 500)->nullable();
                
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('astap_kemitraans');
    }
};
