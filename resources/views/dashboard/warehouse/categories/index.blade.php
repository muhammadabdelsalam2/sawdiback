@extends('layouts.customer.dashboard')

@section('title', __('warehouse.titles.categories'))
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/pages/livestock.css') }}">
@endpush

@section('content')
    <div class="container py-4 livestock-page">
        <div class="page-head mb-3">
            <h2 class="page-title">{{ __('warehouse.titles.categories') }}</h2>
            <a class="btn btn-primary-green" href="{{ route('customer.inventory.categories.create', ['locale' => $currentLocale]) }}">
                {{ __('warehouse.actions.add_category') }}
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
                        <th>{{ __('warehouse.fields.image') }}</th>
                        <th>{{ __('warehouse.fields.name') }}</th>
                        <th>{{ __('warehouse.fields.code') }}</th>
                        <th>{{ __('warehouse.fields.sort_order') }}</th>
                        <th>{{ __('warehouse.fields.active') }}</th>
                        <th class="no-sort">{{ __('warehouse.fields.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rows as $row)
                        <tr>
                            <td>{{ $row->id }}</td>
                            <td>
                                <img src="{{ $row->image_url }}" alt="{{ $row->name ?? __('warehouse.titles.categories') }}"
                                    onerror="this.onerror=null;this.src='{{ $row->placeholder_image_url }}';"
                                    class="img-thumbnail rounded shadow-sm"
                                    style="width:48px; height:48px; object-fit:cover;">
                            </td>
                            <td>{{ $row->name ?? '-' }}</td>
                            <td>{{ $row->code ?? '-' }}</td>
                            <td>{{ $row->sort_order }}</td>
                            <td>{{ $row->is_active ? __('warehouse.options.yes') : __('warehouse.options.no') }}</td>
                            <td class="d-flex gap-2">
                                <a class="btn btn-sm btn-outline-white" href="{{ route('customer.inventory.categories.edit', ['locale' => $currentLocale, 'category' => $row->id]) }}">{{ __('warehouse.actions.edit') }}</a>
                                <form method="POST" action="{{ route('customer.inventory.categories.destroy', ['locale' => $currentLocale, 'category' => $row->id]) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" type="submit">{{ __('warehouse.actions.delete') }}</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7">{{ __('warehouse.empty.no_categories') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $rows->links('pagination::bootstrap-5') }}</div>
    </div>
@endsection
