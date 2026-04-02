<?php

namespace App\Repositories\Contracts\Procurement;

use App\Models\Rfq;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface RfqRepositoryInterface
{
    public function paginateWithRelations(array $filters, int $perPage = 15): LengthAwarePaginator;
    public function create(array $data): Rfq;
    public function update(Rfq $rfq, array $data): Rfq;
    public function delete(Rfq $rfq): bool;
}
