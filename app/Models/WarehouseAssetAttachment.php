<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WarehouseAssetAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'warehouse_asset_id',
        'name',
        'path',
        'uploaded_at',
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(WarehouseAsset::class, 'warehouse_asset_id');
    }

    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->path);
    }
}
