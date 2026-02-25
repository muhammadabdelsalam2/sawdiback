<?php

namespace App\Models\SalesDistribution;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sales_orders';

    protected $fillable = [
        'tenant_id',
        'order_no',
        'customer_id',
        'contract_id',
        'order_date',
        'status',
        'total',
        'notes',
    ];

    protected $casts = [
        'order_date' => 'date',
        'total' => 'decimal:2',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(SalesCustomer::class, 'customer_id');
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(SalesContract::class, 'contract_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(SalesOrderItem::class, 'sales_order_id');
    }

    public function shipments(): HasMany
    {
        return $this->hasMany(SalesShipment::class, 'sales_order_id');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(SalesInvoice::class, 'sales_order_id');
    }
}
