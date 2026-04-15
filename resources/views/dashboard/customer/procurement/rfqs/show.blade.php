@extends('layouts.customer.dashboard')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">{{ __('procurement.rfqs.show_title') }}</h3>
        <div class="d-flex gap-2">
            <a class="btn btn-outline-primary" href="{{ route('customer.procurement.quotations.create', ['locale' => request()->route('locale'), 'rfq_id' => $rfq->id]) }}">{{ __('procurement.rfqs.actions.create_quotation') }}</a>
            <a class="btn btn-outline-secondary" href="{{ route('customer.procurement.rfqs.index', ['locale' => request()->route('locale')]) }}">{{ __('procurement.common.back') }}</a>
        </div>
    </div>

    <div class="card mb-3"><div class="card-body">
        <div class="row g-3">
            <div class="col-md-4"><strong>{{ __('procurement.rfqs.fields.code') }}:</strong> {{ $rfq->code }}</div>
            <div class="col-md-4"><strong>{{ __('procurement.rfqs.fields.requisition') }}:</strong> {{ $rfq->requisition?->code ?? '-' }}</div>
            <div class="col-md-4"><strong>{{ __('procurement.rfqs.fields.status') }}:</strong> {{ __('procurement.status.rfq.' . $rfq->status) }}</div>
        </div>
    </div></div>

    <div class="card"><div class="card-body">
        <h5 class="mb-3">{{ __('procurement.quotations.title') }}</h5>
        <table class="table align-middle">
            <thead><tr><th>#</th><th>{{ __('procurement.quotations.fields.supplier') }}</th><th>{{ __('procurement.quotations.fields.status') }}</th><th>{{ __('procurement.quotations.fields.total') }}</th><th class="text-end">{{ __('procurement.common.actions') }}</th></tr></thead>
            <tbody>
            @forelse($rfq->quotations as $quotation)
                <tr>
                    <td>{{ $quotation->id }}</td>
                    <td>{{ $quotation->supplier?->name ?? '-' }}</td>
                    <td>{{ __('procurement.status.quotation.' . $quotation->status) }}</td>
                    <td>{{ number_format($quotation->total, 2) }}</td>
                    <td class="text-end">
                        <a class="btn btn-sm btn-outline-info" href="{{ route('customer.procurement.quotations.show', ['locale' => request()->route('locale'), 'quotation' => $quotation->id]) }}">{{ __('procurement.common.view') }}</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted">{{ __('procurement.quotations.empty') }}</td></tr>
            @endforelse
            </tbody>
        </table>
    </div></div>
</div>
@endsection
