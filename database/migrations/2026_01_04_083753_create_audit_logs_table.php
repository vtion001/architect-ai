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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->timestamp('timestamp')->useCurrent();
            $table->uuid('actor_id')->nullable();
            $table->enum('actor_type', ['user', 'system', 'developer']);
            $table->uuid('tenant_id');
            $table->string('action');
            $table->string('resource_type')->nullable();
            $table->uuid('resource_id')->nullable();
            $table->enum('result', ['success', 'failure', 'denied']);
            $table->ipAddress('ip_address')->nullable();
            $table->json('metadata')->nullable();
            $table->text('justification')->nullable();

            $table->foreign('actor_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};