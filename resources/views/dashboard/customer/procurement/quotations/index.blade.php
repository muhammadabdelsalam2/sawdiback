@extends('layouts.customer.dashboard')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">{{ __('procurement.quotations.title') }}</h3>
        <a class="btn btn-primary" href="{{ route('customer.procurement.quotations.create', ['locale' => request()->route('locale')]) }}">{{ __('procurement.quotations.add') }}</a>
    </div>

    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif

    <form class="card card-body mb-3" method="GET">
        <div class="row g-2">
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">{{ __('procurement.common.all_status') }}</option>
                    @foreach(['submitted','selected','rejected'] as $value)
                        <option value="{{ $value }}" @selected(request('status') === $value)>{{ __('procurement.status.quotation.' . $value) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="rfq_id" class="form-select">
                    <option value="">{{ __('procurement.common.select') }}</option>
                    @foreach($rfqs as $rfq)
                        <option value="{{ $rfq->id }}" @selected((string) request('rfq_id') === (string) $rfq->id)>{{ $rfq->code }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="supplier_id" class="form-select">
                    <option value="">{{ __('procurement.common.select') }}</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" @selected((string) request('supplier_id') === (string) $supplier->id)>{{ $supplier->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button class="btn btn-outline-primary w-100">{{ __('procurement.common.filter') }}</button>
                <a class="btn btn-light w-100" href="{{ route('customer.procurement.quotations.index', ['locale' => request()->route('locale')]) }}">{{ __('procurement.common.reset') }}</a>
            </div>
        </div>
    </form>

    <div class="card"><div class="card-body">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('procurement.quotations.fields.rfq') }}</th>
                    <th>{{ __('procurement.quotations.fields.supplier') }}</th>
                    <th>{{ __('procurement.quotations.fields.status') }}</th>
                    <th>{{ __('procurement.quotations.fields.total') }}</th>
                    <th class="text-end no-sort">{{ __('procurement.common.actions') }}</th>
                </tr>
            </thead>
            <tbody>
            @forelse($rows as $row)
                <tr>
                    <td>{{ $row->id }}</td>
                    <td>{{ $row->rfq?->code ?? '-' }}</td>
                    <td>{{ $row->supplier?->name ?? '-' }}</td>
                    <td>{{ __('procurement.status.quotation.' . $row->status) }}</td>
                    <td>{{ number_format($row->total, 2) }}</td>
                    <td class="text-end">
                        <a class="btn btn-sm btn-outline-info" href="{{ route('customer.procurement.quotations.show', ['locale' => request()->route('locale'), 'quotation' => $row->id]) }}">{{ __('procurement.common.view') }}</a>
                        <a class="btn btn-sm btn-outline-secondary" href="{{ route('customer.procurement.quotations.edit', ['locale' => request()->route('locale'), 'quotation' => $row->id]) }}">{{ __('procurement.common.edit') }}</a>
                        <form class="d-inline" method="POST" action="{{ route('customer.procurement.quotations.destroy', ['locale' => request()->route('locale'), 'quotation' => $row->id]) }}">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" onclick="return confirm('{{ __('procurement.quotations.confirm_delete') }}')">{{ __('procurement.common.delete') }}</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-4">{{ __('procurement.quotations.empty') }}</td></tr>
            @endforelse
            </tbody>
        </table>
        {{ $rows->links() }}
    </div></div>
</div>
@endsection
