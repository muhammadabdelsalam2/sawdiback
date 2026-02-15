<div class="chart-card mb-4">
    <div class="row g-4">
        <div class="col-md-6">
            <label for="country_id" class="form-label fw-semibold">Country <span class="text-danger">*</span></label>
            <select id="country_id" name="country_id" class="form-select @error('country_id') is-invalid @enderror" required>
                <option value="">Select country</option>
                @foreach ($countries as $countryItem)
                    <option
                        value="{{ $countryItem->id }}"
                        {{ (string) old('country_id', $city->country_id ?? '') === (string) $countryItem->id ? 'selected' : '' }}>
                        {{ $countryItem->name }}
                    </option>
                @endforeach
            </select>
            @error('country_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-6">
            <label for="name" class="form-label fw-semibold">City Name <span class="text-danger">*</span></label>
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

        <div class="col-md-6 d-flex align-items-center">
            <label class="custom-checkbox-label mb-0 mt-4">
                <input
                    type="checkbox"
                    name="is_active"
                    value="1"
                    class="custom-checkbox"
                    {{ old('is_active', ($city->is_active ?? true)) ? 'checked' : '' }}>
                <span class="checkbox-text">Active</span>
            </label>
            @error('is_active')
                <div class="text-danger small ms-2">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>
