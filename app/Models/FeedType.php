<?php

namespace App\Models;

use App\Models\Concerns\ScopedByTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FeedType extends Model
{
    use HasFactory;
    use ScopedByTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'category',
        'unit',
        'cost_per_unit',
        'low_stock_threshold',
        'notes',
    ];

    protected $casts = [
        'cost_per_unit' => 'decimal:2',
        'low_stock_threshold' => 'decimal:2',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function feedingLogs(): HasMany
    {
        return $this->hasMany(AnimalFeedingLog::class, 'feed_type_id');
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(FeedStockMovement::class);
    }

    public function feedConsumptions(): HasMany
    {
        return $this->hasMany(FeedConsumption::class);
    }

    public function cropFeedAllocations(): HasMany
    {
        return $this->hasMany(CropFeedAllocation::class);
    }
}
