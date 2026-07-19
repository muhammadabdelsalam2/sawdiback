<?php

namespace App\Models\Poultry;

use App\Models\FarmPen;
use App\Models\Concerns\ScopedByTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PoultryLayerFlock extends Model
{
    use HasFactory;
    use ScopedByTenant;
    use SoftDeletes;

    protected $fillable = ['tenant_id', 'pen_id', 'flock_number', 'chicken_count', 'purchase_cost', 'started_at', 'status', 'notes'];

    protected $casts = [
        'started_at' => 'date',
        'chicken_count' => 'integer',
        'purchase_cost' => 'decimal:2',
    ];

    protected $appends = ['total_egg_revenue', 'total_feed_cost', 'total_mortality', 'net_profit'];

    public function eggProductionLogs(): HasMany
    {
        return $this->hasMany(PoultryLayerEggProductionLog::class, 'layer_flock_id');
    }

    public function pen(): BelongsTo
    {
        return $this->belongsTo(FarmPen::class, 'pen_id');
    }

    public function mortalities(): HasMany
    {
        return $this->hasMany(PoultryLayerMortality::class, 'layer_flock_id');
    }

    public function getTotalEggRevenueAttribute(): string
    {
        $logs = $this->relationLoaded('eggProductionLogs') ? $this->eggProductionLogs : $this->eggProductionLogs()->get();
        $value = $logs->sum(fn (PoultryLayerEggProductionLog $log) => (float) $log->eggs_count * (float) $log->sale_price);

        return number_format($value, 2, '.', '');
    }

    public function getTotalFeedCostAttribute(): string
    {
        $value = $this->relationLoaded('eggProductionLogs')
            ? $this->eggProductionLogs->sum('daily_feed_cost')
            : $this->eggProductionLogs()->sum('daily_feed_cost');

        return number_format((float) $value, 2, '.', '');
    }

    public function getTotalMortalityAttribute(): int
    {
        return (int) ($this->relationLoaded('mortalities')
            ? $this->mortalities->sum('quantity')
            : $this->mortalities()->sum('quantity'));
    }

    public function getNetProfitAttribute(): string
    {
        return number_format((float) $this->total_egg_revenue - (float) $this->purchase_cost - (float) $this->total_feed_cost, 2, '.', '');
    }
}
