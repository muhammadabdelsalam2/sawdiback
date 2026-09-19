<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class FishBatch extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'fish_batches';

    protected $fillable = [
        'tenant_id',
        'farm_id',
        'batch_code',
        'pond_name',
        'fish_type',
        'initial_count',
        'current_count',
        'mortality_count',
        'initial_weight_g',
        'current_weight_g',
        'target_weight_g',
        'feed_consumed_kg',
        'total_cost',
        'started_at',
        'harvested_at',
        'status',
        'notes',
    ];

    protected $casts = [
        'started_at' => 'date',
        'harvested_at' => 'date',
        'initial_weight_g' => 'decimal:2',
        'current_weight_g' => 'decimal:2',
        'target_weight_g' => 'decimal:2',
        'feed_consumed_kg' => 'decimal:2',
        'total_cost' => 'decimal:2',
    ];

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }
}
