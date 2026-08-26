<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ trim(($title ?? 'Admin').' | '.config('app.name', 'Digital Star Consultants')) }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        {!! file_exists(resource_path('css/admin-glass.css')) ? file_get_contents(resource_path('css/admin-glass.css')) : '' !!}
    </style>
</head>
<body class="admin-body min-h-screen">
@php
    $user = auth()->user();
    $managementNav = [
        ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'icon' => '◈'],
        ['label' => 'Submissions', 'route' => 'admin.submissions.index', 'icon' => '▣'],
        ['label' => 'Categories', 'route' => 'admin.categories.index', 'icon' => '▤'],
        ['label' => 'Services', 'route' => 'admin.services.index', 'icon' => '✦'],
        ['label' => 'Users', 'route' => 'admin.users.index', 'icon' => '◎'],
        ['label' => 'Reports', 'route' => 'admin.reports.index', 'icon' => '◉'],
        ['label' => 'Messages', 'route' => 'admin.contact-messages.index', 'icon' => '✉'],
    ];
    $staffNav = [
        ['label' => 'My Work', 'route' => 'staff.submissions', 'icon' => '▣'],
    ];
    $navItems = $user?->isManagement() ? $managementNav : $staffNav;
@endphp

<div class="admin-aurora" aria-hidden="true"></div>
<div class="admin-mobile-scrim" data-sidebar-close></div>

<aside class="admin-sidebar" id="admin-sidebar">
    <a href="{{ $user?->isManagement() ? route('admin.dashboard') : route('staff.submissions') }}" class="admin-brand">
        <span class="admin-brand-mark">DS</span>
        <span>
            <span class="block text-sm font-black text-white">Digital Star</span>
            <span class="block text-[11px] text-white/50">Operations</span>
        </span>
    </a>

    <nav class="mt-8 space-y-1.5" aria-label="Admin navigation">
        @foreach ($navItems as $item)
            <a href="{{ route($item['route']) }}"
               class="admin-nav-link {{ request()->routeIs($item['route']) || request()->routeIs(str_replace('.index', '.*', $item['route'])) ? 'is-active' : '' }}">
                <span class="admin-nav-icon">{{ $item['icon'] }}</span>
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>

    <div class="mt-auto admin-sidebar-card" style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);border-radius:16px;padding:14px;">
        <p class="text-[11px] uppercase tracking-[0.16em] text-white/40">Signed in</p>
        <p class="mt-2 text-sm font-bold text-white">{{ $user?->name }}</p>
        <p class="text-xs text-white/50">{{ $user?->role_label }}</p>
    </div>
</aside>

<div class="admin-main">
    <header class="admin-topbar">
        <button type="button" class="admin-icon-button lg:hidden" data-sidebar-toggle aria-controls="admin-sidebar" aria-expanded="false">
            <span class="sr-only">Open navigation</span>
            <span class="block h-0.5 w-5 bg-current"></span>
            <span class="block h-0.5 w-5 bg-current"></span>
            <span class="block h-0.5 w-5 bg-current"></span>
        </button>

        <div>
            <p class="admin-kicker">{{ $eyebrow ?? 'Admin' }}</p>
            <h1 class="text-xl font-black text-white sm:text-2xl">{{ $title ?? 'Dashboard' }}</h1>
        </div>

        <div class="ml-auto flex items-center gap-3">
            <a href="{{ route('home') }}" class="admin-button admin-button-muted">Public site</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="admin-button admin-button-dark">Logout</button>
            </form>
        </div>
    </header>

    <main class="admin-content">
        @if (session('status') || session('success'))
            <div class="admin-alert">{{ session('status') ?? session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="admin-alert admin-alert-danger">{{ $errors->first() }}</div>
        @endif
        @yield('content')
    </main>
</div>

<div class="admin-toast-stack" data-toast-stack aria-live="polite" aria-atomic="true"></div>
</body>
</html>
