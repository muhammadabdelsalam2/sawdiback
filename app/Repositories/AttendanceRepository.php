<?php

namespace App\Repositories;

use App\Models\Attendance;
use App\Repositories\Contracts\AttendanceRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AttendanceRepository implements AttendanceRepositoryInterface
{
    public function paginate(string $tenantId, int $perPage = 20): LengthAwarePaginator
    {
        return Attendance::query()
            ->where("tenant_id", $tenantId)
            ->with("employee")
            ->orderByDesc("day")
            ->orderByDesc("id")
            ->paginate($perPage);
    }

    public function checkIn(string $tenantId, int $employeeId): Attendance
    {
        $day = now()->toDateString();

        $attendance = Attendance::query()->firstOrCreate(
            ["tenant_id" => $tenantId, "employee_id" => $employeeId, "day" => $day],
            ["check_in_at" => now()]
        );

        if (!$attendance->check_in_at) {
            $attendance->check_in_at = now();
            $attendance->save();
        }

        return $attendance;
    }

    public function checkOut(Attendance $attendance): Attendance
    {
        $attendance->check_out_at = now();
        $attendance->save();

        return $attendance;
    }
}
