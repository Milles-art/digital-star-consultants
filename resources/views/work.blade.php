@extends('layouts.digitalstar')

@section('title', 'Our Work | Digital Star Consultants')

@php
    $ds = config('digitalstar');
    // Use projects passed from the controller if available, otherwise the defaults in config/digitalstar.php
    $projects = $projects ?? $ds['projects'];
    $featured = $projects[0] ?? null;
    $workStats = [
        ['value' => count($projects) . '+', 'label' => 'Featured projects', 'icon' => 'folder-kanban'],
        ['value' => '3', 'label' => 'Service areas', 'icon' => 'briefcase'],
        ['value' => 'IT', 'label' => '& infrastructure', 'icon' => 'server'],
        ['value' => 'Brand', 'label' => '& creative design', 'icon' => 'palette'],
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
            <button type="button" class="ds-btn ds-btn-white" data-project-id="0">View case study <i data-lucide="arrow-right"></i></button>
        </div>
    @endif
@endsection

@section('content')
    @include('partials.ds-hero', [
        'kicker' => 'Our work',
        'title' => 'We build solutions <span class="ds-accent">that drive success.</span>',
        'lead' => 'Explore a selection of our IT solutions, software projects and creative design work that help businesses grow and stand out.',
        'image' => $ds['images']['hero'],
        'points' => ['IT & Technology', 'Software & Web', 'Graphics & Branding'],
    ])

    @include('partials.ds-stats', ['stats' => $workStats])

    <section class="ds-section" id="projects" style="scroll-margin-top:80px">
        <div class="ds-container">
            @include('partials.ds-heading', [
                'kicker' => 'Our portfolio',
                'title' => 'Featured projects',
                'copy' => 'IT systems, software and creative work presented in one place.',
            ])

            <div class="ds-tabs" role="tablist" aria-label="Project categories">
                @foreach ($ds['filters'] as $key => $label)
                    <button type="button" role="tab" class="ds-tab {{ $loop->first ? 'is-active' : '' }}"
                            aria-selected="{{ $loop->first ? 'true' : 'false' }}" data-work-filter="{{ $key }}">{{ $label }}</button>
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
            <img data-modal-image src="" alt="">
            <button type="button" class="ds-modal-close" data-modal-close aria-label="Close case study"><i data-lucide="x"></i></button>
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
        'title' => "Let's build something amazing together.",
        'copy' => "Tell us about your goals and we'll propose the right solution.",
        'buttonLabel' => 'Start your project',
        'url' => route('public.contact.show'),
    ])
@endsection

@push('scripts')
    <script>window.digitalStarProjects = @json(array_values($projects));</script>
@endpush
