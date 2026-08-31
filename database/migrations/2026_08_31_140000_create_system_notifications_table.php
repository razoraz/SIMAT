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
        Schema::create('system_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('role_target', 50)->default('all'); // 'admin,master_admin', 'sub_admin', 'all'
            $table->foreignId('unit_id')->nullable()->constrained('units')->nullOnDelete();
            $table->string('ruangan_target', 255)->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title', 255);
            $table->text('message');
            $table->string('type', 50)->default('info'); // 'astap', 'distribusi', 'mutasi', 'info'
            $table->string('link', 500)->nullable();
            $table->json('read_by_users')->nullable(); // List ID users who read this notification
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_notifications');
    }
};
