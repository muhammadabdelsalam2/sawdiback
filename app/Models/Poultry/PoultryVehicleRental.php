<?php

namespace App\Models\Poultry;

use App\Models\Concerns\ScopedByTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PoultryVehicleRental extends Model
{
    use HasFactory;
    use ScopedByTenant;
    use SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'vehicle_id',
        'rental_type',
        'customer_name',
        'customer_phone',
        'started_at',
        'ended_at',
        'origin',
        'destination',
        'rental_fee',
        'fuel_cost',
        'driver_commission',
        'other_expenses',
        'payment_status',
        'notes',
    ];

    protected $casts = [
        'started_at'        => 'datetime',
        'ended_at'          => 'datetime',
        'rental_fee'        => 'decimal:2',
        'fuel_cost'         => 'decimal:2',
        'driver_commission' => 'decimal:2',
        'other_expenses'    => 'decimal:2',
    ];

    protected $appends = [
        'total_trip_expenses',
        'net_trip_profit',
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(PoultryTransportVehicle::class, 'vehicle_id');
    }

    public function getTotalTripExpensesAttribute(): string
    {
        $expenses = (float) $this->fuel_cost + (float) $this->driver_commission + (float) $this->other_expenses;

        return number_format($expenses, 2, '.', '');
    }

    public function getNetTripProfitAttribute(): string
    {
        $profit = (float) $this->rental_fee - (float) $this->total_trip_expenses;

        return number_format($profit, 2, '.', '');
    }
}