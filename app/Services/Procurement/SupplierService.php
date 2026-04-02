<?php

namespace App\Services\Procurement;

use App\Models\Supplier;
use App\Repositories\Contracts\Procurement\SupplierRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class SupplierService
{
    public function __construct(
        private readonly SupplierRepositoryInterface $repo
    ) {}

    public function paginate(array $filters): LengthAwarePaginator
    {
        return $this->repo->paginate($filters);
    }

    public function listActive(): Collection
    {
        return $this->repo->listActive();
    }

    public function create(array $data): Supplier
    {
        return $this->repo->create($data);
    }

    public function update(Supplier $supplier, array $data): Supplier
    {
        return $this->repo->update($supplier, $data);
    }

    public function delete(Supplier $supplier): bool
    {
        return $this->repo->delete($supplier);
    }
}
