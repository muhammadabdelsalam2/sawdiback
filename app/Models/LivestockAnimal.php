<?php

namespace App\Models;

use App\Models\Concerns\ScopedByTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LivestockAnimal extends Model
{
    use HasFactory;
    use ScopedByTenant;

    protected $fillable = [
        'tenant_id',
        'tag_number',
        'species_id',
        'breed_id',
        'gender',
        'birth_date',
        'source_type',
        'purchase_date',
        'purchase_price',
        'status',
        'health_status',
        'mother_id',
        'father_id',
        'notes',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'purchase_date' => 'date',
        'purchase_price' => 'decimal:2',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function species(): BelongsTo
    {
        return $this->belongsTo(AnimalSpecies::class, 'species_id');
    }

    public function breed(): BelongsTo
    {
        return $this->belongsTo(AnimalBreed::class, 'breed_id');
    }

    public function mother(): BelongsTo
    {
        return $this->belongsTo(self::class, 'mother_id');
    }

    public function father(): BelongsTo
    {
        return $this->belongsTo(self::class, 'father_id');
    }

    public function offspringFromMother(): HasMany
    {
        return $this->hasMany(self::class, 'mother_id');
    }

    public function offspringFromFather(): HasMany
    {
        return $this->hasMany(self::class, 'father_id');
    }

    public function healthRecords(): HasMany
    {
        return $this->hasMany(AnimalHealthRecord::class, 'animal_id');
    }

    public function vaccinations(): HasMany
    {
        return $this->hasMany(AnimalVaccination::class, 'animal_id');
    }

    public function reproductionCyclesAsFemale(): HasMany
    {
        return $this->hasMany(ReproductionCycle::class, 'female_animal_id');
    }

    public function reproductionCyclesAsMale(): HasMany
    {
        return $this->hasMany(ReproductionCycle::class, 'male_animal_id');
    }

    public function birthsAsMother(): HasMany
    {
        return $this->hasMany(AnimalBirth::class, 'mother_id');
    }

    public function birthOffspringEntries(): HasMany
    {
        return $this->hasMany(BirthOffspring::class, 'offspring_animal_id');
    }

    public function milkProductionLogs(): HasMany
    {
        return $this->hasMany(MilkProductionLog::class, 'animal_id');
    }

    public function feedingLogs(): HasMany
    {
        return $this->hasMany(AnimalFeedingLog::class, 'animal_id');
    }

    public function weightLogs(): HasMany
    {
        return $this->hasMany(AnimalWeightLog::class, 'animal_id');
    }

    public function statusHistory(): HasMany
    {
        return $this->hasMany(AnimalStatusHistory::class, 'animal_id');
    }

    public function feedConsumptions(): HasMany
    {
        return $this->hasMany(FeedConsumption::class, 'animal_id');
    }
}
