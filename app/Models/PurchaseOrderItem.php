<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class PurchaseOrderItem extends Model
{
    use HasFactory;

    protected $table = 'purchase_order_items';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'purchase_order_id',
        'product_id',
        'quantity',
        'unit_price',
        'received_quantity',
        'total',
    ];

    protected $casts = [
        'id' => 'string',
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'received_quantity' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    protected static function booted()
    {
        static::creating(function (self $model): void {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(InventoryProduct::class, 'product_id');
    }
}
