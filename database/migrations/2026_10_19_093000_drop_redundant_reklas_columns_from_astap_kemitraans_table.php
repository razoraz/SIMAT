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
        $columnsToDrop = [
            'is_transferred_to_rsud',
            'reklas_id',
            'target_kib_reklas',
            'target_kode_108',
            'nilai_perolehan_rsud',
        ];

        $existingColumns = array_filter($columnsToDrop, function ($col) {
            return Schema::hasColumn('astap_kemitraans', $col);
        });

        if (!empty($existingColumns)) {
            Schema::table('astap_kemitraans', function (Blueprint $table) use ($existingColumns) {
                $table->dropColumn($existingColumns);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('astap_kemitraans', function (Blueprint $table) {
            $table->boolean('is_transferred_to_rsud')->default(false)->after('catatan_terminasi');
            $table->unsignedBigInteger('reklas_id')->nullable()->after('is_transferred_to_rsud');
            $table->string('target_kib_reklas', 50)->nullable()->after('reklas_id');
            $table->string('target_kode_108', 50)->nullable()->after('target_kib_reklas');
            $table->decimal('nilai_perolehan_rsud', 18, 2)->nullable()->after('target_kode_108');
        });
    }
};
