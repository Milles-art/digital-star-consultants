<header class="site-header sticky top-0 z-50">
    <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3 sm:px-6 lg:px-8">
        <a href="{{ route('home') }}" class="flex items-center gap-2.5 text-white">
            <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-brand-600 font-display text-sm font-bold text-white">
                DS
            </span>
            <span class="hidden font-display text-base font-semibold tracking-tight sm:inline">
                Digital Star <span class="text-accent-400">Consultants</span>
            </span>
        </a>

        <nav class="hidden items-center gap-6 md:flex" aria-label="Primary">
            <a href="{{ route('home') }}" class="nav-link text-sm font-medium {{ request()->routeIs('home') ? 'is-active' : '' }}">Home</a>
            <a href="{{ route('public.services.index') }}" class="nav-link text-sm font-medium {{ request()->routeIs('public.services.*') ? 'is-active' : '' }}">Services</a>
            <a href="{{ url('/contact') }}" class="nav-link text-sm font-medium {{ request()->is('contact') ? 'is-active' : '' }}">Contact</a>
            <a href="{{ url('/track') }}" class="nav-link text-sm font-medium">Track request</a>
        </nav>

        <div class="flex items-center gap-2">
            @auth
                <a href="{{ auth()->user()->isManagement() ? route('admin.dashboard') : route('staff.submissions.index') }}"
                   class="btn-accent text-sm !py-2 !px-3">
                    Dashboard
                </a>
            @else
                <a href="{{ route('login') }}" class="btn-ghost text-sm !py-2 !px-3">
                    Staff login
                </a>
            @endauth

            {{-- Mobile menu toggle (simple) --}}
            <details class="relative md:hidden">
                <summary class="list-none cursor-pointer rounded-lg border border-white/20 px-3 py-2 text-sm text-white">
                    Menu
                </summary>
                <div class="absolute right-0 mt-2 w-48 rounded-xl border border-white/10 bg-ink-900 p-2 shadow-xl">
                    <a href="{{ route('home') }}" class="block rounded-lg px-3 py-2 text-sm text-white/90 hover:bg-white/10">Home</a>
                    <a href="{{ route('public.services.index') }}" class="block rounded-lg px-3 py-2 text-sm text-white/90 hover:bg-white/10">Services</a>
                    <a href="{{ url('/contact') }}" class="block rounded-lg px-3 py-2 text-sm text-white/90 hover:bg-white/10">Contact</a>
                    <a href="{{ url('/track') }}" class="block rounded-lg px-3 py-2 text-sm text-white/90 hover:bg-white/10">Track request</a>
                </div>
            </details>
        </div>
    </div>
</header>
