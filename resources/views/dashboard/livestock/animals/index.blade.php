@extends('layouts.customer.dashboard')

@section('title', __('livestock.titles.animals'))

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        .animals-page .card-main {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.04);
            border: 1px solid #eef2f6;
            overflow: hidden;
        }

        .animals-page .table thead th {
            background-color: #f8fafc;
            color: #334155;
            font-size: 0.84rem;
            font-weight: 700;
            border-bottom: 2px solid #e2e8f0;
            padding: 12px 10px;
            vertical-align: middle;
            white-space: nowrap;
        }

        .animals-page .table tbody td {
            font-size: 0.88rem;
            color: #1e293b;
            vertical-align: middle;
            padding: 12px 10px;
            border-bottom: 1px solid #f1f5f9;
        }

        .animals-page .table tbody tr:hover {
            background-color: #f8fafc;
        }

        .animals-page .table-responsive {
            overflow-x: auto !important;
            overflow-y: hidden !important;
            scrollbar-width: thin;
        }

        .tag-pill {
            background-color: #f1f5f9;
            color: #0f172a;
            border: 1px solid #cbd5e1;
            padding: 3px 8px;
            border-radius: 6px;
            font-family: monospace;
            font-weight: 700;
        }

        .badge-health-healthy {
            background-color: #dcfce7;
            color: #15803d;
            border: 1px solid #bbf7d0;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.76rem;
            font-weight: 600;
        }

        .badge-health-sick {
            background-color: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fecaca;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.76rem;
            font-weight: 600;
        }

        .badge-health-treatment {
            background-color: #fef9c3;
            color: #854d0e;
            border: 1px solid #fef08a;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.76rem;
            font-weight: 600;
        }

        .badge-status-active {
            background-color: #e0f2fe;
            color: #0369a1;
            border: 1px solid #bae6fd;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.76rem;
            font-weight: 600;
        }

        .btn-action-view {
            color: #0284c7;
            background-color: #f0f9ff;
            border: 1px solid #bae6fd;
            border-radius: 6px;
            padding: 5px 9px;
            transition: all 0.2s ease;
        }
        .btn-action-view:hover {
            background-color: #0284c7;
            color: #ffffff;
        }

        .btn-action-edit {
            color: #d97706;
            background-color: #fffbeb;
            border: 1px solid #fde68a;
            border-radius: 6px;
            padding: 5px 9px;
            transition: all 0.2s ease;
        }
        .btn-action-edit:hover {
            background-color: #d97706;
            color: #ffffff;
        }

        .filter-card {
            background: #ffffff;
            border: 1px solid #eef2f6;
            border-radius: 12px;
            padding: 14px;
            margin-bottom: 1rem;
        }
    </style>
@endpush

@section('content')
@php
    $currentLocale = request()->route('locale') ?? app()->getLocale();
    $isArabic = str_starts_with(strtolower($currentLocale), 'ar');
@endphp

