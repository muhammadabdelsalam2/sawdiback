@extends('layouts.customer.dashboard')

@section('title', __('poultry.titles.hatchery_batches'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/pages/livestock.css') }}">
    <style>
        .registry-table th {
            background-color: #f8fafc;
            color: #334155;
            font-weight: 700;
            white-space: nowrap;
        }
        .registry-table td {
            vertical-align: middle;
        }
    </style>
@endpush

@section('content')
@php
    $currentLocale = request()->route('locale') ?? app()->getLocale();
    $isArabic = str_starts_with(strtolower($currentLocale), 'ar');
@endphp

<div class="container py-4 livestock-page">
    <div class="page-head mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h2 class="page-title mb-0 font-weight-bold text-dark">{{ __('poultry.titles.hatchery_batches') }}</h2>
        <a class="btn btn-primary-green px-3" href="{{ route('customer.poultry.hatchery-batches.create', ['locale' => $currentLocale]) }}">
            <i class="fas fa-plus mr-1"></i> {{ __('poultry.actions.add_hatchery_batch') }}
        </a>
    </div>

    @include('dashboard.customer.poultry.partials.flash')

    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        <div class="table-responsive">
            <table class="table registry-table table-hover mb-0 text-center align-middle">
                <thead>
                    <tr>
                        <th>{{ __('poultry.fields.batch_number') }}</th>
                        <th>{{ __('poultry.fields.machine') }}</th>
                        <th>{{ $isArabic ? 'المصدر' : 'Source' }}</th>
                        <th>{{ $isArabic ? 'مبلغ الشراء' : 'Purchase Cost' }}</th>
                        <th>{{ __('poultry.fields.eggs_loaded') }}</th>
                        <th>{{ __('poultry.fields.chicks_produced') }}</th>
                        <th>{{ __('poultry.fields.success_rate') }}</th>
                        <th style="width: 130px;">{{ __('poultry.fields.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($batches as $batch)
                        <tr>
                            <td class="font-weight-bold text-primary">{{ $batch->batch_number }}</td>
                            <td>{{ $batch->machine?->machine_number ?? '-' }}</td>
                            <td>
                                @if($batch->egg_source === 'purchased')
                                    <span class="badge bg-warning text-dark">{{ $isArabic ? 'شراء' : 'Purchased' }}</span>
                                @else
                                    <span class="badge bg-light text-dark border">{{ $isArabic ? 'المزرعة' : 'Farm' }}</span>
                                @endif
                            </td>
                            <td class="font-weight-bold">{{ number_format((float)$batch->purchase_amount, 2) }}</td>
                            <td>{{ number_format($batch->eggs_loaded) }}</td>
                            <td class="text-success font-weight-bold">{{ number_format($batch->chicks_produced) }}</td>
                            <td>
                                <span class="badge bg-light text-info border font-weight-bold">
                                    {{ $batch->success_rate }}%
                                </span>
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-1">
                                    <a class="btn btn-sm btn-outline-primary" 
                                       href="{{ route('customer.poultry.hatchery-batches.show', ['locale' => $currentLocale, 'hatchery_batch' => $batch->id]) }}" 
                                       title="{{ __('poultry.actions.view') }}">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a class="btn btn-sm btn-outline-secondary" 
                                       href="{{ route('customer.poultry.hatchery-batches.edit', ['locale' => $currentLocale, 'hatchery_batch' => $batch->id]) }}" 
                                       title="{{ __('poultry.actions.edit') }}">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-4 text-muted">
                                <i class="fas fa-inbox fa-2x mb-2 d-block text-secondary"></i>
                                {{ __('poultry.empty.no_hatchery_batches') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($batches->hasPages())
            <div class="card-footer bg-white border-top py-3">
                {{ $batches->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>
@endsection