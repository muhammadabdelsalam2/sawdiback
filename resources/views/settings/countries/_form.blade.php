<div class="chart-card mb-4">
    <div class="row g-4">
        <div class="col-md-6">
            <label for="name" class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
            <input
                type="text"
                id="name"
                name="name"
                value="{{ old('name', $country->name ?? '') }}"
                class="form-control @error('name') is-invalid @enderror"
                required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-3">
            <label for="iso2" class="form-label fw-semibold">ISO2</label>
            <input
                type="text"
                id="iso2"
                name="iso2"
                value="{{ old('iso2', $country->iso2 ?? '') }}"
                class="form-control @error('iso2') is-invalid @enderror"
                maxlength="2">
            @error('iso2')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-3">
            <label for="iso3" class="form-label fw-semibold">ISO3</label>
            <input
                type="text"
                id="iso3"
                name="iso3"
                value="{{ old('iso3', $country->iso3 ?? '') }}"
                class="form-control @error('iso3') is-invalid @enderror"
                maxlength="3">
            @error('iso3')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-6">
            <label for="phone_code" class="form-label fw-semibold">Phone Code</label>
            <input
                type="text"
                id="phone_code"
                name="phone_code"
                value="{{ old('phone_code', $country->phone_code ?? '') }}"
                class="form-control @error('phone_code') is-invalid @enderror"
                placeholder="+20">
            @error('phone_code')
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
                    {{ old('is_active', ($country->is_active ?? true)) ? 'checked' : '' }}>
                <span class="checkbox-text">Active</span>
            </label>
            @error('is_active')
                <div class="text-danger small ms-2">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>
