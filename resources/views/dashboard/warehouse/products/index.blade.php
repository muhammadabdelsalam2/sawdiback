@extends('layouts.customer.dashboard')

@section('title', __('warehouse.titles.products'))
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/pages/livestock.css') }}">
@endpush

@section('content')
    <div class="container py-4 livestock-page">
        <div class="page-head mb-3">
            <h2 class="page-title">{{ __('warehouse.titles.products') }}</h2>
            <a class="btn btn-primary-green"
                href="{{ route('customer.inventory.products.create', ['locale' => $currentLocale]) }}">
                {{ __('warehouse.actions.add_product') }}
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card text-center p-3">
                    <div style="font-size:13px; color:gray;">{{ __('warehouse.stats.total_products') }}</div>
                    <div style="font-size:24px; font-weight:600;">{{ $totalProducts }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center p-3">
                    <div style="font-size:13px; color:gray;">{{ __('warehouse.stats.active') }}</div>
                    <div style="font-size:24px; font-weight:600; color:green;">{{ $activeProducts }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center p-3">
                    <div style="font-size:13px; color:gray;">{{ __('warehouse.stats.low_stock') }}</div>
                    <div style="font-size:24px; font-weight:600; color:red;">{{ $lowStock }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center p-3">
                    <div style="font-size:13px; color:gray;">{{ __('warehouse.stats.best_selling') }}</div>
                    <div style="font-size:24px; font-weight:600; color:orange;">{{ $bestSelling }}</div>
                </div>
            </div>
        </div>
        <div class="table-container">
            <table class="table registry-table mb-0 js-livestock-table">
                <thead>
                    <tr>
                        <th>{{ __('warehouse.fields.id') }}</th>
                        <th>{{ __('warehouse.fields.code') }}</th>
                        <th>{{ __('warehouse.fields.name') }}</th>
                        <th>{{ __('warehouse.fields.category') }}</th>
                        <th>{{ __('warehouse.fields.asset_category') }}</th>
                        <th>{{ __('warehouse.fields.farm_location') }}</th>
                        <th>{{ __('warehouse.fields.image') }}</th>
                        <th>{{ __('warehouse.fields.unit') }}</th>
                        <th>{{ __('warehouse.fields.tax') }}</th>
                        <th>{{ __('warehouse.fields.low_stock_threshold') }}</th>
                        <th class="no-sort">{{ __('warehouse.fields.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $row)
                        <tr>
                            <td>{{ $loop?->iteration }}</td>
                            <td>{{ $row->code ?? '-' }}</td>
                            <td>{{ $row->name }}</td>
                            <td>
                                @php
                                    $legacyCategory = $row->getRawOriginal('category');
                                    $legacyCategoryText = $legacyCategory && \Illuminate\Support\Facades\Lang::has($legacyCategory)
                                        ? __($legacyCategory)
                                        : ($legacyCategory && ! str_contains($legacyCategory, '.') ? $legacyCategory : null);
                                @endphp
                                {{ $row->categoryRelation?->name ?? $legacyCategoryText ?? '-' }}
                            </td>
                            <td>
                                @php
                                    $assetCategoryKey = 'warehouse.asset_categories.' . $row->asset_category;
                                @endphp
                                {{ $row->asset_category ? (\Illuminate\Support\Facades\Lang::has($assetCategoryKey) ? __($assetCategoryKey) : $row->asset_category) : '-' }}
                            </td>
                            <td>{{ $row->farm?->name ?? '-' }}{{ $row->farm_location ? ' - ' . $row->farm_location : '' }}</td>
                            <td>
                                <img src="{{ $row->image_url }}" alt="{{ $row->name }}"
                                    onerror="this.onerror=null;this.src='{{ $row->placeholder_image_url }}';"
                                    style="width:48px; height:48px; border-radius:8px; object-fit:cover;">
                            </td>
                            <td>{{ $row->unit }}</td>
                            <td>{{ number_format($row->tax ?? 0, 2) }}</td>
                            <td>{{ $row->low_stock_threshold }}</td>
                            <td class="d-flex gap-2">
                                <a class="btn btn-sm btn-outline-white"
                                    href="{{ route('customer.inventory.products.edit', ['locale' => $currentLocale, 'product' => $row->id]) }}">{{ __('warehouse.actions.edit') }}</a>
                                <form method="POST"
                                    action="{{ route('customer.inventory.products.destroy', ['locale' => $currentLocale, 'product' => $row->id]) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"
                                        onclick="return confirm('{{ __('warehouse.messages.confirm_delete_product') }}')"
                                        type="submit">{{ __('warehouse.actions.delete') }}</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11">{{ __('warehouse.empty.no_products') }}</td>
                        </tr>
                    @endforelse

                    <!-- Placeholder rows for consistent table height -->

                </tbody>

            </table>
        </div>
        <div class="mt-3">{{ $rows->links('pagination::bootstrap-5') }}</div>
    </div>
@endsection
