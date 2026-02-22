<?php

namespace App\Repositories;

use App\Models\LeaveRequest;
use App\Repositories\Contracts\LeaveRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class LeaveRepository implements LeaveRepositoryInterface
{
    public function paginate(string $tenantId, int $perPage = 20): LengthAwarePaginator
    {
        return LeaveRequest::query()
            ->where('tenant_id', $tenantId)
            ->with('employee')
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    public function create(array $data): LeaveRequest
    {
        return LeaveRequest::create($data);
    }

    public function approve(LeaveRequest $leave, int $actionedBy): LeaveRequest
    {
        $leave->status      = LeaveRequest::STATUS_APPROVED;
        $leave->actioned_by = $actionedBy;
        $leave->actioned_at = now();
        $leave->save();

        return $leave;
    }

    public function reject(LeaveRequest $leave, int $actionedBy): LeaveRequest
    {
        $leave->status      = LeaveRequest::STATUS_REJECTED;
        $leave->actioned_by = $actionedBy;
        $leave->actioned_at = now();
        $leave->save();

        return $leave;
    }
}
