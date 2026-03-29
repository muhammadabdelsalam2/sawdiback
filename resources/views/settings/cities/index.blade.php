@extends('layouts.customer.dashboard')

@section('title', __('dashboard.cities.title') . ' | EL-Sawady')

@section('content')
    @php
        $activeLocale = $activeLocale ?? ($currentLocale ?? session('locale_full', 'en-SA'));
    @endphp

    <div class="dashboard-body">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <div>
                <h1 class="dashboard-title mb-1">{{ __('dashboard.cities.title') }}</h1>
                <p class="dashboard-desc mb-0">{{ __('dashboard.cities.desc') }}</p>
            </div>

            <a href="{{ route('superadmin.setting.cities.create', ['locale' => $activeLocale]) }}"
                class="btn btn-primary-green">
                <i class="fa-solid fa-plus me-2"></i>{{ __('dashboard.cities.create') }}
            </a>
        </div>

        @include('settings.cities._flash')
        <form method="GET" action="{{ route('superadmin.setting.cities.index', ['locale' => $activeLocale]) }}"
            class="mb-3">
            <div class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">{{ __('dashboard.cities.filter_country') }}</label>
                    <select name="country_id" class="form-select">
                        <option value="">{{ __('dashboard.cities.all_countries') }}</option>
                        @foreach ($countries as $country)
                            <option value="{{ $country->id }}" {{ (string) $country->id === (string) ($selectedCountryId ?? '') ? 'selected' : '' }}>
                                {{ $country->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4 d-flex gap-2">
                    <button class="btn btn-outline-white" type="submit">{{ __('dashboard.cities.apply') }}</button>
                    <a class="btn btn-outline-white"
                        href="{{ route('superadmin.setting.cities.index', ['locale' => $activeLocale]) }}">{{ __('dashboard.cities.reset') }}</a>
                </div>
            </div>
        </form>

        <div class="table-container">
            <div class="table-responsive">
                <table class="table registry-table mb-0">
                    <thead>
                        <tr>
                            <th>{{ __('dashboard.cities.id') }}</th>
                            <th>{{ __('dashboard.cities.name') }}</th>
                            <th>{{ __('dashboard.cities.country') }}</th>
                            <th>{{ __('dashboard.cities.status') }}</th>
                            <th class="text-end">{{ __('dashboard.cities.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($cities as $city)
                            <tr>
                                <td>{{ $city->id }}</td>
                                <td class="animal-id">{{ $city->name }}</td>
                                <td>{{ $city->country?->name ?? '-' }}</td>
                                <td>
                                    @if ($city->is_active)
                                        <span class="badge badge-lactating">{{ __('dashboard.countries.active') }}</span>
                                    @else
                                        <span class="badge badge-dry">{{ __('dashboard.countries.inactive') }}</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('superadmin.setting.cities.edit', ['locale' => $activeLocale, 'city' => $city]) }}"
                                        class="btn btn-sm btn-outline-white me-1">
                                        {{ __('dashboard.cities.edit') }}
                                    </a>

                                    <form
                                        action="{{ route('superadmin.setting.cities.destroy', ['locale' => $activeLocale, 'city' => $city]) }}"
                                        method="POST" class="d-inline" onsubmit="return confirm('{{ __('dashboard.cities.delete_confirm') }}');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">{{ __('dashboard.cities.delete') }}</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">{{ __('dashboard.cities.no_data') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-3">
            {{ $cities->links() }}
        </div>
    </div>
@endsection