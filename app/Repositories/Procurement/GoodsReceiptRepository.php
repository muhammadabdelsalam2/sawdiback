<?php

namespace App\Repositories\Procurement;

use App\Models\GoodsReceipt;
use App\Repositories\Contracts\Procurement\GoodsReceiptRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class GoodsReceiptRepository implements GoodsReceiptRepositoryInterface
{
    public function paginateWithRelations(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return GoodsReceipt::query()
            ->with(['purchaseOrder', 'receiver'])
            ->when($filters['status'] ?? null, fn ($q, $v) => $q->where('status', $v))
            ->when($filters['purchase_order_id'] ?? null, fn ($q, $v) => $q->where('purchase_order_id', $v))
            ->orderByDesc('created_at')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function create(array $data): GoodsReceipt
    {
        return GoodsReceipt::query()->create($data);
    }

    public function update(GoodsReceipt $receipt, array $data): GoodsReceipt
    {
        $receipt->update($data);

        return $receipt->refresh();
    }

    public function delete(GoodsReceipt $receipt): bool
    {
        return (bool) $receipt->delete();
    }

    public function replaceItems(GoodsReceipt $receipt, array $items): void
    {
        $receipt->items()->delete();
        $receipt->items()->createMany($items);
    }
}
