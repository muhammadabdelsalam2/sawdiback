<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('poultry_hatchery_daily_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('poultry_hatchery_daily_logs', 'stoppage_duration_hours')) {
                $table->decimal('stoppage_duration_hours', 5, 2)->nullable()->after('has_incident');
            }
        });
    }

    public function down(): void
    {
        Schema::table('poultry_hatchery_daily_logs', function (Blueprint $table) {
            if (Schema::hasColumn('poultry_hatchery_daily_logs', 'stoppage_duration_hours')) {
                $table->dropColumn('stoppage_duration_hours');
            }
        });
    }
};