<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Convert 'contents' table
        DB::statement('ALTER TABLE contents MODIFY id BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE contents DROP PRIMARY KEY');
        Schema::table('contents', function (Blueprint $table) {
            $table->uuid('id')->primary()->change();
        });

        // Convert 'researches' table
        DB::statement('ALTER TABLE researches MODIFY id BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE researches DROP PRIMARY KEY');
        Schema::table('researches', function (Blueprint $table) {
            $table->uuid('id')->primary()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Not easily reversible without data loss or complex mapping
    }
};