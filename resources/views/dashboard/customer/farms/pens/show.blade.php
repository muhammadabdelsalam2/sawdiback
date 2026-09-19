@extends('layouts.customer.dashboard')

@section('title', __('farms.titles.pen_details') . ' - ' . ($pen->pen_number ?? ''))

@section('content')
@php
    $currentLocale = request()->route('locale') ?? app()->getLocale();
@endphp

<div class="container-fluid my-4">
    {{-- شريط العنوان وأزرار التنقل --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <h2 class="h3 font-weight-bold text-gray-800 mb-1">
                {{ __('farms.titles.pen_details') }}: {{ $pen->pen_number }} {{ $pen->name ? '(' . $pen->name . ')' : '' }}
            </h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent p-0 mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('customer.farm-pens.index', ['locale' => $currentLocale]) }}">{{ __('farms.titles.pens') }}</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $pen->pen_number }}</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('customer.farm-pens.index', ['locale' => $currentLocale]) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-1"></i> {{ __('farms.actions.back') }}
            </a>
            <button type="button" class="btn btn-success" data-toggle="modal" data-target="#addFinancialEntryModal" data-bs-toggle="modal" data-bs-target="#addFinancialEntryModal">
                <i class="fas fa-plus-circle mr-1"></i> {{ __('farms.actions.record_financial_entry') }}
            </button>
            <a href="{{ route('customer.farm-pens.edit', ['locale' => $currentLocale, 'farm_pen' => $pen->id]) }}" class="btn btn-primary">
                <i class="fas fa-edit mr-1"></i> {{ __('farms.actions.edit') }}
            </a>
        </div>
    </div>

    {{-- تنبيهات النجاح والأخطاء --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close btn-close" data-dismiss="alert" data-bs-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close btn-close" data-dismiss="alert" data-bs-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    {{-- كروت الملخص المالي --}}
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm border-left-success py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">{{ __('farms.fields.total_sales') }}</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($profitSummary['total_sales'] ?? 0, 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm border-left-danger py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">{{ __('farms.fields.feed_costs') }}</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($profitSummary['feed_costs'] ?? 0, 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm border-left-warning py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">{{ __('farms.fields.slaughter_packaging_costs') }}</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($profitSummary['slaughter_packaging_costs'] ?? 0, 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm border-left-primary py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">{{ __('farms.fields.net_profit') }}</div>
                    <div class="h5 mb-0 font-weight-bold {{ ($profitSummary['net_profit'] ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                        {{ number_format($profitSummary['net_profit'] ?? 0, 2) }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- تفاصيل الحظيرة الأساسية --}}
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-bottom font-weight-bold">
                    <i class="fas fa-info-circle mr-1 text-primary"></i> {{ __('farms.titles.pen_details') }}
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">{{ __('farms.fields.farm') }}:</span>
                            <strong>{{ $pen->farm->name ?? '-' }}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">{{ __('farms.fields.type') }}:</span>
                            <span class="badge badge-light border">
                                {{ __('farms.types.' . $pen->type) != 'farms.types.' . $pen->type ? __('farms.types.' . $pen->type) : ($pen->type ?? '-') }}
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">{{ __('farms.fields.capacity') }}:</span>
                            <strong>{{ $pen->capacity ?? 0 }}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">{{ __('farms.fields.current_count') }}:</span>
                            <strong>{{ $pen->current_count ?? 0 }}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">{{ __('farms.fields.status') }}:</span>
                            <span class="badge badge-secondary">
                                {{ __('farms.options.' . $pen->status) != 'farms.options.' . $pen->status ? __('farms.options.' . $pen->status) : ($pen->status ?? '-') }}
                            </span>
                        </li>
                        <li class="list-group-item px-0">
                            <span class="text-muted d-block mb-1">{{ __('farms.fields.notes') }}:</span>
                            <p class="small text-dark mb-0 bg-light p-2 rounded">{{ $pen->notes ?: '-' }}</p>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- تبويبات القيود المالية وقائمة الحيوانات --}}
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <ul class="nav nav-tabs card-header-tabs" id="penTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active font-weight-bold" id="financial-tab" data-toggle="tab" data-bs-toggle="tab" href="#financial" role="tab" aria-controls="financial" aria-selected="true">
                                <i class="fas fa-receipt mr-1 text-success"></i> {{ __('farms.titles.financial_entries') }} ({{ $pen->financialEntries->count() }})
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link font-weight-bold" id="animals-tab" data-toggle="tab" data-bs-toggle="tab" href="#animals" role="tab" aria-controls="animals" aria-selected="false">
                                <i class="fas fa-paw mr-1 text-primary"></i> {{ __('farms.fields.animal_count') }} ({{ $pen->animals->count() }})
                            </a>
                        </li>
                    </ul>
                    <button type="button" class="btn btn-sm btn-outline-success" data-toggle="modal" data-target="#addFinancialEntryModal" data-bs-toggle="modal" data-bs-target="#addFinancialEntryModal">
                        <i class="fas fa-plus mr-1"></i> {{ __('farms.actions.record_financial_entry') }}
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="tab-content" id="penTabsContent">
                        {{-- تبويب القيود المالية --}}
                        <div class="tab-pane fade show active" id="financial" role="tabpanel" aria-labelledby="financial-tab">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped mb-0 text-center">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>#</th>
                                            <th>{{ __('farms.fields.entry_type') }}</th>
                                            <th>{{ __('farms.fields.amount') }}</th>
                                            <th>{{ __('farms.fields.entry_date') }}</th>
                                            <th>{{ __('farms.fields.notes') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($pen->financialEntries as $entry)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>
                                                    @if($entry->type === 'sale')
                                                        <span class="badge badge-success">{{ __('farms.options.sale') }}</span>
                                                    @elseif($entry->type === 'feed_costs')
                                                        <span class="badge badge-danger">{{ __('farms.fields.feed_costs') }}</span>
                                                    @elseif($entry->type === 'slaughter_packaging')
                                                        <span class="badge badge-warning">{{ __('farms.options.slaughter_packaging') }}</span>
                                                    @else
                                                        <span class="badge badge-secondary">{{ $entry->type }}</span>
                                                    @endif
                                                </td>
                                                <td class="font-weight-bold">{{ number_format($entry->amount, 2) }}</td>
                                                <td>{{ $entry->entry_date ? \Carbon\Carbon::parse($entry->entry_date)->format('Y-m-d') : '-' }}</td>
                                                <td>{{ $entry->notes ?? '-' }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="py-4 text-muted">{{ __('farms.empty.no_financial_entries') }}</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- تبويب الحيوانات --}}
                        <div class="tab-pane fade" id="animals" role="tabpanel" aria-labelledby="animals-tab">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped mb-0 text-center">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>#</th>
                                            <th>{{ __('farms.fields.code') }} / Tag</th>
                                            <th>{{ __('farms.fields.name') }}</th>
                                            <th>{{ __('farms.fields.type') }}</th>
                                            <th>{{ __('farms.fields.status') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($pen->animals as $animal)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td class="font-weight-bold">{{ $animal->tag_number ?? $animal->code ?? $animal->id }}</td>
                                                <td>{{ $animal->name ?? '-' }}</td>
                                                <td>{{ $animal->species->name ?? '-' }} - {{ $animal->breed->name ?? '-' }}</td>
                                                <td><span class="badge badge-info">{{ $animal->status ?? '-' }}</span></td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="py-4 text-muted">{{ __('farms.empty.no_animals') }}</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- نافذة تسجيل قيد مالي جديد (Modal) --}}
<div class="modal fade" id="addFinancialEntryModal" tabindex="-1" role="dialog" aria-labelledby="financialModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form action="{{ url($currentLocale . '/farm-pens/' . $pen->id . '/financial-entries') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold" id="financialModalLabel">{{ __('farms.actions.record_financial_entry') }}</h5>
                    <button type="button" class="close btn-close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label class="font-weight-bold">{{ __('farms.fields.entry_type') }} <span class="text-danger">*</span></label>
                        <select name="type" class="form-control" required>
                            <option value="feed_costs">{{ __('farms.fields.feed_costs') }}</option>
                            <option value="slaughter_packaging">{{ __('farms.options.slaughter_packaging') }}</option>
                            <option value="sale">{{ __('farms.options.sale') }}</option>
                            <option value="other">{{ __('farms.types.other') }}</option>
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold">{{ __('farms.fields.amount') }} <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0.01" name="amount" class="form-control" required placeholder="0.00">
                    </div>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold">{{ __('farms.fields.entry_date') }} <span class="text-danger">*</span></label>
                        <input type="date" name="entry_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="form-group mb-0">
                        <label class="font-weight-bold">{{ __('farms.fields.notes') }}</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="{{ __('farms.fields.notes') }}"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal">{{ __('farms.actions.cancel') }}</button>
                    <button type="submit" class="btn btn-success">{{ __('farms.actions.save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection