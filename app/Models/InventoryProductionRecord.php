<?php

namespace App\Models;

use App\Models\Concerns\ScopedByTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryProductionRecord extends Model
{
    use HasFactory;
    use ScopedByTenant;

    protected $fillable = [
        'tenant_id',
        'inventory_product_id',
        'livestock_animal_id',
        'production_date',
        'quantity',
        'unit_cost',
        'notes',
    ];

    protected $casts = [
        'production_date' => 'date',
        'quantity' => 'decimal:2',
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

    public function animal(): BelongsTo
    {
        return $this->belongsTo(LivestockAnimal::class, 'livestock_animal_id');
    }
}

