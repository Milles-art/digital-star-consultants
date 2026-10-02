@extends('layouts.digitalstar')

@php
    $ds = config('digitalstar');
    $floatCards = [
        ['icon' => 'laptop', 'title' => 'Web Development', 'sub' => 'Modern & responsive'],
        ['icon' => 'megaphone', 'title' => 'Digital Marketing', 'sub' => 'Grow your brand'],
        ['icon' => 'server', 'title' => 'IT Consultancy', 'sub' => 'Reliable & secure'],
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
            'copy' => \Illuminate\Support\Str::limit($c->description ?: 'Professional digital services tailored to your needs.', 110),
            'icon' => $dsIconFor($c->slug),
            'points' => $c->children->pluck('name')->merge($c->services->pluck('name'))->take(3)->all() ?: ['Guided application', 'Document support', 'Status tracking'],
            'url' => route('public.services.index', ['category' => $c->slug]),
        ])
        : collect($ds['services'])->map(fn ($s) => $s + ['url' => route('public.services.index')]);
@endphp

{{-- Hero slots (must be defined before the content section) --}}
@section('hero-actions')
    <a href="{{ route('work') }}" class="ds-btn ds-btn-mint">Explore our work <i data-lucide="arrow-right"></i></a>
    <a href="{{ route('public.contact.show') }}" class="ds-btn ds-btn-ghost-light">Get a quote</a>
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
        'kicker' => 'Digital solutions for a brighter tomorrow',
        'title' => 'Your trusted digital partner <span class="ds-accent">in Tanzania</span>',
        'lead' => 'We help businesses and organizations grow through modern web, software, creative and IT solutions.',
        'image' => $ds['images']['hero'],
        'points' => ['Modern solutions', 'Reliable support', 'Local expertise'],
    ])

    @include('partials.ds-stats')

    {{-- SERVICES --}}
    <section class="ds-section">
        <div class="ds-container">
            @include('partials.ds-heading', [
                'kicker' => 'What we do',
                'title' => 'Our services',
                'copy' => 'From web development to digital consultation, we provide end-to-end solutions for your business.',
                'action' => '<a href="' . route('public.services.index') . '" class="ds-btn ds-btn-soft">View all services <i data-lucide="arrow-right"></i></a>',
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
                        <span class="ds-card-link">Learn more <i data-lucide="arrow-up-right"></i></span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    @include('partials.ds-process', [
        'kicker' => 'How it works',
        'title' => 'Our simple process',
        'copy' => 'We make it easy to bring your ideas to life with a clear and transparent process.',
    ])

    {{-- RECENT WORK --}}
    <section class="ds-section">
        <div class="ds-container">
            @include('partials.ds-heading', [
                'kicker' => 'Our portfolio',
                'title' => 'Recent work',
                'copy' => "Some of the projects we've delivered for our clients.",
                'action' => '<a href="' . route('work') . '" class="ds-btn ds-btn-soft">View all work <i data-lucide="arrow-right"></i></a>',
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
        'kicker' => 'Ready to get started?',
        'title' => 'Ready to transform your ideas into reality?',
        'copy' => "Let's discuss how we can help your business grow with the right digital solutions.",
        'buttonLabel' => 'Get a free quote',
        'url' => route('public.contact.show'),
    ])
@endsection
