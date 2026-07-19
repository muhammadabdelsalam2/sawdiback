<?php

namespace App\Services\HR;

use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class HrDocumentAlertService
{
    public function expiringDocuments(int $days = 30): Collection
    {
        $start = Carbon::today();
        $end = $start->copy()->addDays(max($days, 0));

        return Employee::query()
            ->with(['department', 'jobTitle'])
            ->where(function ($query) use ($start, $end): void {
                $query->whereBetween('passport_expiry_date', [$start->toDateString(), $end->toDateString()])
                    ->orWhereBetween('iqama_expiry_date', [$start->toDateString(), $end->toDateString()]);
            })
            ->orderByRaw('COALESCE(passport_expiry_date, iqama_expiry_date) asc')
            ->get()
            ->map(function (Employee $employee) use ($start, $end): array {
                return [
                    'employee' => $employee,
                    'passport_expiring' => $employee->passport_expiry_date !== null
                        && $employee->passport_expiry_date->betweenIncluded($start, $end),
                    'iqama_expiring' => $employee->iqama_expiry_date !== null
                        && $employee->iqama_expiry_date->betweenIncluded($start, $end),
                ];
            });
    }
}
