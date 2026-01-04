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
        Schema::create('documents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->uuid('user_id')->nullable();
            $table->string('name');
            $table->string('type'); // PDF, HTML, DOCX, etc.
            $table->string('category')->default('General');
            $table->bigInteger('size')->default(0);
            $table->string('path')->nullable(); // Local storage path or URL
            $table->longText('content')->nullable(); // For generated HTML reports
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });

        Schema::create('knowledge_base_assets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->uuid('user_id')->nullable();
            $table->string('title');
            $table->enum('type', ['file', 'website', 'text', 'youtube'])->default('file');
            $table->string('category')->default('Uncategorized');
            $table->longText('content'); // The processed text for RAG
            $table->string('source_url')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('knowledge_base_assets');
        Schema::dropIfExists('documents');
    }
};