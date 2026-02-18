<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            if (!Schema::hasColumn('subscriptions', 'tenant_id')) {
                $table->uuid('tenant_id')->nullable()->after('id');

                $table->foreign('tenant_id')
                    ->references('id')->on('tenants')
                    ->cascadeOnUpdate()
                    ->cascadeOnDelete();

                $table->index(['tenant_id', 'status']);
                $table->index(['tenant_id', 'plan_id']);
            }
        });

        // Safe backfill (if any legacy subscriptions exist)
        if (Schema::hasColumn('subscriptions', 'tenant_id')) {
            DB::statement("
                UPDATE subscriptions s
                JOIN users u ON u.id = s.customer_id
                SET s.tenant_id = u.tenant_id
                WHERE s.tenant_id IS NULL AND u.tenant_id IS NOT NULL
            ");
        }
    }

    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            if (Schema::hasColumn('subscriptions', 'tenant_id')) {
                $table->dropForeign(['tenant_id']);
                $table->dropIndex(['tenant_id', 'status']);
                $table->dropIndex(['tenant_id', 'plan_id']);
                $table->dropColumn('tenant_id');
            }
        });
    }
};
