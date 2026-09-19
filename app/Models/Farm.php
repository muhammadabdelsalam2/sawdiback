<?php

namespace App\Models;

use App\Models\Concerns\ScopedByTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Farm extends Model
{
    use HasFactory, ScopedByTenant, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'name',
        'code',
        'ownership_type', // owned (ملك) / rented (إيجار)
        'location',
        'area_sqm',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'area_sqm' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function pens(): HasMany
    {
        return $this->hasMany(FarmPen::class);
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }
}