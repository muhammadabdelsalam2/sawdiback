@extends('layouts.customer.dashboard')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">{{ __('sales_dist.dashboard.title') }}</h3>
        <a class="btn btn-primary" href="{{ route('customer.sales-distribution.orders.index', ['locale' => request()->route('locale')]) }}">{{ __('sales_dist.dashboard.manage_orders') }}</a>
    </div>

    <div class="row g-3">
        <div class="col-md-4"><div class="card"><div class="card-body"><h6>{{ __('sales_dist.dashboard.cards.customers') }}</h6><h3>{{ $summary['customers_count'] }}</h3></div></div></div>
        <div class="col-md-4"><div class="card"><div class="card-body"><h6>{{ __('sales_dist.dashboard.cards.contracts') }}</h6><h3>{{ $summary['contracts_count'] }}</h3></div></div></div>
        <div class="col-md-4"><div class="card"><div class="card-body"><h6>{{ __('sales_dist.dashboard.cards.orders') }}</h6><h3>{{ $summary['orders_count'] }}</h3></div></div></div>
        <div class="col-md-4"><div class="card"><div class="card-body"><h6>{{ __('sales_dist.dashboard.cards.shipments') }}</h6><h3>{{ $summary['shipments_count'] }}</h3></div></div></div>
        <div class="col-md-4"><div class="card"><div class="card-body"><h6>{{ __('sales_dist.dashboard.cards.invoices') }}</h6><h3>{{ $summary['invoices_count'] }}</h3></div></div></div>
        <div class="col-md-4"><div class="card"><div class="card-body"><h6>{{ __('sales_dist.dashboard.cards.open_invoices_total') }}</h6><h3>{{ number_format($summary['open_invoices_total'], 2) }}</h3></div></div></div>
    </div>

    <div class="card mt-3"><div class="card-body">
        <table class="table align-middle no-datatable sd-export-table"
            data-export-title="{{ __('sales_dist.export.titles.dashboard_summary') }}"
            data-pdf-orientation="portrait" data-pdf-page-size="A4">
            <thead>
                <tr>
                    <th>{{ __('sales_dist.common.field') }}</th>
                    <th>{{ __('sales_dist.common.value') }}</th>
                </tr>
            </thead>
            <tbody>
                <tr><td>{{ __('sales_dist.dashboard.cards.customers') }}</td><td>{{ $summary['customers_count'] }}</td></tr>
                <tr><td>{{ __('sales_dist.dashboard.cards.contracts') }}</td><td>{{ $summary['contracts_count'] }}</td></tr>
                <tr><td>{{ __('sales_dist.dashboard.cards.orders') }}</td><td>{{ $summary['orders_count'] }}</td></tr>
                <tr><td>{{ __('sales_dist.dashboard.cards.shipments') }}</td><td>{{ $summary['shipments_count'] }}</td></tr>
                <tr><td>{{ __('sales_dist.dashboard.cards.invoices') }}</td><td>{{ $summary['invoices_count'] }}</td></tr>
                <tr><td>{{ __('sales_dist.dashboard.cards.open_invoices_total') }}</td><td>{{ number_format($summary['open_invoices_total'], 2) }}</td></tr>
            </tbody>
        </table>
    </div></div>
</div>
@endsection
