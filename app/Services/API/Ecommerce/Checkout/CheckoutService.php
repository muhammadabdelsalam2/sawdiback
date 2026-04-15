<?php

namespace App\Services\API\Ecommerce\Checkout;

use App\Http\Resources\OrderResource;
use App\Http\Resources\UserAddressResource;
use App\Http\Resources\UserPaymentMethodResource;
use App\Models\Order;
use App\Models\User;
use App\Repositories\Contracts\Api\CartRepositoryInterface;
use App\Repositories\Contracts\Api\OrderRepositoryInterface;
use App\Repositories\Contracts\Api\WalletRepositoryInterface;
use App\Support\ServiceResult;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CheckoutService
{
    public function __construct(
        protected CartRepositoryInterface $cartRepository,
        protected OrderRepositoryInterface $orderRepository,
        protected WalletRepositoryInterface $walletRepository
    ) {
    }

    public function summary(User $user): array
    {
        $cart = $this->cartRepository->loadCartForUser($user);

        $totals = [
            'subtotal' => (float) $cart->subtotal,
            'shipping' => (float) $cart->shipping,
            'taxes' => (float) $cart->taxes,
            'vat' => (float) $cart->vat,
            'discount' => (float) $cart->discount,
            'total' => (float) $cart->total,
        ];

        $addresses = $user->addresses()->orderByDesc('is_default')->latest('id')->get();
        $defaultAddress = $addresses->firstWhere('is_default', true) ?? $addresses->first();

        $selectedAddress = $cart->user_address_id
            ? $addresses->firstWhere('id', $cart->user_address_id)
            : $defaultAddress;

        $paymentMethods = $user->paymentMethods()->orderByDesc('is_default')->latest('id')->get();

        $wallet = $this->walletRepository->findOrCreateByUser($user);

        return ServiceResult::success(
            data: [
                'cart_id' => $cart->id,
                'items_count' => $cart->items()->count(),
                'totals' => $totals,
                'selected_address' => $selectedAddress ? new UserAddressResource($selectedAddress) : null,
                'addresses' => UserAddressResource::collection($addresses),
                'payment_methods' => [
                    'saved_cards' => UserPaymentMethodResource::collection($paymentMethods),
                    'options' => [
                        ['key' => 'card', 'label' => 'Card', 'requires_saved_method' => true],
                        ['key' => 'apple_pay', 'label' => 'Apple Pay', 'requires_saved_method' => false],
                        ['key' => 'wallet', 'label' => 'Wallet', 'requires_saved_method' => false],
                        ['key' => 'cash', 'label' => 'Cash on Delivery', 'requires_saved_method' => false],
                    ],
                ],
                'wallet' => [
                    'balance' => (float) $wallet->balance,
                    'currency' => $wallet->currency,
                    'points_balance' => (int) $wallet->points_balance,
                ],
                'can_place_order' => $cart->items()->count() > 0 && $selectedAddress !== null,
            ],
            message: 'Checkout summary loaded successfully.',
            code: 200
        );
    }

    public function placeOrder(User $user, array $payload): array
    {
        try {
            return DB::transaction(function () use ($user, $payload) {
                $cart = $this->cartRepository->loadCartForUser($user);

                if ($cart->items()->count() === 0) {
                    return ServiceResult::error('Cart is empty.', code: 422, errors: ['cart' => 'empty']);
                }

                $addressId = $payload['address_id'] ?? $cart->user_address_id;
                $address = $addressId ? $user->addresses()->whereKey($addressId)->first() : null;

                if (!$address) {
                    return ServiceResult::error('Shipping address is required.', code: 422, errors: ['address_id' => 'required']);
                }

                $paymentMethod = $payload['payment_method'] ?? 'cash';
                $allowedMethods = ['card', 'apple_pay', 'wallet', 'cash'];

                if (!in_array($paymentMethod, $allowedMethods, true)) {
                    return ServiceResult::error('Invalid payment method.', code: 422, errors: ['payment_method' => 'invalid']);
                }

                if ($paymentMethod === 'card') {
                    $methodId = $payload['payment_method_id'] ?? null;
                    if (!$methodId) {
                        return ServiceResult::error('Payment method is required.', code: 422, errors: ['payment_method_id' => 'required']);
                    }
                    if (!$user->paymentMethods()->whereKey($methodId)->exists()) {
                        return ServiceResult::error('Payment method is invalid.', code: 422, errors: ['payment_method_id' => 'invalid']);
                    }
                }

                $items = $cart->items()->with('product')->get();
                $productIds = $items->pluck('inventory_product_id')->all();
                $products = $this->cartRepository->getProductsWithPricing($productIds)->keyBy('id');

                foreach ($items as $item) {
                    $product = $products->get($item->inventory_product_id);
                    if (!$product) {
                        return ServiceResult::error('Product not available.', code: 422);
                    }

                    $available = (float) (($product->total_in ?? 0) - ($product->total_out ?? 0));
                    if ($available > 0 && (float) $item->quantity > $available) {
                        return ServiceResult::error('Insufficient stock for one of the items.', code: 422, errors: ['inventory' => 'insufficient']);
                    }
                }

                $subtotal = 0.0;
                $taxes = 0.0;

                foreach ($items as $item) {
                    $product = $products->get($item->inventory_product_id);
                    $unitPrice = (float) $item->unit_price;
                    $unitTax = (float) ($product?->tax ?? 0);
                    $lineSubtotal = round($unitPrice * (float) $item->quantity, 2);
                    $lineTax = round($unitTax * (float) $item->quantity, 2);

                    $subtotal += $lineSubtotal;
                    $taxes += $lineTax;
                }

                $subtotal = round($subtotal, 2);
                $taxes = round($taxes, 2);
                $shipping = (float) $cart->shipping;
                $discount = (float) $cart->discount;
                $total = max(0, round($subtotal + $shipping + $taxes - $discount, 2));

                $orderNo = $this->generateOrderNumber($user);
                $estimatedDelivery = now()->addDays((int) config('ecommerce.estimated_delivery_days', 3));

                $order = $this->orderRepository->createForUser($user, [
                    'order_no' => $orderNo,
                    'user_address_id' => $address->id,
                    'status' => Order::STATUS_PENDING,
                    'payment_method' => $paymentMethod,
                    'payment_status' => $paymentMethod === 'wallet' ? 'paid' : 'pending',
                    'subtotal' => $subtotal,
                    'shipping' => $shipping,
                    'taxes' => $taxes,
                    'vat' => $taxes,
                    'discount' => $discount,
                    'total' => $total,
                    'currency' => config('ecommerce.currency', 'AED'),
                    'paid_at' => $paymentMethod === 'wallet' ? now() : null,
                    'placed_at' => now(),
                    'estimated_delivery_at' => $estimatedDelivery,
                    'metadata' => [
                        'weekly_delivery' => (bool) $cart->weekly_delivery,
                        'coupon_code' => $cart->coupon_code,
                    ],
                ]);

                foreach ($items as $item) {
                    $product = $products->get($item->inventory_product_id);
                    $unitPrice = (float) $item->unit_price;
                    $unitTax = (float) ($product?->tax ?? 0);
                    $lineSubtotal = round($unitPrice * (float) $item->quantity, 2);
                    $lineTax = round($unitTax * (float) $item->quantity, 2);
                    $lineTotal = round($lineSubtotal + $lineTax, 2);

                    $snapshot = [
                        'id' => $product?->id,
                        'name' => $product?->name,
                        'code' => $product?->code,
                        'unit' => $product?->unit,
                        'image' => $product?->image_url ?? null,
                        'category' => $product?->category,
                        'tax' => (float) ($product?->tax ?? 0),
                    ];

                    $order->items()->create([
                        'inventory_product_id' => $product?->id,
                        'product_name' => $product?->name ?? $item->product?->name ?? 'Product',
                        'product_code' => $product?->code,
                        'unit' => $product?->unit,
                        'quantity' => $item->quantity,
                        'unit_price' => $unitPrice,
                        'unit_tax' => $unitTax,
                        'line_subtotal' => $lineSubtotal,
                        'line_tax' => $lineTax,
                        'discount' => 0,
                        'line_total' => $lineTotal,
                        'snapshot' => $snapshot,
                    ]);
                }

                $this->orderRepository->addStatusHistory($order, null, Order::STATUS_PENDING, $user->id, 'Order placed');

                if ($paymentMethod === 'wallet') {
                    $wallet = $this->walletRepository->findOrCreateByUser($user);
                    if ((float) $wallet->balance < (float) $order->total) {
                        throw ValidationException::withMessages([
                            'wallet' => ['Insufficient wallet balance.'],
                        ]);
                    }

                    $this->walletRepository->adjustBalances($wallet, -((float) $order->total), 0);
                    $this->walletRepository->createTransaction($wallet, [
                        'type' => 'order_payment',
                        'status' => 'completed',
                        'title' => 'Order Payment',
                        'description' => 'Wallet payment for order ' . $order->order_no,
                        'amount' => -((float) $order->total),
                        'points' => 0,
                        'reference_type' => Order::class,
                        'reference_id' => $order->id,
                        'metadata' => [
                            'payment_method' => 'wallet',
                        ],
                    ]);
                }

                $cart->items()->delete();
                $cart->update([
                    'coupon_code' => null,
                    'discount' => 0,
                    'subtotal' => 0,
                    'shipping' => 0,
                    'taxes' => 0,
                    'vat' => 0,
                    'total' => 0,
                    'user_address_id' => $address->id,
                ]);

                $order->load(['items', 'address']);

                return ServiceResult::success(
                    data: [
                        'order' => new OrderResource($order),
                    ],
                    message: 'Order placed successfully.',
                    code: 201
                );
            });
        } catch (ValidationException $e) {
            return ServiceResult::error('Unable to place order.', errors: $e->errors(), code: 422);
        }
    }

    protected function generateOrderNumber(User $user): string
    {
        $prefix = 'ORD-' . now()->format('Ymd');

        for ($i = 0; $i < 5; $i++) {
            $candidate = $prefix . '-' . strtoupper(substr(md5(uniqid((string) $user->id, true)), 0, 6));
            if (!Order::query()->where('order_no', $candidate)->exists()) {
                return $candidate;
            }
        }

        return $prefix . '-' . strtoupper(substr(md5((string) microtime(true)), 0, 6));
    }
}
