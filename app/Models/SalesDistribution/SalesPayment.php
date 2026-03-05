<?php

namespace App\Models\SalesDistribution;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesPayment extends Model
{
    use HasFactory;

    protected $table = 'sales_payments';

    protected $fillable = [
        'invoice_id',
        'amount',
        'paid_at',
        'method',
        'reference',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(SalesInvoice::class, 'invoice_id');
    }
}
