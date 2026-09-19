<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'farm_id',
        'department_id',
        'job_title_id',
        'worker_number',
        'profession',
        'employment_status',
        'operational_department',
        'full_name',
        'email',
        'phone',
        'national_id',
        'hire_date',
        'passport_expiry_date',
        'iqama_expiry_date',
        'salary',
        'is_active',
        'replacement_employee_id',
        'next_annual_leave_date',
        'education',
        'work_experience',
        'self_development',
        'achievements_creativity',
        'infractions_absence_notes',
    ];

    protected $casts = [
        'hire_date'              => 'date',
        'passport_expiry_date'   => 'date',
        'iqama_expiry_date'      => 'date',
        'salary'                 => 'decimal:2',
        'is_active'              => 'boolean',
        'next_annual_leave_date' => 'date',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function jobTitle(): BelongsTo
    {
        return $this->belongsTo(JobTitle::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function leaves(): HasMany
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(EmployeeAttachment::class);
    }

    public function financialActions(): HasMany
    {
        return $this->hasMany(EmployeeFinancialAction::class);
    }

    public function replacementEmployee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'replacement_employee_id');
    }

    public function substituteFor(): HasMany
    {
        return $this->hasMany(Employee::class, 'replacement_employee_id');
    }

    public function getActiveIncreasesTotalAttribute(): float
    {
        $fixed = (float) $this->financialActions()
            ->where('type', 'salary_increase_fixed')
            ->where('status', 'active')
            ->sum('amount');

        $percentSum = (float) $this->financialActions()
            ->where('type', 'salary_increase_percent')
            ->where('status', 'active')
            ->sum('percentage');

        $baseSalary = (float) ($this->salary ?? 0);
        $fromPercent = ($baseSalary * $percentSum) / 100;

        return $fixed + $fromPercent;
    }

    public function getCurrentSalaryAttribute(): float
    {
        return (float) ($this->salary ?? 0) + $this->active_increases_total;
    }

    public function getActiveLoansBalanceAttribute(): float
    {
        return (float) $this->financialActions()
            ->where('type', 'advance_payment')
            ->where('status', 'active')
            ->sum('remaining_amount');
    }

    /**
     * استخراج بيانات شهادة الراتب المعتمدة
     */
 public function generateSalaryCertificate(): array
    {
        $baseSalary = (float) ($this->salary ?? 0);
        $increases = $this->active_increases_total;
        $totalSalary = $baseSalary + $increases;

        // الاستقطاع الشهري للسلفيات والأقساط النشطة
        $monthlyAdvanceDeduction = (float) $this->financialActions()
            ->whereIn('type', [
                EmployeeFinancialAction::TYPE_ADVANCE_PAYMENT,
                EmployeeFinancialAction::TYPE_MONTHLY_DEDUCTION
            ])
            ->where('status', EmployeeFinancialAction::STATUS_ACTIVE)
            ->sum('installment_amount');

        return [
            'worker_number'            => $this->worker_number,
            'full_name'                => $this->full_name,
            'national_id'              => $this->national_id,
            'profession'               => $this->profession ?? $this->jobTitle?->name,
            'department'               => $this->department?->name ?? $this->operational_department,
            'hire_date'                => $this->hire_date?->toDateString(),
            'base_salary'              => $baseSalary,
            'salary_increases'         => $increases,
            'total_salary'             => $totalSalary,
            'monthly_loan_deduction'   => $monthlyAdvanceDeduction,
            'net_salary'               => max(0, $totalSalary - $monthlyAdvanceDeduction),
            'issue_date'               => now()->toDateString(),
        ];
    }
}