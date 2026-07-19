@extends('layouts.customer.dashboard')

@section('title', __('analytics.title'))

@section('content')
<div class="container-fluid py-4">
    <div class="row g-4">
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title">{{ __('analytics.cards.best_customer') }}</h5>
                    <p class="display-6 mb-0">{{ $report['best_customer']['name'] ?? __('analytics.cards.none') }}</p>
                    <p class="text-muted mb-0">{{ __('analytics.cards.total_value') }}: {{ number_format($report['best_customer']['value'] ?? 0, 2) }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title">{{ __('analytics.cards.best_product') }}</h5>
                    <p class="display-6 mb-0">{{ $report['best_product']['name'] ?? __('analytics.cards.none') }}</p>
                    <p class="text-muted mb-0">{{ __('analytics.cards.quantity') }}: {{ $report['best_product']['quantity'] ?? 0 }}</p>
                    <p class="text-muted mb-0">{{ __('analytics.cards.value') }}: {{ number_format($report['best_product']['value'] ?? 0, 2) }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title">{{ __('analytics.cards.highest_margin') }}</h5>
                    <p class="display-6 mb-0">{{ $report['highest_margin']['name'] ?? __('analytics.cards.none') }}</p>
                    <p class="text-muted mb-0">{{ number_format($report['highest_margin']['margin'] ?? 0, 2) }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title">{{ __('analytics.cards.lowest_margin') }}</h5>
                    <p class="display-6 mb-0">{{ $report['lowest_margin']['name'] ?? __('analytics.cards.none') }}</p>
                    <p class="text-muted mb-0">{{ number_format($report['lowest_margin']['margin'] ?? 0, 2) }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
