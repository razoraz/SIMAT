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
        Schema::create('access_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action_type', 50); // created, updated, permissions_escalated, deleted, restored
            $table->text('summary')->nullable(); // Human-readable explanation
            $table->json('old_permissions')->nullable();
            $table->json('new_permissions')->nullable();
            $table->string('old_role', 50)->nullable();
            $table->string('new_role', 50)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('access_audit_logs');
    }
};
