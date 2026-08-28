<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ isset($title) ? $title . ' — Digital Star Consultants' : 'Digital Star Consultants — IT & Software Systems Studio' }}</title>
    <meta name="description" content="{{ $metaDescription ?? 'Digital Star Consultants is a premium technology and software engineering consultancy in Dar es Salaam, Tanzania — delivering robust web platforms, internal operations tools, and structured institutional support.' }}">

    {{-- Fonts: Inter (Preloaded & Optimized) --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800,900&display=swap" rel="stylesheet" />

    {{-- Vite Scripts and Styles --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#F8FAFD] text-[#07172C] font-sans antialiased selection:bg-yellow selection:text-navy min-h-screen flex flex-col overflow-x-hidden">
    @php
        $locale = app()->getLocale();
        $isSw = $locale === 'sw';
        $currentRoute = request()->route()?->getName();
        $phones = [
            ['display' => '0783 257 716', 'tel' => '+255783257716', 'wa' => '255783257716'],
            ['display' => '0754 931 751', 'tel' => '+255754931751', 'wa' => '255754931751'],
        ];
    @endphp

    {{-- ========================================================================= --}}
    {{-- STUDIO HEADER & NAVIGATION                                                --}}
    {{-- ========================================================================= --}}
    <header class="sticky top-0 z-50 bg-white/90 backdrop-blur-xl border-b border-[#E2EAF4] transition-all duration-300">
        <div class="shell">
            <div class="flex h-20 items-center justify-between gap-4">
                
                {{-- Brand Identity Monogram & Wordmark --}}
                <a href="{{ route('home') }}" class="flex items-center gap-3 group shrink-0 focus-ring rounded-xl py-1">
                    <div class="relative flex h-11 w-11 items-center justify-center rounded-xl bg-gradient-to-br from-navy to-navy-dark text-yellow shadow-md border border-white/15 group-hover:scale-105 group-hover:shadow-lg transition-all duration-300">
                        <svg class="h-6 w-6 text-yellow" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2L14.4 8.6L21 11L14.4 13.4L12 20L9.6 13.4L3 11L9.6 8.6L12 2Z" />
                        </svg>
                        <span class="absolute -bottom-1 -right-1 flex h-4 w-4 items-center justify-center rounded-full bg-yellow text-[8px] font-black text-navy shadow-xs">DS</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-base font-black tracking-tight text-ink group-hover:text-blue transition-colors leading-none">
                            Digital Star
                        </span>
                        <span class="text-[10px] font-extrabold uppercase tracking-[0.18em] text-muted mt-1 leading-none">
                            Consultants <span class="text-yellow">•</span> Studio
                        </span>
                    </div>
                </a>

                {{-- Desktop Navigation Capsule --}}
                <nav class="hidden lg:flex items-center gap-1 rounded-full bg-surface border border-line p-1 shadow-xs" aria-label="Main Navigation">
                    @php
                        $navItems = [
                            ['route' => 'home', 'label' => __('site.nav.home')],
                            ['route' => 'public.services.index', 'label' => __('site.nav.services')],
                            ['route' => 'work', 'label' => __('site.nav.work')],
                            ['route' => 'about', 'label' => __('site.nav.about')],
                            ['route' => 'public.track.form', 'label' => __('site.nav.track')],
                            ['route' => 'public.contact.show', 'label' => __('site.nav.contact')],
                        ];
                    @endphp

                    @foreach ($navItems as $item)
                        @php
                            $isActive = $currentRoute === $item['route'] || (str_starts_with($item['route'], 'public.services.') && str_starts_with($currentRoute, 'public.services.'));
                        @endphp
                        <a href="{{ route($item['route']) }}"
                           class="relative px-4 py-2 rounded-full text-xs font-bold transition-all duration-200 {{ $isActive ? 'bg-navy text-yellow shadow-xs' : 'text-muted hover:text-ink hover:bg-white' }}">
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                </nav>

                {{-- Right Actions: Language Switcher & CTA --}}
                <div class="hidden sm:flex items-center gap-3 shrink-0">
                    {{-- Language Toggle --}}
                    <div class="flex items-center rounded-full bg-surface border border-line p-0.5 text-[11px] font-bold">
                        <a href="{{ route('locale.switch', 'en') }}"
                           class="px-2.5 py-1 rounded-full transition-all {{ ! $isSw ? 'bg-navy text-yellow shadow-xs' : 'text-muted hover:text-navy' }}"
                           title="Switch to English">
                            EN
                        </a>
                        <a href="{{ route('locale.switch', 'sw') }}"
                           class="px-2.5 py-1 rounded-full transition-all {{ $isSw ? 'bg-navy text-yellow shadow-xs' : 'text-muted hover:text-navy' }}"
                           title="Badili kwenda Kiswahili">
                            SW
                        </a>
                    </div>

                    {{-- Primary Action Button --}}
                    <a href="{{ route('public.contact.show') }}" class="button-primary !py-2.5 !px-5 !text-xs">
                        <span>{{ __('site.nav.contact') }}</span>
                        <svg class="h-3.5 w-3.5" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path d="M3 8h10M9 4l4 4-4 4"/>
                        </svg>
                    </a>
                </div>

                {{-- Mobile Hamburger Trigger --}}
                <button type="button" id="mobile-menu-btn" class="lg:hidden flex h-10 w-10 items-center justify-center rounded-xl bg-surface border border-line text-navy hover:bg-sky transition-colors focus-ring" aria-label="Toggle Navigation Menu">
                    <svg id="hamburger-icon" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <svg id="close-icon" class="hidden h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>

            </div>
        </div>

        {{-- Mobile Dropdown Drawer --}}
        <div id="mobile-drawer" class="hidden lg:hidden border-t border-line bg-white/95 backdrop-blur-2xl px-6 py-6 transition-all duration-300 shadow-xl">
            <div class="flex flex-col gap-2">
                @foreach ($navItems as $item)
                    @php
                        $isActive = $currentRoute === $item['route'];
                    @endphp
                    <a href="{{ route($item['route']) }}"
                       class="px-4 py-3 rounded-xl text-sm font-bold transition-all {{ $isActive ? 'bg-navy text-yellow' : 'text-navy hover:bg-surface' }}">
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </div>

            <div class="mt-6 pt-5 border-t border-line flex items-center justify-between gap-4">
                <div class="flex items-center gap-1 bg-surface border border-line rounded-full p-1 text-xs font-bold">
                    <a href="{{ route('locale.switch', 'en') }}" class="px-3 py-1 rounded-full {{ ! $isSw ? 'bg-navy text-yellow' : 'text-muted' }}">EN</a>
                    <a href="{{ route('locale.switch', 'sw') }}" class="px-3 py-1 rounded-full {{ $isSw ? 'bg-navy text-yellow' : 'text-muted' }}">SW</a>
                </div>
                <a href="{{ route('public.contact.show') }}" class="button-primary !py-2.5 !px-5 !text-xs">
                    <span>{{ __('site.nav.contact') }}</span>
                    <svg class="h-3.5 w-3.5" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 8h10M9 4l4 4-4 4"/></svg>
                </a>
            </div>
        </div>
    </header>

    {{-- ========================================================================= --}}
    {{-- MAIN PAGE CONTENT                                                         --}}
    {{-- ========================================================================= --}}
    <main class="flex-1">
        @yield('content')
    </main>

    {{-- ========================================================================= --}}
    {{-- STUDIO FOOTER                                                             --}}
    {{-- ========================================================================= --}}
    <footer class="bg-navy-dark text-white border-t border-line-dark pt-16 pb-12 mt-auto">
        <div class="shell">
            <div class="grid gap-10 sm:grid-cols-2 lg:grid-cols-4 pb-14 border-b border-white/10">
                
                {{-- Column 1: Studio Monogram & Narrative --}}
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-navy border border-yellow/30 text-yellow shadow-sm">
                            <svg class="h-5 w-5 text-yellow" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2L14.4 8.6L21 11L14.4 13.4L12 20L9.6 13.4L3 11L9.6 8.6L12 2Z" />
                            </svg>
                        </div>
                        <div>
                            <span class="text-base font-black tracking-tight text-white block leading-none">Digital Star</span>
                            <span class="text-[10px] font-bold uppercase tracking-[0.18em] text-white/50 mt-1 block leading-none">Consultants • Studio</span>
                        </div>
                    </div>
                    <p class="text-xs text-white/70 leading-relaxed max-w-sm">
                        {{ $isSw ? 'Uhandisi wa programu, mifumo ya kidijitali, na ushauri wa kiteknolojia kwa biashara na taasisi nchini Tanzania.' : 'Software engineering, digital systems architecture, and technology consulting for enterprises and institutions across Tanzania.' }}
                    </p>
                    <div class="text-xs text-white/60 pt-2 space-y-1">
                        <p class="font-bold text-white/90">Mbagala · Dar es Salaam, Tanzania</p>
                        <p>Near Puma Petrol Station</p>
                    </div>
                </div>

                {{-- Column 2: Software & Solutions --}}
                <div class="space-y-3">
                    <h3 class="text-xs font-black uppercase tracking-[0.18em] text-yellow">
                        {{ $isSw ? 'Huduma na Mifumo' : 'Services & Solutions' }}
                    </h3>
                    <ul class="space-y-2 text-xs text-white/75">
                        <li><a href="{{ route('public.services.index') }}" class="hover:text-yellow transition-colors">Custom Web Applications</a></li>
                        <li><a href="{{ route('public.services.index') }}" class="hover:text-yellow transition-colors">REST APIs & Backend Engines</a></li>
                        <li><a href="{{ route('public.services.index') }}" class="hover:text-yellow transition-colors">Business Operations Dashboards</a></li>
                        <li><a href="{{ route('public.services.index') }}" class="hover:text-yellow transition-colors">Cloud Infrastructure & Databases</a></li>
                        <li><a href="{{ route('work') }}" class="hover:text-yellow transition-colors">Shipped Client Work & Case Studies</a></li>
                    </ul>
                </div>

                {{-- Column 3: Institutional & Workflow Support --}}
                <div class="space-y-3">
                    <h3 class="text-xs font-black uppercase tracking-[0.18em] text-white/50">
                        {{ $isSw ? 'Usaidizi na Miongozo' : 'Support & Portals' }}
                    </h3>
                    <ul class="space-y-2 text-xs text-white/75">
                        <li><a href="{{ route('public.track.form') }}" class="hover:text-yellow transition-colors">Track Request by Reference Code</a></li>
                        <li><a href="{{ route('public.services.index', ['category' => 'official-services']) }}" class="hover:text-yellow transition-colors">NIDA, TRA & BRELA Workflows</a></li>
                        <li><a href="{{ route('public.services.index', ['category' => 'design-print']) }}" class="hover:text-yellow transition-colors">Brand Identity & Digital Print</a></li>
                        <li><a href="{{ route('about') }}" class="hover:text-yellow transition-colors">About Our Engineering Team</a></li>
                        <li><a href="{{ route('login') }}" class="text-white/40 hover:text-white transition-colors">Staff Portal Login &rarr;</a></li>
                    </ul>
                </div>

                {{-- Column 4: Direct Engineering Line --}}
                <div class="space-y-3">
                    <h3 class="text-xs font-black uppercase tracking-[0.18em] text-yellow">
                        {{ $isSw ? 'Mawasiliano' : 'Direct Contact' }}
                    </h3>
                    <div class="space-y-2 text-xs text-white/80">
                        <p class="text-white/50">Direct Telephone:</p>
                        <p><a href="tel:{{ $phones[0]['tel'] }}" class="font-bold text-white hover:text-yellow transition-colors">{{ $phones[0]['display'] }}</a></p>
                        <p><a href="tel:{{ $phones[1]['tel'] }}" class="font-bold text-white hover:text-yellow transition-colors">{{ $phones[1]['display'] }}</a></p>
                    </div>
                    <div class="pt-3">
                        <a href="https://wa.me/{{ $phones[0]['wa'] }}" class="wa-btn !py-2 !px-4 !text-xs" target="_blank" rel="noopener">
                            <span>Chat on WhatsApp</span>
                        </a>
                    </div>
                </div>

            </div>

            {{-- Footer Bottom Baseline --}}
            <div class="mt-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-white/50">
                <p>&copy; {{ date('Y') }} Digital Star Consultants. All rights reserved.</p>
                <div class="flex items-center gap-6">
                    <a href="{{ route('about') }}" class="hover:text-white transition-colors">About</a>
                    <a href="{{ route('public.contact.show') }}" class="hover:text-white transition-colors">Contact</a>
                    <a href="{{ route('locale.switch', $isSw ? 'en' : 'sw') }}" class="hover:text-white transition-colors">
                        {{ $isSw ? 'English' : 'Kiswahili' }}
                    </a>
                </div>
            </div>
        </div>
    </footer>

    {{-- Floating WhatsApp Assistant Capsule --}}
    <div class="fixed bottom-6 right-6 z-40">
        <a href="https://wa.me/{{ $phones[0]['wa'] }}?text={{ urlencode('Hello Digital Star Consultants, I would like to inquire about your software and digital services.') }}"
           class="flex h-13 w-13 items-center justify-center rounded-full bg-emerald-500 text-white shadow-xl hover:bg-emerald-600 hover:scale-110 active:scale-95 transition-all duration-300 focus-ring"
           target="_blank" rel="noopener" aria-label="Contact Digital Star on WhatsApp">
            <svg class="h-7 w-7 fill-current" viewBox="0 0 24 24">
                <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.664-.698c.97.53 1.95.817 2.796.817 3.18 0 5.767-2.587 5.768-5.766.001-3.182-2.585-5.769-5.768-5.769zm3.393 8.307c-.143.403-.83.743-1.15.787-.319.043-.729.074-2.378-.611-1.649-.684-2.709-2.366-2.791-2.476-.082-.11-1.009-1.343-1.009-2.56 0-1.218.636-1.817.863-2.065.227-.247.498-.31.663-.31.166 0 .332.002.477.009.153.007.359-.059.562.43.207.499.704 1.716.766 1.841.062.124.103.27.021.433-.083.163-.124.266-.247.408-.124.143-.261.319-.373.428-.124.12-.254.25-.11.498.144.247.639 1.054 1.371 1.706.942.839 1.737 1.099 1.984 1.222.247.124.392.103.537-.062.145-.166.621-.723.787-.971.165-.248.331-.207.56-.124.228.083 1.448.683 1.696.807.248.124.414.186.477.29.062.103.062.6-.081 1.003z"/>
            </svg>
        </a>
    </div>

    {{-- Interactive Mobile Navigation Script --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const btn = document.getElementById('mobile-menu-btn');
            const drawer = document.getElementById('mobile-drawer');
            const hamburger = document.getElementById('hamburger-icon');
            const close = document.getElementById('close-icon');

            if (btn && drawer) {
                btn.addEventListener('click', () => {
                    const isHidden = drawer.classList.contains('hidden');
                    drawer.classList.toggle('hidden', !isHidden);
                    hamburger.classList.toggle('hidden', isHidden);
                    close.classList.toggle('hidden', !isHidden);
                });
            }
        });
    </script>
</body>
</html>
