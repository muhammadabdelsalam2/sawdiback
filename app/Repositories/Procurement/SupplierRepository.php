<?php

namespace App\Repositories\Procurement;

use App\Models\Supplier;
use App\Repositories\Contracts\Procurement\SupplierRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class SupplierRepository implements SupplierRepositoryInterface
{
    public function paginate(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return Supplier::query()
            ->when($filters['status'] ?? null, fn ($q, $v) => $q->where('is_active', $v === 'active'))
            ->when($filters['q'] ?? null, function ($q, $v) {
                $q->where(function ($inner) use ($v) {
                    $inner->where('name', 'like', "%{$v}%")
                        ->orWhere('email', 'like', "%{$v}%")
                        ->orWhere('phone', 'like', "%{$v}%");
                });
            })
            ->orderBy('name')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function listActive(): Collection
    {
        return Supplier::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    public function create(array $data): Supplier
    {
        return Supplier::query()->create($data);
    }

    public function update(Supplier $supplier, array $data): Supplier
    {
        $supplier->update($data);

        return $supplier->refresh();
    }

    public function delete(Supplier $supplier): bool
    {
        return (bool) $supplier->delete();
    }
}
