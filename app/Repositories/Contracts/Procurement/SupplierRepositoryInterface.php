<?php

namespace App\Repositories\Contracts\Procurement;

use App\Models\Supplier;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface SupplierRepositoryInterface
{
    public function paginate(array $filters, int $perPage = 15): LengthAwarePaginator;
    public function listActive(): Collection;
    public function create(array $data): Supplier;
    public function update(Supplier $supplier, array $data): Supplier;
    public function delete(Supplier $supplier): bool;
}
