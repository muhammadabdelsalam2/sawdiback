<?php

namespace App\Repositories\Contracts\Api;

use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Support\Collection;

interface WalletRepositoryInterface
{
    public function findOrCreateByUser(User $user): Wallet;

    public function getRecentTransactions(Wallet $wallet, int $limit = 10): Collection;

    public function adjustBalances(Wallet $wallet, float $balanceDelta = 0, int $pointsDelta = 0): Wallet;

    public function createTransaction(Wallet $wallet, array $data): WalletTransaction;
}
