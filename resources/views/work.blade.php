@extends('layouts.digitalstar')

@php
    $w = __('site.work');
    $ds = config('digitalstar');
    // Use projects passed from the controller if available, otherwise the defaults in config/digitalstar.php
    $projects = $projects ?? $ds['projects'];
    $featured = $projects[0] ?? null;
    $browserProjects = array_map(fn ($p) => array_merge($p, ['image' => asset($p['image']), 'video' => isset($p['video']) ? asset($p['video']) : null]), array_values($projects));
    $countFilter = fn ($f) => count(array_filter($projects, fn ($p) => ($p['filter'] ?? '') === $f));
    $workStats = [
        ['value' => (string) count($projects), 'label' => $w['stat_pieces'], 'icon' => 'layout-grid'],
        ['value' => (string) $countFilter('logos'), 'label' => $w['stat_logos'], 'icon' => 'pen-tool'],
        ['value' => (string) ($countFilter('print') + $countFilter('merchandise')), 'label' => $w['stat_print'], 'icon' => 'printer'],
        ['value' => (string) $countFilter('video'), 'label' => $w['stat_videos'], 'icon' => 'play'],
    ];
@endphp

@section('title', $w['title'] . ' | Digital Star Consultants')
@section('description', $w['meta_desc'])

@section('hero-actions')
    <a href="#projects" class="ds-btn ds-btn-mint">{{ $w['btn_view_all'] }} <i data-lucide="arrow-right"></i></a>
    <a href="{{ route('public.contact.show') }}" class="ds-btn ds-btn-ghost-light">{{ $w['btn_start'] }}</a>
@endsection

@section('hero-aside')
    @if ($featured)
        <div class="ds-hero-card">
            <div class="ds-hero-card-chip" aria-hidden="true"></div>
            <span class="ds-hero-card-label">{{ $w['featured_label'] }}</span>
            <h3>{{ $featured['title'] }}</h3>
            <small>{{ $featured['category'] }}</small>
            <p>{{ \Illuminate\Support\Str::limit($featured['description'], 140) }}</p>
            <button type="button" class="ds-btn ds-btn-white" data-project-id="0">{{ $w['btn_view_project'] }} <i data-lucide="arrow-right"></i></button>
        </div>
    @endif
@endsection

@section('content')
    @include('partials.ds-hero', [
        'kicker' => $w['hero_kicker'],
        'title' => $w['hero_title'],
        'lead' => $w['hero_lead'],
        'image' => $ds['images']['hero'],
        'points' => $w['hero_points'],
    ])

    @include('partials.ds-stats', ['stats' => $workStats])

    <section class="ds-section" id="projects" style="scroll-margin-top:80px">
        <div class="ds-container">
            @include('partials.ds-heading', [
                'kicker' => $w['kicker_portfolio'],
                'title' => $w['title_portfolio'],
                'copy' => $w['copy_portfolio'],
            ])

            <div class="ds-tabs" role="group" aria-label="{{ $w['tabs_aria'] }}">
                @foreach ($w['filters'] as $key => $label)
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
            <button type="button" class="ds-modal-close" data-modal-close aria-label="{{ $w['modal_close'] }}"><i data-lucide="x"></i></button>
        </div>
        <div class="ds-modal-body">
            <span class="ds-kicker" data-modal-category></span>
            <h2 id="ds-modal-title" data-modal-title></h2>
            <p data-modal-description></p>
            <div class="ds-badges" data-modal-meta></div>
            <div>
                <a href="{{ route('public.contact.show') }}" class="ds-btn ds-btn-primary">{{ $w['modal_cta'] }} <i data-lucide="arrow-right"></i></a>
            </div>
        </div>
    </dialog>

    @include('partials.ds-cta', [
        'kicker' => $w['cta_kicker'],
        'title' => $w['cta_title'],
        'copy' => $w['cta_copy'],
        'buttonLabel' => $w['cta_btn'],
        'url' => route('public.contact.show'),
        'checklist' => $w['cta_checklist'],
    ])
@endsection

@push('scripts')
    <script>window.digitalStarProjects = @json($browserProjects);</script>
@endpush

@push('head')
<link rel="stylesheet" href="{{ asset('css/work-media.css') }}?v={{ filemtime(public_path('css/work-media.css')) }}">
@endpush
