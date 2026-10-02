@extends('layouts.digitalstar')

@section('title', 'Application status | Digital Star Consultants')

@php
    $isWaitingOnCustomer = $submission?->status === \App\Models\Submission::STATUS_AWAITING_CUSTOMER;
    $isCompleted = $submission?->status === \App\Models\Submission::STATUS_COMPLETED;
    $isClosed = in_array($submission?->status, [\App\Models\Submission::STATUS_REJECTED, \App\Models\Submission::STATUS_CANCELLED], true);

    // Next-step panel content by status
    $nextStep = match (true) {
        $isWaitingOnCustomer => ['icon' => 'circle-alert', 'title' => 'We need something from you.', 'copy' => 'Please contact our team so we can tell you what information or document is required.', 'label' => 'Contact support', 'url' => route('public.contact.show')],
        $isCompleted => ['icon' => 'party-popper', 'title' => 'Your request is complete.', 'copy' => 'Thank you for using Digital Star Consultants. Keep this reference for your records.', 'label' => 'Explore services', 'url' => route('public.services.index')],
        $isClosed => ['icon' => 'circle-slash', 'title' => 'This request is no longer active.', 'copy' => 'Our team can help explain the outcome and advise on the next available option.', 'label' => 'Contact us', 'url' => route('public.contact.show')],
        default => ['icon' => 'hourglass', 'title' => 'Nothing needed from you right now.', 'copy' => 'Our team is handling the next stage. Keep your reference number for future updates.', 'label' => 'Browse services', 'url' => route('public.services.index')],
    };
@endphp

@section('hero-crumbs')
    <a href="{{ route('public.track.form') }}"><i data-lucide="arrow-left"></i> Track another application</a>
@endsection

@if ($submission)
    @section('hero-actions')
        <span class="ds-status">{{ $submission->status_label }}</span>
    @endsection

    @section('hero-aside')
        <div class="ds-hero-card">
            <div class="ds-hero-card-chip" aria-hidden="true"></div>
            <span class="ds-hero-card-label">Your service</span>
            <h3>{{ $submission->service?->name ?? 'Digital Star service' }}</h3>
            <small>Submitted {{ $submission->created_at?->format('d M Y, H:i') }}</small>
            <dl class="ds-meta">
                <div><dt>Service</dt><dd>{{ $submission->service?->name ?? '—' }}</dd></div>
                <div><dt>Submitted</dt><dd>{{ $submission->created_at?->format('d M Y') ?? '—' }}</dd></div>
                @if ($submission->completed_at)
                    <div><dt>Completed</dt><dd>{{ $submission->completed_at->format('d M Y') }}</dd></div>
                @endif
            </dl>
        </div>
    @endsection
@endif

@section('content')
    @include('partials.ds-hero', [
        'kicker' => 'Application status',
        'title' => e($submission?->reference_number ?? $reference),
        'lead' => $submission ? 'Here is the latest progress for your Digital Star request.' : 'We could not find an application with this reference.',
    ])

    <section class="ds-section">
        <div class="ds-container">
            @if ($submission)
                <div class="ds-two">
                    <div class="ds-panel ds-reveal">
                        <span class="ds-kicker">Progress</span>
                        <h3>Your application journey</h3>
                        <div class="ds-timeline">
                            @foreach ($timeline as $item)
                                @php
                                    $state = $item['state'] === 'current danger' ? 'danger' : $item['state'];
                                    $dotIcon = match ($state) { 'done' => 'check', 'current' => 'loader', 'danger' => 'x', default => 'circle' };
                                @endphp
                                <div class="ds-tl-item {{ $state }}">
                                    <span class="ds-tl-dot"><i data-lucide="{{ $dotIcon }}"></i></span>
                                    <div>
                                        <strong>{{ $item['label'] }}</strong>
                                        <p>{{ $item['description'] }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="ds-panel ds-panel-muted ds-reveal" style="--d:.1s;align-self:start">
                        <span class="ds-icon-box"><i data-lucide="{{ $nextStep['icon'] }}"></i></span>
                        <span class="ds-kicker" style="display:block;padding-top:16px">Next step</span>
                        <h3>{{ $nextStep['title'] }}</h3>
                        <p>{{ $nextStep['copy'] }}</p>
                        <div class="ds-actions">
                            <a class="ds-btn ds-btn-primary" href="{{ $nextStep['url'] }}">{{ $nextStep['label'] }} <i data-lucide="arrow-right"></i></a>
                        </div>
                        @if ($submission->preferred_date)
                            <dl class="ds-meta" style="margin-top:20px">
                                <div><dt>Preferred date</dt><dd>{{ $submission->preferred_date->format('d M Y') }}</dd></div>
                            </dl>
                        @endif
                    </div>
                </div>
            @else
                <div class="ds-empty ds-reveal">
                    <span class="ds-icon-box"><i data-lucide="search-x"></i></span>
                    <span class="ds-kicker" style="display:block;padding-top:16px">Reference not found</span>
                    <h3>We couldn't find that application.</h3>
                    <p>Check the reference number and try again. It should look like <strong>DSC-20260901-ABC123</strong>.</p>
                    <div class="ds-actions">
                        <a class="ds-btn ds-btn-primary" href="{{ route('public.track.form') }}">Try again <i data-lucide="arrow-right"></i></a>
                        <a class="ds-btn ds-btn-soft" href="{{ route('public.contact.show') }}">Contact support</a>
                    </div>
                </div>
            @endif
        </div>
    </section>
@endsection
