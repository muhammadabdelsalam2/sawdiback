<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'user_id',
        'user_address_id',
        'coupon_code',
        'subtotal',
        'shipping',
        'taxes',
        'vat',
        'discount',
        'total',
        'weekly_delivery',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'shipping' => 'decimal:2',
            'taxes' => 'decimal:2',
            'vat' => 'decimal:2',
            'discount' => 'decimal:2',
            'total' => 'decimal:2',
            'weekly_delivery' => 'boolean',
            'metadata' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(UserAddress::class, 'user_address_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }
}
