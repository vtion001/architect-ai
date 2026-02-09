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
        Schema::create('signature_requests', function (Blueprint $table) {
            $table->id();
            $table->uuid('document_id');
            $table->uuid('user_id');
            $table->string('signer_name');
            $table->string('signer_email');
            $table->string('subject')->nullable();
            $table->text('message')->nullable();
            $table->string('hellosign_signature_request_id')->nullable();
            $table->string('status')->default('pending'); // pending, sent, viewed, signed, declined
            $table->string('signature_token', 64)->unique();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('viewed_at')->nullable();
            $table->timestamp('signed_at')->nullable();
            $table->json('signature_data')->nullable(); // Store signature image/data
            $table->timestamps();

            $table->foreign('document_id')->references('id')->on('documents')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('signature_requests');
    }
};
