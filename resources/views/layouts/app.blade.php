<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Digital Star Consultants — modern web, software, creative and IT solutions in Tanzania.">
    <title>{{ $title ?? 'Digital Star Consultants' }}</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="site-body public-redesign">
<header class="dsh-nav" id="site-nav">
    <div class="dsh-nav-inner">
        <a class="dsh-brand" href="{{ route('home') }}" aria-label="Digital Star Consultants home">
            <span class="dsh-brand-mark"><img src="{{ asset('images/digital-star-mark-home.svg') }}" alt=""></span>
            <span class="dsh-brand-text"><strong>Digital Star</strong><small>Consultants</small></span>
        </a>
        <button class="dsh-menu-toggle" type="button" aria-expanded="false" aria-controls="dsh-mobile-nav" aria-label="Open menu"><span></span><span></span><span></span></button>
        <nav class="dsh-desktop-nav" aria-label="Primary navigation">
            <a class="{{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Home</a>
            <a class="{{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('about') }}">About</a>
            <a class="{{ request()->routeIs('public.services.*') ? 'active' : '' }}" href="{{ route('public.services.index') }}">Services</a>
            <a class="{{ request()->routeIs('work') ? 'active' : '' }}" href="{{ route('work') }}">Our Work</a>
            <a class="{{ request()->routeIs('public.contact.*') ? 'active' : '' }}" href="{{ route('public.contact.show') }}">Contact</a>
        </nav>
        <div class="dsh-nav-actions">
            <a class="dsh-search" href="{{ route('public.services.index') }}" aria-label="Search services"></a>
            <a class="dsh-quote-btn" href="{{ route('public.contact.show') }}">Get a Quote <span>→</span></a>
        </div>
    </div>
    <nav class="dsh-mobile-nav" id="dsh-mobile-nav" aria-label="Mobile navigation">
        <a href="{{ route('home') }}">Home</a><a href="{{ route('about') }}">About</a><a href="{{ route('public.services.index') }}">Services</a><a href="{{ route('work') }}">Our Work</a><a href="{{ route('public.contact.show') }}">Contact</a>
        <a class="dsh-mobile-quote" href="{{ route('public.contact.show') }}">Get a Quote →</a>
    </nav>
</header>
<main>@yield('content')</main>
<footer class="dsh-footer">
    <div class="dsh-footer-top">
        <div class="dsh-footer-brand">
            <a class="dsh-footer-brand-link" href="{{ route('home') }}"><span class="dsh-brand-mark"><img src="{{ asset('images/digital-star-mark-home.svg') }}" alt=""></span><span class="dsh-brand-text"><strong>Digital Star</strong><small>Consultants</small></span></a>
            <p>We’re a Tanzanian digital solutions company helping businesses grow through innovation, creativity and technology.</p>
            <div class="dsh-socials"><a href="{{ route('public.contact.show') }}" aria-label="Facebook">f</a><a href="{{ route('public.contact.show') }}" aria-label="Instagram">◎</a><a href="{{ route('work') }}" aria-label="LinkedIn">in</a><a href="{{ route('work') }}" aria-label="YouTube">▶</a></div>
        </div>
        <div><h4>Quick Links</h4><a href="{{ route('home') }}">Home</a><a href="{{ route('about') }}">About</a><a href="{{ route('public.services.index') }}">Services</a><a href="{{ route('work') }}">Our Work</a><a href="{{ route('public.contact.show') }}">Contact</a></div>
        <div><h4>Our Services</h4><a href="{{ route('public.services.index',['category'=>'online-government-services']) }}">Web Development</a><a href="{{ route('public.services.index',['category'=>'business-services']) }}">Software Development</a><a href="{{ route('public.services.index',['category'=>'printing-graphics-design']) }}">Digital Marketing</a><a href="{{ route('public.services.index',['category'=>'it-tech-consultancy']) }}">IT Consultancy</a></div>
        <div><h4>Contact</h4><p>⌖ &nbsp; Dar es Salaam, Tanzania</p><p>✉ &nbsp; info@digitalstar.co.tz</p><p>☎ &nbsp; +255 754 345 678</p><p>◷ &nbsp; Mon - Fri: 8:00 AM - 5:00 PM</p></div>
    </div>
    <div class="dsh-footer-bottom"><span>© {{ date('Y') }} Digital Star Consultants. All rights reserved.</span><span><a href="{{ route('about') }}">Privacy Policy</a> &nbsp;&nbsp; <a href="{{ route('about') }}">Terms of Service</a></span></div>
</footer>
<script>document.addEventListener('DOMContentLoaded',()=>{const b=document.querySelector('.dsh-menu-toggle'),m=document.querySelector('.dsh-mobile-nav');if(b&&m){b.addEventListener('click',()=>{const open=m.classList.toggle('open');b.setAttribute('aria-expanded',open?'true':'false')})}});</script>
</body>
</html>
