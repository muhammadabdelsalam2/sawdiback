<?php

namespace App\Http\Controllers\Customer\HR;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\HR\DepartmentStoreRequest;
use App\Http\Requests\Customer\HR\DepartmentUpdateRequest;
use App\Models\Department;
use App\Repositories\Contracts\DepartmentRepositoryInterface;
use App\Services\Customer\HR\HrContextService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DepartmentController extends Controller
{
    public function __construct(
        private readonly DepartmentRepositoryInterface $repo,
        private readonly HrContextService $ctx
    ) {}

    public function index(string $locale): View
    {
        $tenantId = $this->ctx->tenantIdOrFail(auth()->user());
        $departments = $this->repo->paginate($tenantId, 15);

        return view('dashboard.customer.hr.departments.index', compact('departments'));
    }

    public function create(string $locale): View
    {
        return view('dashboard.customer.hr.departments.create');
    }

    public function store(DepartmentStoreRequest $request, string $locale): RedirectResponse
    {
        $tenantId = $this->ctx->tenantIdOrFail(auth()->user());

        $this->repo->create([
            'tenant_id' => $tenantId,
            ...$request->validated(),
        ]);

        return redirect()->route('customer.hr.departments.index', ['locale' => $locale])
            ->with('success', 'Department created.');
    }

    public function edit(string $locale, Department $department): View
    {
        $this->authorizeTenant($department);

        return view('dashboard.customer.hr.departments.edit', compact('department'));
    }

    public function update(DepartmentUpdateRequest $request, string $locale, Department $department): RedirectResponse
    {
        $this->authorizeTenant($department);

        $this->repo->update($department, $request->validated());

        return redirect()->route('customer.hr.departments.index', ['locale' => $locale])
            ->with('success', 'Department updated.');
    }

    public function destroy(string $locale, Department $department): RedirectResponse
    {
        $this->authorizeTenant($department);

        $this->repo->delete($department);

        return redirect()->route('customer.hr.departments.index', ['locale' => $locale])
            ->with('success', 'Department deleted.');
    }

    private function authorizeTenant(Department $department): void
    {
        $tenantId = (string) auth()->user()->tenant_id;
        if ($department->tenant_id !== $tenantId) {
            abort(403);
        }
    }
}
