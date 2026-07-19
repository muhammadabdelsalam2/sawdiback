<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('livestock_animals', function (Blueprint $table) {
            $table->foreignId('pen_id')->nullable()->after('tenant_id')->constrained('farm_pens')->nullOnDelete();
            $table->index(['tenant_id', 'pen_id']);
        });

        Schema::table('crops', function (Blueprint $table) {
            $table->foreignId('farm_id')->nullable()->after('tenant_id')->constrained('farms')->nullOnDelete();
            $table->index(['tenant_id', 'farm_id']);
        });

        foreach (['poultry_broiler_cycles', 'poultry_layer_flocks', 'poultry_chicken_breeds'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->foreign('pen_id')->references('id')->on('farm_pens')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        foreach (['poultry_chicken_breeds', 'poultry_layer_flocks', 'poultry_broiler_cycles'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropForeign(['pen_id']);
            });
        }

        Schema::table('crops', function (Blueprint $table) {
            $table->dropForeign(['farm_id']);
            $table->dropIndex(['tenant_id', 'farm_id']);
            $table->dropColumn('farm_id');
        });

        Schema::table('livestock_animals', function (Blueprint $table) {
            $table->dropForeign(['pen_id']);
            $table->dropIndex(['tenant_id', 'pen_id']);
            $table->dropColumn('pen_id');
        });
    }
};
