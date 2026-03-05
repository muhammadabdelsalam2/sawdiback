<?php

namespace App\Repositories\SalesDistribution;

use App\Models\SalesDistribution\SalesContract;
use App\Repositories\Contracts\SalesDistribution\SalesContractRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class SalesContractRepository implements SalesContractRepositoryInterface
{
    public function paginate(string $tenantId, array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return SalesContract::query()
            ->with('customer')
            ->where('tenant_id', $tenantId)
            ->when($filters['customer_id'] ?? null, fn ($q, $v) => $q->where('customer_id', $v))
            ->when($filters['status'] ?? null, fn ($q, $v) => $q->where('status', $v))
            ->when($filters['date_from'] ?? null, fn ($q, $v) => $q->whereDate('start_date', '>=', $v))
            ->when($filters['date_to'] ?? null, fn ($q, $v) => $q->whereDate('start_date', '<=', $v))
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function listActive(string $tenantId): Collection
    {
        return SalesContract::query()
            ->where('tenant_id', $tenantId)
            ->where('status', 'active')
            ->orderByDesc('id')
            ->get();
    }

    public function create(array $data): SalesContract
    {
        return SalesContract::query()->create($data);
    }

    public function update(SalesContract $contract, array $data): SalesContract
    {
        $contract->update($data);

        return $contract->refresh();
    }

    public function delete(SalesContract $contract): bool
    {
        return (bool) $contract->delete();
    }
}
