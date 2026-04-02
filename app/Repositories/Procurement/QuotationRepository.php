<?php

namespace App\Repositories\Procurement;

use App\Models\Quotation;
use App\Repositories\Contracts\Procurement\QuotationRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class QuotationRepository implements QuotationRepositoryInterface
{
    public function paginateWithRelations(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return Quotation::query()
            ->with(['rfq', 'supplier'])
            ->when($filters['status'] ?? null, fn ($q, $v) => $q->where('status', $v))
            ->when($filters['rfq_id'] ?? null, fn ($q, $v) => $q->where('rfq_id', $v))
            ->when($filters['supplier_id'] ?? null, fn ($q, $v) => $q->where('supplier_id', $v))
            ->orderByDesc('created_at')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function create(array $data): Quotation
    {
        return Quotation::query()->create($data);
    }

    public function update(Quotation $quotation, array $data): Quotation
    {
        $quotation->update($data);

        return $quotation->refresh();
    }

    public function delete(Quotation $quotation): bool
    {
        return (bool) $quotation->delete();
    }

    public function replaceItems(Quotation $quotation, array $items): void
    {
        $quotation->items()->delete();
        $quotation->items()->createMany($items);
    }
}
