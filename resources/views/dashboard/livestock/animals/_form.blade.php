@php
    $isEdit = isset($animal);
@endphp

<div class="row g-3">
    <div class="col-md-4">
        <label class="form-label">{{ __('livestock.fields.tag_number') }}</label>
        <input type="text" name="tag_number" class="form-control" value="{{ old('tag_number', $animal->tag_number ?? '') }}"
            required>
    </div>
    <div class="col-md-4">
        <label class="form-label">{{ __('livestock.fields.species') }}</label>
        <select name="species_id" class="form-select" required>
            <option value="">{{ __('livestock.options.select_species') }}</option>
            @foreach ($species as $item)
                <option value="{{ $item->id }}" @selected(old('species_id', $animal->species_id ?? '') == $item->id)>{{ $item->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">{{ __('livestock.fields.breed') }}</label>
        <select name="breed_id" class="form-select">
            <option value="">{{ __('livestock.options.no_breed') }}</option>
            @foreach ($breeds as $item)
                <option value="{{ $item->id }}" @selected(old('breed_id', $animal->breed_id ?? '') == $item->id)>{{ $item->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-3">
        <label class="form-label">{{ __('livestock.fields.gender') }}</label>
        <select name="gender" class="form-select" required>
            <option value="male" @selected(old('gender', $animal->gender ?? '') === 'male')>{{ __('livestock.options.male') }}</option>
            <option value="female" @selected(old('gender', $animal->gender ?? '') === 'female')>{{ __('livestock.options.female') }}</option>
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label">{{ __('livestock.fields.source_type') }}</label>
        <select name="source_type" class="form-select" required>
            <option value="born" @selected(old('source_type', $animal->source_type ?? '') === 'born')>{{ __('livestock.options.born') }}</option>
            <option value="purchased" @selected(old('source_type', $animal->source_type ?? '') === 'purchased')>{{ __('livestock.options.purchased') }}</option>
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label">{{ __('livestock.fields.birth_date') }}</label>
        <input type="date" name="birth_date" class="form-control"
            value="{{ old('birth_date', optional($animal->birth_date ?? null)->toDateString()) }}">
    </div>
    <div class="col-md-3">
        <label class="form-label">{{ __('livestock.fields.purchase_date') }}</label>
        <input type="date" name="purchase_date" class="form-control"
            value="{{ old('purchase_date', optional($animal->purchase_date ?? null)->toDateString()) }}">
    </div>

    <div class="col-md-3">
        <label class="form-label">{{ __('livestock.fields.purchase_price') }}</label>
        <input type="number" step="0.01" min="0" name="purchase_price" class="form-control"
            value="{{ old('purchase_price', $animal->purchase_price ?? '') }}">
    </div>
    <div class="col-md-3">
        <label class="form-label">{{ __('livestock.fields.status') }}</label>
        <select name="status" class="form-select">
            @foreach (['active', 'sold', 'dead', 'slaughtered'] as $status)
                <option value="{{ $status }}" @selected(old('status', $animal->status ?? 'active') === $status)>{{ __('livestock.options.' . $status) }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label">{{ __('livestock.fields.health_status') }}</label>
        <select name="health_status" class="form-select">
            @foreach (['healthy', 'under_treatment', 'quarantined'] as $status)
                <option value="{{ $status }}" @selected(old('health_status', $animal->health_status ?? 'healthy') === $status)>
                    {{ __('livestock.options.' . $status) }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label">{{ __('livestock.fields.initial_weight_optional') }}</label>
        <input type="number" step="0.01" min="0" name="initial_weight" class="form-control"
            value="{{ old('initial_weight') }}" @disabled($isEdit)>
    </div>

    <div class="col-md-6">
        <label class="form-label">{{ __('livestock.fields.mother') }}</label>
        <select name="mother_id" class="form-select">
            <option value="">{{ __('livestock.options.none') }}</option>
            @foreach ($animals as $item)
                <option value="{{ $item->id }}" @selected(old('mother_id', $animal->mother_id ?? '') == $item->id)>
                    {{ $item->tag_number }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label">{{ __('livestock.fields.father') }}</label>
        <select name="father_id" class="form-select">
            <option value="">{{ __('livestock.options.none') }}</option>
            @foreach ($animals as $item)
                <option value="{{ $item->id }}" @selected(old('father_id', $animal->father_id ?? '') == $item->id)>
                    {{ $item->tag_number }}
                </option>
            @endforeach
        </select>
    </div>

    @unless ($isEdit)
        <div class="col-md-4">
            <div class="form-check mt-4">
                <input class="form-check-input" type="checkbox" value="1" name="capture_birth_event" id="captureBirth"
                    @checked(old('capture_birth_event'))>
                <label class="form-check-label" for="captureBirth">
                    {{ __('livestock.messages.capture_birth_event') }}
                </label>
            </div>
        </div>
    @endunless

    <div class="col-12">
        <label class="form-label">{{ __('livestock.fields.notes') }}</label>
        <textarea name="notes" class="form-control" rows="3">{{ old('notes', $animal->notes ?? '') }}</textarea>
    </div>
</div>
