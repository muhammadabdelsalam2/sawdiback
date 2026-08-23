<?php

namespace App\Models;

use App\Models\Concerns\ScopedByTenant;
use App\Services\Livestock\LivestockPenProfitService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class FarmPen extends Model
{
    use HasFactory;
    use ScopedByTenant;
    use SoftDeletes;

    protected $fillable = ['tenant_id', 'farm_id', 'pen_number', 'type', 'capacity', 'notes'];

    protected $casts = ['capacity' => 'integer'];

    protected $appends = ['animal_count', 'male_count', 'female_count', 'mortality_rate', 'net_profit'];

    public function scopeForSelect(Builder $query): Builder
    {
        return $query->with('farm')->orderBy('pen_number');
    }

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function animals(): HasMany
    {
        return $this->hasMany(LivestockAnimal::class, 'pen_id');
    }

    public function financialEntries(): HasMany
    {
        return $this->hasMany(LivestockPenFinancialEntry::class, 'pen_id');
    }

    public function getAnimalCountAttribute(): int
    {
        return (int) ($this->relationLoaded('animals') ? $this->animals->count() : $this->animals()->count());
    }

    public function getMaleCountAttribute(): int
    {
        return (int) ($this->relationLoaded('animals')
            ? $this->animals->where('gender', 'male')->count()
            : $this->animals()->where('gender', 'male')->count());
    }

    public function getFemaleCountAttribute(): int
    {
        return (int) ($this->relationLoaded('animals')
            ? $this->animals->where('gender', 'female')->count()
            : $this->animals()->where('gender', 'female')->count());
    }

    public function getMortalityRateAttribute(): string
    {
        return app(LivestockPenProfitService::class)->mortalityRate($this);
    }

    public function getNetProfitAttribute(): string
    {
        return app(LivestockPenProfitService::class)->netProfit($this);
    }
}
