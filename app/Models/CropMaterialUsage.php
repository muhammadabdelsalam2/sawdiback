<?php

namespace App\Models;

use App\Models\Concerns\ScopedByTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CropMaterialUsage extends Model
{
    use HasFactory;
    use ScopedByTenant;

    protected $fillable = ['tenant_id', 'crop_id', 'material_type', 'name', 'quantity', 'unit', 'amount', 'used_on', 'notes'];

    protected $casts = [
        'quantity' => 'decimal:2',
        'amount' => 'decimal:2',
        'used_on' => 'date',
    ];

    public function crop(): BelongsTo
    {
        return $this->belongsTo(Crop::class);
    }
}
