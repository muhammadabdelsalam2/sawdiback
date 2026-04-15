@extends('layouts.customer.dashboard')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">{{ __('procurement.requisitions.show_title') }}</h3>
        <div class="d-flex gap-2">
            <a class="btn btn-outline-primary" href="{{ route('customer.procurement.rfqs.create', ['locale' => request()->route('locale'), 'purchase_requisition_id' => $requisition->id]) }}">{{ __('procurement.requisitions.actions.create_rfq') }}</a>
            <a class="btn btn-outline-secondary" href="{{ route('customer.procurement.requisitions.index', ['locale' => request()->route('locale')]) }}">{{ __('procurement.common.back') }}</a>
        </div>
    </div>

    <div class="card mb-3"><div class="card-body">
        <div class="row g-3">
            <div class="col-md-4"><strong>{{ __('procurement.requisitions.fields.code') }}:</strong> {{ $requisition->code }}</div>
            <div class="col-md-4"><strong>{{ __('procurement.requisitions.fields.department') }}:</strong> {{ $requisition->department?->name ?? '-' }}</div>
            <div class="col-md-4"><strong>{{ __('procurement.requisitions.fields.requested_by') }}:</strong> {{ $requisition->requester?->name ?? '-' }}</div>
            <div class="col-md-4"><strong>{{ __('procurement.requisitions.fields.status') }}:</strong> {{ __('procurement.status.requisition.' . $requisition->status) }}</div>
            <div class="col-md-8"><strong>{{ __('procurement.requisitions.fields.notes') }}:</strong> {{ $requisition->notes ?? '-' }}</div>
        </div>
    </div></div>

    <div class="card mb-3"><div class="card-body">
        <h5 class="mb-3">{{ __('procurement.requisitions.items.title') }}</h5>
        <table class="table align-middle">
            <thead><tr><th>{{ __('procurement.requisitions.items.product') }}</th><th>{{ __('procurement.requisitions.items.quantity') }}</th><th>{{ __('procurement.requisitions.items.estimated_price') }}</th></tr></thead>
            <tbody>
            @foreach($requisition->items as $item)
                <tr>
                    <td>{{ $item->product?->name ?? $item->product_id }}</td>
                    <td>{{ number_format($item->quantity, 2) }}</td>
                    <td>{{ number_format($item->estimated_price ?? 0, 2) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div></div>

    <div class="card"><div class="card-body">
        <h5 class="mb-3">{{ __('procurement.rfqs.title') }}</h5>
        <table class="table align-middle">
            <thead><tr><th>#</th><th>{{ __('procurement.rfqs.fields.code') }}</th><th>{{ __('procurement.rfqs.fields.status') }}</th><th class="text-end">{{ __('procurement.common.actions') }}</th></tr></thead>
            <tbody>
            @forelse($requisition->rfqs as $rfq)
                <tr>
                    <td>{{ $rfq->id }}</td>
                    <td>{{ $rfq->code }}</td>
                    <td>{{ __('procurement.status.rfq.' . $rfq->status) }}</td>
                    <td class="text-end">
                        <a class="btn btn-sm btn-outline-info" href="{{ route('customer.procurement.rfqs.show', ['locale' => request()->route('locale'), 'rfq' => $rfq->id]) }}">{{ __('procurement.common.view') }}</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-center text-muted">{{ __('procurement.rfqs.empty') }}</td></tr>
            @endforelse
            </tbody>
        </table>
    </div></div>
</div>
@endsection
