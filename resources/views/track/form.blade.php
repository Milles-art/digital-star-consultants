@extends('layouts.app')

@section('content')
<section class="track-page track-entry-page">
    <div class="track-entry-shell">
        <div class="track-entry-copy">
            <span class="track-eyebrow">APPLICATION TRACKING</span>
            <h1>Know where your request <em>stands.</em></h1>
            <p>Enter your Digital Star reference number to see the latest status, service details and the next step for your request.</p>
            <div class="track-benefits">
                <div><span>01</span><strong>Live status</strong><small>See the latest stage of your application.</small></div>
                <div><span>02</span><strong>Clear next step</strong><small>Know whether you need to do anything.</small></div>
                <div><span>03</span><strong>One reference</strong><small>Use the same code from submission to completion.</small></div>
            </div>
        </div>

        <div class="track-entry-panel">
            <div class="track-panel-top">
                <div class="track-icon-wrap">@include('partials.icon', ['iconKey' => 'forms'])</div>
                <div><span>YOUR REFERENCE NUMBER</span><strong>Track an application</strong></div>
            </div>
            <form class="track-entry-form" id="track-form">
                <label for="tracking-reference">Reference number</label>
                <div class="track-input-wrap"><span>DSC</span><input id="tracking-reference" name="reference" required autocomplete="off" placeholder="20260901-ABC123" aria-describedby="tracking-hint"></div>
                <p id="tracking-hint">Example: <b>DSC-20260901-ABC123</b></p>
                <button class="ds-button ds-button-gold" type="submit">Check application status <span>→</span></button>
            </form>
            <div class="track-security"><span>✓</span><div><strong>Private & secure</strong><small>Your reference is used only to retrieve the status of your request.</small></div></div>
        </div>
    </div>
</section>

<section class="track-help-strip">
    <div><span>CAN'T FIND YOUR REFERENCE?</span><strong>Check the confirmation message or contact our team.</strong></div>
    <a class="ds-button ds-button-outline" href="{{ route('public.contact.show') }}">Contact support <span>→</span></a>
</section>

<script>
document.getElementById('track-form')?.addEventListener('submit', (event) => {
    event.preventDefault();
    const value = new FormData(event.currentTarget).get('reference')?.toString().trim();
    if (!value) return;
    const normalized = value.toUpperCase().startsWith('DSC-') ? value.toUpperCase() : `DSC-${value.toUpperCase()}`;
    window.location.href = `/track/status/${encodeURIComponent(normalized)}`;
});
</script>
@endsection
