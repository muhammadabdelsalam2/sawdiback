@extends('layouts.customer.dashboard')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">{{ __('sales_dist.customers.title') }}</h3>
        <a class="btn btn-primary" href="{{ route('customer.sales-distribution.customers.create', ['locale' => request()->route('locale')]) }}">{{ __('sales_dist.customers.add') }}</a>
    </div>

    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif

    <form class="card card-body mb-3" method="GET">
        <div class="row g-2">
            <div class="col-md-3"><input type="text" name="q" class="form-control" placeholder="{{ __('sales_dist.customers.search') }}" value="{{ request('q') }}"></div>
            <div class="col-md-3">
                <select name="type" class="form-select">
                    <option value="">{{ __('sales_dist.customers.all_types') }}</option>
                    @foreach (['trader', 'factory', 'shop'] as $value)
                        <option value="{{ $value }}" @selected(request('type') === $value)>{{ __("sales_dist.status.types.$value") }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">{{ __('sales_dist.common.all_status') }}</option>
                    @foreach (['active', 'inactive'] as $value)
                        <option value="{{ $value }}" @selected(request('status') === $value)>{{ __("sales_dist.status.customer.$value") }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button class="btn btn-outline-primary w-100">{{ __('sales_dist.common.filter') }}</button>
                <a class="btn btn-light w-100" href="{{ route('customer.sales-distribution.customers.index', ['locale' => request()->route('locale')]) }}">{{ __('sales_dist.common.reset') }}</a>
            </div>
        </div>
    </form>

    <div class="card">
        <div class="card-body">
            <table class="table align-middle no-datatable sd-export-table"
                data-export-title="{{ __('sales_dist.export.titles.customers_index') }}"
                data-pdf-orientation="portrait" data-pdf-page-size="A4">
                <thead><tr><th>#</th><th>{{ __('sales_dist.customers.fields.name') }}</th><th>{{ __('sales_dist.customers.fields.type') }}</th><th>{{ __('sales_dist.customers.fields.status') }}</th><th class="text-end no-sort no-export">{{ __('sales_dist.common.actions') }}</th></tr></thead>
                <tbody>
                @forelse($customers as $customer)
                    <tr>
                        <td>{{ $customer->id }}</td>
                        <td>{{ $customer->name }}</td>
                        <td>{{ __("sales_dist.status.types.$customer->type") }}</td>
                        <td>{{ __("sales_dist.status.customer.$customer->status") }}</td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-outline-info" href="{{ route('customer.sales-distribution.customers.show', ['locale' => request()->route('locale'), 'customer' => $customer->id]) }}">{{ __('sales_dist.common.view') }}</a>
                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('customer.sales-distribution.customers.edit', ['locale' => request()->route('locale'), 'customer' => $customer->id]) }}">{{ __('sales_dist.common.edit') }}</a>
                            <form class="d-inline" method="POST" action="{{ route('customer.sales-distribution.customers.destroy', ['locale' => request()->route('locale'), 'customer' => $customer->id]) }}">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" onclick="return confirm('{{ __('sales_dist.customers.confirm_delete') }}')">{{ __('sales_dist.common.delete') }}</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">{{ __('sales_dist.customers.empty') }}</td></tr>
                @endforelse
                </tbody>
            </table>
            {{ $customers->links() }}
        </div>
    </div>
</div>
@endsection
