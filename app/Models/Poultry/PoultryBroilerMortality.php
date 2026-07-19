<?php

namespace App\Models\Poultry;

use App\Models\Concerns\ScopedByTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PoultryBroilerMortality extends Model
{
    use HasFactory;
    use ScopedByTenant;

    protected $fillable = ['tenant_id', 'broiler_cycle_id', 'mortality_date', 'quantity', 'notes'];

    protected $casts = [
        'mortality_date' => 'date',
        'quantity' => 'integer',
    ];

    public function cycle(): BelongsTo
    {
        return $this->belongsTo(PoultryBroilerCycle::class, 'broiler_cycle_id');
    }
}
