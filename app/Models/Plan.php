<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Plan extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'price',
        'currency_id',
        'billing_cycle',
        'is_active',
        'description',
        'sort_order',
        'features', // ✅ IMPORTANT
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'features' => 'array',
        'is_active' => 'boolean',
    ];

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Feature enabled?
     * ✅ Supports NEW structure: features[key][enabled]
     * ✅ Backward compatible with OLD structure: features[key] = true/false
     */
    public function hasFeature(string $key): bool
    {
        $features = $this->features ?? [];

        // New structure
        if (isset($features[$key]) && is_array($features[$key])) {
            return (bool) ($features[$key]['enabled'] ?? false);
        }

        // Old structure fallback
        return (bool) ($features[$key] ?? false);
    }

    /**
     * Feature value
     * ✅ Supports NEW structure: features[key][value]
     * ✅ Backward compatible: if old boolean structure, return default
     */
    public function getFeatureValue(string $key, $default = null)
    {
        $features = $this->features ?? [];

        if (isset($features[$key]) && is_array($features[$key])) {
            return $features[$key]['value'] ?? $default;
        }

        return $default;
    }

    public function getAllFeatures(): array
    {
        return $this->features ?? [];
    }

    protected static function booted(): void
    {
        // Always get only active plans by default
        static::addGlobalScope('active', function (Builder $builder) {
            $builder->where('is_active', 1);
        });
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', 1);
    }

    /**
     * Use this when you need ALL plans (including inactive) in admin screens.
     * Example: Plan::withoutGlobalScope('active')->paginate()
     */
    public function scopeWithInactive(Builder $query): Builder
    {
        return $query->withoutGlobalScope('active');
    }
}
