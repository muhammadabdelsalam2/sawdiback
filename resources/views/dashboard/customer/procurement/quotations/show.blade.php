@extends('layouts.customer.dashboard')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">{{ __('procurement.quotations.show_title') }}</h3>
        <div class="d-flex gap-2">
            <a class="btn btn-outline-primary" href="{{ route('customer.procurement.purchase-orders.create', ['locale' => request()->route('locale'), 'quotation_id' => $quotation->id, 'rfq_id' => $quotation->rfq_id]) }}">{{ __('procurement.quotations.actions.create_po') }}</a>
            <a class="btn btn-outline-secondary" href="{{ route('customer.procurement.quotations.index', ['locale' => request()->route('locale')]) }}">{{ __('procurement.common.back') }}</a>
        </div>
    </div>

    <div class="card mb-3"><div class="card-body">
        <div class="row g-3">
            <div class="col-md-4"><strong>{{ __('procurement.quotations.fields.rfq') }}:</strong> {{ $quotation->rfq?->code ?? '-' }}</div>
            <div class="col-md-4"><strong>{{ __('procurement.quotations.fields.supplier') }}:</strong> {{ $quotation->supplier?->name ?? '-' }}</div>
            <div class="col-md-4"><strong>{{ __('procurement.quotations.fields.status') }}:</strong> {{ __('procurement.status.quotation.' . $quotation->status) }}</div>
        </div>
    </div></div>

    <div class="card"><div class="card-body">
        <h5 class="mb-3">{{ __('procurement.quotations.items.title') }}</h5>
        <table class="table align-middle">
            <thead><tr><th>{{ __('procurement.quotations.items.product') }}</th><th>{{ __('procurement.quotations.items.quantity') }}</th><th>{{ __('procurement.quotations.items.unit_price') }}</th><th>{{ __('procurement.quotations.items.total') }}</th></tr></thead>
            <tbody>
            @foreach($quotation->items as $item)
                <tr>
                    <td>{{ $item->product?->name ?? $item->product_id }}</td>
                    <td>{{ number_format($item->quantity, 2) }}</td>
                    <td>{{ number_format($item->unit_price, 2) }}</td>
                    <td>{{ number_format($item->total, 2) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="text-end"><strong>{{ __('procurement.quotations.fields.total') }}:</strong> {{ number_format($quotation->total, 2) }}</div>
    </div></div>
</div>
@endsection
