@csrf
<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">{{ __('warehouse.fields.name') }}</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $category->name ?? '') }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">{{ __('warehouse.fields.code') }}</label>
        <input type="text" name="code" class="form-control" value="{{ old('code', $category->code ?? '') }}">
    </div>
    <div class="col-md-6">
        <label class="form-label">{{ __('warehouse.fields.image') }}</label>
        @php
            $categoryImageUrl = isset($category)
                ? $category->image_url
                : 'https://ui-avatars.com/api/?name=Category&background=E5E7EB&color=374151&size=400';
        @endphp
        <div class="d-flex align-items-center gap-3 mb-2">
            <img id="category-image-preview" src="{{ $categoryImageUrl }}"
                alt="{{ old('name', $category->name ?? __('warehouse.titles.categories')) }}"
                onerror="this.onerror=null;this.src='{{ isset($category) ? $category->placeholder_image_url : 'https://ui-avatars.com/api/?name=Category&background=E5E7EB&color=374151&size=400' }}';"
                class="img-thumbnail rounded shadow-sm"
                style="width:96px; height:96px; object-fit:cover;">
            <div class="text-muted small">
                {{ __('warehouse.help.image_upload') }}
            </div>
        </div>
        <input type="file" name="image" id="category-image-input" class="form-control" accept="image/jpeg,image/png,image/webp">
    </div>
    <div class="col-md-6">
        <label class="form-label">{{ __('warehouse.fields.sort_order') }}</label>
        <input type="number" min="0" name="sort_order" class="form-control" value="{{ old('sort_order', $category->sort_order ?? 0) }}">
    </div>
    <div class="col-md-6 d-flex align-items-end">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active"
                @checked((bool) old('is_active', $category->is_active ?? true))>
            <label class="form-check-label" for="is_active">{{ __('warehouse.fields.active') }}</label>
        </div>
    </div>
    <div class="col-12">
        <label class="form-label">{{ __('warehouse.fields.notes') }}</label>
        <textarea name="notes" class="form-control" rows="3">{{ old('notes', $category->notes ?? '') }}</textarea>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const input = document.getElementById('category-image-input');
            const preview = document.getElementById('category-image-preview');

            if (!input || !preview) {
                return;
            }

            input.addEventListener('change', function () {
                const file = this.files && this.files[0];

                if (!file) {
                    return;
                }

                preview.src = URL.createObjectURL(file);
                preview.onload = function () {
                    URL.revokeObjectURL(preview.src);
                };
            });
        });
    </script>
@endpush
