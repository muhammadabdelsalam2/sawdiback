<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, HasApiTokens, Notifiable, HasRoles;

    protected string $guard_name = 'web';

    protected $fillable = [
        'tenant_id',
        'name',
        'phone',
        'email',
        'password',
        'avatar',
        'preferred_language',
        'appearance_mode',
        'is_completed',
        'email_verified_at',
        'phone_verified_at',
        'password_reset_token',
        'password_reset_at',
        'google_id',
        'facebook_id',
        'instagram_id',
        'social_avatar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'password' => 'hashed',
            'password_reset_at' => 'datetime',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class, 'customer_id');
    }

    public function subscription(): HasOne
    {
        return $this->hasOne(Subscription::class, 'customer_id');
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(UserAddress::class);
    }

    public function paymentMethods(): HasMany
    {
        return $this->hasMany(UserPaymentMethod::class);
    }

    public function notificationSetting(): HasOne
    {
        return $this->hasOne(UserNotificationSetting::class);
    }

    public function wallet(): HasOne
    {
        return $this->hasOne(Wallet::class);
    }

    public function cart(): HasOne
    {
        return $this->hasOne(Cart::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function orderReviews(): HasMany
    {
        return $this->hasMany(OrderReview::class);
    }

    public function otps(): HasMany
    {
        return $this->hasMany(Otp::class);
    }

    public function planFeatures(): array
    {
        $this->loadMissing([
            'subscription.plan',
            'tenant.plan',
        ]);

        $plan = $this->subscription?->plan ?? $this->tenant?->plan;

        if (!$plan) {
            return [];
        }

        $features = $plan->features ?? [];

        if (!is_array($features)) {
            $features = json_decode((string) $features, true) ?: [];
        }

        return $features;
    }

    public function hasPlanFeature(string $key): bool
    {
        $features = $this->planFeatures();
        $feature = $features[$key] ?? null;

        if (is_array($feature)) {
            return (bool) ($feature['enabled'] ?? false);
        }

        return (bool) $feature;
    }

    public function planFeatureValue(string $key, $default = null)
    {
        $features = $this->planFeatures();
        $feature = $features[$key] ?? null;

        if (is_array($feature)) {
            return $feature['value'] ?? $default;
        }

        return $default;
    }

    // Feature: Favorite Products
    public function favorites()
    {
        return $this->hasMany(FavoriteProduct::class);
    }

    public function favoriteProducts()
    {
        return $this->belongsToMany(InventoryProduct::class, 'favorite_products', 'user_id', 'inventory_product_id');
    }
}
