@extends('layouts.customer.dashboard')

@section('title', 'Edit City | EL-Sawady')

@section('content')
    @php
        $activeLocale = $activeLocale ?? ($currentLocale ?? session('locale_full', 'en-SA'));
    @endphp

    <div class="dashboard-body">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <div>
                <h1 class="dashboard-title mb-1">Edit City</h1>
                <p class="dashboard-desc mb-0">Update city details.</p>
            </div>
        </div>

        @include('settings.cities._flash')

        <form action="{{ route('superadmin.setting.cities.update', ['locale' => $activeLocale, 'city' => $city]) }}" method="POST">
            @csrf
            @method('PUT')

            @include('settings.cities._form', ['city' => $city, 'countries' => $countries])

            <div class="d-flex flex-wrap gap-2">
                <button type="submit" class="btn btn-primary-green">Update</button>
                <a href="{{ route('superadmin.setting.cities.index', ['locale' => $activeLocale]) }}" class="btn btn-outline-white">Cancel</a>
            </div>
        </form>
    </div>
@endsection
