<?php

namespace App\Models;

use App\Models\Concerns\ScopedByTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeFinancialAction extends Model
{
    use HasFactory, ScopedByTenant;

    protected $table = 'employee_financial_actions';

    // أنواع الحركات المالية المطلوبة
    public const TYPE_ADVANCE_PAYMENT           = 'advance_payment';          // سلفة
    public const TYPE_MONTHLY_DEDUCTION         = 'monthly_deduction';        // استقطاع شهري
    public const TYPE_SALARY_INCREASE_FIXED     = 'salary_increase_fixed';    // زيادة راتب (مبلغ ثابت)
    public const TYPE_SALARY_INCREASE_PERCENT   = 'salary_increase_percent';  // زيادة نسبة على الراتب
    public const TYPE_FINANCIAL_BONUS           = 'financial_bonus';          // مكافأة مالية
    public const TYPE_IN_KIND_GIFT              = 'in_kind_gift';             // هدية عينية

    // حالات الحركة
    public const STATUS_ACTIVE    = 'active';
    public const STATUS_PENDING   = 'pending';
    public const STATUS_PAID      = 'paid';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'tenant_id',
        'employee_id',
        'type',
        'amount',
        'percentage',
        'gift_description',
        'action_date',
        'effective_month',
        'installments_count',
        'installment_amount',
        'remaining_amount',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'action_date'        => 'date',
        'effective_month'    => 'date',
        'amount'             => 'decimal:2',
        'percentage'         => 'decimal:2',
        'installment_amount' => 'decimal:2',
        'remaining_amount'   => 'decimal:2',
        'installments_count' => 'integer',
    ];

    public static function getTypes(): array
    {
        $isArabic = str_starts_with(app()->getLocale(), 'ar');

        return [
            self::TYPE_ADVANCE_PAYMENT         => $isArabic ? 'سلفة مالية' : 'Advance Payment',
            self::TYPE_MONTHLY_DEDUCTION       => $isArabic ? 'استقطاع شهري' : 'Monthly Deduction',
            self::TYPE_SALARY_INCREASE_FIXED   => $isArabic ? 'زيادة راتب (مبلغ مقطوع)' : 'Salary Increase (Fixed)',
            self::TYPE_SALARY_INCREASE_PERCENT => $isArabic ? 'زيادة راتب (نسبة مئوية)' : 'Salary Increase (Percentage)',
            self::TYPE_FINANCIAL_BONUS         => $isArabic ? 'مكافأة مالية' : 'Financial Bonus',
            self::TYPE_IN_KIND_GIFT            => $isArabic ? 'هدية عينية' : 'In-Kind Gift',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}