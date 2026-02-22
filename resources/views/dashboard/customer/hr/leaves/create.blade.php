@extends('layouts.customer.dashboard')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Create Leave Request</h3>
        <a class="btn btn-outline-secondary"
           href="{{ route('customer.hr.leaves.index', ['locale' => request()->route('locale')]) }}">
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
                  action="{{ route('customer.hr.leaves.store', ['locale' => request()->route('locale')]) }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Employee *</label>
                    <select name="employee_id" class="form-select" required>
                        <option value="">-- Select Employee --</option>
                        @foreach($employees as $e)
                            <option value="{{ $e->id }}" @selected(old('employee_id') == $e->id)>
                                {{ $e->full_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Type *</label>
                    <select name="type" class="form-select" required>
                        <option value="annual" @selected(old('type', 'annual') === 'annual')>Annual</option>
                        <option value="sick" @selected(old('type') === 'sick')>Sick</option>
                        <option value="unpaid" @selected(old('type') === 'unpaid')>Unpaid</option>
                        <option value="other" @selected(old('type') === 'other')>Other</option>
                    </select>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Start Date *</label>
                        <input type="date" name="start_date" class="form-control"
                               value="{{ old('start_date') }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">End Date *</label>
                        <input type="date" name="end_date" class="form-control"
                               value="{{ old('end_date') }}" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Reason</label>
                    <textarea name="reason" class="form-control" rows="3">{{ old('reason') }}</textarea>
                </div>

                <button class="btn btn-primary">Submit</button>
                <a class="btn btn-light"
                   href="{{ route('customer.hr.leaves.index', ['locale' => request()->route('locale')]) }}">
                    Cancel
                </a>
            </form>
        </div>
    </div>
</div>
@endsection
