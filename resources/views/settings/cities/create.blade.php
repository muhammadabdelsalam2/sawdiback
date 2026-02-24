@extends('layouts.customer.dashboard')

@section('title', 'Create City | EL-Sawady')

@section('content')
    @php
        $activeLocale = $activeLocale ?? ($currentLocale ?? session('locale_full', 'en-SA'));
    @endphp

    <div class="dashboard-body">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <div>
                <h1 class="dashboard-title mb-1">Create City</h1>
                <p class="dashboard-desc mb-0">Add a new city.</p>
            </div>
        </div>

        @include('settings.cities._flash')

        <form action="{{ route('superadmin.setting.cities.store', ['locale' => $activeLocale]) }}" method="POST">
            @csrf

            @include('settings.cities._form', ['city' => null, 'countries' => $countries])

            <div class="d-flex flex-wrap gap-2">
                <button type="submit" class="btn btn-primary-green">Save</button>
                <a href="{{ route('superadmin.setting.cities.index', ['locale' => $activeLocale]) }}" class="btn btn-outline-white">Cancel</a>
            </div>
        </form>
    </div>
@endsection
