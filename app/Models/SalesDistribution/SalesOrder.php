<?php

namespace App\Models\SalesDistribution;

use App\Models\Farm;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SalesOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sales_orders';

    protected $fillable = [
        'tenant_id',
        'order_no',
        'customer_id',
        'contract_id',
        'order_date',
        'status',
        'total',
        'notes',
    ];

    protected $casts = [
        'order_date' => 'date',
        'total' => 'decimal:2',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(SalesCustomer::class, 'customer_id');
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(SalesContract::class, 'contract_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(SalesOrderItem::class, 'sales_order_id');
    }

    public function shipments(): HasMany
    {
        return $this->hasMany(SalesShipment::class, 'sales_order_id');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(SalesInvoice::class, 'sales_order_id');
    }

    public function scopeLinkedToFarm(Builder $query, int $farmId): Builder
    {
        return $query->whereExists(function ($subquery) use ($farmId): void {
            $subquery->selectRaw('1')
                ->from('sales_order_items')
                ->join('inventory_products', 'sales_order_items.product_id', '=', 'inventory_products.id')
                ->whereColumn('sales_order_items.sales_order_id', 'sales_orders.id')
                ->where('inventory_products.farm_id', $farmId);
        });
    }

    public function farms(): Collection
    {
        return Farm::withoutGlobalScopes()
            ->join('inventory_products', 'farms.id', '=', 'inventory_products.farm_id')
            ->join('sales_order_items', 'inventory_products.id', '=', 'sales_order_items.product_id')
            ->where('sales_order_items.sales_order_id', $this->id)
            ->select('farms.*')
            ->distinct()
            ->get()
            ->values();
    }

    public function farmLineTotal(int $farmId): float
    {
        return round((float) DB::table('sales_order_items')
            ->join('inventory_products', 'sales_order_items.product_id', '=', 'inventory_products.id')
            ->where('sales_order_items.sales_order_id', $this->id)
            ->where('inventory_products.farm_id', $farmId)
            ->sum('sales_order_items.line_total'), 2);
    }
}
