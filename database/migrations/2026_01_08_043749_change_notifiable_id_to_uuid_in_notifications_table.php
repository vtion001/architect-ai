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
        Schema::table('notifications', function (Blueprint $table) {
            // Drop the existing columns created by $table->morphs('notifiable')
            // These are usually 'notifiable_type' (varchar) and 'notifiable_id' (bigint unsigned)

            // Note: Since we have data or foreign key constraints potentially, we must be careful.
            // But this is a dev environment fix.

            // Option 1: Modify the column directly if possible (MySQL 5.7+ supports renaming/modifying type)
            // $table->uuid('notifiable_id')->change();
            // BUT: 'notifiable_id' currently holds BIGINTs if any data existed, but here we see a UUID string trying to be inserted.
            // The error said: Incorrect integer value: 'a0c8d839-...' for column 'notifiable_id'
            // This confirms 'notifiable_id' IS currently an Integer, but we are passing a UUID.

            // We need to change it to CHAR(36) or UUID.
            $table->char('notifiable_id', 36)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->unsignedBigInteger('notifiable_id')->change();
        });
    }
};
