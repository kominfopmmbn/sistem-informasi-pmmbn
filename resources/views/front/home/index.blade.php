@extends('front.layouts.app')

@section('content')
    @include('front.home.hero')
    @include('front.home.about')
    @include('front.home.program-unggulan')
    @include('front.home.id-card')
    @include('front.home.berita')
    @include('front.home.stats')
    @include('front.home.peta')
@endsection

@push('scripts')
    @include('front.home.home-inline-scripts')
@endpush
