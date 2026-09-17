@extends('layouts.customer.dashboard')

@section('title', __('hr.titles.create_employee'))

@section('content')
@php
    $currentLocale = app()->getLocale();
@endphp

<div class="container-fluid my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 font-weight-bold text-gray-800 mb-1">{{ __('hr.titles.create_employee') }}</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent p-0 mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('customer.hr.employees.index', ['locale' => $currentLocale]) }}">{{ __('hr.titles.employees') }}</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">{{ __('hr.titles.create_employee') }}</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('customer.hr.employees.index', ['locale' => $currentLocale]) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left mr-1"></i> {{ __('hr.actions.back') }}
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <form action="{{ route('customer.hr.employees.store', ['locale' => $currentLocale]) }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- 1. البيانات الأساسية والوظيفية --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="card-title font-weight-bold text-primary mb-0">
                    <i class="fas fa-user mr-2"></i> {{ __('hr.titles.employee_details') }}
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 form-group mb-3">
                        <label class="font-weight-bold">{{ __('hr.fields.full_name') }} <span class="text-danger">*</span></label>
                        <input type="text" name="full_name" class="form-control" value="{{ old('full_name') }}" required>
                    </div>

                    <div class="col-md-4 form-group mb-3">
                        <label class="font-weight-bold">{{ __('hr.fields.worker_number') }}</label>
                        <input type="text" name="worker_number" class="form-control" value="{{ old('worker_number') }}">
                    </div>

                    <div class="col-md-4 form-group mb-3">
                        <label class="font-weight-bold">{{ __('hr.fields.profession') }}</label>
                        <input type="text" name="profession" class="form-control" value="{{ old('profession') }}">
                    </div>

                    <div class="col-md-4 form-group mb-3">
                        <label class="font-weight-bold">{{ __('hr.fields.farm') }}</label>
                        <select name="farm_id" class="form-control select2">
                            <option value="">{{ __('hr.options.select_farm') }}</option>
                            @foreach($farms as $farm)
                                <option value="{{ $farm->id }}" {{ old('farm_id') == $farm->id ? 'selected' : '' }}>{{ $farm->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 form-group mb-3">
                        <label class="font-weight-bold">{{ __('hr.fields.department') }} <span class="text-danger">*</span></label>
                        <select name="department_id" class="form-control select2" required>
                            <option value="">-- {{ __('hr.options.select') }} --</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 form-group mb-3">
                        <label class="font-weight-bold">{{ __('hr.fields.job_title') }} <span class="text-danger">*</span></label>
                        <select name="job_title_id" class="form-control select2" required>
                            <option value="">-- {{ __('hr.options.select') }} --</option>
                            @foreach($jobTitles as $job)
                                <option value="{{ $job->id }}" {{ old('job_title_id') == $job->id ? 'selected' : '' }}>{{ $job->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 form-group mb-3">
                        <label class="font-weight-bold">{{ __('hr.fields.operational_department') }}</label>
                        <select name="operational_department" class="form-control">
                            <option value="">-- {{ __('hr.options.select') }} --</option>
                            <option value="poultry" {{ old('operational_department') == 'poultry' ? 'selected' : '' }}>{{ __('hr.options.poultry') }}</option>
                            <option value="crops" {{ old('operational_department') == 'crops' ? 'selected' : '' }}>{{ __('hr.options.crops') }}</option>
                            <option value="livestock" {{ old('operational_department') == 'livestock' ? 'selected' : '' }}>{{ __('hr.options.livestock') }}</option>
                        </select>
                    </div>

                    <div class="col-md-3 form-group mb-3">
                        <label class="font-weight-bold">{{ __('hr.fields.hire_date') }}</label>
                        <input type="date" name="hire_date" class="form-control" value="{{ old('hire_date', date('Y-m-d')) }}">
                    </div>

                    <div class="col-md-3 form-group mb-3">
                        <label class="font-weight-bold">{{ __('hr.fields.salary') }} <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="salary" class="form-control" value="{{ old('salary', '0.00') }}" required>
                    </div>

                    <div class="col-md-3 form-group mb-3">
                        <label class="font-weight-bold">{{ __('hr.fields.employment_status') }} <span class="text-danger">*</span></label>
                        <select name="employment_status" class="form-control" required>
                            <option value="active" {{ old('employment_status', 'active') == 'active' ? 'selected' : '' }}>{{ __('hr.status_active') }}</option>
                            <option value="on_leave" {{ old('employment_status') == 'on_leave' ? 'selected' : '' }}>{{ __('hr.status_on_leave') }}</option>
                            <option value="traveling" {{ old('employment_status') == 'traveling' ? 'selected' : '' }}>{{ __('hr.status_traveling') }}</option>
                            <option value="contract_ended" {{ old('employment_status') == 'contract_ended' ? 'selected' : '' }}>{{ __('hr.options.contract_ended') }}</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 form-group mb-3">
                        <label class="font-weight-bold">{{ __('hr.fields.phone') }}</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                    </div>

                    <div class="col-md-4 form-group mb-3">
                        <label class="font-weight-bold">{{ __('hr.fields.email') }}</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                    </div>

                    <div class="col-md-4 form-group mb-3">
                        <label class="font-weight-bold">{{ __('hr.fields.national_id') }}</label>
                        <input type="text" name="national_id" class="form-control" value="{{ old('national_id') }}">
                    </div>

                    <div class="col-md-6 form-group mb-3">
                        <label class="font-weight-bold">{{ __('hr.fields.passport_expiry_date') }}</label>
                        <input type="date" name="passport_expiry_date" class="form-control" value="{{ old('passport_expiry_date') }}">
                    </div>

                    <div class="col-md-6 form-group mb-3">
                        <label class="font-weight-bold">{{ __('hr.fields.iqama_expiry_date') }}</label>
                        <input type="date" name="iqama_expiry_date" class="form-control" value="{{ old('iqama_expiry_date') }}">
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. السجل المهني والمؤهلات والبديل والإجازة السنوية --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="card-title font-weight-bold text-info mb-0">
                    <i class="fas fa-user-graduate mr-2"></i> {{ __('hr.work_experience') }} &amp; {{ __('hr.education') }}
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 form-group mb-3">
                        <label class="font-weight-bold">{{ __('hr.replacement_employee') }}</label>
                        <select name="replacement_employee_id" class="form-control select2">
                            <option value="">-- {{ __('hr.none') }} --</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}" {{ old('replacement_employee_id') == $emp->id ? 'selected' : '' }}>
                                    {{ $emp->full_name }} ({{ $emp->worker_number ?? $emp->id }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 form-group mb-3">
                        <label class="font-weight-bold">{{ __('hr.next_annual_leave_date') }}</label>
                        <input type="date" name="next_annual_leave_date" class="form-control" value="{{ old('next_annual_leave_date') }}">
                    </div>

                    <div class="col-md-6 form-group mb-3">
                        <label class="font-weight-bold">{{ __('hr.education') }}</label>
                        <textarea name="education" class="form-control" rows="3" placeholder="{{ __('hr.education') }}">{{ old('education') }}</textarea>
                    </div>

                    <div class="col-md-6 form-group mb-3">
                        <label class="font-weight-bold">{{ __('hr.work_experience') }}</label>
                        <textarea name="work_experience" class="form-control" rows="3" placeholder="{{ __('hr.work_experience') }}">{{ old('work_experience') }}</textarea>
                    </div>

                    <div class="col-md-6 form-group mb-3">
                        <label class="font-weight-bold">{{ __('hr.self_development') }}</label>
                        <textarea name="self_development" class="form-control" rows="3" placeholder="{{ __('hr.self_development') }}">{{ old('self_development') }}</textarea>
                    </div>

                    <div class="col-md-6 form-group mb-3">
                        <label class="font-weight-bold">{{ __('hr.achievements_creativity') }}</label>
                        <textarea name="achievements_creativity" class="form-control" rows="3" placeholder="{{ __('hr.achievements_creativity') }}">{{ old('achievements_creativity') }}</textarea>
                    </div>

                    <div class="col-md-12 form-group mb-3">
                        <label class="font-weight-bold text-danger">{{ __('hr.infractions_absence_notes') }}</label>
                        <textarea name="infractions_absence_notes" class="form-control border-danger" rows="3" placeholder="{{ __('hr.infractions_absence_notes') }}">{{ old('infractions_absence_notes') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. المرفقات --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="card-title font-weight-bold text-secondary mb-0">
                    <i class="fas fa-paperclip mr-2"></i> {{ __('hr.titles.attachments') }}
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 form-group mb-3">
                        <label class="font-weight-bold">{{ __('hr.fields.passport_attachment') }}</label>
                        <input type="file" name="attachment_passport" class="form-control-file">
                    </div>
                    <div class="col-md-4 form-group mb-3">
                        <label class="font-weight-bold">{{ __('hr.fields.iqama_attachment') }}</label>
                        <input type="file" name="attachment_iqama" class="form-control-file">
                    </div>
                    <div class="col-md-4 form-group mb-3">
                        <label class="font-weight-bold">{{ __('hr.fields.identity_attachment') }}</label>
                        <input type="file" name="attachment_identity" class="form-control-file">
                    </div>
                </div>
            </div>
        </div>

        <div class="text-right mb-4">
            <button type="submit" class="btn btn-primary px-4">
                <i class="fas fa-save mr-1"></i> {{ __('hr.actions.save') }}
            </button>
        </div>
    </form>
</div>
@endsection