#!/bin/bash
# Complete view system rebuild
# Backs up old views, deletes them, creates fresh ones
set -e

echo "🎨 Rebuilding entire view system..."

# Backup old views
if [ -d "resources/views" ]; then
    backup_dir="resources/views-backup-$(date +%Y%m%d-%H%M%S)"
    cp -r resources/views "$backup_dir"
    echo "📦 Old views backed up to: $backup_dir"
fi

# Create the PHP generator script
cat > /tmp/generate-views.php << 'PHPEOF'
<?php
$viewsDir = __DIR__ . '/resources/views';

function writeFile($path, $content) {
    $dir = dirname($path);
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    file_put_contents($path, $content);
    echo "✅ " . str_replace(__DIR__ . '/', '', $path) . "\n";
}

// ============================================================
// 1. LAYOUT
// ============================================================
writeFile("$viewsDir/layouts/app.blade.php", <<<'BLADE'
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Digital Star Consultants')</title>
    <meta name="description" content="@yield('meta_description', 'Practical digital services for government, business, and personal needs.')">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>body{font-family:'Inter',sans-serif;}</style>
    @stack('styles')
</head>
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen flex flex-col">
    @include('partials.nav')
    @include('partials.alerts')
    <main id="main-content" class="flex-1">@yield('content')</main>
    @include('partials.footer')
    @stack('scripts')
</body>
</html>
BLADE);

// ============================================================
// 2. NAV
// ============================================================
writeFile("$viewsDir/partials/nav.blade.php", <<<'BLADE'
<nav class="bg-white border-b border-slate-200 sticky top-0 z-40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                <div class="w-8 h-8 bg-amber-500 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                </div>
                <span class="text-xl font-bold text-slate-900">Digital Star</span>
            </a>
            <div class="hidden md:flex items-center gap-1">
                <a href="{{ route('home') }}" class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('home') ? 'text-slate-900 bg-slate-100' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">Home</a>
                <a href="{{ route('public.services.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('public.services.*') ? 'text-slate-900 bg-slate-100' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">Services</a>
            </div>
            <div class="hidden md:flex items-center gap-3">
                @auth
                    @php $dash = auth()->user()->isManagement() ? 'admin.dashboard' : 'staff.submissions'; @endphp
                    <a href="{{ route($dash) }}" class="px-4 py-2 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100">Dashboard</a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">@csrf<button class="px-4 py-2 rounded-lg text-sm font-medium text-slate-500 hover:text-rose-600">Sign out</button></form>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2 rounded-lg text-sm font-medium text-slate-600 hover:text-slate-900">Sign in</a>
                    <a href="{{ route('public.services.index') }}" class="px-4 py-2 rounded-lg text-sm font-semibold text-white bg-slate-900 hover:bg-slate-800">Start your request</a>
                @endauth
            </div>
            <button onclick="document.getElementById('mobile-menu').classList.toggle('hidden')" class="md:hidden p-2 rounded-lg text-slate-600 hover:bg-slate-100">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
        </div>
    </div>
    <div id="mobile-menu" class="hidden md:hidden border-t border-slate-100 bg-white">
        <div class="px-4 py-3 space-y-1">
            <a href="{{ route('home') }}" class="block px-3 py-2 rounded-lg text-sm text-slate-700 hover:bg-slate-50">Home</a>
            <a href="{{ route('public.services.index') }}" class="block px-3 py-2 rounded-lg text-sm text-slate-700 hover:bg-slate-50">Services</a>
            @auth
                @php $dash = auth()->user()->isManagement() ? 'admin.dashboard' : 'staff.submissions'; @endphp
                <a href="{{ route($dash) }}" class="block px-3 py-2 rounded-lg text-sm text-slate-700 hover:bg-slate-50">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="block px-3 py-2 rounded-lg text-sm text-slate-700 hover:bg-slate-50">Sign in</a>
            @endauth
        </div>
    </div>
</nav>
BLADE);

// ============================================================
// 3. ALERTS
// ============================================================
writeFile("$viewsDir/partials/alerts.blade.php", <<<'BLADE'
@if(session('success') || session('status') || session('message'))
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
        <div class="rounded-lg bg-emerald-50 border border-emerald-200 p-4 flex items-start gap-3">
            <svg class="w-5 h-5 text-emerald-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div class="text-sm text-emerald-800 font-medium">{{ session('success') ?? session('status') ?? session('message') }}</div>
            <button onclick="this.parentElement.remove()" class="ml-auto text-emerald-400 hover:text-emerald-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
    </div>
@endif
@if(session('error'))
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
        <div class="rounded-lg bg-rose-50 border border-rose-200 p-4 flex items-start gap-3">
            <svg class="w-5 h-5 text-rose-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div class="text-sm text-rose-800 font-medium">{{ session('error') }}</div>
            <button onclick="this.parentElement.remove()" class="ml-auto text-rose-400 hover:text-rose-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
    </div>
@endif
@if($errors->any())
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
        <div class="rounded-lg bg-amber-50 border border-amber-200 p-4">
            <div class="flex items-center gap-2 mb-2">
                <svg class="w-5 h-5 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span class="text-sm font-semibold text-amber-800">Please fix the following:</span>
            </div>
            <ul class="ml-7 space-y-1">
                @foreach($errors->all() as $error)<li class="text-sm text-amber-700">{{ $error }}</li>@endforeach
            </ul>
        </div>
    </div>
@endif
BLADE);

