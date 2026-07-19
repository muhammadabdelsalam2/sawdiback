@extends('layouts.customer.dashboard')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">{{ __('sales_dist.orders.show_title') }}</h3>
        <a class="btn btn-outline-secondary" href="{{ route('customer.sales-distribution.orders.index', ['locale' => request()->route('locale')]) }}">{{ __('sales_dist.common.back') }}</a>
    </div>

    <div class="card mb-3"><div class="card-body">
        <div><strong>{{ __('sales_dist.orders.fields.order_no') }}:</strong> {{ $order->order_no }}</div>
        <div><strong>{{ __('sales_dist.orders.fields.customer') }}:</strong> {{ $order->customer->name }}</div>
        <div><strong>{{ __('sales_dist.orders.fields.contract') }}:</strong> {{ $order->contract?->contract_code ?: __('sales_dist.common.not_available') }}</div>
        <div><strong>{{ __('sales_dist.orders.fields.status') }}:</strong> {{ __("sales_dist.status.order.$order->status") }}</div>
        <div><strong>{{ __('sales_dist.orders.fields.order_date') }}:</strong> {{ $order->order_date?->format('Y-m-d') }}</div>
        <div><strong>{{ __('sales_dist.invoices.fields.tax') }}:</strong> {{ number_format($order->invoices->sum('tax') ?? 0, 2) }}</div>
        <div><strong>{{ __('sales_dist.orders.fields.total') }}:</strong> {{ number_format($order->total, 2) }}</div>
        <div><strong>{{ __('sales_dist.orders.fields.notes') }}:</strong> {{ $order->notes ?: __('sales_dist.common.not_available') }}</div>
    </div></div>

    <div class="card"><div class="card-body">
        <h5>{{ __('sales_dist.orders.items.title') }}</h5>
        <table class="table align-middle no-datatable sd-export-table"
            data-export-title="{{ $orderItemsExportTitle }}"
            data-pdf-orientation="portrait" data-pdf-page-size="A4">
            <thead><tr><th>{{ __('sales_dist.orders.items.product_id') }}</th><th>{{ __('sales_dist.orders.items.product_category') }}</th><th>{{ __('sales_dist.orders.items.qty') }}</th><th>{{ __('sales_dist.orders.items.unit_price') }}</th><th>{{ __('sales_dist.orders.items.discount') }}</th><th>{{ __('sales_dist.orders.items.line_total') }}</th></tr></thead>
            <tbody>
                @forelse($order->items as $item)
                <tr>
                    <td>{{ $item->product_id }}</td>
                    <td>{{ __('sales_dist.product_categories.' . ($item->product_category ?? 'other')) }}</td>
                    <td>{{ number_format($item->qty, 2) }}</td>
                    <td>{{ number_format($item->unit_price, 2) }}</td>
                    <td>{{ number_format($item->discount, 2) }}</td>
                    <td>{{ number_format($item->line_total, 2) }}</td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted">{{ __('sales_dist.orders.items.empty') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div></div>
</div>
@endsection
