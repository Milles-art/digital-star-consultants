<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $siteName = 'Digital Star Consultants';
        // Inline @section('name', $value) stores the value HTML-escaped already, so decode
        // back to plain text here and let the single {{ }} at output do one clean escape.
        $decode = fn ($v) => html_entity_decode(trim($v), ENT_QUOTES, 'UTF-8');

        $pageTitle = $decode($__env->yieldContent('title'));
        // Pages may already include the brand; only append it if they don't.
        $fullTitle = $pageTitle === ''
            ? $siteName . ' | ' . __('site.meta.title_suffix')
            : (str_contains($pageTitle, $siteName) ? $pageTitle : $pageTitle . ' | ' . $siteName);

        $pageDescription = $decode($__env->yieldContent(
            'description',
            __('site.meta.description')
        ));
        $canonical = url()->current();
        $ogImage = $decode($__env->yieldContent('og_image', asset('images/ds/hero.jpg')));
        $ogType = $decode($__env->yieldContent('og_type', 'website'));
    @endphp
    <title>{{ $fullTitle }}</title>
    <meta name="description" content="{{ $pageDescription }}">
    <link rel="canonical" href="{{ $canonical }}">

    {{-- Icons: empty favicon.ico is replaced by a real SVG mark --}}
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="alternate icon" href="{{ asset('favicon.ico') }}">
    <meta name="theme-color" content="#041f21">

    {{-- Social sharing (WhatsApp / Facebook / X / LinkedIn) --}}
    <meta property="og:type" content="{{ $ogType }}">
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:title" content="{{ $fullTitle }}">
    <meta property="og:description" content="{{ $pageDescription }}">
    <meta property="og:url" content="{{ $canonical }}">
    <meta property="og:image" content="{{ $ogImage }}">
    <meta property="og:image:alt" content="{{ $siteName }}">
    <meta property="og:locale" content="{{ app()->getLocale() === 'sw' ? 'sw_TZ' : 'en_TZ' }}">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $fullTitle }}">
    <meta name="twitter:description" content="{{ $pageDescription }}">
    <meta name="twitter:image" content="{{ $ogImage }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/digitalstar.css') }}?v={{ filemtime(public_path('css/digitalstar.css')) }}">

    {{-- Local business structured data (helps Google show contact/hours in search) --}}
    <script type="application/ld+json">{!! json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'LocalBusiness',
        'name' => $siteName,
        'description' => $pageDescription,
        'url' => url('/'),
        'image' => asset('images/ds/hero.jpg'),
        'email' => 'hello@digitalstar.co.tz',
        'address' => [
            '@type' => 'PostalAddress',
            'addressLocality' => 'Dar es Salaam',
            'addressCountry' => 'TZ',
        ],
        'areaServed' => 'Tanzania',
        'openingHoursSpecification' => [
            '@type' => 'OpeningHoursSpecification',
            'dayOfWeek' => ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'],
            'opens' => '08:00',
            'closes' => '18:00',
        ],
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>

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
