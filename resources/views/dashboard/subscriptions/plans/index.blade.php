@extends('layouts.customer.dashboard')

@section('title', __('subscriptions.titles.plans'))

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="mb-0">{{ __('subscriptions.titles.plans') }}</h2>
            <a class="btn btn-primary" href="{{ route('superadmin.plans.create', ['locale' => $currentLocale]) }}">
                {{ __('subscriptions.actions.create_plan') }}
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
                        <th>{{ __('subscriptions.fields.name') }}</th>
                        <th>{{ __('subscriptions.fields.slug') }}</th>
                        <th>{{ __('subscriptions.fields.price') }}</th>
                        <th>{{ __('subscriptions.fields.billing_cycle') }}</th>
                        <th>{{ __('subscriptions.fields.is_active') }}</th>
                        <th>{{ __('subscriptions.fields.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($plans as $plan)
                        <tr>
                            <td>{{ $plan->id }}</td>
                            <td>{{ $plan->name }}</td>
                            <td>{{ $plan->slug }}</td>
                            <td>{{ $plan->currency->symbol ?? '' }} {{ $plan->price }}</td>
                            <td>{{ __('subscriptions.billing_cycles.' . $plan->billing_cycle) }}</td>
                            <td>{{ $plan->is_active ? __('subscriptions.general.yes') : __('subscriptions.general.no') }}</td>
                            <td class="d-flex gap-2">
                                <a class="btn btn-sm btn-outline-secondary"
                                    href="{{ route('superadmin.plans.features.edit', ['locale' => $currentLocale, 'plan' => $plan->id]) }}">
                                    {{ __('subscriptions.actions.features') }}
                                </a>
                                <a class="btn btn-sm btn-outline-primary"
                                    href="{{ route('superadmin.plans.edit', ['locale' => $currentLocale, 'plan' => $plan->id]) }}">
                                    {{ __('subscriptions.actions.edit') }}
                                </a>
                                <form method="POST"
                                    action="{{ route('superadmin.plans.destroy', ['locale' => $currentLocale, 'plan' => $plan->id]) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" type="submit">{{ __('subscriptions.actions.delete') }}</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">{{ __('subscriptions.empty.no_data') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $plans->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endsection
