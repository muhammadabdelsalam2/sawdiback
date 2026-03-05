<?php

namespace App\Repositories\Contracts\SalesDistribution;

use App\Models\SalesDistribution\SalesOrder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface SalesOrderRepositoryInterface
{
    public function paginateWithRelations(string $tenantId, array $filters, int $perPage = 15): LengthAwarePaginator;
    public function listForSelection(string $tenantId): Collection;
    public function create(array $data): SalesOrder;
    public function update(SalesOrder $order, array $data): SalesOrder;
    public function delete(SalesOrder $order): bool;
    public function replaceItems(SalesOrder $order, array $items): void;
}
