<?php

namespace App\Models;

use App\Models\Concerns\ScopedByTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Farm extends Model
{
    use HasFactory;
    use ScopedByTenant;
    use SoftDeletes;

    protected $fillable = ['tenant_id', 'name', 'type', 'location', 'is_active', 'notes'];

    protected $casts = ['is_active' => 'boolean'];

    public function pens(): HasMany
    {
        return $this->hasMany(FarmPen::class);
    }

    public function crops(): HasMany
    {
        return $this->hasMany(Crop::class);
    }
}
