@php
    $isEdit = isset($animal);
    $currentLocale = request()->route('locale') ?? app()->getLocale();
    $isArabic = str_starts_with(strtolower($currentLocale), 'ar');
@endphp

<div class="row g-3">
    {{-- رقم الشريحة / المعرّف --}}
    <div class="col-md-4">
        <label class="form-label font-weight-bold small text-muted">
            {{ __('livestock.fields.tag_number') }} <span class="text-danger">*</span>
        </label>
        <input type="text" name="tag_number" class="form-control" 
               value="{{ old('tag_number', $animal->tag_number ?? '') }}" 
               placeholder="مثال: TAG-0012" required>
    </div>

    {{-- الفصيلة / النوع --}}
    <div class="col-md-4">
        <label class="form-label font-weight-bold small text-muted">
            {{ __('livestock.fields.species') }} <span class="text-danger">*</span>
        </label>
        <select name="species_id" class="form-select" required>
            <option value="">-- {{ __('livestock.options.select_species') }} --</option>
            @foreach ($species as $item)
                <option value="{{ $item->id }}" @selected(old('species_id', $animal->species_id ?? '') == $item->id)>
                    {{ $item->name }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- السلالة --}}
    <div class="col-md-4">
        <label class="form-label font-weight-bold small text-muted">
            {{ __('livestock.fields.breed') }}
        </label>
        <select name="breed_id" class="form-select">
            <option value="">-- {{ __('livestock.options.no_breed') }} --</option>
            @foreach ($breeds as $item)
                <option value="{{ $item->id }}" @selected(old('breed_id', $animal->breed_id ?? '') == $item->id)>
                    {{ $item->name }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- الحظيرة التابعة مع النوع --}}
    <div class="col-md-4">
        <label class="form-label font-weight-bold small text-muted">
            {{ __('farms.fields.pen') }}
        </label>
        <select name="pen_id" class="form-select">
            <option value="">-- {{ $isArabic ? 'بدون حظيرة (اختياري)' : 'No Pen (Optional)' }} --</option>
            @foreach ($pens ?? [] as $pen)
                <option value="{{ $pen->id }}" @selected(old('pen_id', $animal->pen_id ?? '') == $pen->id)>
                    {{ $pen->farm?->name ?? 'مزرعة' }} - {{ $pen->pen_number }} 
                    ({{ __('farms.pen_types.' . $pen->type) != 'farms.pen_types.' . $pen->type ? __('farms.pen_types.' . $pen->type) : $pen->type }})
                </option>
            @endforeach
        </select>
    </div>

    {{-- الجنس --}}
    <div class="col-md-4">
        <label class="form-label font-weight-bold small text-muted">
            {{ __('livestock.fields.gender') }} <span class="text-danger">*</span>
        </label>
        <select name="gender" class="form-select" required>
            <option value="male" @selected(old('gender', $animal->gender ?? '') === 'male')>{{ __('livestock.options.male') }}</option>
            <option value="female" @selected(old('gender', $animal->gender ?? '') === 'female')>{{ __('livestock.options.female') }}</option>
        </select>
    </div>

    {{-- مصدر الحيوان --}}
    <div class="col-md-4">
        <label class="form-label font-weight-bold small text-muted">
            {{ __('livestock.fields.source_type') }} <span class="text-danger">*</span>
        </label>
        <select name="source_type" class="form-select" required>
            <option value="born" @selected(old('source_type', $animal->source_type ?? '') === 'born')>{{ __('livestock.options.born') }}</option>
            <option value="purchased" @selected(old('source_type', $animal->source_type ?? '') === 'purchased')>{{ __('livestock.options.purchased') }}</option>
        </select>
    </div>

    {{-- تاريخ الولادة --}}
    <div class="col-md-3">
        <label class="form-label font-weight-bold small text-muted">
            {{ __('livestock.fields.birth_date') }}
        </label>
        <input type="date" name="birth_date" class="form-control"
            value="{{ old('birth_date', optional($animal->birth_date ?? null)->toDateString()) }}">
    </div>

    {{-- تاريخ الشراء --}}
    <div class="col-md-3">
        <label class="form-label font-weight-bold small text-muted">
            {{ __('livestock.fields.purchase_date') }}
        </label>
        <input type="date" name="purchase_date" class="form-control"
            value="{{ old('purchase_date', optional($animal->purchase_date ?? null)->toDateString()) }}">
    </div>

    {{-- سعر الشراء --}}
    <div class="col-md-3">
        <label class="form-label font-weight-bold small text-muted">
            {{ __('livestock.fields.purchase_price') }}
        </label>
        <input type="number" step="0.01" min="0" name="purchase_price" class="form-control"
            value="{{ old('purchase_price', $animal->purchase_price ?? '') }}" placeholder="0.00">
    </div>

    {{-- الحالة العامة --}}
    <div class="col-md-3">
        <label class="form-label font-weight-bold small text-muted">
            {{ __('livestock.fields.status') }}
        </label>
        <select name="status" class="form-select">
            @foreach (['active', 'sold', 'dead', 'slaughtered'] as $status)
                <option value="{{ $status }}" @selected(old('status', $animal->status ?? 'active') === $status)>
                    {{ __('livestock.options.' . $status) }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- الحالة الصحية --}}
    <div class="col-md-4">
        <label class="form-label font-weight-bold small text-muted">
            {{ __('livestock.fields.health_status') }}
        </label>
        <select name="health_status" class="form-select">
            @foreach (['healthy', 'under_treatment', 'quarantined'] as $status)
                <option value="{{ $status }}" @selected(old('health_status', $animal->health_status ?? 'healthy') === $status)>
                    {{ __('livestock.options.' . $status) }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- الغرض الإنتاجي --}}
    <div class="col-md-4">
        <label class="form-label font-weight-bold small text-muted">
            {{ __('livestock.fields.intended_purpose') }}
        </label>
        <select name="intended_purpose" class="form-select">
            <option value="">-- {{ __('livestock.options.none') }} --</option>
            @foreach (['milk', 'breeding', 'sale'] as $purpose)
                <option value="{{ $purpose }}" @selected(old('intended_purpose', $animal->intended_purpose ?? '') === $purpose)>
                    {{ __('livestock.options.' . $purpose) }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- الوزن المبدئي (للإضافة فقط) --}}
    <div class="col-md-4">
        <label class="form-label font-weight-bold small text-muted">
            {{ __('livestock.fields.initial_weight_optional') }} ({{ $isArabic ? 'كجم' : 'kg' }})
        </label>
        <input type="number" step="0.01" min="0" name="initial_weight" class="form-control"
            value="{{ old('initial_weight') }}" placeholder="0.00" @disabled($isEdit)>
    </div>

    {{-- الأم --}}
    <div class="col-md-6">
        <label class="form-label font-weight-bold small text-muted">
            {{ __('livestock.fields.mother') }}
        </label>
        <select name="mother_id" class="form-select">
            <option value="">-- {{ __('livestock.options.none') }} --</option>
            @foreach ($animals as $item)
                <option value="{{ $item->id }}" @selected(old('mother_id', $animal->mother_id ?? '') == $item->id)>
                    {{ $item->tag_number }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- الأب --}}
    <div class="col-md-6">
        <label class="form-label font-weight-bold small text-muted">
            {{ __('livestock.fields.father') }}
        </label>
        <select name="father_id" class="form-select">
            <option value="">-- {{ __('livestock.options.none') }} --</option>
            @foreach ($animals as $item)
                <option value="{{ $item->id }}" @selected(old('father_id', $animal->father_id ?? '') == $item->id)>
                    {{ $item->tag_number }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- تسجيل واقعة الولادة تلقائياً --}}
    @unless ($isEdit)
        <div class="col-12">
            <div class="form-check p-3 bg-light rounded border">
                <input class="form-check-input ms-0 me-2" type="checkbox" value="1" name="capture_birth_event" id="captureBirth"
                    @checked(old('capture_birth_event'))>
                <label class="form-check-label font-weight-bold text-dark" for="captureBirth">
                    {{ __('livestock.messages.capture_birth_event') }}
                </label>
            </div>
        </div>
    @endunless

    {{-- ملاحظات --}}
    <div class="col-12">
        <label class="form-label font-weight-bold small text-muted">
            {{ __('livestock.fields.notes') }}
        </label>
        <textarea name="notes" class="form-control" rows="3" placeholder="{{ $isArabic ? 'أي ملاحظات إضافية عن الحيوان...' : 'Any additional notes...' }}">{{ old('notes', $animal->notes ?? '') }}</textarea>
    </div>
</div>