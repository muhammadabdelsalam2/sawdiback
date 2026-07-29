<?php

namespace Tests\Feature;

use App\Models\AnimalSpecies;
use App\Models\Crop;
use App\Models\Farm;
use App\Models\FarmPen;
use App\Models\LivestockAnimal;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class RequirementFieldsTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $tenant;
    private User $user;
    private Farm $farm;

    protected function setUp(): void
    {
        parent::setUp();

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $role = Role::create(['name' => 'SuperAdmin', 'guard_name' => 'web']);
        foreach (['animals.view', 'crops.view'] as $permission) {
            Permission::create(['name' => $permission, 'guard_name' => 'web']);
        }
        $role->givePermissionTo(['animals.view', 'crops.view']);

        $this->tenant = Tenant::query()->create([
            'name' => 'Requirement Fields Tenant',
            'slug' => 'requirement-fields-tenant',
            'status' => 'active',
        ]);

        $this->user = User::factory()->create(['tenant_id' => $this->tenant->id]);
        $this->user->assignRole('SuperAdmin');

        $this->farm = Farm::query()->create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Sweihan',
            'type' => 'owned',
            'is_active' => true,
        ]);
    }

    public function test_livestock_animal_endpoint_stores_intended_purpose(): void
    {
        $species = AnimalSpecies::query()->create([
            'tenant_id' => $this->tenant->id,
            'code' => 'GOAT',
            'name' => 'Goat',
        ]);

        $pen = FarmPen::query()->create([
            'tenant_id' => $this->tenant->id,
            'farm_id' => $this->farm->id,
            'pen_number' => 'P-01',
            'type' => 'livestock',
        ]);

        $this->actingAs($this->user)
            ->post('/en-SA/livestock/animals', [
                'tag_number' => 'GOAT-001',
                'pen_id' => $pen->id,
                'species_id' => $species->id,
                'gender' => 'female',
                'source_type' => 'purchased',
                'purchase_date' => '2026-07-01',
                'purchase_price' => 1200,
                'status' => 'active',
                'health_status' => 'healthy',
                'intended_purpose' => 'milk',
            ])
            ->assertRedirect();

        $animal = LivestockAnimal::query()->where('tag_number', 'GOAT-001')->firstOrFail();

        $this->assertSame('milk', $animal->intended_purpose);

        $this->actingAs($this->user)
            ->get('/en-SA/livestock/animals/' . $animal->id)
            ->assertOk()
            ->assertSee('Intended Purpose')
            ->assertSee('Milk');
    }

    public function test_crop_endpoint_stores_greenhouse_number(): void
    {
        $this->actingAs($this->user)
            ->post('/en-SA/crops-feed/crops', [
                'farm_id' => $this->farm->id,
                'name' => 'Tomato',
                'land_area' => 150,
                'greenhouse_type' => 'Tunnel',
                'greenhouse_number' => 'GH-07',
                'greenhouse_location' => 'North Field',
                'irrigation_type' => 'ground',
                'planting_date' => '2026-07-10',
                'expected_harvest_date' => '2026-09-10',
                'yield_tons' => 4,
                'wasted_tons' => 0.5,
                'available_for_feed_tons' => 0,
                'sale_price_per_ton' => 900,
                'water_cost' => 150,
                'labor_cost' => 300,
            ])
            ->assertRedirect();

        $crop = Crop::query()->where('name', 'Tomato')->firstOrFail();

        $this->assertSame('GH-07', $crop->greenhouse_number);

        $this->actingAs($this->user)
            ->get('/en-SA/crops-feed/crops/' . $crop->id)
            ->assertOk()
            ->assertSee('Greenhouse Number')
            ->assertSee('GH-07');
    }
}
