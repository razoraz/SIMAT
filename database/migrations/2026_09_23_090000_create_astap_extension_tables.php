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
        // 1. Extension Table: Belanja Modal (APBD / BLUD)
        if (!Schema::hasTable('astap_belanja_modals')) {
            Schema::create('astap_belanja_modals', function (Blueprint $table) {
                $table->id();
                $table->foreignId('astap_id')->constrained('astaps')->cascadeOnDelete()->unique();
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
                
                $table->timestamps();
            });
        }

        // 2. Extension Table: Belanja Barang (Pusat Perbekalan / Operasional / Barang & Jasa)
        if (!Schema::hasTable('astap_belanja_barangs')) {
            Schema::create('astap_belanja_barangs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('astap_id')->constrained('astaps')->cascadeOnDelete()->unique();
                $table->string('toko_penyedia', 500);
                $table->string('nomor_faktur', 255);
                $table->date('tanggal_faktur')->nullable();
                $table->decimal('total_pembelian', 15, 2)->default(0);
                $table->text('keterangan')->nullable();
                $table->timestamps();
            });
        }

        // 3. Extension Table: Pelimpahan SKPD (Pelimpahan dari Dinas / SKPD Luar)
        if (!Schema::hasTable('astap_pelimpahan_skpds')) {
            Schema::create('astap_pelimpahan_skpds', function (Blueprint $table) {
                $table->id();
                $table->foreignId('astap_id')->constrained('astaps')->cascadeOnDelete()->unique();
                $table->string('skpd_asal', 500);
                $table->string('nomor_bamb', 255);
                $table->date('tanggal_bamb')->nullable();
                $table->decimal('nilai_perolehan', 15, 2)->default(0);
                $table->text('keterangan')->nullable();
                $table->timestamps();
            });
        }

        // 4. Backfill Data: Pindahkan data pengadaan aset belanja_modal yang ada ke astap_belanja_modals
        if (Schema::hasTable('astaps')) {
            $existingModals = DB::table('astaps')
                ->where(function($q) {
                    $q->whereNull('sumber_dana')
                      ->orWhere('sumber_dana', '')
                      ->orWhere('sumber_dana', 'belanja_modal');
                })
                ->get();

            foreach ($existingModals as $m) {
                $exists = DB::table('astap_belanja_modals')->where('astap_id', $m->id)->exists();
                if (!$exists) {
                    DB::table('astap_belanja_modals')->insert([
                        'astap_id'               => $m->id,
                        'jenis_pengadaan_id'     => $m->jenis_pengadaan_id ?? null,
                        'rekening_belanja_id'    => $m->rekening_belanja_id ?? null,
                        'spk_nomor'              => $m->spk_nomor ?? null,
                        'spk_tanggal'            => $m->spk_tanggal ?? null,
                        'surat_pesanan_nomor'    => $m->surat_pesanan_nomor ?? null,
                        'surat_pesanan_tanggal'  => $m->surat_pesanan_tanggal ?? null,
                        'kwitansi_nomor'         => $m->kwitansi_nomor ?? null,
                        'kwitansi_tanggal'       => $m->kwitansi_tanggal ?? null,
                        'faktur_nomor'           => $m->faktur_nomor ?? null,
                        'faktur_tanggal'         => $m->faktur_tanggal ?? null,
                        'sp2d_nomor'             => $m->sp2d_nomor ?? null,
                        'sp2d_tanggal'           => $m->sp2d_tanggal ?? null,
                        'bast_dokumen_nomor'     => $m->bast_dokumen_nomor ?? null,
                        'bast_dokumen_tanggal'   => $m->bast_dokumen_tanggal ?? null,
                        'penyedia_nama'          => $m->penyedia_nama ?? null,
                        'penyedia_pemilik'       => $m->penyedia_pemilik ?? null,
                        'penyedia_rekening_nama' => $m->penyedia_rekening_nama ?? null,
                        'penyedia_rekening_nomor'=> $m->penyedia_rekening_nomor ?? null,
                        'penyedia_alamat'        => $m->penyedia_alamat ?? null,
                        'created_at'             => now(),
                        'updated_at'             => now(),
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('astap_pelimpahan_skpds');
        Schema::dropIfExists('astap_belanja_barangs');
        Schema::dropIfExists('astap_belanja_modals');
    }
};
