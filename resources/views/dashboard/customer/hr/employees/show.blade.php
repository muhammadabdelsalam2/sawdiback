@extends('layouts.customer.dashboard')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">{{ __('hr.titles.employee_details') }}</h3>
        <div class="d-flex gap-2">
            <a class="btn btn-outline-secondary"
               href="{{ route('customer.hr.employees.index', ['locale' => request()->route('locale')]) }}">
                {{ __('hr.actions.back') }}
            </a>
            <a class="btn btn-primary"
               href="{{ route('customer.hr.employees.edit', ['locale' => request()->route('locale'), 'employee' => $employee->id]) }}">
                {{ __('hr.actions.edit') }}
            </a>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="text-muted small">{{ __('hr.fields.full_name') }}</div>
                    <div class="fw-semibold">{{ $employee->full_name }}</div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="text-muted small">{{ __('hr.fields.worker_number') }}</div>
                    <div class="fw-semibold">{{ $employee->worker_number ?? '-' }}</div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="text-muted small">{{ __('hr.fields.employment_status') }}</div>
                    <div class="fw-semibold">{{ __('hr.options.' . $employee->employment_status) }}</div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="text-muted small">{{ __('hr.fields.profession') }}</div>
                    <div class="fw-semibold">{{ $employee->profession ?? '-' }}</div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="text-muted small">{{ __('hr.fields.operational_department') }}</div>
                    <div class="fw-semibold">{{ $employee->operational_department ? __('hr.options.' . $employee->operational_department) : '-' }}</div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="text-muted small">{{ __('hr.fields.department') }}</div>
                    <div class="fw-semibold">{{ $employee->department?->name ?? '-' }}</div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="text-muted small">{{ __('hr.fields.job_title') }}</div>
                    <div class="fw-semibold">{{ $employee->jobTitle?->name ?? '-' }}</div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="text-muted small">{{ __('hr.fields.hire_date') }}</div>
                    <div class="fw-semibold">{{ $employee->hire_date?->format('Y-m-d') ?? '-' }}</div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="text-muted small">{{ __('hr.fields.salary') }}</div>
                    <div class="fw-semibold">{{ $employee->salary ?? '-' }}</div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="text-muted small">{{ __('hr.fields.passport_expiry_date') }}</div>
                    <div class="fw-semibold">{{ $employee->passport_expiry_date?->format('Y-m-d') ?? '-' }}</div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="text-muted small">{{ __('hr.fields.iqama_expiry_date') }}</div>
                    <div class="fw-semibold">{{ $employee->iqama_expiry_date?->format('Y-m-d') ?? '-' }}</div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="text-muted small">{{ __('hr.fields.active') }}</div>
                    <div class="fw-semibold">{{ $employee->is_active ? __('hr.options.yes') : __('hr.options.no') }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h5>{{ __('hr.titles.attachments') }}</h5>
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>{{ __('hr.fields.attachment_type') }}</th>
                        <th>{{ __('hr.fields.uploaded_at') }}</th>
                        <th class="text-end">{{ __('hr.fields.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employee->attachments as $attachment)
                        <tr>
                            <td>{{ __('hr.attachments.' . $attachment->type) }}</td>
                            <td>{{ $attachment->uploaded_at?->format('Y-m-d H:i') ?? '-' }}</td>
                            <td class="text-end">
                                <a class="btn btn-sm btn-outline-secondary" href="{{ $attachment->url }}" target="_blank" rel="noopener">
                                    {{ __('hr.actions.view_attachment') }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-center text-muted">{{ __('hr.empty.no_attachments') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
