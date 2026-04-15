<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Repositories\Contracts\Api\WalletRepositoryInterface;
use Illuminate\Support\Collection;

class WalletRepository implements WalletRepositoryInterface
{
    public function findOrCreateByUser(User $user): Wallet
    {
        return Wallet::query()->firstOrCreate(
            ['user_id' => $user->id],
            [
                'balance' => 0,
                'currency' => config('wallet.default_currency', 'AED'),
                'points_balance' => 0,
            ]
        );
    }

    public function getRecentTransactions(Wallet $wallet, int $limit = 10): Collection
    {
        return $wallet->transactions()
            ->with('wallet')
            ->limit($limit)
            ->get();
    }

    public function adjustBalances(Wallet $wallet, float $balanceDelta = 0, int $pointsDelta = 0): Wallet
    {
        $wallet->balance = round(((float) $wallet->balance + $balanceDelta), 2);
        $wallet->points_balance = max(0, ((int) $wallet->points_balance + $pointsDelta));
        $wallet->save();

        return $wallet->refresh();
    }

    public function createTransaction(Wallet $wallet, array $data): WalletTransaction
    {
        return $wallet->transactions()->create([
            'type' => $data['type'],
            'status' => $data['status'] ?? 'completed',
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'amount' => $data['amount'] ?? 0,
            'points' => $data['points'] ?? 0,
            'reference_type' => $data['reference_type'] ?? null,
            'reference_id' => $data['reference_id'] ?? null,
            'metadata' => $data['metadata'] ?? null,
        ]);
    }
}
