@csrf
@php
    $vehicle = $vehicle ?? null;
    $ownershipType = old('ownership_type', $vehicle->ownership_type ?? 'owned');
@endphp

<div class="row g-3">
    <div class="col-md-3">
        <label class="form-label font-weight-bold">{{ __('poultry.fields.plate_number') }} <span class="text-danger">*</span></label>
        <input type="text" name="plate_number" class="form-control" value="{{ old('plate_number', $vehicle->plate_number ?? '') }}" required>
    </div>
    <div class="col-md-3">
        <label class="form-label font-weight-bold">{{ __('farms.fields.farm') }}</label>
        <select name="farm_id" class="form-select">
            <option value="">{{ __('farms.empty.no_farms') }}</option>
            @foreach($farms ?? [] as $farm)
                <option value="{{ $farm->id }}" @selected(old('farm_id', $vehicle->farm_id ?? '') == $farm->id)>{{ $farm->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label font-weight-bold">{{ __('poultry.fields.ownership_type') }} <span class="text-danger">*</span></label>
        <select name="ownership_type" id="ownership_type" class="form-select" required>
            <option value="owned" @selected($ownershipType === 'owned')>{{ __('poultry.options.owned') }}</option>
            <option value="leased" @selected($ownershipType === 'leased')>{{ __('poultry.options.leased') }}</option>
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label font-weight-bold">{{ __('poultry.fields.status') }}</label>
        <select name="status" class="form-select">
            @foreach(['available', 'on_trip', 'maintenance', 'out_of_service'] as $st)
                <option value="{{ $st }}" @selected(old('status', $vehicle->status ?? 'available') === $st)>{{ __('poultry.options.' . $st) }}</option>
            @endforeach
        </select>
    </div>

    {{-- حقول تظهر حصراً عند اختيار سيارة مستأجرة --}}
    <div class="col-md-3 leasing-field" style="display: {{ $ownershipType === 'leased' ? 'block' : 'none' }};">
        <label class="form-label font-weight-bold">{{ __('poultry.fields.lessor_name') }}</label>
        <input type="text" name="lessor_name" class="form-control" value="{{ old('lessor_name', $vehicle->lessor_name ?? '') }}">
    </div>
    <div class="col-md-3 leasing-field" style="display: {{ $ownershipType === 'leased' ? 'block' : 'none' }};">
        <label class="form-label font-weight-bold">{{ __('poultry.fields.lessor_phone') }}</label>
        <input type="text" name="lessor_phone" class="form-control" value="{{ old('lessor_phone', $vehicle->lessor_phone ?? '') }}">
    </div>
    <div class="col-md-3 leasing-field" style="display: {{ $ownershipType === 'leased' ? 'block' : 'none' }};">
        <label class="form-label font-weight-bold">{{ __('poultry.fields.lease_cost') }}</label>
        <input type="number" step="0.01" min="0" name="lease_cost" class="form-control" value="{{ old('lease_cost', $vehicle->lease_cost ?? '0.00') }}">
    </div>
    <div class="col-md-3 leasing-field" style="display: {{ $ownershipType === 'leased' ? 'block' : 'none' }};">
        <label class="form-label font-weight-bold">{{ __('poultry.fields.lease_period') }}</label>
        <select name="lease_period" class="form-select">
            @foreach(['monthly', 'daily', 'per_trip'] as $period)
                <option value="{{ $period }}" @selected(old('lease_period', $vehicle->lease_period ?? 'monthly') === $period)>{{ __('poultry.options.' . $period) }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3 leasing-field" style="display: {{ $ownershipType === 'leased' ? 'block' : 'none' }};">
        <label class="form-label font-weight-bold">{{ __('poultry.fields.lease_start_date') }}</label>
        <input type="date" name="lease_start_date" class="form-control" value="{{ old('lease_start_date', isset($vehicle) && $vehicle->lease_start_date ? $vehicle->lease_start_date->format('Y-m-d') : '') }}">
    </div>
    <div class="col-md-3 leasing-field" style="display: {{ $ownershipType === 'leased' ? 'block' : 'none' }};">
        <label class="form-label font-weight-bold">{{ __('poultry.fields.lease_end_date') }}</label>
        <input type="date" name="lease_end_date" class="form-control" value="{{ old('lease_end_date', isset($vehicle) && $vehicle->lease_end_date ? $vehicle->lease_end_date->format('Y-m-d') : '') }}">
    </div>

    <div class="col-md-3">
        <label class="form-label font-weight-bold">{{ __('poultry.fields.driver_name') }}</label>
        <input type="text" name="driver_name" class="form-control" value="{{ old('driver_name', $vehicle->driver_name ?? '') }}">
    </div>
    <div class="col-md-3">
        <label class="form-label font-weight-bold">{{ __('poultry.fields.driver_phone') }}</label>
        <input type="text" name="driver_phone" class="form-control" value="{{ old('driver_phone', $vehicle->driver_phone ?? '') }}">
    </div>
    <div class="col-md-3">
        <label class="form-label font-weight-bold">{{ __('poultry.fields.capacity_birds') }}</label>
        <input type="number" min="0" name="capacity_birds" class="form-control" value="{{ old('capacity_birds', $vehicle->capacity_birds ?? '') }}">
    </div>
    <div class="col-md-3">
        <label class="form-label font-weight-bold">{{ __('poultry.fields.capacity_crates') }}</label>
        <input type="number" min="0" name="capacity_crates" class="form-control" value="{{ old('capacity_crates', $vehicle->capacity_crates ?? '') }}">
    </div>
    <div class="col-md-12">
        <label class="form-label font-weight-bold">{{ __('poultry.fields.notes') }}</label>
        <input type="text" name="notes" class="form-control" value="{{ old('notes', $vehicle->notes ?? '') }}">
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const select = document.getElementById('ownership_type');
        const fields = document.querySelectorAll('.leasing-field');
        if (select) {
            select.addEventListener('change', function () {
                fields.forEach(el => el.style.display = this.value === 'leased' ? 'block' : 'none');
            });
        }
    });
</script>