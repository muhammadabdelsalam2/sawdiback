@extends('layouts.customer.dashboard')

@section('title', __('livestock.titles.edit_vaccine'))
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/pages/livestock.css') }}">
@endpush

@section('content')
    <div class="container py-4 livestock-page">
        <h2 class="page-title mb-3">{{ __('livestock.titles.edit_vaccine') }}</h2>
        @if ($errors->any())
            <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
        @endif
        <div class="card-block">
            <form method="POST" action="{{ route('customer.livestock.vaccines.update', ['locale' => $currentLocale, 'vaccine' => $vaccine->id]) }}">
                @method('PUT')
                @include('dashboard.livestock.master.vaccines._form', ['vaccine' => $vaccine])
                <div class="mt-3 d-flex gap-2">
                    <button class="btn btn-primary-green" type="submit">{{ __('livestock.actions.update') }}</button>
                    <a class="btn btn-outline-white" href="{{ route('customer.livestock.vaccines.index', ['locale' => $currentLocale]) }}">{{ __('livestock.actions.back') }}</a>
                </div>
            </form>
        </div>

        <div class="card-block mt-3">
            <h5>{{ __('livestock.sections.vaccine_batches') }}</h5>
            <form method="POST" action="{{ route('customer.livestock.vaccines.batches.store', ['locale' => $currentLocale, 'vaccine' => $vaccine->id]) }}" class="row g-3">
                @csrf
                <input type="hidden" name="vaccine_id" value="{{ $vaccine->id }}">
                <div class="col-md-3">
                    <label class="form-label">{{ __('livestock.fields.batch_number') }}</label>
                    <input type="text" name="batch_number" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('livestock.fields.farm') }}</label>
                    <select name="farm_id" class="form-select">
                        <option value="">{{ __('livestock.options.central_stock') }}</option>
                        @foreach($farms as $farm)
                            <option value="{{ $farm->id }}">{{ $farm->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('livestock.fields.quantity') }}</label>
                    <input type="number" step="0.01" min="0" name="quantity" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('livestock.fields.expiry_date') }}</label>
                    <input type="date" name="expiry_date" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('livestock.fields.notes') }}</label>
                    <input type="text" name="notes" class="form-control">
                </div>
                <div class="col-12">
                    <button class="btn btn-primary-green" type="submit">{{ __('livestock.actions.save') }}</button>
                </div>
            </form>

            <div class="table-container mt-3">
                <table class="table registry-table mb-0">
                    <thead>
                        <tr>
                            <th>{{ __('livestock.fields.batch_number') }}</th>
                            <th>{{ __('livestock.fields.farm') }}</th>
                            <th>{{ __('livestock.fields.quantity') }}</th>
                            <th>{{ __('livestock.fields.expiry_date') }}</th>
                            <th>{{ __('livestock.fields.notes') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vaccine->batches as $batch)
                            <tr>
                                <td>{{ $batch->batch_number ?? '-' }}</td>
                                <td>{{ $batch->farm?->name ?? __('livestock.options.central_stock') }}</td>
                                <td>{{ $batch->quantity }}</td>
                                <td>{{ $batch->expiry_date?->format('Y-m-d') }}</td>
                                <td>{{ $batch->notes ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5">{{ __('livestock.empty.no_vaccine_batches') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
