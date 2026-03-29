@extends('layouts.customer.dashboard')

@section('title', __('dashboard.countries.title') . ' | EL-Sawady')

@section('content')
    @php
        $activeLocale = $activeLocale ?? ($currentLocale ?? session('locale_full', 'en-SA'));
    @endphp

    <div class="dashboard-body">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <div>
                <h1 class="dashboard-title mb-1">{{ __('dashboard.countries.title') }}</h1>
                <p class="dashboard-desc mb-0">{{ __('dashboard.countries.desc') }}</p>
            </div>
            <a href="{{ route('superadmin.setting.countries.create', ['locale' => $activeLocale]) }}" class="btn btn-primary-green">
                <i class="fa-solid fa-plus me-2"></i>{{ __('dashboard.countries.create') }}
            </a>
        </div>

        @include('settings.countries._flash')

        <div class="table-container">
            <div class="table-responsive">
                <table class="table registry-table mb-0">
                    <thead>
                        <tr>
                            <th>{{ __('dashboard.countries.id') }}</th>
                            <th>{{ __('dashboard.countries.name') }}</th>
                            <th>{{ __('dashboard.countries.iso2') }}</th>
                            <th>{{ __('dashboard.countries.iso3') }}</th>
                            <th>{{ __('dashboard.countries.phone_code') }}</th>
                            <th>{{ __('dashboard.countries.status') }}</th>
                            <th class="text-end">{{ __('dashboard.countries.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($countries as $country)
                            <tr>
                                <td>{{ $country->id }}</td>
                                <td class="animal-id">{{ $country->name }}</td>
                                <td>{{ $country->iso2 ?? '-' }}</td>
                                <td>{{ $country->iso3 ?? '-' }}</td>
                                <td>{{ $country->phone_code ?? '-' }}</td>
                                <td>
                                    @if ($country->is_active)
                                        <span class="badge badge-lactating">{{ __('dashboard.countries.active') }}</span>
                                    @else
                                        <span class="badge badge-dry">{{ __('dashboard.countries.inactive') }}</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('superadmin.setting.countries.edit', ['locale' => $activeLocale, 'country' => $country]) }}"
                                       class="btn btn-sm btn-outline-white me-1">
                                        {{ __('dashboard.countries.edit') }}
                                    </a>

                                    <form
                                        action="{{ route('superadmin.setting.countries.destroy', ['locale' => $activeLocale, 'country' => $country]) }}"
                                        method="POST"
                                        class="d-inline"
                                        onsubmit="return confirm('{{ __('dashboard.countries.delete_confirm') }}');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">{{ __('dashboard.countries.delete') }}</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">{{ __('dashboard.countries.no_data') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-3">
            {{ $countries->links() }}
        </div>
    </div>
@endsection
