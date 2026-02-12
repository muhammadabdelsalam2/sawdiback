@extends('layouts.customer.dashboard')

@section('title', __('subscriptions.actions.create_feature'))

@section('content')
    <div class="container py-4">
        <h2>{{ __('subscriptions.actions.create_feature') }}</h2>
        <div class="bg-white p-3 rounded">
            <form method="POST" action="{{ route('superadmin.features.store', ['locale' => $currentLocale]) }}">
                @include('dashboard.subscriptions.features._form')
            </form>
        </div>
    </div>
@endsection
