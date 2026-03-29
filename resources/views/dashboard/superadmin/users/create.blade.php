@extends('layouts.customer.dashboard')

@section('title', __('dashboard.users.create_title') . ' | EL-Sawady')

@section('content')
    @php
        $activeLocale = $currentLocale ?? session('locale_full', 'en-SA');
    @endphp
    <div class="dashboard-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="dashboard-title mb-1">{{ __('dashboard.users.create_title') }}</h1>
                <p class="dashboard-desc mb-0">{{ __('dashboard.users.create_desc') }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('superadmin.users.store', ['locale' => $activeLocale]) }}">
            @csrf
            @php($submitLabel = __('dashboard.users.create'))
            @include('dashboard.superadmin.users._form')
        </form>
    </div>
@endsection
