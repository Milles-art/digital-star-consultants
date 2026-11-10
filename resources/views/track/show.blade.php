@extends('layouts.digitalstar')

@php
    $t = __('site.track');
    $isWaitingOnCustomer = $submission?->status === \App\Models\Submission::STATUS_AWAITING_CUSTOMER;
    $isCompleted = $submission?->status === \App\Models\Submission::STATUS_COMPLETED;
    $isClosed = in_array($submission?->status, [\App\Models\Submission::STATUS_REJECTED, \App\Models\Submission::STATUS_CANCELLED], true);

    // Next-step panel content by status
    $nextStep = match (true) {
        $isWaitingOnCustomer => ['icon' => 'circle-alert', 'title' => $t['need_title'], 'copy' => $t['need_copy'], 'label' => $t['need_label'], 'url' => route('public.contact.show')],
        $isCompleted => ['icon' => 'party-popper', 'title' => $t['done_title'], 'copy' => $t['done_copy'], 'label' => $t['done_label'], 'url' => route('public.services.index')],
        $isClosed => ['icon' => 'circle-slash', 'title' => $t['closed_title'], 'copy' => $t['closed_copy'], 'label' => $t['closed_label'], 'url' => route('public.contact.show')],
        default => ['icon' => 'hourglass', 'title' => $t['wait_title'], 'copy' => $t['wait_copy'], 'label' => $t['wait_label'], 'url' => route('public.services.index')],
    };
@endphp

@section('title', $t['result_title'] . ' | Digital Star Consultants')

@section('hero-crumbs')
    <a href="{{ route('public.track.form') }}"><i data-lucide="arrow-left"></i> {{ $t['crumb_back'] }}</a>
@endsection

@if ($submission)
    @section('hero-actions')
        <span class="ds-status">{{ $t['statuses'][$submission->status] ?? $submission->status_label }}</span>
    @endsection

    @section('hero-aside')
        <div class="ds-hero-card">
            <div class="ds-hero-card-chip" aria-hidden="true"></div>
            <span class="ds-hero-card-label">{{ $t['aside_service_label'] }}</span>
            <h3>{{ $submission->service?->name ?? $t['service_fallback'] }}</h3>
            <small>{{ $t['submitted_prefix'] }} {{ $submission->created_at?->format('d M Y, H:i') }}</small>
            <dl class="ds-meta">
                <div><dt>{{ $t['meta_service'] }}</dt><dd>{{ $submission->service?->name ?? '—' }}</dd></div>
                <div><dt>{{ $t['meta_submitted'] }}</dt><dd>{{ $submission->created_at?->format('d M Y') ?? '—' }}</dd></div>
                @if ($submission->completed_at)
                    <div><dt>{{ $t['meta_completed'] }}</dt><dd>{{ $submission->completed_at->format('d M Y') }}</dd></div>
                @endif
            </dl>
        </div>
    @endsection
@endif

@section('content')
    @include('partials.ds-hero', [
        'kicker' => $t['result_title'],
        'title' => e($submission?->reference_number ?? $reference),
        'lead' => $submission ? $t['lead_found'] : $t['lead_missing'],
    ])

    <section class="ds-section">
        <div class="ds-container">
            @if ($submission)
                <div class="ds-two">
                    <div class="ds-panel ds-reveal">
                        <span class="ds-kicker">{{ $t['progress_kicker'] }}</span>
                        <h3>{{ $t['progress_title'] }}</h3>
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
                        <span class="ds-kicker" style="display:block;padding-top:16px">{{ $t['next_kicker'] }}</span>
                        <h3>{{ $nextStep['title'] }}</h3>
                        <p>{{ $nextStep['copy'] }}</p>
                        <div class="ds-actions">
                            <a class="ds-btn ds-btn-primary" href="{{ $nextStep['url'] }}">{{ $nextStep['label'] }} <i data-lucide="arrow-right"></i></a>
                        </div>
                        @if ($submission->preferred_date)
                            <dl class="ds-meta" style="margin-top:20px">
                                <div><dt>{{ $t['meta_preferred'] }}</dt><dd>{{ $submission->preferred_date->format('d M Y') }}</dd></div>
                            </dl>
                        @endif
                    </div>
                </div>
            @else
                <div class="ds-empty ds-reveal">
                    <span class="ds-icon-box"><i data-lucide="search-x"></i></span>
                    <span class="ds-kicker" style="display:block;padding-top:16px">{{ $t['empty_kicker'] }}</span>
                    <h3>{{ $t['empty_title'] }}</h3>
                    <p>{{ $t['empty_copy'] }} <strong>{{ \App\Support\SubmissionReference::example() }}</strong>.</p>
                    <div class="ds-actions">
                        <a class="ds-btn ds-btn-primary" href="{{ route('public.track.form') }}">{{ $t['try_again'] }} <i data-lucide="arrow-right"></i></a>
                        <a class="ds-btn ds-btn-soft" href="{{ route('public.contact.show') }}">{{ $t['contact_support'] }}</a>
                    </div>
                </div>
            @endif
        </div>
    </section>
@endsection
