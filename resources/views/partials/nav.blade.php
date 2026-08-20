<header class="glass-dark sticky top-0 z-40 text-white">
    <div class="shell flex min-h-[76px] items-center justify-between gap-6">
        <a href="{{ route('home') }}" class="group flex items-center gap-3" aria-label="Digital Star Consultants home">
            <span class="relative flex h-10 w-10 items-center justify-center rounded-xl bg-yellow text-ink shadow-[0_8px_20px_#f5c84b30]">
                <span class="absolute h-5 w-5 rotate-45 border-[3px] border-ink"></span>
                <span class="h-2 w-2 rounded-full bg-ink"></span>
            </span>
            <span class="leading-tight"><span class="block text-sm font-bold tracking-[-.02em]">Digital Star</span><span class="block text-[11px] font-medium text-slate-300">Consultants</span></span>
        </a>
        <nav class="hidden items-center gap-8 text-sm text-slate-200 md:flex" aria-label="Primary navigation">
            <a class="transition-colors hover:text-white" href="{{ route('public.services.index') }}">Services</a>
            <a class="transition-colors hover:text-white" href="{{ url('/track') }}">Track a request</a>
            @if (Route::has('public.contact.show'))<a class="transition-colors hover:text-white" href="{{ route('public.contact.show') }}">Contact</a>@else<a class="transition-colors hover:text-white" href="{{ url('/contact') }}">Contact</a>@endif
        </nav>
        <div class="hidden items-center gap-4 md:flex">
            @auth
                @php $dashboardRoute = auth()->user()->isManagement() ? 'admin.dashboard' : 'staff.submissions.index'; @endphp
                <a class="text-sm font-semibold text-slate-200 hover:text-white" href="{{ route($dashboardRoute) }}">Dashboard</a>
                <a class="button-primary !px-4 !py-2.5" href="{{ route('logout') }}">Sign out</a>
            @else
                <a class="text-sm font-semibold text-slate-200 hover:text-white" href="{{ route('login') }}">Sign in</a>
                <a class="button-primary !px-4 !py-2.5" href="{{ route('public.services.index') }}">Start a request <span aria-hidden="true">↗</span></a>
            @endauth
        </div>
        <details class="relative md:hidden">
            <summary class="flex cursor-pointer list-none items-center rounded-lg border border-white/20 px-3 py-2 text-sm font-semibold">Menu <span class="ml-2 text-yellow">+</span></summary>
            <nav class="absolute right-0 top-14 min-w-56 rounded-2xl border border-white/15 bg-navy p-3 shadow-2xl" aria-label="Mobile navigation">
                <a class="block rounded-xl px-3 py-3 text-sm hover:bg-white/10" href="{{ route('public.services.index') }}">Services</a>
                <a class="block rounded-xl px-3 py-3 text-sm hover:bg-white/10" href="{{ url('/track') }}">Track a request</a>
                @if (Route::has('public.contact.show'))<a class="block rounded-xl px-3 py-3 text-sm hover:bg-white/10" href="{{ route('public.contact.show') }}">Contact</a>@else<a class="block rounded-xl px-3 py-3 text-sm hover:bg-white/10" href="{{ url('/contact') }}">Contact</a>@endif
                @auth
                    @php $dashboardRoute = auth()->user()->isManagement() ? 'admin.dashboard' : 'staff.submissions.index'; @endphp
                    <a class="block rounded-xl px-3 py-3 text-sm hover:bg-white/10" href="{{ route($dashboardRoute) }}">Dashboard</a>
                    <a class="mt-2 block rounded-xl bg-yellow px-3 py-3 text-sm font-bold text-ink" href="{{ route('logout') }}">Sign out</a>
                @else
                    <a class="mt-2 block rounded-xl bg-yellow px-3 py-3 text-sm font-bold text-ink" href="{{ route('login') }}">Sign in</a>
                @endauth
            </nav>
        </details>
    </div>
</header>
