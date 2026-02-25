@extends('layouts.customer.dashboard')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">{{ __('sales_dist.contracts.show_title') }}</h3>
        <a class="btn btn-outline-secondary" href="{{ route('customer.sales-distribution.contracts.index', ['locale' => request()->route('locale')]) }}">{{ __('sales_dist.common.back') }}</a>
    </div>

    <div class="card"><div class="card-body">
        <div><strong>{{ __('sales_dist.contracts.fields.customer') }}:</strong> {{ $contract->customer->name }}</div>
        <div><strong>{{ __('sales_dist.contracts.fields.code') }}:</strong> {{ $contract->contract_code }}</div>
        <div><strong>{{ __('sales_dist.contracts.fields.start_date') }}:</strong> {{ $contract->start_date?->format('Y-m-d') }}</div>
        <div><strong>{{ __('sales_dist.contracts.fields.end_date') }}:</strong> {{ $contract->end_date?->format('Y-m-d') ?: __('sales_dist.common.not_available') }}</div>
        <div><strong>{{ __('sales_dist.contracts.fields.payment_terms') }}:</strong> {{ $contract->payment_terms }}</div>
        <div><strong>{{ __('sales_dist.contracts.fields.credit_limit') }}:</strong> {{ $contract->credit_limit ?? __('sales_dist.common.not_available') }}</div>
        <div><strong>{{ __('sales_dist.contracts.fields.status') }}:</strong> {{ __("sales_dist.status.contract.$contract->status") }}</div>
        <div><strong>{{ __('sales_dist.contracts.fields.notes') }}:</strong> {{ $contract->notes ?: __('sales_dist.common.not_available') }}</div>
    </div></div>

    <div class="card mt-3"><div class="card-body">
        <table class="table align-middle no-datatable sd-export-table"
            data-export-title="{{ __('sales_dist.export.titles.contracts_show') }}"
            data-pdf-orientation="portrait" data-pdf-page-size="A4">
            <thead>
                <tr>
                    <th>{{ __('sales_dist.common.field') }}</th>
                    <th>{{ __('sales_dist.common.value') }}</th>
                </tr>
            </thead>
            <tbody>
                <tr><td>{{ __('sales_dist.contracts.fields.customer') }}</td><td>{{ $contract->customer->name }}</td></tr>
                <tr><td>{{ __('sales_dist.contracts.fields.code') }}</td><td>{{ $contract->contract_code }}</td></tr>
                <tr><td>{{ __('sales_dist.contracts.fields.start_date') }}</td><td>{{ $contract->start_date?->format('Y-m-d') }}</td></tr>
                <tr><td>{{ __('sales_dist.contracts.fields.end_date') }}</td><td>{{ $contract->end_date?->format('Y-m-d') ?: __('sales_dist.common.not_available') }}</td></tr>
                <tr><td>{{ __('sales_dist.contracts.fields.payment_terms') }}</td><td>{{ $contract->payment_terms }}</td></tr>
                <tr><td>{{ __('sales_dist.contracts.fields.credit_limit') }}</td><td>{{ $contract->credit_limit ?? __('sales_dist.common.not_available') }}</td></tr>
                <tr><td>{{ __('sales_dist.contracts.fields.status') }}</td><td>{{ __("sales_dist.status.contract.$contract->status") }}</td></tr>
                <tr><td>{{ __('sales_dist.contracts.fields.notes') }}</td><td>{{ $contract->notes ?: __('sales_dist.common.not_available') }}</td></tr>
            </tbody>
        </table>
    </div></div>
</div>
@endsection
