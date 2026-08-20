<header class="glass-dark sticky top-0 z-40 text-white">
    <div class="w-full flex min-h-[72px] items-center justify-between gap-16 px-6">

        <!-- Logo -->
        <a href="{{ route('home') }}"
           class="group flex items-center gap-3"
           aria-label="Digital Star Consultants home">

            <span class="relative flex h-10 w-10 items-center justify-center rounded-xl bg-yellow text-ink shadow-[0_8px_20px_#f5c84b30]">
                <span class="absolute h-5 w-5 rotate-45 border-[3px] border-ink"></span>
                <span class="h-2 w-2 rounded-full bg-ink"></span>
            </span>

            <span class="leading-tight">
                <span class="block text-[25px] font-bold tracking-[-.07em]">
                    Digital Star
                </span>
                <span class="block text-[19px] font-medium text-slate-300">
                    Consultants
                </span>
            </span>
        </a>

        <!-- Desktop Navigation -->
        <nav class="hidden items-center gap-7 text-sm font-medium text-slate-200 lg:flex"
             aria-label="Primary navigation">

            <a class="nav-link hover:text-white" href="{{ route('home') }}">
                Home
            </a>

            <details class="group relative">
                <summary class="nav-link flex cursor-pointer list-none items-center gap-1 hover:text-white">
                    Services
                    <span class="text-[10px] text-slate-400 transition-transform group-open:rotate-180">
                        ▼
                    </span>
                </summary>

                <div class="mega-panel absolute left-1/2 top-full z-50 mt-3 hidden w-[560px] -translate-x-1/2 p-5 group-open:block">

                    <div class="mb-4 flex items-center justify-between border-b border-line pb-3">
                        <span class="eyebrow">Service categories</span>

                        <a class="text-xs font-bold text-blue hover:text-ink"
                           href="{{ route('public.services.index') }}">
                            View all →
                        </a>
                    </div>

                    <div class="grid grid-cols-2 gap-2">

                        <a class="mega-link" href="{{ route('public.services.index') }}">
                            <span class="mega-link-icon">🏛</span>
                            <span>
                                <span class="block text-sm font-bold text-ink">
                                    Government Services
                                </span>
                                <span class="mt-0.5 block text-xs text-muted">
                                    Permits, licenses, registrations
                                </span>
                            </span>
                        </a>

                        <a class="mega-link" href="{{ route('public.services.index') }}">
                            <span class="mega-link-icon">🏢</span>
                            <span>
                                <span class="block text-sm font-bold text-ink">
                                    Business Services
                                </span>
                                <span class="mt-0.5 block text-xs text-muted">
                                    Formation, compliance, filings
                                </span>
                            </span>
                        </a>

                        <a class="mega-link" href="{{ route('public.services.index') }}">
                            <span class="mega-link-icon">💻</span>
                            <span>
                                <span class="block text-sm font-bold text-ink">
                                    Digital Services
                                </span>
                                <span class="mt-0.5 block text-xs text-muted">
                                    Portals, integrations, support
                                </span>
                            </span>
                        </a>

                        <a class="mega-link" href="{{ route('public.services.index') }}">
                            <span class="mega-link-icon">📋</span>
                            <span>
                                <span class="block text-sm font-bold text-ink">
                                    Consulting
                                </span>
                                <span class="mt-0.5 block text-xs text-muted">
                                    Strategy, advisory, planning
                                </span>
                            </span>
                        </a>

                        <a class="mega-link" href="{{ route('public.services.index') }}">
                            <span class="mega-link-icon">⚖</span>
                            <span>
                                <span class="block text-sm font-bold text-ink">
                                    Legal & Compliance
                                </span>
                                <span class="mt-0.5 block text-xs text-muted">
                                    Documents, verification
                                </span>
                            </span>
                        </a>

                        <a class="mega-link" href="{{ route('public.services.index') }}">
                            <span class="mega-link-icon">📊</span>
                            <span>
                                <span class="block text-sm font-bold text-ink">
                                    Analytics & Reporting
                                </span>
                                <span class="mt-0.5 block text-xs text-muted">
                                    Data, insights, dashboards
                                </span>
                            </span>
                        </a>

                    </div>
                </div>
            </details>

            <a class="nav-link hover:text-white" href="{{ route('home') }}#industries">
                Industries
            </a>

            <a class="nav-link hover:text-white" href="{{ route('home') }}#why">
                About
            </a>

            <a class="nav-link hover:text-white" href="{{ url('/track') }}">
                Track a request
            </a>

            @if (Route::has('public.contact.show'))
                <a class="nav-link hover:text-white" href="{{ route('public.contact.show') }}">
                    Contact
                </a>
            @else
                <a class="nav-link hover:text-white" href="{{ url('/contact') }}">
                    Contact
                </a>
            @endif

        </nav>

        <!-- Desktop Actions -->
        <div class="hidden items-center gap-4 lg:flex">

            @auth

                @php
                    $dashboardRoute = auth()->user()->isManagement()
                        ? 'admin.dashboard'
                        : 'staff.submissions.index';
                @endphp

                <a class="text-sm font-semibold text-slate-200 hover:text-white"
                   href="{{ route($dashboardRoute) }}">
                    Dashboard
                </a>

                <a class="button-primary !px-4 !py-2.5"
                   href="{{ route('logout') }}">
                    Sign out
                </a>

            @else

                <a class="text-sm font-semibold text-slate-200 hover:text-white"
                   href="{{ route('login') }}">
                    Sign in
                </a>

                <a class="button-primary !px-4 !py-2.5"
                   href="{{ route('public.services.index') }}">
                    Start a request
                    <span aria-hidden="true">↗</span>
                </a>

            @endauth

        </div>

        <!-- Mobile Menu -->
        <details class="relative lg:hidden">

            <summary class="flex cursor-pointer list-none items-center rounded-lg border border-white/20 px-3 py-2 text-sm font-semibold">
                Menu
                <span class="ml-2 text-yellow">≡</span>
            </summary>

            <nav class="absolute right-0 top-14 z-50 min-w-64 rounded-2xl border border-white/15 bg-navy p-4 shadow-2xl"
                 aria-label="Mobile navigation">

                <a class="block rounded-xl px-3 py-3 text-sm hover:bg-white/10"
                   href="{{ route('home') }}">
                    Home
                </a>

                <details class="rounded-xl">

                    <summary class="flex cursor-pointer list-none items-center justify-between rounded-xl px-3 py-3 text-sm hover:bg-white/10">
                        Services
                        <span class="text-yellow">+</span>
                    </summary>

                    <div class="ml-3 mt-1 space-y-1">

                        <a class="block rounded-lg px-3 py-2 text-xs text-slate-300 hover:bg-white/10"
                           href="{{ route('public.services.index') }}">
                            All services
                        </a>

                        <a class="block rounded-lg px-3 py-2 text-xs text-slate-300 hover:bg-white/10"
                           href="{{ route('public.services.index') }}">
                            Government
                        </a>

                        <a class="block rounded-lg px-3 py-2 text-xs text-slate-300 hover:bg-white/10"
                           href="{{ route('public.services.index') }}">
                            Business
                        </a>

                        <a class="block rounded-lg px-3 py-2 text-xs text-slate-300 hover:bg-white/10"
                           href="{{ route('public.services.index') }}">
                            Digital
                        </a>

                        <a class="block rounded-lg px-3 py-2 text-xs text-slate-300 hover:bg-white/10"
                           href="{{ route('public.services.index') }}">
                            Consulting
                        </a>

                    </div>
                </details>

                <a class="block rounded-xl px-3 py-3 text-sm hover:bg-white/10"
                   href="{{ route('home') }}#industries">
                    Industries
                </a>

                <a class="block rounded-xl px-3 py-3 text-sm hover:bg-white/10"
                   href="{{ route('home') }}#why">
                    About
                </a>

                <a class="block rounded-xl px-3 py-3 text-sm hover:bg-white/10"
                   href="{{ url('/track') }}">
                    Track a request
                </a>

                @if (Route::has('public.contact.show'))

                    <a class="block rounded-xl px-3 py-3 text-sm hover:bg-white/10"
                       href="{{ route('public.contact.show') }}">
                        Contact
                    </a>

                @else

                    <a class="block rounded-xl px-3 py-3 text-sm hover:bg-white/10"
                       href="{{ url('/contact') }}">
                        Contact
                    </a>

                @endif

                <div class="mt-2 border-t border-white/10 pt-3">

                    @auth

                        @php
                            $dashboardRoute = auth()->user()->isManagement()
                                ? 'admin.dashboard'
                                : 'staff.submissions.index';
                        @endphp

                        <a class="block rounded-xl px-3 py-3 text-sm hover:bg-white/10"
                           href="{{ route($dashboardRoute) }}">
                            Dashboard
                        </a>

                        <a class="mt-1 block rounded-xl bg-yellow px-3 py-3 text-sm font-bold text-ink"
                           href="{{ route('logout') }}">
                            Sign out
                        </a>

                    @else

                        <a class="mt-1 block rounded-xl bg-yellow px-3 py-3 text-sm font-bold text-ink"
                           href="{{ route('login') }}">
                            Sign in
                        </a>

                    @endauth

                </div>

            </nav>

        </details>

    </div>
</header>
