@extends('layouts.customer.dashboard')

@section('title', __('livestock.titles.vaccines'))
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/pages/livestock.css') }}">
@endpush

@section('content')
    <div class="container py-4 livestock-page">
        <div class="page-head">
            <h2 class="page-title">{{ __('livestock.titles.vaccines') }}</h2>
            <a class="btn btn-primary-green" href="{{ route('customer.livestock.vaccines.create', ['locale' => $currentLocale]) }}">{{ __('livestock.actions.add_vaccine') }}</a>
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
                    <tr><th>{{ __('livestock.fields.id') }}</th><th>{{ __('livestock.fields.name') }}</th><th>{{ __('livestock.fields.default_interval') }}</th><th>{{ __('livestock.fields.actions') }}</th></tr>
                </thead>
                <tbody>
                    @forelse ($rows as $row)
                        <tr>
                            <td>{{ $row->id }}</td>
                            <td>{{ $row->name }}</td>
                            <td>{{ $row->default_interval_days ?? '-' }}</td>
                            <td class="d-flex gap-2">
                                <a class="btn btn-sm btn-outline-white" href="{{ route('customer.livestock.vaccines.edit', ['locale' => $currentLocale, 'vaccine' => $row->id]) }}">{{ __('livestock.actions.edit') }}</a>
                                <form method="POST" action="{{ route('customer.livestock.vaccines.destroy', ['locale' => $currentLocale, 'vaccine' => $row->id]) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" type="submit">{{ __('livestock.actions.delete') }}</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4">{{ __('livestock.empty.no_vaccines') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $rows->links('pagination::bootstrap-5') }}</div>
    </div>
@endsection
