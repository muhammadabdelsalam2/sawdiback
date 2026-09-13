@csrf
<div class="row g-3">
    {{-- المزرعة --}}
    <div class="col-md-6">
        <label class="form-label font-weight-bold small text-muted">
            {{ __('farms.fields.farm') }} <span class="text-danger">*</span>
        </label>
        <select name="farm_id" class="form-select" required>
            <option value="">-- {{ __('farms.placeholders.select_farm') ?? 'اختر المزرعة' }} --</option>
            @foreach($farms as $farm)
                <option value="{{ $farm->id }}" @selected((int) old('farm_id', $pen->farm_id ?? 0) === $farm->id)>
                    {{ $farm->name }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- رقم الحظيرة --}}
    <div class="col-md-6">
        <label class="form-label font-weight-bold small text-muted">
            {{ __('farms.fields.pen_number') }} <span class="text-danger">*</span>
        </label>
        <input type="text" name="pen_number" class="form-control" value="{{ old('pen_number', $pen->pen_number ?? '') }}" required placeholder="مثال: P-101">
    </div>

    {{-- نوع الحظيرة (الأنواع المطلوبة) --}}
    <div class="col-md-6">
        <label class="form-label font-weight-bold small text-muted">
            {{ __('farms.fields.type') }} <span class="text-danger">*</span>
        </label>
        <select name="type" class="form-select" required>
            <option value="">-- {{ __('farms.placeholders.select_type') ?? 'اختر النوع' }} --</option>
            @foreach(\App\Models\FarmPen::getTypeOptions() as $key => $label)
                <option value="{{ $key }}" @selected(old('type', $pen->type ?? '') === $key)>
                    {{ $label }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- السعة الاستيعابية --}}
    <div class="col-md-6">
        <label class="form-label font-weight-bold small text-muted">
            {{ __('farms.fields.capacity') }}
        </label>
        <input type="number" min="0" name="capacity" class="form-control" value="{{ old('capacity', $pen->capacity ?? '') }}" placeholder="0">
    </div>

    {{-- ملاحظات --}}
    <div class="col-12">
        <label class="form-label font-weight-bold small text-muted">
            {{ __('farms.fields.notes') }}
        </label>
        <textarea name="notes" class="form-control" rows="3" placeholder="{{ __('farms.placeholders.notes') ?? 'أي ملاحظات إضافية...' }}">{{ old('notes', $pen->notes ?? '') }}</textarea>
    </div>
</div>