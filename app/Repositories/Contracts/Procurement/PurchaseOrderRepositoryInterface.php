<?php

namespace App\Repositories\Contracts\Procurement;

use App\Models\PurchaseOrder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface PurchaseOrderRepositoryInterface
{
    public function paginateWithRelations(array $filters, int $perPage = 15): LengthAwarePaginator;
    public function create(array $data): PurchaseOrder;
    public function update(PurchaseOrder $order, array $data): PurchaseOrder;
    public function delete(PurchaseOrder $order): bool;
    public function replaceItems(PurchaseOrder $order, array $items): void;
}
