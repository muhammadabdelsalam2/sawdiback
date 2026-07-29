<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('poultry_hatchery_machines', function (Blueprint $table): void {
            if (! Schema::hasColumn('poultry_hatchery_machines', 'farm_id')) {
                $table->foreignId('farm_id')
                    ->nullable()
                    ->after('tenant_id')
                    ->constrained('farms')
                    ->cascadeOnUpdate()
                    ->nullOnDelete();

                $table->index(['tenant_id', 'farm_id']);
            }
        });

        Schema::table('crop_seedling_stocks', function (Blueprint $table): void {
            if (! Schema::hasColumn('crop_seedling_stocks', 'farm_id')) {
                $table->foreignId('farm_id')
                    ->nullable()
                    ->after('tenant_id')
                    ->constrained('farms')
                    ->cascadeOnUpdate()
                    ->nullOnDelete();

                $table->index(['tenant_id', 'farm_id']);
            }
        });

        Schema::table('journal_entries', function (Blueprint $table): void {
            if (! Schema::hasColumn('journal_entries', 'farm_id')) {
                $table->foreignId('farm_id')
                    ->nullable()
                    ->after('tenant_id')
                    ->constrained('farms')
                    ->cascadeOnUpdate()
                    ->nullOnDelete();

                $table->index(['tenant_id', 'farm_id']);
            }
        });

        Schema::table('expenses', function (Blueprint $table): void {
            if (! Schema::hasColumn('expenses', 'farm_id')) {
                $table->foreignId('farm_id')
                    ->nullable()
                    ->after('tenant_id')
                    ->constrained('farms')
                    ->cascadeOnUpdate()
                    ->nullOnDelete();

                $table->index(['tenant_id', 'farm_id']);
            }
        });

        Schema::table('vaccine_batches', function (Blueprint $table): void {
            if (! Schema::hasColumn('vaccine_batches', 'farm_id')) {
                $table->foreignId('farm_id')
                    ->nullable()
                    ->after('vaccine_id')
                    ->constrained('farms')
                    ->cascadeOnUpdate()
                    ->nullOnDelete();

                $table->index(['tenant_id', 'farm_id']);
            }
        });
    }

    public function down(): void
    {
        foreach (['vaccine_batches', 'expenses', 'journal_entries', 'crop_seedling_stocks', 'poultry_hatchery_machines'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName): void {
                if (Schema::hasColumn($tableName, 'farm_id')) {
                    $table->dropForeign(['farm_id']);
                    $table->dropIndex(['tenant_id', 'farm_id']);
                    $table->dropColumn('farm_id');
                }
            });
        }
    }
};
