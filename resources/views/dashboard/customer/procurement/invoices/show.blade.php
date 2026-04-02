@extends('layouts.customer.dashboard')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">{{ __('procurement.invoices.show_title') }}</h3>
        <a class="btn btn-outline-secondary" href="{{ route('customer.procurement.invoices.index', ['locale' => request()->route('locale')]) }}">{{ __('procurement.common.back') }}</a>
    </div>

    <div class="card"><div class="card-body">
        <div class="row g-3">
            <div class="col-md-4"><strong>{{ __('procurement.invoices.fields.number') }}:</strong> {{ $invoice->number }}</div>
            <div class="col-md-4"><strong>{{ __('procurement.invoices.fields.supplier') }}:</strong> {{ $invoice->supplier?->name ?? '-' }}</div>
            <div class="col-md-4"><strong>{{ __('procurement.invoices.fields.department') }}:</strong> {{ $invoice->department?->name ?? '-' }}</div>
            <div class="col-md-4"><strong>{{ __('procurement.invoices.fields.purchase_order') }}:</strong> {{ $invoice->purchaseOrder?->po_number ?? '-' }}</div>
            <div class="col-md-4"><strong>{{ __('procurement.invoices.fields.goods_receipt') }}:</strong> {{ $invoice->goodsReceipt?->grn_number ?? '-' }}</div>
            <div class="col-md-4"><strong>{{ __('procurement.invoices.fields.invoice_date') }}:</strong> {{ $invoice->invoice_date?->format('Y-m-d') }}</div>
            <div class="col-md-4"><strong>{{ __('procurement.invoices.fields.status') }}:</strong> {{ __('procurement.status.invoice.' . $invoice->status) }}</div>
            <div class="col-md-4"><strong>{{ __('procurement.invoices.fields.subtotal') }}:</strong> {{ number_format($invoice->subtotal, 2) }}</div>
            <div class="col-md-4"><strong>{{ __('procurement.invoices.fields.tax') }}:</strong> {{ number_format($invoice->tax, 2) }}</div>
            <div class="col-md-4"><strong>{{ __('procurement.invoices.fields.discount') }}:</strong> {{ number_format($invoice->discount, 2) }}</div>
            <div class="col-md-4"><strong>{{ __('procurement.invoices.fields.total') }}:</strong> {{ number_format($invoice->total, 2) }}</div>
            <div class="col-md-12"><strong>{{ __('procurement.invoices.fields.notes') }}:</strong> {{ $invoice->notes ?? '-' }}</div>
        </div>
    </div></div>
</div>
@endsection
