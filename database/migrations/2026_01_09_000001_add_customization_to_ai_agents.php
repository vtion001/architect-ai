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
        // Add new customization fields to ai_agents table
        Schema::table('ai_agents', function (Blueprint $table) {
            // Appearance customization
            $table->string('avatar_url')->nullable()->after('is_active');
            $table->string('primary_color', 7)->default('#00F2FF')->after('avatar_url');
            $table->string('welcome_message', 500)->default('Hello! How can I assist you today?')->after('primary_color');
            
            // Behavior settings
            $table->string('model')->nullable()->after('welcome_message');
            $table->float('temperature')->default(0.7)->after('model');
            $table->integer('max_tokens')->default(2000)->after('temperature');
            $table->text('system_prompt')->nullable()->after('max_tokens');
            
            // Widget settings
            $table->string('widget_position', 20)->default('bottom-right')->after('system_prompt');
            $table->boolean('widget_enabled')->default(true)->after('widget_position');
            $table->json('allowed_domains')->nullable()->after('widget_enabled');
        });

        // Create agent_conversations table
        Schema::create('agent_conversations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('agent_id');
            $table->string('session_id');
            $table->uuid('user_id')->nullable();
            $table->json('messages')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->foreign('agent_id')->references('id')->on('ai_agents')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            
            $table->unique(['agent_id', 'session_id']);
            $table->index('session_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agent_conversations');

        Schema::table('ai_agents', function (Blueprint $table) {
            $table->dropColumn([
                'avatar_url',
                'primary_color',
                'welcome_message',
                'model',
                'temperature',
                'max_tokens',
                'system_prompt',
                'widget_position',
                'widget_enabled',
                'allowed_domains',
            ]);
        });
    }
};
