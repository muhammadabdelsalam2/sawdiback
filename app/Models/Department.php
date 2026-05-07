<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    use HasFactory, SoftDeletes, HasTranslations;

    protected $fillable = [
        'tenant_id',
        'name',
        'name_translations',
        'code',
    ];

    protected $casts = [
        'name_translations' => 'array',
    ];

    public function getLocalizedNameAttribute(): ?string
    {
        return $this->getLocalized('name_translations', 'name');
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }
}
