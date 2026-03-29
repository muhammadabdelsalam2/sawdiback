<?php

namespace App\Services\API\Ecommerce\Cart;

use App\Http\Resources\CartResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\UserAddressResource;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\InventoryProduct;
use App\Models\User;
use App\Repositories\Contracts\Api\CartRepositoryInterface;
use App\Repositories\Contracts\Api\CouponRepositoryInterface;
use App\Support\ServiceResult;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CartService
{
    public function __construct(
        protected CartRepositoryInterface $cartRepository,
        protected CouponRepositoryInterface $couponRepository
    ) {
    }

    public function getCart(User $user): array
    {
        $cart = $this->cartRepository->loadCartForUser($user);

        $this->refreshCartItemsPricing($cart);
        $totals = $this->recalculateTotals($cart, $user);

        $cart->refresh();

        $suggested = $this->cartRepository->suggestedProducts(
            $cart,
            (int) config('ecommerce.suggested_products_limit', 6)
        );

        $suggested->each(fn ($product) => $product->setAttribute('price', $product->last_price));

        return ServiceResult::success(
            data: [
                'cart' => new CartResource($cart->load(['items.product', 'address'])),
                'suggested_products' => ProductResource::collection($suggested),
                'totals' => $totals,
            ],
            message: __('ecommerce.cart.success'),
            code: 200
        );
    }

    public function addItem(User $user, InventoryProduct $product, float $quantity): array
    {
        return DB::transaction(function () use ($user, $product, $quantity) {
            $cart = $this->cartRepository->getOrCreateForUser($user);

            $productData = $this->getProductPricing($product->id);
            $available = (float) ($productData['available_quantity'] ?? 0);

            if ($quantity <= 0) {
                return ServiceResult::error('Quantity must be at least 1.', errors: ['quantity' => 'Invalid quantity'], code: 422);
            }

            if ($available > 0 && $quantity > $available) {
                return ServiceResult::error('Requested quantity is not available.', errors: ['quantity' => 'Insufficient stock'], code: 422);
            }

            $unitPrice = (float) ($productData['last_price'] ?? 0);
            $unitTax = (float) ($productData['tax'] ?? 0);

            $this->cartRepository->upsertItem($cart, $product, $quantity, $unitPrice, $unitTax);

            $cart = $this->cartRepository->loadCartForUser($user);
            $this->refreshCartItemsPricing($cart);
            $totals = $this->recalculateTotals($cart, $user);

            return ServiceResult::success(
                data: [
                    'cart' => new CartResource($cart->load(['items.product', 'address'])),
                    'totals' => $totals,
                ],
                message: __('ecommerce.cart.updated', ['resource' => 'cart']),
                code: 200
            );
        });
    }

    public function updateItemQuantity(User $user, CartItem $item, float $quantity): array
    {
        return DB::transaction(function () use ($user, $item, $quantity) {
            $cart = $this->cartRepository->loadCartForUser($user);

            if ($item->cart_id !== $cart->id) {
                return ServiceResult::error('Cart item not found.', code: 404);
            }

            if ($quantity <= 0) {
                $this->cartRepository->removeItem($item);
            } else {
                $productData = $this->getProductPricing($item->inventory_product_id);
                $available = (float) ($productData['available_quantity'] ?? 0);

                if ($available > 0 && $quantity > $available) {
                    return ServiceResult::error('Requested quantity is not available.', errors: ['quantity' => 'Insufficient stock'], code: 422);
                }

                $unitPrice = (float) ($productData['last_price'] ?? 0);
                $unitTax = (float) ($productData['tax'] ?? 0);
                $this->cartRepository->updateItemQuantity($item, $quantity, $unitPrice, $unitTax);
            }

            $cart = $this->cartRepository->loadCartForUser($user);
            $this->refreshCartItemsPricing($cart);
            $totals = $this->recalculateTotals($cart, $user);

            return ServiceResult::success(
                data: [
                    'cart' => new CartResource($cart->load(['items.product', 'address'])),
                    'totals' => $totals,
                ],
                message: __('ecommerce.cart.updated', ['resource' => 'cart']),
                code: 200
            );
        });
    }

    public function removeItem(User $user, CartItem $item): array
    {
        return DB::transaction(function () use ($user, $item) {
            $cart = $this->cartRepository->loadCartForUser($user);

            if ($item->cart_id !== $cart->id) {
                return ServiceResult::error('Cart item not found.', code: 404);
            }

            $this->cartRepository->removeItem($item);

            $cart = $this->cartRepository->loadCartForUser($user);
            $this->refreshCartItemsPricing($cart);
            $totals = $this->recalculateTotals($cart, $user);

            return ServiceResult::success(
                data: [
                    'cart' => new CartResource($cart->load(['items.product', 'address'])),
                    'totals' => $totals,
                ],
                message: __('ecommerce.cart.updated', ['resource' => 'cart']),
                code: 200
            );
        });
    }

    public function clearCart(User $user): array
    {
        return DB::transaction(function () use ($user) {
            $cart = $this->cartRepository->getOrCreateForUser($user);

            $this->cartRepository->clearCart($cart);
            $cart->update([
                'coupon_code' => null,
                'discount' => 0,
                'subtotal' => 0,
                'shipping' => 0,
                'taxes' => 0,
                'vat' => 0,
                'total' => 0,
            ]);

            $cart = $this->cartRepository->loadCartForUser($user);

            return ServiceResult::success(
                data: [
                    'cart' => new CartResource($cart->load(['items.product', 'address'])),
                    'totals' => $this->totalsFromCart($cart),
                ],
                message: 'Cart cleared successfully.',
                code: 200
            );
        });
    }

    public function applyCoupon(User $user, string $code): array
    {
        return DB::transaction(function () use ($user, $code) {
            $cart = $this->cartRepository->loadCartForUser($user);

            $coupon = $this->couponRepository->findValidCouponForUser($user, $code);

            if (!$coupon) {
                return ServiceResult::error('Invalid or expired coupon.', errors: ['coupon' => 'Invalid code'], code: 422);
            }

            $this->refreshCartItemsPricing($cart);
            try {
                $totals = $this->recalculateTotals($cart, $user, $coupon);
            } catch (ValidationException $e) {
                return ServiceResult::error('Coupon is not applicable.', errors: $e->errors(), code: 422);
            }

            $cart->update([
                'coupon_code' => strtoupper(trim($code)),
                'discount' => $totals['discount'],
                'subtotal' => $totals['subtotal'],
                'shipping' => $totals['shipping'],
                'taxes' => $totals['taxes'],
                'vat' => $totals['taxes'],
                'total' => $totals['total'],
            ]);

            $cart->refresh();

            return ServiceResult::success(
                data: [
                    'cart' => new CartResource($cart->load(['items.product', 'address'])),
                    'totals' => $this->totalsFromCart($cart),
                ],
                message: 'Coupon applied successfully.',
                code: 200
            );
        });
    }

    public function removeCoupon(User $user): array
    {
        return DB::transaction(function () use ($user) {
            $cart = $this->cartRepository->loadCartForUser($user);

            $cart->update([
                'coupon_code' => null,
                'discount' => 0,
            ]);

            $this->refreshCartItemsPricing($cart);
            $totals = $this->recalculateTotals($cart, $user, null);

            return ServiceResult::success(
                data: [
                    'cart' => new CartResource($cart->load(['items.product', 'address'])),
                    'totals' => $totals,
                ],
                message: 'Coupon removed successfully.',
                code: 200
            );
        });
    }

    public function toggleWeeklyDelivery(User $user, bool $enabled): array
    {
        $cart = $this->cartRepository->getOrCreateForUser($user);

        $cart->update([
            'weekly_delivery' => $enabled,
        ]);

        return ServiceResult::success(
            data: [
                'weekly_delivery' => (bool) $cart->weekly_delivery,
            ],
            message: 'Weekly delivery updated successfully.',
            code: 200
        );
    }

    public function setAddress(User $user, int $addressId): array
    {
        $cart = $this->cartRepository->getOrCreateForUser($user);

        $address = $user->addresses()->whereKey($addressId)->first();

        if (!$address) {
            return ServiceResult::error('Address not found.', code: 404);
        }

        $cart->update(['user_address_id' => $address->id]);

        return ServiceResult::success(
            data: [
                'address' => new UserAddressResource($address),
            ],
            message: 'Address selected successfully.',
            code: 200
        );
    }

    protected function refreshCartItemsPricing(Cart $cart): void
    {
        $items = $cart->items;

        if ($items->isEmpty()) {
            return;
        }

        $productIds = $items->pluck('inventory_product_id')->all();
        $products = $this->cartRepository->getProductsWithPricing($productIds)->keyBy('id');

        foreach ($items as $item) {
            $product = $products->get($item->inventory_product_id);

            if (!$product) {
                continue;
            }

            $available = (float) (($product->total_in ?? 0) - ($product->total_out ?? 0));
            $product->setAttribute('available_quantity', $available);

            $unitPrice = (float) ($product->last_price ?? 0);
            $unitTax = (float) ($product->tax ?? 0);

            $lineSubtotal = round($unitPrice * (float) $item->quantity, 2);
            $lineTax = round($unitTax * (float) $item->quantity, 2);
            $lineTotal = round($lineSubtotal + $lineTax, 2);

            if (
                (float) $item->unit_price !== $unitPrice
                || (float) $item->unit_tax !== $unitTax
                || (float) $item->line_subtotal !== $lineSubtotal
                || (float) $item->line_tax !== $lineTax
                || (float) $item->line_total !== $lineTotal
            ) {
                $item->update([
                    'unit_price' => $unitPrice,
                    'unit_tax' => $unitTax,
                    'line_subtotal' => $lineSubtotal,
                    'line_tax' => $lineTax,
                    'line_total' => $lineTotal,
                ]);
            }

            $item->setRelation('product', $product);
        }
    }

    protected function recalculateTotals(Cart $cart, User $user, ?\App\Models\Coupon $coupon = null): array
    {
        $subtotal = round($cart->items()->sum('line_subtotal'), 2);
        $taxes = round($cart->items()->sum('line_tax'), 2);

        $shipping = $subtotal > 0 ? (float) config('ecommerce.shipping_fee', 0) : 0;

        $discount = 0.0;

        if ($coupon) {
            if ($coupon->min_subtotal && $subtotal < (float) $coupon->min_subtotal) {
                throw ValidationException::withMessages([
                    'coupon' => ['Cart subtotal does not meet the minimum required for this coupon.'],
                ]);
            }

            if ($coupon->type === 'percent') {
                $discount = round($subtotal * ((float) $coupon->value / 100), 2);
            } else {
                $discount = round((float) $coupon->value, 2);
            }

            if ($coupon->max_discount && $discount > (float) $coupon->max_discount) {
                $discount = (float) $coupon->max_discount;
            }
        }

        $total = max(0, round($subtotal + $shipping + $taxes - $discount, 2));

        $cart->update([
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'taxes' => $taxes,
            'vat' => $taxes,
            'discount' => $discount,
            'total' => $total,
        ]);

        return [
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'taxes' => $taxes,
            'vat' => $taxes,
            'discount' => $discount,
            'total' => $total,
        ];
    }

    protected function totalsFromCart(Cart $cart): array
    {
        return [
            'subtotal' => (float) $cart->subtotal,
            'shipping' => (float) $cart->shipping,
            'taxes' => (float) $cart->taxes,
            'vat' => (float) $cart->vat,
            'discount' => (float) $cart->discount,
            'total' => (float) $cart->total,
        ];
    }

    protected function getProductPricing(int $productId): array
    {
        $product = $this->cartRepository->getProductsWithPricing([$productId])->first();

        if (!$product) {
            return [
                'last_price' => 0,
                'tax' => 0,
                'available_quantity' => 0,
            ];
        }

        $available = (float) (($product->total_in ?? 0) - ($product->total_out ?? 0));

        return [
            'last_price' => (float) ($product->last_price ?? 0),
            'tax' => (float) ($product->tax ?? 0),
            'available_quantity' => $available,
        ];
    }
}
