@extends('layouts.customer.dashboard')

@section('title', __('warehouse.titles.warehouse'))
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/pages/livestock.css') }}">
@endpush

@section('content')
    <div class="container py-4 livestock-page">
        <div class="page-head mb-3">
            <h2 class="page-title">{{ __('warehouse.titles.warehouse') }}</h2>
            <div class="quick-actions">
                <a class="btn btn-outline-white" href="{{ route('customer.inventory.products.index', ['locale' => $currentLocale]) }}">{{ __('warehouse.titles.products') }}</a>
                <a class="btn btn-outline-white" href="{{ route('customer.inventory.alerts.index', ['locale' => $currentLocale]) }}">{{ __('warehouse.titles.alerts') }}</a>
                <a class="btn btn-primary-green" href="{{ route('customer.inventory.traceability.index', ['locale' => $currentLocale]) }}">{{ __('warehouse.titles.traceability') }}</a>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        <div class="row g-3 mb-3">
            <div class="col-lg-6">
                <div class="card-block h-100">
                    <h5>{{ __('warehouse.sections.receive_batch') }}</h5>
                    <form method="POST" action="{{ route('customer.inventory.batches.store', ['locale' => $currentLocale]) }}" class="row g-2">
                        @csrf
                        <div class="col-md-6">
                            <label class="form-label">{{ __('warehouse.fields.product') }}</label>
                            <select name="inventory_product_id" class="form-select" required>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('warehouse.fields.batch_number') }}</label>
                            <input type="text" name="batch_number" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('warehouse.fields.received_at') }}</label>
                            <input type="date" name="received_at" class="form-control" value="{{ now()->toDateString() }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('warehouse.fields.expiry_date') }}</label>
                            <input type="date" name="expiry_date" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('warehouse.fields.quantity') }}</label>
                            <input type="number" step="0.01" min="0.01" name="quantity" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('warehouse.fields.unit_cost') }}</label>
                            <input type="number" step="0.01" min="0" name="unit_cost" class="form-control">
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <button class="btn btn-primary-green w-100" type="submit">{{ __('warehouse.actions.save') }}</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card-block h-100">
                    <h5>{{ __('warehouse.sections.manual_movement') }}</h5>
                    <form method="POST" action="{{ route('customer.inventory.movements.store', ['locale' => $currentLocale]) }}" class="row g-2">
                        @csrf
                        <div class="col-md-6">
                            <label class="form-label">{{ __('warehouse.fields.product') }}</label>
                            <select name="inventory_product_id" class="form-select" required>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('warehouse.fields.batch') }}</label>
                            <select name="inventory_batch_id" class="form-select">
                                <option value="">-</option>
                                @foreach($batches as $batch)
                                    <option value="{{ $batch->id }}">{{ $batch->product?->name }} - {{ $batch->batch_number }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('warehouse.fields.movement_type') }}</label>
                            <select name="movement_type" class="form-select" required>
                                <option value="in">{{ __('warehouse.options.in') }}</option>
                                <option value="out">{{ __('warehouse.options.out') }}</option>
                                <option value="adjustment">{{ __('warehouse.options.adjustment') }}</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('warehouse.fields.quantity') }}</label>
                            <input type="number" step="0.01" min="0.01" name="quantity" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('warehouse.fields.movement_date') }}</label>
                            <input type="date" name="movement_date" class="form-control" value="{{ now()->toDateString() }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('warehouse.fields.unit_cost') }}</label>
                            <input type="number" step="0.01" min="0" name="unit_cost" class="form-control">
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <button class="btn btn-primary-green w-100" type="submit">{{ __('warehouse.actions.save') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-lg-6">
                <div class="card-block h-100">
                    <h5>{{ __('warehouse.sections.record_production') }}</h5>
                    <form method="POST" action="{{ route('customer.inventory.production.store', ['locale' => $currentLocale]) }}" class="row g-2">
                        @csrf
                        <div class="col-md-6">
                            <label class="form-label">{{ __('warehouse.fields.product') }}</label>
                            <select name="inventory_product_id" class="form-select" required>
                                @foreach($products->where('category', 'animal_product') as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('warehouse.fields.animal') }}</label>
                            <select name="livestock_animal_id" class="form-select">
                                <option value="">-</option>
                                @foreach($animals as $animal)
                                    <option value="{{ $animal->id }}">{{ $animal->tag_number }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('warehouse.fields.batch_number') }}</label>
                            <input type="text" name="batch_number" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('warehouse.fields.production_date') }}</label>
                            <input type="date" name="production_date" class="form-control" value="{{ now()->toDateString() }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('warehouse.fields.expiry_date') }}</label>
                            <input type="date" name="expiry_date" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('warehouse.fields.quantity') }}</label>
                            <input type="number" step="0.01" min="0.01" name="quantity" class="form-control" required>
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <button class="btn btn-primary-green w-100" type="submit">{{ __('warehouse.actions.save') }}</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card-block h-100">
                    <h5>{{ __('warehouse.sections.record_delivery') }}</h5>
                    <form method="POST" action="{{ route('customer.inventory.deliveries.store', ['locale' => $currentLocale]) }}" class="row g-2">
                        @csrf
                        <div class="col-md-4">
                            <label class="form-label">{{ __('warehouse.fields.delivery_number') }}</label>
                            <input type="text" name="delivery_number" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('warehouse.fields.customer_name') }}</label>
                            <input type="text" name="customer_name" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('warehouse.fields.delivered_at') }}</label>
                            <input type="datetime-local" name="delivered_at" class="form-control" value="{{ now()->format('Y-m-d\TH:i') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('warehouse.fields.product') }}</label>
                            <select name="items[0][inventory_product_id]" class="form-select" required>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('warehouse.fields.batch') }}</label>
                            <select name="items[0][inventory_batch_id]" class="form-select" required>
                                @foreach($batches as $batch)
                                    <option value="{{ $batch->id }}">{{ $batch->product?->name }} - {{ $batch->batch_number }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">{{ __('warehouse.fields.quantity') }}</label>
                            <input type="number" step="0.01" min="0.01" name="items[0][quantity]" class="form-control" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">{{ __('warehouse.fields.unit_price') }}</label>
                            <input type="number" step="0.01" min="0" name="items[0][unit_price]" class="form-control">
                        </div>
                        <input type="hidden" name="status" value="delivered">
                        <div class="col-12">
                            <button class="btn btn-primary-green" type="submit">{{ __('warehouse.actions.save') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="card-block mb-3">
            <h5>{{ __('warehouse.sections.stock_overview') }}</h5>
            <div class="table-container">
                <table class="table registry-table mb-0 js-livestock-table">
                    <thead>
                        <tr>
                            <th>{{ __('warehouse.fields.product') }}</th>
                            <th>{{ __('warehouse.fields.category') }}</th>
                            <th>{{ __('warehouse.fields.stock_on_hand') }}</th>
                            <th>{{ __('warehouse.fields.low_stock_threshold') }}</th>
                            <th>{{ __('warehouse.fields.status') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stockRows as $row)
                            <tr>
                                <td>{{ $row['product']->name }}</td>
                                <td>{{ __('warehouse.options.' . $row['product']->category) }}</td>
                                <td>{{ number_format((float)$row['stock_on_hand'], 2) }}</td>
                                <td>{{ number_format((float)$row['product']->low_stock_threshold, 2) }}</td>
                                <td>{{ $row['is_low_stock'] ? __('dashboard.alerts.warning') : 'OK' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5">{{ __('warehouse.empty.no_stock_rows') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-block">
            <h5>{{ __('warehouse.sections.recent_batches') }}</h5>
            <div class="table-container">
                <table class="table registry-table mb-0 js-livestock-table">
                    <thead>
                        <tr>
                            <th>{{ __('warehouse.fields.product') }}</th>
                            <th>{{ __('warehouse.fields.batch_number') }}</th>
                            <th>{{ __('warehouse.fields.quantity') }}</th>
                            <th>{{ __('warehouse.fields.quantity_available') }}</th>
                            <th>{{ __('warehouse.fields.expiry_date') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($batches as $batch)
                            <tr>
                                <td>{{ $batch->product?->name }}</td>
                                <td>{{ $batch->batch_number }}</td>
                                <td>{{ $batch->quantity_initial }}</td>
                                <td>{{ $batch->quantity_available }}</td>
                                <td>{{ optional($batch->expiry_date)->toDateString() ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5">{{ __('warehouse.empty.no_batches') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

