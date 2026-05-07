<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use App\Models\Concerns\ScopedByTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AnimalBreed extends Model
{
    use HasFactory;
    use HasTranslations;
    use ScopedByTenant;

    protected $fillable = [
        'tenant_id',
        'species_id',
        'name',
        'name_translations',
    ];

    protected $casts = [
        'name_translations' => 'array',
    ];

    public function getLocalizedNameAttribute(): ?string
    {
        return $this->getLocalized('name_translations', 'name');
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function species(): BelongsTo
    {
        return $this->belongsTo(AnimalSpecies::class, 'species_id');
    }

    public function animals(): HasMany
    {
        return $this->hasMany(LivestockAnimal::class, 'breed_id');
    }
}
