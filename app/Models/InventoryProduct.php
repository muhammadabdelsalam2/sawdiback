<?php

namespace App\Models;

use App\Models\Concerns\ScopedByTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryProduct extends Model
{
    use HasFactory;
    use ScopedByTenant;

    protected $fillable = [
        'tenant_id',
        'code',
        'name',
        'category',
        'unit',
        'track_expiry',
        'low_stock_threshold',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'track_expiry' => 'boolean',
        'is_active' => 'boolean',
        'low_stock_threshold' => 'decimal:2',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function batches(): HasMany
    {
        return $this->hasMany(InventoryBatch::class);
    }

    public function movements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class);
    }

    public function productionRecords(): HasMany
    {
        return $this->hasMany(InventoryProductionRecord::class);
    }

    public function deliveryItems(): HasMany
    {
        return $this->hasMany(InventoryDeliveryItem::class);
    }
}

