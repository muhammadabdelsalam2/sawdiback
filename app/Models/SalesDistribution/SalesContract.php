<?php

namespace App\Models\SalesDistribution;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesContract extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sales_contracts';

    protected $fillable = [
        'tenant_id',
        'customer_id',
        'contract_code',
        'start_date',
        'end_date',
        'payment_terms',
        'credit_limit',
        'notes',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'credit_limit' => 'decimal:2',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(SalesCustomer::class, 'customer_id');
    }
}
