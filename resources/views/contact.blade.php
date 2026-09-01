@extends('layouts.app')

@section('content')
<section class="ds-page-hero ds-contact-hero">
    <div class="ds-container ds-page-hero-grid">
        <div>
            <div class="ds-eyebrow">CONTACT DIGITAL STAR</div>
            <h1>Let's get something <em>moving.</em></h1>
            <p>Tell us what you need help with. We'll point you to the right service or help you understand the next step.</p>
            <div class="ds-contact-quick">
                <a href="mailto:hello@digitalstar.co.tz"><span class="ds-quick-icon">@</span><span><small>EMAIL</small><strong>hello@digitalstar.co.tz</strong></span></a>
                <div><span class="ds-quick-icon">8–6</span><span><small>BUSINESS HOURS</small><strong>Mon – Sat · 8:00 AM – 6:00 PM</strong></span></div>
                <div><span class="ds-quick-icon">TZ</span><span><small>LOCATION</small><strong>Dar es Salaam, Tanzania</strong></span></div>
            </div>
        </div>
        <div class="ds-contact-side-card">
            <div class="ds-contact-side-top"><span>QUICK START</span><span class="ds-side-dot"></span></div>
            <h2>Not sure which service you need?</h2>
            <p>Start with the complete catalogue and browse by service area, then choose the exact service that matches your request.</p>
            <a class="ds-button ds-button-dark" href="{{ route('public.services.index') }}">Browse services <span>→</span></a>
        </div>
    </div>
</section>

<section class="ds-section ds-contact-section">
    <div class="ds-container ds-contact-layout">
        <div class="ds-contact-info">
            <div class="ds-eyebrow">SEND A MESSAGE</div>
            <h2>We'd like to <em>hear from you.</em></h2>
            <p>Use the form and give us enough detail to understand what you need. A member of the team can then respond with the most useful next step.</p>
            <div class="ds-contact-note">
                <span class="ds-note-icon">✓</span>
                <div><strong>Keep it simple</strong><p>You do not need to know the service name. Just tell us what you are trying to accomplish.</p></div>
            </div>
            <div class="ds-contact-note">
                <span class="ds-note-icon">↗</span>
                <div><strong>Need something urgent?</strong><p>Use the service catalogue first to see whether your exact request already has a guided application.</p></div>
            </div>
        </div>

        <div class="ds-contact-form-card">
            @if (session('success'))
                <div class="ds-alert ds-alert-success">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="ds-alert ds-alert-error"><strong>Please check the highlighted details.</strong><span>{{ $errors->first() }}</span></div>
            @endif
            <form class="ds-contact-form" method="POST" action="{{ route('public.contact.store') }}">
                @csrf
                <div class="ds-form-grid-2">
                    <label>Name<input name="name" autocomplete="name" required value="{{ old('name') }}" placeholder="Your full name"></label>
                    <label>Email<input type="email" name="email" autocomplete="email" required value="{{ old('email') }}" placeholder="you@example.com"></label>
                    <label>Phone<input name="phone" autocomplete="tel" value="{{ old('phone') }}" placeholder="+255 ..."></label>
                    <label>Subject<input name="subject" value="{{ old('subject') }}" placeholder="What can we help with?"></label>
                </div>
                <label>Message<textarea name="message" required placeholder="Tell us what you need help with...">{{ old('message') }}</textarea></label>
                <div class="ds-form-footer">
                    <span>Your details are used to respond to your enquiry.</span>
                    <button class="ds-button ds-button-gold" type="submit">Send message <span>→</span></button>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection
