@extends('layouts.customer.dashboard')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">{{ __('procurement.rfqs.title') }}</h3>
        <a class="btn btn-primary" href="{{ route('customer.procurement.rfqs.create', ['locale' => request()->route('locale')]) }}">{{ __('procurement.rfqs.add') }}</a>
    </div>

    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif

    <form class="card card-body mb-3" method="GET">
        <div class="row g-2">
            <div class="col-md-4">
                <select name="status" class="form-select">
                    <option value="">{{ __('procurement.common.all_status') }}</option>
                    @foreach(['open','sent','closed','awarded'] as $value)
                        <option value="{{ $value }}" @selected(request('status') === $value)>{{ __('procurement.status.rfq.' . $value) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <select name="purchase_requisition_id" class="form-select">
                    <option value="">{{ __('procurement.common.select') }}</option>
                    @foreach($requisitions as $req)
                        <option value="{{ $req->id }}" @selected((string) request('purchase_requisition_id') === (string) $req->id)>{{ $req->code }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button class="btn btn-outline-primary w-100">{{ __('procurement.common.filter') }}</button>
                <a class="btn btn-light w-100" href="{{ route('customer.procurement.rfqs.index', ['locale' => request()->route('locale')]) }}">{{ __('procurement.common.reset') }}</a>
            </div>
        </div>
    </form>

    <div class="card"><div class="card-body">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('procurement.rfqs.fields.code') }}</th>
                    <th>{{ __('procurement.rfqs.fields.requisition') }}</th>
                    <th>{{ __('procurement.rfqs.fields.status') }}</th>
                    <th class="text-end no-sort">{{ __('procurement.common.actions') }}</th>
                </tr>
            </thead>
            <tbody>
            @forelse($rows as $row)
                <tr>
                    <td>{{ $row->id }}</td>
                    <td>{{ $row->code }}</td>
                    <td>{{ $row->requisition?->code ?? '-' }}</td>
                    <td>{{ __('procurement.status.rfq.' . $row->status) }}</td>
                    <td class="text-end">
                        <a class="btn btn-sm btn-outline-info" href="{{ route('customer.procurement.rfqs.show', ['locale' => request()->route('locale'), 'rfq' => $row->id]) }}">{{ __('procurement.common.view') }}</a>
                        <a class="btn btn-sm btn-outline-secondary" href="{{ route('customer.procurement.rfqs.edit', ['locale' => request()->route('locale'), 'rfq' => $row->id]) }}">{{ __('procurement.common.edit') }}</a>
                        <form class="d-inline" method="POST" action="{{ route('customer.procurement.rfqs.destroy', ['locale' => request()->route('locale'), 'rfq' => $row->id]) }}">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" onclick="return confirm('{{ __('procurement.rfqs.confirm_delete') }}')">{{ __('procurement.common.delete') }}</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted py-4">{{ __('procurement.rfqs.empty') }}</td></tr>
            @endforelse
            </tbody>
        </table>
        {{ $rows->links() }}
    </div></div>
</div>
@endsection
