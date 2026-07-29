<?php

namespace App\Models;

use App\Models\Concerns\ScopedByTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CropSeedlingStock extends Model
{
    use HasFactory;
    use ScopedByTenant;

    protected $fillable = ['tenant_id', 'farm_id', 'item_type', 'name', 'quantity', 'unit', 'low_stock_threshold', 'notes'];

    protected $casts = [
        'quantity' => 'decimal:2',
        'low_stock_threshold' => 'decimal:2',
    ];

    public function getIsLowStockAttribute(): bool
    {
        return (float) $this->quantity <= (float) $this->low_stock_threshold;
    }

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }
}
