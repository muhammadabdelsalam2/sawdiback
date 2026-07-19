@csrf
<div class="row g-3">
    <div class="col-md-3">
        <label class="form-label">{{ __('poultry.fields.cycle_number') }}</label>
        <input type="text" name="cycle_number" class="form-control" value="{{ old('cycle_number', $cycle->cycle_number ?? '') }}" required>
    </div>
    <div class="col-md-3">
        <label class="form-label">{{ __('poultry.fields.pen_id') }}</label>
        <select name="pen_id" class="form-select">
            <option value="">{{ __('farms.empty.no_pens') }}</option>
            @foreach ($pens ?? [] as $pen)
                <option value="{{ $pen->id }}" @selected(old('pen_id', $cycle->pen_id ?? '') == $pen->id)>{{ $pen->farm?->name }} - {{ $pen->pen_number }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label">{{ __('poultry.fields.chick_count') }}</label>
        <input type="number" min="1" name="chick_count" class="form-control" value="{{ old('chick_count', $cycle->chick_count ?? '') }}" required>
    </div>
    <div class="col-md-3">
        <label class="form-label">{{ __('poultry.fields.started_at') }}</label>
        <input type="date" name="started_at" class="form-control" value="{{ old('started_at', isset($cycle) ? $cycle->started_at?->format('Y-m-d') : '') }}" required>
    </div>
    <div class="col-md-3">
        <label class="form-label">{{ __('poultry.fields.status') }}</label>
        <select name="status" class="form-select">
            @foreach(['active', 'finished'] as $status)
                <option value="{{ $status }}" @selected(old('status', $cycle->status ?? 'active') === $status)>{{ __('poultry.options.' . $status) }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-9">
        <label class="form-label">{{ __('poultry.fields.notes') }}</label>
        <input type="text" name="notes" class="form-control" value="{{ old('notes', $cycle->notes ?? '') }}">
    </div>
</div>
