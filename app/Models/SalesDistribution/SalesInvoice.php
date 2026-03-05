<?php

namespace App\Models\SalesDistribution;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesInvoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sales_invoices';

    protected $fillable = [
        'tenant_id',
        'invoice_no',
        'sales_order_id',
        'customer_id',
        'invoice_date',
        'due_date',
        'subtotal',
        'tax',
        'total',
        'status',
        'notes',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(SalesOrder::class, 'sales_order_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(SalesCustomer::class, 'customer_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(SalesPayment::class, 'invoice_id');
    }
}
