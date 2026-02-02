<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Migrates existing plan values to new tier structure:
     * - standard → starter
     * - enterprise → pro  
     * - master → agency
     */
    public function up(): void
    {
        // Migrate existing plan values to new naming convention
        DB::table('tenants')->where('plan', 'standard')->update(['plan' => 'starter']);
        DB::table('tenants')->where('plan', 'enterprise')->update(['plan' => 'pro']);
        DB::table('tenants')->where('plan', 'master')->update(['plan' => 'agency']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to old naming convention
        DB::table('tenants')->where('plan', 'starter')->update(['plan' => 'standard']);
        DB::table('tenants')->where('plan', 'pro')->update(['plan' => 'enterprise']);
        DB::table('tenants')->where('plan', 'agency')->update(['plan' => 'master']);
    }
};
