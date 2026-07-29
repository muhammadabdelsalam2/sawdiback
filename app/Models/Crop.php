<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use App\Models\Concerns\ScopedByTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Crop extends Model
{
    use HasFactory;
    use HasTranslations;
    use ScopedByTenant;

    protected $fillable = [
        'tenant_id',
        'farm_id',
        'greenhouse_type',
        'greenhouse_number',
        'greenhouse_location',
        'irrigation_type',
        'name',
        'name_translations',
        'land_area',
        'planting_date',
        'expected_harvest_date',
        'yield_tons',
        'wasted_tons',
        'available_for_feed_tons',
        'sale_price_per_ton',
        'water_cost',
        'labor_cost',
        'notes',
    ];

    protected $casts = [
        'land_area' => 'decimal:2',
        'planting_date' => 'date',
        'expected_harvest_date' => 'date',
        'yield_tons' => 'decimal:2',
        'wasted_tons' => 'decimal:2',
        'available_for_feed_tons' => 'decimal:2',
        'sale_price_per_ton' => 'decimal:2',
        'water_cost' => 'decimal:2',
        'labor_cost' => 'decimal:2',
        'name_translations' => 'array',
    ];

    public function getLocalizedNameAttribute(): ?string
    {
        return $this->getLocalized('name_translations', 'name');
    }

    protected $appends = [
        'total_cost',
        'cost_per_ton',
        'profit_or_loss',
        'loss_rate',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
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

    public function materialUsages(): HasMany
    {
        return $this->hasMany(CropMaterialUsage::class);
    }

    public function getTotalCostAttribute(): string
    {
        $costItems = $this->relationLoaded('costItems')
            ? $this->costItems->sum('amount')
            : (float) $this->costItems()->sum('amount');
        $materials = $this->relationLoaded('materialUsages')
            ? $this->materialUsages->sum('amount')
            : (float) $this->materialUsages()->sum('amount');
        $value = $costItems + $materials + (float) $this->water_cost + (float) $this->labor_cost;

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

    public function getLossRateAttribute(): string
    {
        $yield = (float) ($this->yield_tons ?? 0);
        $wasted = (float) ($this->wasted_tons ?? 0);
        $total = $yield + $wasted;

        if ($total <= 0) {
            return '0.00';
        }

        return number_format(($wasted / $total) * 100, 2, '.', '');
    }
}
