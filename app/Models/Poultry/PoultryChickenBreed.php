<?php

namespace App\Models\Poultry;

use App\Models\FarmPen;
use App\Models\Concerns\ScopedByTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PoultryChickenBreed extends Model
{
    use HasFactory;
    use ScopedByTenant;
    use SoftDeletes;

    protected $fillable = ['tenant_id', 'pen_id', 'code', 'breed_type', 'purchase_amount', 'female_count', 'male_count', 'started_at', 'notes'];

    protected $casts = [
        'purchase_amount' => 'decimal:2',
        'female_count' => 'integer',
        'male_count' => 'integer',
        'started_at' => 'date',
    ];

    public function eggLogs(): HasMany
    {
        return $this->hasMany(PoultryChickenBreedEggLog::class, 'chicken_breed_id');
    }

    public function pen(): BelongsTo
    {
        return $this->belongsTo(FarmPen::class, 'pen_id');
    }
}
