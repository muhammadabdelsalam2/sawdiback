@extends('layouts.customer.dashboard')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">{{ __('procurement.goods_receipts.show_title') }}</h3>
        <div class="d-flex gap-2">
            <a class="btn btn-outline-primary" href="{{ route('customer.procurement.invoices.create', ['locale' => request()->route('locale'), 'goods_receipt_id' => $receipt->id, 'purchase_order_id' => $receipt->purchase_order_id]) }}">{{ __('procurement.goods_receipts.actions.create_invoice') }}</a>
            <a class="btn btn-outline-secondary" href="{{ route('customer.procurement.goods-receipts.index', ['locale' => request()->route('locale')]) }}">{{ __('procurement.common.back') }}</a>
        </div>
    </div>

    <div class="card mb-3"><div class="card-body">
        <div class="row g-3">
            <div class="col-md-4"><strong>{{ __('procurement.goods_receipts.fields.grn_number') }}:</strong> {{ $receipt->grn_number }}</div>
            <div class="col-md-4"><strong>{{ __('procurement.goods_receipts.fields.purchase_order') }}:</strong> {{ $receipt->purchaseOrder?->po_number ?? '-' }}</div>
            <div class="col-md-4"><strong>{{ __('procurement.goods_receipts.fields.received_by') }}:</strong> {{ $receipt->receiver?->name ?? '-' }}</div>
            <div class="col-md-4"><strong>{{ __('procurement.goods_receipts.fields.status') }}:</strong> {{ __('procurement.status.goods_receipt.' . $receipt->status) }}</div>
        </div>
    </div></div>

    <div class="card"><div class="card-body">
        <h5 class="mb-3">{{ __('procurement.goods_receipts.items.title') }}</h5>
        <table class="table align-middle">
            <thead><tr><th>{{ __('procurement.goods_receipts.items.product') }}</th><th>{{ __('procurement.goods_receipts.items.quantity') }}</th></tr></thead>
            <tbody>
            @foreach($receipt->items as $item)
                <tr>
                    <td>{{ $item->product?->name ?? $item->product_id }}</td>
                    <td>{{ number_format($item->quantity, 2) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div></div>
</div>
@endsection
