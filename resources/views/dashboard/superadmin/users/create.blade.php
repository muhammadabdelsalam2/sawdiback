@extends('layouts.customer.dashboard')

@section('title', 'Create User | EL-Sawady')

@section('content')
    <div class="dashboard-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="dashboard-title mb-1">Create User</h1>
                <p class="dashboard-desc mb-0">Create a new account and assign roles/permissions.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('superadmin.users.store', ['locale' => $currentLocale ?? session('locale_full', 'en-SA')]) }}">
            @csrf
            @php($submitLabel = 'Create User')
            @include('dashboard.superadmin.users._form')
        </form>
    </div>
@endsection
