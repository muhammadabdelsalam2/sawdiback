<?php

namespace App\Models\SalesDistribution;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesOrderItem extends Model
{
    use HasFactory;

    protected $table = 'sales_order_items';

    protected $fillable = [
        'sales_order_id',
        'product_id',
        'qty',
        'unit_price',
        'discount',
        'line_total',
    ];

    protected $casts = [
        'qty' => 'decimal:3',
        'unit_price' => 'decimal:2',
        'discount' => 'decimal:2',
        'line_total' => 'decimal:2',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(SalesOrder::class, 'sales_order_id');
    }
}
