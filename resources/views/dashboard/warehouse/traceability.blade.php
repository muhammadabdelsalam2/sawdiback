@extends('layouts.customer.dashboard')

@section('title', __('warehouse.titles.traceability'))
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/pages/livestock.css') }}">
@endpush

@section('content')
    <div class="container py-4 livestock-page">
        <div class="page-head mb-3">
            <h2 class="page-title">{{ __('warehouse.titles.traceability') }}</h2>
            <form method="GET" action="{{ route('customer.inventory.traceability.index', ['locale' => $currentLocale]) }}" class="d-flex gap-2">
                <select name="product_id" class="form-select warehouse-filter-select">
                    <option value="">All</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" @selected((int) $selectedProductId === (int) $product->id)>{{ $product->name }}</option>
                    @endforeach
                </select>
                <button class="btn btn-primary-green" type="submit">{{ __('warehouse.actions.filter') }}</button>
            </form>
        </div>

        <div class="card-block mb-3">
            <h5>{{ __('warehouse.sections.recent_batches') }}</h5>
            <div class="table-container">
                <table class="table registry-table mb-0 js-livestock-table">
                    <thead>
                        <tr>
                            <th>{{ __('warehouse.fields.product') }}</th>
                            <th>{{ __('warehouse.fields.batch_number') }}</th>
                            <th>{{ __('warehouse.fields.production_date') }}</th>
                            <th>{{ __('warehouse.fields.expiry_date') }}</th>
                            <th>{{ __('warehouse.fields.quantity') }}</th>
                            <th>{{ __('warehouse.fields.quantity_available') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($batches as $batch)
                            <tr>
                                <td>{{ $batch->product?->name }}</td>
                                <td>{{ $batch->batch_number }}</td>
                                <td>{{ optional($batch->production_date)->toDateString() ?? '-' }}</td>
                                <td>{{ optional($batch->expiry_date)->toDateString() ?? '-' }}</td>
                                <td>{{ $batch->quantity_initial }}</td>
                                <td>{{ $batch->quantity_available }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6">{{ __('warehouse.empty.no_batches') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-block">
            <h5>{{ __('warehouse.sections.delivery_history') }}</h5>
            <div class="table-container">
                <table class="table registry-table mb-0 js-livestock-table">
                    <thead>
                        <tr>
                            <th>{{ __('warehouse.fields.delivery_number') }}</th>
                            <th>{{ __('warehouse.fields.customer_name') }}</th>
                            <th>{{ __('warehouse.fields.product') }}</th>
                            <th>{{ __('warehouse.fields.batch_number') }}</th>
                            <th>{{ __('warehouse.fields.quantity') }}</th>
                            <th>{{ __('warehouse.fields.delivered_at') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($deliveryItems as $item)
                            <tr>
                                <td>{{ $item->delivery?->delivery_number }}</td>
                                <td>{{ $item->delivery?->customer_name ?? '-' }}</td>
                                <td>{{ $item->product?->name ?? '-' }}</td>
                                <td>{{ $item->batch?->batch_number ?? '-' }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ optional($item->delivery?->delivered_at)->toDateTimeString() }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6">{{ __('warehouse.empty.no_delivery_items') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
