<?php

namespace App\Http\Controllers\Customer\HR;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Repositories\Contracts\AttendanceRepositoryInterface;
use App\Repositories\Contracts\EmployeeRepositoryInterface;
use App\Services\Customer\HR\HrContextService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    public function __construct(
        private readonly AttendanceRepositoryInterface $repo,
        private readonly EmployeeRepositoryInterface $employeesRepo,
        private readonly HrContextService $ctx
    ) {}

    public function index(string $locale): View
    {
        $tenantId = $this->ctx->tenantIdOrFail(auth()->user());
        $attendances = $this->repo->paginate($tenantId, 20);

        // dropdown employees
        $employees = $this->employeesRepo->paginate($tenantId, 200);

        return view('dashboard.customer.hr.attendance.index', compact('attendances', 'employees'));
    }

    public function checkIn(Request $request, string $locale): RedirectResponse
    {
        $request->validate(['employee_id' => ['required', 'integer']]);

        $tenantId = $this->ctx->tenantIdOrFail(auth()->user());
        $this->repo->checkIn($tenantId, (int) $request->employee_id);

        return back()->with('success', 'Checked in successfully.');
    }

    public function checkOut(Request $request, string $locale, Attendance $attendance): RedirectResponse
    {
        $tenantId = $this->ctx->tenantIdOrFail(auth()->user());

        if ($attendance->tenant_id !== $tenantId) {
            abort(403);
        }

        $this->repo->checkOut($attendance);

        return back()->with('success', 'Checked out successfully.');
    }
}
