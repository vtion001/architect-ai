<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->boolean('alarm_enabled')->default(false);
            $table->string('alarm_sound')->nullable()->default('default'); // 'default', 'chime', 'bell', etc.
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn(['alarm_enabled', 'alarm_sound']);
        });
    }
};
