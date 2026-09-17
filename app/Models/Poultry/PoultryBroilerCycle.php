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
        'chick_purchase_cost', // سعر التكلفة والشراء
        'electricity_cost',    // الكهرباء
        'water_cost',          // المياه
        'transport_cost',      // النقل والتوصيل
        'fuel_cost',           // الوقود
        'started_at',
        'status',
        'notes',
    ];

    protected $casts = [
        'started_at'          => 'date',
        'chick_count'         => 'integer',
        'chick_purchase_cost' => 'decimal:2',
        'electricity_cost'    => 'decimal:2',
        'water_cost'          => 'decimal:2',
        'transport_cost'      => 'decimal:2',
        'fuel_cost'           => 'decimal:2',
    ];

    protected $appends = [
        'age_days',
        'total_mortality',
        'mortality_rate',
        'total_sales',
        'total_operational_costs',
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

    // إجمالي التكاليف المباشرة المحددة في دورة اللاحم
    public function getTotalOperationalCostsAttribute(): float
    {
        return (float) $this->chick_purchase_cost
             + (float) $this->electricity_cost
             + (float) $this->water_cost
             + (float) $this->transport_cost
             + (float) $this->fuel_cost;
    }

    // إجمالي التكاليف الكلية (المباشرة + الإضافية من جدول التكاليف)
    public function getTotalCostsAttribute(): string
    {
        $additionalCosts = $this->relationLoaded('costs')
            ? $this->costs->sum('amount')
            : $this->costs()->sum('amount');

        $total = $this->total_operational_costs + (float) $additionalCosts;

        return number_format($total, 2, '.', '');
    }

    public function getNetProfitAttribute(): string
    {
        return number_format((float) $this->total_sales - (float) $this->total_costs, 2, '.', '');
    }
}