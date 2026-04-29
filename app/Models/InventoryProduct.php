<?php

namespace App\Models;

use App\Models\Concerns\ScopedByTenant;
use App\Scopes\ActiveScope;
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
        'farmer_id',
        'category_id',
        'unit',
        'tax',
        'track_expiry',
        'low_stock_threshold',
        'is_active',
        'is_best_selling',
        'notes',
        'price',
        'last_price',
    ];

    protected $casts = [
        'track_expiry' => 'boolean',
        'is_active' => 'boolean',
        'farmer_id' => 'string',
        'is_best_selling' => 'boolean',
        'low_stock_threshold' => 'decimal:2',
        'tax' => 'decimal:2',
        'price' => 'decimal:2',
        'last_price' => 'decimal:2',
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

    public function favoritedBy()
    {
        return $this->hasMany(FavoriteProduct::class);
    }

    public function favoriteProducts()
    {
        return $this->hasMany(FavoriteProduct::class, 'inventory_product_id');
    }

    // Feat: Products Make it By Farmer
    public function farmer(): BelongsTo
    {
        return $this->belongsTo(Farmer::class);
    }

    // Feat: Products Make it By Farmer
    public function byFarmer($query, $farmerId)
    {
        return $query->where('farmer_id', $farmerId);
    }

    // Scope to filter By Farmer
    // Boot 
    protected static function booted()
    {
        static::addGlobalScope(new ActiveScope);

    }

}
