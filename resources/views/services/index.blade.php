@extends('layouts.digitalstar')

@section('title', ($selectedCategory?->name ? $selectedCategory->name . ' | ' : 'Services | ') . 'Digital Star Consultants')

@php
    $totalServices = $categories->sum(fn ($c) => $c->children->sum('active_services_count') + $c->active_services_count);
    $pad = fn ($n) => str_pad($n, 2, '0', STR_PAD_LEFT);

    if ($selectedCategory) {
        $heroTitle = e($selectedCategory->name);
        $heroLead = e($selectedCategory->description ?: 'Choose the service that matches what you need.');
    } elseif ($search) {
        $heroTitle = 'Search <span class="ds-accent">results.</span>';
        $heroLead = 'Showing services that match <strong style="color:#fff">&ldquo;' . e($search) . '&rdquo;</strong>.';
    } else {
        $heroTitle = 'Everything you need. <span class="ds-accent">In one place.</span>';
        $heroLead = 'Explore our complete service catalogue. Start with a service area, open a group, then choose the exact service you need.';
    }
@endphp

@if ($selectedCategory)
    @section('hero-crumbs')
        <a href="{{ route('public.services.index') }}">Services</a>
        <i data-lucide="chevron-right"></i>
        @if ($selectedCategory->parent)
            <a href="{{ route('public.services.index', ['category' => $selectedCategory->parent->slug]) }}">{{ $selectedCategory->parent->name }}</a>
            <i data-lucide="chevron-right"></i>
        @endif
        <strong>{{ $selectedCategory->name }}</strong>
    @endsection
@endif

@section('hero-actions')
    <a href="#catalogue" class="ds-btn ds-btn-mint">Browse services <i data-lucide="arrow-down"></i></a>
    <a href="{{ route('public.track.form') }}" class="ds-btn ds-btn-ghost-light">Track an application</a>
@endsection

@section('hero-aside')
    <div class="ds-hero-card">
        <div class="ds-hero-card-chip" aria-hidden="true"></div>
        <span class="ds-hero-card-label">{{ $search ? 'Matching services' : 'Services available' }}</span>
        <strong class="ds-hero-number">{{ $search ? $services->count() : $totalServices }}</strong>
        <small>{{ $search ? 'matching your search' : 'across all service areas' }}</small>
        <p>Choose the exact service and we'll guide you through the application.</p>
    </div>
@endsection

