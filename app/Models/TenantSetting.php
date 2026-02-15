<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenantSetting extends Model
{
    protected $table = 'tenant_settings';

    protected $fillable = [
        'tenant_id',
        'rtl_enabled',
        'app_name',
        'primary_color',
    ];

    protected $casts = [
        'rtl_enabled' => 'boolean',
    ];

    /**
     * TODO: Apply tenant scoping once tenant resolver is implemented
     */
}
