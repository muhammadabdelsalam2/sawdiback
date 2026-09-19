<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('employee_records')) {
            Schema::create('employee_records', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('tenant_id')->index();
                $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();

                $table->enum('record_type', [
                    'achievement',   // إنجازات وإبداعات
                    'qualification', // شهادة علمية
                    'experience',    // خبرة سابقة
                    'training',      // تطوير مستمر ودورات
                    'violation',     // تهرب ومخالفات
                    'note',          // ملاحظات
                ]);

                $table->string('title');
                $table->text('description')->nullable();
                $table->date('event_date');
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_records');
    }
};
