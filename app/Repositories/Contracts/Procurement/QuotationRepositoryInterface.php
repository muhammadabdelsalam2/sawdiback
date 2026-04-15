<?php

namespace App\Repositories\Contracts\Procurement;

use App\Models\Quotation;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface QuotationRepositoryInterface
{
    public function paginateWithRelations(array $filters, int $perPage = 15): LengthAwarePaginator;
    public function create(array $data): Quotation;
    public function update(Quotation $quotation, array $data): Quotation;
    public function delete(Quotation $quotation): bool;
    public function replaceItems(Quotation $quotation, array $items): void;
}