// ============================================================
// 4. FOOTER
// ============================================================
writeFile("$viewsDir/partials/footer.blade.php", <<<'BLADE'
<footer class="bg-slate-900 text-slate-300 mt-auto">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div>
                <a href="{{ route('home') }}" class="flex items-center gap-2 mb-4">
                    <div class="w-8 h-8 bg-amber-500 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    </div>
                    <span class="text-lg font-bold text-white">Digital Star</span>
                </a>
                <p class="text-sm text-slate-400">Practical digital services for government, business, and personal needs across Tanzania.</p>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-white uppercase tracking-wider mb-3">Services</h3>
                <ul class="space-y-2">
                    <li><a href="{{ route('public.services.index') }}" class="text-sm text-slate-400 hover:text-amber-400">All services</a></li>
                    <li><a href="{{ route('public.services.index', ['category' => 'government']) }}" class="text-sm text-slate-400 hover:text-amber-400">Government</a></li>
                    <li><a href="{{ route('public.services.index', ['category' => 'business']) }}" class="text-sm text-slate-400 hover:text-amber-400">Business</a></li>
                </ul>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-white uppercase tracking-wider mb-3">Company</h3>
                <ul class="space-y-2">
                    <li><a href="{{ route('home') }}" class="text-sm text-slate-400 hover:text-amber-400">About us</a></li>
                    <li><a href="{{ route('public.services.index') }}" class="text-sm text-slate-400 hover:text-amber-400">Industries</a></li>
                </ul>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-white uppercase tracking-wider mb-3">Contact</h3>
                <ul class="space-y-2 text-sm text-slate-400">
                    <li>hello@digitalstar.consulting</li>
                    <li>+255 712 345 678</li>
                    <li>Mon–Fri, 8:00–18:00</li>
                </ul>
            </div>
        </div>
        <div class="border-t border-slate-800 mt-8 pt-6 text-center">
            <p class="text-xs text-slate-500">&copy; {{ date('Y') }} Digital Star Consultants. All rights reserved.</p>
        </div>
    </div>
</footer>
BLADE);

PHPEOF

echo "✅ Part 1 complete (layout + partials)"

# Continue PHP generator with public pages
cat >> /tmp/generate-views.php << 'PHPEOF'

