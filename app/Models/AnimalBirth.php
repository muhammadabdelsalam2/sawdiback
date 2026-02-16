<?php

namespace App\Models;

use App\Models\Concerns\ScopedByTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AnimalBirth extends Model
{
    use HasFactory;
    use ScopedByTenant;

    protected $fillable = [
        'tenant_id',
        'mother_id',
        'reproduction_cycle_id',
        'birth_date',
        'complications',
        'notes',
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function mother(): BelongsTo
    {
        return $this->belongsTo(LivestockAnimal::class, 'mother_id');
    }

    public function reproductionCycle(): BelongsTo
    {
        return $this->belongsTo(ReproductionCycle::class, 'reproduction_cycle_id');
    }

    public function offspring(): HasMany
    {
        return $this->hasMany(BirthOffspring::class, 'birth_id');
    }
}
