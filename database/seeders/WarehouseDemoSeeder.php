<?php

namespace Database\Seeders;

use App\Models\InventoryBatch;
use App\Models\InventoryDelivery;
use App\Models\InventoryDeliveryItem;
use App\Models\InventoryMovement;
use App\Models\InventoryProduct;
use App\Models\InventoryProductionRecord;
use App\Models\LivestockAnimal;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class WarehouseDemoSeeder extends Seeder
{
    public function run(): void
    {
        $tenants = Tenant::withoutGlobalScopes()->get();
        if ($tenants->isEmpty()) {
            return;
        }

        foreach ($tenants as $tenant) {
            $tenantId = $tenant->id;
            $tenantCode = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', (string) $tenantId), 0, 6));
            $animalId = LivestockAnimal::withoutGlobalScopes()->where('tenant_id', $tenantId)->value('id');

            $products = $this->seedProducts($tenantId);
            $this->seedBatchesAndMovements($tenantId, $tenantCode, $products);
            $this->seedProduction($tenantId, $tenantCode, $animalId, $products);
            $this->seedDeliveries($tenantId, $tenantCode, $products);
        }
    }

    private function seedProducts(string $tenantId): array
    {
        $data = [
            ['code' => 'WHD-FEED-CORN', 'name' => 'Corn Mix Feed', 'category' => 'feed', 'unit' => 'kg', 'track_expiry' => false, 'low_stock_threshold' => 100],
            ['code' => 'WHD-FEED-HAY', 'name' => 'Dry Hay', 'category' => 'feed', 'unit' => 'kg', 'track_expiry' => false, 'low_stock_threshold' => 120],
            ['code' => 'WHD-FEED-SUP', 'name' => 'Mineral Supplement', 'category' => 'feed', 'unit' => 'kg', 'track_expiry' => true, 'low_stock_threshold' => 40],
            ['code' => 'WHD-VET-VAC1', 'name' => 'FMD Vaccine', 'category' => 'vet_medicine', 'unit' => 'vial', 'track_expiry' => true, 'low_stock_threshold' => 25],
            ['code' => 'WHD-VET-VAC2', 'name' => 'Brucella Vaccine', 'category' => 'vet_medicine', 'unit' => 'vial', 'track_expiry' => true, 'low_stock_threshold' => 20],
            ['code' => 'WHD-VET-ANTI', 'name' => 'Antibiotic Shot', 'category' => 'vet_medicine', 'unit' => 'bottle', 'track_expiry' => true, 'low_stock_threshold' => 15],
            ['code' => 'WHD-EQP-MILK', 'name' => 'Milking Unit', 'category' => 'equipment', 'unit' => 'piece', 'track_expiry' => false, 'low_stock_threshold' => 2],
            ['code' => 'WHD-EQP-PIPE', 'name' => 'Milk Transfer Pipe', 'category' => 'equipment', 'unit' => 'piece', 'track_expiry' => false, 'low_stock_threshold' => 5],
            ['code' => 'WHD-EQP-FILT', 'name' => 'Filter Cartridge', 'category' => 'equipment', 'unit' => 'piece', 'track_expiry' => true, 'low_stock_threshold' => 8],
            ['code' => 'WHD-AP-MILK', 'name' => 'Raw Milk', 'category' => 'animal_product', 'unit' => 'liter', 'track_expiry' => true, 'low_stock_threshold' => 180],
            ['code' => 'WHD-AP-MEAT', 'name' => 'Fresh Meat', 'category' => 'animal_product', 'unit' => 'kg', 'track_expiry' => true, 'low_stock_threshold' => 60],
            ['code' => 'WHD-AP-GHEE', 'name' => 'Ghee', 'category' => 'animal_product', 'unit' => 'kg', 'track_expiry' => true, 'low_stock_threshold' => 30],
        ];

        $products = [];
        foreach ($data as $row) {
            $products[$row['code']] = InventoryProduct::withoutGlobalScopes()->updateOrCreate(
                ['tenant_id' => $tenantId, 'code' => $row['code']],
                [
                    'name' => $row['name'],
                    'category' => $row['category'],
                    'unit' => $row['unit'],
                    'track_expiry' => $row['track_expiry'],
                    'low_stock_threshold' => $row['low_stock_threshold'],
                    'is_active' => true,
                ]
            );
        }

        return $products;
    }

    private function seedBatchesAndMovements(string $tenantId, string $tenantCode, array $products): void
    {
        $plans = [
            ['code' => 'WHD-FEED-CORN', 'batch' => "B-{$tenantCode}-CORN-01", 'in' => 600, 'available' => 540, 'unit_cost' => 1.45, 'received' => now()->subDays(30), 'expiry' => null],
            ['code' => 'WHD-FEED-HAY', 'batch' => "B-{$tenantCode}-HAY-01", 'in' => 500, 'available' => 470, 'unit_cost' => 1.10, 'received' => now()->subDays(26), 'expiry' => null],
            ['code' => 'WHD-FEED-SUP', 'batch' => "B-{$tenantCode}-SUP-01", 'in' => 120, 'available' => 32, 'unit_cost' => 4.20, 'received' => now()->subDays(20), 'expiry' => now()->addDays(12)],
            ['code' => 'WHD-VET-VAC1', 'batch' => "B-{$tenantCode}-VAC1-01", 'in' => 80, 'available' => 18, 'unit_cost' => 32.00, 'received' => now()->subDays(45), 'expiry' => now()->addDays(10)],
            ['code' => 'WHD-VET-VAC2', 'batch' => "B-{$tenantCode}-VAC2-01", 'in' => 70, 'available' => 22, 'unit_cost' => 29.00, 'received' => now()->subDays(40), 'expiry' => now()->addDays(20)],
            ['code' => 'WHD-VET-ANTI', 'batch' => "B-{$tenantCode}-ANTI-01", 'in' => 50, 'available' => 9, 'unit_cost' => 41.00, 'received' => now()->subDays(50), 'expiry' => now()->addDays(8)],
            ['code' => 'WHD-EQP-MILK', 'batch' => "B-{$tenantCode}-EQM-01", 'in' => 10, 'available' => 7, 'unit_cost' => 380.00, 'received' => now()->subDays(70), 'expiry' => null],
            ['code' => 'WHD-EQP-PIPE', 'batch' => "B-{$tenantCode}-EQP-01", 'in' => 40, 'available' => 28, 'unit_cost' => 52.00, 'received' => now()->subDays(65), 'expiry' => null],
            ['code' => 'WHD-EQP-FILT', 'batch' => "B-{$tenantCode}-EQF-01", 'in' => 30, 'available' => 6, 'unit_cost' => 24.00, 'received' => now()->subDays(60), 'expiry' => now()->addDays(15)],
            ['code' => 'WHD-AP-MILK', 'batch' => "B-{$tenantCode}-MILK-01", 'in' => 520, 'available' => 440, 'unit_cost' => 2.40, 'received' => now()->subDays(2), 'expiry' => now()->addDays(4)],
            ['code' => 'WHD-AP-MEAT', 'batch' => "B-{$tenantCode}-MEAT-01", 'in' => 190, 'available' => 150, 'unit_cost' => 19.20, 'received' => now()->subDays(3), 'expiry' => now()->addDays(6)],
            ['code' => 'WHD-AP-GHEE', 'batch' => "B-{$tenantCode}-GHEE-01", 'in' => 85, 'available' => 68, 'unit_cost' => 14.00, 'received' => now()->subDays(10), 'expiry' => now()->addDays(25)],
        ];

        foreach ($plans as $plan) {
            $product = $products[$plan['code']];
            $incoming = (float) $plan['in'];
            $available = (float) $plan['available'];
            $consumed = max(0, round($incoming - $available, 2));

            $batch = InventoryBatch::withoutGlobalScopes()->firstOrCreate(
                [
                    'tenant_id' => $tenantId,
                    'inventory_product_id' => $product->id,
                    'batch_number' => $plan['batch'],
                ],
                [
                    'production_date' => optional($plan['received'])->copy()->subDays(2)->toDateString(),
                    'expiry_date' => $plan['expiry'] ? $plan['expiry']->toDateString() : null,
                    'received_at' => $plan['received']->toDateString(),
                    'quantity_initial' => $incoming,
                    'quantity_available' => $available,
                    'unit_cost' => $plan['unit_cost'],
                    'source_type' => 'warehouse_demo_seed',
                    'source_id' => null,
                ]
            );

            $this->firstInMovement($tenantId, $product->id, $batch->id, $incoming, (float) $plan['unit_cost'], $plan['received']->toDateString());

            if ($consumed > 0) {
                $this->firstOutMovement($tenantId, $product->id, $batch->id, $consumed, (float) $plan['unit_cost'], now()->subDays(rand(1, 14))->toDateString(), 'warehouse_demo_usage');
            }
        }
    }

    private function seedProduction(string $tenantId, string $tenantCode, ?int $animalId, array $products): void
    {
        $rows = [
            ['code' => 'WHD-AP-MILK', 'date' => now()->subDays(1)->toDateString(), 'qty' => 120, 'cost' => 2.35],
            ['code' => 'WHD-AP-MEAT', 'date' => now()->subDays(2)->toDateString(), 'qty' => 40, 'cost' => 18.80],
            ['code' => 'WHD-AP-GHEE', 'date' => now()->subDays(4)->toDateString(), 'qty' => 20, 'cost' => 13.70],
        ];

        foreach ($rows as $index => $row) {
            $product = $products[$row['code']];

            $production = InventoryProductionRecord::withoutGlobalScopes()->firstOrCreate(
                [
                    'tenant_id' => $tenantId,
                    'inventory_product_id' => $product->id,
                    'production_date' => $row['date'],
                    'quantity' => $row['qty'],
                ],
                [
                    'livestock_animal_id' => $animalId,
                    'unit_cost' => $row['cost'],
                    'notes' => 'Warehouse demo production',
                ]
            );

            $batch = InventoryBatch::withoutGlobalScopes()->firstOrCreate(
                [
                    'tenant_id' => $tenantId,
                    'inventory_product_id' => $product->id,
                    'batch_number' => "PR-{$tenantCode}-" . ($index + 1),
                ],
                [
                    'production_date' => $row['date'],
                    'expiry_date' => now()->addDays(7 + ($index * 5))->toDateString(),
                    'received_at' => $row['date'],
                    'quantity_initial' => $row['qty'],
                    'quantity_available' => $row['qty'],
                    'unit_cost' => $row['cost'],
                    'source_type' => 'production_seed',
                    'source_id' => $production->id,
                ]
            );

            $this->firstInMovement($tenantId, $product->id, $batch->id, (float) $row['qty'], (float) $row['cost'], $row['date']);
        }
    }

    private function seedDeliveries(string $tenantId, string $tenantCode, array $products): void
    {
        $milkBatch = InventoryBatch::withoutGlobalScopes()
            ->where('tenant_id', $tenantId)
            ->where('inventory_product_id', $products['WHD-AP-MILK']->id)
            ->orderByDesc('id')
            ->first();

        $meatBatch = InventoryBatch::withoutGlobalScopes()
            ->where('tenant_id', $tenantId)
            ->where('inventory_product_id', $products['WHD-AP-MEAT']->id)
            ->orderByDesc('id')
            ->first();

        if (!$milkBatch || !$meatBatch) {
            return;
        }

        $delivery = InventoryDelivery::withoutGlobalScopes()->firstOrCreate(
            [
                'tenant_id' => $tenantId,
                'delivery_number' => "WDEL-{$tenantCode}-1001",
            ],
            [
                'customer_name' => 'Retail Partner A',
                'delivered_at' => now()->subHours(12),
                'status' => 'delivered',
                'notes' => 'Warehouse demo delivery',
            ]
        );

        if ($delivery->items()->count() > 0) {
            return;
        }

        $milkQty = 35.0;
        $meatQty = 12.0;

        InventoryDeliveryItem::withoutGlobalScopes()->create([
            'tenant_id' => $tenantId,
            'inventory_delivery_id' => $delivery->id,
            'inventory_product_id' => $milkBatch->inventory_product_id,
            'inventory_batch_id' => $milkBatch->id,
            'quantity' => $milkQty,
            'unit_price' => 3.2,
            'total_price' => round(3.2 * $milkQty, 2),
        ]);

        InventoryDeliveryItem::withoutGlobalScopes()->create([
            'tenant_id' => $tenantId,
            'inventory_delivery_id' => $delivery->id,
            'inventory_product_id' => $meatBatch->inventory_product_id,
            'inventory_batch_id' => $meatBatch->id,
            'quantity' => $meatQty,
            'unit_price' => 25.5,
            'total_price' => round(25.5 * $meatQty, 2),
        ]);

        $milkBatch->update([
            'quantity_available' => max(0, round((float) $milkBatch->quantity_available - $milkQty, 2)),
        ]);
        $meatBatch->update([
            'quantity_available' => max(0, round((float) $meatBatch->quantity_available - $meatQty, 2)),
        ]);

        $this->firstOutMovement($tenantId, $milkBatch->inventory_product_id, $milkBatch->id, $milkQty, (float) ($milkBatch->unit_cost ?? 0), now()->toDateString(), 'warehouse_demo_delivery');
        $this->firstOutMovement($tenantId, $meatBatch->inventory_product_id, $meatBatch->id, $meatQty, (float) ($meatBatch->unit_cost ?? 0), now()->toDateString(), 'warehouse_demo_delivery');

        $this->seedBulkDeliveries($tenantId, $tenantCode, $products, 30);
    }

    private function seedBulkDeliveries(string $tenantId, string $tenantCode, array $products, int $count): void
    {
        $productCodes = ['WHD-AP-MILK', 'WHD-AP-MEAT', 'WHD-AP-GHEE'];
        $customers = [
            'Retail Partner A',
            'Retail Partner B',
            'Distribution Center North',
            'Distribution Center South',
            'Wholesale Client X',
            'Wholesale Client Y',
        ];

        for ($i = 1; $i <= $count; $i++) {
            $deliveryNumber = sprintf('WDEL-%s-%04d', $tenantCode, 2000 + $i);
            $delivery = InventoryDelivery::withoutGlobalScopes()->firstOrCreate(
                [
                    'tenant_id' => $tenantId,
                    'delivery_number' => $deliveryNumber,
                ],
                [
                    'customer_name' => $customers[$i % count($customers)],
                    'delivered_at' => now()->subHours($i * 3),
                    'status' => 'delivered',
                    'notes' => 'Warehouse bulk demo delivery',
                ]
            );

            if ($delivery->wasRecentlyCreated === false || $delivery->items()->count() > 0) {
                continue;
            }

            foreach ($productCodes as $codeIndex => $code) {
                if (!isset($products[$code])) {
                    continue;
                }

                $product = $products[$code];
                $requestedQty = $this->bulkQuantityFor($code, $i + $codeIndex);
                if ($requestedQty <= 0) {
                    continue;
                }

                $allocated = $this->allocateFromBatches($tenantId, $product->id, $requestedQty);
                foreach ($allocated as $slice) {
                    $batch = $slice['batch'];
                    $qty = $slice['qty'];
                    $unitCost = (float) ($batch->unit_cost ?? 0);
                    $unitPrice = round($unitCost * 1.35, 2);

                    InventoryDeliveryItem::withoutGlobalScopes()->create([
                        'tenant_id' => $tenantId,
                        'inventory_delivery_id' => $delivery->id,
                        'inventory_product_id' => $product->id,
                        'inventory_batch_id' => $batch->id,
                        'quantity' => $qty,
                        'unit_price' => $unitPrice,
                        'total_price' => round($unitPrice * $qty, 2),
                    ]);

                    $this->firstOutMovement(
                        $tenantId,
                        $product->id,
                        $batch->id,
                        $qty,
                        $unitCost,
                        optional($delivery->delivered_at)->toDateString() ?? now()->toDateString(),
                        'warehouse_demo_bulk_delivery'
                    );
                }
            }
        }
    }

    private function bulkQuantityFor(string $code, int $seed): float
    {
        if ($code === 'WHD-AP-MILK') {
            return (float) (4 + ($seed % 6)); // 4..9 liters
        }

        if ($code === 'WHD-AP-MEAT') {
            return (float) (2 + ($seed % 4)); // 2..5 kg
        }

        if ($code === 'WHD-AP-GHEE') {
            return (float) (1 + ($seed % 3)); // 1..3 kg
        }

        return 0.0;
    }

    private function allocateFromBatches(string $tenantId, int $productId, float $requestedQty): array
    {
        $remaining = round($requestedQty, 2);
        $allocated = [];

        $batches = InventoryBatch::withoutGlobalScopes()
            ->where('tenant_id', $tenantId)
            ->where('inventory_product_id', $productId)
            ->where('quantity_available', '>', 0)
            ->orderByRaw("COALESCE(expiry_date, '9999-12-31') asc")
            ->orderBy('id')
            ->get();

        foreach ($batches as $batch) {
            if ($remaining <= 0) {
                break;
            }

            $available = (float) $batch->quantity_available;
            if ($available <= 0) {
                continue;
            }

            $take = min($available, $remaining);
            if ($take <= 0) {
                continue;
            }

            $batch->update([
                'quantity_available' => round($available - $take, 2),
            ]);

            $allocated[] = [
                'batch' => $batch,
                'qty' => round($take, 2),
            ];

            $remaining = round($remaining - $take, 2);
        }

        return $allocated;
    }

    private function firstInMovement(string $tenantId, int $productId, int $batchId, float $qty, float $unitCost, string $date): void
    {
        InventoryMovement::withoutGlobalScopes()->firstOrCreate(
            [
                'tenant_id' => $tenantId,
                'inventory_product_id' => $productId,
                'inventory_batch_id' => $batchId,
                'movement_type' => 'in',
                'quantity' => $qty,
                'movement_date' => $date,
            ],
            [
                'unit_cost' => $unitCost,
                'total_cost' => round($unitCost * $qty, 2),
                'reference_type' => 'warehouse_demo_seed',
            ]
        );
    }

    private function firstOutMovement(string $tenantId, int $productId, int $batchId, float $qty, float $unitCost, string $date, string $reference): void
    {
        InventoryMovement::withoutGlobalScopes()->firstOrCreate(
            [
                'tenant_id' => $tenantId,
                'inventory_product_id' => $productId,
                'inventory_batch_id' => $batchId,
                'movement_type' => 'out',
                'quantity' => $qty,
                'movement_date' => $date,
                'reference_type' => $reference,
            ],
            [
                'unit_cost' => $unitCost,
                'total_cost' => round($unitCost * $qty, 2),
            ]
        );
    }
}
