@extends('layouts.landing')

@section('title', $title . ' - EL-Sawady')

@section('content')
    <div class="container py-5" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
        <div class="row justify-content-center">
            <div class="col-md-9">
                <div class="card border-0 shadow-sm p-4 p-lg-5" style="border-radius: 24px;">
                    <h1 class="fw-bold mb-3" style="color: #2D5A27;">{{ $title }}</h1>
                    <p class="text-muted small mb-4">{{ __('auth.last_updated') }}: {{ date('Y-m-d') }}</p>

                    <hr class="mb-5 opacity-10">

                    <div class="legal-content" style="line-height: 1.8; color: #444;">
                        {!! $content !!}
                    </div>

                    <div class="mt-5">
                        <a href="{{ url()->previous() }}" class="btn btn-farm px-4 py-2"
                            style="background-color: #2D5A27; color: white; border-radius: 10px;">
                            {{ app()->getLocale() == 'ar' ? 'العودة' : 'Back' }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection