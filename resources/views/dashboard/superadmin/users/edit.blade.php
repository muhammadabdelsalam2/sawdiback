@extends('layouts.customer.dashboard')

@section('title', 'Edit User | EL-Sawady')

@section('content')
    <div class="dashboard-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="dashboard-title mb-1">Edit User</h1>
                <p class="dashboard-desc mb-0">Update account profile, roles and permissions.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('superadmin.users.update', ['locale' => $currentLocale ?? session('locale_full', 'en-SA'), 'user' => $userModel]) }}">
            @csrf
            @method('PUT')
            @php($submitLabel = 'Update User')
            @include('dashboard.superadmin.users._form')
        </form>
    </div>
@endsection
