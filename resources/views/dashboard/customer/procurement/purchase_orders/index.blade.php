@extends('layouts.customer.dashboard')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">{{ __('procurement.purchase_orders.title') }}</h3>
        <a class="btn btn-primary" href="{{ route('customer.procurement.purchase-orders.create', ['locale' => request()->route('locale')]) }}">{{ __('procurement.purchase_orders.add') }}</a>
    </div>

    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif

    <form class="card card-body mb-3" method="GET">
        <div class="row g-2">
            <div class="col-md-4">
                <select name="status" class="form-select">
                    <option value="">{{ __('procurement.common.all_status') }}</option>
                    @foreach(['draft','confirmed','partially_received','received','closed'] as $value)
                        <option value="{{ $value }}" @selected(request('status') === $value)>{{ __('procurement.status.purchase_order.' . $value) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <select name="supplier_id" class="form-select">
                    <option value="">{{ __('procurement.common.select') }}</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" @selected((string) request('supplier_id') === (string) $supplier->id)>{{ $supplier->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button class="btn btn-outline-primary w-100">{{ __('procurement.common.filter') }}</button>
                <a class="btn btn-light w-100" href="{{ route('customer.procurement.purchase-orders.index', ['locale' => request()->route('locale')]) }}">{{ __('procurement.common.reset') }}</a>
            </div>
        </div>
    </form>

    <div class="card"><div class="card-body">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('procurement.purchase_orders.fields.po_number') }}</th>
                    <th>{{ __('procurement.purchase_orders.fields.supplier') }}</th>
                    <th>{{ __('procurement.purchase_orders.fields.status') }}</th>
                    <th>{{ __('procurement.purchase_orders.fields.net_total') }}</th>
                    <th class="text-end no-sort">{{ __('procurement.common.actions') }}</th>
                </tr>
            </thead>
            <tbody>
            @forelse($rows as $row)
                <tr>
                    <td>{{ $row->id }}</td>
                    <td>{{ $row->po_number }}</td>
                    <td>{{ $row->supplier?->name ?? '-' }}</td>
                    <td>{{ __('procurement.status.purchase_order.' . $row->status) }}</td>
                    <td>{{ number_format($row->net_total, 2) }}</td>
                    <td class="text-end">
                        <a class="btn btn-sm btn-outline-info" href="{{ route('customer.procurement.purchase-orders.show', ['locale' => request()->route('locale'), 'order' => $row->id]) }}">{{ __('procurement.common.view') }}</a>
                        <a class="btn btn-sm btn-outline-secondary" href="{{ route('customer.procurement.purchase-orders.edit', ['locale' => request()->route('locale'), 'order' => $row->id]) }}">{{ __('procurement.common.edit') }}</a>
                        <form class="d-inline" method="POST" action="{{ route('customer.procurement.purchase-orders.destroy', ['locale' => request()->route('locale'), 'order' => $row->id]) }}">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" onclick="return confirm('{{ __('procurement.purchase_orders.confirm_delete') }}')">{{ __('procurement.common.delete') }}</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-4">{{ __('procurement.purchase_orders.empty') }}</td></tr>
            @endforelse
            </tbody>
        </table>
        {{ $rows->links() }}
    </div></div>
</div>
@endsection
