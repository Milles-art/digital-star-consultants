@extends('layouts.app')

@section('content')
<section class="track-page track-result-page">
    <div class="track-result-shell">
        <div class="track-result-top">
            <a class="track-back" href="{{ route('public.track.form') }}">← Track another application</a>
            @if($submission)
                <span class="track-result-badge {{ $submission->status_color }}"><i></i>{{ $submission->status_label }}</span>
            @endif
        </div>

        @if($submission)
            <div class="track-result-header">
                <div>
                    <span class="track-eyebrow">APPLICATION STATUS</span>
                    <h1>{{ $submission->reference_number }}</h1>
                    <p>Here is the latest progress for your Digital Star request.</p>
                </div>
                <div class="track-service-card">
                    <span>YOUR SERVICE</span>
                    <strong>{{ $submission->service?->name ?? 'Digital Star service' }}</strong>
                    <small>Submitted {{ $submission->created_at?->format('d M Y, H:i') }}</small>
                </div>
            </div>

            <div class="track-result-grid">
                <section class="track-progress-card">
                    <div class="track-card-heading"><div><span>PROGRESS</span><h2>Your application journey</h2></div><strong>{{ $submission->status_label }}</strong></div>
                    <div class="track-timeline">
                        @foreach($timeline as $item)
                            <article class="timeline-item {{ $item['state'] }}">
                                <div class="timeline-marker">{{ $item['state'] === 'done' ? '✓' : ($item['state'] === 'current' ? '•' : ($item['state'] === 'current danger' ? '!' : '')) }}</div>
                                <div class="timeline-copy"><strong>{{ $item['label'] }}</strong><p>{{ $item['description'] }}</p></div>
                            </article>
                        @endforeach
                    </div>
                </section>

                <aside class="track-side-column">
                    <section class="track-detail-card">
                        <span>REQUEST DETAILS</span>
                        <dl>
                            <div><dt>Service</dt><dd>{{ $submission->service?->name ?? '—' }}</dd></div>
                            <div><dt>Submitted</dt><dd>{{ $submission->created_at?->format('d M Y') ?? '—' }}</dd></div>
                            @if($submission->preferred_date)<div><dt>Preferred date</dt><dd>{{ $submission->preferred_date->format('d M Y') }}</dd></div>@endif
                            @if($submission->completed_at)<div><dt>Completed</dt><dd>{{ $submission->completed_at->format('d M Y') }}</dd></div>@endif
                        </dl>
                    </section>
                    <section class="track-next-card">
                        <span>NEXT STEP</span>
                        @if($submission->status === \App\Models\Submission::STATUS_AWAITING_CUSTOMER)
                            <strong>We need something from you.</strong><p>Please contact our team so we can tell you what information or document is required.</p>
                            <a class="ds-button ds-button-gold" href="{{ route('public.contact.show') }}">Contact support <span>→</span></a>
                        @elseif($submission->status === \App\Models\Submission::STATUS_COMPLETED)
                            <strong>Your request is complete.</strong><p>Thank you for using Digital Star Consultants. Keep this reference for your records.</p>
                            <a class="ds-button ds-button-outline" href="{{ route('public.services.index') }}">Explore services <span>→</span></a>
                        @elseif(in_array($submission->status, [\App\Models\Submission::STATUS_REJECTED, \App\Models\Submission::STATUS_CANCELLED], true))
                            <strong>This request is no longer active.</strong><p>Our team can help explain the outcome and advise on the next available option.</p>
                            <a class="ds-button ds-button-outline" href="{{ route('public.contact.show') }}">Contact us <span>→</span></a>
                        @else
                            <strong>Nothing needed from you right now.</strong><p>Our team is handling the next stage. Keep your reference number for future updates.</p>
                            <a class="ds-button ds-button-outline" href="{{ route('public.services.index') }}">Browse services <span>→</span></a>
                        @endif
                    </section>
                </aside>
            </div>
        @else
            <div class="track-not-found">
                <div class="track-not-found-icon">?</div>
                <span class="track-eyebrow">REFERENCE NOT FOUND</span>
                <h1>We couldn't find that application.</h1>
                <p>Check the reference number and try again. Your reference should look like <strong>DSC-20260901-ABC123</strong>.</p>
                <div class="track-not-found-actions"><a class="ds-button ds-button-gold" href="{{ route('public.track.form') }}">Try again <span>→</span></a><a class="ds-button ds-button-outline" href="{{ route('public.contact.show') }}">Contact support</a></div>
            </div>
        @endif
    </div>
</section>
@endsection
