@extends('layouts.customer.dashboard')

@section('title', __('dashboard.content.create_title') . ' | EL-Sawady')

@section('content')
    @php
        $activeLocale = $currentLocale ?? session('locale_full', 'en-SA');
    @endphp
    <div class="dashboard-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="dashboard-title mb-1">{{ __('dashboard.content.create_title') }}</h1>
                <p class="dashboard-desc mb-0">{{ __('dashboard.content.create_desc') }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('superadmin.content.store', ['locale' => $activeLocale]) }}" enctype="multipart/form-data">
            @csrf
            @php($submitLabel = __('dashboard.content.create'))
            @include('superadmin.content._form')
        </form>
    </div>
@endsection
