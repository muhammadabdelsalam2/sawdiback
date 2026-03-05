<?php

namespace App\Repositories\SalesDistribution;

use App\Models\SalesDistribution\SalesOrder;
use App\Repositories\Contracts\SalesDistribution\SalesOrderRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class SalesOrderRepository implements SalesOrderRepositoryInterface
{
    public function paginateWithRelations(string $tenantId, array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return SalesOrder::query()
            ->with(['customer', 'contract'])
            ->where('tenant_id', $tenantId)
            ->when($filters['customer_id'] ?? null, fn ($q, $v) => $q->where('customer_id', $v))
            ->when($filters['status'] ?? null, fn ($q, $v) => $q->where('status', $v))
            ->when($filters['date_from'] ?? null, fn ($q, $v) => $q->whereDate('order_date', '>=', $v))
            ->when($filters['date_to'] ?? null, fn ($q, $v) => $q->whereDate('order_date', '<=', $v))
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function listForSelection(string $tenantId): Collection
    {
        return SalesOrder::query()
            ->where('tenant_id', $tenantId)
            ->orderByDesc('id')
            ->get();
    }

    public function create(array $data): SalesOrder
    {
        return SalesOrder::query()->create($data);
    }

    public function update(SalesOrder $order, array $data): SalesOrder
    {
        $order->update($data);

        return $order->refresh();
    }

    public function delete(SalesOrder $order): bool
    {
        return (bool) $order->delete();
    }

    public function replaceItems(SalesOrder $order, array $items): void
    {
        $order->items()->delete();
        $order->items()->createMany($items);
    }
}
