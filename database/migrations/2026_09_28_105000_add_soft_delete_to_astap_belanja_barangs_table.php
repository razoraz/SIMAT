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
        if (Schema::hasTable('astap_belanja_barangs')) {
            Schema::table('astap_belanja_barangs', function (Blueprint $table) {
                if (!Schema::hasColumn('astap_belanja_barangs', 'is_deleted')) {
                    $table->tinyInteger('is_deleted')->default(0)->index()->after('keterangan');
                }
                if (!Schema::hasColumn('astap_belanja_barangs', 'deleted_at')) {
                    $table->timestamp('deleted_at')->nullable()->after('is_deleted');
                }
                if (!Schema::hasColumn('astap_belanja_barangs', 'deleted_by')) {
                    $table->string('deleted_by', 255)->nullable()->after('deleted_at');
                }
                if (!Schema::hasColumn('astap_belanja_barangs', 'deleted_by_id')) {
                    $table->unsignedBigInteger('deleted_by_id')->nullable()->after('deleted_by');
                }
                if (!Schema::hasColumn('astap_belanja_barangs', 'alasan_hapus')) {
                    $table->text('alasan_hapus')->nullable()->after('deleted_by_id');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('astap_belanja_barangs')) {
            Schema::table('astap_belanja_barangs', function (Blueprint $table) {
                $cols = ['is_deleted', 'deleted_at', 'deleted_by', 'deleted_by_id', 'alasan_hapus'];
                foreach ($cols as $col) {
                    if (Schema::hasColumn('astap_belanja_barangs', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};
