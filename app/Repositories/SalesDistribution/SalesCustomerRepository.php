<?php

namespace App\Repositories\SalesDistribution;

use App\Models\SalesDistribution\SalesCustomer;
use App\Repositories\Contracts\SalesDistribution\SalesCustomerRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class SalesCustomerRepository implements SalesCustomerRepositoryInterface
{
    public function paginate(string $tenantId, array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return SalesCustomer::query()
            ->where('tenant_id', $tenantId)
            ->when($filters['type'] ?? null, fn ($q, $v) => $q->where('type', $v))
            ->when($filters['status'] ?? null, fn ($q, $v) => $q->where('status', $v))
            ->when($filters['q'] ?? null, function ($q, $v) {
                $q->where(function ($inner) use ($v) {
                    $inner->where('name', 'like', "%{$v}%")
                        ->orWhere('phones', 'like', "%{$v}%")
                        ->orWhere('tax_number', 'like', "%{$v}%");
                });
            })
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function listActive(string $tenantId): Collection
    {
        return SalesCustomer::query()
            ->where('tenant_id', $tenantId)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();
    }

    public function create(array $data): SalesCustomer
    {
        return SalesCustomer::query()->create($data);
    }

    public function update(SalesCustomer $customer, array $data): SalesCustomer
    {
        $customer->update($data);

        return $customer->refresh();
    }

    public function delete(SalesCustomer $customer): bool
    {
        return (bool) $customer->delete();
    }
}
