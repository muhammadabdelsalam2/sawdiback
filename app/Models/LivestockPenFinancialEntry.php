<?php

namespace App\Models;

use App\Models\Concerns\ScopedByTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LivestockPenFinancialEntry extends Model
{
    use HasFactory;
    use ScopedByTenant;

    protected $fillable = ['tenant_id', 'pen_id', 'type', 'amount', 'entry_date', 'notes'];

    protected $casts = [
        'amount' => 'decimal:2',
        'entry_date' => 'date',
    ];

    public function pen(): BelongsTo
    {
        return $this->belongsTo(FarmPen::class, 'pen_id');
    }
}
