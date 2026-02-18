<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            if (!Schema::hasColumn('tenants', 'plan_id')) {
                $table->foreignId('plan_id')
                    ->nullable()
                    ->constrained('plans')
                    ->cascadeOnUpdate()
                    ->nullOnDelete()
                    ->after('status');

                $table->index(['plan_id']);
            }
        });

        // NOTE:
        // tenants.subscription_plan_id (legacy) stays untouched to avoid breaking old work.
        // From now on, Customer-side will rely on subscriptions.tenant_id + subscriptions.plan_id.
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            if (Schema::hasColumn('tenants', 'plan_id')) {
                $table->dropForeign(['plan_id']);
                $table->dropIndex(['plan_id']);
                $table->dropColumn('plan_id');
            }
        });
    }
};
