@extends('layouts.customer.dashboard')

@section('title', __('livestock.titles.species'))
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/pages/livestock.css') }}">
@endpush

@section('content')
    <div class="container py-4 livestock-page">
        <div class="page-head">
            <h2 class="page-title">{{ __('livestock.titles.species') }}</h2>
            <a class="btn btn-primary-green" href="{{ route('superadmin.livestock.species.create', ['locale' => $currentLocale]) }}">{{ __('livestock.actions.add_species') }}</a>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="table-container">
            <table class="table registry-table mb-0">
                <thead>
                    <tr>
                        <th>{{ __('livestock.fields.id') }}</th>
                        <th>{{ __('livestock.fields.code') }}</th>
                        <th>{{ __('livestock.fields.name') }}</th>
                        <th>{{ __('livestock.fields.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rows as $row)
                        <tr>
                            <td>{{ $row->id }}</td>
                            <td>{{ $row->code }}</td>
                            <td>{{ $row->name }}</td>
                            <td class="d-flex gap-2">
                                <a class="btn btn-sm btn-outline-white" href="{{ route('superadmin.livestock.species.edit', ['locale' => $currentLocale, 'species' => $row->id]) }}">{{ __('livestock.actions.edit') }}</a>
                                <form method="POST" action="{{ route('superadmin.livestock.species.destroy', ['locale' => $currentLocale, 'species' => $row->id]) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" type="submit">{{ __('livestock.actions.delete') }}</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4">{{ __('livestock.empty.no_species') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $rows->links('pagination::bootstrap-5') }}</div>
    </div>
@endsection
