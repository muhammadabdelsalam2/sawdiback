@extends('layouts.customer.dashboard')

@section('title', __('dashboard.superadmin.title') . ' | EL-Sawady')

@section('content')
    @php
        $activeLocale = $currentLocale ?? session('locale_full', 'en-SA');
    @endphp

    <div class="dashboard-body">
        <div class="row align-items-center g-3">
            <div class="col-12 col-md">
                <h1 class="dashboard-title">{{ __('dashboard.superadmin.title') }}</h1>
                <p class="dashboard-desc">{{ __('dashboard.superadmin.desc') }}</p>
            </div>
            <div class="col-12 col-md-auto">
                <a href="{{ route('superadmin.access-management', ['locale' => $activeLocale]) }}"
                    class="btn btn-primary-green me-2">
                    {{ __('dashboard.superadmin.manage_roles') }}
                </a>
                <a href="{{ route('superadmin.users.index', ['locale' => $activeLocale]) }}" class="btn btn-outline-white">
                    {{ __('dashboard.superadmin.manage_users') }}
                </a>
            </div>
        </div>
    </div>
@endsection
