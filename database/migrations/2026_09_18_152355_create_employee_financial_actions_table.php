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
        if (!Schema::hasTable('employee_financial_actions')) {
            Schema::create('employee_financial_actions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('tenant_id')->index();
                $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();

                $table->enum('type', [
                    'advance_payment', // سلفة
                    'deduction',       // استقطاع شهري
                    'salary_increase', // زيادة راتب
                    'bonus',           // مكافأة مالية
                    'gift',            // هدية عينية
                ]);

                $table->decimal('amount', 12, 2)->default(0);
                $table->decimal('percentage', 5, 2)->nullable();
                $table->integer('installments_count')->default(1);
                $table->decimal('installment_amount', 12, 2)->default(0);
                $table->decimal('remaining_amount', 12, 2)->default(0);

                $table->string('item_name')->nullable();
                $table->date('effective_date');
                $table->string('status')->default('active');
                $table->text('notes')->nullable();

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
        Schema::dropIfExists('employee_financial_actions');
    }
};
