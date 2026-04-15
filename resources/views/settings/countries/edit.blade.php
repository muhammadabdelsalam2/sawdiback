@extends('layouts.customer.dashboard')

@section('title', __('dashboard.countries.edit') . ' | EL-Sawady')

@section('content')
    @php
        $activeLocale = $activeLocale ?? ($currentLocale ?? session('locale_full', 'en-SA'));
    @endphp

    <div class="dashboard-body">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <div>
                <h1 class="dashboard-title mb-1">{{ __('dashboard.countries.edit') }}</h1>
                <p class="dashboard-desc mb-0">{{ __('dashboard.countries.desc') }}</p>
            </div>
        </div>

        @include('settings.countries._flash')

        <form action="{{ route('superadmin.setting.countries.update', ['locale' => $activeLocale, 'country' => $country]) }}" method="POST">
            @csrf
            @method('PUT')

            @include('settings.countries._form', ['country' => $country])

            <div class="d-flex flex-wrap gap-2">
                <button type="submit" class="btn btn-primary-green">{{ __('dashboard.countries.save') }}</button>
                <a href="{{ route('superadmin.setting.countries.index', ['locale' => $activeLocale]) }}" class="btn btn-outline-white">{{ __('dashboard.countries.cancel') }}</a>
            </div>
        </form>
    </div>
@endsection
