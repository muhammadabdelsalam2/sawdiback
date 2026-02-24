<?php

namespace App\Repositories;

use App\Models\Department;
use App\Repositories\Contracts\DepartmentRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class DepartmentRepository implements DepartmentRepositoryInterface
{
    public function paginate(string $tenantId, int $perPage = 15): LengthAwarePaginator
    {
        return Department::query()
            ->where('tenant_id', $tenantId)
            ->orderBy('id', 'desc')
            ->paginate($perPage);
    }

    public function create(array $data): Department
    {
        return Department::create($data);
    }

    public function update(Department $department, array $data): Department
    {
        $department->update($data);
        return $department;
    }

    public function delete(Department $department): bool
    {
        return (bool) $department->delete();
    }
}
