@extends('layouts.customer.dashboard')

@section('title')
    {{ __('farmer.titles.farmer') }}
@endsection

@section('content')
    <div class="container py-4">

        {{-- Header --}}
        <div class="d-flex justify-content-between text-color align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-1 text-main">
                    <i class="fa-solid fa-wheat-awn me-2"></i>
                    {{ __('farmer.titles.farmer') }}
                </h2>
                <small class="text-color custom-highlight ">{{ __('farmer.content.description') }}</small>
            </div>

            <a class="btn btn-primary-green px-4 py-2 shadow-sm border-0 rounded-3"
                data-url="{{ route('customer.farmers.create', ['locale' => $currentLocale]) }}" data-ajax-popup="true"
                data-title="{{ __('Create Farmer') }}" data-size="lg"
                href="{{ route('customer.farmers.create', ['locale' => $currentLocale]) }}">
                <i class="fa-solid fa-plus me-1"></i>
                {{ __('farmer.actions.add_farmer') }}
            </a>
        </div>

        {{-- Alerts --}}
        @if (session('success'))
            <div class="alert alert-success shadow-sm border-0 rounded-3">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger shadow-sm border-0 rounded-3">
                {{ session('error') }}
            </div>
        @endif

        {{-- Card --}}

        {{-- Table --}}
        <div class="table-container">
            <table class="table align-middle registry-table mb-0 js-livestock-table sd-export-table">
                <thead class="table-light">
                    <tr>
                        <th>{{ __('#') }}</th>
                        {{-- Professional Image Farmer --}}
                        <th>
                            {{ __('farmer.fields.image') }}
                        </th>
                        <th>{{ __('farmer.fields.name') }}</th>
                        <th>{{ __('farmer.fields.email') }}</th>
                        <th>{{ __('farmer.fields.phone') }}</th>
                        <th>{{ __('farmer.fields.opening_balance') }}</th>
                        <th>{{ __('farmer.fields.address') }}</th>
                        <th>{{ __('farmer.fields.status') }}</th>
                        <th class="text-end">{{ __('farmer.fields.actions') }}</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($farmers as $row)
                        <tr class="hover-row">

                            <td class="fw-semibold text-muted w-100 text-center">
                                {{-- Style Number  --}}
                                <span
                                    class="badge  w-50 d-flex justify-content-center custom-highlight align-items-center text-white fs-5  fw-bold">{{ $loop->iteration }}</span>
                            </td>

                            {{-- Display Professional Image --}}
                            <td>
                                @if ($row->image)
                                    <a
                                        href="{{ route('customer.farmers.show', ['locale' => $currentLocale, 'farmer' => $row->id]) }}">
                                        <img src="{{ asset('storage/' . $row->image) }}" alt="Farmer Image"
                                            class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover;">
                                    </a>
                                @else
                                    <div class="w-100 bg-secondary-subtle border border-secondary text-secondary rounded-circle d-flex align-items-center justify-content-center"
                                        style="width: 50px; height: 50px;">
                                        <a
                                            href="{{ route('customer.farmers.show', ['locale' => $currentLocale, 'farmer' => $row->id]) }}">
                                            <img class="w-100  rounded-5"
                                                src="{{ asset('assets/images/farmer/default-farmer.jpg') }}"
                                                alt="Default Farmer Image">
                                        </a>
                                    </div>
                                @endif
                            </td>
                            <td class="fw-semibold w-100">
                                <i class="fa-solid fa-user text-main me-2"></i>
                                {{ $row->name }}
                            </td>

                            <td>{{ $row->email }}</td>

                            <td>{{ $row->phone }}</td>

                            <td>
                                <span class="badge bg-soft-success text-success">
                                    {{ $row->opening_balance }}
                                </span>
                            </td>

                            <td class="text-muted">{{ $row->address }}</td>

                            <td>
                                @if ($row->is_active)
                                    <span class="badge bg-success-subtle text-success px-3 py-2">
                                        <i class="fa-solid fa-circle-check me-1"></i> Active
                                    </span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger px-3 py-2">
                                        <i class="fa-solid fa-circle-xmark me-1"></i> Inactive
                                    </span>
                                @endif
                            </td>

                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-2">

                                    <a class="btn btn-icon btn-light"
                                        href="{{ route('customer.crops-feed.crops.show', ['locale' => $currentLocale, 'crop' => $row->id]) }}">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>

                                    <a class="btn btn-icon btn-light"
                                        href="{{ route('customer.crops-feed.crops.edit', ['locale' => $currentLocale, 'crop' => $row->id]) }}">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>

                                    <form method="POST"
                                        action="{{ route('customer.farmers.force-delete', ['locale' => $currentLocale, 'farmer' => $row->id]) }}">
                                        @csrf
                                        @method('DELETE')

                                        <button class="btn btn-icon btn-danger-soft">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>

                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-box-open fa-2x mb-3"></i>
                                <p class="mb-0">{{ __('farmers.empty.no_farmers') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>
    @endsection
