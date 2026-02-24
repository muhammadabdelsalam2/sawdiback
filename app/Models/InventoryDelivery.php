<?php

namespace App\Models;

use App\Models\Concerns\ScopedByTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryDelivery extends Model
{
    use HasFactory;
    use ScopedByTenant;

    protected $fillable = [
        'tenant_id',
        'delivery_number',
        'customer_name',
        'delivered_at',
        'status',
        'notes',
    ];

    protected $casts = [
        'delivered_at' => 'datetime',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InventoryDeliveryItem::class);
    }
}