<div class="container-fluid my-4 animals-page">
    {{-- Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <h2 class="h3 font-weight-bold text-dark mb-1">{{ __('livestock.titles.animals') }}</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent p-0 mb-0">
                    <li class="breadcrumb-item text-muted">{{ __('livestock.titles.animals') }}</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a class="btn btn-outline-secondary btn-sm px-3"
                href="{{ route('customer.livestock.reproduction-cycles.index', ['locale' => $currentLocale]) }}">
                <i class="fas fa-venus-mars mr-1"></i> {{ __('livestock.actions.reproduction') }}
            </a>
            <a class="btn btn-outline-warning btn-sm px-3 text-dark"
                href="{{ route('customer.livestock.alerts.under-treatment', ['locale' => $currentLocale]) }}">
                <i class="fas fa-heartbeat mr-1 text-danger"></i> {{ __('livestock.actions.alerts') }}
            </a>
            <a class="btn btn-primary btn-sm px-3 shadow-sm font-weight-bold"
                href="{{ route('customer.livestock.animals.create', ['locale' => $currentLocale]) }}">
                <i class="fas fa-plus mr-1"></i> {{ __('livestock.actions.register_animal') }}
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    {{-- Filters --}}
    <div class="filter-card shadow-sm">
        <form method="GET" action="{{ route('customer.livestock.animals.index', ['locale' => $currentLocale]) }}" class="row g-2 align-items-center">
            <div class="col-md-3 col-sm-6">
                <input type="text" name="search" class="form-control form-control-sm" 
                       placeholder="{{ $isArabic ? 'بحث برقم الشريحة / المعرّف...' : 'Search by Tag / ID...' }}" 
                       value="{{ request('search') }}">
            </div>
            
            @if(isset($farms))
                <div class="col-md-3 col-sm-6">
                    <select name="farm_id" id="filterFarmSelect" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">-- {{ __('farms.fields.farm') }}: {{ $isArabic ? 'الكل (المزارع الأربعة)' : 'All Farms' }} --</option>
                        @foreach($farms as $farm)
                            <option value="{{ $farm->id }}" @selected(request('farm_id') == $farm->id)>{{ $farm->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            @if(isset($pens))
                <div class="col-md-3 col-sm-6">
                    <select name="pen_id" id="filterPenSelect" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">-- {{ __('farms.fields.pen') }}: {{ $isArabic ? 'الكل' : 'All' }} --</option>
                        @foreach($pens as $pen)
                            <option value="{{ $pen->id }}" data-farm="{{ $pen->farm_id }}" @selected(request('pen_id') == $pen->id)>
                                {{ $pen->pen_number }} ({{ $pen->farm->name ?? '' }})
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="col-md-3 col-sm-6 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-primary w-100">
                    <i class="fas fa-filter mr-1"></i> {{ $isArabic ? 'فلترة' : 'Filter' }}
                </button>
                @if(request()->hasAny(['search', 'farm_id', 'pen_id']))
                    <a href="{{ route('customer.livestock.animals.index', ['locale' => $currentLocale]) }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-undo"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Animals Table --}}
    <div class="card-main">
        <div class="table-responsive">
            <table class="table table-hover mb-0 text-center">
                <thead>
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th>{{ __('livestock.fields.tag') }}</th>
                        <th>{{ __('livestock.fields.species') }}</th>
                        <th>{{ __('livestock.fields.breed') }}</th>
                        <th>{{ __('farms.fields.farm') }}</th>
                        <th>{{ __('farms.fields.pen') }}</th>
                        <th>{{ __('livestock.fields.gender') }}</th>
                        <th>{{ __('livestock.fields.status') }}</th>
                        <th>{{ __('livestock.fields.health') }}</th>
                        <th style="width: 120px;">{{ __('livestock.fields.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($items as $animal)
                        <tr>
                            <td class="text-muted font-weight-bold">{{ $loop->iteration }}</td>
                            <td><span class="tag-pill">{{ $animal->tag_number }}</span></td>
                            <td class="font-weight-bold">{{ $animal->species->name ?? '-' }}</td>
                            <td>{{ $animal->breed->name ?? '-' }}</td>
                            <td>
                                <span class="badge bg-light text-primary border font-weight-bold">
                                    {{ $animal->pen?->farm?->name ?? ($animal->farm?->name ?? '-') }}
                                </span>
                            </td>
                            <td>
                                @if($animal->pen)
                                    <span class="badge bg-light text-dark border">
                                        {{ $animal->pen->pen_number }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-light text-secondary border">
                                    {{ __('livestock.options.' . $animal->gender) != 'livestock.options.' . $animal->gender ? __('livestock.options.' . $animal->gender) : $animal->gender }}
                                </span>
                            </td>
                            <td>
                                <span class="badge-status-active">
                                    {{ __('livestock.options.' . $animal->status) != 'livestock.options.' . $animal->status ? __('livestock.options.' . $animal->status) : $animal->status }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $healthClass = match($animal->health_status) {
                                        'healthy'         => 'badge-health-healthy',
                                        'sick', 'dead'    => 'badge-health-sick',
                                        'under_treatment' => 'badge-health-treatment',
                                        default           => 'badge-health-healthy',
                                    };
                                @endphp
                                <span class="{{ $healthClass }}">
                                    {{ __('livestock.options.' . $animal->health_status) != 'livestock.options.' . $animal->health_status ? __('livestock.options.' . $animal->health_status) : $animal->health_status }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="{{ route('customer.livestock.animals.show', ['locale' => $currentLocale, 'animal' => $animal->id]) }}" 
                                       class="btn-action-view" title="{{ __('livestock.actions.view') }}">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('customer.livestock.animals.edit', ['locale' => $currentLocale, 'animal' => $animal->id]) }}" 
                                       class="btn-action-edit" title="{{ __('livestock.actions.edit') }}">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="py-5 text-center text-muted">
                                <i class="fas fa-paw fa-2x mb-2 d-block text-secondary"></i>
                                {{ __('livestock.empty.no_animals') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($items->hasPages())
            <div class="card-footer bg-white border-top py-3">
                {{ $items->links() }}
            </div>
        @endif
    </div>
</div>
@endsection