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

    public function documentAlerts(Request $request, string $locale): View
    {
        $tenantId = (string) auth()->user()->tenant_id;
        $currentLocale = $locale;
        $today = \Carbon\Carbon::today();
        $thresholdDate = $today->copy()->addDays(60);

        // فحص الموظفين النشطين الذين تقترب وثائقهم من الانتهاء
        // (مثل: national_id_expiry, passport_expiry, contract_end_date, residency_expiry)
        $employees = Employee::query()
            ->where('tenant_id', $tenantId)
            ->where('status', 'active')
            ->with(['department:id,name', 'jobTitle:id,name'])
            ->get()
            ->map(function ($employee) use ($today, $thresholdDate) {
                $expiringDocs = [];

                // 1. انتهاء الهوية / الإقامة
                if ($employee->id_expiry_date) {
                    $expiry = \Carbon\Carbon::parse($employee->id_expiry_date);
                    if ($expiry->lessThanOrEqualTo($thresholdDate)) {
                        $expiringDocs[] = [
                            'doc_name' => 'بطاقة الهوية / الإقامة',
                            'doc_name_en' => 'National ID / Residency',
                            'doc_number' => $employee->national_id ?? $employee->id_number ?? '-',
                            'expiry_date' => $expiry,
                            'days_remaining' => (int) $today->diffInDays($expiry, false),
                            'is_expired' => $expiry->isPast(),
                        ];
                    }
                }

                // 2. انتهاء عقد العمل
                if ($employee->contract_end_date) {
                    $contractExpiry = \Carbon\Carbon::parse($employee->contract_end_date);
                    if ($contractExpiry->lessThanOrEqualTo($thresholdDate)) {
                        $expiringDocs[] = [
                            'doc_name' => 'عقد العمل',
                            'doc_name_en' => 'Employment Contract',
                            'doc_number' => $employee->contract_number ?? '-',
                            'expiry_date' => $contractExpiry,
                            'days_remaining' => (int) $today->diffInDays($contractExpiry, false),
                            'is_expired' => $contractExpiry->isPast(),
                        ];
                    }
                }

                // 3. جواز السفر (إن وجد الحقل)
                if (isset($employee->passport_expiry_date) && $employee->passport_expiry_date) {
                    $passExpiry = \Carbon\Carbon::parse($employee->passport_expiry_date);
                    if ($passExpiry->lessThanOrEqualTo($thresholdDate)) {
                        $expiringDocs[] = [
                            'doc_name' => 'جواز السفر',
                            'doc_name_en' => 'Passport',
                            'doc_number' => $employee->passport_number ?? '-',
                            'expiry_date' => $passExpiry,
                            'days_remaining' => (int) $today->diffInDays($passExpiry, false),
                            'is_expired' => $passExpiry->isPast(),
                        ];
                    }
                }

                $employee->expiring_documents = $expiringDocs;
                return $employee;
            })
            ->filter(function ($emp) {
                return !empty($emp->expiring_documents);
            })
            ->values();

        return view('dashboard.customer.hr.employees.document-alerts', compact('employees', 'currentLocale'));
    }
    public function annualLeaveAlerts(Request $request, string $locale): View
    {
        $tenantId = (string) auth()->user()->tenant_id;
        $currentLocale = $locale;

        // جلب الموظفين النشطين مع بيانات القسم والمسمى الوظيفي
        $employees = Employee::query()
            ->where('tenant_id', $tenantId)
            ->where('status', 'active')
            ->with(['department:id,name', 'jobTitle:id,name'])
            ->get()
            ->map(function ($employee) {
                if (!$employee->hire_date) {
                    return null;
                }

                // حساب موعد الإجازة السنوية القادم بناءً على تاريخ التعيين
                $hireDate = \Carbon\Carbon::parse($employee->hire_date);
                $today = \Carbon\Carbon::today();

                $nextDueDate = $hireDate->copy()->year($today->year);
                if ($nextDueDate->isPast()) {
                    $nextDueDate->addYear();
                }

                $daysRemaining = (int) $today->diffInDays($nextDueDate, false);

                $employee->next_annual_leave_date = $nextDueDate;
                $employee->days_until_leave = $daysRemaining;

                return $employee;
            })
            ->filter(function ($emp) {
                // عرض الموظفين الذين موعد إجازتهم خلال الـ 60 يوماً القادمة
                return $emp && $emp->days_until_leave <= 60;
            })
            ->sortBy('days_until_leave')
            ->values();

        return view('dashboard.customer.hr.employees.annual-leave-alerts', compact('employees', 'currentLocale'));    }
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
        $tenantId = (string) auth()->user()->tenant_id;
        if ((string) $employee->tenant_id !== $tenantId) {
            abort(403);
        }

        $employee->load([
            'department:id,name',
            'jobTitle:id,name',
        ]);

        // الراتب الأساسي
        $baseSalary = (float) ($employee->basic_salary ?? $employee->salary ?? 0);
        $salaryIncreases = 0.0;
        $monthlyLoanDeduction = 0.0;

        // فحص آمن لجدول الحركات المالية إن وجد
        if (method_exists($employee, 'financialActions')) {
            $relation = $employee->financialActions();
            $relatedTable = $relation->getRelated()->getTable();

            // تحديد اسم عمود نوع الحركة (type أو action_type)
            $typeCol = \Illuminate\Support\Facades\Schema::hasColumn($relatedTable, 'type')
                ? 'type'
                : (\Illuminate\Support\Facades\Schema::hasColumn($relatedTable, 'action_type') ? 'action_type' : null);

            $hasStatusCol = \Illuminate\Support\Facades\Schema::hasColumn($relatedTable, 'status');
            $hasInstallmentCol = \Illuminate\Support\Facades\Schema::hasColumn($relatedTable, 'monthly_installment');

            if ($typeCol) {
                // حساب الزيادات والبدلات المستمرة
                $increasesQuery = $employee->financialActions()->whereIn($typeCol, ['increase', 'allowance', 'bonus']);
                if ($hasStatusCol) {
                    $increasesQuery->whereIn('status', ['approved', 'active']);
                }
                $salaryIncreases = (float) $increasesQuery->sum('amount');

                // حساب استقطاع السلف الشهري
                if ($hasInstallmentCol) {
                    $loansQuery = $employee->financialActions()->whereIn($typeCol, ['loan', 'advance']);
                    if ($hasStatusCol) {
                        $loansQuery->whereIn('status', ['active', 'approved']);
                    }
                    $monthlyLoanDeduction = (float) $loansQuery->sum('monthly_installment');
                }
            }
        }

        $totalSalary = $baseSalary + $salaryIncreases;
        $netSalary = max(0, $totalSalary - $monthlyLoanDeduction);

        $certificateData = [
            'full_name'              => $employee->name ?? trim(($employee->first_name ?? '') . ' ' . ($employee->last_name ?? '')),
            'worker_number'          => $employee->employee_code ?? $employee->worker_number ?? ('#' . $employee->id),
            'national_id'            => $employee->national_id ?? $employee->id_number ?? '-',
            'profession'             => $employee->jobTitle?->name ?? $employee->profession ?? '-',
            'department'             => $employee->department?->name ?? '-',
            'hire_date'              => $employee->hire_date ? \Carbon\Carbon::parse($employee->hire_date)->format('Y-m-d') : '-',
            'issue_date'             => \Carbon\Carbon::today()->format('Y-m-d'),
            'base_salary'            => $baseSalary,
            'salary_increases'       => $salaryIncreases,
            'total_salary'           => $totalSalary,
            'monthly_loan_deduction' => $monthlyLoanDeduction,
            'net_salary'             => $netSalary,
        ];

        $currentLocale = $locale;

        return view('dashboard.customer.hr.employees.salary-certificate', compact('employee', 'certificateData', 'currentLocale'));
    }    public function storeFinancialAction(EmployeeFinancialActionStoreRequest $request, string $locale, Employee $employee): RedirectResponse
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