// ============================================================
// 5. HOME
// ============================================================
writeFile("$viewsDir/home.blade.php", <<<'BLADE'
@extends('layouts.app')
@section('title', 'Digital Star Consultants — Make important work move')
@section('content')

<section class="bg-slate-900 py-20 lg:py-28">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl">
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-amber-500/10 border border-amber-500/20 text-amber-400 text-xs font-semibold uppercase tracking-wide mb-6">
                Trusted across Tanzania
            </div>
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-white leading-tight mb-6">
                Move the work that <span class="text-amber-400">matters</span> forward.
            </h1>
            <p class="text-lg sm:text-xl text-slate-300 leading-relaxed mb-8 max-w-2xl">
                From government requests to business systems, we turn complex next steps into clear, confident progress.
            </p>
            <div class="flex flex-wrap items-center gap-4 mb-10">
                <a href="{{ route('public.services.index') }}" class="px-6 py-3.5 rounded-xl text-sm font-semibold text-slate-900 bg-amber-400 hover:bg-amber-300 transition-colors">Browse services</a>
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

<section class="bg-white border-b border-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="text-center"><div class="text-3xl font-bold text-slate-900">12,400+</div><div class="text-sm text-slate-500 mt-1">Requests completed</div></div>
            <div class="text-center"><div class="text-3xl font-bold text-slate-900">48h</div><div class="text-sm text-slate-500 mt-1">Average response</div></div>
            <div class="text-center"><div class="text-3xl font-bold text-slate-900">26</div><div class="text-sm text-slate-500 mt-1">Regions served</div></div>
            <div class="text-center"><div class="text-3xl font-bold text-slate-900">98%</div><div class="text-sm text-slate-500 mt-1">Client satisfaction</div></div>
        </div>
    </div>
</section>

<section class="py-20 lg:py-24 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-14">
            <span class="text-sm font-semibold text-amber-600 uppercase tracking-wide">What we help with</span>
            <h2 class="text-3xl sm:text-4xl font-bold text-slate-900 mt-3">One place for the next right move.</h2>
            <p class="text-slate-500 mt-4">Browse practical services built around real needs. Choose a starting point and we will take it from there.</p>
        </div>
        @if(isset($categories) && count($categories))
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($categories as $category)
                    <a href="{{ route('public.services.index', ['category' => data_get($category, 'slug')]) }}" class="group bg-white rounded-2xl p-6 border border-slate-100 hover:border-amber-200 hover:shadow-lg transition-all">
                        <div class="flex items-start justify-between mb-4">
                            <span class="text-3xl">{{ data_get($category, 'icon', '✨') }}</span>
                            <span class="text-xs font-mono text-slate-300 group-hover:text-amber-500">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <h3 class="text-lg font-semibold text-slate-900 group-hover:text-amber-700">{{ data_get($category, 'name') }}</h3>
                        <p class="text-sm text-slate-500 mt-2">{{ data_get($category, 'description', 'Focused support with a clear outcome.') }}</p>
                        @if(data_get($category, 'services') && collect(data_get($category, 'services'))->isNotEmpty())
                            <div class="mt-4 pt-4 border-t border-slate-50">
                                <ul class="space-y-1">
                                    @foreach(collect(data_get($category, 'services'))->take(3) as $svc)
                                        <li class="text-xs text-slate-400 flex items-center gap-1.5"><span class="w-1 h-1 rounded-full bg-amber-400"></span>{{ data_get($svc, 'name') }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </a>
                @endforeach
            </div>
        @else
            <div class="text-center py-16">
                <p class="text-slate-500">Services are being curated. Check back shortly.</p>
            </div>
        @endif
        <div class="text-center mt-10">
            <a href="{{ route('public.services.index') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl text-sm font-semibold text-slate-700 bg-white border border-slate-200 hover:border-amber-300">View all services <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg></a>
        </div>
    </div>
</section>

<section class="py-20 lg:py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-14">
            <span class="text-sm font-semibold text-amber-600 uppercase tracking-wide">Why Digital Star</span>
            <h2 class="text-3xl sm:text-4xl font-bold text-slate-900 mt-3">Less chasing. More done.</h2>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="text-center">
                <div class="w-14 h-14 bg-amber-50 rounded-2xl flex items-center justify-center mx-auto mb-4"><svg class="w-7 h-7 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg></div>
                <h3 class="text-lg font-semibold text-slate-900">Clarity</h3>
                <p class="text-sm text-slate-500 mt-2">Every step explained in plain language. No jargon, no confusion.</p>
            </div>
            <div class="text-center">
                <div class="w-14 h-14 bg-emerald-50 rounded-2xl flex items-center justify-center mx-auto mb-4"><svg class="w-7 h-7 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg></div>
                <h3 class="text-lg font-semibold text-slate-900">Speed</h3>
                <p class="text-sm text-slate-500 mt-2">Most requests receive a response within two business days.</p>
            </div>
            <div class="text-center">
                <div class="w-14 h-14 bg-blue-50 rounded-2xl flex items-center justify-center mx-auto mb-4"><svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
                <h3 class="text-lg font-semibold text-slate-900">Trust</h3>
                <p class="text-sm text-slate-500 mt-2">Your data is handled with enterprise-grade security.</p>
            </div>
            <div class="text-center">
                <div class="w-14 h-14 bg-violet-50 rounded-2xl flex items-center justify-center mx-auto mb-4"><svg class="w-7 h-7 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
                <h3 class="text-lg font-semibold text-slate-900">Reach</h3>
                <p class="text-sm text-slate-500 mt-2">Multilingual support across all regions of Tanzania.</p>
            </div>
        </div>
    </div>
</section>

<section class="py-20 lg:py-24 bg-slate-900">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl sm:text-4xl font-bold text-white mb-6">Ready to make your next move?</h2>
        <p class="text-lg text-slate-300 mb-8 max-w-xl mx-auto">Choose a service, tell us what you need, and we will handle the rest. No account required.</p>
        <a href="{{ route('public.services.index') }}" class="inline-flex items-center gap-2 px-8 py-4 rounded-xl text-base font-semibold text-slate-900 bg-amber-400 hover:bg-amber-300 transition-colors">Browse all services <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg></a>
    </div>
</section>

@endsection
BLADE);

PHPEOF

echo "✅ Part 2 complete (home page)"

# Services pages
cat >> /tmp/generate-views.php << 'PHPEOF'

// ============================================================
// 6. SERVICES INDEX
// ============================================================
writeFile("$viewsDir/services/index.blade.php", <<<'BLADE'
@extends('layouts.app')
@section('title', 'Services — Digital Star Consultants')
@section('content')

<section class="bg-slate-900 py-16 lg:py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl">
            <span class="text-sm font-semibold text-amber-400 uppercase tracking-wide">The service directory</span>
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-white mt-3 leading-tight">Clear, practical support for digital, government, and business needs.</h1>
            <p class="text-slate-300 mt-4 text-lg">Choose a category or search to begin. Every service is designed around a real outcome.</p>
        </div>
    </div>
</section>

<section class="bg-white border-b border-slate-100 sticky top-16 z-30">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <form method="GET" action="{{ route('public.services.index') }}" class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Search services..." class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-400">
            </div>
            <select name="category" onchange="this.form.submit()" class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-400 min-w-[160px]">
                <option value="">All categories</option>
                @if(isset($categories))
                    @foreach($categories as $cat)
                        <option value="{{ data_get($cat, 'slug') }}" {{ ($selectedCategory ?? '') == data_get($cat, 'slug') ? 'selected' : '' }}>{{ data_get($cat, 'name') }}</option>
                    @endforeach
                @endif
            </select>
            @if(($search ?? '') || ($selectedCategory ?? ''))
                <a href="{{ route('public.services.index') }}" class="px-4 py-2.5 rounded-xl text-sm font-medium text-slate-500 hover:text-rose-600 border border-slate-200">Clear</a>
            @endif
        </form>
    </div>
</section>

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
                        <h3 class="text-lg font-semibold text-slate-700">No services found</h3>
                        <p class="text-sm text-slate-500 mt-1">Try a different search term or category.</p>
                    </div>
                @endforelse
            </div>
        @endif
        @if(method_exists($services, 'links'))
            <div class="mt-10">{{ $services->withQueryString()->links() }}</div>
        @endif
    </div>
</section>

@endsection
BLADE);

// ============================================================
// 7. SERVICE CARD PARTIAL
// ============================================================
writeFile("$viewsDir/services/_card.blade.php", <<<'BLADE'
@php
    $price = data_get($service, 'formatted_price') ?? (data_get($service, 'price') ? number_format(data_get($service, 'price')) . ' TZS' : null);
    $duration = data_get($service, 'duration');
    $categoryName = data_get($service, 'category.name') ?? data_get($service, 'category_name');
@endphp
<a href="{{ route('public.services.show', data_get($service, 'slug')) }}" class="group bg-white rounded-2xl border border-slate-100 hover:border-amber-200 hover:shadow-lg transition-all p-5 flex flex-col h-full">
    <div class="flex items-start justify-between mb-3">
        @if($categoryName)
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-600 group-hover:bg-amber-50 group-hover:text-amber-700 transition-colors">{{ $categoryName }}</span>
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
BLADE);

PHPEOF

echo "✅ Part 3 complete (services index + card)"

# Service show page (THE FORM)
cat >> /tmp/generate-views.php << 'PHPEOF'

// ============================================================
// 8. SERVICES SHOW — THE FORM
// ============================================================
writeFile("$viewsDir/services/show.blade.php", <<<'BLADE'
@extends('layouts.app')
@section('title', data_get($service, 'name', 'Service request').' — Digital Star Consultants')
@section('content')

@php
    $price = data_get($service, 'formatted_price') ?? (data_get($service, 'price') ? number_format(data_get($service, 'price')) . ' TZS' : null);
    $duration = data_get($service, 'duration');
    $category = data_get($service, 'category');
    $allFields = data_get($service, 'fields', collect())->sortBy('sort_order');
    $serviceName = strtolower(data_get($service, 'name', ''));
    $categoryName = strtolower(data_get($category, 'name', ''));

    $agency = match(true) {
        str_contains($serviceName, 'tin') || str_contains($serviceName, 'tax') || str_contains($serviceName, 'tra') || str_contains($categoryName, 'tra') || str_contains($categoryName, 'tax') => 'tra',
        str_contains($serviceName, 'brela') || str_contains($serviceName, 'business') || str_contains($serviceName, 'company') || str_contains($serviceName, 'ngo') => 'brela',
        str_contains($serviceName, 'nida') || str_contains($serviceName, 'passport') || str_contains($serviceName, 'visa') || str_contains($serviceName, 'immigration') || str_contains($serviceName, 'residence') || str_contains($categoryName, 'immigration') || str_contains($categoryName, 'travel') => 'immigration',
        str_contains($serviceName, 'rita') || str_contains($serviceName, 'birth') || str_contains($serviceName, 'death') || str_contains($serviceName, 'marriage') => 'rita',
        str_contains($serviceName, 'police') || str_contains($serviceName, 'clearance') || str_contains($serviceName, 'conduct') || str_contains($serviceName, 'loss') || str_contains($serviceName, 'driving') || str_contains($serviceName, 'vehicle') => 'police',
        default => 'generic',
    };

    $agencyConfig = match($agency) {
        'tra' => ['name' => 'Tanzania Revenue Authority (TRA)', 'color' => 'blue', 'bg' => 'bg-blue-50', 'border' => 'border-blue-200', 'text' => 'text-blue-800'],
        'brela' => ['name' => 'Business Registrations & Licensing Agency (BRELA)', 'color' => 'emerald', 'bg' => 'bg-emerald-50', 'border' => 'border-emerald-200', 'text' => 'text-emerald-800'],
        'immigration' => ['name' => 'Immigration Department — Tanzania', 'color' => 'amber', 'bg' => 'bg-amber-50', 'border' => 'border-amber-200', 'text' => 'text-amber-800'],
        'rita' => ['name' => 'Registration, Insolvency & Trusteeship Agency (RITA)', 'color' => 'violet', 'bg' => 'bg-violet-50', 'border' => 'border-violet-200', 'text' => 'text-violet-800'],
        'police' => ['name' => 'Tanzania Police Force', 'color' => 'rose', 'bg' => 'bg-rose-50', 'border' => 'border-rose-200', 'text' => 'text-rose-800'],
        default => ['name' => data_get($category, 'name', 'Government Service'), 'color' => 'slate', 'bg' => 'bg-slate-50', 'border' => 'border-slate-200', 'text' => 'text-slate-800'],
    };

    $groupedFields = ['personal' => [], 'identification' => [], 'contact' => [], 'application' => [], 'documents' => []];
    foreach ($allFields as $field) {
        $key = data_get($field, 'field_key', '');
        if (str_starts_with($key, 'upload_')) { $groupedFields['documents'][] = $field; }
        elseif (in_array($key, ['full_name','date_of_birth','gender','place_of_birth','marital_status','nationality','occupation','home_address','current_address','residential_address','permanent_address'])) { $groupedFields['personal'][] = $field; }
        elseif (in_array($key, ['nida_number','tin_number','passport_number','nida_or_passport_number','existing_licence_number','registration_number','document_number'])) { $groupedFields['identification'][] = $field; }
        elseif (in_array($key, ['email','phone','contact_name'])) { $groupedFields['contact'][] = $field; }
        else { $groupedFields['application'][] = $field; }
    }
    $sectionLabels = ['personal' => 'Personal Information', 'identification' => 'Identification Details', 'contact' => 'Contact Information', 'application' => 'Application Details', 'documents' => 'Required Documents'];
@endphp

<section class="bg-slate-900 py-12 lg:py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <a href="{{ route('public.services.index') }}" class="inline-flex items-center gap-1.5 text-sm text-slate-400 hover:text-amber-400 mb-6">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to services
        </a>
        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-xl {{ $agencyConfig['bg'] }} border {{ $agencyConfig['border'] }} mb-5">
            <span class="text-sm font-semibold {{ $agencyConfig['text'] }}">{{ $agencyConfig['name'] }}</span>
        </div>
        @if(data_get($service, 'is_active'))
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 mb-4">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 mr-1.5 animate-pulse"></span>Available now
            </span>
        @endif
        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-white leading-tight">{{ data_get($service, 'name') }}</h1>
        <p class="text-slate-300 mt-4 text-lg max-w-3xl">{{ data_get($service, 'description', data_get($service, 'short_description', 'Professional service with clear outcomes.')) }}</p>
        <div class="flex flex-wrap items-center gap-6 mt-6 text-sm">
            @if($price)
                <div class="flex items-center gap-2 text-amber-400"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><span class="font-semibold">{{ $price }}</span></div>
            @endif
            @if($duration)
                <div class="flex items-center gap-2 text-slate-400"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><span>{{ $duration }}</span></div>
            @endif
            <div class="flex items-center gap-2 text-slate-400"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><span>{{ $allFields->count() }} fields to complete</span></div>
        </div>
    </div>
</section>

<section class="py-12 lg:py-16 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-10 lg:gap-16">
            <div class="lg:col-span-2 space-y-8">
                <div class="bg-white rounded-2xl border border-slate-100 p-6">
                    <h2 class="text-lg font-bold text-slate-900 mb-4">What happens next</h2>
                    <div class="space-y-4">
                        <div class="flex gap-4">
                            <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center text-sm font-bold shrink-0">1</div>
                            <div><h4 class="text-sm font-semibold text-slate-800">Fill in your details</h4><p class="text-sm text-slate-500 mt-0.5">Complete all required fields. Fields marked with <span class="text-rose-500">*</span> are mandatory.</p></div>
                        </div>
                        <div class="flex gap-4">
                            <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center text-sm font-bold shrink-0">2</div>
                            <div><h4 class="text-sm font-semibold text-slate-800">We process your request</h4><p class="text-sm text-slate-500 mt-0.5">Our team submits to {{ $agencyConfig['name'] }} and tracks progress.</p></div>
                        </div>
                        <div class="flex gap-4">
                            <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center text-sm font-bold shrink-0">3</div>
                            <div><h4 class="text-sm font-semibold text-slate-800">Track with your reference</h4><p class="text-sm text-slate-500 mt-0.5">Use your reference number to check status anytime.</p></div>
                        </div>
                    </div>
                </div>
                @if(count($groupedFields['documents']))
                <div class="bg-white rounded-2xl border border-slate-100 p-6">
                    <h3 class="text-sm font-semibold text-slate-800 mb-3">Documents you will need</h3>
                    <ul class="space-y-2">
                        @foreach($groupedFields['documents'] as $doc)
                            <li class="flex items-center gap-2 text-sm text-slate-600">
                                <span class="w-5 h-5 rounded border-2 border-slate-200 flex items-center justify-center"><svg class="w-3 h-3 text-slate-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg></span>
                                {{ data_get($doc, 'label') }}
                                @if(data_get($doc, 'is_required'))<span class="text-xs text-rose-500">required</span>@else<span class="text-xs text-slate-400">optional</span>@endif
                            </li>
                        @endforeach
                    </ul>
                    <p class="text-xs text-slate-400 mt-3">Accepted: PDF, JPG, PNG, DOC. Max 10MB each.</p>
                </div>
                @endif
                <div class="bg-white rounded-2xl border border-slate-100 p-6">
                    <h3 class="text-sm font-semibold text-slate-800 mb-3">Your data is protected</h3>
                    <ul class="space-y-2 text-sm text-slate-500">
                        <li class="flex items-center gap-2"><span class="w-1 h-1 rounded-full bg-slate-300"></span>SSL-encrypted submission</li>
                        <li class="flex items-center gap-2"><span class="w-1 h-1 rounded-full bg-slate-300"></span>Shared only with {{ $agencyConfig['name'] }}</li>
                        <li class="flex items-center gap-2"><span class="w-1 h-1 rounded-full bg-slate-300"></span>No marketing emails</li>
                    </ul>
                </div>
            </div>

            <div class="lg:col-span-3">
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50">
                        <div class="flex items-center justify-between">
                            <h2 class="text-lg font-bold text-slate-900">Service Application Form</h2>
                            <span class="text-xs font-medium px-2.5 py-1 rounded-full bg-slate-100 text-slate-500">{{ $allFields->where('is_required', true)->count() }} required</span>
                        </div>
                        <p class="text-sm text-slate-500 mt-1">Complete all sections below. Required fields are marked with <span class="text-rose-500">*</span></p>
                    </div>

                    <form id="submissionForm" method="POST" action="{{ route('public.submissions.store') }}" enctype="multipart/form-data" class="px-6 py-6 space-y-8">
                        @csrf
                        <input type="hidden" name="service_id" value="{{ data_get($service, 'id') }}">

                        {{-- Base Contact --}}
                        <div>
                            <h3 class="text-sm font-semibold text-slate-800 mb-4">Your Contact Details</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Full name <span class="text-rose-500">*</span></label>
                                    <input type="text" name="customer_name" value="{{ old('customer_name') }}" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-400 placeholder:text-slate-300" placeholder="e.g. Juma Abdallah">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Phone number <span class="text-rose-500">*</span></label>
                                    <input type="tel" name="customer_phone" value="{{ old('customer_phone') }}" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-400 placeholder:text-slate-300" placeholder="+255 712 345 678">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Email address</label>
                                    <input type="email" name="customer_email" value="{{ old('customer_email') }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-400 placeholder:text-slate-300" placeholder="juma@example.com">
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Preferred date for follow-up</label>
                                    <input type="date" name="preferred_date" value="{{ old('preferred_date') }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-400">
                                </div>
                            </div>
                        </div>
BLADE);

PHPEOF

echo "✅ Part 4a complete (service show header + form start)"

# Continue service show page
cat >> /tmp/generate-views.php << 'PHPEOF'

// Continue services/show.blade.php
file_put_contents("$viewsDir/services/show.blade.php", file_get_contents("$viewsDir/services/show.blade.php") . <<<'BLADE'

                        {{-- Dynamic Fields --}}
                        @foreach($groupedFields as $groupKey => $groupFields)
                            @if(count($groupFields))
                                <div class="border-t border-slate-100 pt-6">
                                    <h3 class="text-sm font-semibold text-slate-800 mb-4">{{ $sectionLabels[$groupKey] }}</h3>
                                    <div class="space-y-5">
                                        @foreach($groupFields as $field)
                                            @php
                                                $key = data_get($field, 'field_key');
                                                $type = data_get($field, 'field_type', 'text');
                                                $label = data_get($field, 'label');
                                                $required = data_get($field, 'is_required', false);
                                                $placeholder = data_get($field, 'placeholder');
                                                $help = data_get($field, 'help_text');
                                                $options = data_get($field, 'options', []);
                                                if (is_string($options)) { $options = json_decode($options, true) ?: array_filter(array_map('trim', explode(',', $options))); }
                                                $oldValue = old("fields.{$key}");
                                            @endphp
                                            <div>
                                                <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ $label }} @if($required)<span class="text-rose-500">*</span>@endif</label>

                                                @if($type === 'textarea')
                                                    <textarea name="fields[{{ $key }}]" @if($required) required @endif rows="3" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-400 placeholder:text-slate-300 resize-y" placeholder="{{ $placeholder }}">{{ $oldValue }}</textarea>

                                                @elseif($type === 'select')
                                                    <select name="fields[{{ $key }}]" @if($required) required @endif class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-400">
                                                        <option value="">Select {{ strtolower($label) }}</option>
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
                                                                <input type="{{ $type === 'checkbox' ? 'checkbox' : 'radio' }}" name="fields[{{ $key }}]{{ $type === 'checkbox' ? '[]' : '' }}" value="{{ $optValue }}" {{ $isChecked ? 'checked' : '' }} @if($required) required @endif class="w-4 h-4 text-amber-600 border-slate-300 focus:ring-amber-500">
                                                                <span class="text-sm text-slate-700">{{ $optLabel }}</span>
                                                            </label>
                                                        @endforeach
                                                    </div>

                                                @elseif($type === 'file')
                                                    <div class="relative">
                                                        <div class="flex items-center gap-3 p-3 rounded-xl border-2 border-dashed border-slate-200 hover:border-amber-300 hover:bg-amber-50/20 transition-colors cursor-pointer" onclick="document.getElementById('field_{{ $key }}').click()">
                                                            <div class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center shrink-0"><svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg></div>
                                                            <div class="flex-1 min-w-0">
                                                                <p id="file-label-{{ $key }}" class="text-sm text-slate-600 truncate">Click to upload or drag and drop</p>
                                                                <p class="text-xs text-slate-400">PDF, JPG, PNG, DOC up to 10MB</p>
                                                            </div>
                                                        </div>
                                                        <input type="file" id="field_{{ $key }}" name="fields[{{ $key }}]" @if($required) required @endif class="hidden" onchange="document.getElementById('file-label-{{ $key }}').textContent = this.files[0]?.name || 'Click to upload or drag and drop'; this.previousElementSibling.classList.add('border-amber-300', 'bg-amber-50/20');">
                                                    </div>

                                                @elseif($type === 'date')
                                                    <input type="date" name="fields[{{ $key }}]" value="{{ $oldValue }}" @if($required) required @endif class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-400">

                                                @elseif($type === 'number')
                                                    <input type="number" name="fields[{{ $key }}]" value="{{ $oldValue }}" @if($required) required @endif class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-400 placeholder:text-slate-300" placeholder="{{ $placeholder }}">

                                                @elseif($type === 'email')
                                                    <input type="email" name="fields[{{ $key }}]" value="{{ $oldValue }}" @if($required) required @endif class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-400 placeholder:text-slate-300" placeholder="{{ $placeholder }}">

                                                @elseif($type === 'tel')
                                                    <input type="tel" name="fields[{{ $key }}]" value="{{ $oldValue }}" @if($required) required @endif class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-400 placeholder:text-slate-300" placeholder="{{ $placeholder ?? '+255 XXX XXX XXX' }}">

                                                @else
                                                    <input type="text" name="fields[{{ $key }}]" value="{{ $oldValue }}" @if($required) required @endif class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-400 placeholder:text-slate-300" placeholder="{{ $placeholder }}">
                                                @endif

                                                @if($help)
                                                    <p class="text-xs text-slate-400 mt-1.5 flex items-start gap-1"><svg class="w-3.5 h-3.5 text-slate-300 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>{{ $help }}</p>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endforeach

                        {{-- Notes --}}
                        <div class="border-t border-slate-100 pt-6">
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Additional notes (optional)</label>
                            <textarea name="customer_notes" rows="3" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-400 placeholder:text-slate-300 resize-y" placeholder="Any extra information that will help us process your request faster...">{{ old('customer_notes') }}</textarea>
                        </div>

                        {{-- Submit --}}
                        <div class="border-t border-slate-100 pt-6">
                            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                                <p class="text-xs text-slate-400">By submitting, you confirm the information is accurate.</p>
                                <button type="submit" id="submitBtn" class="w-full sm:w-auto px-8 py-3 rounded-xl text-sm font-semibold text-white bg-slate-900 hover:bg-slate-800 transition-colors shadow-sm flex items-center justify-center gap-2">
                                    <span>Start your request</span>
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

