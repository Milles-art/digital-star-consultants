@php
    $navLinks = [
        ['label' => 'Home',          'href' => route('home')],
        ['label' => 'Services',      'href' => route('public.services.index')],
        ['label' => 'Contact',       'href' => Route::has('public.contact.show') ? route('public.contact.show') : url('/contact')],
        ['label' => 'Track request', 'href' => url('/track')],
    ];

    $dashboardUrl = null;
    if (auth()->check()) {
        $user = auth()->user();
        if (method_exists($user, 'isManagement') && $user->isManagement()) {
            $dashboardUrl = route('admin.dashboard');
        } elseif (Route::has('staff.submissions.index')) {
            $dashboardUrl = route('staff.submissions.index');
        } elseif (Route::has('admin.dashboard')) {
            $dashboardUrl = route('admin.dashboard');
        }
    }
@endphp

<header class="sticky top-0 z-50 border-b border-white/10 bg-[color:var(--color-brand-950)]/85 backdrop-blur-xl supports-[backdrop-filter]:bg-[color:var(--color-brand-950)]/70">
    <div class="shell">
        <nav class="flex h-16 items-center justify-between gap-4 md:h-[4.5rem]" aria-label="Primary">

            <a href="{{ route('home') }}"
               class="group flex items-center gap-2.5 rounded-lg text-white focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[color:var(--color-accent-400)]">
                <span aria-hidden="true"
                      class="flex h-9 w-9 items-center justify-center rounded-xl bg-[color:var(--color-accent-400)] text-[color:var(--color-brand-950)] shadow-lg shadow-black/30 transition-transform duration-200 group-hover:scale-105">
                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="currentColor" aria-hidden="true">
                        <path d="M12 2.5l2.6 6.1 6.6.55-5 4.32 1.5 6.43L12 16.5l-5.7 3.4 1.5-6.43-5-4.32 6.6-.55L12 2.5z"/>
                    </svg>
                </span>
                <span class="text-[0.98rem] font-bold tracking-tight sm:text-lg">
                    Digital Star <span class="text-[color:var(--color-accent-300)]">Consultants</span>
                </span>
            </a>

            <ul class="hidden items-center gap-1 lg:flex">
                @foreach ($navLinks as $link)
                    <li>
                        <a href="{{ $link['href'] }}"
                           @if (request()->url() === $link['href']) aria-current="page" @endif
                           class="rounded-full px-3.5 py-2 text-sm font-medium text-white/75 transition-colors duration-150 hover:bg-white/10 hover:text-white aria-[current=page]:bg-white/10 aria-[current=page]:text-white">
                            {{ $link['label'] }}
                        </a>
                    </li>
                @endforeach
            </ul>

            <div class="hidden items-center gap-2 lg:flex">
                @auth
                    @if ($dashboardUrl)
                        <a href="{{ $dashboardUrl }}" class="btn btn-sm btn-ghost text-white/85 hover:text-white">Dashboard</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-accent">Sign out</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn btn-sm btn-ghost text-white/85 hover:text-white">Staff login</a>
                    <a href="{{ route('public.services.index') }}" class="btn btn-sm btn-accent">Submit a request</a>
                @endauth
            </div>

            <details class="group relative lg:hidden">
                <summary
                    class="flex h-10 w-10 cursor-pointer list-none items-center justify-center rounded-lg text-white transition-colors hover:bg-white/10 [&::-webkit-details-marker]:hidden"
                    aria-label="Toggle navigation menu">
                    <svg class="h-5 w-5 group-open:hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                        <path d="M4 7h16M4 12h16M4 17h16"/>
                    </svg>
                    <svg class="hidden h-5 w-5 group-open:block" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                        <path d="M6 6l12 12M18 6L6 18"/>
                    </svg>
                </summary>

                <div class="absolute right-0 top-[calc(100%+0.75rem)] w-[min(19rem,calc(100vw-2.5rem))] overflow-hidden rounded-2xl border border-[color:var(--color-line)] bg-white p-2 shadow-2xl shadow-black/25">
                    <ul class="flex flex-col">
                        @foreach ($navLinks as $link)
                            <li>
                                <a href="{{ $link['href'] }}"
                                   class="block rounded-xl px-3.5 py-3 text-sm font-medium text-[color:var(--color-ink)] transition-colors hover:bg-[color:var(--color-brand-50)] hover:text-[color:var(--color-brand-700)]">
                                    {{ $link['label'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>

                    <div class="divider my-2"></div>

                    <div class="flex flex-col gap-2 p-1.5">
                        @auth
                            @if ($dashboardUrl)
                                <a href="{{ $dashboardUrl }}" class="btn btn-sm btn-outline w-full">Dashboard</a>
                            @endif
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-primary w-full">Sign out</button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-sm btn-outline w-full">Staff login</a>
                            <a href="{{ route('public.services.index') }}" class="btn btn-sm btn-primary w-full">Submit a request</a>
                        @endauth
                    </div>
                </div>
            </details>
        </nav>
    </div>
</header>
