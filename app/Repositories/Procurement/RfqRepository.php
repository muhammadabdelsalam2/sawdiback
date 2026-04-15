<?php

namespace App\Repositories\Procurement;

use App\Models\Rfq;
use App\Repositories\Contracts\Procurement\RfqRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class RfqRepository implements RfqRepositoryInterface
{
    public function paginateWithRelations(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return Rfq::query()
            ->with(['requisition'])
            ->when($filters['status'] ?? null, fn ($q, $v) => $q->where('status', $v))
            ->when($filters['purchase_requisition_id'] ?? null, fn ($q, $v) => $q->where('purchase_requisition_id', $v))
            ->orderByDesc('created_at')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function create(array $data): Rfq
    {
        return Rfq::query()->create($data);
    }

    public function update(Rfq $rfq, array $data): Rfq
    {
        $rfq->update($data);

        return $rfq->refresh();
    }

    public function delete(Rfq $rfq): bool
    {
        return (bool) $rfq->delete();
    }
}
