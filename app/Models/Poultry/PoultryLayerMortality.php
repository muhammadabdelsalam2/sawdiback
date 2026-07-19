<?php

namespace App\Models\Poultry;

use App\Models\Concerns\ScopedByTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PoultryLayerMortality extends Model
{
    use HasFactory;
    use ScopedByTenant;

    protected $fillable = ['tenant_id', 'layer_flock_id', 'mortality_date', 'quantity', 'notes'];

    protected $casts = ['mortality_date' => 'date', 'quantity' => 'integer'];

    public function flock(): BelongsTo
    {
        return $this->belongsTo(PoultryLayerFlock::class, 'layer_flock_id');
    }
}
