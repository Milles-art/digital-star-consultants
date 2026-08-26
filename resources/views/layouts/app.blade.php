<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ trim(($title ?? __('site.brand')).' | '.__('site.brand')) }}</title>
    <meta name="description" content="{{ $metaDescription ?? __('site.home.hero_lead') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        {!! file_exists(resource_path('css/public-extras.css')) ? file_get_contents(resource_path('css/public-extras.css')) : '' !!}
        .lang-pill { border:1px solid #dbe4ef; border-radius:999px; display:inline-flex; overflow:hidden; font-size:12px; font-weight:800; }
        .lang-pill a { padding:6px 12px; color:#62728a; text-decoration:none; }
        .lang-pill a.is-active { background:#081b35; color:#f5c84b; }
        .wa-btn { background:#25D366; color:#fff!important; border-radius:999px; font-weight:800; font-size:13px; padding:10px 16px; display:inline-flex; align-items:center; gap:8px; text-decoration:none; }
        .wa-btn:hover { filter:brightness(1.06); }
        .mobile-nav { display:none; }
        .mobile-nav.is-open { display:block; }
        @media (max-width:768px){ .desktop-nav{display:none!important;} }
        .sticky-wa {
            position:fixed; bottom:20px; right:20px; z-index:50;
            box-shadow:0 12px 30px #25D36655;
        }
    </style>
</head>
<body class="min-h-screen bg-white text-ink antialiased">
@php
    $locale = app()->getLocale();
    $phones = [
        ['display' => '0783 257 716', 'tel' => '+255783257716', 'wa' => '255783257716'],
        ['display' => '0754 931 751', 'tel' => '+255754931751', 'wa' => '255754931751'],
    ];
@endphp

<header class="sticky top-0 z-40 border-b border-line/70 bg-white/80 backdrop-blur-xl">
    <div class="shell flex items-center justify-between gap-3 py-3.5">
        <a href="{{ route('home') }}" class="flex items-center gap-3 no-underline">
            <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-ink text-sm font-black text-yellow">DS</span>
            <span class="hidden sm:block">
                <span class="block text-sm font-black text-ink">{{ __('site.brand') }}</span>
                <span class="block text-[11px] font-semibold text-muted">{{ __('site.tagline') }}</span>
            </span>
        </a>

        <nav class="desktop-nav hidden items-center gap-0.5 md:flex" aria-label="Primary">
            @foreach ([
                ['home', route('home')],
                ['services', route('public.services.index')],
                ['work', route('work')],
                ['about', route('about')],
                ['track', route('public.track.form')],
                ['contact', route('public.contact.show')],
            ] as [$key, $url])
                <a href="{{ $url }}" class="rounded-full px-3 py-2 text-sm font-bold text-ink/75 transition hover:bg-sky hover:text-ink">{{ __('site.nav.'.$key) }}</a>
            @endforeach
        </nav>

        <div class="flex items-center gap-2">
            <div class="lang-pill">
                <a href="{{ route('locale.switch', 'en') }}" class="{{ $locale === 'en' ? 'is-active' : '' }}">EN</a>
                <a href="{{ route('locale.switch', 'sw') }}" class="{{ $locale === 'sw' ? 'is-active' : '' }}">SW</a>
            </div>
            <a href="https://wa.me/{{ $phones[0]['wa'] }}" class="wa-btn !hidden sm:!inline-flex" target="_blank" rel="noopener">WhatsApp</a>
            <button type="button" class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-line md:hidden" data-mobile-nav-toggle aria-expanded="false">
                <span class="sr-only">Menu</span>
                <span class="flex flex-col gap-1.5"><span class="block h-0.5 w-5 bg-ink"></span><span class="block h-0.5 w-5 bg-ink"></span><span class="block h-0.5 w-5 bg-ink"></span></span>
            </button>
        </div>
    </div>
    <div id="mobile-nav" class="mobile-nav border-t border-line bg-white px-4 py-3 md:hidden">
        <div class="flex flex-col gap-1">
            <a href="{{ route('home') }}" class="rounded-xl px-3 py-2.5 text-sm font-bold">{{ __('site.nav.home') }}</a>
            <a href="{{ route('public.services.index') }}" class="rounded-xl px-3 py-2.5 text-sm font-bold">{{ __('site.nav.services') }}</a>
            <a href="{{ route('work') }}" class="rounded-xl px-3 py-2.5 text-sm font-bold">{{ __('site.nav.work') }}</a>
            <a href="{{ route('about') }}" class="rounded-xl px-3 py-2.5 text-sm font-bold">{{ __('site.nav.about') }}</a>
            <a href="{{ route('public.track.form') }}" class="rounded-xl px-3 py-2.5 text-sm font-bold">{{ __('site.nav.track') }}</a>
            <a href="{{ route('public.contact.show') }}" class="rounded-xl px-3 py-2.5 text-sm font-bold">{{ __('site.nav.contact') }}</a>
        </div>
    </div>
</header>

<main>@yield('content')</main>

<footer class="mt-24 border-t border-line bg-ink text-white">
    <div class="shell grid gap-10 py-14 md:grid-cols-[1.4fr_1fr_1fr]">
        <div>
            <div class="flex items-center gap-3">
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-yellow text-sm font-black text-ink">DS</span>
                <div>
                    <p class="text-sm font-black">{{ __('site.brand') }}</p>
                    <p class="text-xs text-white/50">{{ __('site.tagline') }}</p>
                </div>
            </div>
            <p class="mt-5 max-w-md text-sm text-white/65 leading-relaxed">{{ __('site.home.hero_lead') }}</p>
            <div class="mt-5 flex flex-wrap gap-2">
                @foreach ($phones as $p)
                    <a href="https://wa.me/{{ $p['wa'] }}" class="wa-btn" target="_blank" rel="noopener">{{ $p['display'] }}</a>
                @endforeach
            </div>
        </div>
        <div>
            <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-yellow">{{ __('site.footer.explore') }}</p>
            <ul class="mt-4 space-y-2 text-sm text-white/70">
                <li><a href="{{ route('public.services.index') }}" class="hover:text-white">{{ __('site.nav.services') }}</a></li>
                <li><a href="{{ route('work') }}" class="hover:text-white">{{ __('site.nav.work') }}</a></li>
                <li><a href="{{ route('about') }}" class="hover:text-white">{{ __('site.nav.about') }}</a></li>
                <li><a href="{{ route('public.track.form') }}" class="hover:text-white">{{ __('site.nav.track') }}</a></li>
                <li><a href="{{ route('login') }}" class="hover:text-white">{{ __('site.nav.staff_login') }}</a></li>
            </ul>
        </div>
        <div>
            <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-yellow">{{ __('site.footer.contact') }}</p>
            <p class="mt-4 text-sm text-white/70">{{ __('site.footer.location') }}</p>
            @foreach ($phones as $p)
                <p class="mt-2"><a href="tel:{{ $p['tel'] }}" class="text-sm font-bold text-white hover:text-yellow">{{ $p['display'] }}</a></p>
            @endforeach
        </div>
    </div>
    <div class="border-t border-white/10">
        <div class="shell flex flex-col gap-2 py-5 text-xs text-white/40 sm:flex-row sm:justify-between">
            <p>&copy; {{ date('Y') }} {{ __('site.brand') }}. {{ __('site.footer.rights') }}</p>
            <p>{{ __('site.footer.track_hint') }}</p>
        </div>
    </div>
</footer>

<a href="https://wa.me/{{ $phones[0]['wa'] }}" class="wa-btn sticky-wa sm:hidden" target="_blank" rel="noopener">WhatsApp</a>

@stack('scripts')
<script>
document.querySelector('[data-mobile-nav-toggle]')?.addEventListener('click', function () {
    document.getElementById('mobile-nav').classList.toggle('is-open');
});
const io = new IntersectionObserver((entries) => {
    entries.forEach((e) => { if (e.isIntersecting) e.target.classList.add('on'); });
}, { threshold: 0.12 });
document.querySelectorAll('.rise-in').forEach((el) => io.observe(el));
</script>
</body>
</html>
