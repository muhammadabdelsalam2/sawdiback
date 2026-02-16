<?php

namespace App\Models;

use App\Models\Concerns\ScopedByTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnimalHealthRecord extends Model
{
    use HasFactory;
    use ScopedByTenant;

    protected $fillable = [
        'tenant_id',
        'animal_id',
        'record_type',
        'diagnosis',
        'treatment',
        'vet_employee_id',
        'cost',
        'next_followup_date',
    ];

    protected $casts = [
        'cost' => 'decimal:2',
        'next_followup_date' => 'date',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function animal(): BelongsTo
    {
        return $this->belongsTo(LivestockAnimal::class, 'animal_id');
    }

    public function vetEmployee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vet_employee_id');
    }
}
