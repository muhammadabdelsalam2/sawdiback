@csrf
<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">{{ __('livestock.fields.species') }}</label>
        <select name="species_id" class="form-select" required>
            @foreach ($species as $item)
                <option value="{{ $item->id }}" @selected(old('species_id', $breed->species_id ?? '') == $item->id)>{{ $item->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label">{{ __('livestock.fields.breed_name') }}</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $breed->name ?? '') }}" required>
    </div>
</div>
