<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @extends('layouts.head')
        @yield('css')
    </head>

    <body>
        @yield('content')
    </body>

    @extends('layouts.foot')
    @stack('scripts')
</html>
