@extends('layouts.customer.dashboard')

@section('title', __('livestock.titles.reproduction_cycles'))
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/pages/livestock.css') }}">
@endpush

@section('content')
    <div class="container py-4 livestock-page">
        <h2 class="page-title mb-3">{{ __('livestock.titles.reproduction_cycles') }}</h2>

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
            <h5 class="section-title">{{ __('livestock.sections.open_new_cycle') }}</h5>
            <form method="POST" action="{{ route('customer.livestock.reproduction-cycles.store', ['locale' => $currentLocale]) }}"
                class="row g-2">
                @csrf
                <div class="col-md-4">
                    <label class="form-label">{{ __('livestock.fields.female_animal') }}</label>
                    <select name="female_animal_id" class="form-select" required>
                        @foreach ($femaleAnimals as $animal)
                            <option value="{{ $animal->id }}">{{ $animal->tag_number }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('livestock.fields.heat_date') }}</label>
                    <input type="date" name="heat_date" class="form-control" value="{{ now()->toDateString() }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('livestock.fields.insemination_type') }}</label>
                    <select name="insemination_type" class="form-select">
                        <option value="natural">{{ __('livestock.options.natural') }}</option>
                        <option value="artificial">{{ __('livestock.options.artificial') }}</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button class="btn btn-primary-green w-100" type="submit">{{ __('livestock.actions.open_cycle') }}</button>
                </div>
            </form>
        </div>

        <div class="table-container">
            <table class="table align-middle registry-table mb-0 js-livestock-table">
                <thead>
                    <tr>
                        <th>{{ __('livestock.fields.id') }}</th>
                        <th>{{ __('livestock.fields.female_animal') }}</th>
                        <th>{{ __('livestock.fields.status') }}</th>
                        <th>{{ __('livestock.fields.heat_date') }}</th>
                        <th>{{ __('livestock.sections.insemination') }}</th>
                        <th>{{ __('livestock.fields.expected_delivery_date') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rows as $cycle)
                        <tr>
                            <td>{{ $cycle->id }}</td>
                            <td>{{ $cycle->femaleAnimal->tag_number ?? '-' }}</td>
                            <td>{{ __('livestock.options.' . $cycle->status) }}</td>
                            <td>{{ optional($cycle->heat_date)->toDateString() ?? __('livestock.options.no_data') }}</td>
                            <td>{{ optional($cycle->insemination_date)->toDateString() ?? __('livestock.options.no_data') }}</td>
                            <td>{{ optional($cycle->expected_delivery_date)->toDateString() ?? __('livestock.options.no_data') }}</td>
                        </tr>
                        <tr>
                            <td colspan="6">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <form method="POST"
                                            action="{{ route('customer.livestock.reproduction-cycles.insemination', ['locale' => $currentLocale, 'cycle' => $cycle->id]) }}"
                                            class="border rounded p-2">
                                            @csrf
                                            <h6 class="mb-2">{{ __('livestock.sections.insemination') }}</h6>
                                            <input type="date" name="insemination_date" class="form-control mb-2"
                                                required>
                                            <select name="insemination_type" class="form-select mb-2" required>
                                                <option value="natural">{{ __('livestock.options.natural') }}</option>
                                                <option value="artificial">{{ __('livestock.options.artificial') }}</option>
                                            </select>
                                            <select name="male_animal_id" class="form-select mb-2">
                                                <option value="">{{ __('livestock.fields.male_animal_optional') }}</option>
                                                @foreach ($maleAnimals as $male)
                                                    <option value="{{ $male->id }}">{{ $male->tag_number }}</option>
                                                @endforeach
                                            </select>
                                            <button class="btn btn-sm btn-primary-green" type="submit">{{ __('livestock.actions.save') }}</button>
                                        </form>
                                    </div>
                                    <div class="col-md-4">
                                        <form method="POST"
                                            action="{{ route('customer.livestock.reproduction-cycles.pregnancy-check', ['locale' => $currentLocale, 'cycle' => $cycle->id]) }}"
                                            class="border rounded p-2">
                                            @csrf
                                            <h6 class="mb-2">{{ __('livestock.sections.pregnancy_check') }}</h6>
                                            <select name="pregnancy_confirmed" class="form-select mb-2" required>
                                                <option value="1">{{ __('livestock.options.confirmed') }}</option>
                                                <option value="0">{{ __('livestock.options.not_confirmed') }}</option>
                                            </select>
                                            <input type="date" name="pregnancy_check_date" class="form-control mb-2"
                                                required>
                                            <input type="date" name="expected_delivery_date" class="form-control mb-2">
                                            <button class="btn btn-sm btn-primary-green" type="submit">{{ __('livestock.actions.save') }}</button>
                                        </form>
                                    </div>
                                    <div class="col-md-4">
                                        <form method="POST"
                                            action="{{ route('customer.livestock.reproduction-cycles.birth', ['locale' => $currentLocale, 'cycle' => $cycle->id]) }}"
                                            class="border rounded p-2">
                                            @csrf
                                            <h6 class="mb-2">{{ __('livestock.sections.birth') }}</h6>
                                            <input type="date" name="birth_date" class="form-control mb-2" required>
                                            <input type="text" name="offspring[0][tag_number]" class="form-control mb-2"
                                                placeholder="{{ __('livestock.fields.tag_number') }}" required>
                                            <select name="offspring[0][species_id]" class="form-select mb-2" required>
                                                @foreach ($femaleAnimals->pluck('species')->unique('id')->filter() as $species)
                                                    <option value="{{ $species->id }}">{{ $species->name }}</option>
                                                @endforeach
                                            </select>
                                            <select name="offspring[0][gender]" class="form-select mb-2" required>
                                                <option value="male">{{ __('livestock.options.male') }}</option>
                                                <option value="female">{{ __('livestock.options.female') }}</option>
                                            </select>
                                            <input type="number" step="0.01" min="0" name="offspring[0][birth_weight]"
                                                class="form-control mb-2" placeholder="{{ __('livestock.fields.weight') }}">
                                            <button class="btn btn-sm btn-primary-green" type="submit">{{ __('livestock.actions.record') }}</button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">{{ __('livestock.empty.no_cycles') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $rows->links('pagination::bootstrap-5') }}
        </div>

        <div class="card-block mt-3">
            <h5>{{ __('livestock.sections.recent_birth_events') }}</h5>
            <ul class="mb-0">
                @forelse ($recentBirths as $birth)
                    <li>
                        {{ optional($birth->birth_date)->toDateString() }} - {{ __('livestock.fields.mother') }}:
                        {{ $birth->mother->tag_number ?? '-' }}
                    </li>
                @empty
                    <li>{{ __('livestock.empty.no_birth_events') }}</li>
                @endforelse
            </ul>
        </div>
    </div>
@endsection
