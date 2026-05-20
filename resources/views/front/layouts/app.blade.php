<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

@include('front.layouts.partials.head')

<body class="{{ $bodyClass ?? '' }}">
    <div id="page-transition-overlay" class="page-transition-overlay" aria-hidden="true">
        <img src="{{ asset('assets/img/logo/pmmbn.png') }}" alt="PMMBN" class="page-transition-logo">
    </div>

    @include('front.layouts.partials.navbar')

    @yield('content')

    @include('front.layouts.partials.footer')

    @include('front.layouts.partials.scripts')
    @stack('scripts')

</body>

</html>
