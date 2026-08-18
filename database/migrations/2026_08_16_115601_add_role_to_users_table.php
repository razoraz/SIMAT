<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menambahkan kolom role pada tabel users
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['master_admin', 'admin', 'sub_admin'])
                  ->default('sub_admin')
                  ->after('email');
            $table->string('deskripsi')->nullable()->after('role');
        });
    }

    /**
     * Hapus kolom role dan deskripsi pada tabel users
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'deskripsi']);
        });
    }
};
