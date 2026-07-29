<?php

namespace Tests\Feature;

use App\Models\InventoryProduct;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class InventoryProductImagesTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $tenant;
    private User $user;
    private int $categoryId;
    private int $farmId;

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
            'name' => 'Inventory Images Tenant',
            'slug' => 'inventory-images-tenant',
            'status' => 'active',
        ]);

        $this->user = User::factory()->create(['tenant_id' => $this->tenant->id]);
        $this->user->assignRole('SuperAdmin');

        $this->categoryId = DB::table('categories')->insertGetId([
            'tenant_id' => $this->tenant->id,
            'parent_id' => null,
            'code' => 'feed',
            'sort_order' => 1,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('category_translations')->insert([
            'category_id' => $this->categoryId,
            'locale' => 'en',
            'name' => 'Feed',
            'slug' => 'feed-images-test',
            'description' => 'Feed category',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->farmId = DB::table('farms')->insertGetId([
            'tenant_id' => $this->tenant->id,
            'name' => 'Image Farm',
            'type' => 'owned',
            'location' => 'Storage North',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function test_inventory_product_uploaded_image_is_visible_on_list_and_edit_pages(): void
    {
        $this->actingAs($this->user)
            ->post('/en-SA/inventory/products', $this->payload([
                'image' => $this->uploadedImage('corn.png'),
            ]))
            ->assertRedirect('/en-SA/inventory/products');

        $product = InventoryProduct::query()->where('name', 'Corn Mix')->firstOrFail();

        $this->assertNotNull($product->image);
        $initialImage = $product->image;
        $this->assertFileExists(storage_path('app/public/' . $product->image));
        $this->assertStringContainsString('/storage/inventory/products/', $product->image_url);
        $this->assertStringNotContainsString('ui-avatars.com', $product->image_url);

        $this->actingAs($this->user)
            ->get('/en-SA/inventory/products')
            ->assertOk()
            ->assertSee($product->image_url, false);

        $this->actingAs($this->user)
            ->get("/en-SA/inventory/products/{$product->id}/edit")
            ->assertOk()
            ->assertSee($product->image_url, false);

        $this->actingAs($this->user)
            ->get('/en-SA/inventory/products')
            ->assertOk()
            ->assertSee($product->image_url, false)
            ->assertSee('Farm Location');

        $this->actingAs($this->user)
            ->put("/en-SA/inventory/products/{$product->id}", $this->payload([
                'name' => 'Corn Mix Updated',
                'image' => $this->uploadedImage('corn-new.png'),
            ]))
            ->assertRedirect('/en-SA/inventory/products');

        $product->refresh();

        $this->assertFileExists(storage_path('app/public/' . $product->image));
        $this->assertStringContainsString('/storage/inventory/products/', $product->image_url);

        $this->actingAs($this->user)
            ->get("/en-SA/inventory/products/{$product->id}/edit")
            ->assertOk()
            ->assertSee($product->image_url, false);

        @unlink(storage_path('app/public/' . $initialImage));
        @unlink(storage_path('app/public/' . $product->image));
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'code' => 'CORN-MIX',
            'name' => 'Corn Mix',
            'title_ar' => 'خلطة ذرة',
            'title_en' => 'Corn Mix',
            'category_id' => $this->categoryId,
            'asset_category' => 'feed',
            'farm_location' => 'Silo 2',
            'farm_id' => $this->farmId,
            'unit' => 'kg',
            'tax' => 0,
            'track_expiry' => 1,
            'low_stock_threshold' => 5,
            'is_active' => 1,
            'is_best_selling' => 0,
            'notes' => 'Image test product',
            'description_ar' => 'منتج اختبار',
            'description_en' => 'Test product',
            'price' => 10,
            'last_price' => 9,
        ], $overrides);
    }

    private function uploadedImage(string $name): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'inventory-product-image-');

        file_put_contents(
            $path,
            base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/p9sAAAAASUVORK5CYII=')
        );

        return new UploadedFile($path, $name, 'image/png', null, true);
    }
}
