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
        $data = $request->validated();
        $data['name_translations'] = [$this->localeKey() => $data['name']];

        $this->repo->create([
            'tenant_id' => $tenantId,
            ...$data,
        ]);

        return redirect()->route('customer.hr.departments.index', ['locale' => $locale])
            ->with('success', __('dashboard.messages.success.department_created'));
    }

    public function edit(string $locale, Department $department): View
    {
        $this->authorizeTenant($department);

        return view('dashboard.customer.hr.departments.edit', compact('department'));
    }

    public function update(DepartmentUpdateRequest $request, string $locale, Department $department): RedirectResponse
    {
        $this->authorizeTenant($department);
        $data = $request->validated();
        
        $translations = $department->name_translations ?? [];
        $translations[$this->localeKey()] = $data['name'];
        $data['name_translations'] = $translations;

        $this->repo->update($department, $data);

        return redirect()->route('customer.hr.departments.index', ['locale' => $locale])
            ->with('success', __('dashboard.messages.success.department_updated'));
    }

    public function destroy(string $locale, Department $department): RedirectResponse
    {
        $this->authorizeTenant($department);

        $this->repo->delete($department);

        return redirect()->route('customer.hr.departments.index', ['locale' => $locale])
            ->with('success', __('dashboard.messages.success.department_deleted'));
    }

    private function localeKey(): string
    {
        return substr(app()->getLocale(), 0, 2);
    }

    private function authorizeTenant(Department $department): void
    {
        $tenantId = (string) auth()->user()->tenant_id;
        if ($department->tenant_id !== $tenantId) {
            abort(403);
        }
    }
}
