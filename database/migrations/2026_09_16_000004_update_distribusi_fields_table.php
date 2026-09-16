<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tambah kolom alasan_penolakan di distribusis dan migrasikan data alasan_tolak jika ada
        if (Schema::hasTable('distribusis')) {
            Schema::table('distribusis', function (Blueprint $table) {
                if (!Schema::hasColumn('distribusis', 'alasan_penolakan')) {
                    $table->text('alasan_penolakan')->nullable()->after('keterangan');
                }
            });

            // Salin data lama dari alasan_tolak ke alasan_penolakan jika ada
            if (Schema::hasColumn('distribusis', 'alasan_tolak')) {
                try {
                    DB::table('distribusis')
                        ->whereNull('alasan_penolakan')
                        ->whereNotNull('alasan_tolak')
                        ->update(['alasan_penolakan' => DB::raw('alasan_tolak')]);
                } catch (\Throwable $e) {
                    // Ignore if driver error
                }

                Schema::table('distribusis', function (Blueprint $table) {
                    $table->dropColumn('alasan_tolak');
                });
            }
        }

        // 2. Hapus kolom keterangan dari distribusi_items (redundant / tidak digunakan lagi)
        if (Schema::hasTable('distribusi_items') && Schema::hasColumn('distribusi_items', 'keterangan')) {
            Schema::table('distribusi_items', function (Blueprint $table) {
                $table->dropColumn('keterangan');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('distribusi_items') && !Schema::hasColumn('distribusi_items', 'keterangan')) {
            Schema::table('distribusi_items', function (Blueprint $table) {
                $table->text('keterangan')->nullable()->after('qty');
            });
        }

        if (Schema::hasTable('distribusis')) {
            Schema::table('distribusis', function (Blueprint $table) {
                if (!Schema::hasColumn('distribusis', 'alasan_tolak')) {
                    $table->text('alasan_tolak')->nullable()->after('status');
                }
                if (Schema::hasColumn('distribusis', 'alasan_penolakan')) {
                    $table->dropColumn('alasan_penolakan');
                }
            });
        }
    }
};
