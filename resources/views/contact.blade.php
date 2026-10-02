@extends('layouts.digitalstar')

@section('title', 'Contact | Digital Star Consultants')

@php
    $ds = config('digitalstar');
    $info = [
        ['icon' => 'mail', 'label' => 'Email us', 'value' => 'hello@digitalstar.co.tz', 'href' => 'mailto:hello@digitalstar.co.tz'],
        ['icon' => 'map-pin', 'label' => 'Visit us', 'value' => 'Dar es Salaam, Tanzania', 'href' => null],
        ['icon' => 'clock', 'label' => 'Office hours', 'value' => 'Mon - Sat, 8AM - 6PM', 'href' => null],
    ];
    $tips = [
        'Clear guidance on the right service for your request.',
        'Response from our team during business hours.',
        'Need something urgent? Check the catalogue first for a guided application.',
    ];
    $fields = [
        ['name' => 'name', 'label' => 'Name', 'type' => 'text', 'placeholder' => 'John Mwakyusa', 'autocomplete' => 'name', 'required' => true],
        ['name' => 'email', 'label' => 'Email', 'type' => 'email', 'placeholder' => 'example@gmail.com', 'autocomplete' => 'email', 'required' => true],
        ['name' => 'phone', 'label' => 'Phone (optional)', 'type' => 'tel', 'placeholder' => '+255 712 345 678', 'autocomplete' => 'tel', 'required' => false],
        ['name' => 'subject', 'label' => 'Subject (optional)', 'type' => 'text', 'placeholder' => 'New website for my business', 'autocomplete' => 'off', 'required' => false],
    ];
@endphp

@section('hero-actions')
    <a href="#message" class="ds-btn ds-btn-mint">Send a message <i data-lucide="arrow-right"></i></a>
    <a href="mailto:hello@digitalstar.co.tz" class="ds-btn ds-btn-ghost-light">hello@digitalstar.co.tz</a>
@endsection

@section('hero-aside')
    <div class="ds-hero-card">
        <div class="ds-hero-card-chip" aria-hidden="true"></div>
        <span class="ds-hero-card-label">Quick start</span>
        <h3>Not sure where to begin?</h3>
        <small>Start with the complete catalogue</small>
        <p>Browse by service area, then choose the exact service that matches your request.</p>
        <a href="{{ route('public.services.index') }}" class="ds-btn ds-btn-white">Browse services <i data-lucide="arrow-right"></i></a>
    </div>
@endsection

@section('content')
    @include('partials.ds-hero', [
        'kicker' => 'Contact Digital Star',
        'title' => "Let's get something <span class=\"ds-accent\">moving.</span>",
        'lead' => "Tell us what you need help with. We'll point you to the right service or help you understand the next step.",
        'image' => $ds['images']['collab'],
        'points' => ['Mon - Sat, 8AM - 6PM', 'Dar es Salaam, Tanzania'],
    ])

    <section class="ds-section" id="message" style="scroll-margin-top:80px">
        <div class="ds-container">
            @include('partials.ds-heading', [
                'kicker' => 'Send a message',
                'title' => "We'd like to hear from you.",
                'copy' => 'Give us enough detail to understand what you need. A member of the team will respond with the most useful next step.',
            ])

            <div class="ds-contact">
                <div class="ds-stack ds-reveal">
                    @foreach ($info as $item)
                        <div class="ds-info">
                            <span class="ds-icon-box"><i data-lucide="{{ $item['icon'] }}"></i></span>
                            <div style="min-width:0">
                                <small>{{ $item['label'] }}</small>
                                @if ($item['href'])
                                    <a href="{{ $item['href'] }}">{{ $item['value'] }}</a>
                                @else
                                    <strong>{{ $item['value'] }}</strong>
                                @endif
                            </div>
                        </div>
                    @endforeach

                    <div class="ds-tips">
                        <h3>Keep it simple</h3>
                        <p>You don't need to know the service name. Just tell us what you're trying to accomplish.</p>
                        <ul>
                            @foreach ($tips as $tip)
                                <li><i data-lucide="circle-check"></i><span>{{ $tip }}</span></li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <div class="ds-form-card ds-reveal" style="--d:.1s">
                    @if (session('success'))
                        <div class="ds-alert ds-alert-success" role="status">{{ session('success') }}</div>
                    @endif
                    @if ($errors->any())
                        <div class="ds-alert ds-alert-error" role="alert"><strong>Please check the highlighted details.</strong></div>
                    @endif

                    <form class="ds-form" method="POST" action="{{ route('public.contact.store') }}">
                        @csrf
                        <div class="ds-form-row">
                            @foreach ($fields as $field)
                                <label class="ds-field {{ $errors->has($field['name']) ? 'has-error' : '' }}">
                                    {{ $field['label'] }}
                                    <input type="{{ $field['type'] }}" name="{{ $field['name'] }}" value="{{ old($field['name']) }}"
                                           placeholder="{{ $field['placeholder'] }}" autocomplete="{{ $field['autocomplete'] }}"
                                           @if($field['required']) required @endif>
                                    @error($field['name'])<span class="ds-field-error">{{ $message }}</span>@enderror
                                </label>
                            @endforeach
                        </div>
                        <label class="ds-field {{ $errors->has('message') ? 'has-error' : '' }}">
                            Message
                            <textarea name="message" rows="6" required placeholder="Tell us what you need help with...">{{ old('message') }}</textarea>
                            @error('message')<span class="ds-field-error">{{ $message }}</span>@enderror
                        </label>
                        <button type="submit" class="ds-btn ds-btn-primary ds-btn-block" style="height:48px">
                            Send message <i data-lucide="arrow-right"></i>
                        </button>
                        <p class="ds-form-note">Your details are used only to respond to your enquiry.</p>
                    </form>
                </div>
            </div>
        </div>
    </section>

    @include('partials.ds-cta', [
        'kicker' => 'Prefer to browse?',
        'title' => 'Find the right service first.',
        'copy' => 'Explore the complete catalogue, then start a guided application in minutes.',
        'buttonLabel' => 'Explore services',
        'url' => route('public.services.index'),
        'checklist' => ['Guided applications', 'Document support', 'Status tracking'],
    ])
@endsection
