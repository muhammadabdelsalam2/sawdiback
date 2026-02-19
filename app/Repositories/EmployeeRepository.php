<?php

namespace App\Repositories;

use App\Models\Employee;
use App\Repositories\Contracts\EmployeeRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EmployeeRepository implements EmployeeRepositoryInterface
{
    public function paginate(string $tenantId, int $perPage = 15): LengthAwarePaginator
    {
        return Employee::query()
            ->where('tenant_id', $tenantId)
            ->with(['department', 'jobTitle'])
            ->orderBy('id', 'desc')
            ->paginate($perPage);
    }

    public function create(array $data): Employee
    {
        return Employee::create($data);
    }

    public function update(Employee $employee, array $data): Employee
    {
        $employee->update($data);
        return $employee;
    }

    public function delete(Employee $employee): bool
    {
        return (bool) $employee->delete();
    }
}
