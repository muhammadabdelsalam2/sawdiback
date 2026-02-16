<?php

namespace App\Models;

use App\Models\Concerns\ScopedByTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ReproductionCycle extends Model
{
    use HasFactory;
    use ScopedByTenant;

    protected $fillable = [
        'tenant_id',
        'female_animal_id',
        'heat_date',
        'insemination_date',
        'insemination_type',
        'male_animal_id',
        'pregnancy_confirmed',
        'pregnancy_check_date',
        'expected_delivery_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'heat_date' => 'date',
        'insemination_date' => 'date',
        'pregnancy_confirmed' => 'boolean',
        'pregnancy_check_date' => 'date',
        'expected_delivery_date' => 'date',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function femaleAnimal(): BelongsTo
    {
        return $this->belongsTo(LivestockAnimal::class, 'female_animal_id');
    }

    public function maleAnimal(): BelongsTo
    {
        return $this->belongsTo(LivestockAnimal::class, 'male_animal_id');
    }

    public function birth(): HasOne
    {
        return $this->hasOne(AnimalBirth::class, 'reproduction_cycle_id');
    }
}
