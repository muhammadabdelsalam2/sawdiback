@extends('layouts.customer.dashboard')

@section('title', __('hr.titles.employee_details') . ' - ' . $employee->full_name)

@section('content')
    @php
        $currentLocale = request()->route('locale') ?? app()->getLocale();
    @endphp

    <div class="container-fluid my-4">
        {{-- Top Action Bar --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
            <div>
                <h2 class="h3 font-weight-bold text-gray-800 mb-1">{{ $employee->full_name }}</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent p-0 mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('customer.hr.employees.index', ['locale' => $currentLocale]) }}">{{ __('hr.titles.employees') }}</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $employee->worker_number ?? $employee->id }}</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('customer.hr.employees.index', ['locale' => $currentLocale]) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-1"></i> {{ __('hr.actions.back') }}
                </a>
                <a href="{{ url($currentLocale . '/hr/employees/' . $employee->id . '/salary-certificate') }}" class="btn btn-outline-info" target="_blank">
                    <i class="fas fa-file-invoice-dollar mr-1"></i> {{ __('hr.salary_certificate') ?? 'شهادة الراتب' }}
                </a>
                <button type="button" class="btn btn-outline-warning" data-toggle="modal" data-target="#updateStatusModal" data-bs-toggle="modal" data-bs-target="#updateStatusModal">
                    <i class="fas fa-user-clock mr-1"></i> {{ __('hr.update_status_modal_title') ?? 'تحديث الحالة والبديل' }}
                </button>
                <a href="{{ route('customer.hr.employees.edit', ['locale' => $currentLocale, 'employee' => $employee->id]) }}" class="btn btn-primary">
                    <i class="fas fa-edit mr-1"></i> {{ __('hr.actions.edit') }}
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close btn-close" data-dismiss="alert" data-bs-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <div class="row">
            {{-- Employee Details Sidebar Card --}}
            <div class="col-lg-4 col-md-12 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0"><i class="fas fa-id-card mr-2"></i> {{ __('hr.titles.employee_details') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <div class="avatar bg-light text-primary rounded-circle d-inline-flex justify-content-center align-items-center mb-2" style="width: 80px; height: 80px; font-size: 32px; font-weight: bold;">
                                {{ mb_substr($employee->full_name, 0, 1) }}
                            </div>
                            <h5 class="font-weight-bold mb-1">{{ $employee->full_name }}</h5>
                            <span class="badge badge-{{ $employee->is_active ? 'success' : 'danger' }} px-3 py-1">
                                {{ $employee->is_active ? (__('hr.options.active') ?? 'نشط') : (__('hr.options.inactive') ?? 'غير نشط') }}
                            </span>
                            <div class="mt-2">
                                <span class="badge badge-info px-2 py-1">
                                    {{ match($employee->employment_status) {
                                        'on_leave' => 'في إجازة سنوية',
                                        'traveling' => 'مسافر / بالخارج',
                                        'terminated' => 'موقوف / مستقيل',
                                        default => 'على رأس العمل (نشط)'
                                    } }}
                                </span>
                            </div>
                        </div>

                        <ul class="list-group list-group-flush border-top">
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span class="text-muted">{{ __('hr.fields.worker_number') }}:</span>
                                <span class="font-weight-bold">{{ $employee->worker_number ?? '-' }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span class="text-muted">{{ __('hr.fields.department') }}:</span>
                                <span class="font-weight-bold">{{ $employee->department->name ?? ($employee->operational_department ?? '-') }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span class="text-muted">{{ __('hr.fields.job_title') }}:</span>
                                <span class="font-weight-bold">{{ $employee->jobTitle->name ?? ($employee->profession ?? '-') }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span class="text-muted">{{ __('hr.fields.farm') }}:</span>
                                <span class="font-weight-bold">{{ $employee->farm->name ?? '-' }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span class="text-muted">{{ __('hr.fields.hire_date') }}:</span>
                                <span class="font-weight-bold">{{ $employee->hire_date ? $employee->hire_date->format('Y-m-d') : '-' }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span class="text-muted">{{ __('hr.fields.phone') }}:</span>
                                <span class="font-weight-bold">{{ $employee->phone ?? '-' }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span class="text-muted">{{ __('hr.fields.national_id') }}:</span>
                                <span class="font-weight-bold">{{ $employee->national_id ?? '-' }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span class="text-muted">{{ __('hr.replacement_employee') ?? 'الموظف البديل' }}:</span>
                                <span class="font-weight-bold text-primary">{{ $employee->replacementEmployee?->full_name ?? 'لا يوجد بديل' }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span class="text-muted">{{ __('hr.next_annual_leave_date') ?? 'الإجازة السنوية' }}:</span>
                                <span class="font-weight-bold text-info">{{ $employee->next_annual_leave_date ? $employee->next_annual_leave_date->format('Y-m-d') : '-' }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Main Sections --}}
            <div class="col-lg-8 col-md-12 mb-4">
                {{-- Salary Summary Cards --}}
                @php
                    $baseSalary = (float) ($employee->basic_salary ?? $employee->salary ?? 0);
                    $increasesTotal = (float) $employee->financialActions
                        ->where('type', 'salary_increase')
                        ->where('status', 'active')
                        ->sum('amount');
                    $grossSalary = $baseSalary + $increasesTotal;
                    $remainingLoans = (float) $employee->financialActions
                        ->where('type', 'advance_payment')
                        ->where('status', 'active')
                        ->sum('remaining_amount');
                @endphp
                <div class="row mb-4">
                    <div class="col-md-4 mb-3">
                        <div class="card border-0 shadow-sm border-left-primary h-100 py-2">
                            <div class="card-body">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">{{ __('hr.basic_salary') ?? 'الراتب الأساسي' }}</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($baseSalary, 2) }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card border-0 shadow-sm border-left-success h-100 py-2">
                            <div class="card-body">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">{{ __('hr.gross_salary_label') ?? 'إجمالي الراتب' }}</div>
                                <div class="h5 mb-0 font-weight-bold text-success">{{ number_format($grossSalary, 2) }}</div>
                                @if($increasesTotal > 0)
                                    <small class="text-muted d-block mt-1">+{{ number_format($increasesTotal, 2) }} زيادة معتمدة</small>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card border-0 shadow-sm border-left-warning h-100 py-2">
                            <div class="card-body">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">{{ __('hr.remaining_amount') ?? 'المتبقي من السلف' }}</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($remainingLoans, 2) }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 1. Sijil Al-Muwadhaf: Achievements, Qualifications, Experience, Violations --}}
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                        <h5 class="card-title font-weight-bold mb-0 text-dark">
                            <i class="fas fa-medal text-warning mr-2"></i> سجل الموظف الشامل (إنجازات، خبرات، شهادات، ومخالفات)
                        </h5>
                        <button type="button" class="btn btn-sm btn-outline-warning" data-toggle="modal" data-target="#addRecordModal" data-bs-toggle="modal" data-bs-target="#addRecordModal">
                            <i class="fas fa-plus mr-1"></i> إضافة بند للسجل
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped mb-0 text-center align-middle">
                                <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>النوع</th>
                                    <th>العنوان</th>
                                    <th>التفاصيل والملاحظات</th>
                                    <th>التاريخ</th>
                                    <th>الإجراءات</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($employee->records ?? [] as $rec)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            @php
                                                $badgeClass = match($rec->record_type) {
                                                    'achievement' => 'badge-success',
                                                    'qualification' => 'badge-info',
                                                    'experience' => 'badge-primary',
                                                    'training' => 'badge-secondary',
                                                    'violation' => 'badge-danger',
                                                    default => 'badge-light border'
                                                };
                                                $recordLabel = match($rec->record_type) {
                                                    'achievement' => 'إنجاز وإبداع',
                                                    'qualification' => 'شهادة علمية',
                                                    'experience' => 'خبرة سابقة',
                                                    'training' => 'تطوير مستمر',
                                                    'violation' => 'تهرب / مخالفة',
                                                    default => 'ملاحظة'
                                                };
                                            @endphp
                                            <span class="badge {{ $badgeClass }} px-2 py-1">{{ $recordLabel }}</span>
                                        </td>
                                        <td class="font-weight-bold text-dark">{{ $rec->title }}</td>
                                        <td>{{ $rec->description ?: '-' }}</td>
                                        <td>{{ $rec->event_date ? (is_string($rec->event_date) ? $rec->event_date : $rec->event_date->format('Y-m-d')) : '-' }}</td>
                                        <td>
                                            <form action="{{ route('customer.hr.employees.records.destroy', ['locale' => $currentLocale, 'employee' => $employee->id, 'record' => $rec->id]) }}" method="POST" onsubmit="return confirm('هل تريد حذف هذا البند من السجل؟')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="حذف">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-4 text-muted">لا توجد بنود مسجلة في سجل إنجازات وخبرات الموظف حتى الآن.</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- 2. Financial Actions Table: Loans, Deductions, Bonuses, Increases, Gifts --}}
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                        <h5 class="card-title font-weight-bold mb-0 text-dark">
                            <i class="fas fa-hand-holding-usd text-success mr-2"></i> {{ __('hr.financial_actions') ?? 'السلف، الاستقطاعات، المكافآت، والزيادات' }}
                        </h5>
                        <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#addFinancialActionModal" data-bs-toggle="modal" data-bs-target="#addFinancialActionModal">
                            <i class="fas fa-plus mr-1"></i> {{ __('hr.add_financial_action') ?? 'إضافة حركة مالية' }}
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped mb-0 text-center align-middle">
                                <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>النوع</th>
                                    <th>المبلغ / النسبة</th>
                                    <th>البيان / الهدية العينية</th>
                                    <th>تاريخ السريان</th>
                                    <th>الأقساط</th>
                                    <th>المتبقي</th>
                                    <th>الحالة</th>
                                    <th>الإجراءات</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($employee->financialActions ?? [] as $action)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                                <span class="badge badge-light border">
                                                    {{ match($action->type) {
                                                        'advance_payment' => 'سلفة مالية',
                                                        'deduction' => 'استقطاع شهري',
                                                        'salary_increase' => 'زيادة راتب',
                                                        'bonus' => 'مكافأة مالية',
                                                        'gift' => 'هدية عينية',
                                                        default => $action->type
                                                    } }}
                                                </span>
                                        </td>
                                        <td class="font-weight-bold">
                                            @if($action->percentage > 0)
                                                <span class="text-primary">{{ $action->percentage }}%</span>
                                            @elseif($action->amount > 0)
                                                <span class="{{ in_array($action->type, ['advance_payment', 'deduction']) ? 'text-danger' : 'text-success' }}">
                                                        {{ number_format((float) $action->amount, 2) }}
                                                    </span>
                                                @if($action->installment_amount > 0 && $action->installments_count > 1)
                                                    <small class="text-muted d-block">({{ number_format((float) $action->installment_amount, 2) }}/شهر)</small>
                                                @endif
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ $action->item_name ?: ($action->notes ?: '-') }}</td>
                                        <td>{{ $action->effective_date ? (is_string($action->effective_date) ? $action->effective_date : $action->effective_date->format('Y-m-d')) : '-' }}</td>
                                        <td>{{ $action->installments_count ?? 1 }}</td>
                                        <td class="text-danger font-weight-bold">
                                            {{ $action->remaining_amount > 0 ? number_format((float) $action->remaining_amount, 2) : '-' }}
                                        </td>
                                        <td>
                                                <span class="badge badge-{{ $action->status === 'active' ? 'success' : 'secondary' }}">
                                                    {{ $action->status === 'active' ? 'ساري' : 'منتهي' }}
                                                </span>
                                        </td>
                                        <td>
                                            <form action="{{ route('customer.hr.employees.financial-actions.destroy', ['locale' => $currentLocale, 'employee' => $employee->id, 'financialAction' => $action->id]) }}" method="POST" onsubmit="return confirm('{{ __('hr.messages.confirm_delete_action') ?? 'هل أنت متأكد من حذف هذا السجل المالي؟' }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="{{ __('hr.actions.delete') }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="py-4 text-muted">{{ __('hr.empty.no_actions') ?? 'لا توجد حركات مالية مسجلة لهذا الموظف.' }}</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal 1: Update Status and Replacement --}}
    <div class="modal fade" id="updateStatusModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form action="{{ route('customer.hr.employees.update-status', ['locale' => $currentLocale, 'employee' => $employee->id]) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="modal-header">
                        <h5 class="modal-title font-weight-bold">{{ __('hr.update_status_modal_title') ?? 'تحديث حالة الموظف والبديل' }}</h5>
                        <button type="button" class="close btn-close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label class="font-weight-bold">{{ __('hr.fields.status') ?? 'الحالة الوظيفية' }}</label>
                            <select name="employment_status" class="form-control" required>
                                <option value="active" {{ $employee->employment_status === 'active' ? 'selected' : '' }}>نشط (Active)</option>
                                <option value="on_leave" {{ $employee->employment_status === 'on_leave' ? 'selected' : '' }}>في إجازة سنوية (On Leave)</option>
                                <option value="traveling" {{ $employee->employment_status === 'traveling' ? 'selected' : '' }}>سفر / بالخارج (Traveling)</option>
                                <option value="terminated" {{ $employee->employment_status === 'terminated' ? 'selected' : '' }}>موقوف / مستقيل (Terminated)</option>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label class="font-weight-bold">{{ __('hr.replacement_employee') ?? 'الموظف البديل أثناء الإجازة' }}</label>
                            <select name="replacement_employee_id" class="form-control">
                                <option value="">-- {{ __('hr.none') ?? 'لا يوجد بديل' }} --</option>
                                @foreach(\App\Models\Employee::where('tenant_id', $employee->tenant_id)->where('id', '!=', $employee->id)->where('is_active', true)->get() as $otherEmp)
                                    <option value="{{ $otherEmp->id }}" {{ $employee->replacement_employee_id == $otherEmp->id ? 'selected' : '' }}>
                                        {{ $otherEmp->full_name }} ({{ $otherEmp->worker_number ?? $otherEmp->id }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-0">
                            <label class="font-weight-bold">{{ __('hr.next_annual_leave_date') ?? 'موعد الإجازة السنوية القادمة' }}</label>
                            <input type="date" name="next_annual_leave_date" class="form-control" value="{{ $employee->next_annual_leave_date ? (is_string($employee->next_annual_leave_date) ? $employee->next_annual_leave_date : $employee->next_annual_leave_date->format('Y-m-d')) : '' }}">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal">{{ __('hr.actions.cancel') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('hr.actions.save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal 2: Add Financial Action --}}
    <div class="modal fade" id="addFinancialActionModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form action="{{ route('customer.hr.employees.financial-actions.store', ['locale' => $currentLocale, 'employee' => $employee->id]) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title font-weight-bold">{{ __('hr.add_financial_action') ?? 'إضافة حركة مالية' }}</h5>
                        <button type="button" class="close btn-close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label class="font-weight-bold">نوع الحركة المالية <span class="text-danger">*</span></label>
                            <select name="type" class="form-control" required id="actionTypeSelector">
                                <option value="advance_payment">سلفة مالية (Advance Payment)</option>
                                <option value="deduction">استقطاع شهري (Deduction)</option>
                                <option value="salary_increase">زيادة راتب (Salary Increase)</option>
                                <option value="bonus">مكافأة مالية (Bonus)</option>
                                <option value="gift">هدية عينية (In-Kind Gift)</option>
                            </select>
                        </div>

                        <div class="form-group mb-3" id="inputAmountWrapper">
                            <label class="font-weight-bold" id="labelAmount">المبلغ</label>
                            <input type="number" step="0.01" min="0" name="amount" class="form-control" placeholder="0.00">
                        </div>

                        <div class="form-group mb-3 d-none" id="inputPercentWrapper">
                            <label class="font-weight-bold">نسبة الزيادة على الراتب (%)</label>
                            <input type="number" step="0.01" min="0.01" max="100" name="percentage" class="form-control" placeholder="مثال: 10%">
                        </div>

                        <div class="form-group mb-3 d-none" id="inputGiftWrapper">
                            <label class="font-weight-bold">البيان / الهدية العينية</label>
                            <input type="text" name="item_name" class="form-control" placeholder="مثال: درع تكريم، هاتف، أو سبب المكافأة">
                        </div>

                        <div class="form-group mb-3">
                            <label class="font-weight-bold">تاريخ السريان <span class="text-danger">*</span></label>
                            <input type="date" name="effective_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>

                        <div class="form-group mb-3" id="inputInstallmentsWrapper">
                            <label class="font-weight-bold">عدد شهور السداد (الأقساط)</label>
                            <input type="number" name="installments_count" class="form-control" value="1" min="1">
                            <small class="text-muted">سيتم تقسيم السلفة تلقائياً على هذا العدد كقسط شهري.</small>
                        </div>

                        <div class="form-group mb-0">
                            <label class="font-weight-bold">ملاحظات</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="أدخل أي ملاحظات..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal">{{ __('hr.actions.cancel') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('hr.actions.save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal 3: Add Employee Record (Achievements, Experience, Violations) --}}
    <div class="modal fade" id="addRecordModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form action="{{ route('customer.hr.employees.records.store', ['locale' => $currentLocale, 'employee' => $employee->id]) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title font-weight-bold">إضافة بند لسجل الموظف</h5>
                        <button type="button" class="close btn-close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label class="font-weight-bold">نوع البند <span class="text-danger">*</span></label>
                            <select name="record_type" class="form-control" required>
                                <option value="achievement">إنجاز أو إبداع مهني</option>
                                <option value="qualification">شهادة علمية / مؤهل</option>
                                <option value="experience">خبرة عملية سابقة</option>
                                <option value="training">تطوير مستمر ودورات</option>
                                <option value="violation">تهرب من العمل ومخالفة</option>
                                <option value="note">ملاحظة عامة</option>
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label class="font-weight-bold">العنوان <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" placeholder="مثال: تطوير نظام الري، بكالوريوس زراعة..." required>
                        </div>

                        <div class="form-group mb-3">
                            <label class="font-weight-bold">التاريخ <span class="text-danger">*</span></label>
                            <input type="date" name="event_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>

                        <div class="form-group mb-0">
                            <label class="font-weight-bold">التفاصيل والشرح</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="اكتب تفاصيل الإنجاز، الخبرة أو سبب المخالفة..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal">{{ __('hr.actions.cancel') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('hr.actions.save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const typeSelect = document.getElementById('actionTypeSelector');
            const amountWrap = document.getElementById('inputAmountWrapper');
            const percentWrap = document.getElementById('inputPercentWrapper');
            const giftWrap = document.getElementById('inputGiftWrapper');
            const installmentsWrap = document.getElementById('inputInstallmentsWrapper');
            const labelAmount = document.getElementById('labelAmount');

            function toggleFields() {
                if (!typeSelect) return;
                const val = typeSelect.value;

                if (val === 'gift') {
                    giftWrap.classList.remove('d-none');
                    amountWrap.classList.remove('d-none');
                    percentWrap.classList.add('d-none');
                    installmentsWrap.classList.add('d-none');
                    if (labelAmount) labelAmount.textContent = 'القيمة التقديرية للهدية (اختياري)';
                } else if (val === 'advance_payment') {
                    amountWrap.classList.remove('d-none');
                    installmentsWrap.classList.remove('d-none');
                    percentWrap.classList.add('d-none');
                    giftWrap.classList.add('d-none');
                    if (labelAmount) labelAmount.textContent = 'مبلغ السلفة الإجمالي';
                } else {
                    amountWrap.classList.remove('d-none');
                    percentWrap.classList.add('d-none');
                    giftWrap.classList.add('d-none');
                    installmentsWrap.classList.add('d-none');
                    if (labelAmount) labelAmount.textContent = 'المبلغ';
                }
            }

            if (typeSelect) {
                typeSelect.addEventListener('change', toggleFields);
                toggleFields();
            }
        });
    </script>
@endsection
