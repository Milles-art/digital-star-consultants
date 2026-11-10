@extends('layouts.digitalstar')

@section('title', __('forms.index.title') . ' | Digital Star Consultants')
@section('description', __('forms.index.lead'))

@section('hero-crumbs')
    <a href="{{ route('home') }}">{{ __('site.nav.home') }}</a>
    <i data-lucide="chevron-right"></i>
    <span aria-current="page">{{ __('site.nav.forms') }}</span>
@endsection

@section('hero-actions')
    <a href="{{ route('public.services.index') }}" class="ds-btn ds-btn-mint">
        {{ __('site.nav.browse_services') }} <i data-lucide="arrow-right"></i>
    </a>
@endsection

@section('content')
    @include('partials.ds-hero', [
        'kicker' => __('forms.index.eyebrow'),
        'title' => __('forms.index.title'),
        'lead' => __('forms.index.lead'),
        'image' => config('digitalstar.images.collab'),
        'points' => [
            __('site.nav.forms') . ' · ' . count($cards),
            __('forms.common.print'),
        ],
    ])

    <section class="ds-section">
        <div class="ds-container">
            @include('partials.ds-heading', [
                'kicker' => __('forms.index.eyebrow'),
                'title' => __('forms.index.title'),
                'copy' => __('forms.index.lead'),
            ])

            @if (empty($cards))
                <div class="ds-empty">
                    <span class="ds-icon-box"><i data-lucide="file-x"></i></span>
                    <h3>{{ __('forms.index.empty') }}</h3>
                </div>
            @else
                <div class="ds-grid-3-even">
                    @foreach ($cards as $card)
                        <article class="ds-service ds-reveal" style="--d:{{ $loop->index * 0.08 }}s">
                            <span class="ds-icon-box"><i data-lucide="file-text"></i></span>
                            <h3>{{ $card['title'] }}</h3>
                            <p>{{ $card['summary'] }}</p>

                            @if (! empty($card['meta']))
                                <ul class="ds-tags">
                                    @foreach ($card['meta'] as $tag)
                                        <li>{{ $tag }}</li>
                                    @endforeach
                                </ul>
                            @endif

                            <a class="ds-card-link" href="{{ $card['url'] }}">
                                {{ __('forms.index.open') }} <i data-lucide="arrow-right"></i>
                            </a>
                        </article>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    @include('partials.ds-cta', [
        'kicker' => __('forms.index.eyebrow'),
        'title' => __('forms.index.services_link'),
        'copy' => __('forms.index.lead'),
        'buttonLabel' => __('site.nav.browse_services'),
        'url' => route('public.services.index'),
        'checklist' => [
            __('site.nav.track'),
            __('site.nav.forms'),
            __('site.nav.contact'),
        ],
    ])
@endsection