{{-- Success Modal --}}
<div id="successModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-8 text-center">
        <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <h3 class="text-xl font-bold text-slate-900 mb-2">Application submitted!</h3>
        <p class="text-sm text-slate-500 mb-4">We have received your application for <strong>{{ data_get($service, 'name') }}</strong>.</p>
        <div class="bg-slate-50 rounded-xl p-4 mb-6">
            <div class="text-xs text-slate-400 uppercase tracking-wide mb-1">Your reference number</div>
            <div id="refNumber" class="text-2xl font-mono font-bold text-slate-900 tracking-wider">---</div>
        </div>
        <div class="flex flex-col gap-2">
            <a id="trackLink" href="#" class="px-4 py-2.5 rounded-xl text-sm font-semibold text-white bg-slate-900 hover:bg-slate-800 transition-colors">Track your application</a>
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
        const response = await fetch(this.action, { method: 'POST', body: formData, headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }});
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
BLADE);

PHPEOF

echo "✅ Part 4b complete (service show form + modal)"

# Auth + Track + Admin pages
cat >> /tmp/generate-views.php << 'PHPEOF'

// ============================================================
// 9. AUTH LOGIN
// ============================================================
writeFile("$viewsDir/auth/login.blade.php", <<<'BLADE'
@extends('layouts.app')
@section('title', 'Sign in — Digital Star Consultants')
@section('content')
<section class="min-h-screen flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <div class="w-12 h-12 bg-amber-500 rounded-lg flex items-center justify-center mx-auto mb-4">
                <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
            </div>
            <h1 class="text-2xl font-bold text-slate-900">Welcome back</h1>
            <p class="text-sm text-slate-500 mt-1">Sign in to your Digital Star account</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Email address</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-400 placeholder:text-slate-300" placeholder="you@example.com">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Password</label>
                    <input type="password" name="password" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-400 placeholder:text-slate-300" placeholder="••••••••">
                </div>
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="remember" class="w-4 h-4 text-amber-600 border-slate-300 rounded focus:ring-amber-500">
                        <span class="text-sm text-slate-600">Remember me</span>
                    </label>
                    <a href="{{ route('password.request') }}" class="text-sm text-amber-600 hover:text-amber-700">Forgot password?</a>
                </div>
                <button type="submit" class="w-full px-4 py-3 rounded-xl text-sm font-semibold text-white bg-slate-900 hover:bg-slate-800 transition-colors">Sign in</button>
            </form>
        </div>
        <p class="text-center text-sm text-slate-500 mt-6">Need an account? <a href="{{ route('register') }}" class="text-amber-600 hover:text-amber-700 font-medium">Contact your administrator</a></p>
    </div>
</section>
@endsection
BLADE);

