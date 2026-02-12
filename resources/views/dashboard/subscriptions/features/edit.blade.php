@extends('layouts.customer.dashboard')

@section('title', __('subscriptions.actions.edit_feature'))

@section('content')
    <div class="container py-4">
        <h2>{{ __('subscriptions.actions.edit_feature') }}</h2>
        <div class="bg-white p-3 rounded">
            <form method="POST" action="{{ route('superadmin.features.update', ['locale' => $currentLocale, 'feature' => $feature->id]) }}">
                @method('PUT')
                @include('dashboard.subscriptions.features._form')
            </form>
        </div>
    </div>
@endsection
