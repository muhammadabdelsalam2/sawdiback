<?php

namespace App\Models;

use App\Models\Concerns\ScopedByTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryBatch extends Model
{
    use HasFactory;
    use ScopedByTenant;

    protected $fillable = [
        'tenant_id',
        'inventory_product_id',
        'batch_number',
        'production_date',
        'expiry_date',
        'received_at',
        'quantity_initial',
        'quantity_available',
        'unit_cost',
        'source_type',
        'source_id',
        'notes',
    ];

    protected $casts = [
        'production_date' => 'date',
        'expiry_date' => 'date',
        'received_at' => 'date',
        'quantity_initial' => 'decimal:2',
        'quantity_available' => 'decimal:2',
        'unit_cost' => 'decimal:2',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(InventoryProduct::class, 'inventory_product_id');
    }

    public function movements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class);
    }

    public function deliveryItems(): HasMany
    {
        return $this->hasMany(InventoryDeliveryItem::class);
    }
}

