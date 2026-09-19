@csrf
<div class="row g-3">
    <div class="col-md-3">
        <label class="form-label">{{ __('fisheries.fields.pond_name') ?? __('اسم الحوض') }} <span class="text-danger">*</span></label>
        <input type="text" name="pond_name" class="form-control" value="{{ old('pond_name', $batch->pond_name ?? '') }}" required>
    </div>

    <div class="col-md-3">
        <label class="form-label">{{ __('fisheries.fields.farm_id') ?? __('المزرعة') }} <span class="text-danger">*</span></label>
        <select name="farm_id" class="form-select" required>
            <option value="">{{ __('farms.empty.no_pens') ?? '-- اختر المزرعة --' }}</option>
            @foreach ($farms ?? [] as $farm)
                <option value="{{ $farm->id }}" @selected(old('farm_id', $batch->farm_id ?? '') == $farm->id)>
                    {{ $farm->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-3">
        <label class="form-label">{{ __('fisheries.fields.fish_type') ?? __('نوع السمك') }} <span class="text-danger">*</span></label>
        <input type="text" name="fish_type" class="form-control" value="{{ old('fish_type', $batch->fish_type ?? '') }}" placeholder="بلطي، بوري..." required>
    </div>

    <div class="col-md-3">
        <label class="form-label">{{ __('fisheries.fields.batch_code') ?? __('كود الدفعة') }}</label>
        <input type="text" name="batch_code" class="form-control" value="{{ old('batch_code', $batch->batch_code ?? '') }}" placeholder="FISH-001">
    </div>

    @if(!isset($batch))
        <div class="col-md-3">
            <label class="form-label">{{ __('fisheries.fields.initial_count') ?? __('العدد الأولي') }} <span class="text-danger">*</span></label>
            <input type="number" min="1" step="1" name="initial_count" class="form-control" value="{{ old('initial_count') }}" required>
        </div>

        <div class="col-md-3">
            <label class="form-label">{{ __('fisheries.fields.initial_weight_g') ?? __('متوسط الوزن الأولي (جرام)') }}</label>
            <input type="number" step="0.01" min="0" name="initial_weight_g" class="form-control" value="{{ old('initial_weight_g', '0.00') }}">
        </div>
    @else
        <div class="col-md-3">
            <label class="form-label">{{ __('fisheries.fields.current_count') ?? __('العدد الحالي') }} <span class="text-danger">*</span></label>
            <input type="number" min="0" step="1" name="current_count" class="form-control" value="{{ old('current_count', $batch->current_count ?? 0) }}" required>
        </div>

        <div class="col-md-3">
            <label class="form-label">{{ __('fisheries.fields.current_weight_g') ?? __('الوزن الحالي (جرام)') }}</label>
            <input type="number" step="0.01" min="0" name="current_weight_g" class="form-control" value="{{ old('current_weight_g', $batch->current_weight_g ?? '0.00') }}">
        </div>
    @endif

    <div class="col-md-3">
        <label class="form-label">{{ __('fisheries.fields.target_weight_g') ?? __('الوزن المستهدف (جرام)') }}</label>
        <input type="number" step="0.01" min="0" name="target_weight_g" class="form-control" value="{{ old('target_weight_g', $batch->target_weight_g ?? '350.00') }}">
    </div>

    <div class="col-md-3">
        <label class="form-label">{{ __('fisheries.fields.started_at') ?? __('تاريخ البدء') }} <span class="text-danger">*</span></label>
        <input type="date" name="started_at" class="form-control" value="{{ old('started_at', isset($batch) && $batch->started_at ? $batch->started_at->format('Y-m-d') : date('Y-m-d')) }}" required>
    </div>

    <div class="col-md-3">
        <label class="form-label">{{ __('fisheries.fields.total_cost') ?? __('إجمالي التكلفة') }}</label>
        <input type="number" step="0.01" min="0" name="total_cost" class="form-control" value="{{ old('total_cost', $batch->total_cost ?? '0.00') }}">
    </div>

    <div class="col-md-3">
        <label class="form-label">{{ __('fisheries.fields.status') ?? __('الحالة') }}</label>
        <select name="status" class="form-select">
            @foreach(['active', 'harvested', 'paused'] as $st)
                <option value="{{ $st }}" @selected(old('status', $batch->status ?? 'active') === $st)>
                    {{ __('fisheries.options.' . $st) ?? $st }}
                </option>
            @endforeach
        </select>
    </div>

    @if(isset($batch))
        <div class="col-md-3">
            <label class="form-label">{{ __('fisheries.fields.harvested_at') ?? __('تاريخ الحصاد') }}</label>
            <input type="date" name="harvested_at" class="form-control" value="{{ old('harvested_at', isset($batch) && $batch->harvested_at ? $batch->harvested_at->format('Y-m-d') : '') }}">
        </div>
        <div class="col-md-3">
            <label class="form-label">{{ __('fisheries.fields.feed_consumed_kg') ?? __('العلف المستهلك (كجم)') }}</label>
            <input type="number" step="0.01" min="0" name="feed_consumed_kg" class="form-control" value="{{ old('feed_consumed_kg', $batch->feed_consumed_kg ?? '0.00') }}">
        </div>
        <div class="col-md-6">
            <label class="form-label">{{ __('fisheries.fields.notes') ?? __('ملاحظات') }}</label>
            <input type="text" name="notes" class="form-control" value="{{ old('notes', $batch->notes ?? '') }}">
        </div>
    @else
        <div class="col-md-9">
            <label class="form-label">{{ __('fisheries.fields.notes') ?? __('ملاحظات') }}</label>
            <input type="text" name="notes" class="form-control" value="{{ old('notes', '') }}">
        </div>
    @endif
</div>
