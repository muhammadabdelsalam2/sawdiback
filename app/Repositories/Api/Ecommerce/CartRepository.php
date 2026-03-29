<?php

namespace App\Repositories\Api\Ecommerce;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\InventoryProduct;
use App\Models\User;
use App\Repositories\Contracts\Api\CartRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class CartRepository implements CartRepositoryInterface
{
    public function getOrCreateForUser(User $user): Cart
    {
        $tenantId = $this->resolveTenantId($user);

        return Cart::query()->firstOrCreate(
            ['user_id' => $user->id],
            [
                'tenant_id' => $tenantId,
                'subtotal' => 0,
                'shipping' => 0,
                'taxes' => 0,
                'vat' => 0,
                'discount' => 0,
                'total' => 0,
                'weekly_delivery' => false,
            ]
        );
    }

    public function loadCartForUser(User $user): Cart
    {
        $cart = $this->getOrCreateForUser($user);

        return $cart->load(['items', 'items.product', 'address']);
    }

    public function findItem(Cart $cart, int $productId): ?CartItem
    {
        return $cart->items()->where('inventory_product_id', $productId)->first();
    }

    public function upsertItem(Cart $cart, InventoryProduct $product, float $quantity, float $unitPrice, float $unitTax = 0): CartItem
    {
        $item = $this->findItem($cart, $product->id);

        if ($item) {
            $newQty = round(((float) $item->quantity + $quantity), 2);
            return $this->updateItemQuantity($item, $newQty, $unitPrice, $unitTax);
        }

        $lineSubtotal = round($quantity * $unitPrice, 2);
        $lineTax = round($quantity * $unitTax, 2);

        return $cart->items()->create([
            'inventory_product_id' => $product->id,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'unit_tax' => $unitTax,
            'line_subtotal' => $lineSubtotal,
            'line_tax' => $lineTax,
            'line_total' => round($lineSubtotal + $lineTax, 2),
        ]);
    }

    public function updateItemQuantity(CartItem $item, float $quantity, float $unitPrice, float $unitTax = 0): CartItem
    {
        $lineSubtotal = round($quantity * $unitPrice, 2);
        $lineTax = round($quantity * $unitTax, 2);

        $item->update([
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'unit_tax' => $unitTax,
            'line_subtotal' => $lineSubtotal,
            'line_tax' => $lineTax,
            'line_total' => round($lineSubtotal + $lineTax, 2),
        ]);

        return $item->refresh();
    }

    public function removeItem(CartItem $item): void
    {
        $item->delete();
    }

    public function clearCart(Cart $cart): void
    {
        $cart->items()->delete();
    }

    public function suggestedProducts(Cart $cart, int $limit): Collection
    {
        $excluded = $cart->items()->pluck('inventory_product_id')->all();

        $query = InventoryProduct::query()->where('is_active', true);

        if (!empty($excluded)) {
            $query->whereNotIn('id', $excluded);
        }

        $this->applyStockCalculations($query);
        $this->applyLastPrice($query);

        return $query->inRandomOrder()->limit($limit)->get();
    }

    public function getProductsWithPricing(array $productIds): Collection
    {
        if (empty($productIds)) {
            return collect();
        }

        $query = InventoryProduct::query()->whereIn('id', $productIds);

        $this->applyStockCalculations($query);
        $this->applyLastPrice($query);

        return $query->get();
    }

    protected function applyStockCalculations(Builder $query): void
    {
        $query->withSum([
            'movements as total_in' => fn ($q) => $q->where('movement_type', 'in')
        ], 'quantity');

        $query->withSum([
            'movements as total_out' => fn ($q) => $q->where('movement_type', 'out')
        ], 'quantity');
    }

    protected function applyLastPrice(Builder $query): void
    {
        $query->addSelect([
            'last_price' => function ($subQuery) {
                $subQuery->select('unit_cost')
                    ->from('inventory_movements')
                    ->whereColumn('inventory_product_id', 'inventory_products.id')
                    ->whereNotNull('unit_cost')
                    ->latest('movement_date')
                    ->limit(1);
            }
        ]);
    }

    protected function resolveTenantId(User $user, bool $failIfMissing = true): ?string
    {
        $tenantId = $user->tenant_id ?: User::query()->whereKey($user->id)->value('tenant_id');

        if (!$tenantId && $failIfMissing) {
            throw ValidationException::withMessages([
                'tenant_id' => ['The authenticated user is not linked to any tenant.'],
            ]);
        }

        return $tenantId;
    }
}
