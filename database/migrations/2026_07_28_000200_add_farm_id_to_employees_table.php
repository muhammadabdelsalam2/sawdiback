<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table): void {
            if (! Schema::hasColumn('employees', 'farm_id')) {
                $table->foreignId('farm_id')
                    ->nullable()
                    ->after('tenant_id')
                    ->constrained('farms')
                    ->cascadeOnUpdate()
                    ->nullOnDelete();

                $table->index(['tenant_id', 'farm_id']);
            }
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table): void {
            if (Schema::hasColumn('employees', 'farm_id')) {
                $table->dropForeign(['farm_id']);
                $table->dropIndex(['tenant_id', 'farm_id']);
                $table->dropColumn('farm_id');
            }
        });
    }
};
