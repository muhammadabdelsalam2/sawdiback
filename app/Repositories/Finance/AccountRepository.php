<?php

namespace App\Repositories\Finance;

use App\Models\Finance\Account;
use App\Repositories\Contracts\Finance\AccountRepositoryInterface;
use Illuminate\Support\Collection;

class AccountRepository implements AccountRepositoryInterface
{
    public function listByTenant(string $tenantId): Collection
    {
        return Account::query()
            ->with('parent')
            ->where('tenant_id', $tenantId)
            ->orderBy('code')
            ->get();
    }

    public function listByTypes(string $tenantId, array $types): Collection
    {
        return Account::query()
            ->with('parent')
            ->where('tenant_id', $tenantId)
            ->whereIn('type', $types)
            ->where('is_active', true)
            ->orderBy('code')
            ->get();
    }

    public function findByCode(string $tenantId, string $code): ?Account
    {
        return Account::query()
            ->with('parent')
            ->where('tenant_id', $tenantId)
            ->where('code', $code)
            ->first();
    }

    public function create(array $data): Account
    {
        return Account::query()->create($data);
    }

    public function update(Account $account, array $data): Account
    {
        $account->update($data);

        return $account->refresh();
    }

    public function delete(Account $account): bool
    {
        return (bool) $account->delete();
    }
}
