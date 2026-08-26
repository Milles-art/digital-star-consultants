<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ trim(($title ?? 'Home').' | '.config('app.name', 'Digital Star Consultants')) }}</title>
    <meta name="description" content="{{ $metaDescription ?? 'Digital Star Consultants — IT, printing, design, stationery and tech services in Mbagala, Dar es Salaam.' }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-paper text-ink antialiased">
    <header class="sticky top-0 z-40 border-b border-line/80 bg-paper/90 backdrop-blur-md">
        <div class="shell flex items-center justify-between gap-4 py-4">
            <a href="{{ route('home') }}" class="flex items-center gap-3 no-underline">
                <span class="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-ink text-sm font-black text-yellow shadow-lg shadow-ink/10">DS</span>
                <span>
                    <span class="block text-sm font-black text-ink">Digital Star</span>
                    <span class="block text-[11px] font-semibold uppercase tracking-[0.16em] text-muted">Consultants</span>
                </span>
            </a>

            <nav class="hidden items-center gap-1 md:flex" aria-label="Primary">
                <a href="{{ route('home') }}" class="rounded-full px-4 py-2 text-sm font-bold text-ink/80 transition hover:bg-sky hover:text-ink {{ request()->routeIs('home') ? 'bg-sky text-ink' : '' }}">Home</a>
                <a href="{{ route('public.services.index') }}" class="rounded-full px-4 py-2 text-sm font-bold text-ink/80 transition hover:bg-sky hover:text-ink {{ request()->routeIs('public.services.*') ? 'bg-sky text-ink' : '' }}">Services</a>
                <a href="{{ route('home') }}#how-it-works" class="rounded-full px-4 py-2 text-sm font-bold text-ink/80 transition hover:bg-sky hover:text-ink">How it works</a>
                <a href="{{ route('home') }}#contact" class="rounded-full px-4 py-2 text-sm font-bold text-ink/80 transition hover:bg-sky hover:text-ink">Contact</a>
            </nav>

            <div class="flex items-center gap-2">
                <a href="{{ route('public.services.index') }}" class="button-primary !py-2.5 !px-4 text-sm">Browse services</a>
                @auth
                    <a href="{{ auth()->user()->isManagement() ? route('admin.dashboard') : route('staff.submissions') }}" class="button-secondary !py-2.5 !px-4 text-sm hidden sm:inline-flex">Console</a>
                @else
                    <a href="{{ route('login') }}" class="button-secondary !py-2.5 !px-4 text-sm hidden sm:inline-flex">Staff login</a>
                @endauth
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
        <div class="shell grid gap-10 py-14 md:grid-cols-[1.4fr_1fr_1fr]">
            <div>
                <div class="flex items-center gap-3">
                    <span class="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-yellow text-sm font-black text-ink">DS</span>
                    <div>
                        <p class="text-sm font-black">Digital Star Consultants</p>
                        <p class="text-xs text-white/55">Mbagala, Dar es Salaam</p>
                    </div>
                </div>
                <p class="mt-5 max-w-md text-sm leading-relaxed text-white/70">
                    IT &amp; internet café, printing &amp; design, stationery, and tech consultancy — practical digital help for everyday work and official processes.
                </p>
            </div>

            <div>
                <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-yellow">Explore</p>
                <ul class="mt-4 space-y-2 text-sm text-white/75">
                    <li><a class="hover:text-white" href="{{ route('home') }}">Home</a></li>
                    <li><a class="hover:text-white" href="{{ route('public.services.index') }}">All services</a></li>
                    <li><a class="hover:text-white" href="{{ route('home') }}#how-it-works">How it works</a></li>
                    <li><a class="hover:text-white" href="{{ route('login') }}">Staff login</a></li>
                </ul>
            </div>

            <div>
                <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-yellow">Contact</p>
                <ul class="mt-4 space-y-2 text-sm text-white/75">
                    <li>Mbagala, Dar es Salaam</li>
                    <li>Tanzania</li>
                    <li class="pt-2">
                        <a href="{{ route('home') }}#contact" class="inline-flex rounded-full border border-white/20 px-4 py-2 text-xs font-bold text-white transition hover:border-yellow hover:text-yellow">Send a message</a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="border-t border-white/10">
            <div class="shell flex flex-col gap-2 py-5 text-xs text-white/45 sm:flex-row sm:items-center sm:justify-between">
                <p>&copy; {{ date('Y') }} Digital Star Consultants. All rights reserved.</p>
                <p>Track requests with your reference number — no account needed.</p>
            </div>
        </div>
    </footer>

    <div class="admin-toast-stack" data-toast-stack aria-live="polite" aria-atomic="true"></div>
    @stack('scripts')
</body>
</html>
