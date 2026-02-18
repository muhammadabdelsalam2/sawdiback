@extends('layouts.customer.dashboard')

@section('title', __('livestock.titles.animals'))
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/pages/livestock.css') }}">
@endpush

@section('content')
    <div class="container py-4 livestock-page">
        <div class="page-head">
            <h2 class="page-title">{{ __('livestock.titles.animals') }}</h2>
            <div class="quick-actions">
                <a class="btn btn-outline-white"
                    href="{{ route('customer.livestock.reproduction-cycles.index', ['locale' => $currentLocale]) }}">
                    {{ __('livestock.actions.reproduction') }}
                </a>
                <a class="btn btn-outline-white"
                    href="{{ route('customer.livestock.alerts.under-treatment', ['locale' => $currentLocale]) }}">
                    {{ __('livestock.actions.alerts') }}
                </a>
                <a class="btn btn-primary-green"
                    href="{{ route('customer.livestock.animals.create', ['locale' => $currentLocale]) }}">
                    {{ __('livestock.actions.register_animal') }}
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="table-container">
            <table class="table align-middle registry-table mb-0 js-livestock-table">
                <thead>
                    <tr>
                        <th>{{ __('livestock.fields.id') }}</th>
                        <th>{{ __('livestock.fields.tag') }}</th>
                        <th>{{ __('livestock.fields.species') }}</th>
                        <th>{{ __('livestock.fields.breed') }}</th>
                        <th>{{ __('livestock.fields.gender') }}</th>
                        <th>{{ __('livestock.fields.status') }}</th>
                        <th>{{ __('livestock.fields.health') }}</th>
                        <th>{{ __('livestock.fields.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($items as $animal)
                        <tr>
                            <td>{{ $animal->id }}</td>
                            <td>{{ $animal->tag_number }}</td>
                            <td>{{ $animal->species->name ?? '-' }}</td>
                            <td>{{ $animal->breed->name ?? '-' }}</td>
                            <td>{{ __('livestock.options.' . $animal->gender) }}</td>
                            <td>{{ __('livestock.options.' . $animal->status) }}</td>
                            <td>{{ __('livestock.options.' . $animal->health_status) }}</td>
                            <td class="d-flex gap-2">
                                <a class="btn btn-sm btn-outline-primary"
                                    href="{{ route('customer.livestock.animals.show', ['locale' => $currentLocale, 'animal' => $animal->id]) }}">
                                    {{ __('livestock.actions.view') }}
                                </a>
                                <a class="btn btn-sm btn-outline-secondary"
                                    href="{{ route('customer.livestock.animals.edit', ['locale' => $currentLocale, 'animal' => $animal->id]) }}">
                                    {{ __('livestock.actions.edit') }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">{{ __('livestock.empty.no_animals') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $items->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endsection
