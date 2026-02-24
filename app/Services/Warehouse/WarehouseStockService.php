<?php

namespace App\Services\Warehouse;

use App\Models\InventoryBatch;
use App\Models\InventoryProduct;

class WarehouseStockService
{
    public function stockOnHand(int $productId): float
    {
        return round((float) InventoryBatch::query()
            ->where('inventory_product_id', $productId)
            ->sum('quantity_available'), 2);
    }

    public function isLowStock(InventoryProduct $product): bool
    {
        return $this->stockOnHand($product->id) <= (float) $product->low_stock_threshold;
    }
}

