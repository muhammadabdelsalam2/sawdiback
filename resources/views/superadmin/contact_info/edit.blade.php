@extends('layouts.customer.dashboard')

@section('title', __('dashboard.contact_info.title'))

@section('content')
    @php
        $activeLocale = $currentLocale ?? session('locale_full', 'en-SA');
    @endphp
    <div class="dashboard-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="dashboard-title mb-1">{{ __('dashboard.contact_info.title') }}</h1>
                <p class="dashboard-desc mb-0">{{ __('dashboard.contact_info.description') }}</p>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('superadmin.contact-info.update', ['locale' => $activeLocale]) }}">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('dashboard.contact_info.phone') }}</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone', $contactInfo->phone) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('dashboard.contact_info.email') }}</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $contactInfo->email) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('dashboard.contact_info.address_ar') }}</label>
                            <textarea name="address_ar" class="form-control" rows="2">{{ old('address_ar', $contactInfo->address['ar'] ?? '') }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('dashboard.contact_info.address_en') }}</label>
                            <textarea name="address_en" class="form-control" rows="2">{{ old('address_en', $contactInfo->address['en'] ?? '') }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('dashboard.contact_info.working_hours_ar') }}</label>
                            <input type="text" name="working_hours_ar" class="form-control" value="{{ old('working_hours_ar', $contactInfo->working_hours['ar'] ?? '') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('dashboard.contact_info.working_hours_en') }}</label>
                            <input type="text" name="working_hours_en" class="form-control" value="{{ old('working_hours_en', $contactInfo->working_hours['en'] ?? '') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('dashboard.contact_info.description_ar') }}</label>
                            <textarea name="description_ar" class="form-control" rows="4">{{ old('description_ar', $contactInfo->description['ar'] ?? '') }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('dashboard.contact_info.description_en') }}</label>
                            <textarea name="description_en" class="form-control" rows="4">{{ old('description_en', $contactInfo->description['en'] ?? '') }}</textarea>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ __('dashboard.contact_info.whatsapp_url') }}</label>
                            <input type="url" name="whatsapp_url" class="form-control" value="{{ old('whatsapp_url', $contactInfo->whatsapp_url) }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ __('dashboard.contact_info.facebook_url') }}</label>
                            <input type="url" name="facebook_url" class="form-control" value="{{ old('facebook_url', $contactInfo->facebook_url) }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ __('dashboard.contact_info.instagram_url') }}</label>
                            <input type="url" name="instagram_url" class="form-control" value="{{ old('instagram_url', $contactInfo->instagram_url) }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ __('dashboard.contact_info.x_url') }}</label>
                            <input type="url" name="x_url" class="form-control" value="{{ old('x_url', $contactInfo->x_url) }}">
                        </div>
                    </div>

                    <div class="mt-4">
                        <button class="btn btn-primary" type="submit">{{ __('dashboard.contact_info.save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
