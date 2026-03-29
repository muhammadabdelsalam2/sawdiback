@extends('layouts.customer.dashboard')

@section('title', __('dashboard.cities.edit') . ' | EL-Sawady')

@section('content')
    @php
        $activeLocale = $activeLocale ?? ($currentLocale ?? session('locale_full', 'en-SA'));
    @endphp

    <div class="dashboard-body">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <div>
                <h1 class="dashboard-title mb-1">{{ __('dashboard.cities.edit') }}</h1>
                <p class="dashboard-desc mb-0">{{ __('dashboard.cities.desc') }}</p>
            </div>
        </div>

        @include('settings.cities._flash')

        <form action="{{ route('superadmin.setting.cities.update', ['locale' => $activeLocale, 'city' => $city]) }}" method="POST">
            @csrf
            @method('PUT')

            @include('settings.cities._form', ['city' => $city, 'countries' => $countries])

            <div class="d-flex flex-wrap gap-2">
                <button type="submit" class="btn btn-primary-green">{{ __('dashboard.cities.save') }}</button>
                <a href="{{ route('superadmin.setting.cities.index', ['locale' => $activeLocale]) }}" class="btn btn-outline-white">{{ __('dashboard.cities.cancel') }}</a>
            </div>
        </form>
    </div>
@endsection