// ============================================================
// 10. TRACK SUBMISSION
// ============================================================
writeFile("$viewsDir/submissions/track.blade.php", <<<'BLADE'
@extends('layouts.app')
@section('title', 'Track Your Request — Digital Star Consultants')
@section('content')
<section class="bg-slate-900 py-16 lg:py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-3xl sm:text-4xl font-bold text-white mb-4">Track your request</h1>
        <p class="text-slate-300 max-w-xl mx-auto">Enter your reference number to check the status of your application.</p>
    </div>
</section>
<section class="py-12 lg:py-16 bg-slate-50">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-8">
            <form method="GET" action="{{ route('public.submissions.track', ['reference' => '']) }}" class="flex gap-3" onsubmit="event.preventDefault(); const ref = this.querySelector('input').value; if(ref) window.location.href = '{{ url('/track') }}/' + ref;">
                <input type="text" name="reference" placeholder="Enter reference number (e.g. DSC-2026-001234)" class="flex-1 px-4 py-3 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-400 placeholder:text-slate-300" required>
                <button type="submit" class="px-6 py-3 rounded-xl text-sm font-semibold text-white bg-slate-900 hover:bg-slate-800 transition-colors">Track</button>
            </form>
        </div>
    </div>
</section>
@endsection
BLADE);

