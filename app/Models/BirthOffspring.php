<?php

namespace App\Models;

use App\Models\Concerns\ScopedByTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BirthOffspring extends Model
{
    use HasFactory;
    use ScopedByTenant;

    protected $table = 'birth_offspring';

    protected $fillable = [
        'tenant_id',
        'birth_id',
        'offspring_animal_id',
        'birth_weight',
        'notes',
    ];

    protected $casts = [
        'birth_weight' => 'decimal:2',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function birth(): BelongsTo
    {
        return $this->belongsTo(AnimalBirth::class, 'birth_id');
    }

    public function offspringAnimal(): BelongsTo
    {
        return $this->belongsTo(LivestockAnimal::class, 'offspring_animal_id');
    }
}
