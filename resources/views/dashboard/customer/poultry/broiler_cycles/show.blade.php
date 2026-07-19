@extends('layouts.customer.dashboard')

@section('title', __('poultry.titles.broiler_cycle_details'))
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/pages/livestock.css') }}">
@endpush

@section('content')
    <div class="container py-4 livestock-page">
        <div class="page-head">
            <h2 class="page-title">{{ __('poultry.titles.broiler_cycle_details') }}: {{ $cycle->cycle_number }}</h2>
            <a class="btn btn-outline-white" href="{{ route('customer.poultry.broiler-cycles.index', ['locale' => $currentLocale]) }}">{{ __('poultry.actions.back') }}</a>
        </div>
        @include('dashboard.customer.poultry.partials.flash')
        <div class="card-block mb-3">
            <div class="row g-3">
                <div class="col-md-2"><strong>{{ __('poultry.fields.age_days') }}:</strong> {{ $cycle->age_days }}</div>
                <div class="col-md-2"><strong>{{ __('poultry.fields.total_mortality') }}:</strong> {{ $cycle->total_mortality }}</div>
                <div class="col-md-2"><strong>{{ __('poultry.fields.mortality_rate') }}:</strong> {{ $cycle->mortality_rate }}%</div>
                <div class="col-md-2"><strong>{{ __('poultry.fields.total_sales') }}:</strong> {{ $cycle->total_sales }}</div>
                <div class="col-md-2"><strong>{{ __('poultry.fields.total_costs') }}:</strong> {{ $cycle->total_costs }}</div>
                <div class="col-md-2"><strong>{{ __('poultry.fields.net_profit') }}:</strong> {{ $cycle->net_profit }}</div>
            </div>
        </div>
        <div class="card-block mb-3">
            <h5 class="section-title">{{ __('poultry.actions.record_mortality') }}</h5>
            <form method="POST" action="{{ route('customer.poultry.broiler-cycles.mortalities.store', ['locale' => $currentLocale, 'broiler_cycle' => $cycle->id]) }}" class="row g-3">
                @csrf
                <div class="col-md-3"><input type="date" name="mortality_date" class="form-control" required></div>
                <div class="col-md-3"><input type="number" min="1" name="quantity" class="form-control" placeholder="{{ __('poultry.fields.quantity') }}" required></div>
                <div class="col-md-4"><input type="text" name="notes" class="form-control" placeholder="{{ __('poultry.fields.notes') }}"></div>
                <div class="col-md-2"><button class="btn btn-primary-green w-100">{{ __('poultry.actions.save') }}</button></div>
            </form>
        </div>
        <div class="card-block mb-3">
            <h5 class="section-title">{{ __('poultry.actions.record_sale') }}</h5>
            <form method="POST" action="{{ route('customer.poultry.broiler-cycles.sales.store', ['locale' => $currentLocale, 'broiler_cycle' => $cycle->id]) }}" class="row g-3">
                @csrf
                <div class="col-md-2"><input type="date" name="sale_date" class="form-control" required></div>
                <div class="col-md-2"><input type="number" step="0.01" min="0.01" name="quantity" class="form-control" placeholder="{{ __('poultry.fields.quantity') }}" required></div>
                <div class="col-md-2"><input type="number" step="0.01" min="0" name="unit_price" class="form-control" placeholder="{{ __('poultry.fields.unit_price') }}" required></div>
                <div class="col-md-3"><input type="text" name="customer_name" class="form-control" placeholder="{{ __('poultry.fields.customer_name') }}"></div>
                <div class="col-md-2"><input type="text" name="notes" class="form-control" placeholder="{{ __('poultry.fields.notes') }}"></div>
                <div class="col-md-1"><button class="btn btn-primary-green w-100">{{ __('poultry.actions.save') }}</button></div>
            </form>
        </div>
        <div class="card-block">
            <h5 class="section-title">{{ __('poultry.actions.record_cost') }}</h5>
            <form method="POST" action="{{ route('customer.poultry.broiler-cycles.costs.store', ['locale' => $currentLocale, 'broiler_cycle' => $cycle->id]) }}" class="row g-3">
                @csrf
                <div class="col-md-3">
                    <select name="cost_type" class="form-select" required>
                        @foreach(['chicks_purchase', 'feed', 'slaughter_packaging'] as $type)
                            <option value="{{ $type }}">{{ __('poultry.options.' . $type) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3"><input type="number" step="0.01" min="0.01" name="amount" class="form-control" placeholder="{{ __('poultry.fields.amount') }}" required></div>
                <div class="col-md-3"><input type="date" name="cost_date" class="form-control" required></div>
                <div class="col-md-2"><input type="text" name="notes" class="form-control" placeholder="{{ __('poultry.fields.notes') }}"></div>
                <div class="col-md-1"><button class="btn btn-primary-green w-100">{{ __('poultry.actions.save') }}</button></div>
            </form>
        </div>
    </div>
@endsection
