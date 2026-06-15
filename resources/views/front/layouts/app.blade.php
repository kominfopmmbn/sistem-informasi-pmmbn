<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

@include('front.layouts.partials.head')

<body class="{{ $bodyClass ?? '' }}">
    <div id="page-transition-overlay" class="page-transition-overlay" aria-hidden="true">
    <div class="page-transition-logo-wrap">
        <div class="spinner-ring-outer"></div>
        <div class="spinner-ring"></div>
        <img src="{{ asset('assets/img/logo/pmmbn.png') }}" alt="PMMBN" class="page-transition-logo">
    </div>
</div>

    @include('front.layouts.partials.navbar')

    @yield('content')

    @include('front.layouts.partials.footer')

    @include('front.layouts.partials.scripts')
    @stack('scripts')

</body>

</html>
