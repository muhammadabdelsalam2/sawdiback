<?php

namespace App\Models\Poultry;

use App\Models\Concerns\ScopedByTenant;
use App\Models\Farm;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PoultryTransportVehicle extends Model
{
    use HasFactory;
    use ScopedByTenant;
    use SoftDeletes;

   protected $fillable = [
    'tenant_id',
    'farm_id',
    'ownership_type',
    'lessor_name',
    'lessor_phone',
    'lease_cost',
    'lease_period',
    'lease_start_date',
    'lease_end_date',
    'plate_number',
    'driver_name',
    'driver_phone',
    'capacity_birds',
    'capacity_crates',
    'status',
    'notes',
];

protected $casts = [
    'capacity_birds'   => 'integer',
    'capacity_crates'  => 'integer',
    'lease_cost'       => 'decimal:2',
    'lease_start_date' => 'date',
    'lease_end_date'   => 'date',
];

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function rentals(): HasMany
    {
        return $this->hasMany(PoultryVehicleRental::class, 'vehicle_id');
    }
}