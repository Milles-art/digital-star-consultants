@extends('layouts.app')

@section('content')
@php
    $categoryIcon = function ($category) {
        $slug = strtolower($category->slug ?? '');
        return match (true) {
            str_contains($slug, 'government') => 'government',
            str_contains($slug, 'passport') || str_contains($slug, 'immigration') => 'passport',
            str_contains($slug, 'jobs') => 'jobs',
            str_contains($slug, 'education') => 'education',
            str_contains($slug, 'tra') || str_contains($slug, 'tax') => 'tax',
            str_contains($slug, 'travel') => 'travel',
            str_contains($slug, 'printing') => 'printing',
            str_contains($slug, 'stationery') => 'stationery',
            str_contains($slug, 'business') || str_contains($slug, 'brela') => 'business',
            str_contains($slug, 'it') || str_contains($slug, 'tech') => 'it',
            default => 'default',
        };
    };
@endphp
<section class="catalog-hero">
    <div class="catalog-hero-inner">
        <div>
            <div class="eyebrow">DIGITAL STAR SERVICES</div>
            @if($selectedCategory)
                <div class="catalog-breadcrumb">
                    <a href="{{ route('public.services.index') }}">Services</a><span>›</span>
                    @if($selectedCategory->parent)
                        <a href="{{ route('public.services.index', ['category' => $selectedCategory->parent->slug]) }}">{{ $selectedCategory->parent->name }}</a><span>›</span>
                    @endif
                    <strong>{{ $selectedCategory->name }}</strong>
                </div>
                <h1>{{ $selectedCategory->name }}</h1>
                <p>{{ $selectedCategory->description ?: 'Choose the service that matches what you need.' }}</p>
            @elseif($search)
                <h1>Search results</h1>
                <p>Showing services that match <strong>“{{ $search }}”</strong>.</p>
            @else
                <h1>Everything you need.<br><em>In one place.</em></h1>
                <p>Explore our complete service catalogue. Start with a service area, open a group, then choose the exact service you need.</p>
            @endif
        </div>
        <div class="catalog-stat">
            @if(!$selectedCategory && !$search)
                <span>{{ $categories->sum(fn($c) => $c->children->sum('active_services_count') + $c->active_services_count) }}</span>
                <small>services available</small>
            @else
                <span>{{ $search ? $services->count() : $services->count() }}</span>
                <small>{{ $search ? 'matching services' : 'services' }}</small>
            @endif
        </div>
    </div>
</section>

