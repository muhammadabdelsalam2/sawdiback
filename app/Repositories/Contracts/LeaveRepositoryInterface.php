<?php

namespace App\Repositories\Contracts;

use App\Models\LeaveRequest;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface LeaveRepositoryInterface
{
    public function paginate(string $tenantId, int $perPage = 20): LengthAwarePaginator;
    public function create(array $data): LeaveRequest;
    public function approve(LeaveRequest $leave, int $actionedBy): LeaveRequest;
    public function reject(LeaveRequest $leave, int $actionedBy): LeaveRequest;
}
