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
        Schema::table('employees', function (Blueprint $table) {
            if (!Schema::hasColumn('employees', 'replacement_employee_id')) {
                $table->unsignedBigInteger('replacement_employee_id')->nullable();
                $table->foreign('replacement_employee_id')->references('id')->on('employees')->nullOnDelete();
            }

            if (!Schema::hasColumn('employees', 'next_annual_leave_date')) {
                $table->date('next_annual_leave_date')->nullable();
            }

            if (!Schema::hasColumn('employees', 'education')) {
                $table->text('education')->nullable();
            }

            if (!Schema::hasColumn('employees', 'work_experience')) {
                $table->text('work_experience')->nullable();
            }

            if (!Schema::hasColumn('employees', 'self_development')) {
                $table->text('self_development')->nullable();
            }

            if (!Schema::hasColumn('employees', 'achievements_creativity')) {
                $table->text('achievements_creativity')->nullable();
            }

            if (!Schema::hasColumn('employees', 'infractions_absence_notes')) {
                $table->text('infractions_absence_notes')->nullable();
            }
        });

        if (!Schema::hasTable('employee_financial_actions')) {
            Schema::create('employee_financial_actions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('tenant_id');
                $table->unsignedBigInteger('employee_id');
                
                $table->string('type'); // advance_payment, monthly_deduction, salary_increase_fixed, salary_increase_percent, financial_bonus, in_kind_gift
                $table->decimal('amount', 12, 2)->default(0);
                $table->decimal('percentage', 5, 2)->nullable();
                $table->string('gift_description')->nullable();
                
                $table->date('action_date');
                $table->date('effective_month')->nullable();
                $table->integer('installments_count')->default(1);
                $table->decimal('installment_amount', 12, 2)->nullable();
                $table->decimal('remaining_amount', 12, 2)->default(0);
                
                $table->string('status')->default('active');
                $table->text('notes')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                
                $table->timestamps();

                $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
                $table->foreign('employee_id')->references('id')->on('employees')->cascadeOnDelete();
                $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_financial_actions');

        Schema::table('employees', function (Blueprint $table) {
            if (Schema::hasColumn('employees', 'replacement_employee_id')) {
                $table->dropForeign(['replacement_employee_id']);
                $table->dropColumn('replacement_employee_id');
            }

            $columnsToDrop = [
                'next_annual_leave_date',
                'education',
                'work_experience',
                'self_development',
                'achievements_creativity',
                'infractions_absence_notes',
            ];

            foreach ($columnsToDrop as $col) {
                if (Schema::hasColumn('employees', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};