<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Digital Star Consultants — digital, government, business, creative and technology services in Tanzania.">
    <title>{{ $title ?? 'Digital Star Consultants' }}</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="site-body public-redesign">
<header class="ds-nav" id="site-nav">
    <div class="ds-nav-inner">
        <a class="ds-brand" href="{{ route('home') }}" aria-label="Digital Star Consultants home">
            <span class="ds-brand-mark"><img src="{{ asset('images/digital-star-mark.svg') }}" alt=""></span>
            <span class="ds-brand-text"><strong>DIGITAL STAR</strong><small>CONSULTANTS</small></span>
        </a>
        <button class="ds-menu-toggle" type="button" aria-expanded="false" aria-controls="ds-mobile-nav" aria-label="Open menu"><span></span><span></span><span></span></button>
        <nav class="ds-desktop-nav" aria-label="Primary navigation">
            <a class="{{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Home</a>
            <a class="{{ request()->routeIs('public.services.*') ? 'active' : '' }}" href="{{ route('public.services.index') }}">Services</a>
            <a class="{{ request()->routeIs('public.track.*') ? 'active' : '' }}" href="{{ route('public.track.form') }}">Track Application</a>
            <a class="{{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('about') }}">About Us</a>
            <a class="{{ request()->routeIs('public.contact.*') ? 'active' : '' }}" href="{{ route('public.contact.show') }}">Contact Us</a>
        </nav>
        <div class="ds-nav-actions">
            <a class="ds-lang" href="{{ route('locale.switch',['locale'=>app()->getLocale()==='en'?'sw':'en']) }}">{{ app()->getLocale()==='en' ? 'SW' : 'EN' }}</a>
            <a class="ds-button ds-button-gold" href="{{ route('public.services.index') }}">Apply Now <span>→</span></a>
        </div>
    </div>
    <nav class="ds-mobile-nav" id="ds-mobile-nav" aria-label="Mobile navigation">
        <a class="{{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Home</a>
        <a class="{{ request()->routeIs('public.services.*') ? 'active' : '' }}" href="{{ route('public.services.index') }}">Services</a>
        <a class="{{ request()->routeIs('public.track.*') ? 'active' : '' }}" href="{{ route('public.track.form') }}">Track Application</a>
        <a class="{{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('about') }}">About Us</a>
        <a href="{{ route('public.contact.show') }}">Contact Us</a>
        <div><a class="ds-button ds-button-gold" href="{{ route('public.services.index') }}">Apply Now →</a><a class="ds-lang" href="{{ route('locale.switch',['locale'=>app()->getLocale()==='en'?'sw':'en']) }}">{{ app()->getLocale()==='en' ? 'Kiswahili' : 'English' }}</a></div>
    </nav>
</header>
<main>@yield('content')</main>
<footer class="ds-footer">
    <div class="ds-footer-top">
        <div class="ds-footer-brand">
            <a class="ds-brand ds-brand-footer" href="{{ route('home') }}">
                <span class="ds-brand-mark"><img src="{{ asset('images/digital-star-mark.svg') }}" alt=""></span>
                <span class="ds-brand-text"><strong>DIGITAL STAR</strong><small>CONSULTANTS</small></span>
            </a>
            <p>Practical digital services for people, businesses and organizations — from online applications to technology solutions.</p>
            <div class="ds-socials"><a href="{{ route('public.contact.show') }}" aria-label="Contact">✉</a><a href="{{ route('public.services.index') }}" aria-label="Services">✦</a><a href="{{ route('public.track.form') }}" aria-label="Track">⌁</a></div>
        </div>
        <div><h4>QUICK LINKS</h4><a class="{{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Home</a><a class="{{ request()->routeIs('public.services.*') ? 'active' : '' }}" href="{{ route('public.services.index') }}">Services</a><a class="{{ request()->routeIs('public.track.*') ? 'active' : '' }}" href="{{ route('public.track.form') }}">Track Application</a><a class="{{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('about') }}">About Us</a><a href="{{ route('public.contact.show') }}">Contact Us</a></div>
        <div><h4>OUR SERVICES</h4><a href="{{ route('public.services.index',['category'=>'online-government-services']) }}">Government Services</a><a href="{{ route('public.services.index',['category'=>'business-services']) }}">Business Services</a><a href="{{ route('public.services.index',['category'=>'printing-graphics-design']) }}">Printing & Creative</a><a href="{{ route('public.services.index',['category'=>'it-tech-consultancy']) }}">IT & Technology</a></div>
        <div><h4>CONTACT</h4><p class="ds-contact-line">Dar es Salaam, Tanzania</p><p class="ds-contact-line">+255 123 456 789</p><p class="ds-contact-line">info@digitalstar.co.tz</p><p class="ds-contact-line">Mon – Sat · 8:00 AM – 6:00 PM</p></div>
    </div>
    <div class="ds-footer-bottom"><span>© {{ date('Y') }} Digital Star Consultants. All Rights Reserved.</span><span><a href="{{ route('about') }}">Privacy & Terms</a></span><strong>Digital services. Done with care.</strong></div>
</footer>
</body>
</html>
