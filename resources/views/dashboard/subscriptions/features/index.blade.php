@extends('layouts.customer.dashboard')

@section('title', __('subscriptions.titles.features'))

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="mb-0">{{ __('subscriptions.titles.features') }}</h2>
            <a class="btn btn-primary" href="{{ route('superadmin.features.create', ['locale' => $currentLocale]) }}">
                {{ __('subscriptions.actions.create_feature') }}
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
                        <th>{{ __('subscriptions.fields.key') }}</th>
                        <th>{{ __('subscriptions.fields.name') }}</th>
                        <th>{{ __('subscriptions.fields.type') }}</th>
                        <th>{{ __('subscriptions.fields.is_active') }}</th>
                        <th>{{ __('subscriptions.fields.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($features as $feature)
                        <tr>
                            <td>{{ $feature->id }}</td>
                            <td>{{ $feature->key }}</td>
                            <td>{{ $feature->name }}</td>
                            <td>{{ $feature->type }}</td>
                            <td>{{ $feature->is_active ? __('subscriptions.general.yes') : __('subscriptions.general.no') }}</td>
                            <td class="d-flex gap-2">
                                <a class="btn btn-sm btn-outline-primary"
                                    href="{{ route('superadmin.features.edit', ['locale' => $currentLocale, 'feature' => $feature->id]) }}">
                                    {{ __('subscriptions.actions.edit') }}
                                </a>
                                <form method="POST"
                                    action="{{ route('superadmin.features.destroy', ['locale' => $currentLocale, 'feature' => $feature->id]) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" type="submit">{{ __('subscriptions.actions.delete') }}</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">{{ __('subscriptions.empty.no_data') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $features->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endsection
