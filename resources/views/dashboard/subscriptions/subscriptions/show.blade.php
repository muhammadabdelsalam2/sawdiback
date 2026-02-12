@extends('layouts.customer.dashboard')

@section('title', __('subscriptions.actions.view_subscription'))

@section('content')
    <div class="container py-4">
        <h2>{{ __('subscriptions.actions.view_subscription') }} #{{ $subscription->id }}</h2>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="bg-white p-3 rounded mb-3">
            <p><strong>{{ __('subscriptions.fields.customer') }}:</strong> {{ $subscription->customer->name ?? '-' }}</p>
            <p><strong>{{ __('subscriptions.fields.plan') }}:</strong> {{ $subscription->plan->name ?? '-' }}</p>
            <p><strong>{{ __('subscriptions.fields.status') }}:</strong> {{ __('subscriptions.statuses.' . $subscription->status) }}</p>
            <p><strong>{{ __('subscriptions.fields.start_at') }}:</strong> {{ optional($subscription->start_at)->toDateTimeString() }}</p>
            <p><strong>{{ __('subscriptions.fields.end_at') }}:</strong> {{ optional($subscription->end_at)->toDateTimeString() }}</p>
            <p><strong>{{ __('subscriptions.fields.renewal_at') }}:</strong> {{ optional($subscription->renewal_at)->toDateTimeString() }}</p>
        </div>

        <div class="bg-white p-3 rounded mb-3">
            <h5>{{ __('subscriptions.actions.change_plan') }}</h5>
            <form method="POST" action="{{ route('superadmin.subscriptions.change-plan', ['locale' => $currentLocale, 'subscription' => $subscription->id]) }}" class="row g-2">
                @csrf
                <div class="col-md-6">
                    <select name="plan_id" class="form-select" required>
                        @foreach ($plans as $plan)
                            <option value="{{ $plan->id }}" @selected($subscription->plan_id === $plan->id)>{{ $plan->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <button class="btn btn-primary" type="submit">{{ __('subscriptions.actions.change_plan') }}</button>
                </div>
            </form>
        </div>

        <div class="bg-white p-3 rounded mb-3">
            <h5>{{ __('subscriptions.actions.renew') }}</h5>
            <form method="POST" action="{{ route('superadmin.subscriptions.renew', ['locale' => $currentLocale, 'subscription' => $subscription->id]) }}" class="row g-2">
                @csrf
                <div class="col-md-6">
                    <input type="datetime-local" class="form-control" name="from_date">
                </div>
                <div class="col-md-6">
                    <button class="btn btn-success" type="submit">{{ __('subscriptions.actions.renew') }}</button>
                </div>
            </form>
        </div>

        <div class="bg-white p-3 rounded mb-3 d-flex gap-2">
            <form method="POST" action="{{ route('superadmin.subscriptions.cancel', ['locale' => $currentLocale, 'subscription' => $subscription->id]) }}">
                @csrf
                <button class="btn btn-warning" type="submit">{{ __('subscriptions.actions.cancel') }}</button>
            </form>
            <form method="POST" action="{{ route('superadmin.subscriptions.expire', ['locale' => $currentLocale, 'subscription' => $subscription->id]) }}">
                @csrf
                <button class="btn btn-danger" type="submit">{{ __('subscriptions.actions.expire') }}</button>
            </form>
        </div>

        <div class="bg-white p-3 rounded">
            <h5>{{ __('subscriptions.titles.history') }}</h5>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>{{ __('subscriptions.fields.action') }}</th>
                            <th>{{ __('subscriptions.fields.from_status') }}</th>
                            <th>{{ __('subscriptions.fields.to_status') }}</th>
                            <th>{{ __('subscriptions.fields.changed_by') }}</th>
                            <th>{{ __('subscriptions.fields.created_at') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($subscription->histories as $history)
                            <tr>
                                <td>{{ $history->action }}</td>
                                <td>{{ $history->from_status ? __('subscriptions.statuses.' . $history->from_status) : '-' }}</td>
                                <td>{{ $history->to_status ? __('subscriptions.statuses.' . $history->to_status) : '-' }}</td>
                                <td>{{ $history->actor->name ?? '-' }}</td>
                                <td>{{ $history->created_at->toDateTimeString() }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">{{ __('subscriptions.empty.no_data') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
