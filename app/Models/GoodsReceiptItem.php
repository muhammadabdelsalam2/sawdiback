<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class GoodsReceiptItem extends Model
{
    use HasFactory;

    protected $table = 'goods_receipt_items';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'goods_receipt_id',
        'product_id',
        'quantity',
    ];

    protected $casts = [
        'id' => 'string',
        'quantity' => 'decimal:2',
    ];

    protected static function booted()
    {
        static::creating(function (self $model): void {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    public function goodsReceipt(): BelongsTo
    {
        return $this->belongsTo(GoodsReceipt::class, 'goods_receipt_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(InventoryProduct::class, 'product_id');
    }
}
