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
        if (!Schema::hasTable('astap_hibahs')) {
            Schema::create('astap_hibahs', function (Blueprint $table) {
                $table->id();
                $table->enum('tipe_hibah', ['masuk', 'keluar'])->default('masuk'); // masuk: penambahan, keluar: pengurangan (dihibahkan)
                $table->foreignId('astap_id')->constrained('astaps')->cascadeOnDelete();
                $table->foreignId('astap_register_id')->nullable()->constrained('astap_registers')->nullOnDelete();
                
                $table->string('pihak_hibah', 255); // Pemberi Hibah (jika masuk) atau Penerima Hibah (jika keluar)
                $table->string('nomor_bast', 150);
                $table->date('tanggal_bast');
                $table->integer('jumlah_volume')->default(1);
                $table->string('satuan', 50)->default('Unit');
                $table->decimal('nilai_aset', 18, 2)->default(0);
                
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
        Schema::dropIfExists('astap_hibahs');
    }
};
