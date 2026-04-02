<?php

namespace App\Services\Finance;

use App\Models\Finance\Account;
use App\Repositories\Contracts\Finance\AccountRepositoryInterface;

class AccountService
{
    public function __construct(
        private readonly AccountRepositoryInterface $accounts
    ) {}

    public function listByTenant(string $tenantId)
    {
        return $this->accounts->listByTenant($tenantId);
    }

    public function listByTypes(string $tenantId, array $types)
    {
        return $this->accounts->listByTypes($tenantId, $types);
    }

    public function create(string $tenantId, array $data): Account
    {
        return $this->accounts->create([
            'tenant_id' => $tenantId,
            ...$data,
        ]);
    }

    public function update(Account $account, array $data): Account
    {
        return $this->accounts->update($account, $data);
    }

    public function delete(Account $account): bool
    {
        return $this->accounts->delete($account);
    }
}
