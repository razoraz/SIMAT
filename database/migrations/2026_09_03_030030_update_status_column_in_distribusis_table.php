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
        Schema::table('distribusis', function (Blueprint $table) {
            $table->string('status', 50)->default('Menunggu Konfirmasi')->change();
            if (!Schema::hasColumn('distribusis', 'alasan_tolak')) {
                $table->text('alasan_tolak')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('distribusis', function (Blueprint $table) {
            $table->enum('status', ['Telah Diterima', 'Dalam Pengiriman', 'Menunggu Konfirmasi', 'Draft'])->default('Draft')->change();
            if (Schema::hasColumn('distribusis', 'alasan_tolak')) {
                $table->dropColumn('alasan_tolak');
            }
        });
    }
};
