@extends('layouts.customer.dashboard')

@section('title', __('subscriptions.actions.assign_features'))

@section('content')
    <div class="container py-4">
        <h2>{{ __('subscriptions.actions.assign_features') }} - {{ $plan->name }}</h2>

        {{-- عرض رسالة نجاح --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- عرض رسالة خطأ --}}
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif


        <div class="bg-white p-3 rounded">
            <form method="POST"
                action="{{ route('superadmin.plans.features.update', ['locale' => $currentLocale, 'plan' => $plan->id]) }}">
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
                            @forelse ($features as $key => $feature)
                                <tr>
                                    <td>{{ $feature['label'] ?? $key }} <small class="text-muted d-block">{{ $key }}</small>
                                    </td>
                                    <td>{{ $feature['type'] }}</td>
                                    <td>
                                        <input type="hidden" name="features[{{ $key }}][enabled]" value="0">
                                        <input type="checkbox" name="features[{{ $key }}][enabled]" value="1" @checked((bool) ($feature['enabled'] ?? false))>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="features[{{ $key }}][value]"
                                            value="{{ old('features.' . $key . '.value', $feature['value'] ?? '') }}">
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