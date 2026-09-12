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

    protected $fillable = [
        'tenant_id',
        'farm_id',
        'pen_number',
        'name',
        'type', // goat, cattle, poultry, fish, rabbit, other
        'capacity',
        'current_count',
        'status',
        'notes',
    ];

    protected $casts = [
        'capacity' => 'integer',
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
}