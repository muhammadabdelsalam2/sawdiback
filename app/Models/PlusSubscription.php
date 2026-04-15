<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PlusSubscription extends Model
{
    use HasFactory;

    public const STATUS_ACTIVE = 'active';
    public const STATUS_PAUSED = 'paused';
    public const STATUS_CANCELED = 'canceled';

    protected $fillable = [
        'tenant_id',
        'user_id',
        'status',
        'monthly_price',
        'currency',
        'frequency',
        'delivery_days',
        'starts_at',
        'next_delivery_at',
        'next_billing_at',
        'paused_until',
        'vacation_mode',
        'canceled_at',
        'user_address_id',
        'user_payment_method_id',
        'auto_renew',
        'notes',
        'metadata',
    ];

    protected $casts = [
        'monthly_price' => 'decimal:2',
        'delivery_days' => 'array',
        'starts_at' => 'date',
        'next_delivery_at' => 'datetime',
        'next_billing_at' => 'datetime',
        'paused_until' => 'date',
        'vacation_mode' => 'boolean',
        'canceled_at' => 'datetime',
        'auto_renew' => 'boolean',
        'metadata' => 'array',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(UserAddress::class, 'user_address_id');
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(UserPaymentMethod::class, 'user_payment_method_id');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(
            Category::class,
            'plus_subscription_categories',
            'plus_subscription_id',
            'category_id'
        )->withTimestamps();
    }

    public function skips(): HasMany
    {
        return $this->hasMany(PlusSubscriptionSkip::class, 'plus_subscription_id');
    }

    public function getIsActiveAttribute(): bool
    {
        return $this->status === self::STATUS_ACTIVE && is_null($this->canceled_at);
    }

    public function getIsPausedAttribute(): bool
    {
        return $this->status === self::STATUS_PAUSED && is_null($this->canceled_at);
    }

    public function getIsCanceledAttribute(): bool
    {
        return $this->status === self::STATUS_CANCELED || !is_null($this->canceled_at);
    }
}
