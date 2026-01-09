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
        Schema::create('brands', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('logo_url')->nullable();
            $table->json('colors')->nullable(); // { primary: '#...', secondary: '#...' }
            $table->json('typography')->nullable(); // { headings: 'Inter', body: 'Roboto' }
            $table->json('voice_profile')->nullable(); // { tone: 'Professional', keywords: [...], avoidance: [...] }
            $table->json('contact_info')->nullable(); // { website: '...', social: '...' }
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('brands');
    }
};
