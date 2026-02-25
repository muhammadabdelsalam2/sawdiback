@extends('layouts.customer.dashboard')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">{{ __('sales_dist.shipments.show_title') }}</h3>
        <a class="btn btn-outline-secondary" href="{{ route('customer.sales-distribution.shipments.index', ['locale' => request()->route('locale')]) }}">{{ __('sales_dist.common.back') }}</a>
    </div>

    <div class="card mb-3"><div class="card-body">
        <div><strong>{{ __('sales_dist.shipments.fields.shipment_no') }}:</strong> {{ $shipment->shipment_no }}</div>
        <div><strong>{{ __('sales_dist.shipments.fields.order_no') }}:</strong> {{ $shipment->order->order_no }}</div>
        <div><strong>{{ __('sales_dist.shipments.fields.customer') }}:</strong> {{ $shipment->order->customer->name }}</div>
        <div><strong>{{ __('sales_dist.common.status') }}:</strong> {{ __("sales_dist.status.shipment.$shipment->status") }}</div>
        <div><strong>{{ __('sales_dist.shipments.fields.shipping_company') }}:</strong> {{ $shipment->shipping_company }}</div>
        <div><strong>{{ __('sales_dist.shipments.fields.tracking_no') }}:</strong> {{ $shipment->tracking_no ?: __('sales_dist.common.not_available') }}</div>
        <div><strong>{{ __('sales_dist.shipments.fields.warehouse_id') }}:</strong> {{ $shipment->warehouse_id ?: __('sales_dist.common.not_available') }}</div>
        <div><strong>{{ __('sales_dist.shipments.fields.shipped_at') }}:</strong> {{ $shipment->shipped_at?->format('Y-m-d H:i') ?: __('sales_dist.common.not_available') }}</div>
        <div><strong>{{ __('sales_dist.shipments.fields.delivered_at') }}:</strong> {{ $shipment->delivered_at?->format('Y-m-d H:i') ?: __('sales_dist.common.not_available') }}</div>
        <div><strong>{{ __('sales_dist.shipments.fields.notes') }}:</strong> {{ $shipment->notes ?: __('sales_dist.common.not_available') }}</div>
    </div></div>

    <div class="card"><div class="card-body">
        <h5>{{ __('sales_dist.shipments.timeline.title') }}</h5>
        <table class="table align-middle no-datatable sd-export-table"
            data-export-title="{{ $shipmentHistoryExportTitle }}"
            data-print-scope="page"
            data-pdf-orientation="landscape" data-pdf-page-size="A4">
            <thead>
                <tr>
                    <th>{{ __('sales_dist.common.status') }}</th>
                    <th>{{ __('sales_dist.shipments.timeline.from') }}</th>
                    <th>{{ __('sales_dist.shipments.fields.notes') }}</th>
                    <th>{{ __('sales_dist.shipments.timeline.changed_at') }}</th>
                </tr>
            </thead>
            <tbody>
            @forelse($shipment->statusHistory as $entry)
                <tr>
                    <td>{{ __("sales_dist.status.shipment.$entry->to_status") }}</td>
                    <td>{{ $entry->from_status ? __("sales_dist.status.shipment.$entry->from_status") : __('sales_dist.common.not_available') }}</td>
                    <td>{{ $entry->notes ?: __('sales_dist.common.not_available') }}</td>
                    <td>{{ $entry->changed_at?->format('Y-m-d H:i') ?: __('sales_dist.common.not_available') }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-muted text-center">{{ __('sales_dist.shipments.timeline.empty') }}</td></tr>
            @endforelse
            </tbody>
        </table>
    </div></div>
</div>
@endsection
