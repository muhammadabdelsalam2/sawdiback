@extends('layouts.customer.dashboard')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Edit Employee</h3>
        <a class="btn btn-outline-secondary"
           href="{{ route('customer.hr.employees.index', ['locale' => request()->route('locale')]) }}">
            Back
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form method="POST"
                  action="{{ route('customer.hr.employees.update', ['locale' => request()->route('locale'), 'employee' => $employee->id]) }}">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Full Name *</label>
                        <input type="text" name="full_name" class="form-control"
                               value="{{ old('full_name', $employee->full_name) }}" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control"
                               value="{{ old('email', $employee->email) }}">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control"
                               value="{{ old('phone', $employee->phone) }}">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">National ID</label>
                        <input type="text" name="national_id" class="form-control"
                               value="{{ old('national_id', $employee->national_id) }}">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Hire Date</label>
                        <input type="date" name="hire_date" class="form-control"
                               value="{{ old('hire_date', optional($employee->hire_date)->format('Y-m-d')) }}">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Department</label>
                        <select name="department_id" class="form-select">
                            <option value="">--</option>
                            @foreach($departments as $d)
                                <option value="{{ $d->id }}" @selected(old('department_id', $employee->department_id) == $d->id)>
                                    {{ $d->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Job Title</label>
                        <select name="job_title_id" class="form-select">
                            <option value="">--</option>
                            @foreach($jobTitles as $t)
                                <option value="{{ $t->id }}" @selected(old('job_title_id', $employee->job_title_id) == $t->id)>
                                    {{ $t->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Salary</label>
                        <input type="number" step="0.01" name="salary" class="form-control"
                               value="{{ old('salary', $employee->salary) }}" min="0">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Active</label>
                    <select name="is_active" class="form-select">
                        <option value="1" @selected((string)old('is_active', (int)$employee->is_active) === '1')>Yes</option>
                        <option value="0" @selected((string)old('is_active', (int)$employee->is_active) === '0')>No</option>
                    </select>
                </div>

                <button class="btn btn-primary">Update</button>
                <a class="btn btn-light"
                   href="{{ route('customer.hr.employees.index', ['locale' => request()->route('locale')]) }}">
                    Cancel
                </a>
            </form>
        </div>
    </div>
</div>
@endsection
