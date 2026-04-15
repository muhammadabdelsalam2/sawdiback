<?php

namespace App\Repositories\Contracts\Procurement;

use App\Models\GoodsReceipt;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface GoodsReceiptRepositoryInterface
{
    public function paginateWithRelations(array $filters, int $perPage = 15): LengthAwarePaginator;
    public function create(array $data): GoodsReceipt;
    public function update(GoodsReceipt $receipt, array $data): GoodsReceipt;
    public function delete(GoodsReceipt $receipt): bool;
    public function replaceItems(GoodsReceipt $receipt, array $items): void;
}