<section class="catalog-shell">
    <form class="catalog-search" method="GET" action="{{ route('public.services.index') }}">
        <span class="search-icon" aria-hidden="true">⌕</span>
        <input name="search" value="{{ $search }}" placeholder="Search services — passport, TIN, business registration, website…" aria-label="Search services">
        <button class="button button-yellow" type="submit">Search</button>
    </form>

    <div class="catalog-tabs" aria-label="Service areas">
        <a class="catalog-tab {{ !$selectedCategory && !$search ? 'active' : '' }}" href="{{ route('public.services.index') }}">All services</a>
        @foreach($categories as $category)
            <a class="catalog-tab {{ $selectedCategory?->slug === $category->slug ? 'active' : '' }}" href="{{ route('public.services.index', ['category' => $category->slug]) }}">
                <span>@include('partials.icon', ['iconKey' => $categoryIcon($category)])</span>{{ $category->name }}
            </a>
        @endforeach
    </div>

    @if($search)
        @forelse($serviceGroups as $group)
            <div class="catalog-section">
                <div class="catalog-section-head"><div><span class="section-index">{{ str_pad($loop->iteration,2,'0',STR_PAD_LEFT) }}</span><h2>{{ $group['name'] }}</h2><p>{{ $group['description'] }}</p></div><span class="section-count">{{ $group['services']->count() }} services</span></div>
                <div class="service-grid service-grid-premium">
                    @foreach($group['services'] as $service)
                        @include('services.partials.card', ['service' => $service])
                    @endforeach
                </div>
            </div>
        @empty
            <div class="catalog-empty"><div class="empty-icon">⌕</div><h3>No service found</h3><p>Try another search term or browse a service area.</p><a class="button button-outline" href="{{ route('public.services.index') }}">Browse all services</a></div>
        @endforelse

    @elseif(!$selectedCategory)
        <div class="catalog-intro-row">
            <div><span class="eyebrow">COMPLETE CATALOGUE</span><h2>All service areas.</h2></div>
            <p>We have grouped every service by purpose, so you can see the full range without a long flat list.</p>
        </div>

        <div class="catalog-all-pillars">
            @foreach($categories as $category)
                @php
                    $pillarServices = $category->children->sum(fn($child) => $child->active_services_count) + $category->active_services_count;
                @endphp
                <section class="catalog-pillar-section">
                    <div class="catalog-pillar-heading">
                        <div class="catalog-pillar-heading-icon">@include('partials.icon', ['iconKey' => $categoryIcon($category)])</div>
                        <div class="catalog-pillar-heading-copy">
                            <span class="eyebrow">{{ str_pad($loop->iteration,2,'0',STR_PAD_LEFT) }} · SERVICE PILLAR</span>
                            <h2>{{ $category->name }}</h2>
                            <p>{{ $category->description }}</p>
                            <span class="catalog-pillar-count">{{ $pillarServices }} services in this pillar</span>
                        </div>
                        <a class="button button-outline" href="{{ route('public.services.index',['category'=>$category->slug]) }}">Explore pillar →</a>
                    </div>

                    @if($category->children->isNotEmpty())
                        <div class="catalog-group-grid">
                            @foreach($category->children as $child)
                                <a class="catalog-group-card" href="{{ route('public.services.index',['category'=>$child->slug]) }}">
                                    <div class="catalog-group-icon">@include('partials.icon', ['iconKey' => $categoryIcon($child)])</div>
                                    <div class="catalog-group-copy"><span class="catalog-group-kicker">{{ str_pad($loop->iteration,2,'0',STR_PAD_LEFT) }}</span><h3>{{ $child->name }}</h3><p>{{ $child->description ?: 'Explore services in this group.' }}</p></div>
                                    <div class="catalog-group-footer"><span>{{ $child->active_services_count }} {{ $child->active_services_count === 1 ? 'service' : 'services' }}</span><strong>Open →</strong></div>
                                </a>
                            @endforeach
                        </div>

                        <div class="catalog-featured-services">
                            <div class="catalog-subhead"><div><span class="eyebrow">POPULAR IN {{ strtoupper($category->name) }}</span><h3>Start with a service</h3></div><a class="catalog-view-all" href="{{ route('public.services.index',['category'=>$category->slug]) }}">View all {{ $pillarServices }} services →</a></div>
                            <div class="service-grid service-grid-premium">
                                @foreach($category->children->flatMap(fn($child) => $child->services)->take(4) as $service)
                                    @include('services.partials.card', ['service' => $service])
                                @endforeach
                            </div>
                        </div>
                    @elseif($category->services->where('is_active',true)->isNotEmpty())
                        <div class="catalog-featured-services">
                            <div class="catalog-subhead"><div><span class="eyebrow">SERVICES</span><h3>Available services</h3></div><a class="catalog-view-all" href="{{ route('public.services.index',['category'=>$category->slug]) }}">View all {{ $pillarServices }} services →</a></div>
                            <div class="service-grid service-grid-premium">
                                @foreach($category->services->where('is_active',true)->take(6) as $service)
                                    @include('services.partials.card', ['service' => $service])
                                @endforeach
                            </div>
                        </div>
                    @endif
                </section>
            @endforeach
        </div>

    @elseif($selectedCategory->isTopLevel())
        @if($childCategories->isNotEmpty())
            <div class="catalog-intro-row"><div><span class="eyebrow">{{ $selectedCategory->name }}</span><h2>Choose a group.</h2></div><p>Every group below opens into the specific services available there.</p></div>
            <div class="catalog-group-grid">
                @foreach($childCategories as $child)
                    <a class="catalog-group-card" href="{{ route('public.services.index', ['category' => $child->slug]) }}"><div class="catalog-group-icon">@include('partials.icon',['iconKey'=>$categoryIcon($child)])</div><div class="catalog-group-copy"><span class="catalog-group-kicker">{{ str_pad($loop->iteration,2,'0',STR_PAD_LEFT) }}</span><h3>{{ $child->name }}</h3><p>{{ $child->description ?: 'Explore services in this group.' }}</p></div><div class="catalog-group-footer"><span>{{ $child->active_services_count }} services</span><strong>Open →</strong></div></a>
                @endforeach
            </div>
        @endif
        @if($services->isNotEmpty())
            <div class="catalog-section direct-services"><div class="catalog-section-head"><div><span class="eyebrow">SERVICES</span><h2>Available directly here</h2></div><span class="section-count">{{ $services->count() }} services</span></div><div class="service-grid service-grid-premium">@foreach($services as $service) @include('services.partials.card',['service'=>$service]) @endforeach</div></div>
        @endif

    @else
        <div class="catalog-section child-service-section"><div class="catalog-section-head"><div><span class="eyebrow">{{ $selectedCategory->parent?->name ?? 'SERVICE GROUP' }}</span><h2>{{ $selectedCategory->name }}</h2><p>Choose the exact service you need and we'll guide you through the application.</p></div><span class="section-count">{{ $services->count() }} services</span></div>@if($services->isNotEmpty())<div class="service-grid service-grid-premium">@foreach($services as $service) @include('services.partials.card',['service'=>$service]) @endforeach</div>@else<div class="catalog-empty compact"><h3>No active services in this group yet.</h3><a class="button button-outline" href="{{ route('public.services.index') }}">Back to service areas</a></div>@endif</div>
    @endif
</section>
@endsection
