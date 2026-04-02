<?php

namespace App\Repositories\Procurement;

use App\Models\PurchaseOrder;
use App\Repositories\Contracts\Procurement\PurchaseOrderRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PurchaseOrderRepository implements PurchaseOrderRepositoryInterface
{
    public function paginateWithRelations(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return PurchaseOrder::query()
            ->with(['supplier'])
            ->when($filters['status'] ?? null, fn ($q, $v) => $q->where('status', $v))
            ->when($filters['supplier_id'] ?? null, fn ($q, $v) => $q->where('supplier_id', $v))
            ->orderByDesc('created_at')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function create(array $data): PurchaseOrder
    {
        return PurchaseOrder::query()->create($data);
    }

    public function update(PurchaseOrder $order, array $data): PurchaseOrder
    {
        $order->update($data);

        return $order->refresh();
    }

    public function delete(PurchaseOrder $order): bool
    {
        return (bool) $order->delete();
    }

    public function replaceItems(PurchaseOrder $order, array $items): void
    {
        $order->items()->delete();
        $order->items()->createMany($items);
    }
}
