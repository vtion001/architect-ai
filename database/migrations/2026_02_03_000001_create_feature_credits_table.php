<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Creates the feature_credits table for tracking per-user, per-feature
     * usage with monthly reset capability.
     */
    public function up(): void
    {
        Schema::create('feature_credits', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->uuid('user_id');
            $table->string('feature_type'); // post_generator, video_generator, blog_generator, click_calendar, document_builder
            $table->integer('limit')->default(0); // -1 = unlimited
            $table->integer('used')->default(0);
            $table->timestamp('reset_at')->nullable(); // For monthly resets
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            // Each user can only have one record per feature type
            $table->unique(['tenant_id', 'user_id', 'feature_type'], 'feature_credits_unique');
            
            // Index for quick lookups
            $table->index(['user_id', 'feature_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feature_credits');
    }
};
