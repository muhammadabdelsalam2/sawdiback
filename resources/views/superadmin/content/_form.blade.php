@php
    $activeLocale = $currentLocale ?? session('locale_full', 'en-SA');
@endphp

<div class="chart-card mb-4">
    <h3 class="chart-title mb-3">{{ __('dashboard.content.basic_info') }}</h3>
    <div class="row g-4">
        <div class="col-md-6">
            <label for="title_ar" class="form-label fw-semibold">{{ __('dashboard.content.title_ar') }}</label>
            <input
                type="text"
                id="title_ar"
                name="title_ar"
                value="{{ old('title_ar', $content->title['ar'] ?? '') }}"
                class="form-control @error('title_ar') is-invalid @enderror"
                required>
            @error('title_ar')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-6">
            <label for="title_en" class="form-label fw-semibold">{{ __('dashboard.content.title_en') }}</label>
            <input
                type="text"
                id="title_en"
                name="title_en"
                value="{{ old('title_en', $content->title['en'] ?? '') }}"
                class="form-control @error('title_en') is-invalid @enderror"
                required>
            @error('title_en')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-6">
            <label for="description_ar" class="form-label fw-semibold">{{ __('dashboard.content.description_ar') }}</label>
            <textarea
                id="description_ar"
                name="description_ar"
                class="form-control @error('description_ar') is-invalid @enderror"
                rows="4"
                required>{{ old('description_ar', $content->description['ar'] ?? '') }}</textarea>
            @error('description_ar')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-6">
            <label for="description_en" class="form-label fw-semibold">{{ __('dashboard.content.description_en') }}</label>
            <textarea
                id="description_en"
                name="description_en"
                class="form-control @error('description_en') is-invalid @enderror"
                rows="4"
                required>{{ old('description_en', $content->description['en'] ?? '') }}</textarea>
            @error('description_en')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="chart-card mb-4">
    <h3 class="chart-title mb-3">{{ __('dashboard.content.video_info') }}</h3>
    <div class="row g-4">
        <div class="col-md-6">
            <label for="video" class="form-label fw-semibold">{{ __('dashboard.content.video_file') }}</label>
            <input
                type="file"
                id="video"
                name="video"
                class="form-control @error('video') is-invalid @enderror"
                accept="video/*">
            <small class="text-muted">{{ __('dashboard.content.video_file_help') }}</small>
            @error('video')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            @if (isset($content) && $content->video)
                <div class="mt-2">
                    <video width="200" height="120" controls>
                        <source src="{{ asset('storage/' . $content->video) }}" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                    <p class="text-muted small mt-1">{{ __('dashboard.content.current_video') }}</p>
                </div>
            @endif
        </div>

        <div class="col-md-6">
            <label for="video_url" class="form-label fw-semibold">{{ __('dashboard.content.video_url') }}</label>
            <input
                type="url"
                id="video_url"
                name="video_url"
                value="{{ old('video_url', $content->video_url ?? '') }}"
                class="form-control @error('video_url') is-invalid @enderror"
                placeholder="https://example.com/video.mp4">
            <small class="text-muted">{{ __('dashboard.content.video_url_help') }}</small>
            @error('video_url')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="d-flex flex-wrap gap-2">
    <button type="submit" class="btn btn-primary-green">{{ $submitLabel }}</button>
    <a href="{{ route('superadmin.content.index', ['locale' => $activeLocale]) }}" class="btn btn-outline-white">Cancel</a>
</div>
