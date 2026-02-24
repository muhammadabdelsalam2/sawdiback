<?php

namespace App\Services\Warehouse;

use App\Models\InventoryBatch;
use App\Models\InventoryProduct;
use Illuminate\Support\Collection;

class WarehouseAlertService
{
    public function __construct(private readonly WarehouseStockService $stockService)
    {
    }

    public function lowStockProducts(): Collection
    {
        return InventoryProduct::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(function (InventoryProduct $product) {
                $onHand = $this->stockService->stockOnHand($product->id);

                return [
                    'product' => $product,
                    'stock_on_hand' => $onHand,
                    'is_low_stock' => $onHand <= (float) $product->low_stock_threshold,
                ];
            })
            ->filter(fn (array $row) => $row['is_low_stock'])
            ->values();
    }

    public function expiringBatches(int $days): Collection
    {
        $endDate = now()->addDays($days)->toDateString();

        return InventoryBatch::query()
            ->with('product')
            ->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '>=', now()->toDateString())
            ->whereDate('expiry_date', '<=', $endDate)
            ->orderBy('expiry_date')
            ->get();
    }
}

