@extends('layouts.customer.dashboard')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">{{ __('sales_dist.contracts.title') }}</h3>
        <a class="btn btn-primary" href="{{ route('customer.sales-distribution.contracts.create', ['locale' => request()->route('locale')]) }}">{{ __('sales_dist.contracts.add') }}</a>
    </div>

    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif

    <form class="card card-body mb-3" method="GET">
        <div class="row g-2">
            <div class="col-md-3">
                <select name="customer_id" class="form-select">
                    <option value="">{{ __('sales_dist.contracts.all_customers') }}</option>
                    @foreach($customerOptions as $option)
                        <option value="{{ $option->id }}" @selected((int) request('customer_id') === $option->id)>{{ $option->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">{{ __('sales_dist.common.all_status') }}</option>
                    @foreach(['active', 'inactive', 'expired'] as $value)
                        <option value="{{ $value }}" @selected(request('status') === $value)>{{ __("sales_dist.status.contract.$value") }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2"><input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}" title="{{ __('sales_dist.common.date_from') }}"></div>
            <div class="col-md-2"><input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}" title="{{ __('sales_dist.common.date_to') }}"></div>
            <div class="col-md-3 d-flex gap-2">
                <button class="btn btn-outline-primary w-100">{{ __('sales_dist.common.filter') }}</button>
                <a class="btn btn-light w-100" href="{{ route('customer.sales-distribution.contracts.index', ['locale' => request()->route('locale')]) }}">{{ __('sales_dist.common.reset') }}</a>
            </div>
        </div>
    </form>

    <div class="card"><div class="card-body">
        <table class="table align-middle no-datatable sd-export-table"
            data-export-title="{{ __('sales_dist.export.titles.contracts_index') }}"
            data-pdf-orientation="landscape" data-pdf-page-size="A4">
            <thead><tr><th>#</th><th>{{ __('sales_dist.contracts.fields.code') }}</th><th>{{ __('sales_dist.contracts.fields.customer') }}</th><th>{{ __('sales_dist.contracts.fields.start_date') }}</th><th>{{ __('sales_dist.contracts.fields.status') }}</th><th class="text-end no-sort no-export">{{ __('sales_dist.common.actions') }}</th></tr></thead>
            <tbody>
            @forelse($contracts as $contract)
                <tr>
                    <td>{{ $contract->id }}</td>
                    <td>{{ $contract->contract_code }}</td>
                    <td>{{ $contract->customer->name }}</td>
                    <td>{{ $contract->start_date?->format('Y-m-d') }}</td>
                    <td>{{ __("sales_dist.status.contract.$contract->status") }}</td>
                    <td class="text-end">
                        <a class="btn btn-sm btn-outline-info" href="{{ route('customer.sales-distribution.contracts.show', ['locale' => request()->route('locale'), 'contract' => $contract->id]) }}">{{ __('sales_dist.common.view') }}</a>
                        <a class="btn btn-sm btn-outline-secondary" href="{{ route('customer.sales-distribution.contracts.edit', ['locale' => request()->route('locale'), 'contract' => $contract->id]) }}">{{ __('sales_dist.common.edit') }}</a>
                        <form class="d-inline" method="POST" action="{{ route('customer.sales-distribution.contracts.destroy', ['locale' => request()->route('locale'), 'contract' => $contract->id]) }}">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" onclick="return confirm('{{ __('sales_dist.contracts.confirm_delete') }}')">{{ __('sales_dist.common.delete') }}</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-4">{{ __('sales_dist.contracts.empty') }}</td></tr>
            @endforelse
            </tbody>
        </table>
        {{ $contracts->links() }}
    </div></div>
</div>
@endsection
