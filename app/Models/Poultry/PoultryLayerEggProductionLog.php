<?php

namespace App\Models\Poultry;

use App\Models\Concerns\ScopedByTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PoultryLayerEggProductionLog extends Model
{
    use HasFactory;
    use ScopedByTenant;

    protected $fillable = ['tenant_id', 'layer_flock_id', 'production_date', 'eggs_count', 'sale_price', 'daily_feed_cost', 'damaged_count', 'notes'];

    protected $casts = [
        'production_date' => 'date',
        'eggs_count' => 'integer',
        'sale_price' => 'decimal:2',
        'daily_feed_cost' => 'decimal:2',
        'damaged_count' => 'integer',
    ];

    public function flock(): BelongsTo
    {
        return $this->belongsTo(PoultryLayerFlock::class, 'layer_flock_id');
    }
}
