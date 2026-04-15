<?php

namespace App\Repositories\Contracts\Procurement;

use App\Models\PurchaseRequisition;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface PurchaseRequisitionRepositoryInterface
{
    public function paginateWithRelations(array $filters, int $perPage = 15): LengthAwarePaginator;
    public function create(array $data): PurchaseRequisition;
    public function update(PurchaseRequisition $requisition, array $data): PurchaseRequisition;
    public function delete(PurchaseRequisition $requisition): bool;
    public function replaceItems(PurchaseRequisition $requisition, array $items): void;
}
