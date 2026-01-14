<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete(); // User-specific categories
            $table->string('name');
            $table->string('color')->default('#3b82f6'); // Tailwind blue-500 equivalent hex
            $table->timestamps();
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->foreignUuid('category_id')->nullable()->constrained('task_categories')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });
        Schema::dropIfExists('task_categories');
    }
};
