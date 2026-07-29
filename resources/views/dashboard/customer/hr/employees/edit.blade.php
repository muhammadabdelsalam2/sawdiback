@extends('layouts.customer.dashboard')

@section('content')
@php
    $statuses = ['active', 'on_leave', 'contract_ended'];
    $operationalDepartments = ['poultry', 'crops', 'livestock'];
@endphp
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">{{ __('hr.titles.edit_employee') }}</h3>
        <a class="btn btn-outline-secondary"
           href="{{ route('customer.hr.employees.index', ['locale' => request()->route('locale')]) }}">
            {{ __('hr.actions.back') }}
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
            </ul>
        </div>
    @endif

    <div class="card mb-3">
        <div class="card-body">
            <form method="POST"
                  enctype="multipart/form-data"
                  action="{{ route('customer.hr.employees.update', ['locale' => request()->route('locale'), 'employee' => $employee->id]) }}">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">{{ __('hr.fields.full_name') }} *</label>
                        <input type="text" name="full_name" class="form-control" value="{{ old('full_name', $employee->full_name) }}" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">{{ __('hr.fields.worker_number') }}</label>
                        <input type="text" name="worker_number" class="form-control" value="{{ old('worker_number', $employee->worker_number) }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">{{ __('hr.fields.employment_status') }}</label>
                        <select name="employment_status" class="form-select" required>
                            @foreach($statuses as $status)
                                <option value="{{ $status }}" @selected(old('employment_status', $employee->employment_status) === $status)>{{ __('hr.options.' . $status) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">{{ __('hr.fields.email') }}</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $employee->email) }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">{{ __('hr.fields.phone') }}</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $employee->phone) }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">{{ __('hr.fields.national_id') }}</label>
                        <input type="text" name="national_id" class="form-control" value="{{ old('national_id', $employee->national_id) }}">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">{{ __('hr.fields.farm') }} *</label>
                        <select name="farm_id" class="form-select" required>
                            <option value="">{{ __('hr.options.select_farm') }}</option>
                            @foreach($farms as $farm)
                                <option value="{{ $farm->id }}" @selected(old('farm_id', $employee->farm_id) == $farm->id)>{{ $farm->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">{{ __('hr.fields.department') }}</label>
                        <select name="department_id" class="form-select">
                            <option value="">{{ __('hr.options.select') }}</option>
                            @foreach($departments as $d)
                                <option value="{{ $d->id }}" @selected(old('department_id', $employee->department_id) == $d->id)>{{ $d->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">{{ __('hr.fields.operational_department') }}</label>
                        <select name="operational_department" class="form-select">
                            <option value="">{{ __('hr.options.select') }}</option>
                            @foreach($operationalDepartments as $department)
                                <option value="{{ $department }}" @selected(old('operational_department', $employee->operational_department) === $department)>{{ __('hr.options.' . $department) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">{{ __('hr.fields.job_title') }}</label>
                        <select name="job_title_id" class="form-select">
                            <option value="">{{ __('hr.options.select') }}</option>
                            @foreach($jobTitles as $t)
                                <option value="{{ $t->id }}" @selected(old('job_title_id', $employee->job_title_id) == $t->id)>{{ $t->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">{{ __('hr.fields.profession') }}</label>
                        <input type="text" name="profession" class="form-control" value="{{ old('profession', $employee->profession) }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">{{ __('hr.fields.hire_date') }}</label>
                        <input type="date" name="hire_date" class="form-control" value="{{ old('hire_date', optional($employee->hire_date)->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">{{ __('hr.fields.salary') }}</label>
                        <input type="number" step="0.01" name="salary" class="form-control" value="{{ old('salary', $employee->salary) }}" min="0">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">{{ __('hr.fields.passport_expiry_date') }}</label>
                        <input type="date" name="passport_expiry_date" class="form-control" value="{{ old('passport_expiry_date', optional($employee->passport_expiry_date)->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">{{ __('hr.fields.iqama_expiry_date') }}</label>
                        <input type="date" name="iqama_expiry_date" class="form-control" value="{{ old('iqama_expiry_date', optional($employee->iqama_expiry_date)->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">{{ __('hr.fields.active') }}</label>
                        <select name="is_active" class="form-select">
                            <option value="1" @selected((string) old('is_active', (int) $employee->is_active) === '1')>{{ __('hr.options.yes') }}</option>
                            <option value="0" @selected((string) old('is_active', (int) $employee->is_active) === '0')>{{ __('hr.options.no') }}</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">{{ __('hr.fields.passport_attachment') }}</label>
                        <input type="file" name="attachment_passport" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">{{ __('hr.fields.iqama_attachment') }}</label>
                        <input type="file" name="attachment_iqama" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">{{ __('hr.fields.identity_attachment') }}</label>
                        <input type="file" name="attachment_identity" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                    </div>
                </div>

                <button class="btn btn-primary">{{ __('hr.actions.update') }}</button>
                <a class="btn btn-light"
                   href="{{ route('customer.hr.employees.index', ['locale' => request()->route('locale')]) }}">
                    {{ __('hr.actions.cancel') }}
                </a>
            </form>
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
