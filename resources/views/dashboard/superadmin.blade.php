@extends('layouts.customer.dashboard')

@section('title', 'Super Admin Dashboard | EL-Sawady')

@section('content')
    @php
        $activeLocale = $currentLocale ?? session('locale_full', 'en-SA');
    @endphp

    <div class="dashboard-body">
        <div class="row align-items-center g-3">
            <div class="col-12 col-md">
                <h1 class="dashboard-title">Super Admin Dashboard</h1>
                <p class="dashboard-desc">Role-based authentication is active. You are logged in as SuperAdmin.</p>
            </div>
            <div class="col-12 col-md-auto">
                <a href="{{ route('superadmin.access-management', ['locale' => $activeLocale]) }}"
                    class="btn btn-primary-green me-2">
                    Manage Roles & Permissions
                </a>
                <a href="{{ route('superadmin.users.index', ['locale' => $activeLocale]) }}" class="btn btn-outline-white">
                    Manage Users
                </a>
            </div>
        </div>
    </div>
@endsection
