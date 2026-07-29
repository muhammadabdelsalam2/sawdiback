@extends('layouts.customer.dashboard')

@section('title', __('superadmin.farm_assignments.products_title') . ' | EL-Sawady')

@section('content')
    @php($activeLocale = $currentLocale ?? session('locale_full', 'en-SA'))

    <div class="dashboard-body">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <div>
                <h1 class="dashboard-title mb-1">{{ __('superadmin.farm_assignments.products_title') }}</h1>
                <p class="dashboard-desc mb-0">{{ __('superadmin.farm_assignments.products_desc') }}</p>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="table-container">
            <div class="table-responsive">
                <table class="table registry-table mb-0 no-datatable">
                    <thead>
                        <tr>
                            <th>{{ __('warehouse.fields.code') }}</th>
                            <th>{{ __('warehouse.fields.name') }}</th>
                            <th>{{ __('warehouse.fields.category') }}</th>
                            <th>{{ __('warehouse.fields.farm') }}</th>
                            <th class="text-end">{{ __('superadmin.farm_assignments.assign') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($products as $product)
                            <tr>
                                <td>{{ $product->code ?? '-' }}</td>
                                <td>{{ $product->localized_title ?? $product->name }}</td>
                                <td>{{ $product->category ?? '-' }}</td>
                                <td>
                                    <form id="assign-product-{{ $product->id }}" method="POST" action="{{ route('superadmin.farm-assignments.products.assign', ['locale' => $activeLocale, 'product' => $product]) }}">
                                        @csrf
                                        @method('PUT')
                                        <select name="farm_id" class="form-select" required>
                                            <option value="">{{ __('warehouse.fields.select_farm') }}</option>
                                            @foreach(($farmsByTenant[$product->tenant_id] ?? collect()) as $farm)
                                                <option value="{{ $farm->id }}">{{ $farm->name }}</option>
                                            @endforeach
                                        </select>
                                    </form>
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-primary-green" form="assign-product-{{ $product->id }}">
                                        {{ __('superadmin.farm_assignments.assign') }}
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">{{ __('superadmin.farm_assignments.no_unassigned_products') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-3 d-flex justify-content-end">
            {{ $products->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endsection
