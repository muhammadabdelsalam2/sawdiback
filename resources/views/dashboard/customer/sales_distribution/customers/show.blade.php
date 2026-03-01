@extends('layouts.customer.dashboard')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">{{ __('sales_dist.customers.show_title') }}</h3>
        <a class="btn btn-outline-secondary" href="{{ route('customer.sales-distribution.customers.index', ['locale' => request()->route('locale')]) }}">{{ __('sales_dist.common.back') }}</a>
    </div>

    <div class="card mb-3"><div class="card-body">
        <div><strong>{{ __('sales_dist.customers.fields.name') }}:</strong> {{ $customer->name }}</div>
        <div><strong>{{ __('sales_dist.customers.fields.type') }}:</strong> {{ __("sales_dist.status.types.$customer->type") }}</div>
        <div><strong>{{ __('sales_dist.customers.fields.phones') }}:</strong> {{ $customer->phones }}</div>
        <div><strong>{{ __('sales_dist.customers.fields.address') }}:</strong> {{ $customer->address }}</div>
        <div><strong>{{ __('sales_dist.customers.fields.tax_number') }}:</strong> {{ $customer->tax_number ?: __('sales_dist.common.not_available') }}</div>
        <div><strong>{{ __('sales_dist.customers.fields.status') }}:</strong> {{ __("sales_dist.status.customer.$customer->status") }}</div>
        <div><strong>{{ __('sales_dist.customers.fields.notes') }}:</strong> {{ $customer->notes ?: __('sales_dist.common.not_available') }}</div>
    </div></div>

    <div class="row g-3">
        <div class="col-md-4"><div class="card"><div class="card-body"><h6>{{ __('sales_dist.customers.stats.contracts') }}</h6><h3>{{ $customer->contracts->count() }}</h3></div></div></div>
        <div class="col-md-4"><div class="card"><div class="card-body"><h6>{{ __('sales_dist.customers.stats.orders') }}</h6><h3>{{ $customer->orders->count() }}</h3></div></div></div>
        <div class="col-md-4"><div class="card"><div class="card-body"><h6>{{ __('sales_dist.customers.stats.invoices') }}</h6><h3>{{ $customer->invoices->count() }}</h3></div></div></div>
    </div>

    <div class="card mt-3"><div class="card-body">
        <table class="table align-middle no-datatable sd-export-table"
            data-export-title="{{ __('sales_dist.export.titles.customers_show') }}"
            data-pdf-orientation="portrait" data-pdf-page-size="A4">
            <thead>
                <tr>
                    <th>{{ __('sales_dist.common.field') }}</th>
                    <th>{{ __('sales_dist.common.value') }}</th>
                </tr>
            </thead>
            <tbody>
                <tr><td>{{ __('sales_dist.customers.fields.name') }}</td><td>{{ $customer->name }}</td></tr>
                <tr><td>{{ __('sales_dist.customers.fields.type') }}</td><td>{{ __("sales_dist.status.types.$customer->type") }}</td></tr>
                <tr><td>{{ __('sales_dist.customers.fields.phones') }}</td><td>{{ $customer->phones }}</td></tr>
                <tr><td>{{ __('sales_dist.customers.fields.address') }}</td><td>{{ $customer->address }}</td></tr>
                <tr><td>{{ __('sales_dist.customers.fields.tax_number') }}</td><td>{{ $customer->tax_number ?: __('sales_dist.common.not_available') }}</td></tr>
                <tr><td>{{ __('sales_dist.customers.fields.status') }}</td><td>{{ __("sales_dist.status.customer.$customer->status") }}</td></tr>
                <tr><td>{{ __('sales_dist.customers.fields.notes') }}</td><td>{{ $customer->notes ?: __('sales_dist.common.not_available') }}</td></tr>
            </tbody>
        </table>
    </div></div>
</div>
@endsection
