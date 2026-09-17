<?php

namespace App\Models\Poultry;

use App\Models\Concerns\ScopedByTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PoultryHatcheryDailyLog extends Model
{
    use HasFactory;
    use ScopedByTenant;

    protected $fillable = [
        'tenant_id',
        'hatchery_batch_id',
        'log_date',
        'temperature',
        'humidity',
        'has_incident',
        'stoppage_duration_hours', // مدة التوقف بالساعات
        'incident_reason',         // السبب / العطل
        'notes',
    ];

    protected $casts = [
        'log_date'                => 'date',
        'temperature'             => 'decimal:2',
        'humidity'                => 'decimal:2',
        'has_incident'            => 'boolean',
        'stoppage_duration_hours' => 'decimal:2',
    ];

    public function batch(): BelongsTo
    {
        return $this->belongsTo(PoultryHatcheryBatch::class, 'hatchery_batch_id');
    }
    public function breeds(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
{
    return $this->belongsToMany(
        PoultryChickenBreed::class,
        'poultry_hatchery_batch_breeds',
        'hatchery_batch_id',
        'breed_id'
    )->withPivot('egg_count')->withTimestamps();
}
}