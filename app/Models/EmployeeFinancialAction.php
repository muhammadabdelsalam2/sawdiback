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
        'action_date' => 'date',
        'effective_month' => 'date',
        'amount' => 'decimal:2',
        'percentage' => 'decimal:2',
        'installment_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
        'installments_count' => 'integer',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}