@extends('layouts.customer.dashboard')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">{{ __('finance.dashboard.title') }}</h3>
        <form class="d-flex gap-2" method="GET">
            <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}" title="{{ __('finance.common.date_from') }}">
            <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}" title="{{ __('finance.common.date_to') }}">
            <button class="btn btn-outline-primary">{{ __('finance.common.filter') }}</button>
        </form>
    </div>

    <div class="row g-3">
        <div class="col-md-4">
            <div class="card"><div class="card-body">
                <h6 class="text-muted">{{ __('finance.dashboard.total_revenue') }}</h6>
                <h3 class="mb-0">{{ number_format($summary['total_revenue'] ?? 0, 2) }}</h3>
            </div></div>
        </div>
        <div class="col-md-4">
            <div class="card"><div class="card-body">
                <h6 class="text-muted">{{ __('finance.dashboard.total_expenses') }}</h6>
                <h3 class="mb-0">{{ number_format($summary['total_expenses'] ?? 0, 2) }}</h3>
            </div></div>
        </div>
        <div class="col-md-4">
            <div class="card"><div class="card-body">
                <h6 class="text-muted">{{ __('finance.dashboard.net_profit') }}</h6>
                <h3 class="mb-0">{{ number_format($summary['net_profit'] ?? 0, 2) }}</h3>
            </div></div>
        </div>
    </div>
</div>
@endsection
