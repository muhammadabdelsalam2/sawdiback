<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. توسيع جدول الموظفين (employees)
        Schema::table('employees', function (Blueprint $table) {
            if (!Schema::hasColumn('employees', 'status')) {
                $table->enum('status', ['active', 'on_leave', 'traveling', 'terminated'])->default('active');
            }
            if (!Schema::hasColumn('employees', 'replacement_employee_id')) {
                $table->unsignedBigInteger('replacement_employee_id')->nullable();
            }
            if (!Schema::hasColumn('employees', 'annual_leave_date')) {
                $table->date('annual_leave_date')->nullable();
            }
            if (!Schema::hasColumn('employees', 'education_degree')) {
                $table->string('education_degree')->nullable();
            }
            if (!Schema::hasColumn('employees', 'practical_experience')) {
                $table->text('practical_experience')->nullable();
            }
            if (!Schema::hasColumn('employees', 'innovations_and_achievements')) {
                $table->text('innovations_and_achievements')->nullable();
            }
            if (!Schema::hasColumn('employees', 'work_evasion_notes')) {
                $table->text('work_evasion_notes')->nullable();
            }
        });

        // 2. جدول السلف والاستقطاعات (employee_advances)
        if (!Schema::hasTable('employee_advances')) {
            Schema::create('employee_advances', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('tenant_id')->nullable();
                $table->unsignedBigInteger('employee_id');
                $table->decimal('amount', 12, 2);
                $table->decimal('monthly_deduction', 12, 2)->default(0);
                $table->date('request_date');
                $table->date('start_deduction_date')->nullable();
                $table->decimal('repaid_amount', 12, 2)->default(0);
                $table->enum('status', ['pending', 'active', 'completed', 'rejected'])->default('active');
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->foreign('employee_id')->references('id')->on('employees')->cascadeOnDelete();
            });
        }

        // 3. جدول المكافآت والزيادات والهدايا العينية (employee_adjustments)
        if (!Schema::hasTable('employee_adjustments')) {
            Schema::create('employee_adjustments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('tenant_id')->nullable();
                $table->unsignedBigInteger('employee_id');
                $table->enum('type', ['salary_increase_fixed', 'salary_increase_percentage', 'financial_bonus', 'in_kind_gift']);
                $table->decimal('value', 12, 2)->default(0);
                $table->string('in_kind_description')->nullable();
                $table->date('effective_date');
                $table->text('reason')->nullable();
                $table->timestamps();

                $table->foreign('employee_id')->references('id')->on('employees')->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_adjustments');
        Schema::dropIfExists('employee_advances');
        Schema::table('employees', function (Blueprint $table) {
            $cols = [
                'status', 'replacement_employee_id', 'annual_leave_date', 
                'education_degree', 'practical_experience', 
                'innovations_and_achievements', 'work_evasion_notes'
            ];
            foreach ($cols as $col) {
                if (Schema::hasColumn('employees', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};