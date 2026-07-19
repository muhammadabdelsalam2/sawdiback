<?php

namespace App\Models\Poultry;

use App\Models\Concerns\ScopedByTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PoultryBroilerSale extends Model
{
    use HasFactory;
    use ScopedByTenant;

    protected $fillable = ['tenant_id', 'broiler_cycle_id', 'sale_date', 'quantity', 'unit_price', 'total_amount', 'customer_name', 'notes'];

    protected $casts = [
        'sale_date' => 'date',
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function cycle(): BelongsTo
    {
        return $this->belongsTo(PoultryBroilerCycle::class, 'broiler_cycle_id');
    }
}
