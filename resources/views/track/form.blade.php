@extends('layouts.digitalstar')

@php
    $t = __('site.track');
    $features = [
        ['icon' => 'activity', 'title' => $t['f1_title'], 'copy' => $t['f1_copy']],
        ['icon' => 'signpost', 'title' => $t['f2_title'], 'copy' => $t['f2_copy']],
        ['icon' => 'hash', 'title' => $t['f3_title'], 'copy' => $t['f3_copy']],
    ];
@endphp

@section('title', $t['title'] . ' | Digital Star Consultants')
@section('description', $t['meta_desc'])

@section('hero-aside')
    <div class="ds-hero-card">
        <div class="ds-hero-card-chip" aria-hidden="true"></div>
        <span class="ds-hero-card-label">{{ $t['aside_label'] }}</span>
        <h3>{{ $t['title'] }}</h3>
        <small>{{ __('public.reference_example') }}: {{ \App\Support\SubmissionReference::example() }}</small>
        <form method="GET" action="{{ route('public.track.form') }}" class="ds-form" id="track-form" style="margin-top:20px;gap:14px">
            <label class="ds-field">{{ $t['field_label'] }}
                <input id="tracking-reference" name="reference" required autocomplete="off" maxlength="28" value="{{ old('reference') }}" placeholder="{{ \App\Support\SubmissionReference::example() }}" aria-describedby="reference-help">
            </label>
            <small id="reference-help">{{ __('public.reference_help') }}</small>
            @error('reference')<p class="ds-alert ds-alert-error" role="alert">{{ $message }}</p>@enderror
            <button class="ds-btn ds-btn-mint ds-btn-block" type="submit">{{ $t['btn'] }} <i data-lucide="arrow-right"></i></button>
        </form>
        <p class="ds-hero-note"><i data-lucide="shield-check"></i> {{ $t['secure'] }}</p>
    </div>
@endsection

@section('content')
    @include('partials.ds-hero', [
        'kicker' => $t['hero_kicker'],
        'title' => $t['title_span'],
        'lead' => $t['lead_full'],
        'points' => [$t['f1_title'], $t['f2_title'], $t['f3_title']],
    ])

    <section class="ds-section">
        <div class="ds-container">
            @include('partials.ds-heading', [
                'kicker' => $t['how_kicker'],
                'title' => $t['how_title'],
                'copy' => $t['how_copy'],
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
                    <span class="ds-kicker">{{ $t['no_ref_kicker'] }}</span>
                    <h3>{{ $t['no_ref_title'] }}</h3>
                </div>
                <a class="ds-btn ds-btn-primary" href="{{ route('public.contact.show') }}">{{ $t['no_ref_btn'] }} <i data-lucide="arrow-right"></i></a>
            </div>
        </div>
    </section>
@endsection
