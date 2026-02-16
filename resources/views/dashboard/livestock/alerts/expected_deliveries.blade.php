@extends('layouts.customer.dashboard')

@section('title', __('livestock.titles.expected_deliveries'))
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/pages/livestock.css') }}">
@endpush

@section('content')
    <div class="container py-4 livestock-page">
        <h2 class="page-title mb-3">{{ __('livestock.titles.expected_deliveries') }}</h2>

        <form class="row g-2 mb-3" method="GET"
            action="{{ route('superadmin.livestock.alerts.expected-deliveries', ['locale' => $currentLocale]) }}">
            <div class="col-md-3">
                <input type="month" name="month" class="form-control" value="{{ $month }}">
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary-green w-100" type="submit">{{ __('livestock.actions.filter') }}</button>
            </div>
        </form>

        <div class="table-container">
            <table class="table registry-table mb-0">
                <thead>
                    <tr>
                        <th>{{ __('livestock.fields.cycle_id') }}</th>
                        <th>{{ __('livestock.fields.female_animal') }}</th>
                        <th>{{ __('livestock.fields.male_animal_optional') }}</th>
                        <th>{{ __('livestock.fields.expected_delivery_date') }}</th>
                        <th>{{ __('livestock.fields.status') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rows as $row)
                        <tr>
                            <td>{{ $row->id }}</td>
                            <td>{{ $row->femaleAnimal->tag_number ?? __('livestock.options.no_data') }}</td>
                            <td>{{ $row->maleAnimal->tag_number ?? __('livestock.options.no_data') }}</td>
                            <td>{{ optional($row->expected_delivery_date)->toDateString() ?? __('livestock.options.no_data') }}</td>
                            <td>{{ __('livestock.options.' . $row->status) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">{{ __('livestock.empty.no_expected_deliveries') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
