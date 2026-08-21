#!/bin/bash
# Complete Blade redesign
set -e
echo "🎨 Redesigning all Blade views..."

# LAYOUT
cat > resources/views/layouts/app.blade.php << 'LAYOUTEOF'
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Digital Star Consultants')</title>
    <meta name="description" content="@yield('meta_description', 'Practical digital services for government, business, and personal needs — delivered with clarity across 12 countries.')">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
        .font-display { font-family: 'Playfair Display', serif; }
        .fade-in { animation: fadeIn 0.5s ease-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
    @stack('styles')
</head>
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen flex flex-col">
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 focus:z-50 focus:bg-amber-500 focus:text-white focus:px-4 focus:py-2 focus:rounded-lg">Skip to content</a>
    @include('partials.nav')
    @include('partials.alerts')
    <main id="main-content" class="flex-1">
        @yield('content')
    </main>
    @include('partials.footer')
    @stack('scripts')
</body>
</html>
LAYOUTEOF
echo "✅ layouts/app.blade.php"

# NAV
cat > resources/views/partials/nav.blade.php << 'NAVEOF'
<nav class="bg-white/80 backdrop-blur-md border-b border-slate-200/60 sticky top-0 z-40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <a href="{{ route('home') }}" class="flex items-center gap-2.5 group">
                <div class="w-9 h-9 bg-gradient-to-br from-amber-400 to-amber-600 rounded-lg flex items-center justify-center shadow-sm group-hover:shadow-md transition-shadow">
                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                </div>
                <span class="font-display text-xl font-bold text-slate-900 tracking-tight">Digital Star</span>
            </a>
            <div class="hidden md:flex items-center gap-1">
                <a href="{{ route('home') }}" class="px-3 py-2 rounded-lg text-sm font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition-colors {{ request()->routeIs('home') ? 'text-slate-900 bg-slate-100' : '' }}">Home</a>
                <a href="{{ route('public.services.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition-colors {{ request()->routeIs('public.services.*') ? 'text-slate-900 bg-slate-100' : '' }}">Services</a>
                <a href="{{ route('public.submissions.track', ['reference' => 'demo-ref']) }}" class="px-3 py-2 rounded-lg text-sm font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition-colors">Track</a>
            </div>
            <div class="hidden md:flex items-center gap-3">
                @auth
                    @php $dashboardRoute = auth()->user()->isManagement() ? 'admin.dashboard' : 'staff.submissions'; @endphp
                    <a href="{{ route($dashboardRoute) }}" class="px-4 py-2 rounded-lg text-sm font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition-colors">Dashboard</a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">@csrf<button type="submit" class="px-4 py-2 rounded-lg text-sm font-medium text-slate-500 hover:text-rose-600 transition-colors">Sign out</button></form>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2 rounded-lg text-sm font-medium text-slate-600 hover:text-slate-900 transition-colors">Sign in</a>
                    <a href="{{ route('public.services.index') }}" class="px-4 py-2.5 rounded-lg text-sm font-semibold text-white bg-slate-900 hover:bg-slate-800 transition-colors shadow-sm hover:shadow">Start a request</a>
                @endauth
            </div>
            <button type="button" onclick="document.getElementById('mobile-menu').classList.toggle('hidden')" class="md:hidden p-2 rounded-lg text-slate-600 hover:bg-slate-100">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
        </div>
    </div>
    <div id="mobile-menu" class="hidden md:hidden border-t border-slate-100 bg-white">
        <div class="px-4 py-3 space-y-1">
            <a href="{{ route('home') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50">Home</a>
            <a href="{{ route('public.services.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50">Services</a>
            <a href="{{ route('public.submissions.track', ['reference' => 'demo-ref']) }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50">Track request</a>
            @auth
                @php $dash = auth()->user()->isManagement() ? 'admin.dashboard' : 'staff.submissions'; @endphp
                <a href="{{ route($dash) }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50">Sign in</a>
            @endauth
        </div>
    </div>
</nav>
NAVEOF
echo "✅ partials/nav.blade.php"

# ALERTS
cat > resources/views/partials/alerts.blade.php << 'ALERTSEOF'
@if(session('success') || session('status') || session('message'))
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 p-4 fade-in flex items-start gap-3">
            <svg class="w-5 h-5 text-emerald-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div class="text-sm text-emerald-800 font-medium">{{ session('success') ?? session('status') ?? session('message') }}</div>
            <button onclick="this.closest('.fade-in').remove()" class="ml-auto text-emerald-400 hover:text-emerald-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
    </div>
@endif
@if(session('error'))
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
        <div class="rounded-xl bg-rose-50 border border-rose-200 p-4 fade-in flex items-start gap-3">
            <svg class="w-5 h-5 text-rose-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div class="text-sm text-rose-800 font-medium">{{ session('error') }}</div>
            <button onclick="this.closest('.fade-in').remove()" class="ml-auto text-rose-400 hover:text-rose-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
    </div>
@endif
@if($errors->any())
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
        <div class="rounded-xl bg-amber-50 border border-amber-200 p-4 fade-in">
            <div class="flex items-center gap-2 mb-2">
                <svg class="w-5 h-5 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span class="text-sm font-semibold text-amber-800">Please fix the following:</span>
            </div>
            <ul class="ml-7 space-y-1">
                @foreach($errors->all() as $error)
                    <li class="text-sm text-amber-700">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif
ALERTSEOF
echo "✅ partials/alerts.blade.php"

# FOOTER
cat > resources/views/partials/footer.blade.php << 'FOOTEREOF'
<footer class="bg-slate-900 text-slate-300 mt-auto">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 lg:gap-12">
            <div class="lg:col-span-1">
                <a href="{{ route('home') }}" class="flex items-center gap-2.5 mb-4">
                    <div class="w-8 h-8 bg-gradient-to-br from-amber-400 to-amber-600 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    </div>
                    <span class="font-display text-lg font-bold text-white">Digital Star</span>
                </a>
                <p class="text-sm text-slate-400 leading-relaxed mb-4">Practical digital services for the moments that matter — delivered with clarity, care, and momentum across 12 countries.</p>
                <div class="flex items-center gap-2 text-xs text-slate-500">
                    <span class="px-2 py-1 rounded bg-slate-800">EN</span>
                    <span class="px-2 py-1 rounded bg-slate-800">FR</span>
                    <span class="px-2 py-1 rounded bg-slate-800">AR</span>
                    <span class="px-2 py-1 rounded bg-slate-800">ES</span>
                </div>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-white uppercase tracking-wider mb-4">Services</h3>
                <ul class="space-y-2.5">
                    <li><a href="{{ route('public.services.index') }}" class="text-sm text-slate-400 hover:text-amber-400 transition-colors">All services</a></li>
                    <li><a href="{{ route('public.services.index', ['category' => 'government']) }}" class="text-sm text-slate-400 hover:text-amber-400 transition-colors">Government</a></li>
                    <li><a href="{{ route('public.services.index', ['category' => 'business']) }}" class="text-sm text-slate-400 hover:text-amber-400 transition-colors">Business</a></li>
                    <li><a href="{{ route('public.services.index', ['category' => 'digital']) }}" class="text-sm text-slate-400 hover:text-amber-400 transition-colors">Digital</a></li>
                </ul>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-white uppercase tracking-wider mb-4">Company</h3>
                <ul class="space-y-2.5">
                    <li><a href="{{ route('home') }}" class="text-sm text-slate-400 hover:text-amber-400 transition-colors">About us</a></li>
                    <li><a href="{{ route('public.submissions.track', ['reference' => 'demo-ref']) }}" class="text-sm text-slate-400 hover:text-amber-400 transition-colors">Track a request</a></li>
                    <li><a href="{{ route('public.services.index') }}" class="text-sm text-slate-400 hover:text-amber-400 transition-colors">Industries</a></li>
                </ul>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-white uppercase tracking-wider mb-4">Get in touch</h3>
                <ul class="space-y-3">
                    <li class="flex items-start gap-3">
                        <svg class="w-4 h-4 text-amber-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        <span class="text-sm text-slate-400">hello@digitalstar.consulting</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="w-4 h-4 text-amber-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        <span class="text-sm text-slate-400">+1 (800) 555-0142</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="w-4 h-4 text-amber-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="text-sm text-slate-400">Mon–Fri, 8:00–18:00</span>
                    </li>
                </ul>
            </div>
        </div>
        <div class="border-t border-slate-800 mt-10 pt-8 flex flex-col sm:flex-row justify-between items-center gap-4">
            <p class="text-xs text-slate-500">&copy; {{ date('Y') }} Digital Star Consultants. All rights reserved.</p>
            <div class="flex items-center gap-6">
                <a href="#" class="text-xs text-slate-500 hover:text-slate-300 transition-colors">Privacy</a>
                <a href="#" class="text-xs text-slate-500 hover:text-slate-300 transition-colors">Terms</a>
            </div>
        </div>
    </div>
</footer>
FOOTEREOF
echo "✅ partials/footer.blade.php"

# HOME PAGE
cat > resources/views/home.blade.php << 'HOMEEOF'
@extends('layouts.app')
@section('title', 'Digital Star Consultants — Make important work move')
@section('content')

{{-- ===== HERO ===== --}}
<section class="relative bg-slate-900 overflow-hidden">
    <div class="absolute inset-0 opacity-20">
        <div class="absolute top-0 right-0 w-96 h-96 bg-amber-500 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2"></div>
        <div class="absolute bottom-0 left-0 w-72 h-72 bg-blue-500 rounded-full blur-3xl translate-y-1/2 -translate-x-1/2"></div>
    </div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-28">
        <div class="max-w-3xl">
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-amber-500/10 border border-amber-500/20 text-amber-400 text-xs font-semibold uppercase tracking-wide mb-6">
                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                Trusted across 12 countries
            </div>
            <h1 class="font-display text-4xl sm:text-5xl lg:text-6xl font-bold text-white leading-tight mb-6">
                Move the work that <span class="text-amber-400">matters</span> forward.
            </h1>
            <p class="text-lg sm:text-xl text-slate-300 leading-relaxed mb-8 max-w-2xl">
                From government requests to business systems, we turn complex next steps into clear, confident progress — for organizations and individuals alike.
            </p>
            <div class="flex flex-wrap items-center gap-4 mb-10">
                <a href="{{ route('public.services.index') }}" class="px-6 py-3.5 rounded-xl text-sm font-semibold text-slate-900 bg-amber-400 hover:bg-amber-300 transition-colors shadow-lg shadow-amber-400/20">Browse services</a>
                <a href="{{ route('public.submissions.track', ['reference' => 'demo-ref']) }}" class="px-6 py-3.5 rounded-xl text-sm font-semibold text-white border border-white/20 hover:bg-white/10 transition-colors">Track a request</a>
            </div>
            <div class="flex flex-wrap items-center gap-6 text-sm text-slate-400">
                <span class="flex items-center gap-2"><svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> No account needed</span>
                <span class="flex items-center gap-2"><svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Reference tracking</span>
                <span class="flex items-center gap-2"><svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Multilingual support</span>
            </div>
        </div>
    </div>
</section>

{{-- ===== STATS ===== --}}
<section class="bg-white border-b border-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="text-center">
                <div class="text-3xl font-bold text-slate-900">12,400+</div>
                <div class="text-sm text-slate-500 mt-1">Requests completed</div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold text-slate-900">48h</div>
                <div class="text-sm text-slate-500 mt-1">Average response</div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold text-slate-900">12</div>
                <div class="text-sm text-slate-500 mt-1">Countries served</div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold text-slate-900">98%</div>
                <div class="text-sm text-slate-500 mt-1">Client satisfaction</div>
            </div>
        </div>
    </div>
</section>

{{-- ===== SERVICES ===== --}}
<section class="py-20 lg:py-24 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-14">
            <span class="text-sm font-semibold text-amber-600 uppercase tracking-wide">What we help with</span>
            <h2 class="font-display text-3xl sm:text-4xl font-bold text-slate-900 mt-3">One place for the next right move.</h2>
            <p class="text-slate-500 mt-4">Browse practical services built around real needs, not jargon. Choose a starting point and we will take it from there.</p>
        </div>

        @if(isset($categories) && count($categories))
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($categories as $category)
                    <a href="{{ route('public.services.index', ['category' => data_get($category, 'slug')]) }}" class="group bg-white rounded-2xl p-6 border border-slate-100 hover:border-amber-200 hover:shadow-lg hover:shadow-amber-500/5 transition-all duration-300">
                        <div class="flex items-start justify-between mb-4">
                            <span class="text-3xl">{{ data_get($category, 'icon', '✨') }}</span>
                            <span class="text-xs font-mono text-slate-300 group-hover:text-amber-500 transition-colors">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <h3 class="text-lg font-semibold text-slate-900 group-hover:text-amber-700 transition-colors">{{ data_get($category, 'name') }}</h3>
                        <p class="text-sm text-slate-500 mt-2 leading-relaxed">{{ data_get($category, 'description', 'Focused support with a clear outcome.') }}</p>
                        @if(data_get($category, 'services') && collect(data_get($category, 'services'))->isNotEmpty())
                            <div class="mt-4 pt-4 border-t border-slate-50">
                                <ul class="space-y-1">
                                    @foreach(collect(data_get($category, 'services'))->take(3) as $svc)
                                        <li class="text-xs text-slate-400 flex items-center gap-1.5">
                                            <span class="w-1 h-1 rounded-full bg-amber-400"></span>
                                            {{ data_get($svc, 'name') }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <div class="mt-4 flex items-center gap-1 text-sm font-medium text-amber-600 opacity-0 group-hover:opacity-100 transition-opacity">
                            Explore <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="text-center py-16">
                <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                </div>
                <p class="text-slate-500">Services are being curated. Check back shortly.</p>
            </div>
        @endif

        <div class="text-center mt-10">
            <a href="{{ route('public.services.index') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl text-sm font-semibold text-slate-700 bg-white border border-slate-200 hover:border-amber-300 hover:text-amber-700 transition-colors">
                View all services <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
        </div>
    </div>
</section>

{{-- ===== WHY US ===== --}}
<section class="py-20 lg:py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-14">
            <span class="text-sm font-semibold text-amber-600 uppercase tracking-wide">Why Digital Star</span>
            <h2 class="font-display text-3xl sm:text-4xl font-bold text-slate-900 mt-3">Less chasing. More done.</h2>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="text-center">
                <div class="w-14 h-14 bg-amber-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-7 h-7 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                </div>
                <h3 class="text-lg font-semibold text-slate-900">Clarity</h3>
                <p class="text-sm text-slate-500 mt-2 leading-relaxed">Every step explained in plain language. No jargon, no confusion, no dead ends.</p>
            </div>
            <div class="text-center">
                <div class="w-14 h-14 bg-emerald-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-7 h-7 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <h3 class="text-lg font-semibold text-slate-900">Speed</h3>
                <p class="text-sm text-slate-500 mt-2 leading-relaxed">Most requests receive a response within two business days. We respect your time.</p>
            </div>
            <div class="text-center">
                <div class="w-14 h-14 bg-blue-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <h3 class="text-lg font-semibold text-slate-900">Trust</h3>
                <p class="text-sm text-slate-500 mt-2 leading-relaxed">Your data is handled with enterprise-grade security and used only for your request.</p>
            </div>
            <div class="text-center">
                <div class="w-14 h-14 bg-violet-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-7 h-7 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="text-lg font-semibold text-slate-900">Reach</h3>
                <p class="text-sm text-slate-500 mt-2 leading-relaxed">Multilingual support across 12 countries means we meet you where you are.</p>
            </div>
        </div>
    </div>
</section>

{{-- ===== PROCESS ===== --}}
<section class="py-20 lg:py-24 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-14">
            <span class="text-sm font-semibold text-amber-600 uppercase tracking-wide">Simple by design</span>
            <h2 class="font-display text-3xl sm:text-4xl font-bold text-slate-900 mt-3">A process you can follow.</h2>
        </div>
        @if(isset($steps) && count($steps))
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                @foreach($steps as $step)
                    <div class="relative">
                        @if(!$loop->last)
                            <div class="hidden lg:block absolute top-8 left-full w-full h-0.5 bg-slate-200 -translate-y-1/2"></div>
                        @endif
                        <div class="w-16 h-16 bg-white rounded-2xl border border-slate-200 flex items-center justify-center mb-4 shadow-sm">
                            <span class="text-xl font-bold text-amber-600">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <h3 class="text-lg font-semibold text-slate-900">{{ data_get($step, 'title') }}</h3>
                        <p class="text-sm text-slate-500 mt-2 leading-relaxed">{{ data_get($step, 'description') }}</p>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12">
                <p class="text-slate-500">Start with a service request and we will guide you through.</p>
            </div>
        @endif
    </div>
</section>

{{-- ===== CTA ===== --}}
<section class="py-20 lg:py-24 bg-slate-900 relative overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-1/2 left-1/2 w-[600px] h-[600px] bg-amber-500 rounded-full blur-3xl -translate-x-1/2 -translate-y-1/2"></div>
    </div>
    <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="font-display text-3xl sm:text-4xl font-bold text-white mb-6">Ready to make your next move?</h2>
        <p class="text-lg text-slate-300 mb-8 max-w-xl mx-auto">Choose a service, tell us what you need, and we will handle the rest. No account required.</p>
        <a href="{{ route('public.services.index') }}" class="inline-flex items-center gap-2 px-8 py-4 rounded-xl text-base font-semibold text-slate-900 bg-amber-400 hover:bg-amber-300 transition-colors shadow-lg shadow-amber-400/20">
            Browse all services <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
        </a>
    </div>
</section>

@endsection
HOMEEOF
echo "✅ home.blade.php"

# SERVICES INDEX
cat > resources/views/services/index.blade.php << 'INDEXEOF'
@extends('layouts.app')
@section('title', 'Services — Digital Star Consultants')
@section('meta_description', 'Browse practical digital services for government, business, and personal needs. Clear support with a confident outcome.')
@section('content')

{{-- Header --}}
<section class="bg-slate-900 py-16 lg:py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl">
            <span class="text-sm font-semibold text-amber-400 uppercase tracking-wide">The service directory</span>
            <h1 class="font-display text-3xl sm:text-4xl lg:text-5xl font-bold text-white mt-3 leading-tight">Clear, practical support for digital, government, and business needs.</h1>
            <p class="text-slate-300 mt-4 text-lg">Choose a category or search to begin. Every service is designed around a real outcome, not jargon.</p>
            <div class="flex flex-wrap items-center gap-4 mt-6 text-sm text-slate-400">
                <span class="flex items-center gap-2"><svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> No account needed to submit</span>
                <span class="flex items-center gap-2"><svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Track with a reference number</span>
            </div>
        </div>
    </div>
</section>

{{-- Filters + Search --}}
<section class="bg-white border-b border-slate-100 sticky top-16 z-30">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="flex flex-col sm:flex-row gap-3">
            <form method="GET" action="{{ route('public.services.index') }}" class="flex-1 flex gap-3">
                <div class="relative flex-1">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Search services..." class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-400 transition-all">
                </div>
                <select name="category" onchange="this.form.submit()" class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-400 transition-all min-w-[160px]">
                    <option value="">All categories</option>
                    @if(isset($categories))
                        @foreach($categories as $cat)
                            <option value="{{ data_get($cat, 'slug') }}" {{ ($selectedCategory ?? '') == data_get($cat, 'slug') ? 'selected' : '' }}>{{ data_get($cat, 'name') }}</option>
                        @endforeach
                    @endif
                </select>
                @if(($search ?? '') || ($selectedCategory ?? ''))
                    <a href="{{ route('public.services.index') }}" class="px-4 py-2.5 rounded-xl text-sm font-medium text-slate-500 hover:text-rose-600 border border-slate-200 hover:border-rose-200 transition-colors">Clear</a>
                @endif
            </form>
        </div>
    </div>
</section>

{{-- Service Groups --}}
<section class="py-12 lg:py-16 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if(isset($serviceGroups) && count($serviceGroups))
            <div class="space-y-16">
                @foreach($serviceGroups as $group)
                    <div>
                        <div class="flex items-end justify-between mb-6">
                            <div>
                                <h2 class="text-2xl font-bold text-slate-900">{{ data_get($group, 'name') }}</h2>
                                <p class="text-sm text-slate-500 mt-1">{{ data_get($group, 'description') }}</p>
                            </div>
                            <span class="text-sm text-slate-400">{{ count(data_get($group, 'services', [])) }} services</span>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                            @foreach(data_get($group, 'services', []) as $service)
                                @include('services._card', ['service' => $service])
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                @forelse($services as $service)
                    @include('services._card', ['service' => $service])
                @empty
                    <div class="col-span-full text-center py-16">
                        <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <h3 class="text-lg font-semibold text-slate-700">No services found</h3>
                        <p class="text-sm text-slate-500 mt-1">Try a different search term or category.</p>
                    </div>
                @endforelse
            </div>
        @endif

        @if(method_exists($services, 'links'))
            <div class="mt-10">
                {{ $services->withQueryString()->links() }}
            </div>
        @endif
    </div>
</section>

@endsection
INDEXEOF
echo "✅ services/index.blade.php"

# SERVICE CARD PARTIAL
cat > resources/views/services/_card.blade.php << 'CARDEOF'
@php
    $price = data_get($service, 'formatted_price') ?? (data_get($service, 'price') ? number_format(data_get($service, 'price')) . ' TZS' : null);
    $duration = data_get($service, 'duration');
    $categoryName = data_get($service, 'category.name') ?? data_get($service, 'category_name');
@endphp
<a href="{{ route('public.services.show', data_get($service, 'slug')) }}" class="group bg-white rounded-2xl border border-slate-100 hover:border-amber-200 hover:shadow-lg hover:shadow-amber-500/5 transition-all duration-300 p-5 flex flex-col h-full">
    <div class="flex items-start justify-between mb-3">
        @if($categoryName)
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-600 group-hover:bg-amber-50 group-hover:text-amber-700 transition-colors">
                {{ $categoryName }}
            </span>
        @endif
        <svg class="w-5 h-5 text-slate-300 group-hover:text-amber-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
    </div>
    <h3 class="text-lg font-semibold text-slate-900 group-hover:text-amber-700 transition-colors">{{ data_get($service, 'name') }}</h3>
    <p class="text-sm text-slate-500 mt-2 leading-relaxed flex-1">{{ Str::limit(data_get($service, 'description', 'Professional service with clear outcomes.'), 100) }}</p>
    <div class="flex items-center gap-4 mt-4 pt-4 border-t border-slate-50 text-xs text-slate-400">
        @if($price)
            <span class="flex items-center gap-1"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> {{ $price }}</span>
        @endif
        @if($duration)
            <span class="flex items-center gap-1"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> {{ $duration }}</span>
        @endif
    </div>
</a>
CARDEOF
echo "✅ services/_card.blade.php"

# SERVICES SHOW — THE FORM
cat > resources/views/services/show.blade.php << 'SHOWEOF'
@extends('layouts.app')
@section('title', data_get($service, 'name', 'Service request').' — Digital Star Consultants')
@section('content')

@php
    $price = data_get($service, 'formatted_price') ?? (data_get($service, 'price') ? number_format(data_get($service, 'price')) . ' TZS' : null);
    $duration = data_get($service, 'duration');
    $category = data_get($service, 'category');
    $fields = data_get($service, 'fields', collect());
@endphp

{{-- ===== SERVICE HERO ===== --}}
<section class="bg-slate-900 py-12 lg:py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <a href="{{ route('public.services.index') }}" class="inline-flex items-center gap-1.5 text-sm text-slate-400 hover:text-amber-400 transition-colors mb-6">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to services
        </a>
        <div class="flex flex-wrap items-center gap-3 mb-4">
            @if($category)
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                    {{ data_get($category, 'name', 'Service') }}
                </span>
            @endif
            @if(data_get($service, 'is_active'))
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 mr-1.5 animate-pulse"></span>
                    Available now
                </span>
            @endif
        </div>
        <h1 class="font-display text-3xl sm:text-4xl lg:text-5xl font-bold text-white leading-tight">{{ data_get($service, 'name') }}</h1>
        <p class="text-slate-300 mt-4 text-lg max-w-3xl leading-relaxed">{{ data_get($service, 'description', data_get($service, 'short_description', 'Professional service with clear outcomes.')) }}</p>
        <div class="flex flex-wrap items-center gap-6 mt-6 text-sm">
            @if($price)
                <div class="flex items-center gap-2 text-amber-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="font-semibold">{{ $price }}</span>
                </div>
            @endif
            @if($duration)
                <div class="flex items-center gap-2 text-slate-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ $duration }}</span>
                </div>
            @endif
        </div>
    </div>
</section>

{{-- ===== FORM SECTION ===== --}}
<section class="py-12 lg:py-16 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-10 lg:gap-16">

            {{-- Left: Info & Trust --}}
            <div class="lg:col-span-2 space-y-8">
                <div>
                    <h2 class="text-xl font-bold text-slate-900 mb-3">What happens next</h2>
                    <div class="space-y-4">
                        <div class="flex gap-4">
                            <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center text-sm font-bold shrink-0">1</div>
                            <div>
                                <h4 class="text-sm font-semibold text-slate-800">Submit your request</h4>
                                <p class="text-sm text-slate-500 mt-0.5">Fill in the details below. No account needed.</p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center text-sm font-bold shrink-0">2</div>
                            <div>
                                <h4 class="text-sm font-semibold text-slate-800">We review & respond</h4>
                                <p class="text-sm text-slate-500 mt-0.5">Our team reviews within 48 hours on average.</p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center text-sm font-bold shrink-0">3</div>
                            <div>
                                <h4 class="text-sm font-semibold text-slate-800">Track your progress</h4>
                                <p class="text-sm text-slate-500 mt-0.5">Use your reference number to check status anytime.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-slate-100 p-5">
                    <h3 class="text-sm font-semibold text-slate-800 mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Your data is protected
                    </h3>
                    <ul class="space-y-2 text-sm text-slate-500">
                        <li class="flex items-center gap-2"><span class="w-1 h-1 rounded-full bg-slate-300"></span> SSL-encrypted submission</li>
                        <li class="flex items-center gap-2"><span class="w-1 h-1 rounded-full bg-slate-300"></span> Used only for your request</li>
                        <li class="flex items-center gap-2"><span class="w-1 h-1 rounded-full bg-slate-300"></span> No marketing emails</li>
                    </ul>
                </div>
            </div>

            {{-- Right: The Form --}}
            <div class="lg:col-span-3">
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm">
                    {{-- Form Header --}}
                    <div class="px-6 py-5 border-b border-slate-100">
                        <div class="flex items-center justify-between">
                            <h2 class="text-lg font-bold text-slate-900">Start your request</h2>
                            <span class="text-xs text-slate-400">Step 1 of 2</span>
                        </div>
                        <p class="text-sm text-slate-500 mt-1">Required fields are marked with <span class="text-rose-500">*</span></p>
                    </div>

                    <form id="submissionForm" method="POST" action="{{ route('public.submissions.store') }}" enctype="multipart/form-data" class="px-6 py-6 space-y-6">
                        @csrf
                        <input type="hidden" name="service_id" value="{{ data_get($service, 'id') }}">

                        {{-- Contact Info --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div class="sm:col-span-2">
                                <label for="customer_name" class="block text-sm font-medium text-slate-700 mb-1.5">
                                    Full name <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" id="customer_name" name="customer_name" value="{{ old('customer_name') }}" required
                                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-400 transition-all placeholder:text-slate-300"
                                    placeholder="e.g. Sarah Johnson">
                            </div>
                            <div>
                                <label for="customer_phone" class="block text-sm font-medium text-slate-700 mb-1.5">
                                    Phone number <span class="text-rose-500">*</span>
                                </label>
                                <input type="tel" id="customer_phone" name="customer_phone" value="{{ old('customer_phone') }}" required
                                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-400 transition-all placeholder:text-slate-300"
                                    placeholder="+255 712 345 678">
                            </div>
                            <div>
                                <label for="customer_email" class="block text-sm font-medium text-slate-700 mb-1.5">
                                    Email address
                                </label>
                                <input type="email" id="customer_email" name="customer_email" value="{{ old('customer_email') }}"
                                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-400 transition-all placeholder:text-slate-300"
                                    placeholder="sarah@example.com">
                            </div>
                            <div class="sm:col-span-2">
                                <label for="preferred_date" class="block text-sm font-medium text-slate-700 mb-1.5">
                                    Preferred date
                                </label>
                                <input type="date" id="preferred_date" name="preferred_date" value="{{ old('preferred_date') }}"
                                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-400 transition-all">
                            </div>
                        </div>

                        {{-- Dynamic Service Fields --}}
                        @if($fields && $fields->count())
                            <div class="border-t border-slate-100 pt-6">
                                <h3 class="text-sm font-semibold text-slate-800 mb-4">Service details</h3>
                                <div class="space-y-5">
                                    @foreach($fields as $field)
                                        @php
                                            $key = data_get($field, 'field_key');
                                            $type = data_get($field, 'field_type', 'text');
                                            $label = data_get($field, 'label');
                                            $required = data_get($field, 'is_required', false);
                                            $placeholder = data_get($field, 'placeholder');
                                            $help = data_get($field, 'help_text');
                                            $options = data_get($field, 'options', []);
                                            if (is_string($options)) {
                                                $options = json_decode($options, true) ?: array_filter(array_map('trim', explode(',', $options)));
                                            }
                                            $oldValue = old("fields.{$key}");
                                        @endphp
                                        <div>
                                            <label for="field_{{ $key }}" class="block text-sm font-medium text-slate-700 mb-1.5">
                                                {{ $label }} @if($required)<span class="text-rose-500">*</span>@endif
                                            </label>

                                            @if($type === 'textarea')
                                                <textarea id="field_{{ $key }}" name="fields[{{ $key }}]" @if($required) required @endif rows="3"
                                                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-400 transition-all placeholder:text-slate-300 resize-y"
                                                    placeholder="{{ $placeholder }}">{{ $oldValue }}</textarea>

                                            @elseif($type === 'select')
                                                <select id="field_{{ $key }}" name="fields[{{ $key }}]" @if($required) required @endif
                                                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-400 transition-all">
                                                    <option value="">Select an option</option>
                                                    @foreach($options as $option)
                                                        @php $optValue = is_array($option) ? data_get($option, 'value', data_get($option, 'label')) : $option; @endphp
                                                        <option value="{{ $optValue }}" {{ $oldValue == $optValue ? 'selected' : '' }}>{{ is_array($option) ? data_get($option, 'label', $optValue) : $option }}</option>
                                                    @endforeach
                                                </select>

                                            @elseif(in_array($type, ['radio', 'checkbox']))
                                                <div class="space-y-2 mt-2">
                                                    @foreach($options as $option)
                                                        @php
                                                            $optValue = is_array($option) ? data_get($option, 'value', data_get($option, 'label')) : $option;
                                                            $optLabel = is_array($option) ? data_get($option, 'label', $optValue) : $option;
                                                            $isChecked = is_array($oldValue) ? in_array($optValue, $oldValue) : $oldValue == $optValue;
                                                        @endphp
                                                        <label class="flex items-center gap-3 p-3 rounded-xl border border-slate-100 hover:border-amber-200 hover:bg-amber-50/30 cursor-pointer transition-colors">
                                                            <input type="{{ $type === 'checkbox' ? 'checkbox' : 'radio' }}" name="fields[{{ $key }}]{{ $type === 'checkbox' ? '[]' : '' }}" value="{{ $optValue }}" {{ $isChecked ? 'checked' : '' }} @if($required) required @endif
                                                                class="w-4 h-4 text-amber-600 border-slate-300 focus:ring-amber-500">
                                                            <span class="text-sm text-slate-700">{{ $optLabel }}</span>
                                                        </label>
                                                    @endforeach
                                                </div>

                                            @elseif($type === 'file')
                                                <div class="relative">
                                                    <input type="file" id="field_{{ $key }}" name="fields[{{ $key }}]" @if($required) required @endif
                                                        class="block w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-medium file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 transition-colors cursor-pointer"
                                                        onchange="document.getElementById('file-label-{{ $key }}').textContent = this.files[0]?.name || 'Choose a file...'">
                                                    <p id="file-label-{{ $key }}" class="text-xs text-slate-400 mt-1.5">PDF, JPG, PNG, DOC up to 10MB</p>
                                                </div>

                                            @else
                                                <input type="{{ $type }}" id="field_{{ $key }}" name="fields[{{ $key }}]" value="{{ $oldValue }}" @if($required) required @endif
                                                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-400 transition-all placeholder:text-slate-300"
                                                    placeholder="{{ $placeholder }}">
                                            @endif

                                            @if($help)
                                                <p class="text-xs text-slate-400 mt-1.5">{{ $help }}</p>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Notes --}}
                        <div class="border-t border-slate-100 pt-6">
                            <label for="customer_notes" class="block text-sm font-medium text-slate-700 mb-1.5">
                                Anything else we should know?
                            </label>
                            <textarea id="customer_notes" name="customer_notes" rows="3"
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-400 transition-all placeholder:text-slate-300 resize-y"
                                placeholder="Add any details that will help us understand your request...">{{ old('customer_notes') }}</textarea>
                        </div>

                        {{-- Submit --}}
                        <div class="border-t border-slate-100 pt-6">
                            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                                <p class="text-xs text-slate-400">By submitting, you agree that we may contact you about this request.</p>
                                <button type="submit" id="submitBtn" class="w-full sm:w-auto px-8 py-3 rounded-xl text-sm font-semibold text-white bg-slate-900 hover:bg-slate-800 transition-colors shadow-sm flex items-center justify-center gap-2">
                                    <span>Submit request</span>
                                    <svg id="btnIcon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                    <svg id="btnSpinner" class="w-4 h-4 animate-spin hidden" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Success Modal (hidden by default, shown via JS) --}}
<div id="successModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-8 text-center fade-in">
        <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <h3 class="text-xl font-bold text-slate-900 mb-2">Request submitted!</h3>
        <p class="text-sm text-slate-500 mb-4">We have received your request and will be in touch soon.</p>
        <div class="bg-slate-50 rounded-xl p-4 mb-6">
            <div class="text-xs text-slate-400 uppercase tracking-wide mb-1">Your reference number</div>
            <div id="refNumber" class="text-2xl font-mono font-bold text-slate-900 tracking-wider">---</div>
        </div>
        <div class="flex flex-col gap-2">
            <a id="trackLink" href="#" class="px-4 py-2.5 rounded-xl text-sm font-semibold text-white bg-slate-900 hover:bg-slate-800 transition-colors">Track your request</a>
            <a href="{{ route('public.services.index') }}" class="px-4 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:text-slate-900 transition-colors">Browse more services</a>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('submissionForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const btn = document.getElementById('submitBtn');
    const icon = document.getElementById('btnIcon');
    const spinner = document.getElementById('btnSpinner');
    btn.disabled = true;
    icon.classList.add('hidden');
    spinner.classList.remove('hidden');

    try {
        const formData = new FormData(this);
        const response = await fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await response.json();

        if (response.ok && data.status === 'success') {
            document.getElementById('refNumber').textContent = data.data.reference_number;
            document.getElementById('trackLink').href = data.data.tracking_url;
            document.getElementById('successModal').classList.remove('hidden');
            document.getElementById('successModal').classList.add('flex');
            this.reset();
        } else {
            alert(data.message || 'Something went wrong. Please try again.');
        }
    } catch (err) {
        alert('Network error. Please check your connection and try again.');
    } finally {
        btn.disabled = false;
        icon.classList.remove('hidden');
        spinner.classList.add('hidden');
    }
});
</script>
@endpush

@endsection
SHOWEOF
echo "✅ services/show.blade.php"

echo ""
echo "============================================================"
echo "🎨 Blade redesign complete!"
echo "============================================================"
echo ""
echo "Files updated:"
echo "  ✅ resources/views/layouts/app.blade.php"
echo "  ✅ resources/views/partials/nav.blade.php"
echo "  ✅ resources/views/partials/footer.blade.php"
echo "  ✅ resources/views/partials/alerts.blade.php"
echo "  ✅ resources/views/home.blade.php"
echo "  ✅ resources/views/services/index.blade.php"
echo "  ✅ resources/views/services/_card.blade.php"
echo "  ✅ resources/views/services/show.blade.php"
echo ""
echo "Design system:"
echo "  • Colors: Slate-900 navy primary, Amber-400 gold accent"
echo "  • Fonts: Inter (body) + Playfair Display (headlines)"
echo "  • Components: Rounded-2xl cards, subtle shadows, smooth transitions"
echo "  • Responsive: Mobile-first, sticky nav, hamburger menu"
echo ""
echo "Next steps:"
echo "  1. Run tests:   php artisan test --compact"
echo "  2. Build assets: npm run build"
echo "  3. Commit:      git add -A"
echo "                  git commit -m 'design: complete blade redesign — layout, nav, home, services, form'"
echo "                  git push origin digital-star-consultants"
echo ""
