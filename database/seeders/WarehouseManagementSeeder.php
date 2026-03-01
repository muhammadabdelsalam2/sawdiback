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

class WarehouseManagementSeeder extends Seeder
{
    public function run(): void
    {
        $tenants = Tenant::query()->get();
        if ($tenants->isEmpty()) {
            return;
        }

        foreach ($tenants as $tenant) {
            $tenantId = $tenant->id;
            $tenantCode = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', (string) $tenantId), 0, 6));
            $today = now()->toDateString();

            $feed = InventoryProduct::query()->updateOrCreate(
                ['tenant_id' => $tenantId, 'code' => 'WH-FEED-ALF'],
                [
                    'name' => 'Alfalfa Feed',
                    'category' => 'feed',
                    'unit' => 'kg',
                    'track_expiry' => false,
                    'low_stock_threshold' => 80,
                    'is_active' => true,
                ]
            );

            $medicine = InventoryProduct::query()->updateOrCreate(
                ['tenant_id' => $tenantId, 'code' => 'WH-VET-ANTI'],
                [
                    'name' => 'Antibiotic 100ml',
                    'category' => 'vet_medicine',
                    'unit' => 'bottle',
                    'track_expiry' => true,
                    'low_stock_threshold' => 20,
                    'is_active' => true,
                ]
            );

            $equipment = InventoryProduct::query()->updateOrCreate(
                ['tenant_id' => $tenantId, 'code' => 'WH-EQ-MILKER'],
                [
                    'name' => 'Milking Cluster Set',
                    'category' => 'equipment',
                    'unit' => 'piece',
                    'track_expiry' => false,
                    'low_stock_threshold' => 2,
                    'is_active' => true,
                ]
            );

            $milkProduct = InventoryProduct::query()->updateOrCreate(
                ['tenant_id' => $tenantId, 'code' => 'WH-AP-MILK'],
                [
                    'name' => 'Raw Milk',
                    'category' => 'animal_product',
                    'unit' => 'liter',
                    'track_expiry' => true,
                    'low_stock_threshold' => 200,
                    'is_active' => true,
                ]
            );

            $meatProduct = InventoryProduct::query()->updateOrCreate(
                ['tenant_id' => $tenantId, 'code' => 'WH-AP-MEAT'],
                [
                    'name' => 'Fresh Meat',
                    'category' => 'animal_product',
                    'unit' => 'kg',
                    'track_expiry' => true,
                    'low_stock_threshold' => 50,
                    'is_active' => true,
                ]
            );

            $feedBatch = InventoryBatch::query()->firstOrCreate(
                [
                    'tenant_id' => $tenantId,
                    'inventory_product_id' => $feed->id,
                    'batch_number' => 'FB-' . $tenantCode . '-001',
                ],
                [
                    'received_at' => now()->subDays(25)->toDateString(),
                    'quantity_initial' => 500,
                    'quantity_available' => 420,
                    'unit_cost' => 1.6,
                    'notes' => 'Initial feed stock',
                ]
            );

            $medicineBatch = InventoryBatch::query()->firstOrCreate(
                [
                    'tenant_id' => $tenantId,
                    'inventory_product_id' => $medicine->id,
                    'batch_number' => 'VM-' . $tenantCode . '-001',
                ],
                [
                    'production_date' => now()->subMonths(4)->toDateString(),
                    'expiry_date' => now()->addDays(18)->toDateString(),
                    'received_at' => now()->subMonths(2)->toDateString(),
                    'quantity_initial' => 60,
                    'quantity_available' => 12,
                    'unit_cost' => 45,
                    'notes' => 'Medicine near expiry for alerts',
                ]
            );

            $equipmentBatch = InventoryBatch::query()->firstOrCreate(
                [
                    'tenant_id' => $tenantId,
                    'inventory_product_id' => $equipment->id,
                    'batch_number' => 'EQ-' . $tenantCode . '-001',
                ],
                [
                    'received_at' => now()->subMonths(1)->toDateString(),
                    'quantity_initial' => 8,
                    'quantity_available' => 6,
                    'unit_cost' => 320,
                ]
            );

            $milkBatch = InventoryBatch::query()->firstOrCreate(
                [
                    'tenant_id' => $tenantId,
                    'inventory_product_id' => $milkProduct->id,
                    'batch_number' => 'MP-' . $tenantCode . '-001',
                ],
                [
                    'production_date' => now()->subDays(1)->toDateString(),
                    'expiry_date' => now()->addDays(5)->toDateString(),
                    'received_at' => now()->subDays(1)->toDateString(),
                    'quantity_initial' => 420,
                    'quantity_available' => 360,
                    'unit_cost' => 2.3,
                ]
            );

            $meatBatch = InventoryBatch::query()->firstOrCreate(
                [
                    'tenant_id' => $tenantId,
                    'inventory_product_id' => $meatProduct->id,
                    'batch_number' => 'MT-' . $tenantCode . '-001',
                ],
                [
                    'production_date' => now()->subDays(3)->toDateString(),
                    'expiry_date' => now()->addDays(7)->toDateString(),
                    'received_at' => now()->subDays(3)->toDateString(),
                    'quantity_initial' => 150,
                    'quantity_available' => 120,
                    'unit_cost' => 18.5,
                ]
            );

            $this->seedInMovement($tenantId, $feed->id, $feedBatch->id, 500, 1.6, now()->subDays(25)->toDateString(), 'seed');
            $this->seedInMovement($tenantId, $medicine->id, $medicineBatch->id, 60, 45, now()->subMonths(2)->toDateString(), 'seed');
            $this->seedInMovement($tenantId, $equipment->id, $equipmentBatch->id, 8, 320, now()->subMonths(1)->toDateString(), 'seed');
            $this->seedInMovement($tenantId, $milkProduct->id, $milkBatch->id, 420, 2.3, now()->subDays(1)->toDateString(), 'production');
            $this->seedInMovement($tenantId, $meatProduct->id, $meatBatch->id, 150, 18.5, now()->subDays(3)->toDateString(), 'production');

            $sourceAnimalId = LivestockAnimal::query()
                ->where('tenant_id', $tenantId)
                ->orderBy('id')
                ->value('id');

            InventoryProductionRecord::query()->firstOrCreate(
                [
                    'tenant_id' => $tenantId,
                    'inventory_product_id' => $milkProduct->id,
                    'production_date' => now()->subDays(1)->toDateString(),
                ],
                [
                    'livestock_animal_id' => $sourceAnimalId,
                    'quantity' => 420,
                    'unit_cost' => 2.3,
                    'notes' => 'Daily production seed',
                ]
            );

            InventoryProductionRecord::query()->firstOrCreate(
                [
                    'tenant_id' => $tenantId,
                    'inventory_product_id' => $meatProduct->id,
                    'production_date' => now()->subDays(3)->toDateString(),
                ],
                [
                    'livestock_animal_id' => $sourceAnimalId,
                    'quantity' => 150,
                    'unit_cost' => 18.5,
                    'notes' => 'Meat production seed',
                ]
            );

            $delivery = InventoryDelivery::query()->firstOrCreate(
                [
                    'tenant_id' => $tenantId,
                    'delivery_number' => 'DEL-' . $tenantCode . '-001',
                ],
                [
                    'customer_name' => 'Demo Customer',
                    'delivered_at' => now()->subHours(6),
                    'status' => 'delivered',
                    'notes' => 'Seeded delivery',
                ]
            );

            if ($delivery->items()->count() === 0) {
                $milkQty = 40.0;
                $meatQty = 20.0;

                InventoryDeliveryItem::query()->create([
                    'tenant_id' => $tenantId,
                    'inventory_delivery_id' => $delivery->id,
                    'inventory_product_id' => $milkProduct->id,
                    'inventory_batch_id' => $milkBatch->id,
                    'quantity' => $milkQty,
                    'unit_price' => 3.1,
                    'total_price' => round(3.1 * $milkQty, 2),
                ]);

                InventoryDeliveryItem::query()->create([
                    'tenant_id' => $tenantId,
                    'inventory_delivery_id' => $delivery->id,
                    'inventory_product_id' => $meatProduct->id,
                    'inventory_batch_id' => $meatBatch->id,
                    'quantity' => $meatQty,
                    'unit_price' => 25.0,
                    'total_price' => round(25.0 * $meatQty, 2),
                ]);

                $milkBatch->update([
                    'quantity_available' => max(0, round((float) $milkBatch->quantity_available - $milkQty, 2)),
                ]);
                $meatBatch->update([
                    'quantity_available' => max(0, round((float) $meatBatch->quantity_available - $meatQty, 2)),
                ]);

                InventoryMovement::query()->create([
                    'tenant_id' => $tenantId,
                    'inventory_product_id' => $milkProduct->id,
                    'inventory_batch_id' => $milkBatch->id,
                    'movement_type' => 'out',
                    'quantity' => $milkQty,
                    'unit_cost' => 2.3,
                    'total_cost' => round(2.3 * $milkQty, 2),
                    'movement_date' => $today,
                    'reference_type' => 'delivery_seed',
                    'reference_id' => $delivery->id,
                ]);

                InventoryMovement::query()->create([
                    'tenant_id' => $tenantId,
                    'inventory_product_id' => $meatProduct->id,
                    'inventory_batch_id' => $meatBatch->id,
                    'movement_type' => 'out',
                    'quantity' => $meatQty,
                    'unit_cost' => 18.5,
                    'total_cost' => round(18.5 * $meatQty, 2),
                    'movement_date' => $today,
                    'reference_type' => 'delivery_seed',
                    'reference_id' => $delivery->id,
                ]);
            }
        }
    }

    private function seedInMovement(
        string $tenantId,
        int $productId,
        int $batchId,
        float $quantity,
        float $unitCost,
        string $movementDate,
        string $referenceType
    ): void {
        InventoryMovement::query()->firstOrCreate(
            [
                'tenant_id' => $tenantId,
                'inventory_product_id' => $productId,
                'inventory_batch_id' => $batchId,
                'movement_type' => 'in',
                'quantity' => $quantity,
                'movement_date' => $movementDate,
                'reference_type' => $referenceType,
            ],
            [
                'unit_cost' => $unitCost,
                'total_cost' => round($unitCost * $quantity, 2),
            ]
        );
    }
}

