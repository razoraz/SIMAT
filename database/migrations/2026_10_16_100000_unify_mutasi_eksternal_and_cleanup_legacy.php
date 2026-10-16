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
        // 1. Pastikan seluruh data dari astap_pelimpahan_skpds tersinkronisasi 100% ke mutasi_eksternals
        if (Schema::hasTable('astap_pelimpahan_skpds') && Schema::hasTable('mutasi_eksternals')) {
            $pelimpahans = DB::table('astap_pelimpahan_skpds')->get();

            foreach ($pelimpahans as $p) {
                $exists = DB::table('mutasi_eksternals')
                    ->where('astap_id', $p->astap_id)
                    ->where('tipe', 'masuk')
                    ->exists();

                if (!$exists) {
                    $astap = DB::table('astaps')->where('id', $p->astap_id)->first();
                    $unit = (!empty($astap->unit_id) && Schema::hasTable('units'))
                        ? DB::table('units')->where('id', $astap->unit_id)->first()
                        : null;
                    $ruangNama = $unit?->nama ?: ($astap?->alamat_barang ?: 'Gudang/Ruangan RSUD');

                    DB::table('mutasi_eksternals')->insert([
                        'astap_id'            => $p->astap_id,
                        'nomor_bamb'          => $p->nomor_bamb,
                        'tanggal_mutasi'      => $p->tanggal_bamb ?: ($astap?->created_at ? substr($astap->created_at, 0, 10) : date('Y-m-d')),
                        'jenis_mutasi'        => 'Transfer Antar-OPD',
                        'tipe'                => 'masuk',
                        'opd_asal'            => $p->skpd_asal ?: 'SKPD / Instansi Luar',
                        'alamat_instansi'     => $p->alamat_instansi ?? null,
                        'opd_tujuan'          => 'RSUD dr. H. Koesnadi (' . $ruangNama . ')',
                        'unit_id'             => $astap?->unit_id ?? null,
                        'ruangan_tujuan'      => $ruangNama,
                        'pj_asal_nama'        => 'Pejabat Penyerah OPD Pengirim',
                        'pj_asal_nip'         => '-',
                        'pj_asal_jabatan'     => 'Pengurus Barang / PPK Asal',
                        'pj_tujuan_nama'      => 'BUDI HARTONO, S.Sos',
                        'pj_tujuan_nip'       => '197602292008011010',
                        'pj_tujuan_jabatan'   => 'Pengurus Barang Pengguna RSUD Dr. H. Koesnadi',
                        'nomor_sk_dasar'      => $p->nomor_bamb,
                        'status'              => 'Disahkan (Selesai)',
                        'jumlah_volume'       => (int) ($astap?->jumlah_volume ?: 1),
                        'satuan'              => $astap?->satuan ?: 'Unit',
                        'nilai_perolehan'     => (float) ($p->nilai_perolehan ?: ($astap?->total_realisasi ?: 0)),
                        'kondisi'             => 'Baik',
                        'alasan_mutasi'       => $p->keterangan ?: 'Mutasi masuk aset barang milik daerah dari OPD luar ke RSUD dr. H. Koesnadi.',
                        'user_id'             => $astap?->user_id ?? null,
                        'is_deleted'          => (int) ($astap?->is_deleted ?? 0),
                        'created_at'          => $p->created_at ?? now(),
                        'updated_at'          => $p->updated_at ?? now(),
                    ]);
                }
            }
        }

        // 2. Standarisasi sumber_dana di tabel astaps dan spesifikasi_json menjadi mutasi_eksternal
        if (Schema::hasTable('astaps')) {
            DB::table('astaps')
                ->whereIn('sumber_dana', ['pelimpahan_skpd', 'pelimpahan', 'mutasi_masuk', 'mutasi'])
                ->update(['sumber_dana' => 'mutasi_eksternal']);

            $astapsWithSpec = DB::table('astaps')->whereNotNull('spesifikasi_json')->get(['id', 'spesifikasi_json']);
            foreach ($astapsWithSpec as $item) {
                $spec = json_decode($item->spesifikasi_json, true);
                if (is_array($spec) && isset($spec['sumber_dana']) && in_array($spec['sumber_dana'], ['pelimpahan_skpd', 'pelimpahan', 'mutasi_masuk', 'mutasi'], true)) {
                    $spec['sumber_dana'] = 'mutasi_eksternal';
                    DB::table('astaps')->where('id', $item->id)->update([
                        'spesifikasi_json' => json_encode($spec, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                    ]);
                }
            }
        }

        // 3. Drop tabel ekstensi legacy astap_pelimpahan_skpds secara aman
        Schema::dropIfExists('astap_pelimpahan_skpds');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('astap_pelimpahan_skpds')) {
            Schema::create('astap_pelimpahan_skpds', function (Blueprint $table) {
                $table->id();
                $table->foreignId('astap_id')->constrained('astaps')->cascadeOnDelete()->unique();
                $table->string('skpd_asal', 500);
                $table->string('nomor_bamb', 255);
                $table->date('tanggal_bamb')->nullable();
                $table->decimal('nilai_perolehan', 15, 2)->default(0);
                $table->text('keterangan')->nullable();
                $table->string('alamat_instansi', 500)->nullable();
                $table->timestamps();
            });

            // Restore data dari mutasi_eksternals (tipe masuk)
            $mutasis = DB::table('mutasi_eksternals')
                ->where('tipe', 'masuk')
                ->whereNotNull('astap_id')
                ->get();

            foreach ($mutasis as $m) {
                DB::table('astap_pelimpahan_skpds')->insert([
                    'astap_id'        => $m->astap_id,
                    'skpd_asal'       => $m->opd_asal,
                    'nomor_bamb'      => $m->nomor_bamb,
                    'tanggal_bamb'    => $m->tanggal_mutasi,
                    'nilai_perolehan' => $m->nilai_perolehan,
                    'keterangan'      => $m->alasan_mutasi,
                    'alamat_instansi' => $m->alamat_instansi,
                    'created_at'      => $m->created_at ?? now(),
                    'updated_at'      => $m->updated_at ?? now(),
                ]);
            }
        }
    }
};
