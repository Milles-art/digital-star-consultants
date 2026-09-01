<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Digital Star · Admin' }}</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
    @stack('styles')
</head>
<body class="admin-body">
@php
    $adminUnreadCount = \Illuminate\Support\Facades\Schema::hasTable('notifications')
        ? auth()->user()->unreadNotifications()->count()
        : 0;
@endphp
<button class="admin-mobile-toggle" type="button" aria-label="Open navigation" aria-expanded="false" data-admin-menu-toggle><span></span><span></span><span></span></button>
<div class="admin-shell-overlay" data-admin-overlay></div>
<aside class="admin-sidebar" data-admin-sidebar>
    <a class="brand admin-brand" href="{{ route('admin.dashboard') }}" aria-label="Digital Star Consultants administration">
        <span class="brand-mark"><img src="{{ asset('images/digital-star-mark.svg') }}" alt="Digital Star Consultants star mark"></span><span><b>DIGITAL STAR</b><small>OPERATIONS</small></span>
    </a>
    <div class="admin-workspace-label"><span class="admin-avatar">{{ strtoupper(substr(auth()->user()->name,0,1)) }}</span><div><strong>{{ auth()->user()->name }}</strong><small>{{ auth()->user()->role_label }}</small></div></div>
    <nav aria-label="Admin navigation">
        <span class="nav-label">OVERVIEW</span>
        <a class="{{ request()->routeIs('admin.dashboard')?'active':'' }}" href="{{ route('admin.dashboard') }}"><span class="nav-icon">⌂</span>Dashboard</a>
        <a class="{{ request()->routeIs('admin.notifications.*')?'active':'' }}" href="{{ route('admin.notifications.index') }}"><span class="nav-icon">◔</span>Notifications @if($adminUnreadCount)<span class="admin-nav-badge">{{ $adminUnreadCount }}</span>@endif</a>
        <span class="nav-label">SERVICE OPERATIONS</span>
        <a class="{{ request()->routeIs('admin.submissions.*')?'active':'' }}" href="{{ route('admin.submissions.index') }}"><span class="nav-icon">◫</span>Requests</a>
        <a class="{{ request()->routeIs('admin.categories.*')?'active':'' }}" href="{{ route('admin.categories.index') }}"><span class="nav-icon">▦</span>Categories</a>
        <a class="{{ request()->routeIs('admin.services.*','admin.fields.*')?'active':'' }}" href="{{ route('admin.services.index') }}"><span class="nav-icon">✦</span>Services</a>
        <span class="nav-label">INSIGHTS</span>
        <a class="{{ request()->routeIs('admin.reports.*')?'active':'' }}" href="{{ route('admin.reports.index') }}"><span class="nav-icon">◌</span>Reports</a>
        <a class="{{ request()->routeIs('admin.finance.*')?'active':'' }}" href="{{ route('admin.finance.index') }}"><span class="nav-icon">◈</span>Finance</a>
        <a class="{{ request()->routeIs('admin.contact-messages.*')?'active':'' }}" href="{{ route('admin.contact-messages.index') }}"><span class="nav-icon">✉</span>Messages</a>
        <span class="nav-label">TEAM</span>
        <a class="{{ request()->routeIs('admin.users.*')?'active':'' }}" href="{{ route('admin.users.index') }}"><span class="nav-icon">◎</span>Users</a>
        <a class="{{ request()->routeIs('admin.settings.*')?'active':'' }}" href="{{ route('admin.settings.index') }}"><span class="nav-icon">⚙</span>Settings</a>
    </nav>
    <div class="admin-sidebar-footer"><a class="admin-site-link" href="{{ route('home') }}"><span>↗</span>View website</a><form method="POST" action="{{ route('admin.logout') }}">@csrf<button type="submit" class="admin-logout-link">Sign out</button></form></div>
</aside>
<main class="admin-main">
<header class="admin-top">
    <div class="admin-title-wrap"><div class="admin-breadcrumb"><span>Digital Star</span><b>/</b><span>{{ ucfirst(str_replace('-',' ',request()->segment(2) ?: 'dashboard')) }}</span></div><h1>@yield('page_title','Dashboard')</h1></div>
    <div class="admin-top-actions"><a class="admin-notification-bell" href="{{ route('admin.notifications.index') }}" aria-label="Notifications">◔ @if($adminUnreadCount)<span>{{ $adminUnreadCount }}</span>@endif</a><a class="admin-view-site" href="{{ route('home') }}" target="_blank" rel="noopener">Open website <span>↗</span></a><div class="admin-user-menu"><span class="admin-avatar admin-avatar-small">{{ strtoupper(substr(auth()->user()->name,0,1)) }}</span><div><strong>{{ auth()->user()->name }}</strong><small>{{ auth()->user()->role_label }}</small></div></div></div>
</header>
@if(session('success'))<div class="flash success" role="status">{{ session('success') }}</div>@endif
@if(session('error'))<div class="flash error" role="alert">{{ session('error') }}</div>@endif
@if($errors->any())<div class="flash error" role="alert">{{ $errors->first() }}</div>@endif
@yield('content')
</main>
@stack('scripts')
<script>
(()=>{const s=document.querySelector('[data-admin-sidebar]'),t=document.querySelector('[data-admin-menu-toggle]'),o=document.querySelector('[data-admin-overlay]');if(!s||!t||!o)return;const setOpen=v=>{s.classList.toggle('is-open',v);o.classList.toggle('is-visible',v);t.setAttribute('aria-expanded',v?'true':'false')};t.addEventListener('click',()=>setOpen(!s.classList.contains('is-open')));o.addEventListener('click',()=>setOpen(false));s.querySelectorAll('a').forEach(a=>a.addEventListener('click',()=>setOpen(false)));})();
</script>
</body></html>
