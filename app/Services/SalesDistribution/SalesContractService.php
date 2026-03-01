<?php

namespace App\Services\SalesDistribution;

use App\Models\SalesDistribution\SalesContract;
use App\Repositories\Contracts\SalesDistribution\SalesContractRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class SalesContractService
{
    public function __construct(
        private readonly SalesContractRepositoryInterface $repo
    ) {}

    public function paginate(string $tenantId, array $filters): LengthAwarePaginator
    {
        return $this->repo->paginate($tenantId, $filters);
    }

    public function listActive(string $tenantId): Collection
    {
        return $this->repo->listActive($tenantId);
    }

    public function create(string $tenantId, array $data): SalesContract
    {
        return $this->repo->create([
            'tenant_id' => $tenantId,
            ...$data,
        ]);
    }

    public function update(SalesContract $contract, array $data): SalesContract
    {
        return $this->repo->update($contract, $data);
    }

    public function delete(SalesContract $contract): bool
    {
        return $this->repo->delete($contract);
    }
}
