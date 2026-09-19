@extends('layouts.customer.dashboard')

@php
    $currentLocale = request()->route('locale') ?? app()->getLocale();
@endphp

@section('title', __('fisheries.titles.fisheries_batches'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/pages/livestock.css') }}">
@endpush

@section('content')
    <div class="container py-4 livestock-page">
        {{-- ترويسة الصفحة مع زر الإضافة السريع --}}
        <div class="page-head">
            <h2 class="page-title">{{ __('fisheries.titles.fisheries_batches') }}</h2>
            <div class="quick-actions">
                <a class="btn btn-primary-green" href="{{ route('customer.fisheries.create', ['locale' => $currentLocale]) }}">
                    <i class="fas fa-plus mr-1"></i> {{ __('fisheries.actions.add_batch') }}
                </a>
            </div>
        </div>

        {{-- تنبيهات النجاح والرسائل --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- شريط التصفية والبحث --}}
        <div class="card-block mb-3">
            <form method="GET" action="{{ route('customer.fisheries.index', ['locale' => $currentLocale]) }}" class="row g-2 align-items-center">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control"
                           placeholder="{{ __('fisheries.actions.search') }}"
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="farm_id" class="form-select">
                        <option value="">{{ __('farms.titles.all_farms') !== 'farms.titles.all_farms' ? __('farms.titles.all_farms') : '-- كل المزارع --' }}</option>
                        @foreach($farms ?? [] as $farm)
                            <option value="{{ $farm->id }}" @selected(request('farm_id') == $farm->id)>{{ $farm->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">{{ __('common.fields.all_statuses') !== 'common.fields.all_statuses' ? __('common.fields.all_statuses') : '-- كل الحالات --' }}</option>
                        @foreach(['active', 'harvested', 'paused'] as $st)
                            <option value="{{ $st }}" @selected(request('status') === $st)>
                                {{ __('fisheries.options.' . $st) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary-green w-100">
                        <i class="fas fa-filter mr-1"></i> {{ __('fisheries.actions.filter') }}
                    </button>
                    <a href="{{ route('customer.fisheries.index', ['locale' => $currentLocale]) }}" class="btn btn-outline-white">
                        {{ __('fisheries.actions.reset') }}
                    </a>
                </div>
            </form>
        </div>

        {{-- جدول سجل الأحواض والدفعات --}}
        <div class="table-container">
            <table class="table registry-table mb-0">
                <thead>
                <tr>
                    <th>{{ __('fisheries.fields.batch_code') }}</th>
                    <th>{{ __('fisheries.fields.pond_name') }}</th>
                    <th>{{ __('fisheries.fields.farm_id') }}</th>
                    <th>{{ __('fisheries.fields.fish_type') }}</th>
                    <th>{{ __('fisheries.fields.current_count') }}</th>
                    <th>{{ __('fisheries.fields.feed_consumed_kg') }}</th>
                    <th>{{ __('fisheries.fields.status') }}</th>
                    <th>{{ __('fisheries.fields.actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @forelse($batches as $batch)
                    <tr>
                        <td><strong>{{ $batch->batch_code ?? '-' }}</strong></td>
                        <td>{{ $batch->pond_name }}</td>
                        <td>{{ $batch->farm?->name ?? '-' }}</td>
                        <td>{{ $batch->fish_type }}</td>
                        <td class="font-weight-bold text-primary">{{ number_format($batch->current_count) }}</td>
                        <td>
                            {{ number_format($batch->feed_consumed_kg, 2) }} {{ __('fisheries.units.kg') }}
                            @if($batch->feed_consumed_kg >= 1000)
                                <small class="text-muted d-block">({{ number_format($batch->feed_consumed_kg / 1000, 2) }} {{ __('fisheries.units.ton') }})</small>
                            @endif
                        </td>
                        <td>
                            @php
                                $badgeClass = match($batch->status) {
                                    'active'    => 'bg-success',
                                    'harvested' => 'bg-info text-dark',
                                    'paused'    => 'bg-secondary',
                                    default     => 'bg-light text-dark'
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }}">
                                {{ __('fisheries.options.' . $batch->status) }}
                            </span>
                        </td>
                        <td class="d-flex gap-2">
                            <a class="btn btn-sm btn-outline-primary" href="{{ route('customer.fisheries.show', ['locale' => $currentLocale, 'fish_batch' => $batch->id]) }}">
                                {{ __('fisheries.actions.view') }}
                            </a>
                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('customer.fisheries.edit', ['locale' => $currentLocale, 'fish_batch' => $batch->id]) }}">
                                {{ __('fisheries.actions.edit') }}
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">{{ __('fisheries.empty.no_batches') }}</td>
                    </tr>
                @endforelse
                </tbody>
            </table>

            @if($batches->hasPages())
                <div class="p-3 border-top">
                    {{ $batches->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
@endsection
