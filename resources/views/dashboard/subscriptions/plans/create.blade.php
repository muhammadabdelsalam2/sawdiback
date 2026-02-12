@extends('layouts.customer.dashboard')

@section('title', __('subscriptions.actions.create_plan'))

@section('content')
    <div class="container py-4">
        <h2>{{ __('subscriptions.actions.create_plan') }}</h2>
        <div class="bg-white p-3 rounded">
            <form method="POST" action="{{ route('superadmin.plans.store', ['locale' => $currentLocale]) }}">
                @include('dashboard.subscriptions.plans._form')
            </form>
        </div>
    </div>
@endsection
