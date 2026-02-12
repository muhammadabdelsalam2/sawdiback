@extends('layouts.customer.dashboard')

@section('title', __('subscriptions.actions.edit_plan'))

@section('content')
    <div class="container py-4">
        <h2>{{ __('subscriptions.actions.edit_plan') }}</h2>
        <div class="bg-white p-3 rounded">
            <form method="POST" action="{{ route('superadmin.plans.update', ['locale' => $currentLocale, 'plan' => $plan->id]) }}">
                @method('PUT')
                @include('dashboard.subscriptions.plans._form')
            </form>
        </div>
    </div>
@endsection
