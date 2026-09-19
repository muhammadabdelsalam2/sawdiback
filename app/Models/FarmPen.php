<?php

namespace App\Models;

use App\Models\Concerns\ScopedByTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class FarmPen extends Model
{
    use HasFactory, ScopedByTenant, SoftDeletes;

    protected $table = 'farm_pens';

    public const TYPES = [
        'goat',      // ماعز
        'cattle',    // بقر
        'poultry',   // دواجن
        'fish',      // أسماك
        'rabbit',    // أرانب
        'other',     // أخرى
    ];

   protected $fillable = [
        'tenant_id',
        'farm_id',
        'pen_number',
        'type',
        'capacity',
        'current_count',
        'notes',
    ];

    protected $casts = [
        'capacity'      => 'integer',
        'current_count' => 'integer',
    ];

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function financialEntries(): HasMany
    {
        return $this->hasMany(LivestockPenFinancialEntry::class, 'pen_id');
    }

    public function animals(): HasMany
    {
        return $this->hasMany(LivestockAnimal::class, 'pen_id');
    }

    public static function getTypeOptions(): array
    {
        return [
            'goat'    => __('farms.pen_types.goat') ?? 'ماعز',
            'cattle'  => __('farms.pen_types.cattle') ?? 'البقر',
            'poultry' => __('farms.pen_types.poultry') ?? 'الدواجن',
            'fish'    => __('farms.pen_types.fish') ?? 'أسماك',
            'rabbit'  => __('farms.pen_types.rabbit') ?? 'أرانب',
            'other'   => __('farms.pen_types.other') ?? 'أخرى',
        ];
    }
    public function scopeForSelect($query)
    {
        return $query->select(['id', 'farm_id', 'pen_number', 'type', 'capacity', 'current_count'])
                     ->with('farm:id,name');
    }
}