@extends('layouts.customer.dashboard')

@section('title', __('crops_feed.titles.reports'))
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/pages/livestock.css') }}">
@endpush

@section('content')
    <div class="container py-4 livestock-page">
        <div class="page-head">
            <h2 class="page-title">{{ __('crops_feed.titles.reports') }}</h2>
            <form method="GET" action="{{ route('customer.crops-feed.reports.index', ['locale' => $currentLocale]) }}" class="d-flex gap-2">
                <input type="month" name="month" class="form-control" value="{{ $month }}">
                <button class="btn btn-primary-green" type="submit">{{ __('crops_feed.actions.filter') }}</button>
            </form>
        </div>

        <div class="card-block mb-3">
            <div class="row g-3">
                <div class="col-md-4"><strong>{{ __('crops_feed.fields.monthly_feed_cost') }}:</strong> {{ number_format($monthlyFeedCost, 2) }}</div>
                <div class="col-md-4"><strong>{{ __('crops_feed.fields.farm_feed_production') }}:</strong> {{ number_format($farmFeedProduction, 2) }}</div>
                <div class="col-md-4"><strong>{{ __('crops_feed.fields.farm_feed_need') }}:</strong> {{ number_format($farmFeedNeed, 2) }}</div>
                <div class="col-md-4">
                    <strong>{{ __('crops_feed.fields.coverage_status') }}:</strong>
                    {{ $farmFeedProduction >= $farmFeedNeed ? __('crops_feed.options.covered') : __('crops_feed.options.not_covered') }}
                </div>
            </div>
        </div>

        <div class="card-block mb-3">
            <h5 class="section-title">{{ __('crops_feed.fields.monthly_feed_cost') }} - {{ __('crops_feed.fields.animal') }}</h5>
            <div class="table-container">
                <table class="table registry-table mb-0 js-livestock-table">
                    <thead>
                        <tr>
                            <th>{{ __('crops_feed.fields.animal') }}</th>
                            <th>{{ __('crops_feed.fields.total_cost') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($costPerAnimal as $row)
                            <tr>
                                <td>{{ $row->animal?->tag_number ?? '-' }}</td>
                                <td>{{ number_format((float) $row->total_cost, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2">{{ __('crops_feed.empty.no_cost_per_animal') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-block">
            <h5 class="section-title">{{ __('dashboard.stats.low_stock_alert') }}</h5>
            <div class="table-container">
                <table class="table registry-table mb-0 js-livestock-table">
                    <thead>
                        <tr>
                            <th>{{ __('crops_feed.fields.feed_type') }}</th>
                            <th>{{ __('crops_feed.fields.stock_on_hand') }}</th>
                            <th>{{ __('crops_feed.fields.low_stock_threshold') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($lowStockRows as $row)
                            <tr>
                                <td>{{ $row['feedType']->name }}</td>
                                <td>{{ number_format((float) $row['stock_on_hand'], 2) }}</td>
                                <td>{{ number_format((float) $row['feedType']->low_stock_threshold, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3">{{ __('crops_feed.empty.no_low_stock') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
