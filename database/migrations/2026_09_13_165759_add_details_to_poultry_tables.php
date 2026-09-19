<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. توسيع حقول دفعات التفقيس بأمان لكل عمود
        Schema::table('poultry_hatchery_batches', function (Blueprint $table) {
            if (!Schema::hasColumn('poultry_hatchery_batches', 'egg_source')) {
                $table->enum('egg_source', ['farm', 'purchased'])->default('farm')->nullable();
            }
            if (!Schema::hasColumn('poultry_hatchery_batches', 'farm_pen_id')) {
                $table->unsignedBigInteger('farm_pen_id')->nullable();
            }
            if (!Schema::hasColumn('poultry_hatchery_batches', 'seller_name')) {
                $table->string('seller_name')->nullable();
            }
            if (!Schema::hasColumn('poultry_hatchery_batches', 'seller_phone')) {
                $table->string('seller_phone')->nullable();
            }
            if (!Schema::hasColumn('poultry_hatchery_batches', 'seller_location')) {
                $table->string('seller_location')->nullable();
            }
            if (!Schema::hasColumn('poultry_hatchery_batches', 'purchase_amount')) {
                $table->decimal('purchase_amount', 12, 2)->default(0);
            }
        });

        // 2. جدول ربط سلالات البيض داخل دفعة التفقيس
        if (!Schema::hasTable('poultry_hatchery_batch_breeds')) {
            Schema::create('poultry_hatchery_batch_breeds', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('hatchery_batch_id');
                $table->unsignedBigInteger('breed_id');
                $table->integer('egg_count')->default(0);
                $table->timestamps();

                $table->foreign('hatchery_batch_id')->references('id')->on('poultry_hatchery_batches')->cascadeOnDelete();
            });
        }

        // 3. جدول المتابعة اليومية للحضانة (حرارة، رطوبة، أعطال)
        if (!Schema::hasTable('poultry_hatchery_daily_logs')) {
            Schema::create('poultry_hatchery_daily_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('hatchery_batch_id');
                $table->date('log_date');
                $table->decimal('temperature', 5, 2)->nullable();
                $table->decimal('humidity', 5, 2)->nullable();
                $table->boolean('has_stoppage')->default(false);
                $table->integer('stoppage_minutes')->default(0);
                $table->text('stoppage_reason')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->foreign('hatchery_batch_id')->references('id')->on('poultry_hatchery_batches')->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('poultry_hatchery_daily_logs');
        Schema::dropIfExists('poultry_hatchery_batch_breeds');

        Schema::table('poultry_hatchery_batches', function (Blueprint $table) {
            $columnsToDrop = [];
            foreach (['egg_source', 'farm_pen_id', 'seller_name', 'seller_phone', 'seller_location', 'purchase_amount'] as $col) {
                if (Schema::hasColumn('poultry_hatchery_batches', $col)) {
                    $columnsToDrop[] = $col;
                }
            }
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};