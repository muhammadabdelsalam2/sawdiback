<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class City extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'country_id',
        'name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * TODO: Apply tenant scoping once tenant resolver is implemented
     */
}
