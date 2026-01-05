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
        Schema::table('waitlists', function (Blueprint $table) {
            if (!Schema::hasColumn('waitlists', 'status')) {
                $table->string('status')->default('pending')->after('agency_name');
            }
            if (!Schema::hasColumn('waitlists', 'provisioned_at')) {
                $table->timestamp('provisioned_at')->nullable()->after('status');
            }
            if (!Schema::hasColumn('waitlists', 'rejected_at')) {
                $table->timestamp('rejected_at')->nullable()->after('provisioned_at');
            }
            if (!Schema::hasColumn('waitlists', 'user_id')) {
                $table->foreignUuid('user_id')->nullable()->after('rejected_at')->constrained()->nullOnDelete();
            }
            if (!Schema::hasColumn('waitlists', 'tenant_id')) {
                $table->foreignUuid('tenant_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('waitlists', function (Blueprint $table) {
            $table->dropConstrainedForeignId('tenant_id');
            $table->dropConstrainedForeignId('user_id');
            $table->dropColumn(['status', 'provisioned_at', 'rejected_at']);
        });
    }
};
