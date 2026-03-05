<?php

namespace App\Services\SalesDistribution;

use App\Models\SalesDistribution\SalesCustomer;
use App\Repositories\Contracts\SalesDistribution\SalesCustomerRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class SalesCustomerService
{
    public function __construct(
        private readonly SalesCustomerRepositoryInterface $repo
    ) {}

    public function paginate(string $tenantId, array $filters): LengthAwarePaginator
    {
        return $this->repo->paginate($tenantId, $filters);
    }

    public function listActive(string $tenantId): Collection
    {
        return $this->repo->listActive($tenantId);
    }

    public function create(string $tenantId, array $data): SalesCustomer
    {
        return $this->repo->create([
            'tenant_id' => $tenantId,
            ...$data,
        ]);
    }

    public function update(SalesCustomer $customer, array $data): SalesCustomer
    {
        return $this->repo->update($customer, $data);
    }

    public function delete(SalesCustomer $customer): bool
    {
        return $this->repo->delete($customer);
    }
}
