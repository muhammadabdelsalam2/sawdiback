<?php

namespace App\Models\Poultry;

use App\Models\Concerns\ScopedByTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PoultryHatcheryBatch extends Model
{
    use HasFactory;
    use ScopedByTenant;
    use SoftDeletes;

    protected $fillable = ['tenant_id', 'hatchery_machine_id', 'batch_number', 'loaded_at', 'expected_hatch_at', 'actual_hatch_at', 'eggs_loaded', 'chicks_produced', 'notes'];

    protected $casts = [
        'loaded_at' => 'date',
        'expected_hatch_at' => 'date',
        'actual_hatch_at' => 'date',
        'eggs_loaded' => 'integer',
        'chicks_produced' => 'integer',
    ];

    protected $appends = ['success_rate'];

    public function machine(): BelongsTo
    {
        return $this->belongsTo(PoultryHatcheryMachine::class, 'hatchery_machine_id');
    }

    public function getSuccessRateAttribute(): string
    {
        if ((int) $this->eggs_loaded === 0) {
            return '0.00';
        }

        return number_format(((int) $this->chicks_produced / (int) $this->eggs_loaded) * 100, 2, '.', '');
    }
}
