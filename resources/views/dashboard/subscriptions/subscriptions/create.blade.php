@extends('layouts.customer.dashboard')

@section('title', __('subscriptions.actions.create_subscription'))

@section('content')
    <div class="container py-4">
        <h2>{{ __('subscriptions.actions.create_subscription') }}</h2>
        <div class="bg-white p-3 rounded">
            <form method="POST" action="{{ route('superadmin.subscriptions.store', ['locale' => $currentLocale]) }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">{{ __('subscriptions.fields.customer') }}</label>
                    <select name="customer_id" class="form-select" required>
                        @foreach ($customers as $customer)
                            <option value="{{ $customer->id }}" @selected((int) old('customer_id') === $customer->id)>{{ $customer->name }} ({{ $customer->email }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">{{ __('subscriptions.fields.plan') }}</label>
                    <select name="plan_id" class="form-select" required>
                        @foreach ($plans as $plan)
                            <option value="{{ $plan->id }}" @selected((int) old('plan_id') === $plan->id)>
                                {{ $plan->name }} - {{ $plan->currency->symbol ?? '' }} {{ $plan->price }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">{{ __('subscriptions.fields.start_at') }}</label>
                    <input type="datetime-local" name="start_at" class="form-control" value="{{ old('start_at') }}">
                </div>
                <button class="btn btn-success" type="submit">{{ __('subscriptions.actions.save') }}</button>
            </form>
        </div>
    </div>
@endsection