// ============================================================
// 11. ADMIN DASHBOARD
// ============================================================
writeFile("$viewsDir/admin/dashboard.blade.php", <<<'BLADE'
@extends('layouts.app')
@section('title', 'Dashboard — Admin')
@section('content')
<section class="bg-slate-900 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-bold text-white">Admin Dashboard</h1>
        <p class="text-slate-400 text-sm mt-1">Overview of all activities</p>
    </div>
</section>
<section class="py-8 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
            <div class="bg-white rounded-2xl border border-slate-100 p-5">
                <div class="text-sm text-slate-500">Total Submissions</div>
                <div class="text-3xl font-bold text-slate-900 mt-1">{{ $totalSubmissions ?? 0 }}</div>
            </div>
            <div class="bg-white rounded-2xl border border-slate-100 p-5">
                <div class="text-sm text-slate-500">Pending</div>
                <div class="text-3xl font-bold text-amber-600 mt-1">{{ $pendingSubmissions ?? 0 }}</div>
            </div>
            <div class="bg-white rounded-2xl border border-slate-100 p-5">
                <div class="text-sm text-slate-500">In Progress</div>
                <div class="text-3xl font-bold text-blue-600 mt-1">{{ $inProgressSubmissions ?? 0 }}</div>
            </div>
            <div class="bg-white rounded-2xl border border-slate-100 p-5">
                <div class="text-sm text-slate-500">Completed</div>
                <div class="text-3xl font-bold text-emerald-600 mt-1">{{ $completedSubmissions ?? 0 }}</div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h2 class="text-lg font-bold text-slate-900">Recent Submissions</h2>
                <a href="{{ route('admin.submissions.index') }}" class="text-sm text-amber-600 hover:text-amber-700">View all</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                        <tr><th class="px-6 py-3">Reference</th><th class="px-6 py-3">Service</th><th class="px-6 py-3">Customer</th><th class="px-6 py-3">Status</th><th class="px-6 py-3">Date</th></tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($recentSubmissions ?? [] as $sub)
                            <tr class="hover:bg-slate-50">
                                <td class="px-6 py-4 font-mono text-slate-900">{{ $sub->reference_number }}</td>
                                <td class="px-6 py-4 text-slate-600">{{ $sub->service->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-slate-600">{{ $sub->customer_name }}</td>
                                <td class="px-6 py-4"><span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $sub->status_color ?? 'slate' }}-100 text-{{ $sub->status_color ?? 'slate' }}-700">{{ $sub->status_label ?? $sub->status }}</span></td>
                                <td class="px-6 py-4 text-slate-500">{{ $sub->created_at->format('M d, Y') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-6 py-8 text-center text-slate-500">No submissions yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
@endsection
BLADE);

PHPEOF

echo "✅ Part 5 complete (auth, track, admin dashboard)"

# Admin services, submissions, users + staff pages
cat >> /tmp/generate-views.php << 'PHPEOF'

// ============================================================
// 12. ADMIN SERVICES INDEX
// ============================================================
writeFile("$viewsDir/admin/services/index.blade.php", <<<'BLADE'
@extends('layouts.app')
@section('title', 'Services — Admin')
@section('content')
<section class="bg-slate-900 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between">
        <div><h1 class="text-2xl font-bold text-white">Services</h1><p class="text-slate-400 text-sm mt-1">Manage all available services</p></div>
        <a href="{{ route('admin.services.create') }}" class="px-4 py-2 rounded-xl text-sm font-semibold text-white bg-amber-500 hover:bg-amber-400 transition-colors">Add service</a>
    </div>
</section>
<section class="py-8 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50 text-xs uppercase text-slate-500"><tr><th class="px-6 py-3">Name</th><th class="px-6 py-3">Category</th><th class="px-6 py-3">Price</th><th class="px-6 py-3">Status</th><th class="px-6 py-3 text-right">Actions</th></tr></thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($services ?? [] as $svc)
                            <tr class="hover:bg-slate-50">
                                <td class="px-6 py-4 font-medium text-slate-900">{{ $svc->name }}</td>
                                <td class="px-6 py-4 text-slate-600">{{ $svc->category->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-slate-600">{{ $svc->formatted_price ?? ($svc->price ? number_format($svc->price) . ' TZS' : 'Free') }}</td>
                                <td class="px-6 py-4"><span class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ $svc->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">{{ $svc->is_active ? 'Active' : 'Inactive' }}</span></td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('admin.services.edit', $svc) }}" class="text-sm text-amber-600 hover:text-amber-700 mr-3">Edit</a>
                                    <a href="{{ route('admin.services.show', $svc) }}" class="text-sm text-slate-500 hover:text-slate-700">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-6 py-8 text-center text-slate-500">No services found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
@endsection
BLADE);

// ============================================================
// 13. ADMIN SUBMISSIONS INDEX
// ============================================================
writeFile("$viewsDir/admin/submissions/index.blade.php", <<<'BLADE'
@extends('layouts.app')
@section('title', 'Submissions — Admin')
@section('content')
<section class="bg-slate-900 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-bold text-white">Submissions</h1>
        <p class="text-slate-400 text-sm mt-1">All service requests</p>
    </div>
</section>
<section class="py-8 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50 text-xs uppercase text-slate-500"><tr><th class="px-6 py-3">Reference</th><th class="px-6 py-3">Service</th><th class="px-6 py-3">Customer</th><th class="px-6 py-3">Status</th><th class="px-6 py-3">Assigned To</th><th class="px-6 py-3 text-right">Actions</th></tr></thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($submissions ?? [] as $sub)
                            <tr class="hover:bg-slate-50">
                                <td class="px-6 py-4 font-mono text-slate-900">{{ $sub->reference_number }}</td>
                                <td class="px-6 py-4 text-slate-600">{{ $sub->service->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-slate-600">{{ $sub->customer_name }}</td>
                                <td class="px-6 py-4"><span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $sub->status_color ?? 'slate' }}-100 text-{{ $sub->status_color ?? 'slate' }}-700">{{ $sub->status_label ?? $sub->status }}</span></td>
                                <td class="px-6 py-4 text-slate-600">{{ $sub->processor->name ?? 'Unassigned' }}</td>
                                <td class="px-6 py-4 text-right"><a href="{{ route('admin.submissions.show', $sub) }}" class="text-sm text-amber-600 hover:text-amber-700">View</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-6 py-8 text-center text-slate-500">No submissions found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if(method_exists($submissions ?? [], 'links'))<div class="mt-6">{{ $submissions->links() }}</div>@endif
    </div>
</section>
@endsection
BLADE);

// ============================================================
// 14. ADMIN USERS INDEX
// ============================================================
writeFile("$viewsDir/admin/users/index.blade.php", <<<'BLADE'
@extends('layouts.app')
@section('title', 'Users — Admin')
@section('content')
<section class="bg-slate-900 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between">
        <div><h1 class="text-2xl font-bold text-white">Users</h1><p class="text-slate-400 text-sm mt-1">Manage staff and administrators</p></div>
        <a href="{{ route('admin.users.create') }}" class="px-4 py-2 rounded-xl text-sm font-semibold text-white bg-amber-500 hover:bg-amber-400 transition-colors">Add user</a>
    </div>
</section>
<section class="py-8 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50 text-xs uppercase text-slate-500"><tr><th class="px-6 py-3">Name</th><th class="px-6 py-3">Email</th><th class="px-6 py-3">Role</th><th class="px-6 py-3">Status</th><th class="px-6 py-3 text-right">Actions</th></tr></thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($users ?? [] as $user)
                            <tr class="hover:bg-slate-50">
                                <td class="px-6 py-4 font-medium text-slate-900">{{ $user->name }}</td>
                                <td class="px-6 py-4 text-slate-600">{{ $user->email }}</td>
                                <td class="px-6 py-4"><span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-700">{{ $user->role_label ?? $user->role }}</span></td>
                                <td class="px-6 py-4"><span class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ $user->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">{{ $user->is_active ? 'Active' : 'Inactive' }}</span></td>
                                <td class="px-6 py-4 text-right"><a href="{{ route('admin.users.edit', $user) }}" class="text-sm text-amber-600 hover:text-amber-700">Edit</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-6 py-8 text-center text-slate-500">No users found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
@endsection
BLADE);

// ============================================================
// 15. STAFF SUBMISSIONS INDEX
// ============================================================
writeFile("$viewsDir/staff/submissions/index.blade.php", <<<'BLADE'
@extends('layouts.app')
@section('title', 'My Submissions — Staff')
@section('content')
<section class="bg-slate-900 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-bold text-white">My Submissions</h1>
        <p class="text-slate-400 text-sm mt-1">Submissions assigned to you</p>
    </div>
</section>
<section class="py-8 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50 text-xs uppercase text-slate-500"><tr><th class="px-6 py-3">Reference</th><th class="px-6 py-3">Service</th><th class="px-6 py-3">Customer</th><th class="px-6 py-3">Status</th><th class="px-6 py-3 text-right">Actions</th></tr></thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($submissions ?? [] as $sub)
                            <tr class="hover:bg-slate-50">
                                <td class="px-6 py-4 font-mono text-slate-900">{{ $sub->reference_number }}</td>
                                <td class="px-6 py-4 text-slate-600">{{ $sub->service->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-slate-600">{{ $sub->customer_name }}</td>
                                <td class="px-6 py-4"><span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $sub->status_color ?? 'slate' }}-100 text-{{ $sub->status_color ?? 'slate' }}-700">{{ $sub->status_label ?? $sub->status }}</span></td>
                                <td class="px-6 py-4 text-right"><a href="{{ route('staff.submissions.show', $sub) }}" class="text-sm text-amber-600 hover:text-amber-700">View</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-6 py-8 text-center text-slate-500">No submissions assigned to you.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
@endsection
BLADE);

PHPEOF

echo "✅ Part 6 complete (admin + staff pages)"

# Finalize the bash script
cat >> /mnt/agents/output/rebuild-views.sh << 'BASHEOF'

# Run the PHP generator
echo ""
echo "🚀 Generating all views..."
php /tmp/generate-views.php

echo ""
echo "============================================================"
echo "🎉 View system rebuilt!"
echo "============================================================"
echo ""
echo "Files created:"
echo "  ✅ layouts/app.blade.php"
echo "  ✅ partials/nav.blade.php"
echo "  ✅ partials/footer.blade.php"
echo "  ✅ partials/alerts.blade.php"
echo "  ✅ home.blade.php"
echo "  ✅ services/index.blade.php"
echo "  ✅ services/show.blade.php"
echo "  ✅ services/_card.blade.php"
echo "  ✅ auth/login.blade.php"
echo "  ✅ submissions/track.blade.php"
echo "  ✅ admin/dashboard.blade.php"
echo "  ✅ admin/services/index.blade.php"
echo "  ✅ admin/submissions/index.blade.php"
echo "  ✅ admin/users/index.blade.php"
echo "  ✅ staff/submissions/index.blade.php"
echo ""
echo "Next steps:"
echo "  1. npm run build"
echo "  2. php artisan test --compact"
echo "  3. git add -A"
echo "  4. git commit -m 'design: complete view system rebuild'"
echo "  5. git push origin digital-star-consultants"
echo ""
BASHEOF

echo "Script ready!"
