@extends('layouts.customer.dashboard')

@section('title', 'Countries | EL-Sawady')

@section('content')
    @php
        $activeLocale = $activeLocale ?? ($currentLocale ?? session('locale_full', 'en-SA'));
    @endphp

    <div class="dashboard-body">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <div>
                <h1 class="dashboard-title mb-1">Countries</h1>
                <p class="dashboard-desc mb-0">Manage countries used across the system.</p>
            </div>
            <a href="{{ route('settings.countries.create', ['locale' => $activeLocale]) }}" class="btn btn-primary-green">
                <i class="fa-solid fa-plus me-2"></i>Create Country
            </a>
        </div>

        @include('settings.countries._flash')

        <div class="table-container">
            <div class="table-responsive">
                <table class="table registry-table mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>ISO2</th>
                            <th>ISO3</th>
                            <th>Phone Code</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
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
                                        <span class="badge badge-lactating">Active</span>
                                    @else
                                        <span class="badge badge-dry">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('settings.countries.edit', ['locale' => $activeLocale, 'country' => $country]) }}"
                                       class="btn btn-sm btn-outline-white me-1">
                                        Edit
                                    </a>

                                    <form
                                        action="{{ route('settings.countries.destroy', ['locale' => $activeLocale, 'country' => $country]) }}"
                                        method="POST"
                                        class="d-inline"
                                        onsubmit="return confirm('Delete this country?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">No countries found.</td>
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
