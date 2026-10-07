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
        Schema::table('astap_reklasis', function (Blueprint $table) {
            if (!Schema::hasColumn('astap_reklasis', 'is_deleted')) {
                $table->tinyInteger('is_deleted')->default(0)->index()->after('sub_koreksi');
            }
            if (!Schema::hasColumn('astap_reklasis', 'deleted_at')) {
                $table->timestamp('deleted_at')->nullable()->index()->after('is_deleted');
            }
            if (!Schema::hasColumn('astap_reklasis', 'deleted_by')) {
                $table->string('deleted_by', 255)->nullable()->after('deleted_at');
            }
            if (!Schema::hasColumn('astap_reklasis', 'deleted_by_id')) {
                $table->unsignedBigInteger('deleted_by_id')->nullable()->after('deleted_by');
            }
            if (!Schema::hasColumn('astap_reklasis', 'alasan_hapus')) {
                $table->text('alasan_hapus')->nullable()->after('deleted_by_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('astap_reklasis', function (Blueprint $table) {
            $cols = ['is_deleted', 'deleted_at', 'deleted_by', 'deleted_by_id', 'alasan_hapus'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('astap_reklasis', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
