<?php

namespace App\Models\SalesDistribution;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesShipmentStatusHistory extends Model
{
    use HasFactory;

    protected $table = 'sales_shipment_status_histories';

    protected $fillable = [
        'shipment_id',
        'from_status',
        'to_status',
        'changed_at',
        'changed_by',
        'notes',
    ];

    protected $casts = [
        'changed_at' => 'datetime',
    ];

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(SalesShipment::class, 'shipment_id');
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
