<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WarehouseAsset extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'farm_id',
        'name',
        'type',
        'storage_location',
        'quantity_or_status',
        'notes',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(WarehouseAssetAttachment::class);
    }
}
