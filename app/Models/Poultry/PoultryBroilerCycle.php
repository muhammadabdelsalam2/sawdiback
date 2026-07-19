<?php

namespace App\Models\Poultry;

use App\Models\FarmPen;
use App\Models\Concerns\ScopedByTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PoultryBroilerCycle extends Model
{
    use HasFactory;
    use ScopedByTenant;
    use SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'pen_id',
        'cycle_number',
        'chick_count',
        'started_at',
        'status',
        'notes',
    ];

    protected $casts = [
        'started_at' => 'date',
        'chick_count' => 'integer',
    ];

    protected $appends = [
        'age_days',
        'total_mortality',
        'mortality_rate',
        'total_sales',
        'total_costs',
        'net_profit',
    ];

    public function mortalities(): HasMany
    {
        return $this->hasMany(PoultryBroilerMortality::class, 'broiler_cycle_id');
    }

    public function pen(): BelongsTo
    {
        return $this->belongsTo(FarmPen::class, 'pen_id');
    }

    public function sales(): HasMany
    {
        return $this->hasMany(PoultryBroilerSale::class, 'broiler_cycle_id');
    }

    public function costs(): HasMany
    {
        return $this->hasMany(PoultryBroilerCost::class, 'broiler_cycle_id');
    }

    public function getAgeDaysAttribute(): int
    {
        return max(0, (int) $this->started_at?->diffInDays(now()));
    }

    public function getTotalMortalityAttribute(): int
    {
        return (int) ($this->relationLoaded('mortalities')
            ? $this->mortalities->sum('quantity')
            : $this->mortalities()->sum('quantity'));
    }

    public function getMortalityRateAttribute(): string
    {
        if ((int) $this->chick_count === 0) {
            return '0.00';
        }

        return number_format(($this->total_mortality / (int) $this->chick_count) * 100, 2, '.', '');
    }

    public function getTotalSalesAttribute(): string
    {
        $value = $this->relationLoaded('sales')
            ? $this->sales->sum('total_amount')
            : $this->sales()->sum('total_amount');

        return number_format((float) $value, 2, '.', '');
    }

    public function getTotalCostsAttribute(): string
    {
        $value = $this->relationLoaded('costs')
            ? $this->costs->sum('amount')
            : $this->costs()->sum('amount');

        return number_format((float) $value, 2, '.', '');
    }

    public function getNetProfitAttribute(): string
    {
        return number_format((float) $this->total_sales - (float) $this->total_costs, 2, '.', '');
    }
}
