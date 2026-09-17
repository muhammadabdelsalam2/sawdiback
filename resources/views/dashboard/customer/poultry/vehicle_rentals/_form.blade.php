@csrf
@php
    $rental = $rental ?? null;
    $selectedVehicleId = old('vehicle_id', $rental->vehicle_id ?? request()->query('vehicle_id'));
    $rentalType = old('rental_type', $rental->rental_type ?? 'external');
@endphp

<div class="row g-3">
    <div class="col-md-3">
        <label class="form-label font-weight-bold">{{ __('poultry.titles.vehicles') }} <span class="text-danger">*</span></label>
        <select name="vehicle_id" class="form-select" required>
            <option value="">{{ __('poultry.options.select_farm') }}</option>
            @foreach($vehicles ?? [] as $veh)
                <option value="{{ $veh->id }}" @selected($selectedVehicleId == $veh->id)>
                    {{ $veh->plate_number }} ({{ $veh->driver_name ?? '-' }})
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-3">
        <label class="form-label font-weight-bold">{{ __('poultry.fields.rental_type') }} <span class="text-danger">*</span></label>
        <select name="rental_type" id="rental_type" class="form-select" required>
            <option value="external" @selected($rentalType === 'external')>{{ __('poultry.options.external') }}</option>
            <option value="internal" @selected($rentalType === 'internal')>{{ __('poultry.options.internal') }}</option>
        </select>
    </div>

    <div class="col-md-3">
        <label class="form-label font-weight-bold">{{ __('poultry.fields.status') }} <span class="text-danger">*</span></label>
        <select name="payment_status" class="form-select" required>
            @foreach(['pending', 'paid', 'partially_paid'] as $st)
                <option value="{{ $st }}" @selected(old('payment_status', $rental->payment_status ?? 'pending') === $st)>
                    {{ __('poultry.options.' . $st) }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-3">
        <label class="form-label font-weight-bold">{{ __('poultry.fields.started_at') }} <span class="text-danger">*</span></label>
        <input type="datetime-local" name="started_at" class="form-control" value="{{ old('started_at', isset($rental) && $rental->started_at ? $rental->started_at->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}" required>
    </div>

    {{-- حقول تظهر حصراً عند التأجير الخارجي --}}
    <div class="col-md-3 external-field" style="display: {{ $rentalType === 'external' ? 'block' : 'none' }};">
        <label class="form-label font-weight-bold">{{ __('poultry.fields.customer_name') }}</label>
        <input type="text" name="customer_name" class="form-control" value="{{ old('customer_name', $rental->customer_name ?? '') }}">
    </div>

    <div class="col-md-3 external-field" style="display: {{ $rentalType === 'external' ? 'block' : 'none' }};">
        <label class="form-label font-weight-bold">{{ __('poultry.fields.customer_phone') }}</label>
        <input type="text" name="customer_phone" class="form-control" value="{{ old('customer_phone', $rental->customer_phone ?? '') }}">
    </div>

    <div class="col-md-3">
        <label class="form-label font-weight-bold">{{ __('poultry.fields.actual_hatch_at') }} ({{ __('poultry.fields.status') }})</label>
        <input type="datetime-local" name="ended_at" class="form-control" value="{{ old('ended_at', isset($rental) && $rental->ended_at ? $rental->ended_at->format('Y-m-d\TH:i') : '') }}">
    </div>

    <div class="col-md-3">
        <label class="form-label font-weight-bold">{{ __('poultry.fields.origin') }}</label>
        <input type="text" name="origin" class="form-control" value="{{ old('origin', $rental->origin ?? '') }}">
    </div>

    <div class="col-md-3">
        <label class="form-label font-weight-bold">{{ __('poultry.fields.destination') }}</label>
        <input type="text" name="destination" class="form-control" value="{{ old('destination', $rental->destination ?? '') }}">
    </div>

    <div class="col-md-3">
        <label class="form-label font-weight-bold">{{ __('poultry.fields.rental_fee') }}</label>
        <input type="number" step="0.01" min="0" name="rental_fee" class="form-control" value="{{ old('rental_fee', $rental->rental_fee ?? '0.00') }}">
    </div>

    <div class="col-md-3">
        <label class="form-label font-weight-bold">{{ __('poultry.fields.fuel_cost') }}</label>
        <input type="number" step="0.01" min="0" name="fuel_cost" class="form-control" value="{{ old('fuel_cost', $rental->fuel_cost ?? '0.00') }}">
    </div>

    <div class="col-md-3">
        <label class="form-label font-weight-bold">{{ __('poultry.fields.driver_commission') }}</label>
        <input type="number" step="0.01" min="0" name="driver_commission" class="form-control" value="{{ old('driver_commission', $rental->driver_commission ?? '0.00') }}">
    </div>

    <div class="col-md-3">
        <label class="form-label font-weight-bold">{{ __('poultry.options.other') }}</label>
        <input type="number" step="0.01" min="0" name="other_expenses" class="form-control" value="{{ old('other_expenses', $rental->other_expenses ?? '0.00') }}">
    </div>

    <div class="col-md-9">
        <label class="form-label font-weight-bold">{{ __('poultry.fields.notes') }}</label>
        <input type="text" name="notes" class="form-control" value="{{ old('notes', $rental->notes ?? '') }}">
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const select = document.getElementById('rental_type');
        const fields = document.querySelectorAll('.external-field');
        if (select) {
            select.addEventListener('change', function () {
                fields.forEach(el => el.style.display = this.value === 'external' ? 'block' : 'none');
            });
        }
    });
</script>