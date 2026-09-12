@extends('layouts.customer.dashboard')

@section('title', __('livestock.titles.animal_profile'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/pages/livestock.css') }}">
@endpush

@section('content')
@php
    $currentLocale = request()->route('locale') ?? app()->getLocale();
@endphp

<div class="container py-4 livestock-page">
    <div class="page-head">
        <h2 class="page-title">{{ __('livestock.titles.animal_profile') }} {{ $animal->tag_number }}</h2>
        <div class="quick-actions d-flex gap-2">
            <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#transferAnimalModal" data-bs-toggle="modal" data-bs-target="#transferAnimalModal">
                <i class="fas fa-exchange-alt mr-1"></i> {{ __('livestock.actions.transfer_pen') }}
            </button>
            <a class="btn btn-outline-white"
                href="{{ route('customer.livestock.animals.edit', ['locale' => $currentLocale, 'animal' => $animal->id]) }}">{{ __('livestock.actions.edit') }}</a>
            <a class="btn btn-outline-white"
                href="{{ route('customer.livestock.animals.index', ['locale' => $currentLocale]) }}">{{ __('livestock.actions.back_to_list') }}</a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card-block mb-3">
        <div class="row">
            <div class="col-md-3"><strong>{{ __('livestock.fields.species') }}:</strong> {{ $animal->species->name ?? __('livestock.options.no_data') }}</div>
            <div class="col-md-3"><strong>{{ __('livestock.fields.breed') }}:</strong> {{ $animal->breed->name ?? __('livestock.options.no_data') }}</div>
            <div class="col-md-3"><strong>{{ __('farms.fields.farm') }}:</strong> {{ $animal->pen?->farm?->name ?? __('livestock.options.no_data') }}</div>
            <div class="col-md-3"><strong>{{ __('farms.fields.pen') }}:</strong> {{ $animal->pen?->pen_number ?? __('livestock.options.no_data') }}</div>
        </div>
        <div class="row mt-2">
            <div class="col-md-3"><strong>{{ __('livestock.fields.gender') }}:</strong> {{ __('livestock.options.' . $animal->gender) }}</div>
            <div class="col-md-3"><strong>{{ __('livestock.fields.status') }}:</strong> {{ __('livestock.options.' . $animal->status) }}</div>
            <div class="col-md-3"><strong>{{ __('livestock.fields.health') }}:</strong> {{ __('livestock.options.' . $animal->health_status) }}</div>
            <div class="col-md-3"><strong>{{ __('livestock.fields.intended_purpose') }}:</strong> {{ $animal->intended_purpose ? __('livestock.options.' . $animal->intended_purpose) : __('livestock.options.no_data') }}</div>
        </div>
        <div class="row mt-2">
            <div class="col-md-3"><strong>{{ __('livestock.fields.birth_date') }}:</strong> {{ optional($animal->birth_date)->toDateString() ?? __('livestock.options.no_data') }}</div>
            <div class="col-md-3"><strong>{{ __('livestock.fields.mother') }}:</strong> {{ $animal->mother->tag_number ?? __('livestock.options.no_data') }}</div>
            <div class="col-md-3"><strong>{{ __('livestock.fields.father') }}:</strong> {{ $animal->father->tag_number ?? __('livestock.options.no_data') }}</div>
        </div>
    </div>

    <div class="card-block mb-3">
        <h5>{{ __('livestock.sections.change_status') }}</h5>
        <form method="POST"
            action="{{ route('customer.livestock.animals.status.change', ['locale' => $currentLocale, 'animal' => $animal->id]) }}"
            class="row g-2">
            @csrf
            <div class="col-md-3">
                <select name="status" class="form-select" required>
                    @foreach (['active', 'sold', 'dead', 'slaughtered'] as $status)
                        <option value="{{ $status }}" @selected($animal->status === $status)>{{ __('livestock.options.' . $status) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <input class="form-control" name="reason" placeholder="{{ __('livestock.fields.reason') }}">
            </div>
            <div class="col-md-3">
                <button class="btn btn-primary-green w-100" type="submit">{{ __('livestock.actions.change_status') }}</button>
            </div>
        </form>
    </div>

    <div class="row g-3">
        {{-- كارت تسجيل التغذية وعرض السجلات --}}
        <div class="col-md-6">
            <div class="card-block h-100">
                <h5>{{ __('livestock.sections.record_feeding') }}</h5>
                <form method="POST" action="{{ route('customer.livestock.feeding-logs.store', ['locale' => $currentLocale]) }}"
                    class="row g-2">
                    @csrf
                    <input type="hidden" name="animal_id" value="{{ $animal->id }}">
                    <div class="col-md-6">
                        <label class="form-label">{{ __('livestock.fields.feed_type') }}</label>
                        <select name="feed_type_id" class="form-select" required>
                            @foreach ($feedTypes as $feedType)
                                <option value="{{ $feedType->id }}">{{ $feedType->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('livestock.fields.feeding_date') }}</label>
                        <input type="date" name="feeding_date" class="form-control" value="{{ now()->toDateString() }}"
                            required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('livestock.fields.quantity') }}</label>
                        <input type="number" step="0.01" min="0.01" name="quantity" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('livestock.fields.unit_cost_optional') }}</label>
                        <input type="number" step="0.01" min="0" name="unit_cost" class="form-control">
                    </div>
                    <div class="col-12">
                        <button class="btn btn-primary-green" type="submit">{{ __('livestock.actions.save_feeding') }}</button>
                    </div>
                </form>

                {{-- جدول آخر سجلات التغذية --}}
                <div class="mt-3 pt-3 border-top">
                    <h6 class="font-weight-bold mb-2 text-muted" style="font-size: 0.85rem;">
                        <i class="fas fa-history mr-1"></i> {{ __('livestock.sections.recent_feeding_logs') ?? 'آخر سجلات التغذية' }}
                    </h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered text-center mb-0" style="font-size: 0.8rem;">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('livestock.fields.date') }}</th>
                                    <th>{{ __('livestock.fields.feed_type') }}</th>
                                    <th>{{ __('livestock.fields.quantity') }}</th>
                                    <th>{{ __('livestock.fields.cost') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($animal->feedingLogs->take(5) as $fLog)
                                    <tr>
                                        <td>{{ optional($fLog->feeding_date)->format('Y-m-d') ?? \Carbon\Carbon::parse($fLog->feeding_date)->format('Y-m-d') }}</td>
                                        <td>{{ $fLog->feedType->name ?? '-' }}</td>
                                        <td>{{ $fLog->quantity }}</td>
                                        <td>{{ $fLog->total_cost ? number_format($fLog->total_cost, 2) : '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-muted">{{ __('livestock.empty.no_feed_types') ?? 'لا توجد سجلات تغذية مسجلة' }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

      {{-- كارت تسجيل إنتاج الحليب وعرض السجلات --}}
        <div class="col-md-6">
            <div class="card-block h-100">
                <h5>{{ __('livestock.sections.record_milk') }}</h5>
                <form method="POST"
                    action="{{ route('customer.livestock.milk-production-logs.store', ['locale' => $currentLocale]) }}"
                    class="row g-2">
                    @csrf
                    <input type="hidden" name="animal_id" value="{{ $animal->id }}">
                    <div class="col-md-6">
                        <label class="form-label">{{ __('livestock.fields.date') }}</label>
                        <input type="date" name="production_date" class="form-control"
                            value="{{ now()->toDateString() }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('livestock.fields.quantity_liters') }}</label>
                        <input type="number" step="0.01" min="0.01" name="quantity_liters" class="form-control"
                            required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('livestock.fields.fat_percentage') }}</label>
                        <input type="number" step="0.01" min="0" max="100" name="fat_percentage"
                            class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('livestock.fields.quality_grade') }}</label>
                        <input type="text" name="quality_grade" class="form-control">
                    </div>
                    <div class="col-12">
                        <button class="btn btn-primary-green" type="submit">{{ __('livestock.actions.save_milk') }}</button>
                    </div>
                </form>

                {{-- جدول آخر سجلات الحليب --}}
                <div class="mt-3 pt-3 border-top">
                    <h6 class="font-weight-bold mb-2 text-muted" style="font-size: 0.85rem;">
                        <i class="fas fa-wine-bottle mr-1"></i> {{ __('livestock.sections.recent_milk_logs') ?? 'آخر سجلات الحليب' }}
                    </h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered text-center mb-0" style="font-size: 0.8rem;">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('livestock.fields.date') }}</th>
                                    <th>{{ __('livestock.fields.quantity_liters') }}</th>
                                    <th>{{ __('livestock.fields.fat_percentage') }}</th>
                                    <th>{{ __('livestock.fields.quality_grade') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($animal->milkProductionLogs->sortByDesc('production_date')->take(5) as $mLog)
                                    <tr>
                                        <td>{{ optional($mLog->production_date)->format('Y-m-d') ?? \Carbon\Carbon::parse($mLog->production_date)->format('Y-m-d') }}</td>
                                        <td>{{ number_format($mLog->quantity_liters, 2) }}</td>
                                        <td>{{ $mLog->fat_percentage !== null ? $mLog->fat_percentage . '%' : '-' }}</td>
                                        <td>{{ $mLog->quality_grade ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-muted">{{ __('livestock.empty.no_milk_logs') ?? 'لا توجد سجلات إنتاج حليب' }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    {{-- كارت تسجيل حدث صحي وعرض السجلات --}}
        <div class="col-md-6">
            <div class="card-block h-100">
                <h5>{{ __('livestock.sections.record_health') }}</h5>
                <form method="POST"
                    action="{{ route('customer.livestock.health-records.store', ['locale' => $currentLocale]) }}"
                    class="row g-2">
                    @csrf
                    <input type="hidden" name="animal_id" value="{{ $animal->id }}">
                    <div class="col-md-6">
                        <label class="form-label">{{ __('livestock.fields.type') }}</label>
                        <select name="record_type" class="form-select" required>
                            <option value="checkup">{{ __('livestock.options.checkup') }}</option>
                            <option value="illness">{{ __('livestock.options.illness') }}</option>
                            <option value="injury">{{ __('livestock.options.injury') }}</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('livestock.fields.cost') }}</label>
                        <input type="number" step="0.01" min="0" name="cost" class="form-control">
                    </div>
                    <div class="col-12">
                        <label class="form-label">{{ __('livestock.fields.diagnosis') }}</label>
                        <textarea name="diagnosis" class="form-control" rows="2" required></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label">{{ __('livestock.fields.treatment') }}</label>
                        <textarea name="treatment" class="form-control" rows="2" required></textarea>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">{{ __('livestock.fields.next_followup_date') }}</label>
                        <input type="date" name="next_followup_date" class="form-control">
                    </div>
                    <div class="col-md-4 d-flex align-items-center">
                        <div class="form-check mt-4">
                            <input class="form-check-input" type="checkbox" name="set_animal_under_treatment"
                                value="1" id="underTreatment">
                            <label class="form-check-label" for="underTreatment">
                                {{ __('livestock.fields.mark_under_treatment') }}
                            </label>
                        </div>
                    </div>
                    <div class="col-12">
                        <button class="btn btn-primary-green" type="submit">{{ __('livestock.actions.save_health') }}</button>
                    </div>
                </form>

                {{-- جدول آخر السجلات الصحية --}}
                <div class="mt-3 pt-3 border-top">
                    <h6 class="font-weight-bold mb-2 text-muted" style="font-size: 0.85rem;">
                        <i class="fas fa-notes-medical mr-1"></i> {{ __('livestock.sections.recent_health_records') ?? 'آخر السجلات الصحية' }}
                    </h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered text-center mb-0" style="font-size: 0.8rem;">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('livestock.fields.type') }}</th>
                                    <th>{{ __('livestock.fields.diagnosis') }}</th>
                                    <th>{{ __('livestock.fields.cost') }}</th>
                                    <th>{{ __('livestock.fields.date') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($animal->healthRecords->take(5) as $hRecord)
                                    <tr>
                                        <td>{{ __('livestock.options.' . $hRecord->record_type) ?? $hRecord->record_type }}</td>
                                        <td>{{ Str::limit($hRecord->diagnosis, 25) }}</td>
                                        <td>{{ $hRecord->cost ? number_format($hRecord->cost, 2) : '-' }}</td>
                                        <td>{{ $hRecord->created_at ? $hRecord->created_at->format('Y-m-d') : '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-muted">{{ __('livestock.empty.no_health_records') ?? 'لا توجد سجلات صحية مسجلة' }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

      {{-- كارت تسجيل تطعيم وعرض السجلات --}}
        <div class="col-md-6">
            <div class="card-block h-100">
                <h5>{{ __('livestock.sections.record_vaccination') }}</h5>
                <form method="POST"
                    action="{{ route('customer.livestock.vaccinations.store', ['locale' => $currentLocale]) }}"
                    class="row g-2">
                    @csrf
                    <input type="hidden" name="animal_id" value="{{ $animal->id }}">
                    <div class="col-md-6">
                        <label class="form-label">{{ __('livestock.fields.vaccine') }}</label>
                        <select name="vaccine_id" class="form-select" required>
                            @foreach ($vaccines as $vaccine)
                                <option value="{{ $vaccine->id }}">{{ $vaccine->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('livestock.fields.dose_number') }}</label>
                        <input type="number" min="1" name="dose_number" class="form-control" value="1" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('livestock.fields.vaccination_date') }}</label>
                        <input type="date" name="vaccination_date" class="form-control"
                            value="{{ now()->toDateString() }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('livestock.fields.next_due_date_optional') }}</label>
                        <input type="date" name="next_due_date" class="form-control">
                    </div>
                    <div class="col-12">
                        <button class="btn btn-primary-green" type="submit">{{ __('livestock.actions.save_vaccination') }}</button>
                    </div>
                </form>

                {{-- جدول آخر التطعيمات --}}
                <div class="mt-3 pt-3 border-top">
                    <h6 class="font-weight-bold mb-2 text-muted" style="font-size: 0.85rem;">
                        <i class="fas fa-syringe mr-1"></i> {{ __('livestock.sections.recent_vaccinations') }}
                    </h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered text-center mb-0" style="font-size: 0.8rem;">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('livestock.fields.vaccine') }}</th>
                                    <th>{{ __('livestock.fields.dose_number') }}</th>
                                    <th>{{ __('livestock.fields.vaccination_date') }}</th>
                                    <th>{{ __('livestock.fields.next_due_date_optional') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($animal->vaccinations->take(5) as $vac)
                                    <tr>
                                        <td>{{ $vac->vaccine->name ?? '-' }}</td>
                                        <td>{{ $vac->dose_number }}</td>
                                        <td>{{ optional($vac->vaccination_date)->format('Y-m-d') ?? \Carbon\Carbon::parse($vac->vaccination_date)->format('Y-m-d') }}</td>
                                        <td>
                                            @if($vac->next_due_date)
                                                <span class="badge bg-light text-dark border">
                                                    {{ \Carbon\Carbon::parse($vac->next_due_date)->format('Y-m-d') }}
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-muted">{{ __('livestock.empty.no_vaccines') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
{{-- كارت تسجيل الوزن وعرض السجلات --}}
        <div class="col-md-6">
            <div class="card-block h-100">
                <h5>{{ __('livestock.sections.record_weight') }}</h5>
                <form method="POST" action="{{ route('customer.livestock.weight-logs.store', ['locale' => $currentLocale]) }}"
                    class="row g-2">
                    @csrf
                    <input type="hidden" name="animal_id" value="{{ $animal->id }}">
                    <div class="col-md-6">
                        <label class="form-label">{{ __('livestock.fields.recorded_at') }}</label>
                        <input type="datetime-local" name="recorded_at" class="form-control"
                            value="{{ now()->format('Y-m-d\TH:i') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('livestock.fields.weight') }} (كجم)</label>
                        <input type="number" step="0.01" min="0.01" name="weight" class="form-control" required>
                    </div>
                    <div class="col-12">
                        <button class="btn btn-primary-green" type="submit">{{ __('livestock.actions.save_weight') }}</button>
                    </div>
                </form>

                {{-- جدول آخر قياسات الوزن --}}
                <div class="mt-3 pt-3 border-top">
                    <h6 class="font-weight-bold mb-2 text-muted" style="font-size: 0.85rem;">
                        <i class="fas fa-weight mr-1"></i> {{ __('livestock.sections.recent_weight_logs') ?? 'آخر قياسات الوزن' }}
                    </h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered text-center mb-0" style="font-size: 0.8rem;">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('livestock.fields.recorded_at') }}</th>
                                    <th>{{ __('livestock.fields.weight') }} (كجم)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($animal->weightLogs->sortByDesc('recorded_at')->take(5) as $wLog)
                                    <tr>
                                        <td>{{ optional($wLog->recorded_at)->format('Y-m-d H:i') ?? \Carbon\Carbon::parse($wLog->recorded_at)->format('Y-m-d H:i') }}</td>
                                        <td class="font-weight-bold">{{ number_format($wLog->weight, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-muted">{{ __('livestock.empty.no_weight_logs') ?? 'لا توجد قياسات مسجلة للوزن' }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- سجل الحالات --}}
    <div class="card-block mt-3">
        <h5>{{ __('livestock.sections.status_history') }}</h5>
        <div class="table-responsive">
            <table class="table js-livestock-table">
                <thead>
                    <tr>
                        <th>{{ __('livestock.fields.old') }}</th>
                        <th>{{ __('livestock.fields.new') }}</th>
                        <th>{{ __('livestock.fields.reason') }}</th>
                        <th>{{ __('livestock.fields.changed_at') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($animal->statusHistory as $history)
                        <tr>
                            <td>{{ __('livestock.options.' . $history->old_status) }}</td>
                            <td>{{ __('livestock.options.' . $history->new_status) }}</td>
                            <td>{{ $history->change_reason ?? __('livestock.options.no_data') }}</td>
                            <td>{{ optional($history->changed_at)->toDateTimeString() ?? __('livestock.options.no_data') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">{{ __('livestock.empty.no_status_changes') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- نافذة نقل الحيوان إلى حظيرة أخرى --}}
<div class="modal fade" id="transferAnimalModal" tabindex="-1" role="dialog" aria-labelledby="transferModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form action="{{ route('customer.livestock.animals.transfer', ['locale' => $currentLocale, 'animal' => $animal->id]) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold" id="transferModalLabel">
                        <i class="fas fa-exchange-alt mr-1 text-warning"></i> {{ __('livestock.actions.transfer_pen') }}
                    </h5>
                    <button type="button" class="close btn-close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info py-2">
                        <small>
                            {{ __('livestock.fields.current_pen') }}: 
                            <strong>{{ $animal->pen ? $animal->pen->pen_number . ' (' . ($animal->pen->name ?? '-') . ')' : 'غير محدد' }}</strong>
                        </small>
                    </div>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold">{{ __('livestock.fields.destination_pen') }} <span class="text-danger">*</span></label>
                        <select name="to_pen_id" class="form-select" required>
                            <option value="">-- {{ __('livestock.placeholders.select_pen') }} --</option>
                            @php
                                $availablePens = \App\Models\FarmPen::where('tenant_id', auth()->user()->tenant_id)
                                    ->where('id', '!=', $animal->pen_id)
                                    ->orderBy('pen_number')
                                    ->get();
                            @endphp
                            @foreach($availablePens as $targetPen)
                                <option value="{{ $targetPen->id }}">
                                    {{ $targetPen->pen_number }} - {{ $targetPen->name ?? $targetPen->farm->name ?? '' }} ({{ $targetPen->current_count }}/{{ $targetPen->capacity }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-0">
                        <label class="font-weight-bold">{{ __('livestock.fields.notes') }}</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="{{ __('livestock.placeholders.notes') }}"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal">{{ __('livestock.actions.cancel') }}</button>
                    <button type="submit" class="btn btn-warning font-weight-bold">{{ __('livestock.actions.confirm_transfer') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection