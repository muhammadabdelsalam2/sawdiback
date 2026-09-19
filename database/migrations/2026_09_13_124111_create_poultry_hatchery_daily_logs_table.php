<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('poultry_hatchery_daily_logs');

        Schema::create('poultry_hatchery_daily_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->index();
            $table->foreignId('hatchery_batch_id')->constrained('poultry_hatchery_batches')->cascadeOnDelete();

            $table->date('log_date');
            $table->decimal('temperature', 5, 2); // درجة الحرارة
            $table->decimal('humidity', 5, 2);    // نسبة الرطوبة
            $table->boolean('has_incident')->default(false); // هل حدث عطل أو توقف؟
            $table->text('incident_reason')->nullable();     // سبب التوقف والمدة
            $table->text('notes')->nullable();

            $table->timestamps();

            // اسم مختصر ومحدد للفهرس لتجنب قيد الـ 64 حرفاً في MySQL
            $table->index(['tenant_id', 'hatchery_batch_id', 'log_date'], 'phd_logs_tenant_batch_date_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('poultry_hatchery_daily_logs');
    }
};