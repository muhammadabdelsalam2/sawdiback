<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

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
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'features' => 'array', // 
        'is_active' => 'boolean',
    ];

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }



    public function hasFeature(string $key): bool
    {
        return isset($this->features[$key]) && $this->features[$key];
    }
    // Get feature value (number or string)
    public function getFeatureValue(string $key, $default = null)
    {
        return $this->features[$key] ?? $default;
    }
    public function getAllFeatures(): array
    {
        return $this->features ?? [];
    }
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    protected static function booted()
    {
        // Always get only active plans by default
        static::addGlobalScope('active', function (Builder $builder) {
            $builder->where('is_active', 1);
        });

        // Optional: Automatically filter by current app currency
        // static::addGlobalScope('currentCurrency', function (Builder $builder) {
        //     if (session()->has('currency_id')) {
        //         $builder->where('currency_id', session('currency_id'));
        //     }
        // });
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }
}
