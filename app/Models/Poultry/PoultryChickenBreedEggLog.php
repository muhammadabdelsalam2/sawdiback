<?php

namespace App\Models\Poultry;

use App\Models\Concerns\ScopedByTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PoultryChickenBreedEggLog extends Model
{
    use HasFactory;
    use ScopedByTenant;

    protected $fillable = ['tenant_id', 'chicken_breed_id', 'production_date', 'eggs_count', 'fertilized_count', 'unfertilized_count', 'notes'];

    protected $casts = [
        'production_date' => 'date',
        'eggs_count' => 'integer',
        'fertilized_count' => 'integer',
        'unfertilized_count' => 'integer',
    ];

    public function breed(): BelongsTo
    {
        return $this->belongsTo(PoultryChickenBreed::class, 'chicken_breed_id');
    }
}
