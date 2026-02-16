@extends('layouts.customer.dashboard')

@section('title', __('livestock.titles.vaccinations_overdue'))
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/pages/livestock.css') }}">
@endpush

@section('content')
    <div class="container py-4 livestock-page">
        <h2 class="page-title mb-3">{{ __('livestock.titles.vaccinations_overdue') }}</h2>

        <div class="table-container">
            <table class="table registry-table mb-0">
                <thead>
                    <tr>
                        <th>{{ __('livestock.fields.tag') }}</th>
                        <th>{{ __('livestock.fields.vaccine') }}</th>
                        <th>{{ __('livestock.fields.dose_number') }}</th>
                        <th>{{ __('livestock.fields.next_due_date_optional') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rows as $row)
                        <tr>
                            <td>{{ $row->animal->tag_number ?? __('livestock.options.no_data') }}</td>
                            <td>{{ $row->vaccine->name ?? __('livestock.options.no_data') }}</td>
                            <td>{{ $row->dose_number }}</td>
                            <td>{{ optional($row->next_due_date)->toDateString() ?? __('livestock.options.no_data') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">{{ __('livestock.empty.no_overdue_vaccinations') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
