@extends('layouts.digitalstar')

@php
    $c = __('site.contact');
    $ds = config('digitalstar');
    $info = [
        ['icon' => 'mail', 'label' => $c['info_email'], 'value' => 'hello@digitalstar.co.tz', 'href' => 'mailto:hello@digitalstar.co.tz'],
        ['icon' => 'map-pin', 'label' => $c['info_visit'], 'value' => $c['point_city'], 'href' => null],
        ['icon' => 'clock', 'label' => $c['info_hours'], 'value' => $c['hours_value'], 'href' => null],
    ];
    $tips = [$c['tip1'], $c['tip2'], $c['tip3']];
    $optional = __('site.services.optional');
    $fields = [
        ['name' => 'name', 'label' => $c['name'], 'type' => 'text', 'placeholder' => __('site.services.detail.name_ph'), 'autocomplete' => 'name', 'required' => true],
        ['name' => 'email', 'label' => $c['email'], 'type' => 'email', 'placeholder' => 'example@gmail.com', 'autocomplete' => 'email', 'required' => true],
        ['name' => 'phone', 'label' => $c['phone'], 'type' => 'tel', 'placeholder' => '+255 712 345 678', 'autocomplete' => 'tel', 'required' => false],
        ['name' => 'subject', 'label' => $c['subject'], 'type' => 'text', 'placeholder' => $c['subject_ph'], 'autocomplete' => 'off', 'required' => false],
    ];
@endphp

@section('title', $c['title'] . ' | Digital Star Consultants')
@section('description', $c['meta_desc'])

@section('hero-actions')
    <a href="#message" class="ds-btn ds-btn-mint">{{ $c['send'] }} <i data-lucide="arrow-right"></i></a>
    <a href="mailto:hello@digitalstar.co.tz" class="ds-btn ds-btn-ghost-light">hello@digitalstar.co.tz</a>
@endsection

@section('hero-aside')
    <div class="ds-hero-card">
        <div class="ds-hero-card-chip" aria-hidden="true"></div>
        <span class="ds-hero-card-label">{{ $c['aside_label'] }}</span>
        <h3>{{ $c['aside_title'] }}</h3>
        <small>{{ $c['aside_small'] }}</small>
        <p>{{ $c['aside_copy'] }}</p>
        <a href="{{ route('public.services.index') }}" class="ds-btn ds-btn-white">{{ $c['aside_btn'] }} <i data-lucide="arrow-right"></i></a>
    </div>
@endsection

@section('content')
    @include('partials.ds-hero', [
        'kicker' => $c['hero_kicker'],
        'title' => $c['title_span'],
        'lead' => $c['lead_full'],
        'image' => $ds['images']['collab'],
        'points' => [$c['point_hours'], $c['point_city']],
    ])

    <section class="ds-section" id="message" style="scroll-margin-top:80px">
        <div class="ds-container">
            @include('partials.ds-heading', [
                'kicker' => $c['msg_kicker'],
                'title' => $c['msg_title'],
                'copy' => $c['msg_copy'],
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
                        <h3>{{ $c['tips_title'] }}</h3>
                        <p>{{ $c['tips_copy'] }}</p>
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
                        <div class="ds-alert ds-alert-error" role="alert"><strong>{{ $c['errors'] }}</strong></div>
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
                            {{ $c['message'] }}
                            <textarea name="message" rows="6" required placeholder="{{ $c['msg_area_ph'] }}">{{ old('message') }}</textarea>
                            @error('message')<span class="ds-field-error">{{ $message }}</span>@enderror
                        </label>
                        <button type="submit" class="ds-btn ds-btn-primary ds-btn-block" style="height:48px">
                            {{ $c['send'] }} <i data-lucide="arrow-right"></i>
                        </button>
                        <p class="ds-form-note">{{ $c['form_note'] }}</p>
                    </form>
                </div>
            </div>
        </div>
    </section>

    @include('partials.ds-cta', [
        'kicker' => $c['cta_kicker'],
        'title' => $c['cta_title'],
        'copy' => $c['cta_copy'],
        'buttonLabel' => $c['cta_btn'],
        'url' => route('public.services.index'),
        'checklist' => $c['checklist'],
    ])
@endsection

@push('scripts')
<script>
    // Contact form: show a spinner while the request is in flight
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.querySelector('.ds-form-card form.ds-form');
        if (!form) return;
        form.addEventListener('submit', () => {
            const btn = form.querySelector('button[type="submit"]');
            if (!btn || !btn.checkValidity()) return;
            btn.classList.add('is-loading');
            btn.setAttribute('aria-busy', 'true');
            btn.disabled = true;
        });
    });
</script>
@endpush
