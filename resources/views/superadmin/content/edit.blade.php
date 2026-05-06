@extends('layouts.customer.dashboard')

@section('title', __('dashboard.content.edit_title') . ' | EL-Sawady')

@section('content')
    @php
        $activeLocale = $currentLocale ?? session('locale_full', 'en-SA');
    @endphp
    <div class="dashboard-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="dashboard-title mb-1">{{ __('dashboard.content.edit_title') }}</h1>
                <p class="dashboard-desc mb-0">{{ __('dashboard.content.edit_desc') }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('superadmin.content.update', ['locale' => $activeLocale, 'content' => $content]) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @php($submitLabel = __('dashboard.content.update'))
            @include('superadmin.content._form')
        </form>
    </div>
@endsection
