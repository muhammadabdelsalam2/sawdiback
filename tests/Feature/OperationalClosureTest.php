<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class OperationalClosureTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Tenant $tenant;
    private array $createdAttachmentPaths = [];

    protected function setUp(): void
    {
        parent::setUp();

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $role = Role::create(['name' => 'SuperAdmin', 'guard_name' => 'web']);
        foreach (['finance.view', 'warehouse.view', 'warehouse.manage', 'analytics.view'] as $permission) {
            Permission::create(['name' => $permission, 'guard_name' => 'web']);
        }
        $role->givePermissionTo(['finance.view', 'warehouse.view', 'warehouse.manage', 'analytics.view']);

        $this->tenant = Tenant::query()->create([
            'name' => 'Operational Test Farm',
            'slug' => 'operational-test-farm',
            'status' => 'active',
        ]);

        $this->user = User::factory()->create(['tenant_id' => $this->tenant->id]);
        $this->user->assignRole('SuperAdmin');
    }

    protected function tearDown(): void
    {
        foreach ($this->createdAttachmentPaths as $path) {
            @unlink(storage_path('app/public/' . $path));
        }

        parent::tearDown();
    }

    public function test_finance_report_shows_the_five_required_metrics_from_real_tables(): void
    {
        $this->seedOperationalData();

        $this->actingAs($this->user)
            ->get('/en-SA/finance/profit-loss')
            ->assertOk()
            ->assertSee('Profit by Department')
            ->assertSee('Poultry')
            ->assertSee('Crops')
            ->assertSee('Livestock')
            ->assertSee('Best Product')
            ->assertSee('Premium Feed')
            ->assertSee('Highest Cost Item')
            ->assertSee('Farm Mortality Rate')
            ->assertSee('Employee Performance')
            ->assertSee('Attendance Rate');
    }

    public function test_staff_performance_attendance_rate_uses_employee_day_capacity_for_multi_day_period(): void
    {
        $employeeIds = [];
        foreach (['Attendance Worker One', 'Attendance Worker Two'] as $index => $name) {
            $employeeIds[] = DB::table('employees')->insertGetId([
                'tenant_id' => $this->tenant->id,
                'full_name' => $name,
                'worker_number' => 'MW-' . ($index + 1),
                'profession' => 'Technician',
                'employment_status' => 'active',
                'operational_department' => 'livestock',
                'hire_date' => '2026-07-01',
                'salary' => 2500,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        foreach (range(1, 5) as $day) {
            DB::table('attendances')->insert([
                'tenant_id' => $this->tenant->id,
                'employee_id' => $employeeIds[0],
                'day' => "2026-07-0{$day}",
                'check_in_at' => "2026-07-0{$day} 08:00:00",
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $report = app(\App\Services\Finance\ProfitLossService::class)->report((string) $this->tenant->id, [
            'date_from' => '2026-07-01',
            'date_to' => '2026-07-05',
        ]);

        $row = collect($report['staff_performance'])->firstWhere('department', 'livestock');

        $this->assertSame(2, $row['employee_count']);
        $this->assertSame(5, $row['attendance_count']);
        $this->assertSame(50.0, $row['attendance_rate']);
        $this->assertGreaterThanOrEqual(0, $row['attendance_rate']);
        $this->assertLessThanOrEqual(100, $row['attendance_rate']);
    }

    public function test_warehouse_asset_crud_stores_attachment_and_links_to_farm(): void
    {
        $farmId = DB::table('farms')->insertGetId([
            'tenant_id' => $this->tenant->id,
            'name' => 'Storage Farm',
            'type' => 'owned',
            'location' => 'North Yard',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($this->user)
            ->post('/en-SA/warehouse-assets', [
                'name' => 'Water Pipe Set',
                'type' => 'water_pipes',
                'storage_location' => 'Warehouse A',
                'quantity_or_status' => '24 pipes',
                'farm_id' => $farmId,
                'attachments' => [
                    UploadedFile::fake()->create('asset.pdf', 50, 'application/pdf'),
                ],
            ])
            ->assertRedirect('/en-SA/warehouse-assets');

        $this->assertDatabaseHas('warehouse_assets', [
            'tenant_id' => $this->tenant->id,
            'farm_id' => $farmId,
            'name' => 'Water Pipe Set',
            'type' => 'water_pipes',
            'storage_location' => 'Warehouse A',
            'quantity_or_status' => '24 pipes',
        ]);

        $path = DB::table('warehouse_asset_attachments')->value('path');
        $this->createdAttachmentPaths[] = $path;
        $this->assertFileExists(storage_path('app/public/' . $path));

        $this->actingAs($this->user)
            ->get('/en-SA/warehouse-assets')
            ->assertOk()
            ->assertSee('Water Pipe Set')
            ->assertSee('Storage Farm')
            ->assertSee('asset.pdf');
    }

    public function test_analytics_dashboard_uses_real_sales_and_percentage_margins(): void
    {
        $this->seedOperationalData();

        $this->actingAs($this->user)
            ->get('/en-SA/analytics')
            ->assertOk()
            ->assertSee('Best Customer')
            ->assertSee('Top Buyer')
            ->assertSee('Best Product')
            ->assertSee('Premium Feed')
            ->assertSee('Highest Margin')
            ->assertSee('Lowest Margin');
    }

    public function test_customer_dashboard_widgets_are_database_driven_and_do_not_show_maintenance_mock(): void
    {
        $this->seedOperationalData();

        $this->actingAs($this->user)
            ->get('/en-SA/dashboard')
            ->assertOk()
            ->assertSee('Dashboard Overview')
            ->assertSee('Active Sales Orders')
            ->assertSee('ORD-001')
            ->assertDontSee('Machinery Maintenance')
            ->assertDontSee('Poltery')
            ->assertDontSee('Tractor T-5');
    }

    private function seedOperationalData(): void
    {
        $farmId = DB::table('farms')->insertGetId([
            'tenant_id' => $this->tenant->id,
            'name' => 'Main Farm',
            'type' => 'owned',
            'location' => 'Main',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $penId = DB::table('farm_pens')->insertGetId([
            'tenant_id' => $this->tenant->id,
            'farm_id' => $farmId,
            'pen_number' => 'L-1',
            'type' => 'mixed',
            'capacity' => 20,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $speciesId = DB::table('animal_species')->insertGetId([
            'tenant_id' => $this->tenant->id,
            'code' => 'cow',
            'name' => 'Cow',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $animalId = DB::table('livestock_animals')->insertGetId([
            'tenant_id' => $this->tenant->id,
            'pen_id' => $penId,
            'tag_number' => 'A-1',
            'species_id' => $speciesId,
            'gender' => 'female',
            'source_type' => 'purchased',
            'status' => 'active',
            'health_status' => 'healthy',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('livestock_animals')->insert([
            'tenant_id' => $this->tenant->id,
            'pen_id' => $penId,
            'tag_number' => 'A-2',
            'species_id' => $speciesId,
            'gender' => 'male',
            'source_type' => 'purchased',
            'status' => 'dead',
            'health_status' => 'healthy',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('livestock_pen_financial_entries')->insert([
            'tenant_id' => $this->tenant->id,
            'pen_id' => $penId,
            'type' => 'sale',
            'amount' => 1000,
            'entry_date' => now()->toDateString(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('animal_feeding_logs')->insert([
            'tenant_id' => $this->tenant->id,
            'animal_id' => $animalId,
            'feed_type_id' => DB::table('feed_types')->insertGetId([
                'tenant_id' => $this->tenant->id,
                'name' => 'Corn',
                'category' => 'concentrate',
                'unit' => 'kg',
                'created_at' => now(),
                'updated_at' => now(),
            ]),
            'feeding_date' => now()->toDateString(),
            'quantity' => 10,
            'unit_cost' => 2,
            'total_cost' => 20,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('poultry_broiler_cycles')->insert([
            'tenant_id' => $this->tenant->id,
            'pen_id' => $penId,
            'cycle_number' => 'B-1',
            'chick_count' => 100,
            'started_at' => now()->subDays(10)->toDateString(),
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $cycleId = DB::table('poultry_broiler_cycles')->where('cycle_number', 'B-1')->value('id');
        DB::table('poultry_broiler_sales')->insert([
            'tenant_id' => $this->tenant->id,
            'broiler_cycle_id' => $cycleId,
            'sale_date' => now()->toDateString(),
            'quantity' => 20,
            'unit_price' => 15,
            'total_amount' => 300,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('poultry_broiler_costs')->insert([
            'tenant_id' => $this->tenant->id,
            'broiler_cycle_id' => $cycleId,
            'cost_type' => 'feed',
            'amount' => 80,
            'cost_date' => now()->toDateString(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('poultry_broiler_mortalities')->insert([
            'tenant_id' => $this->tenant->id,
            'broiler_cycle_id' => $cycleId,
            'mortality_date' => now()->toDateString(),
            'quantity' => 3,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('crops')->insert([
            'tenant_id' => $this->tenant->id,
            'farm_id' => $farmId,
            'name' => 'Tomato',
            'land_area' => 3,
            'planting_date' => now()->subDays(20)->toDateString(),
            'yield_tons' => 5,
            'wasted_tons' => 1,
            'available_for_feed_tons' => 0,
            'sale_price_per_ton' => 120,
            'water_cost' => 40,
            'labor_cost' => 60,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $cropId = DB::table('crops')->where('name', 'Tomato')->value('id');
        DB::table('crop_cost_items')->insert([
            'tenant_id' => $this->tenant->id,
            'crop_id' => $cropId,
            'item' => 'Seeds',
            'amount' => 30,
            'cost_date' => now()->toDateString(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('crop_material_usages')->insert([
            'tenant_id' => $this->tenant->id,
            'crop_id' => $cropId,
            'material_type' => 'fertilizer',
            'name' => 'Fertilizer',
            'amount' => 25,
            'used_on' => now()->toDateString(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $productId = DB::table('inventory_products')->insertGetId([
            'tenant_id' => $this->tenant->id,
            'code' => 'P-1',
            'name' => 'Premium Feed',
            'category' => 'feed',
            'unit' => 'bag',
            'tax' => 0,
            'track_expiry' => false,
            'low_stock_threshold' => 0,
            'is_active' => true,
            'is_best_selling' => false,
            'price' => 200,
            'last_price' => 120,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $customerId = DB::table('sales_customers')->insertGetId([
            'tenant_id' => $this->tenant->id,
            'name' => 'Top Buyer',
            'type' => 'trader',
            'phones' => '0500000000',
            'address' => 'Riyadh',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $orderId = DB::table('sales_orders')->insertGetId([
            'tenant_id' => $this->tenant->id,
            'order_no' => 'ORD-001',
            'customer_id' => $customerId,
            'order_date' => now()->toDateString(),
            'status' => 'confirmed',
            'total' => 600,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('sales_order_items')->insert([
            'sales_order_id' => $orderId,
            'product_id' => $productId,
            'qty' => 3,
            'unit_price' => 200,
            'discount' => 0,
            'line_total' => 600,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $employeeId = DB::table('employees')->insertGetId([
            'tenant_id' => $this->tenant->id,
            'full_name' => 'Attendance Worker',
            'worker_number' => 'W-1',
            'profession' => 'Technician',
            'employment_status' => 'active',
            'operational_department' => 'livestock',
            'hire_date' => now()->subMonth()->toDateString(),
            'salary' => 2500,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('attendances')->insert([
            'tenant_id' => $this->tenant->id,
            'employee_id' => $employeeId,
            'day' => now()->toDateString(),
            'check_in_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
