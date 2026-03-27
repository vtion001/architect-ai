<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * SECURITY: This migration adds tenant_id for data isolation.
     * AgentConversation records must be scoped to tenants to prevent data leakage.
     */
    public function up(): void
    {
        Schema::table('agent_conversations', function (Blueprint $table) {
            $table->uuid('tenant_id')->nullable()->after('id');
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->index('tenant_id');
        });

        // Backfill tenant_id from the related agent's tenant_id
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement('
                UPDATE agent_conversations ac
                SET tenant_id = (
                    SELECT tenant_id FROM ai_agents aa WHERE aa.id = ac.agent_id
                )
                WHERE ac.tenant_id IS NULL
            ');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agent_conversations', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
            $table->dropIndex(['tenant_id']);
            $table->dropColumn('tenant_id');
        });
    }
};
