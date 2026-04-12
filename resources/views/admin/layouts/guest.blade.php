<!doctype html>

<html
  lang="en"
  class="layout-wide customizer-hide"
  dir="ltr"
  data-skin="default"
  data-assets-path="{{ asset('assets') }}/"
  data-template="vertical-menu-template-no-customizer"
  data-bs-theme="light">
  @include('admin.layouts.partials.head')
  <body>
    @yield('content')
    @include('admin.layouts.partials.scripts')
  </body>
</html>
