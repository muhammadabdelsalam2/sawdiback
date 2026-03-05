<?php

namespace App\Repositories\Contracts\SalesDistribution;

use App\Models\SalesDistribution\SalesInvoice;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface SalesInvoiceRepositoryInterface
{
    public function paginateWithRelations(string $tenantId, array $filters, int $perPage = 15): LengthAwarePaginator;
    public function create(array $data): SalesInvoice;
    public function update(SalesInvoice $invoice, array $data): SalesInvoice;
    public function delete(SalesInvoice $invoice): bool;
    public function refreshTotalPaid(SalesInvoice $invoice): float;
}
