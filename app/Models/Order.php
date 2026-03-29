<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_PREPARING = 'preparing';
    public const STATUS_SHIPPED = 'shipped';
    public const STATUS_OUT_FOR_DELIVERY = 'out_for_delivery';
    public const STATUS_DELIVERED = 'delivered';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_CANCELLED = 'cancelled';

    public const ACTIVE_STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_CONFIRMED,
        self::STATUS_PREPARING,
        self::STATUS_SHIPPED,
        self::STATUS_OUT_FOR_DELIVERY,
    ];

    public const HISTORY_STATUSES = [
        self::STATUS_DELIVERED,
        self::STATUS_REJECTED,
        self::STATUS_CANCELLED,
    ];

    protected $fillable = [
        'tenant_id',
        'order_no',
        'user_id',
        'user_address_id',
        'status',
        'payment_method',
        'payment_status',
        'subtotal',
        'shipping',
        'taxes',
        'vat',
        'discount',
        'total',
        'currency',
        'paid_at',
        'placed_at',
        'estimated_delivery_at',
        'notes',
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
            'paid_at' => 'datetime',
            'placed_at' => 'datetime',
            'estimated_delivery_at' => 'datetime',
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
        return $this->hasMany(OrderItem::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class);
    }

    public function review(): HasOne
    {
        return $this->hasOne(OrderReview::class);
    }
}
