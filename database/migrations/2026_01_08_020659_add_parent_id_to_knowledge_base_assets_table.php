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
        Schema::table('knowledge_base_assets', function (Blueprint $table) {
            $table->uuid('parent_id')->nullable()->after('id');
            // Update enum type to include 'folder'
            // We can't easily change enum in all drivers without raw SQL or doctrine,
            // but for this environment, we might just use string or add to enum if supported.
            // Best practice for SQLite/MySQL compatibility in quick migration is often raw statement if needed,
            // but let's try just modifying the column if possible or adding a new one.
            // Actually, let's just make 'type' a string for flexibility if it isn't already, OR modify the enum.
            // The previous migration defined it as: enum('type', ['file', 'website', 'text', 'youtube'])

            // For safety and compatibility, we'll try to modify the column.
            // If Doctrine DBAL is not installed, this might fail.
            // Alternative: Just rely on 'file' type for now or add a new 'is_folder' boolean?
            // Let's assume we can add 'folder' to the allowed types.
            // Raw SQL for MySQL:
            $table->string('type')->change(); // Change to string to allow 'folder' and future types easily.

            $table->foreign('parent_id')->references('id')->on('knowledge_base_assets')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('knowledge_base_assets', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn('parent_id');
            // We won't revert the type change to enum to avoid data loss or complexity
        });
    }
};
