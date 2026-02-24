@extends('layouts.customer.dashboard')

@section('title', __('warehouse.titles.alerts'))
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/pages/livestock.css') }}">
@endpush

@section('content')
    <div class="container py-4 livestock-page">
        <div class="page-head mb-3">
            <h2 class="page-title">{{ __('warehouse.titles.alerts') }}</h2>
            <form method="GET" action="{{ route('customer.inventory.alerts.index', ['locale' => $currentLocale]) }}" class="d-flex gap-2">
                <input type="number" min="1" max="180" name="days" class="form-control" value="{{ $days }}" placeholder="{{ __('warehouse.fields.days') }}">
                <button class="btn btn-primary-green" type="submit">{{ __('warehouse.actions.filter') }}</button>
            </form>
        </div>

        <div class="card-block mb-3">
            <h5>{{ __('warehouse.sections.low_stock') }}</h5>
            <div class="table-container">
                <table class="table registry-table mb-0 js-livestock-table">
                    <thead>
                        <tr>
                            <th>{{ __('warehouse.fields.product') }}</th>
                            <th>{{ __('warehouse.fields.stock_on_hand') }}</th>
                            <th>{{ __('warehouse.fields.low_stock_threshold') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($lowStockRows as $row)
                            <tr>
                                <td>{{ $row['product']->name }}</td>
                                <td>{{ number_format((float)$row['stock_on_hand'], 2) }}</td>
                                <td>{{ number_format((float)$row['product']->low_stock_threshold, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3">{{ __('warehouse.empty.no_low_stock') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-block">
            <h5>{{ __('warehouse.sections.expiring_soon') }} ({{ $days }})</h5>
            <div class="table-container">
                <table class="table registry-table mb-0 js-livestock-table">
                    <thead>
                        <tr>
                            <th>{{ __('warehouse.fields.product') }}</th>
                            <th>{{ __('warehouse.fields.batch_number') }}</th>
                            <th>{{ __('warehouse.fields.expiry_date') }}</th>
                            <th>{{ __('warehouse.fields.quantity_available') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($expiringRows as $batch)
                            <tr>
                                <td>{{ $batch->product?->name }}</td>
                                <td>{{ $batch->batch_number }}</td>
                                <td>{{ optional($batch->expiry_date)->toDateString() }}</td>
                                <td>{{ $batch->quantity_available }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4">{{ __('warehouse.empty.no_expiring') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

