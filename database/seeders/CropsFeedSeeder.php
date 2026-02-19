<?php

namespace Database\Seeders;

use App\Models\Crop;
use App\Models\CropCostItem;
use App\Models\CropGrowthStage;
use App\Models\FeedConsumption;
use App\Models\FeedStockMovement;
use App\Models\FeedType;
use App\Models\Tenant; 
use Illuminate\Database\Seeder;

class CropsFeedSeeder extends Seeder
{
    public function run(): void
    {
        $tenantId = Tenant::query()->value('id'); // أول tenant
        if (!$tenantId) {
            return;
        }

        $crop = Crop::query()->firstOrCreate(
            [
                'tenant_id' => $tenantId,
                'name' => 'Alfalfa',
            ],
            [
                'land_area' => 25,
                'planting_date' => now()->subMonths(3)->toDateString(),
                'yield_tons' => 40,
                'available_for_feed_tons' => 15,
                'sale_price_per_ton' => 320,
            ]
        );

        CropGrowthStage::query()->firstOrCreate([
            'tenant_id' => $tenantId,
            'crop_id' => $crop->id,
            'stage_name' => 'Vegetative',
            'recorded_on' => now()->subMonths(2)->toDateString(),
        ]);

        CropCostItem::query()->firstOrCreate([
            'tenant_id' => $tenantId,
            'crop_id' => $crop->id,
            'item' => 'Seeds',
            'amount' => 1200,
            'cost_date' => now()->subMonths(3)->toDateString(),
        ]);

        $feedType = FeedType::query()
            ->where('tenant_id', $tenantId) // لو feed_types فيها tenant_id
            ->first();

        if (!$feedType) {
            return;
        }

        FeedStockMovement::query()->firstOrCreate([
            'tenant_id' => $tenantId,
            'feed_type_id' => $feedType->id,
            'movement_type' => 'in',
            'quantity' => 10,
            'movement_date' => now()->startOfMonth()->toDateString(),
            'source_type' => 'seed',
            'source_id' => 1,
        ], [
            'unit_cost' => $feedType->cost_per_unit,
            'total_cost' => (float) $feedType->cost_per_unit * 10,
        ]);

        FeedConsumption::query()->firstOrCreate([
            'tenant_id' => $tenantId,
            'feed_type_id' => $feedType->id,
            'consumption_date' => now()->subDays(2)->toDateString(),
            'quantity' => 1.5,
            'group_name' => 'Heifers',
        ], [
            'unit_cost' => $feedType->cost_per_unit,
            'total_cost' => (float) $feedType->cost_per_unit * 1.5,
        ]);
    }
}
