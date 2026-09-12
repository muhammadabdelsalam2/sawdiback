@extends('layouts.customer.dashboard')

@section('title', __('hr.salary_certificate') . ' - ' . $employee->full_name)

@section('content')
<div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <a href="{{ route('customer.hr.employees.show', $employee->id) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-right"></i> {{ __('app.back') }}
        </a>
        <button onclick="window.print()" class="btn btn-primary">
            <i class="fas fa-print"></i> {{ __('hr.print_certificate') }}
        </button>
    </div>

    <div class="card p-5 shadow-sm border certificate-box bg-white">
        <div class="text-center mb-4 border-bottom pb-3">
            <h2 class="font-weight-bold text-uppercase">{{ __('hr.salary_certificate_title') }}</h2>
            <p class="text-muted">{{ __('hr.to_whom_it_may_concern') }}</p>
        </div>

        <div class="mb-4">
            <p style="font-size: 1.15rem; line-height: 2;">
                {{ __('hr.salary_certificate_body', [
                    'company' => auth()->user()->tenant->name ?? 'مزرعة السوادي',
                    'name' => $employee->full_name,
                    'emp_id' => $employee->worker_number ?? $employee->id,
                    'job' => $employee->jobTitle->name ?? __('hr.unspecified'),
                    'dept' => $employee->department->name ?? __('hr.unspecified'),
                    'hire_date' => $employee->hire_date ? $employee->hire_date->format('Y-m-d') : '-'
                ]) }}
            </p>
        </div>

        <div class="table-responsive my-3">
            <table class="table table-bordered text-center">
                <thead class="thead-light">
                    <tr>
                        <th>{{ __('hr.basic_salary') }}</th>
                        <th>{{ __('hr.allowances_and_increments') }}</th>
                        <th>{{ __('hr.gross_salary') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="font-weight-bold">
                        <td>{{ number_format($employee->salary, 2) }}</td>
                        <td>{{ number_format($employee->active_increases_total, 2) }}</td>
                        <td class="text-success">{{ number_format($employee->current_salary, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <p class="mt-4 text-muted" style="font-size: 0.95rem;">
            {{ __('hr.certificate_disclaimer') }}
        </p>

        <div class="row mt-5 pt-4 text-center">
            <div class="col-6">
                <h6>{{ __('hr.hr_manager') }}</h6>
                <div style="height: 60px;"></div>
                <p>_______________________</p>
            </div>
            <div class="col-6">
                <h6>{{ __('hr.company_stamp') }}</h6>
                <div style="height: 60px;"></div>
                <p>_______________________</p>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .no-print, .main-sidebar, .main-header, .main-footer {
        display: none !important;
    }
    .certificate-box {
        border: none !important;
        box-shadow: none !important;
        margin: 0 !important;
        padding: 0 !important;
    }
}
</style>
@endsection