@extends('layouts.customer.dashboard')

@section('title', __('poultry.titles.layer_flocks'))
@push('styles')<link rel="stylesheet" href="{{ asset('assets/css/pages/livestock.css') }}">@endpush

@section('content')
<div class="container py-4 livestock-page">
    <div class="page-head"><h2 class="page-title">{{ __('poultry.titles.layer_flocks') }}</h2><a class="btn btn-primary-green" href="{{ route('customer.poultry.layer-flocks.create', ['locale' => $currentLocale]) }}">{{ __('poultry.actions.add_layer_flock') }}</a></div>
    @include('dashboard.customer.poultry.partials.flash')
    <div class="table-container"><table class="table registry-table mb-0"><thead><tr><th>{{ __('poultry.fields.flock_number') }}</th><th>{{ __('poultry.fields.chicken_count') }}</th><th>{{ __('poultry.fields.total_mortality') }}</th><th>{{ __('poultry.fields.net_profit') }}</th><th>{{ __('poultry.fields.status') }}</th><th>{{ __('poultry.fields.actions') }}</th></tr></thead><tbody>
        @forelse($flocks as $flock)
            <tr><td>{{ $flock->flock_number }}</td><td>{{ $flock->chicken_count }}</td><td>{{ $flock->total_mortality }}</td><td>{{ $flock->net_profit }}</td><td>{{ __('poultry.options.' . $flock->status) }}</td><td class="d-flex gap-2"><a class="btn btn-sm btn-outline-primary" href="{{ route('customer.poultry.layer-flocks.show', ['locale' => $currentLocale, 'layer_flock' => $flock->id]) }}">{{ __('poultry.actions.view') }}</a><a class="btn btn-sm btn-outline-secondary" href="{{ route('customer.poultry.layer-flocks.edit', ['locale' => $currentLocale, 'layer_flock' => $flock->id]) }}">{{ __('poultry.actions.edit') }}</a></td></tr>
        @empty
            <tr><td colspan="6">{{ __('poultry.empty.no_layer_flocks') }}</td></tr>
        @endforelse
    </tbody></table>{{ $flocks->links('pagination::bootstrap-5') }}</div>
</div>
@endsection
