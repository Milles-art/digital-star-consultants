@extends('layouts.digitalstar')

@php
    $a = __('site.about');
    $ds = config('digitalstar');
    $values = [
        ['icon' => 'eye', 'title' => $a['v1_t'], 'copy' => $a['v1_d']],
        ['icon' => 'layers', 'title' => $a['v2_t'], 'copy' => $a['v2_d']],
        ['icon' => 'message-circle', 'title' => $a['v3_t'], 'copy' => $a['v3_d']],
        ['icon' => 'trending-up', 'title' => $a['v4_t'], 'copy' => $a['v4_d']],
    ];
@endphp

@section('title', __('site.nav.about') . ' | Digital Star Consultants')
@section('description', $a['meta_desc'])

@section('hero-actions')
    <a href="{{ route('public.services.index') }}" class="ds-btn ds-btn-mint">{{ $a['btn_services'] }} <i data-lucide="arrow-right"></i></a>
    <a href="{{ route('public.contact.show') }}" class="ds-btn ds-btn-ghost-light">{{ $a['btn_talk'] }}</a>
@endsection

@section('hero-aside')
    <div class="ds-hero-card">
        <div class="ds-hero-card-chip" aria-hidden="true"></div>
        <span class="ds-hero-card-label">{{ $a['aside_label'] }}</span>
        <h3>{{ $a['aside_title'] }}</h3>
        <small>{{ $a['aside_small'] }}</small>
        <p>{{ $a['aside_copy'] }}</p>
    </div>
@endsection

@section('content')
    @include('partials.ds-hero', [
        'kicker' => $a['hero_kicker'],
        'title' => $a['hero_title'],
        'lead' => $a['hero_lead'],
        'image' => $ds['images']['team'],
        'points' => $a['hero_points'],
    ])

    @include('partials.ds-stats')

    {{-- WHY DIGITAL STAR --}}
    <section class="ds-section">
        <div class="ds-container ds-split">
            <div class="ds-photo ds-reveal">
                <img src="{{ $ds['images']['collab'] }}" alt="{{ $a['photo_alt'] }}">
                <div class="ds-photo-badge">
                    <strong>{{ $a['badge_years'] }}</strong>
                    <span>{{ $a['badge_span'] }}</span>
                </div>
            </div>
            <div>
                @include('partials.ds-heading', [
                    'kicker' => $a['why_kicker'],
                    'title' => $a['why_title'],
                    'copy' => $a['why_copy'],
                ])
                <div class="ds-values">
                    @foreach ($values as $value)
                        <article class="ds-value ds-reveal" style="--d:{{ $loop->index * 0.08 }}s">
                            <div class="ds-value-head">
                                <span class="ds-icon-box"><i data-lucide="{{ $value['icon'] }}"></i></span>
                                <h3>{{ $value['title'] }}</h3>
                            </div>
                            <p>{{ $value['copy'] }}</p>
                        </article>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    @include('partials.ds-process', [
        'kicker' => $a['how_kicker'],
        'title' => $a['how_title'],
        'copy' => $a['how_copy'],
    ])

    @include('partials.ds-cta', [
        'kicker' => $a['final_kicker'],
        'title' => $a['final_title'],
        'copy' => $a['final_copy'],
        'buttonLabel' => $a['final_btn'],
        'url' => route('public.contact.show'),
    ])
@endsection
