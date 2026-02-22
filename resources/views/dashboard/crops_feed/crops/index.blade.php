@extends('layouts.customer.dashboard')

@section('title', __('crops_feed.titles.crops'))
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/pages/livestock.css') }}">
@endpush

@section('content')
    <div class="container py-4 livestock-page">
        <div class="page-head">
            <h2 class="page-title">{{ __('crops_feed.titles.crops') }}</h2>
            <a class="btn btn-primary-green" href="{{ route('customer.crops-feed.crops.create', ['locale' => $currentLocale]) }}">{{ __('crops_feed.actions.add_crop') }}</a>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="table-container">
            <table class="table registry-table mb-0 js-livestock-table">
                <thead>
                    <tr>
                        <th>{{ __('crops_feed.fields.id') }}</th>
                        <th>{{ __('crops_feed.fields.name') }}</th>
                        <th>{{ __('crops_feed.fields.land_area') }}</th>
                        <th>{{ __('crops_feed.fields.planting_date') }}</th>
                        <th>{{ __('crops_feed.fields.yield_tons') }}</th>
                        <th>{{ __('crops_feed.fields.total_cost') }}</th>
                        <th>{{ __('crops_feed.fields.profit_or_loss') }}</th>
                        <th class="no-sort">{{ __('crops_feed.fields.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rows as $row)
                        <tr>
                            <td>{{ $row->id }}</td>
                            <td>{{ $row->name }}</td>
                            <td>{{ $row->land_area }}</td>
                            <td>{{ $row->planting_date?->format('Y-m-d') }}</td>
                            <td>{{ $row->yield_tons ?? '-' }}</td>
                            <td>{{ $row->total_cost }}</td>
                            <td>{{ $row->profit_or_loss ?? '-' }}</td>
                            <td class="d-flex gap-2">
                                <a class="btn btn-sm btn-outline-white" href="{{ route('customer.crops-feed.crops.show', ['locale' => $currentLocale, 'crop' => $row->id]) }}">{{ __('livestock.actions.view') }}</a>
                                <a class="btn btn-sm btn-outline-white" href="{{ route('customer.crops-feed.crops.edit', ['locale' => $currentLocale, 'crop' => $row->id]) }}">{{ __('livestock.actions.edit') }}</a>
                                <form method="POST" action="{{ route('customer.crops-feed.crops.destroy', ['locale' => $currentLocale, 'crop' => $row->id]) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" type="submit">{{ __('crops_feed.actions.delete') }}</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8">{{ __('crops_feed.empty.no_crops') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">{{ $rows->links('pagination::bootstrap-5') }}</div>
    </div>
@endsection
