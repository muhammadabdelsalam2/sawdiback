@extends('layouts.customer.dashboard')

@section('title', __('poultry.titles.broiler_cycles'))
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/pages/livestock.css') }}">
@endpush

@section('content')
    <div class="container py-4 livestock-page">
        <div class="page-head">
            <h2 class="page-title">{{ __('poultry.titles.broiler_cycles') }}</h2>
            <div class="quick-actions">
                <a class="btn btn-outline-white" href="{{ route('customer.poultry.alerts.index', ['locale' => $currentLocale]) }}">{{ __('poultry.actions.alerts') }}</a>
                <a class="btn btn-primary-green" href="{{ route('customer.poultry.broiler-cycles.create', ['locale' => $currentLocale]) }}">{{ __('poultry.actions.add_broiler_cycle') }}</a>
            </div>
        </div>
        @include('dashboard.customer.poultry.partials.flash')
        <div class="table-container">
            <table class="table registry-table mb-0">
                <thead><tr><th>{{ __('poultry.fields.cycle_number') }}</th><th>{{ __('poultry.fields.chick_count') }}</th><th>{{ __('poultry.fields.age_days') }}</th><th>{{ __('poultry.fields.mortality_rate') }}</th><th>{{ __('poultry.fields.net_profit') }}</th><th>{{ __('poultry.fields.status') }}</th><th>{{ __('poultry.fields.actions') }}</th></tr></thead>
                <tbody>
                @forelse($cycles as $cycle)
                    <tr>
                        <td>{{ $cycle->cycle_number }}</td>
                        <td>{{ $cycle->chick_count }}</td>
                        <td>{{ $cycle->age_days }}</td>
                        <td>{{ $cycle->mortality_rate }}%</td>
                        <td>{{ $cycle->net_profit }}</td>
                        <td>{{ __('poultry.options.' . $cycle->status) }}</td>
                        <td class="d-flex gap-2">
                            <a class="btn btn-sm btn-outline-primary" href="{{ route('customer.poultry.broiler-cycles.show', ['locale' => $currentLocale, 'broiler_cycle' => $cycle->id]) }}">{{ __('poultry.actions.view') }}</a>
                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('customer.poultry.broiler-cycles.edit', ['locale' => $currentLocale, 'broiler_cycle' => $cycle->id]) }}">{{ __('poultry.actions.edit') }}</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7">{{ __('poultry.empty.no_broiler_cycles') }}</td></tr>
                @endforelse
                </tbody>
            </table>
            {{ $cycles->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endsection
