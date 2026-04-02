@extends('layouts.customer.dashboard')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">{{ __('procurement.goods_receipts.title') }}</h3>
        <a class="btn btn-primary" href="{{ route('customer.procurement.goods-receipts.create', ['locale' => request()->route('locale')]) }}">{{ __('procurement.goods_receipts.add') }}</a>
    </div>

    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif

    <form class="card card-body mb-3" method="GET">
        <div class="row g-2">
            <div class="col-md-4">
                <select name="status" class="form-select">
                    <option value="">{{ __('procurement.common.all_status') }}</option>
                    @foreach(['partial','completed'] as $value)
                        <option value="{{ $value }}" @selected(request('status') === $value)>{{ __('procurement.status.goods_receipt.' . $value) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <select name="purchase_order_id" class="form-select">
                    <option value="">{{ __('procurement.common.select') }}</option>
                    @foreach($orders as $order)
                        <option value="{{ $order->id }}" @selected((string) request('purchase_order_id') === (string) $order->id)>{{ $order->po_number }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button class="btn btn-outline-primary w-100">{{ __('procurement.common.filter') }}</button>
                <a class="btn btn-light w-100" href="{{ route('customer.procurement.goods-receipts.index', ['locale' => request()->route('locale')]) }}">{{ __('procurement.common.reset') }}</a>
            </div>
        </div>
    </form>

    <div class="card"><div class="card-body">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('procurement.goods_receipts.fields.grn_number') }}</th>
                    <th>{{ __('procurement.goods_receipts.fields.purchase_order') }}</th>
                    <th>{{ __('procurement.goods_receipts.fields.status') }}</th>
                    <th class="text-end no-sort">{{ __('procurement.common.actions') }}</th>
                </tr>
            </thead>
            <tbody>
            @forelse($rows as $row)
                <tr>
                    <td>{{ $row->id }}</td>
                    <td>{{ $row->grn_number }}</td>
                    <td>{{ $row->purchaseOrder?->po_number ?? '-' }}</td>
                    <td>{{ __('procurement.status.goods_receipt.' . $row->status) }}</td>
                    <td class="text-end">
                        <a class="btn btn-sm btn-outline-info" href="{{ route('customer.procurement.goods-receipts.show', ['locale' => request()->route('locale'), 'receipt' => $row->id]) }}">{{ __('procurement.common.view') }}</a>
                        <a class="btn btn-sm btn-outline-secondary" href="{{ route('customer.procurement.goods-receipts.edit', ['locale' => request()->route('locale'), 'receipt' => $row->id]) }}">{{ __('procurement.common.edit') }}</a>
                        <form class="d-inline" method="POST" action="{{ route('customer.procurement.goods-receipts.destroy', ['locale' => request()->route('locale'), 'receipt' => $row->id]) }}">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" onclick="return confirm('{{ __('procurement.goods_receipts.confirm_delete') }}')">{{ __('procurement.common.delete') }}</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted py-4">{{ __('procurement.goods_receipts.empty') }}</td></tr>
            @endforelse
            </tbody>
        </table>
        {{ $rows->links() }}
    </div></div>
</div>
@endsection
