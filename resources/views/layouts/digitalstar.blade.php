<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Digital Star Consultants | Your Digital Partner in Tanzania')</title>
    <meta name="description" content="@yield('description', 'Digital Star Consultants helps people and businesses in Tanzania with government applications, business registration, printing, branding and IT.')">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/digitalstar.css') }}">
    @stack('head')
</head>
<body>
    @include('partials.ds-header')

    <main>
        @yield('content')
    </main>

    @include('partials.ds-footer')

    {{-- Lucide icons, self-hosted so they work under our CSP and offline --}}
    <script defer src="{{ asset('js/icons.js') }}?v={{ filemtime(public_path('js/icons.js')) }}"></script>
    @stack('scripts')
    <script defer src="{{ asset('js/digitalstar.js') }}?v={{ filemtime(public_path('js/digitalstar.js')) }}"></script>
</body>
</html>
