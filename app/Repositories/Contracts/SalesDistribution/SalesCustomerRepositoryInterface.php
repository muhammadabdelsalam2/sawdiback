<?php

namespace App\Repositories\Contracts\SalesDistribution;

use App\Models\SalesDistribution\SalesCustomer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface SalesCustomerRepositoryInterface
{
    public function paginate(string $tenantId, array $filters, int $perPage = 15): LengthAwarePaginator;
    public function listActive(string $tenantId): Collection;
    public function create(array $data): SalesCustomer;
    public function update(SalesCustomer $customer, array $data): SalesCustomer;
    public function delete(SalesCustomer $customer): bool;
}
