@extends('layouts.digitalstar')

@section('title', 'Our Work | Digital Star Consultants')

@php
    $ds = config('digitalstar');
    // Use projects passed from the controller if available, otherwise the defaults in config/digitalstar.php
    $projects = $projects ?? $ds['projects'];
    $featured = $projects[0] ?? null;
    $browserProjects = array_map(fn ($p) => array_merge($p, ['image' => asset($p['image']), 'video' => isset($p['video']) ? asset($p['video']) : null]), array_values($projects));
    $countFilter = fn ($f) => count(array_filter($projects, fn ($p) => ($p['filter'] ?? '') === $f));
    $workStats = [
        ['value' => (string) count($projects), 'label' => 'Curated pieces', 'icon' => 'layout-grid'],
        ['value' => (string) $countFilter('logos'), 'label' => 'Logo designs', 'icon' => 'pen-tool'],
        ['value' => (string) ($countFilter('print') + $countFilter('merchandise')), 'label' => 'Print & branded items', 'icon' => 'printer'],
        ['value' => (string) $countFilter('video'), 'label' => 'Project videos', 'icon' => 'play'],
    ];
@endphp

@section('hero-actions')
    <a href="#projects" class="ds-btn ds-btn-mint">View all projects <i data-lucide="arrow-right"></i></a>
    <a href="{{ route('public.contact.show') }}" class="ds-btn ds-btn-ghost-light">Start a project</a>
@endsection

@section('hero-aside')
    @if ($featured)
        <div class="ds-hero-card">
            <div class="ds-hero-card-chip" aria-hidden="true"></div>
            <span class="ds-hero-card-label">Featured</span>
            <h3>{{ $featured['title'] }}</h3>
            <small>{{ $featured['category'] }}</small>
            <p>{{ \Illuminate\Support\Str::limit($featured['description'], 140) }}</p>
            <button type="button" class="ds-btn ds-btn-white" data-project-id="0">View project <i data-lucide="arrow-right"></i></button>
        </div>
    @endif
@endsection

@section('content')
    @include('partials.ds-hero', [
        'kicker' => 'Our work',
        'title' => 'Designs made <span class="ds-accent">for everyday business.</span>',
        'lead' => 'A closer look at our logo designs, shop signs, printed artwork and branded items. Browse the images or watch the short project videos.',
        'image' => $ds['images']['hero'],
        'points' => ['Logo design', 'Print & signage', 'Branded items'],
    ])

    @include('partials.ds-stats', ['stats' => $workStats])

    <section class="ds-section" id="projects" style="scroll-margin-top:80px">
        <div class="ds-container">
            @include('partials.ds-heading', [
                'kicker' => 'Our portfolio',
                'title' => 'Real work for real businesses.',
                'copy' => 'Logos, banners, packaging and signage — every piece below was designed, printed or produced for a paying client. Filter by craft, open any piece, watch the workshop videos.',
            ])

            <div class="ds-tabs" role="group" aria-label="Project categories">
                @foreach ($ds['filters'] as $key => $label)
                    <button type="button" class="ds-tab {{ $loop->first ? 'is-active' : '' }}"
                            aria-pressed="{{ $loop->first ? 'true' : 'false' }}" data-work-filter="{{ $key }}">{{ $label }}</button>
                @endforeach
            </div>

            <div class="ds-grid-3 ds-grid-3-sm2">
                @foreach ($projects as $project)
                    @include('partials.ds-project-card', ['project' => $project, 'index' => $loop->index])
                @endforeach
            </div>
        </div>
    </section>

    {{-- Case study dialog (filled in by public/js/digitalstar.js) --}}
    <dialog class="ds-modal" data-work-modal aria-labelledby="ds-modal-title">
        <div style="position:relative">
            <img data-modal-image alt="" hidden>
            <video data-modal-video controls playsinline preload="none" hidden></video>
            <button type="button" class="ds-modal-close" data-modal-close aria-label="Close project"><i data-lucide="x"></i></button>
        </div>
        <div class="ds-modal-body">
            <span class="ds-kicker" data-modal-category></span>
            <h2 id="ds-modal-title" data-modal-title></h2>
            <p data-modal-description></p>
            <div class="ds-badges" data-modal-meta></div>
            <div>
                <a href="{{ route('public.contact.show') }}" class="ds-btn ds-btn-primary">Discuss a similar project <i data-lucide="arrow-right"></i></a>
            </div>
        </div>
    </dialog>

    @include('partials.ds-cta', [
        'kicker' => 'Have a project in mind?',
        'title' => 'Your logo could be next.',
        'copy' => 'Send us your business name and what you need — a logo, a banner, packaging — and we will come back with ideas and a clear price.',
        'buttonLabel' => 'Start your project',
        'url' => route('public.contact.show'),
        'checklist' => ['Logo & brand identity', 'Banners & signage', 'Branded merchandise', 'Fast, honest pricing'],
    ])
@endsection

@push('scripts')
    <script>window.digitalStarProjects = @json($browserProjects);</script>
@endpush

@push('head')
<link rel="stylesheet" href="{{ asset('css/work-media.css') }}?v={{ filemtime(public_path('css/work-media.css')) }}">
@endpush
