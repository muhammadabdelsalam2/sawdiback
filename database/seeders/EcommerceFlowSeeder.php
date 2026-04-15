<?php

namespace Database\Seeders;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\InventoryMovement;
use App\Models\InventoryProduct;
use App\Models\Order;
use App\Models\OrderReview;
use App\Models\OrderStatusHistory;
use App\Models\Tenant;
use App\Models\User;
use App\Models\UserAddress;
use App\Models\Wallet;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class EcommerceFlowSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::query()->first();

        if (!$tenant) {
            $tenant = Tenant::create([
                'id' => (string) Str::uuid(),
                'name' => 'Ecommerce Tenant',
                'slug' => Str::slug('Ecommerce Tenant'),
                'status' => 'active',
            ]);
        }

        $client = User::updateOrCreate(
            ['email' => 'client@elsawady.com'],
            [
                'name' => 'Mobile Client',
                'password' => Hash::make('password123'),
                'tenant_id' => $tenant->id,
            ]
        );
        $client->assignRole('Client');

        Wallet::firstOrCreate([
            'user_id' => $client->id,
        ], [
            'balance' => 250,
            'currency' => config('wallet.default_currency', 'AED'),
            'points_balance' => 120,
        ]);

        $address = UserAddress::firstOrCreate([
            'user_id' => $client->id,
            'label' => 'Home',
        ], [
            'type' => 'home',
            'recipient_name' => 'Mobile Client',
            'phone' => '+971000000000',
            'address_line_1' => '12 Main Street',
            'city' => 'Dubai',
            'country' => 'UAE',
            'is_default' => true,
            'latitude' => 25.2048,
            'longitude' => 55.2708,
        ]);

        $products = $this->seedProducts($tenant->id);

        $coupon = Coupon::firstOrCreate([
            'code' => 'WELCOME10',
        ], [
            'tenant_id' => $tenant->id,
            'type' => 'percent',
            'value' => 10,
            'min_subtotal' => 50,
            'is_active' => true,
        ]);

        $cart = Cart::firstOrCreate([
            'user_id' => $client->id,
        ], [
            'tenant_id' => $tenant->id,
            'user_address_id' => $address->id,
            'weekly_delivery' => false,
        ]);

        $cart->items()->delete();

        foreach ($products as $product) {
            $unitPrice = (float) ($product->last_price ?? 20);
            $unitTax = (float) ($product->tax ?? 0);
            $lineSubtotal = round($unitPrice * 2, 2);
            $lineTax = round($unitTax * 2, 2);
            $lineTotal = round($lineSubtotal + $lineTax, 2);

            $cart->items()->create([
                'inventory_product_id' => $product->id,
                'quantity' => 2,
                'unit_price' => $unitPrice,
                'unit_tax' => $unitTax,
                'line_subtotal' => $lineSubtotal,
                'line_tax' => $lineTax,
                'line_total' => $lineTotal,
            ]);
        }

        $cartSubtotal = (float) $cart->items()->sum('line_subtotal');
        $cartTaxes = (float) $cart->items()->sum('line_tax');
        $cartDiscount = round($cartSubtotal * 0.1, 2);
        $cartShipping = (float) config('ecommerce.shipping_fee', 15);
        $cartTotal = round($cartSubtotal + $cartShipping + $cartTaxes - $cartDiscount, 2);

        $cart->update([
            'coupon_code' => $coupon->code,
            'subtotal' => $cartSubtotal,
            'shipping' => $cartShipping,
            'taxes' => $cartTaxes,
            'vat' => $cartTaxes,
            'discount' => $cartDiscount,
            'total' => $cartTotal,
        ]);

        $activeOrder = $this->seedOrder($client, $address, $products, Order::STATUS_OUT_FOR_DELIVERY);
        $deliveredOrder = $this->seedOrder($client, $address, $products, Order::STATUS_DELIVERED);

        OrderReview::firstOrCreate([
            'order_id' => $deliveredOrder->id,
            'user_id' => $client->id,
        ], [
            'rating' => 5,
            'title' => 'Great experience',
            'review' => 'Everything arrived fresh and on time.',
            'reasons' => ['Fast delivery', 'Excellent quality'],
            'submitted_at' => now(),
        ]);
    }

    private function seedProducts(string $tenantId): array
    {
        $rows = [
            ['code' => 'ECOM-MILK-01', 'name' => 'Fresh Milk', 'category' => 'animal_product', 'unit' => 'liter', 'price' => 9.5, 'tax' => 0],
            ['code' => 'ECOM-MEAT-01', 'name' => 'Premium Meat', 'category' => 'animal_product', 'unit' => 'kg', 'price' => 38.0, 'tax' => 1.5],
            ['code' => 'ECOM-GHEE-01', 'name' => 'Organic Ghee', 'category' => 'animal_product', 'unit' => 'kg', 'price' => 22.0, 'tax' => 0],
        ];

        $products = [];

        foreach ($rows as $row) {
            $product = InventoryProduct::updateOrCreate(
                ['tenant_id' => $tenantId, 'code' => $row['code']],
                [
                    'name' => $row['name'],
                    'category' => $row['category'],
                    'unit' => $row['unit'],
                    'tax' => $row['tax'],
                    'track_expiry' => true,
                    'low_stock_threshold' => 10,
                    'is_active' => true,
                ]
            );

            InventoryMovement::create([
                'tenant_id' => $tenantId,
                'inventory_product_id' => $product->id,
                'movement_type' => 'in',
                'quantity' => 200,
                'unit_cost' => $row['price'],
                'total_cost' => $row['price'] * 200,
                'movement_date' => now()->subDays(2)->toDateString(),
                'reference_type' => 'ecommerce_seed',
            ]);

            $product->setAttribute('last_price', $row['price']);
            $products[] = $product;
        }

        return $products;
    }

    private function seedOrder(User $user, UserAddress $address, array $products, string $status): Order
    {
        $subtotal = 0.0;
        $taxes = 0.0;

        foreach ($products as $product) {
            $subtotal += (float) ($product->last_price ?? 20);
            $taxes += (float) ($product->tax ?? 0);
        }

        $subtotal = round($subtotal, 2);
        $taxes = round($taxes, 2);
        $shipping = (float) config('ecommerce.shipping_fee', 15);
        $discount = 0;
        $total = round($subtotal + $shipping + $taxes - $discount, 2);

        $order = Order::create([
            'tenant_id' => $user->tenant_id,
            'order_no' => 'ORD-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6)),
            'user_id' => $user->id,
            'user_address_id' => $address->id,
            'status' => $status,
            'payment_method' => 'cash',
            'payment_status' => 'pending',
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'taxes' => $taxes,
            'vat' => $taxes,
            'discount' => $discount,
            'total' => $total,
            'currency' => config('ecommerce.currency', 'AED'),
            'placed_at' => now()->subDays(1),
            'estimated_delivery_at' => now()->addDays(2),
        ]);

        foreach ($products as $product) {
            $unitPrice = (float) ($product->last_price ?? 20);
            $unitTax = (float) ($product->tax ?? 0);
            $lineSubtotal = round($unitPrice * 1, 2);
            $lineTax = round($unitTax * 1, 2);
            $lineTotal = round($lineSubtotal + $lineTax, 2);

            $order->items()->create([
                'inventory_product_id' => $product->id,
                'product_name' => $product->name,
                'product_code' => $product->code,
                'unit' => $product->unit,
                'quantity' => 1,
                'unit_price' => $unitPrice,
                'unit_tax' => $unitTax,
                'line_subtotal' => $lineSubtotal,
                'line_tax' => $lineTax,
                'line_total' => $lineTotal,
                'snapshot' => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'code' => $product->code,
                    'unit' => $product->unit,
                    'image' => $product->image_url ?? null,
                    'tax' => $unitTax,
                ],
            ]);
        }

        OrderStatusHistory::create([
            'order_id' => $order->id,
            'from_status' => null,
            'to_status' => Order::STATUS_PENDING,
            'changed_at' => now()->subDays(1),
            'changed_by' => $user->id,
            'notes' => 'Order created',
        ]);

        OrderStatusHistory::create([
            'order_id' => $order->id,
            'from_status' => Order::STATUS_PENDING,
            'to_status' => $status,
            'changed_at' => now()->subHours(4),
            'changed_by' => $user->id,
            'notes' => 'Status updated in seed',
        ]);

        return $order;
    }
}
