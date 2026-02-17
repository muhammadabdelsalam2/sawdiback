@extends('layouts.customer.dashboard')

@section('title', 'Edit Country | EL-Sawady')

@section('content')
    @php
        $activeLocale = $activeLocale ?? ($currentLocale ?? session('locale_full', 'en-SA'));
    @endphp

    <div class="dashboard-body">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <div>
                <h1 class="dashboard-title mb-1">Edit Country</h1>
                <p class="dashboard-desc mb-0">Update country details.</p>
            </div>
        </div>

        @include('settings.countries._flash')

        <form action="{{ route('settings.countries.update', ['locale' => $activeLocale, 'country' => $country]) }}" method="POST">
            @csrf
            @method('PUT')

            @include('settings.countries._form', ['country' => $country])

            <div class="d-flex flex-wrap gap-2">
                <button type="submit" class="btn btn-primary-green">Update</button>
                <a href="{{ route('settings.countries.index', ['locale' => $activeLocale]) }}" class="btn btn-outline-white">Cancel</a>
            </div>
        </form>
    </div>
@endsection
