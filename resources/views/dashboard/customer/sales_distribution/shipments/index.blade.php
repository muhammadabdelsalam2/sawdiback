@extends('layouts.customer.dashboard')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">{{ __('sales_dist.shipments.title') }}</h3>
        <a class="btn btn-primary" href="{{ route('customer.sales-distribution.shipments.create', ['locale' => request()->route('locale')]) }}">{{ __('sales_dist.shipments.add') }}</a>
    </div>

    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif

    <form class="card card-body mb-3" method="GET">
        <div class="row g-2">
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">{{ __('sales_dist.common.all_status') }}</option>
                    @foreach(['pending', 'packed', 'shipped', 'delivered', 'returned'] as $value)
                        <option value="{{ $value }}" @selected(request('status') === $value)>{{ __("sales_dist.status.shipment.$value") }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3"><input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}" title="{{ __('sales_dist.common.date_from') }}"></div>
            <div class="col-md-3"><input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}" title="{{ __('sales_dist.common.date_to') }}"></div>
            <div class="col-md-3 d-flex gap-2">
                <button class="btn btn-outline-primary w-100">{{ __('sales_dist.common.filter') }}</button>
                <a class="btn btn-light w-100" href="{{ route('customer.sales-distribution.shipments.index', ['locale' => request()->route('locale')]) }}">{{ __('sales_dist.common.reset') }}</a>
            </div>
        </div>
    </form>

    <div class="card"><div class="card-body">
        <table class="table align-middle no-datatable sd-export-table"
            data-export-title="{{ __('sales_dist.export.titles.shipments_index') }}"
            data-pdf-orientation="landscape" data-pdf-page-size="A4">
            <thead><tr><th>#</th><th>{{ __('sales_dist.shipments.fields.shipment_no') }}</th><th>{{ __('sales_dist.shipments.fields.order') }}</th><th>{{ __('sales_dist.common.status') }}</th><th>{{ __('sales_dist.shipments.fields.tracking_no') }}</th><th class="text-end no-sort no-export">{{ __('sales_dist.common.actions') }}</th></tr></thead>
            <tbody>
            @forelse($shipments as $shipment)
                <tr>
                    <td>{{ $shipment->id }}</td>
                    <td>{{ $shipment->shipment_no }}</td>
                    <td>{{ $shipment->order->order_no }}</td>
                    <td>{{ __("sales_dist.status.shipment.$shipment->status") }}</td>
                    <td>{{ $shipment->tracking_no ?: __('sales_dist.common.not_available') }}</td>
                    <td class="text-end">
                        <a class="btn btn-sm btn-outline-info" href="{{ route('customer.sales-distribution.shipments.show', ['locale' => request()->route('locale'), 'shipment' => $shipment->id]) }}">{{ __('sales_dist.common.view') }}</a>
                        <a class="btn btn-sm btn-outline-secondary" href="{{ route('customer.sales-distribution.shipments.edit', ['locale' => request()->route('locale'), 'shipment' => $shipment->id]) }}">{{ __('sales_dist.common.edit') }}</a>
                        <form class="d-inline" method="POST" action="{{ route('customer.sales-distribution.shipments.destroy', ['locale' => request()->route('locale'), 'shipment' => $shipment->id]) }}">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" onclick="return confirm('{{ __('sales_dist.shipments.confirm_delete') }}')">{{ __('sales_dist.common.delete') }}</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-4">{{ __('sales_dist.shipments.empty') }}</td></tr>
            @endforelse
            </tbody>
        </table>
        {{ $shipments->links() }}
    </div></div>
</div>
@endsection
