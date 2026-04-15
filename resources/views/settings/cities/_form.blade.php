<div class="chart-card mb-4">
    <div class="row g-4">
        <div class="col-md-6">
            <label for="name" class="form-label fw-semibold">{{ __('dashboard.cities.fields.name') }} <span class="text-danger">*</span></label>
            <input
                type="text"
                id="name"
                name="name"
                value="{{ old('name', $city->name ?? '') }}"
                class="form-control @error('name') is-invalid @enderror"
                required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-6">
            <label for="country_id" class="form-label fw-semibold">{{ __('dashboard.cities.fields.country') }} <span class="text-danger">*</span></label>
            <select name="country_id" id="country_id" class="form-select @error('country_id') is-invalid @enderror" required>
                <option value="">{{ __('dashboard.cities.all_countries') }}</option>
                @foreach($countries as $country)
                    <option value="{{ $country->id }}" {{ old('country_id', $city->country_id ?? '') == $country->id ? 'selected' : '' }}>
                        {{ $country->name }}
                    </option>
                @endforeach
            </select>
            @error('country_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-6 d-flex align-items-center">
            <label class="custom-checkbox-label mb-0 mt-4">
                <input
                    type="checkbox"
                    name="is_active"
                    value="1"
                    class="custom-checkbox"
                    {{ old('is_active', ($city->is_active ?? true)) ? 'checked' : '' }}>
                <span class="checkbox-text">{{ __('dashboard.countries.active') }}</span>
            </label>
            @error('is_active')
                <div class="text-danger small ms-2">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>
