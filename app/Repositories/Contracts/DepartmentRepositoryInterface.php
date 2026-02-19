<?php

namespace App\Repositories\Contracts;

use App\Models\Department;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface DepartmentRepositoryInterface
{
    public function paginate(string $tenantId, int $perPage = 15): LengthAwarePaginator;
    public function create(array $data): Department;
    public function update(Department $department, array $data): Department;
    public function delete(Department $department): bool;
}
