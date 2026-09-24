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
        if (!Schema::hasTable('mutasi_eksternals')) {
            Schema::create('mutasi_eksternals', function (Blueprint $table) {
                $table->id();
                
                // Relasi ke Master Aset ASTAP
                $table->foreignId('astap_id')->nullable()->constrained('astaps')->cascadeOnDelete();
                
                // Dokumen Berita Acara & Legalitas Mutasi Eksternal
                $table->string('nomor_bamb', 255)->index(); // Nomor BAMB / BAST Resmi
                $table->date('tanggal_mutasi');             // Tanggal Penyerahan / BAST
                $table->string('jenis_mutasi', 100)->default('Transfer Antar-OPD'); // Transfer Antar-OPD, Peminjaman Antar-OPD, Penyerahan ke BPKAD
                $table->enum('tipe', ['masuk', 'keluar'])->default('masuk');       // masuk: diterima RSUD, keluar: diserahkan ke luar
                
                // Pihak Pertama & Kedua
                $table->string('opd_asal', 500);                                   // Instansi / SKPD Pengirim
                $table->string('opd_tujuan', 500)->default('RSUD Dr. H. Koesnadi'); // Instansi Penerima
                $table->foreignId('unit_id')->nullable()->constrained('units')->nullOnDelete(); // Ruangan / Unit Penempatan di RSUD
                $table->string('ruangan_tujuan', 255)->nullable();
                
                // Pejabat Penyerah (Pihak Pertama)
                $table->string('pj_asal_nama', 255)->nullable();
                $table->string('pj_asal_nip', 50)->nullable();
                $table->string('pj_asal_jabatan', 255)->nullable();
                
                // Pejabat Penerima RSUD (Pihak Kedua)
                $table->string('pj_tujuan_nama', 255)->nullable();
                $table->string('pj_tujuan_nip', 50)->nullable();
                $table->string('pj_tujuan_jabatan', 255)->nullable();
                
                // Dasar Hukum & Ketentuan Khusus
                $table->string('nomor_sk_dasar', 255)->nullable();    // Dasar SK Bupati / SK Kepala Daerah
                $table->date('tgl_estimasi_kembali')->nullable();     // Batas waktu pinjam pakai jika peminjaman
                $table->string('status', 100)->default('Disahkan (Selesai)'); // Disahkan (Selesai), Peminjaman Aktif, Menunggu Verifikasi, Dibatalkan
                
                // Kuantitas & Nilai Aset
                $table->integer('jumlah_volume')->default(1);
                $table->string('satuan', 50)->default('Unit');
                $table->decimal('nilai_perolehan', 18, 2)->default(0);
                $table->string('kondisi', 50)->default('Baik');
                
                // Keterangan & Dokumen Lampiran
                $table->text('alasan_mutasi')->nullable();
                $table->string('dokumen_lampiran', 500)->nullable();
                
                // Metadata Pencatat & Soft Delete
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->tinyInteger('is_deleted')->default(0);
                $table->string('deleted_by', 255)->nullable();
                $table->unsignedBigInteger('deleted_by_id')->nullable();
                $table->dateTime('deleted_at')->nullable();
                
                $table->timestamps();
            });
        }

        // Backfill data dari data pelimpahan_skpd / mutasi_masuk yang sudah ada di database
        if (Schema::hasTable('astaps')) {
            $existingPelimpahans = DB::table('astaps')
                ->where(function ($q) {
                    $q->whereIn('sumber_dana', ['pelimpahan_skpd', 'mutasi_masuk']);
                })
                ->get();

            foreach ($existingPelimpahans as $a) {
                $alreadyExists = DB::table('mutasi_eksternals')->where('astap_id', $a->id)->exists();
                if (!$alreadyExists) {
                    // Cari data pelimpahan skpd jika ada
                    $pelimpahan = null;
                    if (Schema::hasTable('astap_pelimpahan_skpds')) {
                        $pelimpahan = DB::table('astap_pelimpahan_skpds')->where('astap_id', $a->id)->first();
                    }

                    $specJson = [];
                    if (!empty($a->spesifikasi_json)) {
                        $specJson = is_array($a->spesifikasi_json) ? $a->spesifikasi_json : (json_decode($a->spesifikasi_json, true) ?? []);
                    }

                    $nomorBamb = $pelimpahan?->nomor_bamb 
                        ?: ($specJson['nomor_bamb'] ?? 'BAMB-SKPD-' . str_pad($a->id, 4, '0', STR_PAD_LEFT));

                    $tanggalMutasi = $pelimpahan?->tanggal_bamb 
                        ?: ($specJson['tanggal_bamb'] ?? ($a->created_at ? substr($a->created_at, 0, 10) : date('Y-m-d')));

                    // Format tanggal jika format dd/mm/yyyy
                    if (preg_match('/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})$/', $tanggalMutasi, $m)) {
                        $tanggalMutasi = sprintf('%04d-%02d-%02d', (int)$m[3], (int)$m[2], (int)$m[1]);
                    }

                    $opdAsal = $pelimpahan?->skpd_asal 
                        ?: ($specJson['skpd_asal'] ?? 'SKPD / Instansi Pemkab Bondowoso');

                    $unitModel = !empty($a->unit_id) && Schema::hasTable('units') 
                        ? DB::table('units')->where('id', $a->unit_id)->first() 
                        : null;
                    $ruangNama = $unitModel?->nama ?: ($a->alamat_barang ?: 'Gudang/Ruangan RSUD');

                    $nilaiReal = (float) ($pelimpahan?->nilai_perolehan ?: ($a->total_realisasi ?: 0));
                    $vol = (int) ($a->jumlah_volume ?: 1);

                    DB::table('mutasi_eksternals')->insert([
                        'astap_id'            => $a->id,
                        'nomor_bamb'          => $nomorBamb,
                        'tanggal_mutasi'      => $tanggalMutasi,
                        'jenis_mutasi'        => 'Transfer Antar-OPD',
                        'tipe'                => 'masuk',
                        'opd_asal'            => $opdAsal,
                        'opd_tujuan'          => 'RSUD dr. H. Koesnadi (' . $ruangNama . ')',
                        'unit_id'             => $a->unit_id ?? null,
                        'ruangan_tujuan'      => $ruangNama,
                        'pj_asal_nama'        => 'Pejabat Penyerah OPD Pengirim',
                        'pj_asal_nip'         => '-',
                        'pj_asal_jabatan'     => 'Pengurus Barang / PPK Asal',
                        'pj_tujuan_nama'      => $a->ppk_nama ?: ($specJson['ppk_nama'] ?? 'dr. H. Yus Priyatna, Sp.P'),
                        'pj_tujuan_nip'       => $a->ppk_nip ?: ($specJson['ppk_nip'] ?? '196904121999031004'),
                        'pj_tujuan_jabatan'   => 'Pengurus Barang / PPK RSUD Dr. H. Koesnadi',
                        'nomor_sk_dasar'      => $nomorBamb,
                        'status'              => 'Disahkan (Selesai)',
                        'jumlah_volume'       => $vol,
                        'satuan'              => $a->satuan ?: 'Unit',
                        'nilai_perolehan'     => $nilaiReal,
                        'kondisi'             => $specJson['kondisi'] ?? 'Baik',
                        'alasan_mutasi'       => $pelimpahan?->keterangan ?: ($specJson['keterangan'] ?? 'Pelimpahan aset barang milik daerah dari SKPD luar ke RSUD dr. H. Koesnadi.'),
                        'user_id'             => $a->user_id ?? null,
                        'is_deleted'          => (int) ($a->is_deleted ?? 0),
                        'deleted_by'          => $a->deleted_by ?? null,
                        'deleted_by_id'       => $a->deleted_by_id ?? null,
                        'deleted_at'          => $a->deleted_at ?? null,
                        'created_at'          => $a->created_at ?? now(),
                        'updated_at'          => $a->updated_at ?? now(),
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
        Schema::dropIfExists('mutasi_eksternals');
    }
};
