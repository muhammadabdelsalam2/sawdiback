@extends('layouts.customer.dashboard')

@section('title', __('livestock.titles.vaccinations_due'))
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/pages/livestock.css') }}">
@endpush

@section('content')
    <div class="container py-4 livestock-page">
        <h2 class="page-title mb-3">{{ __('livestock.titles.vaccinations_due') }} ({{ $days }})</h2>

        <form class="row g-2 mb-3" method="GET"
            action="{{ route('superadmin.livestock.alerts.vaccinations-due', ['locale' => $currentLocale]) }}">
            <div class="col-md-3">
                <input type="number" min="0" max="365" name="days" class="form-control" value="{{ $days }}">
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary-green w-100" type="submit">{{ __('livestock.actions.filter') }}</button>
            </div>
        </form>

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
                            <td colspan="4">{{ __('livestock.empty.no_due_vaccinations') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
