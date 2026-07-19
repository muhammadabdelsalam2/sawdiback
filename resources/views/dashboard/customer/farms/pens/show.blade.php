@extends('layouts.customer.dashboard')

@section('title', __('farms.titles.pen_details'))
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/pages/livestock.css') }}">
@endpush

@section('content')
<div class="container py-4 livestock-page">
    <div class="page-head">
        <h2 class="page-title">{{ __('farms.titles.pen_details') }}: {{ $pen->pen_number }}</h2>
        <a class="btn btn-outline-white" href="{{ route('customer.farm-pens.index', ['locale' => $currentLocale]) }}">
            {{ __('farms.actions.back') }}
        </a>
    </div>

    @include('dashboard.customer.farms.partials.flash')

    <div class="card-block mb-3">
        <div class="row g-3">
            <div class="col-md-2"><strong>{{ __('farms.fields.animal_count') }}:</strong> {{ $pen->animal_count }}</div>
            <div class="col-md-2"><strong>{{ __('farms.fields.male_count') }}:</strong> {{ $pen->male_count }}</div>
            <div class="col-md-2"><strong>{{ __('farms.fields.female_count') }}:</strong> {{ $pen->female_count }}</div>
            <div class="col-md-3"><strong>{{ __('farms.fields.mortality_rate') }}:</strong> {{ $profitSummary['mortality_rate'] }}%</div>
            <div class="col-md-3"><strong>{{ __('farms.fields.net_profit') }}:</strong> {{ $profitSummary['net_profit'] }}</div>
            <div class="col-md-3"><strong>{{ __('farms.fields.total_sales') }}:</strong> {{ $profitSummary['total_sales'] }}</div>
            <div class="col-md-3"><strong>{{ __('farms.fields.feed_costs') }}:</strong> {{ $profitSummary['feed_costs'] }}</div>
            <div class="col-md-3"><strong>{{ __('farms.fields.slaughter_packaging_costs') }}:</strong> {{ $profitSummary['slaughter_packaging_costs'] }}</div>
        </div>
    </div>

    <div class="card-block mb-3">
        <h5>{{ __('farms.actions.record_financial_entry') }}</h5>
        <form method="POST"
              action="{{ route('customer.farm-pens.financial-entries.store', ['locale' => $currentLocale, 'farm_pen' => $pen->id]) }}"
              class="row g-3">
            @csrf
            <div class="col-md-3">
                <label class="form-label">{{ __('farms.fields.entry_type') }}</label>
                <select name="type" class="form-select" required>
                    @foreach(['sale', 'slaughter_packaging'] as $type)
                        <option value="{{ $type }}" @selected(old('type') === $type)>{{ __('farms.options.' . $type) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">{{ __('farms.fields.amount') }}</label>
                <input type="number" step="0.01" min="0.01" name="amount" class="form-control" value="{{ old('amount') }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">{{ __('farms.fields.entry_date') }}</label>
                <input type="date" name="entry_date" class="form-control" value="{{ old('entry_date', now()->toDateString()) }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">{{ __('farms.fields.notes') }}</label>
                <input type="text" name="notes" class="form-control" value="{{ old('notes') }}">
            </div>
            <div class="col-12">
                <button class="btn btn-primary-green" type="submit">{{ __('farms.actions.save') }}</button>
            </div>
        </form>
    </div>

    <div class="card-block mb-3">
        <h5>{{ __('farms.titles.financial_entries') }}</h5>
        <div class="table-container">
            <table class="table registry-table mb-0">
                <thead>
                    <tr>
                        <th>{{ __('farms.fields.entry_type') }}</th>
                        <th>{{ __('farms.fields.amount') }}</th>
                        <th>{{ __('farms.fields.entry_date') }}</th>
                        <th>{{ __('farms.fields.notes') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pen->financialEntries as $entry)
                        <tr>
                            <td>{{ __('farms.options.' . $entry->type) }}</td>
                            <td>{{ $entry->amount }}</td>
                            <td>{{ $entry->entry_date?->format('Y-m-d') }}</td>
                            <td>{{ $entry->notes ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4">{{ __('farms.empty.no_financial_entries') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="table-container">
        <table class="table registry-table mb-0">
            <thead>
                <tr>
                    <th>{{ __('livestock.fields.tag') }}</th>
                    <th>{{ __('livestock.fields.species') }}</th>
                    <th>{{ __('livestock.fields.gender') }}</th>
                    <th>{{ __('livestock.fields.status') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pen->animals as $animal)
                    <tr>
                        <td>{{ $animal->tag_number }}</td>
                        <td>{{ $animal->species?->name ?? '-' }}</td>
                        <td>{{ __('livestock.options.' . $animal->gender) }}</td>
                        <td>{{ __('livestock.options.' . $animal->status) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4">{{ __('farms.empty.no_animals') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
