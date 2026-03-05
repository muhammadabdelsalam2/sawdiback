<?php

namespace App\Repositories\Contracts\SalesDistribution;

use App\Models\SalesDistribution\SalesContract;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface SalesContractRepositoryInterface
{
    public function paginate(string $tenantId, array $filters, int $perPage = 15): LengthAwarePaginator;
    public function listActive(string $tenantId): Collection;
    public function create(array $data): SalesContract;
    public function update(SalesContract $contract, array $data): SalesContract;
    public function delete(SalesContract $contract): bool;
}
