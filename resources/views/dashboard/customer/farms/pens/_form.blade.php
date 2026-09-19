@csrf

@php
    $isArabic = str_starts_with(app()->getLocale(), 'ar');

    $penTypes = [
        'goat'    => $isArabic ? 'ماعز' : 'Goats',
        'cattle'  => $isArabic ? 'البقر' : 'Cattle / Cows',
        'poultry' => $isArabic ? 'الدواجن' : 'Poultry',
        'fish'    => $isArabic ? 'أسماك' : 'Fish',
        'rabbit'  => $isArabic ? 'أرانب' : 'Rabbits',
        'other'   => $isArabic ? 'أخرى' : 'Other',
    ];
@endphp

<div class="row g-3">
    {{-- اختيار المزرعة --}}
    <div class="col-md-4">
        <label class="form-label font-weight-bold">
            {{ __('farms.fields.farm') !== 'farms.fields.farm' ? __('farms.fields.farm') : ($isArabic ? 'المزرعة' : 'Farm') }}
            <span class="text-danger">*</span>
        </label>
        <select name="farm_id" class="form-select @error('farm_id') is-invalid @enderror" required>
            <option value="">{{ $isArabic ? '-- اختر المزرعة --' : '-- Select Farm --' }}</option>
            @foreach($farms as $farmOption)
                <option value="{{ $farmOption->id }}" @selected(old('farm_id', $pen->farm_id ?? request('farm_id')) == $farmOption->id)>
                    {{ $farmOption->name }}
                </option>
            @endforeach
        </select>
        @error('farm_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- رقم / رمز الحظيرة --}}
    <div class="col-md-4">
        <label class="form-label font-weight-bold">
            {{ __('farms.fields.pen_number') !== 'farms.fields.pen_number' ? __('farms.fields.pen_number') : ($isArabic ? 'رقم / كود الحظيرة' : 'Pen Number / Code') }}
            <span class="text-danger">*</span>
        </label>
        <input type="text"
               name="pen_number"
               class="form-control @error('pen_number') is-invalid @enderror"
               value="{{ old('pen_number', $pen->pen_number ?? '') }}"
               placeholder="{{ $isArabic ? 'مثال: حظيرة 1' : 'e.g., Pen 01' }}"
               required>
        @error('pen_number')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- نوع الحظيرة (الفئات الست المعتمدة) --}}
    <div class="col-md-4">
        <label class="form-label font-weight-bold">
            {{ __('farms.fields.type') !== 'farms.fields.type' ? __('farms.fields.type') : ($isArabic ? 'نوع الحظيرة' : 'Pen Type') }}
            <span class="text-danger">*</span>
        </label>
        <select name="type" class="form-select @error('type') is-invalid @enderror" required>
            <option value="">{{ $isArabic ? '-- اختر النوع --' : '-- Select Type --' }}</option>
            @foreach($penTypes as $typeKey => $typeLabel)
                <option value="{{ $typeKey }}" @selected(old('type', $pen->type ?? '') === $typeKey)>
                    {{ $typeLabel }}
                </option>
            @endforeach
        </select>
        @error('type')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- الطاقة الاستيعابية --}}
    <div class="col-md-4">
        <label class="form-label font-weight-bold">
            {{ __('farms.fields.capacity') !== 'farms.fields.capacity' ? __('farms.fields.capacity') : ($isArabic ? 'الطاقة الاستيعابية' : 'Capacity') }}
        </label>
        <input type="number"
               name="capacity"
               min="0"
               class="form-control @error('capacity') is-invalid @enderror"
               value="{{ old('capacity', $pen->capacity ?? '') }}"
               placeholder="0">
        @error('capacity')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- العدد الحالي --}}
    <div class="col-md-4">
        <label class="form-label font-weight-bold">
            {{ __('farms.fields.current_count') !== 'farms.fields.current_count' ? __('farms.fields.current_count') : ($isArabic ? 'العدد الحالي' : 'Current Count') }}
        </label>
        <input type="number"
               name="current_count"
               min="0"
               class="form-control @error('current_count') is-invalid @enderror"
               value="{{ old('current_count', $pen->current_count ?? 0) }}"
               placeholder="0">
        @error('current_count')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- ملاحظات --}}
    <div class="col-12">
        <label class="form-label font-weight-bold">
            {{ __('farms.fields.notes') !== 'farms.fields.notes' ? __('farms.fields.notes') : ($isArabic ? 'ملاحظات' : 'Notes') }}
        </label>
        <textarea name="notes"
                  class="form-control @error('notes') is-invalid @enderror"
                  rows="2"
                  placeholder="{{ $isArabic ? 'أي تفاصيل إضافية...' : 'Any additional details...' }}">{{ old('notes', $pen->notes ?? '') }}</textarea>
        @error('notes')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>