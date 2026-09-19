<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('farm_pens', function (Blueprint $table) {
            $table->unsignedInteger('current_count')->default(0)->after('capacity');
        });
    }

    public function down(): void
    {
        Schema::table('farm_pens', function (Blueprint $table) {
            $table->dropColumn('current_count');
        });
    }
};