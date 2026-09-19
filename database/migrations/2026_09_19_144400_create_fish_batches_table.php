<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fish_batches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->index();
            $table->unsignedBigInteger('farm_id')->nullable()->index();
            $table->string('batch_code', 50)->nullable(); // كود الدفعة / الحوض
            $table->string('pond_name', 100); // اسم الحوض
            $table->string('fish_type', 100); // نوع السمك: بلطي، بوري، إلخ
            $table->unsignedInteger('initial_count')->default(0); // عدد الزريعة
            $table->unsignedInteger('current_count')->default(0); // العدد الحي
            $table->unsignedInteger('mortality_count')->default(0); // النافق
            $table->decimal('initial_weight_g', 8, 2)->default(0); // متوسط وزن الزريعة بالجرام
            $table->decimal('current_weight_g', 8, 2)->default(0); // متوسط الوزن الحالي بالجرام
            $table->decimal('target_weight_g', 8, 2)->default(0); // الوزن المستهدف
            $table->decimal('feed_consumed_kg', 10, 2)->default(0); // استهلاك العلف كجم
            $table->decimal('total_cost', 12, 2)->default(0); // إجمالي التكاليف
            $table->date('started_at');
            $table->date('harvested_at')->nullable();
            $table->enum('status', ['active', 'harvested', 'paused'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fish_batches');
    }
};
