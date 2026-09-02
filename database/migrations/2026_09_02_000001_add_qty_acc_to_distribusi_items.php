<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('distribusi_items', function (Blueprint $table) {
            // Volume yang di-ACC oleh Admin/Master Admin.
            // NULL = belum di-ACC, 0 = ditolak, >0 = volume yang disetujui.
            $table->unsignedInteger('qty_acc')->nullable()->after('qty');
        });
    }

    public function down(): void
    {
        Schema::table('distribusi_items', function (Blueprint $table) {
            $table->dropColumn('qty_acc');
        });
    }
};
