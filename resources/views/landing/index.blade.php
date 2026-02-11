@extends('layouts.landing')

@section('title', 'Home' . 'EL-Sawady')

@section('content')

    @include('landing.partials.hero')
    @include('landing.partials.features')
    @include('landing.partials.pricing')
    @include('landing.partials.cta')

@endsection