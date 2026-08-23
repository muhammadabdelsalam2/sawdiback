@extends('layouts.customer.dashboard')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">{{ __('hr.titles.edit_department') }}</h3>
        <a class="btn btn-outline-secondary"
           href="{{ route('customer.hr.departments.index', ['locale' => request()->route('locale')]) }}">
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
                  action="{{ route('customer.hr.departments.update', ['locale' => request()->route('locale'), 'department' => $department->id]) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">{{ __('hr.fields.name') }} *</label>
                    <input type="text" name="name" class="form-control"
                           value="{{ old('name', $department->name) }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">{{ __('hr.fields.code') }}</label>
                    <input type="text" name="code" class="form-control"
                           value="{{ old('code', $department->code) }}" placeholder="{{ __('hr.options.optional') }}">
                </div>

                <button class="btn btn-primary">{{ __('hr.actions.update') }}</button>
                <a class="btn btn-light"
                   href="{{ route('customer.hr.departments.index', ['locale' => request()->route('locale')]) }}">
                    {{ __('hr.actions.cancel') }}
                </a>
            </form>
        </div>
    </div>
</div>
@endsection
