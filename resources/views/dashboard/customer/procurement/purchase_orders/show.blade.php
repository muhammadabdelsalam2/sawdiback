@extends('layouts.customer.dashboard')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">{{ __('procurement.purchase_orders.show_title') }}</h3>
        <div class="d-flex gap-2">
            <a class="btn btn-outline-primary" href="{{ route('customer.procurement.goods-receipts.create', ['locale' => request()->route('locale'), 'purchase_order_id' => $order->id]) }}">{{ __('procurement.purchase_orders.actions.create_grn') }}</a>
            <a class="btn btn-outline-secondary" href="{{ route('customer.procurement.purchase-orders.index', ['locale' => request()->route('locale')]) }}">{{ __('procurement.common.back') }}</a>
        </div>
    </div>

    <div class="card mb-3"><div class="card-body">
        <div class="row g-3">
            <div class="col-md-4"><strong>{{ __('procurement.purchase_orders.fields.po_number') }}:</strong> {{ $order->po_number }}</div>
            <div class="col-md-4"><strong>{{ __('procurement.purchase_orders.fields.supplier') }}:</strong> {{ $order->supplier?->name ?? '-' }}</div>
            <div class="col-md-4"><strong>{{ __('procurement.purchase_orders.fields.status') }}:</strong> {{ __('procurement.status.purchase_order.' . $order->status) }}</div>
            <div class="col-md-4"><strong>{{ __('procurement.purchase_orders.fields.total') }}:</strong> {{ number_format($order->total, 2) }}</div>
            <div class="col-md-4"><strong>{{ __('procurement.purchase_orders.fields.vat') }}:</strong> {{ number_format($order->vat, 2) }}</div>
            <div class="col-md-4"><strong>{{ __('procurement.purchase_orders.fields.net_total') }}:</strong> {{ number_format($order->net_total, 2) }}</div>
            <div class="col-md-4"><strong>{{ __('procurement.purchase_orders.fields.rfq') }}:</strong> {{ $order->rfq?->code ?? '-' }}</div>
            <div class="col-md-4"><strong>{{ __('procurement.purchase_orders.fields.quotation') }}:</strong> {{ $order->quotation?->id ?? '-' }}</div>
        </div>
    </div></div>

    <div class="card mb-3"><div class="card-body">
        <h5 class="mb-3">{{ __('procurement.purchase_orders.items.title') }}</h5>
        <table class="table align-middle">
            <thead><tr><th>{{ __('procurement.purchase_orders.items.product') }}</th><th>{{ __('procurement.purchase_orders.items.quantity') }}</th><th>{{ __('procurement.purchase_orders.items.unit_price') }}</th><th>{{ __('procurement.purchase_orders.items.total') }}</th></tr></thead>
            <tbody>
            @foreach($order->items as $item)
                <tr>
                    <td>{{ $item->product?->name ?? $item->product_id }}</td>
                    <td>{{ number_format($item->quantity, 2) }}</td>
                    <td>{{ number_format($item->unit_price, 2) }}</td>
                    <td>{{ number_format($item->total, 2) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div></div>

    <div class="card"><div class="card-body">
        <h5 class="mb-3">{{ __('procurement.goods_receipts.title') }}</h5>
        <table class="table align-middle">
            <thead><tr><th>#</th><th>{{ __('procurement.goods_receipts.fields.grn_number') }}</th><th>{{ __('procurement.goods_receipts.fields.status') }}</th><th class="text-end">{{ __('procurement.common.actions') }}</th></tr></thead>
            <tbody>
            @forelse($order->goodsReceipts as $receipt)
                <tr>
                    <td>{{ $receipt->id }}</td>
                    <td>{{ $receipt->grn_number }}</td>
                    <td>{{ __('procurement.status.goods_receipt.' . $receipt->status) }}</td>
                    <td class="text-end">
                        <a class="btn btn-sm btn-outline-info" href="{{ route('customer.procurement.goods-receipts.show', ['locale' => request()->route('locale'), 'receipt' => $receipt->id]) }}">{{ __('procurement.common.view') }}</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-center text-muted">{{ __('procurement.goods_receipts.empty') }}</td></tr>
            @endforelse
            </tbody>
        </table>
    </div></div>
</div>
@endsection
