@extends('layouts.app')

@section('content')
<section class="ds-page-hero ds-about-hero">
    <div class="ds-container ds-page-hero-grid">
        <div>
            <div class="ds-eyebrow">ABOUT DIGITAL STAR</div>
            <h1>We make digital services <em>easier to access.</em></h1>
            <p>Digital Star Consultants helps people, businesses and organizations complete important digital tasks with clear guidance, practical support and a simpler process.</p>
            <div class="ds-page-actions">
                <a class="ds-button ds-button-gold" href="{{ route('public.services.index') }}">Explore services <span>→</span></a>
                <a class="ds-button ds-button-soft" href="{{ route('public.contact.show') }}">Talk to us</a>
            </div>
        </div>
        <div class="ds-about-snapshot" aria-label="Digital Star at a glance">
            <div class="ds-about-mark"><img src="{{ asset('images/digital-star-mark.svg') }}" alt="Digital Star"></div>
            <div class="ds-about-snapshot-copy">
                <span>OUR PROMISE</span>
                <strong>Clear process.<br>Helpful support.</strong>
                <p>From the first request to the final result, we keep the next step easy to understand.</p>
            </div>
        </div>
    </div>
</section>

<section class="ds-section ds-about-intro">
    <div class="ds-container ds-two-column-section">
        <div class="ds-section-kicker">WHY DIGITAL STAR</div>
        <div>
            <h2>One place for the tasks that <em>slow you down.</em></h2>
            <p class="ds-lead">Portals, forms, requirements and business processes can be difficult to navigate alone. We turn those complicated tasks into a guided service experience.</p>
        </div>
    </div>
</section>

<section class="ds-section ds-about-values">
    <div class="ds-container">
        <div class="ds-section-heading">
            <div>
                <div class="ds-eyebrow">HOW WE WORK</div>
                <h2>Built around <em>people, not paperwork.</em></h2>
            </div>
            <p>Our service experience is designed to give customers clarity at every stage.</p>
        </div>

        <div class="ds-value-grid">
            <article class="ds-value-card">
                <span class="ds-value-icon">01</span>
                <h3>We simplify</h3>
                <p>We help you understand what the service requires before you start, so you avoid unnecessary back-and-forth.</p>
            </article>
            <article class="ds-value-card">
                <span class="ds-value-icon">02</span>
                <h3>We organize</h3>
                <p>Your information and supporting documents are handled in a structured way that makes the request easier to process.</p>
            </article>
            <article class="ds-value-card">
                <span class="ds-value-icon">03</span>
                <h3>We communicate</h3>
                <p>You can track your request and know when there is an update or when we need something from you.</p>
            </article>
            <article class="ds-value-card">
                <span class="ds-value-icon">04</span>
                <h3>We keep improving</h3>
                <p>We combine service assistance with digital expertise to make the overall experience clearer and more useful.</p>
            </article>
        </div>
    </div>
</section>

<section class="ds-section ds-about-cta">
    <div class="ds-container">
        <div class="ds-cta-panel">
            <div>
                <div class="ds-eyebrow">READY TO START?</div>
                <h2>Let's get something <em>moving.</em></h2>
                <p>Find a service, start an application or speak with our team about what you need.</p>
            </div>
            <div class="ds-cta-actions">
                <a class="ds-button ds-button-gold" href="{{ route('public.services.index') }}">Find a service <span>→</span></a>
                <a class="ds-button ds-button-outline-light" href="{{ route('public.contact.show') }}">Contact Digital Star</a>
            </div>
        </div>
    </div>
</section>
@endsection
