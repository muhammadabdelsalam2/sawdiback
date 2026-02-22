<?php

namespace App\Models;

use App\Models\Concerns\ScopedByTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Crop extends Model
{
    use HasFactory;
    use ScopedByTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'land_area',
        'planting_date',
        'yield_tons',
        'available_for_feed_tons',
        'sale_price_per_ton',
        'notes',
    ];

    protected $casts = [
        'land_area' => 'decimal:2',
        'planting_date' => 'date',
        'yield_tons' => 'decimal:2',
        'available_for_feed_tons' => 'decimal:2',
        'sale_price_per_ton' => 'decimal:2',
    ];

    protected $appends = [
        'total_cost',
        'cost_per_ton',
        'profit_or_loss',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function growthStages(): HasMany
    {
        return $this->hasMany(CropGrowthStage::class);
    }

    public function costItems(): HasMany
    {
        return $this->hasMany(CropCostItem::class);
    }

    public function feedAllocations(): HasMany
    {
        return $this->hasMany(CropFeedAllocation::class);
    }

    public function getTotalCostAttribute(): string
    {
        $value = $this->relationLoaded('costItems')
            ? $this->costItems->sum('amount')
            : (float) $this->costItems()->sum('amount');

        return number_format((float) $value, 2, '.', '');
    }

    public function getCostPerTonAttribute(): ?string
    {
        $yield = (float) ($this->yield_tons ?? 0);
        if ($yield <= 0) {
            return null;
        }

        return number_format(((float) $this->total_cost) / $yield, 2, '.', '');
    }

    public function getProfitOrLossAttribute(): ?string
    {
        $yield = (float) ($this->yield_tons ?? 0);
        $price = (float) ($this->sale_price_per_ton ?? 0);
        if ($yield <= 0 || $price <= 0) {
            return null;
        }

        $revenue = $yield * $price;
        $profit = $revenue - (float) $this->total_cost;

        return number_format($profit, 2, '.', '');
    }
}
