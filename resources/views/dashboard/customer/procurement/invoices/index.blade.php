@extends('layouts.customer.dashboard')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">{{ __('procurement.invoices.title') }}</h3>
        <a class="btn btn-primary" href="{{ route('customer.procurement.invoices.create', ['locale' => request()->route('locale')]) }}">{{ __('procurement.invoices.add') }}</a>
    </div>

    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif

    <form class="card card-body mb-3" method="GET">
        <div class="row g-2">
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">{{ __('procurement.common.all_status') }}</option>
                    @foreach(['draft','posted','paid','cancelled'] as $value)
                        <option value="{{ $value }}" @selected(request('status') === $value)>{{ __('procurement.status.invoice.' . $value) }}</option>
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
            <div class="col-md-3"><input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}" title="{{ __('procurement.common.date_from') }}"></div>
            <div class="col-md-3"><input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}" title="{{ __('procurement.common.date_to') }}"></div>
            <div class="col-md-12 d-flex gap-2 mt-2">
                <button class="btn btn-outline-primary w-100">{{ __('procurement.common.filter') }}</button>
                <a class="btn btn-light w-100" href="{{ route('customer.procurement.invoices.index', ['locale' => request()->route('locale')]) }}">{{ __('procurement.common.reset') }}</a>
            </div>
        </div>
    </form>

    <div class="card"><div class="card-body">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('procurement.invoices.fields.number') }}</th>
                    <th>{{ __('procurement.invoices.fields.supplier') }}</th>
                    <th>{{ __('procurement.invoices.fields.invoice_date') }}</th>
                    <th>{{ __('procurement.invoices.fields.status') }}</th>
                    <th>{{ __('procurement.invoices.fields.total') }}</th>
                    <th class="text-end no-sort">{{ __('procurement.common.actions') }}</th>
                </tr>
            </thead>
            <tbody>
            @forelse($rows as $row)
                <tr>
                    <td>{{ $row->id }}</td>
                    <td>{{ $row->number }}</td>
                    <td>{{ $row->supplier?->name ?? '-' }}</td>
                    <td>{{ $row->invoice_date?->format('Y-m-d') }}</td>
                    <td>{{ __('procurement.status.invoice.' . $row->status) }}</td>
                    <td>{{ number_format($row->total, 2) }}</td>
                    <td class="text-end">
                        <a class="btn btn-sm btn-outline-info" href="{{ route('customer.procurement.invoices.show', ['locale' => request()->route('locale'), 'invoice' => $row->id]) }}">{{ __('procurement.common.view') }}</a>
                        <a class="btn btn-sm btn-outline-secondary" href="{{ route('customer.procurement.invoices.edit', ['locale' => request()->route('locale'), 'invoice' => $row->id]) }}">{{ __('procurement.common.edit') }}</a>
                        <form class="d-inline" method="POST" action="{{ route('customer.procurement.invoices.destroy', ['locale' => request()->route('locale'), 'invoice' => $row->id]) }}">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" onclick="return confirm('{{ __('procurement.invoices.confirm_delete') }}')">{{ __('procurement.common.delete') }}</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted py-4">{{ __('procurement.invoices.empty') }}</td></tr>
            @endforelse
            </tbody>
        </table>
        {{ $rows->links() }}
    </div></div>
</div>
@endsection
