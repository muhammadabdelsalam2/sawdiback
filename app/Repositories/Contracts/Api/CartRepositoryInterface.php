<?php

namespace App\Repositories\Contracts\Api;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\InventoryProduct;
use App\Models\User;
use Illuminate\Support\Collection;

interface CartRepositoryInterface
{
    public function getOrCreateForUser(User $user): Cart;

    public function loadCartForUser(User $user): Cart;

    public function findItem(Cart $cart, int $productId): ?CartItem;

    public function upsertItem(Cart $cart, InventoryProduct $product, float $quantity, float $unitPrice, float $unitTax = 0): CartItem;

    public function updateItemQuantity(CartItem $item, float $quantity, float $unitPrice, float $unitTax = 0): CartItem;

    public function removeItem(CartItem $item): void;

    public function clearCart(Cart $cart): void;

    public function suggestedProducts(Cart $cart, int $limit): Collection;

    public function getProductsWithPricing(array $productIds): Collection;
}
