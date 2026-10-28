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
        // 1. Perbarui tabel astap_belanja_barangs sebagai Faktur Transaksi Induk
        if (Schema::hasTable('astap_belanja_barangs')) {
            Schema::table('astap_belanja_barangs', function (Blueprint $table) {
                if (!Schema::hasColumn('astap_belanja_barangs', 'dokumen_faktur')) {
                    $table->string('dokumen_faktur', 500)->nullable()->after('total_pembelian');
                }
                if (!Schema::hasColumn('astap_belanja_barangs', 'dokumen_kuitansi')) {
                    $table->string('dokumen_kuitansi', 500)->nullable()->after('dokumen_faktur');
                }
                if (!Schema::hasColumn('astap_belanja_barangs', 'status_alokasi')) {
                    $table->string('status_alokasi', 50)->default('gudang')->after('dokumen_kuitansi');
                }
                if (!Schema::hasColumn('astap_belanja_barangs', 'user_id')) {
                    $table->foreignId('user_id')->nullable()->after('keterangan')->constrained('users')->nullOnDelete();
                }
            });

            // Jadikan astap_id nullable jika masih not-null (backward compatibility)
            try {
                Schema::table('astap_belanja_barangs', function (Blueprint $table) {
                    $table->foreignId('astap_id')->nullable()->change();
                });
            } catch (\Throwable $e) {
                // Abaikan jika SQLite engine versi tertentu membatasi change() in-place
            }
        }

        // 2. Tambahkan belanja_barang_id ke tabel astaps
        if (Schema::hasTable('astaps') && !Schema::hasColumn('astaps', 'belanja_barang_id')) {
            Schema::table('astaps', function (Blueprint $table) {
                $table->foreignId('belanja_barang_id')->nullable()->after('id')->constrained('astap_belanja_barangs')->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('astaps') && Schema::hasColumn('astaps', 'belanja_barang_id')) {
            Schema::table('astaps', function (Blueprint $table) {
                $table->dropConstrainedForeignId('belanja_barang_id');
            });
        }

        if (Schema::hasTable('astap_belanja_barangs')) {
            Schema::table('astap_belanja_barangs', function (Blueprint $table) {
                if (Schema::hasColumn('astap_belanja_barangs', 'dokumen_faktur')) {
                    $table->dropColumn('dokumen_faktur');
                }
                if (Schema::hasColumn('astap_belanja_barangs', 'dokumen_kuitansi')) {
                    $table->dropColumn('dokumen_kuitansi');
                }
                if (Schema::hasColumn('astap_belanja_barangs', 'status_alokasi')) {
                    $table->dropColumn('status_alokasi');
                }
                if (Schema::hasColumn('astap_belanja_barangs', 'user_id')) {
                    $table->dropConstrainedForeignId('user_id');
                }
            });
        }
    }
};

