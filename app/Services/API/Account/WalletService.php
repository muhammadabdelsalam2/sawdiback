<?php

namespace App\Services\API\Account;

use App\Models\User;
use App\Models\UserPaymentMethod;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Repositories\Contracts\Api\WalletRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class WalletService
{
    public function __construct(
        protected WalletRepositoryInterface $walletRepository
    ) {
    }

    public function getOverview(User $user): Wallet
    {
        $wallet = $this->walletRepository->findOrCreateByUser($user);

        $wallet->setRelation(
            'transactions',
            $this->walletRepository->getRecentTransactions(
                $wallet,
                (int) config('wallet.history_limit', 10)
            )
        );

        return $wallet;
    }

    public function topUp(User $user, array $payload): array
    {
        return DB::transaction(function () use ($user, $payload) {
            $wallet = $this->walletRepository->findOrCreateByUser($user);

            $savedPaymentMethod = null;

            if (($payload['payment_channel'] ?? 'card') === 'card') {
                if (!empty($payload['payment_method_id'])) {
                    $savedPaymentMethod = $user->paymentMethods()->find($payload['payment_method_id']);

                    if (!$savedPaymentMethod) {
                        throw ValidationException::withMessages([
                            'payment_method_id' => [__('account.api.invalid_payment_method')],
                        ]);
                    }
                }

                if (
                    empty($payload['payment_method_id']) &&
                    !empty($payload['save_payment_method']) &&
                    !empty($payload['card_number'])
                ) {
                    if (!empty($payload['make_default'])) {
                        $user->paymentMethods()->update(['is_default' => false]);
                    }

                    $savedPaymentMethod = $user->paymentMethods()->create([
                        'brand' => $payload['brand'] ?? 'Card',
                        'last_four' => substr((string) $payload['card_number'], -4),
                        'expiry_month' => (int) $payload['expiry_month'],
                        'expiry_year' => (int) $payload['expiry_year'],
                        'holder_name' => $payload['holder_name'] ?? null,
                        'gateway' => 'manual',
                        'gateway_reference' => null,
                        'is_default' => (bool) ($payload['make_default'] ?? false),
                    ]);
                }
            }

            $amount = round((float) $payload['amount'], 2);

            $wallet = $this->walletRepository->adjustBalances($wallet, $amount, 0);

            $transaction = $this->walletRepository->createTransaction($wallet, [
                'type' => 'top_up',
                'status' => 'completed',
                'title' => __('account.api.wallet_top_up_title'),
                'description' => __('account.api.wallet_top_up_description'),
                'amount' => $amount,
                'points' => 0,
                'reference_type' => $savedPaymentMethod ? UserPaymentMethod::class : null,
                'reference_id' => $savedPaymentMethod?->id,
                'metadata' => [
                    'payment_channel' => $payload['payment_channel'],
                    'payment_method_id' => $savedPaymentMethod?->id ?? $payload['payment_method_id'] ?? null,
                    'saved_for_future' => (bool) ($payload['save_payment_method'] ?? false),
                ],
            ]);

            $wallet->setRelation(
                'transactions',
                $this->walletRepository->getRecentTransactions(
                    $wallet,
                    (int) config('wallet.history_limit', 10)
                )
            );

            return [
                'wallet' => $wallet,
                'transaction' => $transaction->load('wallet'),
            ];
        });
    }

    public function convertPoints(User $user, array $payload): array
    {
        return DB::transaction(function () use ($user, $payload) {
            $wallet = $this->walletRepository->findOrCreateByUser($user);

            $points = (int) $payload['points'];
            $pointsPerBlock = (int) config('wallet.points.conversion.points', 100);
            $amountPerBlock = (float) config('wallet.points.conversion.amount', 1);

            if ($wallet->points_balance < $points) {
                throw ValidationException::withMessages([
                    'points' => [__('account.api.insufficient_points')],
                ]);
            }

            $amount = round(($points / $pointsPerBlock) * $amountPerBlock, 2);

            $wallet = $this->walletRepository->adjustBalances($wallet, $amount, -$points);

            $transaction = $this->walletRepository->createTransaction($wallet, [
                'type' => 'points_conversion',
                'status' => 'completed',
                'title' => __('account.api.points_converted_title'),
                'description' => __('account.api.points_converted_description', ['points' => $points]),
                'amount' => $amount,
                'points' => -$points,
                'reference_type' => null,
                'reference_id' => null,
                'metadata' => [
                    'conversion_rule' => [
                        'points' => $pointsPerBlock,
                        'amount' => $amountPerBlock,
                        'currency' => $wallet->currency,
                    ],
                ],
            ]);

            $wallet->setRelation(
                'transactions',
                $this->walletRepository->getRecentTransactions(
                    $wallet,
                    (int) config('wallet.history_limit', 10)
                )
            );

            return [
                'wallet' => $wallet,
                'transaction' => $transaction->load('wallet'),
            ];
        });
    }
}
