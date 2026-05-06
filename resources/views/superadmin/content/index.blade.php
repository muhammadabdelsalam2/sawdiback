@extends('layouts.customer.dashboard')

@section('title', __('dashboard.content.title') . ' | EL-Sawady')

@section('content')
    @php
        $activeLocale = $currentLocale ?? session('locale_full', 'en-SA');
    @endphp

    <div class="dashboard-body">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <div>
                <h1 class="dashboard-title mb-1">{{ __('dashboard.content.title') }}</h1>
                <p class="dashboard-desc mb-0">{{ __('dashboard.content.desc') }}</p>
            </div>
            <a href="{{ route('superadmin.content.create', ['locale' => $activeLocale]) }}" class="btn btn-primary-green">
                <i class="fa-solid fa-plus me-2"></i>{{ __('dashboard.content.create') }}
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="chart-card">
            <div class="table-responsive">
                <table class="table registry-table mb-0">
                    <thead>
                        <tr>
                            <th>{{ __('dashboard.content.id') }}</th>
                            <th>{{ __('dashboard.content.title_col') }}</th>
                            <th>{{ __('dashboard.content.video') }}</th>
                            <th class="text-end">{{ __('dashboard.content.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($contents as $content)
                            <tr>
                                <td>{{ $content->id }}</td>
                                <td>{{ $content->title[app()->getLocale()] ?? $content->title['en'] ?? $content->title['ar'] ?? '' }}</td>
                                <td>
                                    @if ($content->video_url)
                                        <a href="{{ $content->video_url }}" target="_blank" class="btn btn-info btn-sm">
                                            <i class="fa-solid fa-play me-1"></i>{{ __('dashboard.content.view_video') }}
                                        </a>
                                    @elseif ($content->video)
                                        <video width="100" height="60" controls>
                                            <source src="{{ asset('storage/' . $content->video) }}" type="video/mp4">
                                            Your browser does not support the video tag.
                                        </video>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            <i class="fa-solid fa-ellipsis-h"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a class="dropdown-item" href="{{ route('superadmin.content.edit', ['locale' => $activeLocale, 'content' => $content]) }}">
                                                    <i class="fa-solid fa-edit me-2"></i>{{ __('dashboard.content.edit') }}
                                                </a>
                                            </li>
                                            <li>
                                                <form method="POST" action="{{ route('superadmin.content.destroy', ['locale' => $activeLocale, 'content' => $content]) }}" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger" onclick="return confirm('{{ __('dashboard.content.confirm_delete') }}')">
                                                        <i class="fa-solid fa-trash me-2"></i>{{ __('dashboard.content.delete') }}
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4">
                                    <div class="empty-state">
                                        <i class="fa-solid fa-video empty-icon"></i>
                                        <h5>{{ __('dashboard.content.no_content') }}</h5>
                                        <p>{{ __('dashboard.content.no_content_desc') }}</p>
                                        <a href="{{ route('superadmin.content.create', ['locale' => $activeLocale]) }}" class="btn btn-primary">
                                            <i class="fa-solid fa-plus me-2"></i>{{ __('dashboard.content.create_first') }}
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($contents->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $contents->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
