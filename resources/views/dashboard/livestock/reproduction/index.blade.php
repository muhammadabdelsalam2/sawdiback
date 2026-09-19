@extends('layouts.customer.dashboard')

@section('title', __('livestock.titles.reproduction_cycles'))

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/pages/livestock.css') }}">
    <style>
        .reproduction-page .card-main {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.04);
            border: 1px solid #eef2f6;
            margin-bottom: 2rem;
            overflow: hidden;
        }

        .reproduction-page .section-header {
            background: #ffffff;
            border-bottom: 1px solid #eef2f6;
            padding: 1rem 1.25rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .reproduction-page .table thead th {
            background-color: #f8fafc;
            color: #334155;
            font-size: 0.84rem;
            font-weight: 700;
            border-bottom: 2px solid #e2e8f0;
            padding: 12px 10px;
            vertical-align: middle;
            white-space: nowrap;
        }

        .reproduction-page .table tbody td {
            font-size: 0.88rem;
            color: #1e293b;
            vertical-align: middle;
            padding: 12px 10px;
            border-bottom: 1px solid #f1f5f9;
        }

        .reproduction-page .table tbody tr:hover {
            background-color: #f8fafc;
        }

        .reproduction-page .table-responsive {
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
            text-decoration: none;
        }
        .tag-pill:hover {
            color: #15803d;
            border-color: #86efac;
        }

        /* شارات المراحل التناسلية */
        .badge-cycle-heat {
            background-color: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fecaca;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.76rem;
            font-weight: 600;
        }

        .badge-cycle-inseminated {
            background-color: #fef9c3;
            color: #854d0e;
            border: 1px solid #fef08a;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.76rem;
            font-weight: 600;
        }

        .badge-cycle-pregnant {
            background-color: #dcfce7;
            color: #15803d;
            border: 1px solid #bbf7d0;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.76rem;
            font-weight: 600;
        }

        .badge-cycle-completed {
            background-color: #e0f2fe;
            color: #0369a1;
            border: 1px solid #bae6fd;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.76rem;
            font-weight: 600;
        }

        .action-sub-btn {
            padding: 4px 8px;
            font-size: 0.78rem;
            border-radius: 6px;
            font-weight: 600;
        }

        .form-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 10px;
        }
    </style>
@endpush

@section('content')
@php
    $currentLocale = request()->route('locale') ?? app()->getLocale();
    $isArabic = str_starts_with(strtolower($currentLocale), 'ar');
@endphp

