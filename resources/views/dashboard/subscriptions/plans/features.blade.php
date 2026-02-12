@extends('layouts.customer.dashboard')

@section('title', __('subscriptions.actions.assign_features'))

@section('content')
    <div class="container py-4">
        <h2>{{ __('subscriptions.actions.assign_features') }} - {{ $plan->name }}</h2>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="bg-white p-3 rounded">
            <form method="POST" action="{{ route('superadmin.plans.features.update', ['locale' => $currentLocale, 'plan' => $plan->id]) }}">
                @csrf
                @method('PUT')

                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>{{ __('subscriptions.fields.feature') }}</th>
                                <th>{{ __('subscriptions.fields.type') }}</th>
                                <th>{{ __('subscriptions.fields.enabled') }}</th>
                                <th>{{ __('subscriptions.fields.value') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($features as $feature)
                                @php
                                    $pivot = $plan->features->firstWhere('id', $feature->id)?->pivot;
                                @endphp
                                <tr>
                                    <td>{{ $feature->name }} <small class="text-muted d-block">{{ $feature->key }}</small></td>
                                    <td>{{ $feature->type }}</td>
                                    <td>
                                        <input type="hidden" name="features[{{ $feature->id }}][enabled]" value="0">
                                        <input type="checkbox" name="features[{{ $feature->id }}][enabled]" value="1"
                                            @checked((bool) ($pivot->enabled ?? false))>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control"
                                            name="features[{{ $feature->id }}][value]"
                                            value="{{ old('features.' . $feature->id . '.value', $pivot->value ?? '') }}">
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4">{{ __('subscriptions.empty.no_data') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <button class="btn btn-success" type="submit">{{ __('subscriptions.actions.save') }}</button>
            </form>
        </div>
    </div>
@endsection
