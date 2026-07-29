<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('livestock_animals', function (Blueprint $table): void {
            if (! Schema::hasColumn('livestock_animals', 'intended_purpose')) {
                $table->string('intended_purpose')->nullable()->after('health_status');
            }
        });

        Schema::table('crops', function (Blueprint $table): void {
            if (! Schema::hasColumn('crops', 'greenhouse_number')) {
                $table->string('greenhouse_number')->nullable()->after('greenhouse_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('livestock_animals', function (Blueprint $table): void {
            if (Schema::hasColumn('livestock_animals', 'intended_purpose')) {
                $table->dropColumn('intended_purpose');
            }
        });

        Schema::table('crops', function (Blueprint $table): void {
            if (Schema::hasColumn('crops', 'greenhouse_number')) {
                $table->dropColumn('greenhouse_number');
            }
        });
    }
};
