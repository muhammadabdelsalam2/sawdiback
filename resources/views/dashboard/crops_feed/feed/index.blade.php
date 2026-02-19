@extends('layouts.customer.dashboard')

@section('title', __('crops_feed.titles.feed_management'))
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/pages/livestock.css') }}">
@endpush

@section('content')
    <div class="container py-4 livestock-page">
        <h2 class="page-title mb-3">{{ __('crops_feed.titles.feed_management') }}</h2>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
        @endif

        <div class="card-block mb-3">
            <h5 class="section-title">{{ __('crops_feed.actions.record_stock_movement') }}</h5>
            <form method="POST" action="{{ route('customer.crops-feed.feed.stock-movements.store', ['locale' => $currentLocale]) }}" class="row g-3">
                @csrf
                <div class="col-md-3">
                    <label class="form-label">{{ __('crops_feed.fields.feed_type') }}</label>
                    <select name="feed_type_id" class="form-select" required>
                        @foreach($feedTypes as $feedType)
                            <option value="{{ $feedType->id }}">{{ $feedType->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">{{ __('crops_feed.fields.movement_type') }}</label>
                    <select name="movement_type" class="form-select" required>
                        <option value="in">{{ __('crops_feed.options.in') }}</option>
                        <option value="out">{{ __('crops_feed.options.out') }}</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">{{ __('crops_feed.fields.quantity') }}</label>
                    <input type="number" step="0.01" min="0.01" name="quantity" class="form-control" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">{{ __('crops_feed.fields.unit_cost') }}</label>
                    <input type="number" step="0.01" min="0" name="unit_cost" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('crops_feed.fields.movement_date') }}</label>
                    <input type="date" name="movement_date" class="form-control" required>
                </div>
                <div class="col-12">
                    <label class="form-label">{{ __('crops_feed.fields.notes') }}</label>
                    <input type="text" name="notes" class="form-control">
                </div>
                <div class="col-12">
                    <button class="btn btn-primary-green" type="submit">{{ __('crops_feed.actions.save') }}</button>
                </div>
            </form>
        </div>

        <div class="card-block mb-3">
            <h5 class="section-title">{{ __('crops_feed.actions.record_consumption') }}</h5>
            <form method="POST" action="{{ route('customer.crops-feed.feed.consumptions.store', ['locale' => $currentLocale]) }}" class="row g-3">
                @csrf
                <div class="col-md-3">
                    <label class="form-label">{{ __('crops_feed.fields.feed_type') }}</label>
                    <select name="feed_type_id" class="form-select" required>
                        @foreach($feedTypes as $feedType)
                            <option value="{{ $feedType->id }}">{{ $feedType->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('crops_feed.fields.animal') }}</label>
                    <select name="animal_id" class="form-select">
                        <option value="">{{ __('crops_feed.options.select_animal') }}</option>
                        @foreach($animals as $animal)
                            <option value="{{ $animal->id }}">{{ $animal->tag_number }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">{{ __('crops_feed.fields.group_name') }}</label>
                    <input type="text" name="group_name" class="form-control">
                </div>
                <div class="col-md-2">
                    <label class="form-label">{{ __('crops_feed.fields.quantity') }}</label>
                    <input type="number" step="0.01" min="0.01" name="quantity" class="form-control" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">{{ __('crops_feed.fields.consumption_date') }}</label>
                    <input type="date" name="consumption_date" class="form-control" required>
                </div>
                <div class="col-12">
                    <label class="form-label">{{ __('crops_feed.fields.notes') }}</label>
                    <input type="text" name="notes" class="form-control">
                </div>
                <div class="col-12">
                    <button class="btn btn-primary-green" type="submit">{{ __('crops_feed.actions.save') }}</button>
                </div>
            </form>
        </div>

        <div class="card-block mb-3">
            <h5 class="section-title">{{ __('crops_feed.actions.allocate_crop_to_feed') }}</h5>
            <form method="POST" action="{{ route('customer.crops-feed.feed.crop-allocations.store', ['locale' => $currentLocale]) }}" class="row g-3">
                @csrf
                <div class="col-md-3">
                    <label class="form-label">{{ __('crops_feed.titles.crops') }}</label>
                    <select name="crop_id" class="form-select" required>
                        @foreach($crops as $crop)
                            <option value="{{ $crop->id }}">{{ $crop->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('crops_feed.fields.feed_type') }}</label>
                    <select name="feed_type_id" class="form-select" required>
                        @foreach($feedTypes as $feedType)
                            <option value="{{ $feedType->id }}">{{ $feedType->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">{{ __('crops_feed.fields.quantity_tons') }}</label>
                    <input type="number" step="0.01" min="0.01" name="quantity_tons" class="form-control" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">{{ __('crops_feed.fields.allocation_date') }}</label>
                    <input type="date" name="allocation_date" class="form-control" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">{{ __('crops_feed.fields.notes') }}</label>
                    <input type="text" name="notes" class="form-control">
                </div>
                <div class="col-12">
                    <button class="btn btn-primary-green" type="submit">{{ __('crops_feed.actions.save') }}</button>
                </div>
            </form>
        </div>

        <div class="table-container mb-3">
            <table class="table registry-table mb-0 js-livestock-table">
                <thead>
                    <tr>
                        <th>{{ __('crops_feed.fields.feed_type') }}</th>
                        <th>{{ __('crops_feed.fields.stock_on_hand') }}</th>
                        <th>{{ __('crops_feed.fields.low_stock_threshold') }}</th>
                        <th>{{ __('crops_feed.fields.status') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($stocks as $stock)
                        <tr>
                            <td>{{ $stock['feedType']->name }}</td>
                            <td>{{ $stock['stock_on_hand'] }}</td>
                            <td>{{ $stock['feedType']->low_stock_threshold }}</td>
                            <td>{{ $stock['is_low_stock'] ? __('dashboard.alerts.warning') : __('crops_feed.options.covered') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="table-container">
            <table class="table registry-table mb-0 js-livestock-table">
                <thead>
                    <tr>
                        <th>{{ __('crops_feed.fields.feed_type') }}</th>
                        <th>{{ __('crops_feed.fields.animal') }}</th>
                        <th>{{ __('crops_feed.fields.group_name') }}</th>
                        <th>{{ __('crops_feed.fields.quantity') }}</th>
                        <th>{{ __('crops_feed.fields.unit_cost') }}</th>
                        <th>{{ __('crops_feed.fields.consumption_date') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentConsumptions as $row)
                        <tr>
                            <td>{{ $row->feedType?->name }}</td>
                            <td>{{ $row->animal?->tag_number ?? '-' }}</td>
                            <td>{{ $row->group_name ?? '-' }}</td>
                            <td>{{ $row->quantity }}</td>
                            <td>{{ $row->unit_cost }}</td>
                            <td>{{ $row->consumption_date?->format('Y-m-d') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6">{{ __('crops_feed.empty.no_consumptions') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
