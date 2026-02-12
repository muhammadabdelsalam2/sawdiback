@extends('layouts.customer.dashboard')

@section('title', __('subscriptions.titles.subscriptions'))

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="mb-0">{{ __('subscriptions.titles.subscriptions') }}</h2>
            <a class="btn btn-primary" href="{{ route('superadmin.subscriptions.create', ['locale' => $currentLocale]) }}">
                {{ __('subscriptions.actions.create_subscription') }}
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="table-responsive bg-white p-3 rounded">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('subscriptions.fields.customer') }}</th>
                        <th>{{ __('subscriptions.fields.plan') }}</th>
                        <th>{{ __('subscriptions.fields.status') }}</th>
                        <th>{{ __('subscriptions.fields.start_at') }}</th>
                        <th>{{ __('subscriptions.fields.end_at') }}</th>
                        <th>{{ __('subscriptions.fields.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($subscriptions as $subscription)
                        <tr>
                            <td>{{ $subscription->id }}</td>
                            <td>{{ $subscription->customer->name ?? '-' }}</td>
                            <td>{{ $subscription->plan->name ?? '-' }}</td>
                            <td>{{ __('subscriptions.statuses.' . $subscription->status) }}</td>
                            <td>{{ optional($subscription->start_at)->toDateString() }}</td>
                            <td>{{ optional($subscription->end_at)->toDateString() }}</td>
                            <td>
                                <a class="btn btn-sm btn-outline-primary"
                                    href="{{ route('superadmin.subscriptions.show', ['locale' => $currentLocale, 'subscription' => $subscription->id]) }}">
                                    {{ __('subscriptions.actions.view') }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">{{ __('subscriptions.empty.no_data') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
           {{ $subscriptions->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endsection
