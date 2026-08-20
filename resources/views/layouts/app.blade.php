<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Digital Star Consultants')</title>
    <meta name="description" content="Digital Star Consultants helps people and organizations move important work forward through clear, capable digital services.">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen">
    <a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-50 focus:rounded-full focus:bg-yellow focus:px-4 focus:py-3 focus:text-sm focus:font-bold focus:text-ink">Skip to content</a>
    @include('partials.nav')
    @include('partials.alerts')
    <main id="main-content">@yield('content')</main>
    @include('partials.footer')
    @stack('scripts')
</body>
</html>
