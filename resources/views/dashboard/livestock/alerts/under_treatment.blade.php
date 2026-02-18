@extends('layouts.customer.dashboard')

@section('title', __('livestock.titles.under_treatment'))
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/pages/livestock.css') }}">
@endpush

@section('content')
    <div class="container py-4 livestock-page">
        <h2 class="page-title mb-3">{{ __('livestock.titles.under_treatment') }}</h2>

        <div class="table-container mb-3">
            <table class="table registry-table mb-0 js-livestock-table">
                <thead>
                    <tr>
                        <th>{{ __('livestock.fields.tag') }}</th>
                        <th>{{ __('livestock.fields.species') }}</th>
                        <th>{{ __('livestock.fields.breed') }}</th>
                        <th>{{ __('livestock.fields.status') }}</th>
                        <th>{{ __('livestock.fields.health') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rows as $animal)
                        <tr>
                            <td>{{ $animal->tag_number }}</td>
                            <td>{{ $animal->species->name ?? __('livestock.options.no_data') }}</td>
                            <td>{{ $animal->breed->name ?? __('livestock.options.no_data') }}</td>
                            <td>{{ __('livestock.options.' . $animal->status) }}</td>
                            <td>{{ __('livestock.options.' . $animal->health_status) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">{{ __('livestock.empty.no_under_treatment') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <h4 class="mb-2">{{ __('livestock.sections.upcoming_vaccinations') }}</h4>
        <div class="table-container">
            <table class="table registry-table mb-0 js-livestock-table">
                <thead>
                    <tr>
                        <th>{{ __('livestock.fields.tag') }}</th>
                        <th>{{ __('livestock.fields.vaccine') }}</th>
                        <th>{{ __('livestock.fields.next_due_date_optional') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($vaccinationsDue as $row)
                        <tr>
                            <td>{{ $row->animal->tag_number ?? __('livestock.options.no_data') }}</td>
                            <td>{{ $row->vaccine->name ?? __('livestock.options.no_data') }}</td>
                            <td>{{ optional($row->next_due_date)->toDateString() ?? __('livestock.options.no_data') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3">{{ __('livestock.empty.no_upcoming_vaccinations') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
