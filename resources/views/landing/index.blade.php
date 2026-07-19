@extends('layouts.landing')

@section('title', 'Home' . 'EL-Sawady')

@section('content')

    @include('landing.partials.hero')

    @include('landing.partials.features')
    @include('landing.partials.how_it_works')
    @include('landing.partials.best_selling', ['bestSellingProducts' => $bestSellingProducts])
    @include('landing.partials.products', $products)
    @include('landing.partials.cta')
    @include('landing.partials.contact', ['contactInfo' => $contactInfo])
@endsection
