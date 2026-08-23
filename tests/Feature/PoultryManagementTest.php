<?php

namespace Tests\Feature;

use App\Models\Farm;
use App\Models\FarmPen;
use App\Models\Poultry\PoultryBroilerCycle;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class PoultryManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Tenant $tenant;

    protected function setUp(): void
    {
        parent::setUp();

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $role = Role::create(['name' => 'Customer', 'guard_name' => 'web']);
        Permission::create(['name' => 'poultry.view', 'guard_name' => 'web']);
        Permission::create(['name' => 'poultry.manage', 'guard_name' => 'web']);
        $role->givePermissionTo(['poultry.view', 'poultry.manage']);

        $this->tenant = Tenant::query()->create([
            'name' => 'Test Farm',
            'slug' => 'test-farm',
            'status' => 'active',
        ]);

        $this->user = User::factory()->create(['tenant_id' => $this->tenant->id]);
        $this->user->assignRole('Customer');
    }

    public function test_customer_can_create_broiler_cycle_from_dashboard(): void
    {
        $response = $this->actingAs($this->user)->post('/en-SA/poultry/broiler-cycles', [
            'cycle_number' => 'BR-001',
            'chick_count' => 100,
            'started_at' => now()->subDays(10)->toDateString(),
            'status' => 'active',
        ]);

        $cycle = PoultryBroilerCycle::query()->withoutGlobalScopes()->where('cycle_number', 'BR-001')->firstOrFail();

        $response->assertRedirect('/en-SA/poultry/broiler-cycles/' . $cycle->id);
        $this->assertSame($this->user->tenant_id, $cycle->tenant_id);
    }

    public function test_poultry_forms_show_all_tenant_pens_for_selection(): void
    {
        $farm = Farm::query()->create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Suwaihan',
            'type' => 'owned',
        ]);

        FarmPen::query()->create([
            'tenant_id' => $this->tenant->id,
            'farm_id' => $farm->id,
            'pen_number' => '881',
            'type' => 'livestock',
        ]);

        foreach ([
            '/en-SA/poultry/broiler-cycles/create',
            '/en-SA/poultry/layer-flocks/create',
            '/en-SA/poultry/chicken-breeds/create',
        ] as $path) {
            $this->actingAs($this->user)
                ->get($path)
                ->assertOk()
                ->assertSee('Suwaihan')
                ->assertSee('881');
        }
    }

    public function test_broiler_cycle_calculates_profit_and_mortality_rate(): void
    {
        $cycle = PoultryBroilerCycle::query()->create([
            'tenant_id' => $this->tenant->id,
            'cycle_number' => 'BR-002',
            'chick_count' => 100,
            'started_at' => now()->subDays(7)->toDateString(),
            'status' => 'active',
        ]);

        $this->actingAs($this->user)->post("/en-SA/poultry/broiler-cycles/{$cycle->id}/mortalities", [
            'mortality_date' => now()->toDateString(),
            'quantity' => 5,
        ])->assertRedirect();

        $this->actingAs($this->user)->post("/en-SA/poultry/broiler-cycles/{$cycle->id}/sales", [
            'sale_date' => now()->toDateString(),
            'quantity' => 10,
            'unit_price' => 20,
        ])->assertRedirect();

        $this->actingAs($this->user)->post("/en-SA/poultry/broiler-cycles/{$cycle->id}/costs", [
            'cost_type' => 'feed',
            'amount' => 50,
            'cost_date' => now()->toDateString(),
        ])->assertRedirect();

        $cycle->refresh()->load(['mortalities', 'sales', 'costs']);

        $this->assertSame(5, $cycle->total_mortality);
        $this->assertSame('5.00', $cycle->mortality_rate);
        $this->assertSame('150.00', $cycle->net_profit);
    }
}
