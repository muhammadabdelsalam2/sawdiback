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
    // use ScopedByTenant;

    protected $fillable = [
        'tenant_id',
        'code',
        'name',
        'image',
        'category',
        'category_id',
        'unit',
        'track_expiry',
        'low_stock_threshold',
        'is_active',
        'is_best_selling',
        'notes',
    ];

    protected $casts = [
        'track_expiry' => 'boolean',
        'is_active' => 'boolean',
        'is_best_selling' => 'boolean',
        'low_stock_threshold' => 'decimal:2',
    ];

    protected $append = ['image_url'];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
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

    public function getImageUrlAttribute(): string
    {
        // لو فيه صورة مرفوعة
        if ($this->image) {
            return asset('storage/' . $this->image);
        }

        // ✅ Dynamic image باسم المنتج (API جاهز)
        $name = urlencode($this->name);

        return "https://ui-avatars.com/api/?name={$name}&background=0D8ABC&color=fff&size=400";
    }
}
