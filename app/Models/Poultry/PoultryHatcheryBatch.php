<?php

namespace App\Models\Poultry;

use App\Models\Concerns\ScopedByTenant;
use App\Models\FarmPen;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PoultryHatcheryBatch extends Model
{
    use HasFactory;
    use ScopedByTenant;
    use SoftDeletes;

    // السلالات العشرة المعتمدة
    public const BREEDS = [
        'ross_308'      => 'روس 308 (Ross 308)',
        'cobb_500'      => 'كوب 500 (Cobb 500)',
        'sasso'         => 'ساسو (Sasso)',
        'baladi'        => 'بلدي أصيل (Pure Baladi)',
        'fayoumi'       => 'فيومي (Fayoumi)',
        'lohmann_brown' => 'لوهمان براون (Lohmann Brown)',
        'hy_line'       => 'هاي لاين (Hy-Line)',
        'isa_brown'     => 'إيزا براون (ISA Brown)',
        'hubbard'       => 'هابرد (Hubbard)',
        'dual_purpose'  => 'هجين ثنائي الغرض (Dual Purpose)',
    ];

    protected $fillable = [
        'tenant_id',
        'hatchery_machine_id',
        'batch_number',
        'purchase_amount',
        'breed_type',
        'source_type',
        'egg_source',
        'farm_pen_id',
        'pen_id',
        'seller_name',
        'seller_phone',
        'seller_location',
        'loaded_at',
        'expected_hatch_at',
        'actual_hatch_at',
        'eggs_loaded',
        'chicks_produced',
        'notes',
    ];

    protected $casts = [
        'purchase_amount'   => 'decimal:2',
        'loaded_at'         => 'date',
        'expected_hatch_at' => 'date',
        'actual_hatch_at'   => 'date',
        'eggs_loaded'       => 'integer',
        'chicks_produced'   => 'integer',
    ];

    protected $appends = ['success_rate', 'breed_name'];

    public function machine(): BelongsTo
    {
        return $this->belongsTo(PoultryHatcheryMachine::class, 'hatchery_machine_id');
    }

    public function pen(): BelongsTo
    {
        return $this->belongsTo(FarmPen::class, 'pen_id');
    }

    public function farmPen(): BelongsTo
    {
        return $this->belongsTo(FarmPen::class, 'farm_pen_id');
    }

    public function breeds(): BelongsToMany
    {
        return $this->belongsToMany(
            ChickenBreed::class,
            'poultry_hatchery_batch_breeds',
            'hatchery_batch_id',
            'breed_id'
        )->withPivot('egg_count')->withTimestamps();
    }

    public function dailyLogs(): HasMany
    {
        return $this->hasMany(PoultryHatcheryDailyLog::class, 'hatchery_batch_id');
    }

    public function getSuccessRateAttribute(): string
    {
        if ((int) $this->eggs_loaded === 0) {
            return '0.00';
        }

        return number_format(((int) $this->chicks_produced / (int) $this->eggs_loaded) * 100, 2, '.', '');
    }

    public function getBreedNameAttribute(): string
    {
        return self::BREEDS[$this->breed_type] ?? ($this->breed_type ?? '-');
    }
}