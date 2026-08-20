<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Digital Star Consultants')</title>
    <meta name="description" content="@yield('meta_description', 'Digital Star Consultants — professional technology consulting plus fast, transparent government and business service requests.')">

    <meta property="og:type" content="website">
    <meta property="og:title" content="@yield('title', 'Digital Star Consultants')">
    <meta property="og:description" content="@yield('meta_description', 'Professional technology consulting plus fast, transparent government and business service requests.')">
    <meta name="twitter:card" content="summary_large_image">

    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="min-h-screen antialiased">
    <a href="#main" class="skip-link">Skip to main content</a>

    @include('partials.nav')

    <main id="main" tabindex="-1" class="focus:outline-none">
        @include('partials.alerts')
        @yield('content')
    </main>

    @include('partials.footer')

    @stack('scripts')
</body>
</html>
