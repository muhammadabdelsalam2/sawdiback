@extends('layouts.customer.dashboard')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">{{ __('hr.titles.create_leave_request') }}</h3>
        <a class="btn btn-outline-secondary"
           href="{{ route('customer.hr.leaves.index', ['locale' => request()->route('locale')]) }}">
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

    <div class="card">
        <div class="card-body">
            <form method="POST"
                  action="{{ route('customer.hr.leaves.store', ['locale' => request()->route('locale')]) }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label">{{ __('hr.fields.employee') }} *</label>
                    <select name="employee_id" class="form-select" required>
                        <option value="">{{ __('hr.options.select_employee') }}</option>
                        @foreach($employees as $e)
                            <option value="{{ $e->id }}" @selected(old('employee_id') == $e->id)>
                                {{ $e->full_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">{{ __('hr.fields.type') }} *</label>
                    <select name="type" class="form-select" required>
                        <option value="annual" @selected(old('type', 'annual') === 'annual')>{{ __('hr.options.annual') }}</option>
                        <option value="sick" @selected(old('type') === 'sick')>{{ __('hr.options.sick') }}</option>
                        <option value="unpaid" @selected(old('type') === 'unpaid')>{{ __('hr.options.unpaid') }}</option>
                        <option value="other" @selected(old('type') === 'other')>{{ __('hr.options.other') }}</option>
                    </select>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">{{ __('hr.fields.start_date') }} *</label>
                        <input type="date" name="start_date" class="form-control"
                               value="{{ old('start_date') }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">{{ __('hr.fields.end_date') }} *</label>
                        <input type="date" name="end_date" class="form-control"
                               value="{{ old('end_date') }}" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">{{ __('hr.fields.reason') }}</label>
                    <textarea name="reason" class="form-control" rows="3">{{ old('reason') }}</textarea>
                </div>

                <button class="btn btn-primary">{{ __('hr.actions.submit') }}</button>
                <a class="btn btn-light"
                   href="{{ route('customer.hr.leaves.index', ['locale' => request()->route('locale')]) }}">
                    {{ __('hr.actions.cancel') }}
                </a>
            </form>
        </div>
    </div>
</div>
@endsection
