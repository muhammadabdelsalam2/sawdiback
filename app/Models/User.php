<?php

namespace App\Models;

use App\Services\PlanService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'tenant_id',
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
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

    /**
     * Resolve current plan features for this user.
     * Priority:
     * 1) subscription.plan (if exists)
     * 2) tenant.plan (if tenant has plan_id)
     */
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

        // $plan->features ممكن تكون array (cast) أو string JSON (legacy)
        $features = $plan->features ?? [];

        if (!is_array($features)) {
            $features = json_decode((string) $features, true) ?: [];
        }

        return $features;
    }

    /**
     * Quick check: is feature enabled?
     */
    public function hasPlanFeature(string $key): bool
    {
        $features = $this->planFeatures();

        $feature = $features[$key] ?? null;

        if (is_array($feature)) {
            return (bool) ($feature['enabled'] ?? false);
        }

        // support boolean-style flags: "feature_key" => true
        return (bool) $feature;
    }

    /**
     * Quick read: feature value
     */
    public function planFeatureValue(string $key, $default = null)
    {
        $features = $this->planFeatures();

        $feature = $features[$key] ?? null;

        if (is_array($feature)) {
            return $feature['value'] ?? $default;
        }

        return $default;
    }
}
