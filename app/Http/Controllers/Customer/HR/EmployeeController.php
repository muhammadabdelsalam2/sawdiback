<?php

namespace App\Http\Controllers\Customer\HR;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\HR\EmployeeFinancialActionStoreRequest;
use App\Http\Requests\Customer\HR\EmployeeStoreRequest;
use App\Http\Requests\Customer\HR\EmployeeUpdateRequest;
use App\Models\Employee;
use App\Models\EmployeeAttachment;
use App\Models\EmployeeFinancialAction;
use App\Models\Farm;
use App\Repositories\Contracts\DepartmentRepositoryInterface;
use App\Repositories\Contracts\EmployeeRepositoryInterface;
use App\Repositories\Contracts\JobTitleRepositoryInterface;
use App\Services\Customer\HR\HrContextService;
use App\Services\HR\HrDocumentAlertService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

    public function annualLeaveAlerts(string $locale): View
    {
        $tenantId = $this->ctx->tenantIdOrFail(auth()->user());
        $thresholdDays = 30;

        $approachingLeaves = Employee::query()
            ->where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->whereNotNull('next_annual_leave_date')
            ->whereBetween('next_annual_leave_date', [now()->toDateString(), now()->addDays($thresholdDays)->toDateString()])
            ->with(['department', 'jobTitle', 'replacementEmployee'])
            ->orderBy('next_annual_leave_date')
            ->get();

        return view('dashboard.customer.hr.employees.annual-leave-alerts', compact('approachingLeaves', 'thresholdDays'));
    }

    public function create(string $locale): View
    {
        $tenantId = $this->ctx->tenantIdOrFail(auth()->user());

        $departments = $this->departmentsRepo->paginate($tenantId, 200);
        $jobTitles = $this->jobTitlesRepo->paginate($tenantId, 200);
        $farms = Farm::query()->where('tenant_id', $tenantId)->orderBy('name')->get();
        $employees = Employee::query()->where('tenant_id', $tenantId)->where('is_active', true)->orderBy('full_name')->get();

        return view('dashboard.customer.hr.employees.create', compact('departments', 'jobTitles', 'farms', 'employees'));
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

        $employee->load([
            'department',
            'jobTitle',
            'farm',
            'attachments',
            'replacementEmployee',
            'financialActions' => fn($q) => $q->latest(),
        ]);

        return view('dashboard.customer.hr.employees.show', compact('employee'));
    }

    public function edit(string $locale, Employee $employee): View
    {
        $this->authorizeTenant($employee);

        $tenantId = (string) auth()->user()->tenant_id;
        $departments = $this->departmentsRepo->paginate($tenantId, 200);
        $jobTitles = $this->jobTitlesRepo->paginate($tenantId, 200);
        $farms = Farm::query()->where('tenant_id', $tenantId)->orderBy('name')->get();
        $employees = Employee::query()
            ->where('tenant_id', $tenantId)
            ->where('id', '!=', $employee->id)
            ->where('is_active', true)
            ->orderBy('full_name')
            ->get();

        $employee->load('attachments');

        return view('dashboard.customer.hr.employees.edit', compact('employee', 'departments', 'jobTitles', 'farms', 'employees'));
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

    public function updateStatus(Request $request, string $locale, Employee $employee): RedirectResponse
    {
        $this->authorizeTenant($employee);

        $validated = $request->validate([
            'employment_status'       => ['required', 'string', 'in:active,on_leave,traveling,terminated'],
            'replacement_employee_id' => ['nullable', 'exists:employees,id'],
            'next_annual_leave_date'  => ['nullable', 'date'],
        ]);

        $employee->update([
            'employment_status'       => $validated['employment_status'],
            'replacement_employee_id' => $validated['replacement_employee_id'] ?? $employee->replacement_employee_id,
            'next_annual_leave_date'  => $validated['next_annual_leave_date'] ?? $employee->next_annual_leave_date,
            'is_active'               => $validated['employment_status'] !== 'terminated',
        ]);

        return redirect()->back()->with('success', __('hr.messages.success.status_updated'));
    }

    public function salaryCertificate(string $locale, Employee $employee): View
    {
        $this->authorizeTenant($employee);

        $employee->load([
            'department',
            'jobTitle',
            'farm',
            'financialActions' => fn($q) => $q->where('status', 'active'),
        ]);

        $certificateData = $employee->generateSalaryCertificate();

        return view('dashboard.customer.hr.employees.salary-certificate', compact('employee', 'certificateData'));
    }

    public function storeFinancialAction(EmployeeFinancialActionStoreRequest $request, string $locale, Employee $employee): RedirectResponse
    {
        $this->authorizeTenant($employee);

        $data = $request->validated();
        $data['tenant_id'] = $employee->tenant_id;
        $data['created_by'] = auth()->id();
        $data['status'] = 'active';

        if ($data['type'] === EmployeeFinancialAction::TYPE_ADVANCE_PAYMENT) {
            $amount = (float) ($data['amount'] ?? 0);
            $installments = (int) ($data['installments_count'] ?? 1);
            $data['installment_amount'] = $installments > 0 ? round($amount / $installments, 2) : $amount;
            $data['remaining_amount'] = $amount;
        }

        $employee->financialActions()->create($data);

        return redirect()->route('customer.hr.employees.show', ['locale' => $locale, 'employee' => $employee->id])
            ->with('success', __('hr.messages.success.financial_action_added'));
    }

    public function deleteFinancialAction(string $locale, Employee $employee, EmployeeFinancialAction $financialAction): RedirectResponse
    {
        $this->authorizeTenant($employee);

        if ((int) $financialAction->employee_id !== (int) $employee->id) {
            abort(404);
        }

        $financialAction->delete();

        return redirect()->route('customer.hr.employees.show', ['locale' => $locale, 'employee' => $employee->id])
            ->with('success', __('hr.messages.success.financial_action_deleted'));
    }

    private function authorizeTenant(Employee $employee): void
    {
        $tenantId = (string) auth()->user()->tenant_id;
        if ((string) $employee->tenant_id !== $tenantId) {
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
                'tenant_id'   => $tenantId,
                'employee_id' => $employee->id,
                'type'        => $type,
                'path'        => 'hr/employee-attachments/' . $filename,
                'uploaded_at' => now(),
            ]);
        }
    }

    private function attachmentTypeMap(): array
    {
        return [
            'attachment_passport' => 'passport',
            'attachment_iqama'    => 'iqama',
            'attachment_identity' => 'identity',
        ];
    }

    private function attachmentInputNames(): array
    {
        return array_keys($this->attachmentTypeMap());
    }
}