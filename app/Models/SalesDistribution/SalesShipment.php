<?php

namespace App\Models\SalesDistribution;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesShipment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sales_shipments';

    protected $fillable = [
        'tenant_id',
        'sales_order_id',
        'shipment_no',
        'shipping_company',
        'tracking_no',
        'status',
        'shipped_at',
        'delivered_at',
        'warehouse_id',
        'notes',
    ];

    protected $casts = [
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(SalesOrder::class, 'sales_order_id');
    }

    public function statusHistory(): HasMany
    {
        return $this->hasMany(SalesShipmentStatusHistory::class, 'shipment_id');
    }
}
