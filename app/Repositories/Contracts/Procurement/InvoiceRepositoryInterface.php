<?php

namespace App\Repositories\Contracts\Procurement;

use App\Models\Invoice;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface InvoiceRepositoryInterface
{
    public function paginateWithRelations(array $filters, int $perPage = 15): LengthAwarePaginator;
    public function create(array $data): Invoice;
    public function update(Invoice $invoice, array $data): Invoice;
    public function delete(Invoice $invoice): bool;
}
