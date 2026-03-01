@extends('layouts.customer.dashboard')

@section('title', __('warehouse.titles.products'))
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/pages/livestock.css') }}">
@endpush

@section('content')
    <div class="container py-4 livestock-page">
        <div class="page-head mb-3">
            <h2 class="page-title">{{ __('warehouse.titles.products') }}</h2>
            <a class="btn btn-primary-green" href="{{ route('customer.inventory.products.create', ['locale' => $currentLocale]) }}">
                {{ __('warehouse.actions.add_product') }}
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="table-container">
            <table class="table registry-table mb-0 js-livestock-table">
                <thead>
                    <tr>
                        <th>{{ __('warehouse.fields.id') }}</th>
                        <th>{{ __('warehouse.fields.code') }}</th>
                        <th>{{ __('warehouse.fields.name') }}</th>
                        <th>{{ __('warehouse.fields.category') }}</th>
                        <th>{{ __('warehouse.fields.unit') }}</th>
                        <th>{{ __('warehouse.fields.low_stock_threshold') }}</th>
                        <th class="no-sort">{{ __('warehouse.fields.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $row)
                        <tr>
                            <td>{{ $row->id }}</td>
                            <td>{{ $row->code ?? '-' }}</td>
                            <td>{{ $row->name }}</td>
                            <td>{{ __('warehouse.options.' . $row->category) }}</td>
                            <td>{{ $row->unit }}</td>
                            <td>{{ $row->low_stock_threshold }}</td>
                            <td class="d-flex gap-2">
                                <a class="btn btn-sm btn-outline-white" href="{{ route('customer.inventory.products.edit', ['locale' => $currentLocale, 'product' => $row->id]) }}">{{ __('warehouse.actions.edit') }}</a>
                                <form method="POST" action="{{ route('customer.inventory.products.destroy', ['locale' => $currentLocale, 'product' => $row->id]) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" type="submit">{{ __('warehouse.actions.delete') }}</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7">{{ __('warehouse.empty.no_products') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

