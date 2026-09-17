<?php

namespace App\Models\Poultry;

use App\Models\Concerns\ScopedByTenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class PoultryBroilerCost extends Model
{
    use HasFactory;
    use ScopedByTenant;

    public const TYPE_INITIAL_COST = 'initial_cost'; // سعر التكلفة
    public const TYPE_ELECTRICITY  = 'electricity';  // الكهرباء
    public const TYPE_WATER        = 'water';        // المياه
    public const TYPE_TRANSPORT    = 'transport';    // سعر النقل والتوصيل
    public const TYPE_FUEL         = 'fuel';         // الوقود
    public const TYPE_FEED         = 'feed';         // الأعلاف
    public const TYPE_OTHER        = 'other';        // أخرى

  protected $fillable = [
        'tenant_id',
        'broiler_cycle_id',
        'cost_type',
        'quantity_kg',
        'amount',
        'cost_date',
        'notes',
    ];

    protected $casts = [
        'amount'      => 'decimal:2',
        'quantity_kg' => 'decimal:2',
        'cost_date'   => 'date',
    ];
    public static function getCostTypes(): array
    {
        $isArabic = str_starts_with(app()->getLocale(), 'ar');

        return [
            self::TYPE_INITIAL_COST => $isArabic ? 'سعر التكلفة' : 'Cost Price',
            self::TYPE_ELECTRICITY  => $isArabic ? 'الكهرباء' : 'Electricity',
            self::TYPE_WATER        => $isArabic ? 'المياه' : 'Water',
            self::TYPE_TRANSPORT    => $isArabic ? 'سعر النقل والتوصيل' : 'Transport & Delivery',
            self::TYPE_FUEL         => $isArabic ? 'الوقود' : 'Fuel',
            self::TYPE_FEED         => $isArabic ? 'الأعلاف' : 'Feed',
            self::TYPE_OTHER        => $isArabic ? 'أخرى' : 'Other',
        ];
    }

    public function cycle(): BelongsTo
    {
        return $this->belongsTo(PoultryBroilerCycle::class, 'broiler_cycle_id');
    }

    // فلترة تكاليف الأعلاف فقط
    public function scopeFeedOnly(Builder $query): Builder
    {
        return $query->where('cost_type', self::TYPE_FEED);
    }

    // فلترة حسب سنة وشهر معين
    public function scopeForMonth(Builder $query, int $year, int $month): Builder
    {
        return $query->whereYear('cost_date', $year)
                     ->whereMonth('cost_date', $month);
    }

    // تقرير تجميعي لتكاليف الأعلاف مجمعة بالشهور
    public static function getMonthlyFeedCostsSummary(?int $cycleId = null, ?int $year = null): array
    {
        $year = $year ?? (int) date('Y');

        $query = self::query()
            ->feedOnly()
            ->whereYear('cost_date', $year)
            ->selectRaw("DATE_FORMAT(cost_date, '%Y-%m') as cost_month, SUM(amount) as total_amount, COUNT(id) as entries_count")
            ->groupBy('cost_month')
            ->orderBy('cost_month', 'asc');

        if ($cycleId) {
            $query->where('broiler_cycle_id', $cycleId);
        }

        return $query->get()->toArray();
    }
}