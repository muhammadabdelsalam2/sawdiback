<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Country extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'name',
        'iso2',
        'iso3',
        'phone_code',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * TODO: Apply tenant scoping once tenant resolver is implemented
     * Example later: add global scope where('tenant_id', currentTenantId()).
     */
    public function cities(): HasMany
{
    return $this->hasMany(City::class);
}
}




