@extends('layouts.customer.dashboard')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">{{ __('hr.titles.job_titles') }}</h3>
        <a class="btn btn-primary"
           href="{{ route('customer.hr.job-titles.create', ['locale' => request()->route('locale')]) }}">
            {{ __('hr.actions.add_job_title') }}
        </a>
    </div>

    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
    @if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th style="width:80px">{{ __('hr.fields.id') }}</th>
                        <th>{{ __('hr.fields.name') }}</th>
                        <th style="width:180px">{{ __('hr.fields.code') }}</th>
                        <th class="text-end" style="width:200px">{{ __('hr.fields.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jobTitles as $t)
                        <tr>
                            <td>{{ $t->id }}</td>
                            <td>{{ $t->name }}</td>
                            <td>{{ $t->code ?? '-' }}</td>
                            <td class="text-end">
                                <a class="btn btn-sm btn-outline-secondary"
                                   href="{{ route('customer.hr.job-titles.edit', ['locale' => request()->route('locale'), 'job_title' => $t->id]) }}">
                                    {{ __('hr.actions.edit') }}
                                </a>

                                <form class="d-inline"
                                      method="POST"
                                      action="{{ route('customer.hr.job-titles.destroy', ['locale' => request()->route('locale'), 'job_title' => $t->id]) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"
                                            onclick="return confirm('{{ __('hr.messages.confirm_delete_job_title') }}')">
                                        {{ __('hr.actions.delete') }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">
                                {{ __('hr.empty.no_job_titles') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-3">
                {{ $jobTitles->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
