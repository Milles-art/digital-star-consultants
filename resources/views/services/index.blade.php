@extends('layouts.digitalstar')

@php
    $cat = __('site.services.catalogue');
@endphp

@section('title', ($selectedCategory?->name ? $selectedCategory->name . ' | ' : $cat['meta_suffix'] . ' | ') . 'Digital Star Consultants')
@section('description', $selectedCategory
    ? __('site.services.catalogue.meta_desc_category', ['name' => $selectedCategory->name])
    : $cat['meta_desc_default'])

@php
    $totalServices = $categories->sum(fn ($c) => $c->children->sum('active_services_count') + $c->active_services_count);
    $pad = fn ($n) => str_pad($n, 2, '0', STR_PAD_LEFT);

    if ($selectedCategory) {
        $heroTitle = e($selectedCategory->name);
        $heroLead = e($selectedCategory->description ?: $cat['hero_fallback_lead']);
    } elseif ($search) {
        $heroTitle = $cat['search_title'];
        $heroLead = __('site.services.catalogue.search_lead', ['query' => $search]);
    } else {
        $heroTitle = $cat['hero_title'];
        $heroLead = $cat['hero_lead'];
    }
@endphp

@if ($selectedCategory)
    @section('hero-crumbs')
        <a href="{{ route('public.services.index') }}">{{ $cat['crumb'] }}</a>
        <i data-lucide="chevron-right"></i>
        @if ($selectedCategory->parent)
            <a href="{{ route('public.services.index', ['category' => $selectedCategory->parent->slug]) }}">{{ $selectedCategory->parent->name }}</a>
            <i data-lucide="chevron-right"></i>
        @endif
        <strong>{{ $selectedCategory->name }}</strong>
    @endsection
@endif

@section('hero-actions')
    <a href="#catalogue" class="ds-btn ds-btn-mint">{{ $cat['browse_btn'] }} <i data-lucide="arrow-down"></i></a>
    <a href="{{ route('public.track.form') }}" class="ds-btn ds-btn-ghost-light">{{ $cat['track_btn'] }}</a>
@endsection

@section('hero-aside')
    <div class="ds-hero-card">
        <div class="ds-hero-card-chip" aria-hidden="true"></div>
        <span class="ds-hero-card-label">{{ $search ? $cat['aside_matching'] : $cat['aside_available'] }}</span>
        <strong class="ds-hero-number">{{ $search ? $services->count() : $totalServices }}</strong>
        <small>{{ $search ? $cat['aside_matching_small'] : $cat['aside_available_small'] }}</small>
        <p>{{ $cat['aside_copy'] }}</p>
    </div>
@endsection

@section('content')
    @include('partials.ds-hero', [
        'kicker' => $cat['kicker'],
        'title' => $heroTitle,
        'lead' => $heroLead,
        'points' => $cat['points'],
    ])

    {{-- Search + service area tabs --}}
    <section class="ds-stats-wrap" aria-label="Catalogue search">
        <div class="ds-toolbar ds-reveal">
            <form class="ds-search" method="GET" action="{{ route('public.services.index') }}" role="search">
                <label class="ds-search-input">
                    <i data-lucide="search"></i>
                    <input name="search" value="{{ $search }}" aria-label="{{ $cat['search_aria'] }}"
                           placeholder="{{ $cat['search_ph'] }}">
                </label>
                <button class="ds-btn ds-btn-primary" type="submit">{{ $cat['search_btn'] }}</button>
            </form>
            <div class="ds-tabs" aria-label="{{ $cat['tabs_aria'] }}">
                <a class="ds-tab {{ !$selectedCategory && !$search ? 'is-active' : '' }}" href="{{ route('public.services.index') }}">{{ $cat['all_services'] }}</a>
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
                            'kicker' => $pad($loop->iteration) . ' · ' . $cat['group_kicker'],
                            'title' => $group['name'],
                            'copy' => $group['description'],
                            'action' => '<span class="ds-count">' . __('site.services.catalogue.services_count', ['count' => $group['services']->count()]) . '</span>',
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
                        <h3>{{ $cat['empty_title'] }}</h3>
                        <p>{{ $cat['empty_copy'] }}</p>
                        <div class="ds-actions">
                            <a class="ds-btn ds-btn-primary" href="{{ route('public.services.index') }}">{{ $cat['browse_all'] }}</a>
                            <a class="ds-btn ds-btn-soft" href="{{ route('public.contact.show') }}">{{ $cat['ask_team'] }}</a>
                        </div>
                    </div>
                @endforelse

            {{-- 2. Full catalogue: every pillar --}}
            @elseif (!$selectedCategory)
                @include('partials.ds-heading', [
                    'kicker' => $cat['catalogue_kicker'],
                    'title' => $cat['catalogue_title'],
                    'copy' => $cat['catalogue_copy'],
                ])
                <div class="ds-stack-lg">
                    @foreach ($categories as $category)
                        @php
                            $pillarServices = $category->children->sum(fn ($child) => $child->active_services_count) + $category->active_services_count;
                            $activeServices = $category->services->where('is_active', true);
                        @endphp
                        <div class="ds-pillar ds-reveal">
                            @include('partials.ds-heading', [
                                'kicker' => $pad($loop->iteration) . ' · ' . $cat['pillar_kicker'],
                                'title' => $category->name,
                                'copy' => $category->description,
                                'action' => '<a class="ds-btn ds-btn-soft" href="' . route('public.services.index', ['category' => $category->slug]) . '">' . __('site.services.catalogue.services_count', ['count' => $pillarServices]) . ' <i data-lucide="arrow-right"></i></a>',
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
                    'title' => $cat['choose_group'],
                    'copy' => $cat['choose_group_copy'],
                    'action' => '<span class="ds-count">' . __('site.services.catalogue.groups_count', ['count' => $childCategories->count()]) . '</span>',
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
                            'kicker' => $cat['crumb'],
                            'title' => $cat['direct'],
                            'action' => '<span class="ds-count">' . __('site.services.catalogue.services_count', ['count' => $services->count()]) . '</span>',
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
                    'kicker' => $selectedCategory->parent?->name ?? $cat['group_kicker'],
                    'title' => $selectedCategory->name,
                    'copy' => $cat['group_desc'],
                    'action' => '<span class="ds-count">' . __('site.services.catalogue.services_count', ['count' => $services->count()]) . '</span>',
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
                        <h3>{{ $cat['no_active_title'] }}</h3>
                        <p>{{ $cat['no_active_copy'] }}</p>
                        <div class="ds-actions">
                            <a class="ds-btn ds-btn-primary" href="{{ route('public.services.index') }}">{{ $cat['back_areas'] }}</a>
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </section>

    @include('partials.ds-process', [
        'kicker' => $cat['process_kicker'],
        'title' => $cat['process_title'],
        'copy' => $cat['process_copy'],
        'steps' => [
            ['title' => $cat['step1_title'], 'copy' => $cat['step1_copy'], 'icon' => 'search'],
            ['title' => $cat['step2_title'], 'copy' => $cat['step2_copy'], 'icon' => 'file-check'],
            ['title' => $cat['step3_title'], 'copy' => $cat['step3_copy'], 'icon' => 'radar'],
        ],
    ])

    @include('partials.ds-cta', [
        'kicker' => $cat['cta_kicker'],
        'title' => $cat['cta_title'],
        'copy' => $cat['cta_copy'],
        'buttonLabel' => $cat['cta_btn'],
        'url' => route('public.track.form'),
        'checklist' => $cat['points'],
    ])
@endsection
