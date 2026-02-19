@extends('layouts.customer.dashboard')

@section('title', __('crops_feed.titles.crop_profile'))
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/pages/livestock.css') }}">
@endpush

@section('content')
    <div class="container py-4 livestock-page">
        <div class="page-head">
            <h2 class="page-title">{{ __('crops_feed.titles.crop_profile') }}: {{ $crop->name }}</h2>
            <a class="btn btn-outline-white" href="{{ route('customer.crops-feed.crops.index', ['locale' => $currentLocale]) }}">{{ __('crops_feed.actions.back') }}</a>
        </div>

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
            <div class="row g-3">
                <div class="col-md-3"><strong>{{ __('crops_feed.fields.land_area') }}:</strong> {{ $crop->land_area }}</div>
                <div class="col-md-3"><strong>{{ __('crops_feed.fields.planting_date') }}:</strong> {{ $crop->planting_date?->format('Y-m-d') }}</div>
                <div class="col-md-3"><strong>{{ __('crops_feed.fields.yield_tons') }}:</strong> {{ $crop->yield_tons ?? '-' }}</div>
                <div class="col-md-3"><strong>{{ __('crops_feed.fields.available_for_feed_tons') }}:</strong> {{ $crop->available_for_feed_tons }}</div>
                <div class="col-md-3"><strong>{{ __('crops_feed.fields.total_cost') }}:</strong> {{ $crop->total_cost }}</div>
                <div class="col-md-3"><strong>{{ __('crops_feed.fields.cost_per_ton') }}:</strong> {{ $crop->cost_per_ton ?? '-' }}</div>
                <div class="col-md-3"><strong>{{ __('crops_feed.fields.profit_or_loss') }}:</strong> {{ $crop->profit_or_loss ?? '-' }}</div>
            </div>
        </div>

        <div class="card-block mb-3">
            <h5 class="section-title">{{ __('crops_feed.actions.add_growth_stage') }}</h5>
            <form method="POST" action="{{ route('customer.crops-feed.crops.growth-stages.store', ['locale' => $currentLocale]) }}" class="row g-3">
                @csrf
                <input type="hidden" name="crop_id" value="{{ $crop->id }}">
                <div class="col-md-4">
                    <label class="form-label">{{ __('crops_feed.fields.stage_name') }}</label>
                    <input type="text" name="stage_name" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">{{ __('crops_feed.fields.recorded_on') }}</label>
                    <input type="date" name="recorded_on" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">{{ __('crops_feed.fields.notes') }}</label>
                    <input type="text" name="notes" class="form-control">
                </div>
                <div class="col-12">
                    <button class="btn btn-primary-green" type="submit">{{ __('crops_feed.actions.save') }}</button>
                </div>
            </form>

            <div class="table-container mt-3">
                <table class="table registry-table mb-0">
                    <thead>
                        <tr>
                            <th>{{ __('crops_feed.fields.stage_name') }}</th>
                            <th>{{ __('crops_feed.fields.recorded_on') }}</th>
                            <th>{{ __('crops_feed.fields.notes') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($crop->growthStages as $row)
                            <tr>
                                <td>{{ $row->stage_name }}</td>
                                <td>{{ $row->recorded_on?->format('Y-m-d') }}</td>
                                <td>{{ $row->notes ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3">{{ __('crops_feed.empty.no_growth_stages') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-block">
            <h5 class="section-title">{{ __('crops_feed.actions.add_cost_item') }}</h5>
            <form method="POST" action="{{ route('customer.crops-feed.crops.cost-items.store', ['locale' => $currentLocale]) }}" class="row g-3">
                @csrf
                <input type="hidden" name="crop_id" value="{{ $crop->id }}">
                <div class="col-md-4">
                    <label class="form-label">{{ __('crops_feed.fields.item') }}</label>
                    <input type="text" name="item" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('crops_feed.fields.amount') }}</label>
                    <input type="number" step="0.01" min="0.01" name="amount" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('crops_feed.fields.cost_date') }}</label>
                    <input type="date" name="cost_date" class="form-control" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">{{ __('crops_feed.fields.notes') }}</label>
                    <input type="text" name="notes" class="form-control">
                </div>
                <div class="col-12">
                    <button class="btn btn-primary-green" type="submit">{{ __('crops_feed.actions.save') }}</button>
                </div>
            </form>

            <div class="table-container mt-3">
                <table class="table registry-table mb-0">
                    <thead>
                        <tr>
                            <th>{{ __('crops_feed.fields.item') }}</th>
                            <th>{{ __('crops_feed.fields.amount') }}</th>
                            <th>{{ __('crops_feed.fields.cost_date') }}</th>
                            <th>{{ __('crops_feed.fields.notes') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($crop->costItems as $row)
                            <tr>
                                <td>{{ $row->item }}</td>
                                <td>{{ $row->amount }}</td>
                                <td>{{ $row->cost_date?->format('Y-m-d') }}</td>
                                <td>{{ $row->notes ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4">{{ __('crops_feed.empty.no_cost_items') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
