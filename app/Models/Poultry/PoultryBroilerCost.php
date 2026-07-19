<?php

namespace App\Models\Poultry;

use App\Models\Concerns\ScopedByTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PoultryBroilerCost extends Model
{
    use HasFactory;
    use ScopedByTenant;

    protected $fillable = ['tenant_id', 'broiler_cycle_id', 'cost_type', 'amount', 'cost_date', 'notes'];

    protected $casts = [
        'amount' => 'decimal:2',
        'cost_date' => 'date',
    ];

    public function cycle(): BelongsTo
    {
        return $this->belongsTo(PoultryBroilerCycle::class, 'broiler_cycle_id');
    }
}
