<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use App\Models\WarehouseAssetAttachment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class WarehouseAssetsAndSuperAdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $tenant;
    private User $user;
    private array $createdAttachmentPaths = [];

    protected function tearDown(): void
    {
        foreach ($this->createdAttachmentPaths as $path) {
            @unlink(storage_path('app/public/' . $path));
        }

        parent::tearDown();
    }

    protected function setUp(): void
    {
        parent::setUp();

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $role = Role::create(['name' => 'SuperAdmin', 'guard_name' => 'web']);
        foreach (['warehouse.view', 'warehouse.manage'] as $permission) {
            Permission::create(['name' => $permission, 'guard_name' => 'web']);
        }
        $role->givePermissionTo(['warehouse.view', 'warehouse.manage']);

        $this->tenant = Tenant::query()->create([
            'name' => 'Warehouse Test Farm',
            'slug' => 'warehouse-test-farm',
            'status' => 'active',
        ]);

        $this->user = User::factory()->create(['tenant_id' => $this->tenant->id]);
        $this->user->assignRole('SuperAdmin');
    }

    public function test_warehouse_asset_store_update_and_validation_are_visible(): void
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
            ->from('/ar-SA/warehouse-assets/create')
            ->post('/ar-SA/warehouse-assets', [
                'name' => '',
                'type' => 'equipment',
            ])
            ->assertRedirect('/ar-SA/warehouse-assets/create')
            ->assertSessionHasErrors('name');

        $this->actingAs($this->user)
            ->withSession(['errors' => session('errors')])
            ->get('/ar-SA/warehouse-assets/create')
            ->assertOk()
            ->assertSee('is-invalid', false);

        $this->actingAs($this->user)
            ->post('/ar-SA/warehouse-assets', [
                'name' => 'Main Pump',
                'type' => 'equipment',
                'storage_location' => 'Warehouse A',
                'quantity_or_status' => 'Ready',
                'farm_id' => $farmId,
                'notes' => 'Working asset',
                'attachments' => [
                    UploadedFile::fake()->create('pump-manual.pdf', 120, 'application/pdf'),
                ],
            ])
            ->assertRedirect('/ar-SA/warehouse-assets');

        $assetId = DB::table('warehouse_assets')->where('name', 'Main Pump')->value('id');
        $this->assertNotNull($assetId);

        $this->assertDatabaseHas('warehouse_assets', [
            'id' => $assetId,
            'tenant_id' => $this->tenant->id,
            'farm_id' => $farmId,
            'type' => 'equipment',
        ]);

        $attachment = WarehouseAssetAttachment::query()
            ->where('warehouse_asset_id', $assetId)
            ->firstOrFail();
        $this->createdAttachmentPaths[] = $attachment->path;
        $this->assertFileExists(storage_path('app/public/' . $attachment->path));
        $this->assertStringContainsString('/files/public/warehouse/assets/', $attachment->url);
        $this->assertStringNotContainsString('/storage/', $attachment->url);
        $this->get($attachment->url)->assertOk();

        $this->actingAs($this->user)
            ->put("/ar-SA/warehouse-assets/{$assetId}", [
                'name' => 'Main Pump Updated',
                'type' => 'water_pipes',
                'storage_location' => 'Warehouse B',
                'quantity_or_status' => '12 pcs',
                'farm_id' => $farmId,
            ])
            ->assertRedirect('/ar-SA/warehouse-assets');

        $this->assertDatabaseHas('warehouse_assets', [
            'id' => $assetId,
            'name' => 'Main Pump Updated',
            'type' => 'water_pipes',
            'storage_location' => 'Warehouse B',
        ]);
    }

    public function test_superadmin_dashboard_shows_total_farms_and_farm_summary_cards(): void
    {
        foreach (['Sweihan', 'Al Hayer', 'Rent Farm', 'Owned Farm'] as $index => $name) {
            DB::table('farms')->insert([
                'tenant_id' => $this->tenant->id,
                'name' => $name,
                'type' => $index === 2 ? 'rented' : 'owned',
                'location' => 'Location ' . ($index + 1),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->actingAs($this->user)
            ->get('/en-SA/superadmin/dashboard')
            ->assertOk()
            ->assertSee('Total Farms')
            ->assertSee('Farm Summary')
            ->assertSee('Sweihan')
            ->assertSee('Al Hayer')
            ->assertSee('4');
    }
}
