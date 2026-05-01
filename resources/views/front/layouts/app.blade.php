<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

@include('front.layouts.partials.head')

<body class="{{ $bodyClass ?? '' }}">

    @include('front.layouts.partials.navbar')

    @yield('content')

    @include('front.layouts.partials.footer')

    @include('front.layouts.partials.scripts')
    @stack('scripts')

</body>

</html>
