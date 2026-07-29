<?php

namespace App\Models\Poultry;

use App\Models\Concerns\ScopedByTenant;
use App\Models\Farm;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PoultryHatcheryMachine extends Model
{
    use HasFactory;
    use ScopedByTenant;
    use SoftDeletes;

    protected $fillable = ['tenant_id', 'farm_id', 'machine_number', 'capacity', 'is_active', 'notes'];

    protected $casts = ['capacity' => 'integer', 'is_active' => 'boolean'];

    public function batches(): HasMany
    {
        return $this->hasMany(PoultryHatcheryBatch::class, 'hatchery_machine_id');
    }

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }
}
