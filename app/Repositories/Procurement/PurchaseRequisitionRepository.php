<?php

namespace App\Repositories\Procurement;

use App\Models\PurchaseRequisition;
use App\Repositories\Contracts\Procurement\PurchaseRequisitionRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PurchaseRequisitionRepository implements PurchaseRequisitionRepositoryInterface
{
    public function paginateWithRelations(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return PurchaseRequisition::query()
            ->with(['department', 'requester'])
            ->when($filters['status'] ?? null, fn ($q, $v) => $q->where('status', $v))
            ->when($filters['department_id'] ?? null, fn ($q, $v) => $q->where('department_id', $v))
            ->orderByDesc('created_at')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function create(array $data): PurchaseRequisition
    {
        return PurchaseRequisition::query()->create($data);
    }

    public function update(PurchaseRequisition $requisition, array $data): PurchaseRequisition
    {
        $requisition->update($data);

        return $requisition->refresh();
    }

    public function delete(PurchaseRequisition $requisition): bool
    {
        return (bool) $requisition->delete();
    }

    public function replaceItems(PurchaseRequisition $requisition, array $items): void
    {
        $requisition->items()->delete();
        $requisition->items()->createMany($items);
    }
}
