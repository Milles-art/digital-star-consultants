@extends('layouts.digitalstar')

@section('title', 'Track an application | Digital Star Consultants')

@php
    $features = [
        ['icon' => 'activity', 'title' => 'Live status', 'copy' => 'See the latest stage of your application at any time.'],
        ['icon' => 'signpost', 'title' => 'Clear next step', 'copy' => 'Know whether you need to do anything or just wait.'],
        ['icon' => 'hash', 'title' => 'One reference', 'copy' => 'Use the same code from submission to completion.'],
    ];
@endphp

@section('hero-aside')
    <div class="ds-hero-card">
        <div class="ds-hero-card-chip" aria-hidden="true"></div>
        <span class="ds-hero-card-label">Your reference number</span>
        <h3>Track an application</h3>
        <small>Example: DSC-20260901-ABC123</small>
        <form class="ds-form" id="track-form" style="margin-top:20px;gap:14px">
            <label class="ds-field">Reference number
                <input id="tracking-reference" name="reference" required autocomplete="off" placeholder="20260901-ABC123">
            </label>
            <button class="ds-btn ds-btn-mint ds-btn-block" type="submit">Check status <i data-lucide="arrow-right"></i></button>
        </form>
        <p class="ds-hero-note"><i data-lucide="shield-check"></i> Private and secure. Your reference is only used to find your status.</p>
    </div>
@endsection

@section('content')
    @include('partials.ds-hero', [
        'kicker' => 'Application tracking',
        'title' => 'Know where your request <span class="ds-accent">stands.</span>',
        'lead' => 'Enter your Digital Star reference number to see the latest status, service details and the next step.',
        'points' => ['Live status', 'Clear next step', 'One reference'],
    ])

    <section class="ds-section">
        <div class="ds-container">
            @include('partials.ds-heading', [
                'kicker' => 'How tracking works',
                'title' => 'Three steps, one reference.',
                'copy' => 'Use the same code from submission to completion.',
            ])
            <div class="ds-grid-3-even">
                @foreach ($features as $item)
                    <article class="ds-service ds-reveal" style="--d:{{ $loop->index * 0.08 }}s">
                        <span class="ds-icon-box"><i data-lucide="{{ $item['icon'] }}"></i></span>
                        <h3>{{ $item['title'] }}</h3>
                        <p>{{ $item['copy'] }}</p>
                    </article>
                @endforeach
            </div>

            <div class="ds-panel ds-banner ds-reveal">
                <div>
                    <span class="ds-kicker">Can't find your reference?</span>
                    <h3>Check your confirmation message or contact our team.</h3>
                </div>
                <a class="ds-btn ds-btn-primary" href="{{ route('public.contact.show') }}">Contact support <i data-lucide="arrow-right"></i></a>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('track-form')?.addEventListener('submit', (event) => {
        event.preventDefault();
        const value = new FormData(event.currentTarget).get('reference')?.toString().trim().toUpperCase();
        if (!value) return;
        const normalized = value.startsWith('DSC-') ? value : `DSC-${value}`;
        window.location.href = `/track/status/${encodeURIComponent(normalized)}`;
    });
});
</script>
@endpush
