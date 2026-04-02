<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class PurchaseRequisitionItem extends Model
{
    use HasFactory;

    protected $table = 'purchase_requisition_items';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'purchase_requisition_id',
        'product_id',
        'quantity',
        'estimated_price',
    ];

    protected $casts = [
        'id' => 'string',
        'quantity' => 'decimal:2',
        'estimated_price' => 'decimal:2',
    ];

    protected static function booted()
    {
        static::creating(function (self $model): void {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    public function requisition(): BelongsTo
    {
        return $this->belongsTo(PurchaseRequisition::class, 'purchase_requisition_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(InventoryProduct::class, 'product_id');
    }
}
