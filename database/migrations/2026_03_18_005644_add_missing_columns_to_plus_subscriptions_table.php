<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plus_subscriptions', function (Blueprint $table) {
            if (!Schema::hasColumn('plus_subscriptions', 'paused_until')) {
                $table->date('paused_until')->nullable()->after('next_billing_at');
            }

            if (!Schema::hasColumn('plus_subscriptions', 'vacation_mode')) {
                $table->boolean('vacation_mode')->default(false)->after('paused_until');
            }

            if (!Schema::hasColumn('plus_subscriptions', 'canceled_at')) {
                $table->timestamp('canceled_at')->nullable()->after('vacation_mode');
            }
        });
    }

    public function down(): void
    {
        Schema::table('plus_subscriptions', function (Blueprint $table) {
            if (Schema::hasColumn('plus_subscriptions', 'canceled_at')) {
                $table->dropColumn('canceled_at');
            }

            if (Schema::hasColumn('plus_subscriptions', 'vacation_mode')) {
                $table->dropColumn('vacation_mode');
            }

            if (Schema::hasColumn('plus_subscriptions', 'paused_until')) {
                $table->dropColumn('paused_until');
            }
        });
    }
};
