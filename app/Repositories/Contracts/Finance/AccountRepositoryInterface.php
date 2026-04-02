<?php

namespace App\Repositories\Contracts\Finance;

use App\Models\Finance\Account;
use Illuminate\Support\Collection;

interface AccountRepositoryInterface
{
    public function listByTenant(string $tenantId): Collection;
    public function listByTypes(string $tenantId, array $types): Collection;
    public function findByCode(string $tenantId, string $code): ?Account;
    public function create(array $data): Account;
    public function update(Account $account, array $data): Account;
    public function delete(Account $account): bool;
}
