<?php

namespace App\Http\Controllers\Customer\HR;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\HR\EmployeeStoreRequest;
use App\Http\Requests\Customer\HR\EmployeeUpdateRequest;
use App\Models\Employee;
use App\Models\EmployeeAttachment;
use App\Repositories\Contracts\DepartmentRepositoryInterface;
use App\Repositories\Contracts\EmployeeRepositoryInterface;
use App\Repositories\Contracts\JobTitleRepositoryInterface;
use App\Services\Customer\HR\HrContextService;
use App\Services\HR\HrDocumentAlertService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
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

    public function documentAlerts(string $locale, HrDocumentAlertService $alerts): View
    {
        $days = (int) config('hr.document_expiry_alert_days', 30);
        $rows = $alerts->expiringDocuments($days);

        return view('dashboard.customer.hr.employees.document-alerts', compact('rows', 'days'));
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

        $data = Arr::except($request->validated(), $this->attachmentInputNames());

        $employee = $this->repo->create([
            'tenant_id' => $tenantId,
            ...$data,
        ]);

        $this->storeAttachments($request, $employee, $tenantId);

        return redirect()->route('customer.hr.employees.index', ['locale' => $locale])
            ->with('success', __('hr.messages.success.employee_created'));
    }

    public function show(string $locale, Employee $employee): View
    {
        $this->authorizeTenant($employee);

        $employee->load(['department', 'jobTitle', 'attachments']);

        return view('dashboard.customer.hr.employees.show', compact('employee'));
    }

    public function edit(string $locale, Employee $employee): View
    {
        $this->authorizeTenant($employee);

        $tenantId = (string) auth()->user()->tenant_id;
        $departments = $this->departmentsRepo->paginate($tenantId, 200);
        $jobTitles = $this->jobTitlesRepo->paginate($tenantId, 200);

        $employee->load('attachments');

        return view('dashboard.customer.hr.employees.edit', compact('employee', 'departments', 'jobTitles'));
    }

    public function update(EmployeeUpdateRequest $request, string $locale, Employee $employee): RedirectResponse
    {
        $this->authorizeTenant($employee);

        $data = Arr::except($request->validated(), $this->attachmentInputNames());

        $this->repo->update($employee, $data);
        $this->storeAttachments($request, $employee, (string) $employee->tenant_id);

        return redirect()->route('customer.hr.employees.index', ['locale' => $locale])
            ->with('success', __('hr.messages.success.employee_updated'));
    }

    public function destroy(string $locale, Employee $employee): RedirectResponse
    {
        $this->authorizeTenant($employee);

        $this->repo->delete($employee);

        return redirect()->route('customer.hr.employees.index', ['locale' => $locale])
            ->with('success', __('hr.messages.success.employee_deleted'));
    }

    private function authorizeTenant(Employee $employee): void
    {
        $tenantId = (string) auth()->user()->tenant_id;
        if ($employee->tenant_id !== $tenantId) {
            abort(403);
        }
    }

    private function storeAttachments(EmployeeStoreRequest|EmployeeUpdateRequest $request, Employee $employee, string $tenantId): void
    {
        foreach ($this->attachmentTypeMap() as $input => $type) {
            if (!$request->hasFile($input)) {
                continue;
            }

            $existing = $employee->attachments()->where('type', $type)->first();
            if ($existing) {
                @unlink(storage_path('app/public/' . $existing->path));
                $existing->delete();
            }

            $file = $request->file($input);
            $extension = $file->getClientOriginalExtension() ?: 'bin';
            $filename = Str::uuid() . '.' . $extension;
            $directory = storage_path('app/public/hr/employee-attachments');
            File::ensureDirectoryExists($directory);
            $file->move($directory, $filename);

            EmployeeAttachment::query()->create([
                'tenant_id' => $tenantId,
                'employee_id' => $employee->id,
                'type' => $type,
                'path' => 'hr/employee-attachments/' . $filename,
                'uploaded_at' => now(),
            ]);
        }
    }

    private function attachmentTypeMap(): array
    {
        return [
            'attachment_passport' => 'passport',
            'attachment_iqama' => 'iqama',
            'attachment_identity' => 'identity',
        ];
    }

    private function attachmentInputNames(): array
    {
        return array_keys($this->attachmentTypeMap());
    }
}
