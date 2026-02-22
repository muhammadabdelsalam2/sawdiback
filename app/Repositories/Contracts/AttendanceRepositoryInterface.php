<?php

namespace App\Repositories\Contracts;

use App\Models\Attendance;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface AttendanceRepositoryInterface
{
    public function paginate(string $tenantId, int $perPage = 20): LengthAwarePaginator;

    public function checkIn(string $tenantId, int $employeeId): Attendance;

    public function checkOut(Attendance $attendance): Attendance;
}
