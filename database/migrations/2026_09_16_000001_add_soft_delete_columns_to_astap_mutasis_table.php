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
        Schema::table('astap_mutasis', function (Blueprint $table) {
            // Label status hapus: 0 = data aktif / belum dihapus, 1 = data pernah dihapus
            $table->tinyInteger('is_deleted')->default(0)->after('alasan_penolakan');

            // Pengguna yang menghapus data
            $table->string('deleted_by', 255)->nullable()->after('is_deleted');
            $table->foreignId('deleted_by_id')->nullable()->constrained('users')->nullOnDelete()->after('deleted_by');

            // Tanggal dan jam data dihapus
            $table->timestamp('deleted_at')->nullable()->after('deleted_by_id');

            $table->index('is_deleted');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('astap_mutasis', function (Blueprint $table) {
            $table->dropColumn([
                'is_deleted',
                'deleted_by',
                'deleted_by_id',
                'deleted_at',
            ]);
        });
    }
};