@section('content')
    @include('partials.ds-hero', [
        'kicker' => 'Digital Star services',
        'title' => $heroTitle,
        'lead' => $heroLead,
        'points' => ['Guided applications', 'Document support', 'Status tracking'],
    ])

    {{-- Search + service area tabs --}}
    <section class="ds-stats-wrap" aria-label="Catalogue search">
        <div class="ds-toolbar ds-reveal">
            <form class="ds-search" method="GET" action="{{ route('public.services.index') }}" role="search">
                <label class="ds-search-input">
                    <i data-lucide="search"></i>
                    <input name="search" value="{{ $search }}" aria-label="Search services"
                           placeholder="Search services: passport, TIN, business registration, website...">
                </label>
                <button class="ds-btn ds-btn-primary" type="submit">Search</button>
            </form>
            <div class="ds-tabs" aria-label="Service areas">
                <a class="ds-tab {{ !$selectedCategory && !$search ? 'is-active' : '' }}" href="{{ route('public.services.index') }}">All services</a>
                @foreach ($categories as $category)
                    <a class="ds-tab {{ $selectedCategory?->slug === $category->slug || $selectedCategory?->parent?->slug === $category->slug ? 'is-active' : '' }}"
                       href="{{ route('public.services.index', ['category' => $category->slug]) }}">{{ $category->name }}</a>
                @endforeach
            </div>
        </div>
    </section>

    <section class="ds-section" id="catalogue" style="scroll-margin-top:80px">
        <div class="ds-container">

            {{-- 1. Search results, grouped --}}
            @if ($search)
                @forelse ($serviceGroups as $group)
                    <div class="ds-group">
                        @include('partials.ds-heading', [
                            'kicker' => $pad($loop->iteration) . ' · Service group',
                            'title' => $group['name'],
                            'copy' => $group['description'],
                            'action' => '<span class="ds-count">' . $group['services']->count() . ' services</span>',
                        ])
                        <div class="ds-grid-4">
                            @foreach ($group['services'] as $service)
                                @include('services.partials.card', ['service' => $service, 'delay' => $loop->index * 0.05])
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="ds-empty">
                        <span class="ds-icon-box"><i data-lucide="search-x"></i></span>
                        <h3>No service found</h3>
                        <p>Try another search term or browse a service area.</p>
                        <div class="ds-actions">
                            <a class="ds-btn ds-btn-primary" href="{{ route('public.services.index') }}">Browse all services</a>
                            <a class="ds-btn ds-btn-soft" href="{{ route('public.contact.show') }}">Ask our team</a>
                        </div>
                    </div>
                @endforelse

            {{-- 2. Full catalogue: every pillar --}}
            @elseif (!$selectedCategory)
                @include('partials.ds-heading', [
                    'kicker' => 'Complete catalogue',
                    'title' => 'All service areas.',
                    'copy' => 'We have grouped every service by purpose, so you can see the full range without a long flat list.',
                ])
                <div class="ds-stack-lg">
                    @foreach ($categories as $category)
                        @php
                            $pillarServices = $category->children->sum(fn ($child) => $child->active_services_count) + $category->active_services_count;
                            $activeServices = $category->services->where('is_active', true);
                        @endphp
                        <div class="ds-pillar ds-reveal">
                            @include('partials.ds-heading', [
                                'kicker' => $pad($loop->iteration) . ' · Service pillar',
                                'title' => $category->name,
                                'copy' => $category->description,
                                'action' => '<a class="ds-btn ds-btn-soft" href="' . route('public.services.index', ['category' => $category->slug]) . '">' . $pillarServices . ' services <i data-lucide="arrow-right"></i></a>',
                            ])
                            @if ($category->children->isNotEmpty())
                                <div class="ds-grid-4">
                                    @foreach ($category->children as $child)
                                        @include('services.partials.group-card', ['child' => $child, 'number' => $pad($loop->iteration)])
                                    @endforeach
                                </div>
                            @elseif ($activeServices->isNotEmpty())
                                <div class="ds-grid-4">
                                    @foreach ($activeServices->take(6) as $service)
                                        @include('services.partials.card', ['service' => $service])
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

            {{-- 3. A top-level area: its groups, plus any services directly inside it --}}
            @elseif ($selectedCategory->isTopLevel())
                @include('partials.ds-heading', [
                    'kicker' => $selectedCategory->name,
                    'title' => 'Choose a group.',
                    'copy' => 'Every group below opens into the specific services available there.',
                    'action' => '<span class="ds-count">' . $childCategories->count() . ' groups</span>',
                ])
                @if ($childCategories->isNotEmpty())
                    <div class="ds-grid-4">
                        @foreach ($childCategories as $child)
                            @include('services.partials.group-card', ['child' => $child, 'number' => $pad($loop->iteration)])
                        @endforeach
                    </div>
                @endif
                @if ($services->isNotEmpty())
                    <div class="ds-group">
                        @include('partials.ds-heading', [
                            'kicker' => 'Services',
                            'title' => 'Available directly here',
                            'action' => '<span class="ds-count">' . $services->count() . ' services</span>',
                        ])
                        <div class="ds-grid-4">
                            @foreach ($services as $service)
                                @include('services.partials.card', ['service' => $service, 'delay' => $loop->index * 0.05])
                            @endforeach
                        </div>
                    </div>
                @endif

            {{-- 4. A group: its services --}}
            @else
                @include('partials.ds-heading', [
                    'kicker' => $selectedCategory->parent?->name ?? 'Service group',
                    'title' => $selectedCategory->name,
                    'copy' => "Choose the exact service you need and we'll guide you through the application.",
                    'action' => '<span class="ds-count">' . $services->count() . ' services</span>',
                ])
                @if ($services->isNotEmpty())
                    <div class="ds-grid-4">
                        @foreach ($services as $service)
                            @include('services.partials.card', ['service' => $service, 'delay' => $loop->index * 0.05])
                        @endforeach
                    </div>
                @else
                    <div class="ds-empty">
                        <span class="ds-icon-box"><i data-lucide="inbox"></i></span>
                        <h3>No active services in this group yet.</h3>
                        <p>Check back soon, or ask our team about what you need.</p>
                        <div class="ds-actions">
                            <a class="ds-btn ds-btn-primary" href="{{ route('public.services.index') }}">Back to service areas</a>
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </section>

    @include('partials.ds-cta', [
        'kicker' => 'Already applied?',
        'title' => 'Track your application anytime.',
        'copy' => 'Every request is handled by our team and tracked with a unique reference number.',
        'buttonLabel' => 'Track an application',
        'url' => route('public.track.form'),
        'checklist' => ['Guided applications', 'Document support', 'Status tracking'],
    ])
@endsection
