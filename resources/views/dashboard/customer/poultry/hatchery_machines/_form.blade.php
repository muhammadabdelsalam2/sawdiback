@csrf
<div class="row g-3">
    <div class="col-md-4"><label class="form-label">{{ __('poultry.fields.farm') }}</label><select name="farm_id" class="form-select" required><option value="">{{ __('poultry.options.select_farm') }}</option>@foreach($farms ?? [] as $farm)<option value="{{ $farm->id }}" @selected(old('farm_id', $machine->farm_id ?? '') == $farm->id)>{{ $farm->name }}</option>@endforeach</select></div>
    <div class="col-md-4"><label class="form-label">{{ __('poultry.fields.machine_number') }}</label><input type="text" name="machine_number" class="form-control" value="{{ old('machine_number', $machine->machine_number ?? '') }}" required></div>
    <div class="col-md-4"><label class="form-label">{{ __('poultry.fields.capacity') }}</label><input type="number" min="1" name="capacity" class="form-control" value="{{ old('capacity', $machine->capacity ?? '') }}" required></div>
    <div class="col-md-4"><label class="form-label">{{ __('poultry.fields.is_active') }}</label><select name="is_active" class="form-select"><option value="1" @selected(old('is_active', (int) ($machine->is_active ?? 1)) === 1)>{{ __('poultry.options.active') }}</option><option value="0" @selected(old('is_active', (int) ($machine->is_active ?? 1)) === 0)>{{ __('poultry.options.inactive') }}</option></select></div>
    <div class="col-12"><label class="form-label">{{ __('poultry.fields.notes') }}</label><input type="text" name="notes" class="form-control" value="{{ old('notes', $machine->notes ?? '') }}"></div>
</div>