<div class="container-fluid py-4 reproduction-page">
    {{-- رأس الصفحة --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <h2 class="h3 font-weight-bold text-dark mb-1">
                <i class="fas fa-venus-mars text-primary mr-2"></i> {{ __('livestock.titles.reproduction_cycles') }}
            </h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent p-0 mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('customer.livestock.animals.index', ['locale' => $currentLocale]) }}">{{ __('livestock.titles.animals') }}</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">{{ __('livestock.titles.reproduction_cycles') }}</li>
                </ol>
            </nav>
        </div>
        <div>
            <a class="btn btn-outline-secondary btn-sm px-3"
                href="{{ route('customer.livestock.animals.index', ['locale' => $currentLocale]) }}">
                <i class="fas fa-arrow-left mr-1"></i> {{ __('livestock.actions.back_to_list') }}
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

    @if ($errors->any())
        <div class="alert alert-danger border-0 shadow-sm mb-3">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- كارت فتح دورة تناسلية جديدة --}}
    <div class="card-main">
        <div class="section-header">
            <h5 class="mb-0 font-weight-bold text-success">
                <i class="fas fa-plus-circle mr-2"></i> {{ __('livestock.sections.open_new_cycle') }}
            </h5>
        </div>
        <div class="p-3">
            <form method="POST" action="{{ route('customer.livestock.reproduction-cycles.store', ['locale' => $currentLocale]) }}" class="row g-3">
                @csrf
                <div class="col-md-4">
                    <label class="form-label font-weight-bold small text-muted">{{ __('livestock.fields.female_animal') }} <span class="text-danger">*</span></label>
                    <select name="female_animal_id" class="form-select form-select-sm" required>
                        <option value="">-- {{ $isArabic ? 'اختر الأنثى' : 'Select Female' }} --</option>
                        @foreach ($femaleAnimals as $animal)
                            <option value="{{ $animal->id }}">{{ $animal->tag_number }} ({{ $animal->species->name ?? '' }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label font-weight-bold small text-muted">{{ __('livestock.fields.heat_date') }}</label>
                    <input type="date" name="heat_date" class="form-control form-control-sm" value="{{ now()->toDateString() }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label font-weight-bold small text-muted">{{ __('livestock.fields.insemination_type') }}</label>
                    <select name="insemination_type" class="form-select form-select-sm">
                        <option value="natural">{{ __('livestock.options.natural') }}</option>
                        <option value="artificial">{{ __('livestock.options.artificial') }}</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button class="btn btn-sm btn-primary-green w-100 font-weight-bold py-2" type="submit">
                        <i class="fas fa-play mr-1"></i> {{ __('livestock.actions.open_cycle') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- جدول الدورات التناسلية النشطة والسابقة --}}
    <div class="card-main">
        <div class="section-header">
            <h5 class="mb-0 font-weight-bold text-dark">
                <i class="fas fa-stream mr-2 text-primary"></i> {{ $isArabic ? 'سجل الدورات التناسلية' : 'Reproduction Registry' }}
            </h5>
            <span class="badge bg-light text-dark border">{{ $rows->total() ?? count($rows) }}</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0 text-center">
                <thead>
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th>{{ __('livestock.fields.female_animal') }}</th>
                        <th>{{ __('livestock.fields.status') }}</th>
                        <th>{{ __('livestock.fields.heat_date') }}</th>
                        <th>{{ __('livestock.sections.insemination') }}</th>
                        <th>{{ __('livestock.fields.expected_delivery_date') }}</th>
                        <th style="width: 140px;">{{ __('livestock.fields.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rows as $cycle)
                        <tr>
                            <td class="text-muted font-weight-bold">{{ $loop->iteration }}</td>
                            <td>
                                @if($cycle->femaleAnimal)
                                    <a href="{{ route('customer.livestock.animals.show', ['locale' => $currentLocale, 'animal' => $cycle->femaleAnimal->id]) }}" class="tag-pill">
                                        {{ $cycle->femaleAnimal->tag_number }}
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $statusKey = 'livestock.options.' . $cycle->status;
                                    $statusBadgeClass = match($cycle->status) {
                                        'heat' => 'badge-cycle-heat',
                                        'inseminated' => 'badge-cycle-inseminated',
                                        'pregnant' => 'badge-cycle-pregnant',
                                        default => 'badge-cycle-completed'
                                    };
                                @endphp
                                <span class="{{ $statusBadgeClass }}">
                                    {{ \Illuminate\Support\Facades\Lang::has($statusKey) ? __($statusKey) : \Illuminate\Support\Str::headline((string) $cycle->status) }}
                                </span>
                            </td>
                            <td>{{ optional($cycle->heat_date)->toDateString() ?? '-' }}</td>
                            <td>{{ optional($cycle->insemination_date)->toDateString() ?? '-' }}</td>
                            <td>
                                @if($cycle->expected_delivery_date)
                                    <span class="badge bg-light text-dark border font-monospace">
                                        {{ optional($cycle->expected_delivery_date)->toDateString() }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary action-sub-btn" type="button" 
                                        data-bs-toggle="collapse" data-bs-target="#cycleActions{{ $cycle->id }}" aria-expanded="false">
                                    <i class="fas fa-edit mr-1"></i> {{ $isArabic ? 'إدارة الإجراءات' : 'Actions' }}
                                </button>
                            </td>
                        </tr>

                        {{-- قسم العمليات القابل للطي أسفل كل دورة --}}
                        <tr class="collapse bg-light" id="cycleActions{{ $cycle->id }}">
                            <td colspan="7" class="p-3 text-start">
                                <div class="row g-3">
                                    {{-- 1. تسجيل التلقيح --}}
                                    <div class="col-md-4">
                                        <div class="form-box shadow-sm h-100">
                                            <h6 class="font-weight-bold text-dark border-bottom pb-1 mb-2">
                                                <i class="fas fa-mars text-primary mr-1"></i> {{ __('livestock.sections.insemination') }}
                                            </h6>
                                            <form method="POST" action="{{ route('customer.livestock.reproduction-cycles.insemination', ['locale' => $currentLocale, 'cycle' => $cycle->id]) }}">
                                                @csrf
                                                <label class="small text-muted mb-1">{{ __('livestock.fields.date') }}</label>
                                                <input type="date" name="insemination_date" class="form-control form-control-sm mb-2" value="{{ now()->toDateString() }}" required>

                                                <label class="small text-muted mb-1">{{ __('livestock.fields.type') }}</label>
                                                <select name="insemination_type" class="form-select form-select-sm mb-2" required>
                                                    <option value="natural">{{ __('livestock.options.natural') }}</option>
                                                    <option value="artificial">{{ __('livestock.options.artificial') }}</option>
                                                </select>

                                                <label class="small text-muted mb-1">{{ __('livestock.fields.male_animal_optional') }}</label>
                                                <select name="male_animal_id" class="form-select form-select-sm mb-2">
                                                    <option value="">-- {{ $isArabic ? 'اختياري' : 'Optional' }} --</option>
                                                    @foreach ($maleAnimals as $male)
                                                        <option value="{{ $male->id }}">{{ $male->tag_number }} ({{ $male->species->name ?? '' }})</option>
                                                    @endforeach
                                                </select>

                                                <button class="btn btn-sm btn-primary-green w-100 font-weight-bold" type="submit">
                                                    {{ __('livestock.actions.save') }}
                                                </button>
                                            </form>
                                        </div>
                                    </div>

                                    {{-- 2. فحص الحمل --}}
                                    <div class="col-md-4">
                                        <div class="form-box shadow-sm h-100">
                                            <h6 class="font-weight-bold text-dark border-bottom pb-1 mb-2">
                                                <i class="fas fa-stethoscope text-info mr-1"></i> {{ __('livestock.sections.pregnancy_check') }}
                                            </h6>
                                            <form method="POST" action="{{ route('customer.livestock.reproduction-cycles.pregnancy-check', ['locale' => $currentLocale, 'cycle' => $cycle->id]) }}">
                                                @csrf
                                                <label class="small text-muted mb-1">{{ __('livestock.fields.status') }}</label>
                                                <select name="pregnancy_confirmed" class="form-select form-select-sm mb-2" required>
                                                    <option value="1">{{ __('livestock.options.confirmed') }}</option>
                                                    <option value="0">{{ __('livestock.options.not_confirmed') }}</option>
                                                </select>

                                                <label class="small text-muted mb-1">{{ $isArabic ? 'تاريخ الفحص' : 'Check Date' }}</label>
                                                <input type="date" name="pregnancy_check_date" class="form-control form-control-sm mb-2" value="{{ now()->toDateString() }}" required>

                                                <label class="small text-muted mb-1">{{ __('livestock.fields.expected_delivery_date') }}</label>
                                                <input type="date" name="expected_delivery_date" class="form-control form-control-sm mb-2" value="{{ optional($cycle->expected_delivery_date)->toDateString() }}">

                                                <button class="btn btn-sm btn-info text-white w-100 font-weight-bold" type="submit">
                                                    {{ __('livestock.actions.save') }}
                                                </button>
                                            </form>
                                        </div>
                                    </div>

                                    {{-- 3. تسجيل واقعة الولادة والمولود --}}
                                    <div class="col-md-4">
                                        <div class="form-box shadow-sm h-100">
                                            <h6 class="font-weight-bold text-dark border-bottom pb-1 mb-2">
                                                <i class="fas fa-baby mr-1 text-success"></i> {{ __('livestock.sections.birth') }}
                                            </h6>
                                            <form method="POST" action="{{ route('customer.livestock.reproduction-cycles.birth', ['locale' => $currentLocale, 'cycle' => $cycle->id]) }}">
                                                @csrf
                                                <label class="small text-muted mb-1">{{ __('livestock.fields.birth_date') }}</label>
                                                <input type="date" name="birth_date" class="form-control form-control-sm mb-2" value="{{ now()->toDateString() }}" required>

                                                <label class="small text-muted mb-1">{{ __('livestock.fields.tag_number') }}</label>
                                                <input type="text" name="offspring[0][tag_number]" class="form-control form-control-sm mb-2" placeholder="TAG-NEW" required>

                                                <div class="row g-1 mb-2">
                                                    <div class="col-6">
                                                        <label class="small text-muted mb-1">{{ __('livestock.fields.species') }}</label>
                                                        <select name="offspring[0][species_id]" class="form-select form-select-sm" required>
                                                            @foreach ($femaleAnimals->pluck('species')->unique('id')->filter() as $species)
                                                                <option value="{{ $species->id }}">{{ $species->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-6">
                                                        <label class="small text-muted mb-1">{{ __('livestock.fields.gender') }}</label>
                                                        <select name="offspring[0][gender]" class="form-select form-select-sm" required>
                                                            <option value="male">{{ __('livestock.options.male') }}</option>
                                                            <option value="female">{{ __('livestock.options.female') }}</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <label class="small text-muted mb-1">{{ __('livestock.fields.weight') }} ({{ $isArabic ? 'كجم' : 'kg' }})</label>
                                                <input type="number" step="0.01" min="0" name="offspring[0][birth_weight]" class="form-control form-control-sm mb-2" placeholder="0.00">

                                                <button class="btn btn-sm btn-success w-100 font-weight-bold" type="submit">
                                                    {{ __('livestock.actions.record') }}
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-5 text-center text-muted">
                                <i class="fas fa-dna fa-2x mb-2 d-block text-secondary"></i>
                                {{ __('livestock.empty.no_cycles') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($rows->hasPages())
            <div class="card-footer bg-white border-top py-3">
                {{ $rows->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>

    {{-- كارت أحدث وقائع الولادات المسجلة --}}
    <div class="card-main">
        <div class="section-header">
            <h5 class="mb-0 font-weight-bold text-dark">
                <i class="fas fa-history mr-2 text-info"></i> {{ __('livestock.sections.recent_birth_events') }}
            </h5>
        </div>
        <div class="p-3">
            <div class="row g-2">
                @forelse ($recentBirths as $birth)
                    <div class="col-md-4 col-sm-6">
                        <div class="p-2 border rounded bg-white shadow-sm d-flex justify-content-between align-items-center">
                            <div>
                                <strong class="d-block font-monospace text-dark">
                                    <i class="fas fa-calendar-alt text-muted mr-1"></i> {{ optional($birth->birth_date)->toDateString() }}
                                </strong>
                                <span class="small text-muted">
                                    {{ __('livestock.fields.mother') }}: 
                                    @if($birth->mother)
                                        <a href="{{ route('customer.livestock.animals.show', ['locale' => $currentLocale, 'animal' => $birth->mother->id]) }}" class="tag-pill">
                                            {{ $birth->mother->tag_number }}
                                        </a>
                                    @else
                                        -
                                    @endif
                                </span>
                            </div>
                            <span class="badge bg-success"><i class="fas fa-check"></i></span>
                        </div>
                    </div>
                @empty
                    <div class="col-12 py-3 text-center text-muted">
                        {{ __('livestock.empty.no_birth_events') }}
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush