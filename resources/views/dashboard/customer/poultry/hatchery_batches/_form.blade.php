@csrf
<div class="row g-3">
    <div class="col-md-3"><label class="form-label">{{ __('poultry.fields.batch_number') }}</label><input type="text" name="batch_number" class="form-control" value="{{ old('batch_number', $batch->batch_number ?? '') }}" required></div>
    <div class="col-md-3"><label class="form-label">{{ __('poultry.fields.machine') }}</label><select name="hatchery_machine_id" class="form-select" required>@foreach($machines as $machine)<option value="{{ $machine->id }}" @selected((int) old('hatchery_machine_id', $batch->hatchery_machine_id ?? 0) === $machine->id)>{{ $machine->machine_number }}</option>@endforeach</select></div>
    <div class="col-md-2"><label class="form-label">{{ __('poultry.fields.loaded_at') }}</label><input type="date" name="loaded_at" class="form-control" value="{{ old('loaded_at', isset($batch) ? $batch->loaded_at?->format('Y-m-d') : '') }}" required></div>
    <div class="col-md-2"><label class="form-label">{{ __('poultry.fields.expected_hatch_at') }}</label><input type="date" name="expected_hatch_at" class="form-control" value="{{ old('expected_hatch_at', isset($batch) ? $batch->expected_hatch_at?->format('Y-m-d') : '') }}" required></div>
    <div class="col-md-2"><label class="form-label">{{ __('poultry.fields.actual_hatch_at') }}</label><input type="date" name="actual_hatch_at" class="form-control" value="{{ old('actual_hatch_at', isset($batch) ? $batch->actual_hatch_at?->format('Y-m-d') : '') }}"></div>
    <div class="col-md-3"><label class="form-label">{{ __('poultry.fields.eggs_loaded') }}</label><input type="number" min="1" name="eggs_loaded" class="form-control" value="{{ old('eggs_loaded', $batch->eggs_loaded ?? '') }}" required></div>
    <div class="col-md-3"><label class="form-label">{{ __('poultry.fields.chicks_produced') }}</label><input type="number" min="0" name="chicks_produced" class="form-control" value="{{ old('chicks_produced', $batch->chicks_produced ?? 0) }}"></div>
    <div class="col-md-6"><label class="form-label">{{ __('poultry.fields.notes') }}</label><input type="text" name="notes" class="form-control" value="{{ old('notes', $batch->notes ?? '') }}"></div>
</div>
