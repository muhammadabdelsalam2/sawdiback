@extends('layouts.customer.dashboard')

@section('title', __('livestock.titles.animal_profile'))
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/pages/livestock.css') }}">
@endpush

@section('content')
    <div class="container py-4 livestock-page">
        <div class="page-head">
            <h2 class="page-title">{{ __('livestock.titles.animal_profile') }} {{ $animal->tag_number }}</h2>
            <div class="quick-actions">
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
                <div class="col-md-3"><strong>{{ __('livestock.fields.gender') }}:</strong> {{ __('livestock.options.' . $animal->gender) }}</div>
                <div class="col-md-3"><strong>{{ __('livestock.fields.status') }}:</strong> {{ __('livestock.options.' . $animal->status) }}</div>
            </div>
            <div class="row mt-2">
                <div class="col-md-3"><strong>{{ __('livestock.fields.health') }}:</strong> {{ __('livestock.options.' . $animal->health_status) }}</div>
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
                </div>
            </div>

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
                </div>
            </div>

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
                </div>
            </div>

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
                </div>
            </div>

            <div class="col-md-6">
                <div class="card-block">
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
                            <label class="form-label">{{ __('livestock.fields.weight') }}</label>
                            <input type="number" step="0.01" min="0.01" name="weight" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <button class="btn btn-primary-green" type="submit">{{ __('livestock.actions.save_weight') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

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
@endsection
