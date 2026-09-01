@extends('layouts.app')
@section('content')
<section class="ds-ref-hero">
    <div class="ds-ref-hero-inner">
        <div class="ds-ref-hero-copy">
            <span class="ds-ref-eyebrow">DIGITAL SERVICES PARTNER · TANZANIA</span>
            <h1>Digital services.<br><span>Simplified.</span></h1>
            <p>Government services, business solutions,<br class="desktop-only"> technology and creative services —<br class="desktop-only"> all in one place.</p>
            <div class="ds-ref-actions">
                <a class="ds-ref-btn dark" href="{{ route('public.services.index') }}">Explore Services <b>→</b></a>
                <a class="ds-ref-btn light" href="{{ route('public.track.form') }}">Track Application <b>▣</b></a>
            </div>
        </div>
        <div class="ds-ref-hero-visual" aria-hidden="true">
            <div class="ds-ref-skyline"></div>
            <div class="ds-ref-haze"></div>
            <img src="{{ asset('images/digital-star-mark.svg') }}" alt="">
            <div class="ds-ref-star-shadow"></div>
        </div>
    </div>
</section>

<section class="ds-ref-trust">
    <div class="ds-ref-trust-inner">
        <article><span class="trust-icon shield">@include('partials.icon',['iconKey'=>'support'])</span><div><strong>Professional Assistance</strong><small>We handle the process for you.</small></div></article>
        <article><span class="trust-icon clock">@include('partials.icon',['iconKey'=>'forms'])</span><div><strong>Fast &amp; Reliable</strong><small>Quick turnaround with clear communication.</small></div></article>
        <article><span class="trust-icon shield">@include('partials.icon',['iconKey'=>'support'])</span><div><strong>Secure &amp; Private</strong><small>Your information is always protected.</small></div></article>
        <article><span class="trust-icon headset">@include('partials.icon',['iconKey'=>'support'])</span><div><strong>Dedicated Support</strong><small>We’re here to help every step of the way.</small></div></article>
    </div>
</section>

<section class="ds-ref-categories">
    <div class="ds-ref-container">
        <div class="ds-ref-section-heading centered">
            <h2>What can we help you with?</h2>
        </div>
        <div class="ds-ref-category-grid">
            @foreach($categories->take(4) as $category)
                @php
                    $categoryClasses = ['blue','green','purple','gold'];
                    $class = $categoryClasses[$loop->index] ?? 'blue';
                    $iconKeys = ['government','business','printing','it'];
                    $iconKey = $iconKeys[$loop->index] ?? 'default';
                @endphp
                <a class="ds-ref-category-card" href="{{ route('public.services.index',['category'=>$category->slug]) }}">
                    <div class="ds-ref-category-icon {{ $class }}">@include('partials.icon',['iconKey'=>$iconKey])</div>
                    <h3>{{ $category->name }}</h3>
                    <p>{{ $category->description ?: 'Professional services and guided assistance.' }}</p>
                    <strong>Explore Services <span>→</span></strong>
                </a>
            @endforeach
        </div>
    </div>
</section>

<section class="ds-ref-popular">
    <div class="ds-ref-container">
        <div class="ds-ref-section-heading row-heading">
            <h2>Popular Services</h2>
            <a href="{{ route('public.services.index') }}">View All Services →</a>
        </div>
        <div class="ds-ref-popular-grid">
            @forelse($popularServices->take(6) as $service)
                @include('services.partials.card', ['service' => $service])
            @empty
                <div class="ds-ref-empty">Popular services will appear here as the catalogue grows.</div>
            @endforelse
        </div>
    </div>
</section>

<section class="ds-ref-how">
    <div class="ds-ref-container">
        <div class="ds-ref-section-heading centered light-heading"><h2>How It Works</h2></div>
        <div class="ds-ref-steps">
            @foreach($steps as $step)
                <article>
                    <div class="ds-ref-step-icon">{{ $loop->iteration === 1 ? '▣' : ($loop->iteration === 2 ? '▤' : ($loop->iteration === 3 ? '◎' : '✓')) }}</div>
                    <h3>{{ $step['n'] }}. {{ $step['title'] }}</h3>
                    <p>{{ $step['desc'] }}</p>
                </article>
            @endforeach
        </div>
    </div>
</section>

<section class="ds-ref-track">
    <div class="ds-ref-track-inner">
        <div class="ds-ref-track-copy">
            <h2>Track Your<br>Application</h2>
            <p>Enter your reference number to track your application status.</p>
            <form action="{{ route('public.track.form') }}" method="GET" class="ds-ref-track-form">
                <input name="reference" placeholder="Enter your reference number" aria-label="Reference number">
                <button type="submit">Track Application <span>→</span></button>
            </form>
        </div>
        <div class="ds-ref-track-art" aria-hidden="true">
            <div class="ds-ref-laptop">
                <div class="ds-ref-screen">
                    <div class="screen-top"><strong>Application Status</strong><span>In Progress</span></div>
                    <b>DSC-2026-04125</b>
                    <div class="screen-line"><i class="active"></i><i class="active"></i><i></i></div>
                    <div class="screen-labels"><span>Received</span><span>In Progress</span><span>Completed</span></div>
                </div>
                <div class="ds-ref-base"></div>
            </div>
            <div class="ds-ref-phone"><div></div><small>Reference No.</small><strong>DSC-04125</strong><span>In Progress</span></div>
        </div>
    </div>
</section>

<section class="ds-ref-cta">
    <div class="ds-ref-cta-inner">
        <img src="{{ asset('images/digital-star-mark.svg') }}" alt="">
        <div><h2>Need help with a digital service?</h2><p>Our team is ready to assist you.</p></div>
        <a class="ds-ref-btn gold" href="{{ route('public.contact.show') }}">Contact Us Now <b>→</b></a>
        <div class="cta-star" aria-hidden="true">★</div>
    </div>
</section>
@endsection
