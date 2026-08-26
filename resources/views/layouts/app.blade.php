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
        .reveal { opacity: 0; transform: translateY(18px); transition: opacity .55s ease, transform .55s ease; }
        .reveal.is-visible { opacity: 1; transform: translateY(0); }
        .float-slow { animation: floaty 7s ease-in-out infinite; }
        @keyframes floaty { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-8px); } }
        @media (prefers-reduced-motion: reduce) {
            .reveal { opacity: 1; transform: none; transition: none; }
            .float-slow { animation: none; }
        }
        .lang-pill { border: 1px solid #dbe4ef; border-radius: 999px; display: inline-flex; overflow: hidden; font-size: 12px; font-weight: 800; }
        .lang-pill a { padding: 6px 12px; color: #62728a; text-decoration: none; }
        .lang-pill a.is-active { background: #081b35; color: #f5c84b; }
        .wa-btn { background: #25D366; color: #fff; border-radius: 999px; font-weight: 800; font-size: 13px; padding: 10px 16px; display: inline-flex; align-items: center; gap: 8px; }
        .wa-btn:hover { filter: brightness(1.05); }
        .mobile-nav { display: none; }
        .mobile-nav.is-open { display: block; }
        @media (max-width: 768px) {
            .desktop-nav { display: none !important; }
        }
    </style>
</head>
<body class="min-h-screen bg-paper text-ink antialiased">
    @php
        $locale = app()->getLocale();
        $phones = [
            ['display' => '0783 257 716', 'tel' => '+255783257716', 'wa' => '255783257716'],
            ['display' => '0754 931 751', 'tel' => '+255754931751', 'wa' => '255754931751'],
        ];
    @endphp

    <header class="sticky top-0 z-40 border-b border-line/80 bg-paper/90 backdrop-blur-md">
        <div class="shell flex items-center justify-between gap-3 py-3.5">
            <a href="{{ route('home') }}" class="flex items-center gap-3 no-underline">
                <span class="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-ink text-sm font-black text-yellow shadow-lg shadow-ink/10">DS</span>
                <span class="hidden sm:block">
                    <span class="block text-sm font-black text-ink">{{ __('site.brand') }}</span>
                    <span class="block text-[11px] font-semibold uppercase tracking-[0.14em] text-muted">{{ __('site.tagline') }}</span>
                </span>
            </a>

            <nav class="desktop-nav hidden items-center gap-0.5 md:flex" aria-label="Primary">
                <a href="{{ route('home') }}" class="rounded-full px-3 py-2 text-sm font-bold text-ink/80 transition hover:bg-sky {{ request()->routeIs('home') ? 'bg-sky text-ink' : '' }}">{{ __('site.nav.home') }}</a>
                <a href="{{ route('public.services.index') }}" class="rounded-full px-3 py-2 text-sm font-bold text-ink/80 transition hover:bg-sky {{ request()->routeIs('public.services.*') ? 'bg-sky text-ink' : '' }}">{{ __('site.nav.services') }}</a>
                <a href="{{ route('work') }}" class="rounded-full px-3 py-2 text-sm font-bold text-ink/80 transition hover:bg-sky {{ request()->routeIs('work') ? 'bg-sky text-ink' : '' }}">{{ __('site.nav.work') }}</a>
                <a href="{{ route('about') }}" class="rounded-full px-3 py-2 text-sm font-bold text-ink/80 transition hover:bg-sky {{ request()->routeIs('about') ? 'bg-sky text-ink' : '' }}">{{ __('site.nav.about') }}</a>
                <a href="{{ route('public.track.form') }}" class="rounded-full px-3 py-2 text-sm font-bold text-ink/80 transition hover:bg-sky {{ request()->routeIs('public.track.*') ? 'bg-sky text-ink' : '' }}">{{ __('site.nav.track') }}</a>
                <a href="{{ route('public.contact.show') }}" class="rounded-full px-3 py-2 text-sm font-bold text-ink/80 transition hover:bg-sky {{ request()->routeIs('public.contact.*') ? 'bg-sky text-ink' : '' }}">{{ __('site.nav.contact') }}</a>
            </nav>

            <div class="flex items-center gap-2">
                <div class="lang-pill" title="{{ __('site.common.language') }}">
                    <a href="{{ route('locale.switch', 'en') }}" class="{{ $locale === 'en' ? 'is-active' : '' }}">EN</a>
                    <a href="{{ route('locale.switch', 'sw') }}" class="{{ $locale === 'sw' ? 'is-active' : '' }}">SW</a>
                </div>
                <a href="{{ route('public.services.index') }}" class="button-primary !hidden !py-2.5 !px-4 text-sm sm:!inline-flex">{{ __('site.nav.browse_services') }}</a>
                <button type="button" class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-line bg-white text-ink md:hidden" data-mobile-nav-toggle aria-expanded="false" aria-controls="mobile-nav">
                    <span class="sr-only">Menu</span>
                    <span class="flex flex-col gap-1.5">
                        <span class="block h-0.5 w-5 bg-current"></span>
                        <span class="block h-0.5 w-5 bg-current"></span>
                        <span class="block h-0.5 w-5 bg-current"></span>
                    </span>
                </button>
            </div>
        </div>

        <div id="mobile-nav" class="mobile-nav border-t border-line bg-paper px-4 py-3 md:hidden">
            <div class="flex flex-col gap-1">
                <a href="{{ route('home') }}" class="rounded-xl px-3 py-2.5 text-sm font-bold">{{ __('site.nav.home') }}</a>
                <a href="{{ route('public.services.index') }}" class="rounded-xl px-3 py-2.5 text-sm font-bold">{{ __('site.nav.services') }}</a>
                <a href="{{ route('work') }}" class="rounded-xl px-3 py-2.5 text-sm font-bold">{{ __('site.nav.work') }}</a>
                <a href="{{ route('about') }}" class="rounded-xl px-3 py-2.5 text-sm font-bold">{{ __('site.nav.about') }}</a>
                <a href="{{ route('public.track.form') }}" class="rounded-xl px-3 py-2.5 text-sm font-bold">{{ __('site.nav.track') }}</a>
                <a href="{{ route('public.contact.show') }}" class="rounded-xl px-3 py-2.5 text-sm font-bold">{{ __('site.nav.contact') }}</a>
                <a href="https://wa.me/{{ $phones[0]['wa'] }}" class="wa-btn mt-2 justify-center" target="_blank" rel="noopener">WhatsApp {{ $phones[0]['display'] }}</a>
            </div>
        </div>
    </header>

    <main>
        @if (session('status') || session('success'))
            <div class="shell pt-6">
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-800">
                    {{ session('status') ?? session('success') }}
                </div>
            </div>
        @endif
        @yield('content')
    </main>

    <footer class="mt-20 border-t border-line bg-ink text-white">
        <div class="shell grid gap-10 py-14 md:grid-cols-[1.3fr_1fr_1fr]">
            <div>
                <div class="flex items-center gap-3">
                    <span class="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-yellow text-sm font-black text-ink">DS</span>
                    <div>
                        <p class="text-sm font-black">{{ __('site.brand') }}</p>
                        <p class="text-xs text-white/55">{{ __('site.tagline') }}</p>
                    </div>
                </div>
                <p class="mt-5 max-w-md text-sm leading-relaxed text-white/70">{{ __('site.home.hero_lead') }}</p>
                <div class="mt-5 flex flex-wrap gap-2">
                    @foreach ($phones as $p)
                        <a href="https://wa.me/{{ $p['wa'] }}" class="wa-btn" target="_blank" rel="noopener">{{ __('site.footer.whatsapp') }} {{ $p['display'] }}</a>
                    @endforeach
                </div>
            </div>
            <div>
                <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-yellow">{{ __('site.footer.explore') }}</p>
                <ul class="mt-4 space-y-2 text-sm text-white/75">
                    <li><a class="hover:text-white" href="{{ route('home') }}">{{ __('site.nav.home') }}</a></li>
                    <li><a class="hover:text-white" href="{{ route('public.services.index') }}">{{ __('site.nav.services') }}</a></li>
                    <li><a class="hover:text-white" href="{{ route('work') }}">{{ __('site.nav.work') }}</a></li>
                    <li><a class="hover:text-white" href="{{ route('about') }}">{{ __('site.nav.about') }}</a></li>
                    <li><a class="hover:text-white" href="{{ route('public.track.form') }}">{{ __('site.nav.track') }}</a></li>
                    <li><a class="hover:text-white" href="{{ route('login') }}">{{ __('site.nav.staff_login') }}</a></li>
                </ul>
            </div>
            <div>
                <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-yellow">{{ __('site.footer.contact') }}</p>
                <ul class="mt-4 space-y-2 text-sm text-white/75">
                    <li>{{ __('site.footer.location') }}</li>
                    @foreach ($phones as $p)
                        <li>
                            <a class="hover:text-white" href="tel:{{ $p['tel'] }}">{{ $p['display'] }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
        <div class="border-t border-white/10">
            <div class="shell flex flex-col gap-2 py-5 text-xs text-white/45 sm:flex-row sm:items-center sm:justify-between">
                <p>&copy; {{ date('Y') }} {{ __('site.brand') }}. {{ __('site.footer.rights') }}</p>
                <p>{{ __('site.footer.track_hint') }}</p>
            </div>
        </div>
    </footer>

    <div class="admin-toast-stack" data-toast-stack aria-live="polite" aria-atomic="true"></div>
    @stack('scripts')
    <script>
        document.querySelector('[data-mobile-nav-toggle]')?.addEventListener('click', function () {
            const nav = document.getElementById('mobile-nav');
            const open = nav.classList.toggle('is-open');
            this.setAttribute('aria-expanded', String(open));
        });
        const io = new IntersectionObserver((entries) => {
            entries.forEach((e) => { if (e.isIntersecting) e.target.classList.add('is-visible'); });
        }, { threshold: 0.12 });
        document.querySelectorAll('.reveal').forEach((el) => io.observe(el));
    </script>
</body>
</html>
