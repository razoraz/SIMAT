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
        Schema::table('users', function (Blueprint $table) {
            $table->json('permissions')->nullable()->after('penugasan');
        });

        // Berikan seluruh hak akses secara default bagi akun ber-role 'admin' yang sudah ada
        $allPermissions = json_encode(['astap', 'distribusi', 'bast', 'mutasi', 'unit', 'master_data', 'users']);
        DB::table('users')
            ->where('role', 'admin')
            ->whereNull('permissions')
            ->update(['permissions' => $allPermissions]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('permissions');
        });
    }
};
