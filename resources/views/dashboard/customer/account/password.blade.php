@extends('layouts.customer.dashboard')

@section('title', __('account.password.title') . ' | ' . __('auth.brand_name'))

@section('content')
    <div class="dashboard-body">
        <div class="chart-card">
            <div class="chart-card-header">
                <div>
                    <h3>{{ __('account.password.title') }}</h3>
                    <p>{{ __('account.password.subtitle') }}</p>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success mb-3">{{ session('success') }}</div>
            @endif

            <form class="needs-validation" method="POST"
                action="{{ route('customer.password.update', ['locale' => $activeLocale]) }}" novalidate>
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">{{ __('account.password.current') }}</label>
                        <input type="password" name="current_password"
                            class="form-control @error('current_password') is-invalid @enderror" required>
                        @error('current_password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">{{ __('account.password.new') }}</label>
                        <input type="password" name="password"
                            class="form-control @error('password') is-invalid @enderror" required>
                        @error('password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">{{ __('account.password.confirm') }}</label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4 flex-wrap gap-2">
                    <a href="{{ route('customer.profile.show', ['locale' => $activeLocale]) }}"
                        class="btn btn-outline-white">
                        {{ __('account.profile.back') }}
                    </a>
                    <button class="btn btn-primary-green" type="submit">
                        {{ __('account.password.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
