@extends('layouts.customer.dashboard')

@section('title', __('dashboard.theme.title') . ' | EL-Sawady')

@section('content')
    @php
        $activeLocale = $activeLocale ?? ($currentLocale ?? session('locale_full', 'en-SA'));
    @endphp

    <div class="dashboard-body">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <div>
                <h1 class="dashboard-title mb-1">{{ __('dashboard.theme.title') }}</h1>
                <p class="dashboard-desc mb-0">{{ __('dashboard.theme.desc') }}</p>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <form action="{{ route('superadmin.setting.theme.update', ['locale' => $activeLocale]) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="chart-card mb-4">
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="custom-checkbox-label mb-0 mt-4">
                            <input
                                type="checkbox"
                                name="rtl_enabled"
                                value="1"
                                class="custom-checkbox"
                                {{ old('rtl_enabled', $settings->rtl_enabled ?? false) ? 'checked' : '' }}>
                            <span class="checkbox-text">{{ __('dashboard.theme.enable_rtl') }}</span>
                        </label>
                        @error('rtl_enabled')
                            <div class="text-danger small mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="app_name" class="form-label fw-semibold">{{ __('dashboard.theme.app_name') }}</label>
                        <input
                            type="text"
                            id="app_name"
                            name="app_name"
                            value="{{ old('app_name', $settings->app_name ?? '') }}"
                            class="form-control @error('app_name') is-invalid @enderror"
                            placeholder="EL-Sawady ERP">
                        @error('app_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="primary_color" class="form-label fw-semibold">{{ __('dashboard.theme.primary_color') }}</label>
                        <input
                            type="text"
                            id="primary_color"
                            name="primary_color"
                            value="{{ old('primary_color', $settings->primary_color ?? '') }}"
                            class="form-control @error('primary_color') is-invalid @enderror"
                            placeholder="#16a34a">
                        <small class="text-muted">{{ __('dashboard.theme.example') }}: #16a34a</small>
                        @error('primary_color')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="d-flex flex-wrap gap-2">
                <button type="submit" class="btn btn-primary-green">{{ __('dashboard.theme.save') }}</button>
                <a href="{{ route('superadmin.setting.theme.index', ['locale' => $activeLocale]) }}" class="btn btn-outline-white">{{ __('dashboard.theme.back') }}</a>
            </div>
        </form>
    </div>
@endsection
