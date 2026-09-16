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
        $tables = [
            'astaps',
            'astap_registers',
            'distribusis',
            'astap_bast_triwulans',
            'units',
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    if (!Schema::hasColumn($tableName, 'is_deleted')) {
                        $table->tinyInteger('is_deleted')->default(0)->index()->comment('0 = aktif, 1 = terhapus (soft delete)');
                    }
                    if (!Schema::hasColumn($tableName, 'deleted_by')) {
                        $table->string('deleted_by')->nullable()->comment('Nama dan role user yang menghapus data');
                    }
                    if (!Schema::hasColumn($tableName, 'deleted_by_id')) {
                        $table->unsignedBigInteger('deleted_by_id')->nullable()->index();
                    }
                    if (!Schema::hasColumn($tableName, 'deleted_at')) {
                        $table->timestamp('deleted_at')->nullable()->index()->comment('Waktu dan tanggal penghapusan');
                    }
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'astaps',
            'astap_registers',
            'distribusis',
            'astap_bast_triwulans',
            'units',
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    $columns = [];
                    if (Schema::hasColumn($tableName, 'is_deleted')) $columns[] = 'is_deleted';
                    if (Schema::hasColumn($tableName, 'deleted_by')) $columns[] = 'deleted_by';
                    if (Schema::hasColumn($tableName, 'deleted_by_id')) $columns[] = 'deleted_by_id';
                    if (Schema::hasColumn($tableName, 'deleted_at')) $columns[] = 'deleted_at';
                    
                    if (!empty($columns)) {
                        $table->dropColumn($columns);
                    }
                });
            }
        }
    }
};
