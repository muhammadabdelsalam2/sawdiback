@extends('layouts.customer.dashboard')

@section('title', 'Cities | EL-Sawady')

@section('content')
    @php
        $activeLocale = $activeLocale ?? ($currentLocale ?? session('locale_full', 'en-SA'));
    @endphp

    <div class="dashboard-body">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <div>
                <h1 class="dashboard-title mb-1">Cities</h1>
                <p class="dashboard-desc mb-0">Manage cities linked to countries.</p>
            </div>

            <a href="{{ route('superadmin.setting.cities.create', ['locale' => $activeLocale]) }}"
                class="btn btn-primary-green">
                <i class="fa-solid fa-plus me-2"></i>Create City
            </a>
        </div>

        @include('settings.cities._flash')
        <form method="GET" action="{{ route('superadmin.setting.cities.index', ['locale' => $activeLocale]) }}"
            class="mb-3">
            <div class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Filter by Country</label>
                    <select name="country_id" class="form-select">
                        <option value="">All Countries</option>
                        @foreach ($countries as $country)
                            <option value="{{ $country->id }}" {{ (string) $country->id === (string) ($selectedCountryId ?? '') ? 'selected' : '' }}>
                                {{ $country->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4 d-flex gap-2">
                    <button class="btn btn-outline-white" type="submit">Apply</button>
                    <a class="btn btn-outline-white"
                        href="{{ route('superadmin.setting.cities.index', ['locale' => $activeLocale]) }}">Reset</a>
                </div>
            </div>
        </form>

        <div class="table-container">
            <div class="table-responsive">
                <table class="table registry-table mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>City Name</th>
                            <th>Country</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
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
                                        <span class="badge badge-lactating">Active</span>
                                    @else
                                        <span class="badge badge-dry">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('superadmin.setting.cities.edit', ['locale' => $activeLocale, 'city' => $city]) }}"
                                        class="btn btn-sm btn-outline-white me-1">
                                        Edit
                                    </a>

                                    <form
                                        action="{{ route('superadmin.setting.cities.destroy', ['locale' => $activeLocale, 'city' => $city]) }}"
                                        method="POST" class="d-inline" onsubmit="return confirm('Delete this city?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No cities found.</td>
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