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
        Schema::table('brands', function (Blueprint $table) {
            $table->string('tagline')->nullable()->after('name');
            $table->text('description')->nullable()->after('tagline');
            $table->string('logo_public_id')->nullable()->after('logo_url');
            $table->string('favicon_url')->nullable()->after('logo_public_id');
            $table->json('social_handles')->nullable()->after('contact_info');
            $table->string('industry')->nullable()->after('social_handles');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('brands', function (Blueprint $table) {
            $table->dropColumn([
                'tagline',
                'description',
                'logo_public_id',
                'favicon_url',
                'social_handles',
                'industry',
            ]);
        });
    }
};
