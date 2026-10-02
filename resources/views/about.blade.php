@extends('layouts.digitalstar')

@section('title', 'About | Digital Star Consultants')

@php
    $ds = config('digitalstar');
    $values = [
        ['icon' => 'eye', 'title' => 'We simplify', 'copy' => 'We help you understand what the service requires before you start, so you avoid unnecessary back-and-forth.'],
        ['icon' => 'layers', 'title' => 'We organize', 'copy' => 'Your information and supporting documents are handled in a structured way that makes requests easier to process.'],
        ['icon' => 'message-circle', 'title' => 'We communicate', 'copy' => 'You can track your request and know when there is an update or when we need something from you.'],
        ['icon' => 'trending-up', 'title' => 'We keep improving', 'copy' => 'We combine service assistance with digital expertise to make the overall experience clearer and more useful.'],
    ];
@endphp

@section('hero-actions')
    <a href="{{ route('public.services.index') }}" class="ds-btn ds-btn-mint">Explore services <i data-lucide="arrow-right"></i></a>
    <a href="{{ route('public.contact.show') }}" class="ds-btn ds-btn-ghost-light">Talk to us</a>
@endsection

@section('hero-aside')
    <div class="ds-hero-card">
        <div class="ds-hero-card-chip" aria-hidden="true"></div>
        <span class="ds-hero-card-label">Our promise</span>
        <h3>Clear process. Helpful support.</h3>
        <small>From first request to final result</small>
        <p>From the first request to the final result, we keep the next step easy to understand.</p>
    </div>
@endsection

@section('content')
    @include('partials.ds-hero', [
        'kicker' => 'About Digital Star',
        'title' => 'We make digital services <span class="ds-accent">easier to access.</span>',
        'lead' => 'Digital Star Consultants helps people, businesses and organizations complete important digital tasks with clear guidance and a simpler process.',
        'image' => $ds['images']['team'],
        'points' => ['Clear process', 'Helpful support', 'Local expertise'],
    ])

    @include('partials.ds-stats')

    {{-- WHY DIGITAL STAR --}}
    <section class="ds-section">
        <div class="ds-container ds-split">
            <div class="ds-photo ds-reveal">
                <img src="{{ $ds['images']['collab'] }}" alt="Digital Star team collaborating">
                <div class="ds-photo-badge">
                    <strong>4+ years</strong>
                    <span>Serving clients across Tanzania</span>
                </div>
            </div>
            <div>
                @include('partials.ds-heading', [
                    'kicker' => 'Why Digital Star',
                    'title' => 'One place for the tasks that slow you down.',
                    'copy' => 'Portals, forms, requirements and business processes can be difficult to navigate alone. We turn those complicated tasks into a guided service experience.',
                ])
                <div class="ds-values">
                    @foreach ($values as $value)
                        <article class="ds-value ds-reveal" style="--d:{{ $loop->index * 0.08 }}s">
                            <div class="ds-value-head">
                                <span class="ds-icon-box"><i data-lucide="{{ $value['icon'] }}"></i></span>
                                <h3>{{ $value['title'] }}</h3>
                            </div>
                            <p>{{ $value['copy'] }}</p>
                        </article>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    @include('partials.ds-process', [
        'kicker' => 'How we work',
        'title' => 'Built around people, not paperwork.',
        'copy' => 'Our service experience is designed to give customers clarity at every stage.',
    ])

    @include('partials.ds-cta', [
        'kicker' => 'Ready to start?',
        'title' => "Let's get something moving.",
        'copy' => 'Find a service, start an application or speak with our team about what you need.',
        'buttonLabel' => 'Contact us',
        'url' => route('public.contact.show'),
    ])
@endsection
