@csrf
<div class="row g-3">
    <div class="col-md-4"><label class="form-label">{{ __('farms.fields.farm') }}</label><select name="farm_id" class="form-select" required>@foreach($farms as $farm)<option value="{{ $farm->id }}" @selected((int) old('farm_id', $pen->farm_id ?? 0) === $farm->id)>{{ $farm->name }}</option>@endforeach</select></div>
    <div class="col-md-3"><label class="form-label">{{ __('farms.fields.pen_number') }}</label><input type="text" name="pen_number" class="form-control" value="{{ old('pen_number', $pen->pen_number ?? '') }}" required></div>
    <div class="col-md-3"><label class="form-label">{{ __('farms.fields.type') }}</label><select name="type" class="form-select" required>@foreach(['livestock','poultry','mixed'] as $type)<option value="{{ $type }}" @selected(old('type', $pen->type ?? '') === $type)>{{ __('farms.options.' . $type) }}</option>@endforeach</select></div>
    <div class="col-md-2"><label class="form-label">{{ __('farms.fields.capacity') }}</label><input type="number" min="0" name="capacity" class="form-control" value="{{ old('capacity', $pen->capacity ?? '') }}"></div>
    <div class="col-12"><label class="form-label">{{ __('farms.fields.notes') }}</label><input type="text" name="notes" class="form-control" value="{{ old('notes', $pen->notes ?? '') }}"></div>
</div>
