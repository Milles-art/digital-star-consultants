@extends('layouts.digitalstar')

@php
    $h = __('site.home');
    $ds = config('digitalstar');
    $floatCards = [
        ['icon' => 'laptop', 'title' => $h['float1_t'], 'sub' => $h['float1_s']],
        ['icon' => 'megaphone', 'title' => $h['float2_t'], 'sub' => $h['float2_s']],
        ['icon' => 'server', 'title' => $h['float3_t'], 'sub' => $h['float3_s']],
    ];
    // Prefer live catalogue pillars from the database; fall back to the static config.
    $dsIconFor = function ($slug) {
        $slug = strtolower($slug ?? '');
        return match (true) {
            str_contains($slug, 'print') || str_contains($slug, 'graphic') || str_contains($slug, 'brand') => 'palette',
            str_contains($slug, 'business') || str_contains($slug, 'brela') => 'briefcase',
            str_contains($slug, 'it') || str_contains($slug, 'tech') || str_contains($slug, 'consult') => 'server',
            str_contains($slug, 'government') || str_contains($slug, 'online') => 'laptop',
            default => 'code-xml',
        };
    };
    $homeServices = (isset($categories) && $categories->isNotEmpty())
        ? $categories->take(4)->map(fn ($c) => [
            'title' => $c->name,
            'copy' => \Illuminate\Support\Str::limit($c->description ?: $h['cat_fallback'], 110),
            'icon' => $dsIconFor($c->slug),
            'points' => $c->children->pluck('name')->merge($c->services->pluck('name'))->take(3)->all() ?: $h['points_fallback'],
            'url' => route('public.services.index', ['category' => $c->slug]),
        ])
        : collect($ds['services'])->map(fn ($s) => $s + ['url' => route('public.services.index')]);
@endphp

@section('description', $h['meta_desc'])

{{-- Hero slots (must be defined before the content section) --}}
@section('hero-actions')
    <a href="{{ route('work') }}" class="ds-btn ds-btn-mint">{{ $h['cta_work'] }} <i data-lucide="arrow-right"></i></a>
    <a href="{{ route('public.contact.show') }}" class="ds-btn ds-btn-ghost-light">{{ $h['cta_quote'] }}</a>
@endsection

@section('hero-aside')
    <div class="ds-visual" aria-hidden="true">
        <div class="ds-visual-frame"></div>
        <div class="ds-visual-core"><i data-lucide="code-xml"></i></div>
        @foreach ($floatCards as $card)
            <div class="ds-float ds-float-{{ $loop->iteration }}">
                <i class="ds-float-icon"><i data-lucide="{{ $card['icon'] }}"></i></i>
                <div><strong>{{ $card['title'] }}</strong><small>{{ $card['sub'] }}</small></div>
            </div>
        @endforeach
    </div>
@endsection

@section('content')
    @include('partials.ds-hero', [
        'kicker' => $h['hero_kicker'],
        'title' => $h['hero_title'],
        'lead' => $h['hero_lead'],
        'image' => $ds['images']['hero'],
        'points' => $h['hero_points'],
    ])

    @include('partials.ds-stats')

    {{-- SERVICES --}}
    <section class="ds-section">
        <div class="ds-container">
            @include('partials.ds-heading', [
                'kicker' => $h['svc_kicker'],
                'title' => $h['svc_title'],
                'copy' => $h['svc_copy'],
                'action' => '<a href="' . route('public.services.index') . '" class="ds-btn ds-btn-soft">' . $h['svc_view_all'] . ' <i data-lucide="arrow-right"></i></a>',
            ])
            <div class="ds-grid-4">
                @foreach ($homeServices as $service)
                    <a href="{{ $service['url'] }}" class="ds-service ds-reveal" style="--d:{{ $loop->index * 0.08 }}s">
                        <span class="ds-icon-box"><i data-lucide="{{ $service['icon'] }}"></i></span>
                        <h3>{{ $service['title'] }}</h3>
                        <p>{{ $service['copy'] }}</p>
                        <ul class="ds-tags">
                            @foreach ($service['points'] as $point)
                                <li>{{ $point }}</li>
                            @endforeach
                        </ul>
                        <span class="ds-card-link">{{ __('site.common.learn_more') }} <i data-lucide="arrow-up-right"></i></span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    @include('partials.ds-process', [
        'kicker' => $h['how_kicker'],
        'title' => $h['how_title'],
        'copy' => $h['how_copy'],
    ])

    {{-- RECENT WORK --}}
    <section class="ds-section">
        <div class="ds-container">
            @include('partials.ds-heading', [
                'kicker' => $h['work_kicker'],
                'title' => $h['work_title'],
                'copy' => $h['work_copy'],
                'action' => '<a href="' . route('work') . '" class="ds-btn ds-btn-soft">' . $h['work_view_all'] . ' <i data-lucide="arrow-right"></i></a>',
            ])
            <div class="ds-grid-3">
                @foreach (array_slice($ds['projects'], 0, 3) as $project)
                    <div class="ds-reveal" style="--d:{{ $loop->index * 0.1 }}s">
                        @include('partials.ds-project-card', ['project' => $project, 'href' => route('work')])
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    @include('partials.ds-cta', [
        'kicker' => $h['final_kicker'],
        'title' => $h['final_title'],
        'copy' => $h['final_copy'],
        'buttonLabel' => $h['final_btn'],
        'url' => route('public.contact.show'),
    ])
@endsection
