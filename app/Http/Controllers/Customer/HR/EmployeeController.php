<?php

namespace App\Http\Controllers\Customer\HR;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\HR\EmployeeStoreRequest;
use App\Http\Requests\Customer\HR\EmployeeUpdateRequest;
use App\Models\Employee;
use App\Repositories\Contracts\DepartmentRepositoryInterface;
use App\Repositories\Contracts\EmployeeRepositoryInterface;
use App\Repositories\Contracts\JobTitleRepositoryInterface;
use App\Services\Customer\HR\HrContextService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    public function __construct(
        private readonly EmployeeRepositoryInterface $repo,
        private readonly DepartmentRepositoryInterface $departmentsRepo,
        private readonly JobTitleRepositoryInterface $jobTitlesRepo,
        private readonly HrContextService $ctx
    ) {}

    public function index(string $locale): View
    {
        $tenantId = $this->ctx->tenantIdOrFail(auth()->user());
        $employees = $this->repo->paginate($tenantId, 15);

        return view('dashboard.customer.hr.employees.index', compact('employees'));
    }

    public function create(string $locale): View
    {
        $tenantId = $this->ctx->tenantIdOrFail(auth()->user());

        $departments = $this->departmentsRepo->paginate($tenantId, 200);
        $jobTitles = $this->jobTitlesRepo->paginate($tenantId, 200);

        return view('dashboard.customer.hr.employees.create', compact('departments', 'jobTitles'));
    }

    public function store(EmployeeStoreRequest $request, string $locale): RedirectResponse
    {
        $tenantId = $this->ctx->tenantIdOrFail(auth()->user());

        $this->repo->create([
            'tenant_id' => $tenantId,
            ...$request->validated(),
        ]);

        return redirect()->route('customer.hr.employees.index', ['locale' => $locale])
            ->with('success', 'Employee created.');
    }

    public function edit(string $locale, Employee $employee): View
    {
        $this->authorizeTenant($employee);

        $tenantId = (string) auth()->user()->tenant_id;
        $departments = $this->departmentsRepo->paginate($tenantId, 200);
        $jobTitles = $this->jobTitlesRepo->paginate($tenantId, 200);

        return view('dashboard.customer.hr.employees.edit', compact('employee', 'departments', 'jobTitles'));
    }

    public function update(EmployeeUpdateRequest $request, string $locale, Employee $employee): RedirectResponse
    {
        $this->authorizeTenant($employee);

        $this->repo->update($employee, $request->validated());

        return redirect()->route('customer.hr.employees.index', ['locale' => $locale])
            ->with('success', 'Employee updated.');
    }

    public function destroy(string $locale, Employee $employee): RedirectResponse
    {
        $this->authorizeTenant($employee);

        $this->repo->delete($employee);

        return redirect()->route('customer.hr.employees.index', ['locale' => $locale])
            ->with('success', 'Employee deleted.');
    }

    private function authorizeTenant(Employee $employee): void
    {
        $tenantId = (string) auth()->user()->tenant_id;
        if ($employee->tenant_id !== $tenantId) {
            abort(403);
        }
    }
}
