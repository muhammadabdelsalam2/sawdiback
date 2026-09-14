@extends('layouts.customer.dashboard')

@section('title', __('hr.titles.employee_details') . ' - ' . $employee->full_name)

@section('content')
@php
    $currentLocale = app()->getLocale();
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
            <a href="{{ route('customer.hr.employees.salary-certificate', ['locale' => $currentLocale, 'employee' => $employee->id]) }}" class="btn btn-outline-info" target="_blank">
                <i class="fas fa-file-invoice-dollar mr-1"></i> {{ __('hr.salary_certificate') }}
            </a>
            <button type="button" class="btn btn-outline-warning" data-toggle="modal" data-target="#updateStatusModal">
                <i class="fas fa-user-clock mr-1"></i> {{ __('hr.update_status_modal_title') }}
            </button>
            <a href="{{ route('customer.hr.employees.edit', ['locale' => $currentLocale, 'employee' => $employee->id]) }}" class="btn btn-primary">
                <i class="fas fa-edit mr-1"></i> {{ __('hr.actions.edit') }}
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
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
                            {{ $employee->is_active ? __('hr.options.active') : __('hr.fields.status') }}
                        </span>
                        <div class="mt-2">
                            <span class="badge badge-info">
                                {{ __('hr.status_' . ($employee->employment_status ?? 'active')) }}
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
                            <span class="font-weight-bold">{{ $employee->department->name ?? '-' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">{{ __('hr.fields.job_title') }}:</span>
                            <span class="font-weight-bold">{{ $employee->jobTitle->name ?? '-' }}</span>
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
                            <span class="text-muted">{{ __('hr.fields.email') }}:</span>
                            <span class="font-weight-bold">{{ $employee->email ?? '-' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">{{ __('hr.fields.national_id') }}:</span>
                            <span class="font-weight-bold">{{ $employee->national_id ?? '-' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">{{ __('hr.fields.passport_expiry_date') }}:</span>
                            <span class="font-weight-bold">{{ $employee->passport_expiry_date ? $employee->passport_expiry_date->format('Y-m-d') : '-' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">{{ __('hr.fields.iqama_expiry_date') }}:</span>
                            <span class="font-weight-bold">{{ $employee->iqama_expiry_date ? $employee->iqama_expiry_date->format('Y-m-d') : '-' }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- Financial and Career Record Sections --}}
        <div class="col-lg-8 col-md-12 mb-4">
            {{-- Salary Summary Cards --}}
            <div class="row mb-4">
                <div class="col-md-4 mb-3">
                    <div class="card border-0 shadow-sm bg-light-primary border-left-primary h-100 py-2">
                        <div class="card-body">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">{{ __('hr.basic_salary') }}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($employee->salary, 2) }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card border-0 shadow-sm bg-light-success border-left-success h-100 py-2">
                        <div class="card-body">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">{{ __('hr.gross_salary_label') }}</div>
                            <div class="h5 mb-0 font-weight-bold text-success">{{ number_format($employee->current_salary, 2) }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card border-0 shadow-sm bg-light-warning border-left-warning h-100 py-2">
                        <div class="card-body">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">{{ __('hr.remaining_amount') }} ({{ __('hr.advance_payment') }})</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($employee->active_loans_balance, 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Professional Profile Card --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="card-title font-weight-bold mb-0 text-dark">
                        <i class="fas fa-user-graduate text-primary mr-2"></i> {{ __('hr.work_experience') }} &amp; {{ __('hr.education') }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold text-muted d-block">{{ __('hr.education') }}:</label>
                            <p class="border rounded p-2 bg-light mb-0">{{ $employee->education ?: '-' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold text-muted d-block">{{ __('hr.work_experience') }}:</label>
                            <p class="border rounded p-2 bg-light mb-0">{{ $employee->work_experience ?: '-' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold text-muted d-block">{{ __('hr.achievements_creativity') }}:</label>
                            <p class="border rounded p-2 bg-light mb-0">{{ $employee->achievements_creativity ?: '-' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold text-muted d-block">{{ __('hr.self_development') }}:</label>
                            <p class="border rounded p-2 bg-light mb-0">{{ $employee->self_development ?: '-' }}</p>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="font-weight-bold text-danger d-block">{{ __('hr.infractions_absence_notes') }}:</label>
                            <p class="border border-danger rounded p-2 bg-light text-danger mb-0">{{ $employee->infractions_absence_notes ?: '-' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold text-muted d-block">{{ __('hr.replacement_employee') }}:</label>
                            @if($employee->replacementEmployee)
                                <span class="badge badge-secondary p-2">{{ $employee->replacementEmployee->full_name }}</span>
                            @else
                                <span class="badge badge-light text-muted p-2 border">{{ __('hr.no_replacement_set') }}</span>
                            @endif
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold text-muted d-block">{{ __('hr.next_annual_leave_date') }}:</label>
                            <span class="badge badge-info p-2">{{ $employee->next_annual_leave_date ? $employee->next_annual_leave_date->format('Y-m-d') : '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Financial Actions Table --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="card-title font-weight-bold mb-0 text-dark">
                        <i class="fas fa-hand-holding-usd text-success mr-2"></i> {{ __('hr.financial_actions') }}
                    </h5>
                    <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#addFinancialActionModal">
                        <i class="fas fa-plus mr-1"></i> {{ __('hr.add_financial_action') }}
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0 text-center">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('hr.fields.type') }}</th>
                                    <th>{{ __('hr.fields.salary') }}</th>
                                    <th>{{ __('hr.gift_description') }}</th>
                                    <th>{{ __('hr.fields.day') }}</th>
                                    <th>{{ __('hr.installments_count') }}</th>
                                    <th>{{ __('hr.remaining_amount') }}</th>
                                    <th>{{ __('hr.fields.status') }}</th>
                                    <th>{{ __('hr.fields.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($employee->financialActions as $action)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ __('hr.' . $action->type) }}</td>
                                        <td class="font-weight-bold">
                                            @if($action->amount > 0)
                                                {{ number_format($action->amount, 2) }}
                                            @elseif($action->percentage > 0)
                                                {{ $action->percentage }}%
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ $action->gift_description ?? '-' }}</td>
                                        <td>{{ $action->action_date ? $action->action_date->format('Y-m-d') : '-' }}</td>
                                        <td>{{ $action->installments_count ?? 1 }}</td>
                                        <td class="text-danger font-weight-bold">{{ number_format($action->remaining_amount, 2) }}</td>
                                        <td><span class="badge badge-info">{{ $action->status }}</span></td>
                                        <td>
                                            <form action="{{ route('customer.hr.employees.financial-actions.destroy', ['locale' => $currentLocale, 'employee' => $employee->id, 'financialAction' => $action->id]) }}" method="POST" onsubmit="return confirm('{{ __('hr.messages.confirm_delete_employee') }}')">
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
                                        <td colspan="9" class="py-4 text-muted">{{ __('hr.empty.no_actions') }}</td>
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

{{-- Modal: Update Status and Replacement --}}
<div class="modal fade" id="updateStatusModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form action="{{ route('customer.hr.employees.update-status', ['locale' => $currentLocale, 'employee' => $employee->id]) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold">{{ __('hr.update_status_modal_title') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label class="font-weight-bold">{{ __('hr.fields.status') }}</label>
                        <select name="employment_status" class="form-control" required>
                            <option value="active" {{ $employee->employment_status === 'active' ? 'selected' : '' }}>{{ __('hr.status_active') }}</option>
                            <option value="on_leave" {{ $employee->employment_status === 'on_leave' ? 'selected' : '' }}>{{ __('hr.status_on_leave') }}</option>
                            <option value="traveling" {{ $employee->employment_status === 'traveling' ? 'selected' : '' }}>{{ __('hr.status_traveling') }}</option>
                            <option value="terminated" {{ $employee->employment_status === 'terminated' ? 'selected' : '' }}>{{ __('hr.status_terminated') }}</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold">{{ __('hr.replacement_employee') }}</label>
                        <select name="replacement_employee_id" class="form-control">
                            <option value="">-- {{ __('hr.none') }} --</option>
                            @foreach(\App\Models\Employee::where('tenant_id', $employee->tenant_id)->where('id', '!=', $employee->id)->where('is_active', true)->get() as $otherEmp)
                                <option value="{{ $otherEmp->id }}" {{ $employee->replacement_employee_id == $otherEmp->id ? 'selected' : '' }}>
                                    {{ $otherEmp->full_name }} ({{ $otherEmp->worker_number ?? $otherEmp->id }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-0">
                        <label class="font-weight-bold">{{ __('hr.next_annual_leave_date') }}</label>
                        <input type="date" name="next_annual_leave_date" class="form-control" value="{{ $employee->next_annual_leave_date ? $employee->next_annual_leave_date->format('Y-m-d') : '' }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('hr.actions.cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('hr.actions.save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal: Add Financial Action --}}
<div class="modal fade" id="addFinancialActionModal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form action="{{ route('customer.hr.employees.financial-actions.store', ['locale' => $currentLocale, 'employee' => $employee->id]) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold" id="modalLabel">{{ __('hr.add_financial_action') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label class="font-weight-bold">{{ __('hr.fields.type') }} <span class="text-danger">*</span></label>
                        <select name="type" class="form-control" required id="actionTypeSelector">
                            <option value="advance_payment">{{ __('hr.advance_payment') }}</option>
                            <option value="monthly_deduction">{{ __('hr.monthly_deduction') }}</option>
                            <option value="salary_increase_fixed">{{ __('hr.salary_increase_fixed') }}</option>
                            <option value="salary_increase_percent">{{ __('hr.salary_increase_percent') }}</option>
                            <option value="financial_bonus">{{ __('hr.financial_bonus') }}</option>
                            <option value="in_kind_gift">{{ __('hr.in_kind_gift') }}</option>
                        </select>
                    </div>

                    <div class="form-group mb-3" id="inputAmountWrapper">
                        <label class="font-weight-bold">{{ __('hr.fields.salary') }}</label>
                        <input type="number" step="0.01" name="amount" class="form-control" placeholder="0.00">
                    </div>

                    <div class="form-group mb-3 d-none" id="inputPercentWrapper">
                        <label class="font-weight-bold">{{ __('hr.salary_increase_percent') }} (%)</label>
                        <input type="number" step="0.01" name="percentage" class="form-control" placeholder="0.00">
                    </div>

                    <div class="form-group mb-3 d-none" id="inputGiftWrapper">
                        <label class="font-weight-bold">{{ __('hr.gift_description') }}</label>
                        <input type="text" name="gift_description" class="form-control" placeholder="{{ __('hr.gift_description') }}">
                    </div>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold">{{ __('hr.fields.day') }} <span class="text-danger">*</span></label>
                        <input type="date" name="action_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="form-group mb-3" id="inputInstallmentsWrapper">
                        <label class="font-weight-bold">{{ __('hr.installments_count') }}</label>
                        <input type="number" name="installments_count" class="form-control" value="1" min="1">
                    </div>

                    <div class="form-group mb-0">
                        <label class="font-weight-bold">{{ __('hr.fields.reason') }}</label>
                        <textarea name="notes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('hr.actions.cancel') }}</button>
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

    function toggleFields() {
        const val = typeSelect.value;

        if (val === 'salary_increase_percent') {
            percentWrap.classList.remove('d-none');
            amountWrap.classList.add('d-none');
            giftWrap.classList.add('d-none');
            installmentsWrap.classList.add('d-none');
        } else if (val === 'in_kind_gift') {
            giftWrap.classList.remove('d-none');
            amountWrap.classList.remove('d-none');
            percentWrap.classList.add('d-none');
            installmentsWrap.classList.add('d-none');
        } else if (val === 'advance_payment') {
            amountWrap.classList.remove('d-none');
            installmentsWrap.classList.remove('d-none');
            percentWrap.classList.add('d-none');
            giftWrap.classList.add('d-none');
        } else {
            amountWrap.classList.remove('d-none');
            percentWrap.classList.add('d-none');
            giftWrap.classList.add('d-none');
            installmentsWrap.classList.add('d-none');
        }
    }

    typeSelect.addEventListener('change', toggleFields);
    toggleFields();
});
</script>
@endsection